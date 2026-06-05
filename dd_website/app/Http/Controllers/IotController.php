<?php

namespace App\Http\Controllers;

use App\Models\SensorLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;


class IotController extends Controller
{
    public function store(Request $request)
    {
        $data = [
            'hr'        => $request->input('hr', 0),
            'spo2'      => $request->input('spo2', 0),
            'timestamp' => now()->toTimeString(),
        ];

        // 1. Simpan ke cache (realtime)
        Cache::put('sensor_data', $data, 10);

        // 2. Simpan ke DB tiap 5 detik saja
        $lastSaved = Cache::get('last_db_save');

        if (!$lastSaved || now()->diffInSeconds($lastSaved) >= 5) {
            SensorLog::create([
                'hr' => $data['hr'],
                'spo2' => $data['spo2'],
                'created_at' => now()
            ]);

            Cache::put('last_db_save', now(), 10);
        }

        return response()->json(['status' => 'ok']);
    }

    // Kirim data terbaru ke browser
    public function latest()
    {
        $sensor  = Cache::get('sensor_data', [
            'hr'        => 0,
            'spo2'      => 0,
            'timestamp' => null,
        ]);
        $gender    = Cache::get('driver_gender', 'male');
        $threshold = $gender === 'female' ? 81 : 76;
        $hr        = (float) ($sensor['hr'] ?? 0);

        // hr_low: HR valid (>50) tapi di bawah threshold → indikasi mengantuk
        $hrLow = $hr > 50 && $hr < $threshold;

        return response()->json([
            'hr'        => $sensor['hr'],
            'spo2'      => $sensor['spo2'],
            'timestamp' => $sensor['timestamp'],
            'gender'    => $gender,
            'threshold' => $threshold,
            'hr_low'    => $hrLow,
        ]);
    }

    // ── BARU: simpan pilihan gender dari browser ──────────────────
    public function setGender(Request $request)
    {
        $gender = $request->input('gender');
        if (!in_array($gender, ['male', 'female'])) {
            return response()->json(['error' => 'Invalid gender'], 422);
        }
        Cache::put('driver_gender', $gender, 3600);
        return response()->json(['status' => 'ok', 'gender' => $gender]);
    }
}

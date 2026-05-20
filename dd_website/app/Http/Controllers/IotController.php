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
        $data = Cache::get('sensor_data', [
            'hr'        => 0,
            'spo2'      => 0,
            'timestamp' => null,
        ]);

        return response()->json($data);
    }
}

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
        $sensor = Cache::get('sensor_data', [
            'hr'        => 0,
            'spo2'      => 0,
            'timestamp' => null,
        ]);

        $hr = (float) ($sensor['hr'] ?? 0);

        // Ambil atau bangun baseline
        $baseline    = Cache::get('hr_baseline');        // null kalau belum ada
        $baselineLog = Cache::get('hr_baseline_log', []); // array HR selama 2 menit
        $hrLow       = false;

        if ($hr > 50) {
            if (!$baseline) {
                $baselineLog[] = $hr;
                Cache::put('hr_baseline_log', $baselineLog, 300);

                if (count($baselineLog) >= 60) {
                    $baseline = array_sum($baselineLog) / count($baselineLog);
                    Cache::put('hr_baseline', $baseline, 3600);
                    Cache::forget('hr_baseline_log');
                }
            } else {
                $drop = ($baseline - $hr) / $baseline * 100;

                $cameraWasActive = Cache::get('camera_active', false);

                if (!$cameraWasActive) {
                    // Kamera mati → nyala jika turun ≥ 9.3%
                    $hrLow = $drop >= 9.3;
                } else {
                    // Kamera nyala → mati jika naik kembali ke ≤ 5% penurunan
                    $hrLow = $drop >= 5.0;
                }

                Cache::put('camera_active', $hrLow, 3600);
            }
        }

        return response()->json([
            'hr'           => $sensor['hr'],
            'spo2'         => $sensor['spo2'],
            'timestamp'    => $sensor['timestamp'],
            'hr_low'       => $hrLow,
            'baseline'     => $baseline ? round($baseline, 1) : null,
            'baseline_ready' => $baseline !== null,
        ]);
    }

    public function resetBaseline()
    {
        Cache::forget('hr_baseline');
        Cache::forget('hr_baseline_log');
        return response()->json(['status' => 'ok']);
    }
}

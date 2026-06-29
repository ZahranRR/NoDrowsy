<?php

namespace App\Http\Controllers;

use App\Models\SensorLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;


class IotController extends Controller
{
    const BASELINE_DURATION_SECONDS = 120; // 2 menit waktu AKTIF

    public function store(Request $request)
    {
        $data = [
            'hr'        => $request->input('hr', 0),
            'spo2'      => $request->input('spo2', 0),
            'timestamp' => now()->toTimeString(),
        ];

        // 1. Simpan ke cache (realtime)
        Cache::put('sensor_data', $data, 10);

        // Tandai waktu terakhir kali ESP32 benar-benar kirim data (jari nempel)
        Cache::put('last_hr_received_at', now()->timestamp, 15);

        // 2. Simpan ke DB tiap 5 detik saja
        $lastSaved = Cache::get('last_db_save');

        if (!$lastSaved || (now()->timestamp - $lastSaved) >= 5) {
            SensorLog::create([
                'hr' => $data['hr'],
                'spo2' => $data['spo2'],
                'created_at' => now()
            ]);

            Cache::put('last_db_save', now()->timestamp, 10);
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

        // Cek apakah ESP32 masih aktif kirim data dalam 4 detik terakhir
        // (publish interval ESP32 = 2 detik, beri toleransi 2x lipat)
        $lastHrReceivedAt = Cache::get('last_hr_received_at');
        $fingerActive = $lastHrReceivedAt && (now()->timestamp - $lastHrReceivedAt) <= 4;

        // Ambil atau bangun baseline
        $baseline = Cache::get('hr_baseline');        // null kalau belum ada
        $hrLow    = false;

        if (!$baseline) {
            // Ambil progress timer yang tersimpan
            $elapsedSeconds = Cache::get('baseline_elapsed_seconds', 0);
            $lastTickAt     = Cache::get('baseline_last_tick_at');

            if ($hr > 50 && $fingerActive) {
                // Jari aktif & HR valid → timer jalan, tambahkan delta waktu sejak tick terakhir
                if ($lastTickAt) {
                    $delta = now()->timestamp - $lastTickAt;
                    // Cap delta supaya tidak melonjak kalau ada jeda request yang lama
                    $delta = min($delta, 5);
                    $elapsedSeconds += $delta;
                }
                Cache::put('baseline_last_tick_at', now()->timestamp, 600);
                Cache::put('baseline_elapsed_seconds', $elapsedSeconds, 600);

                // Tetap kumpulkan sample HR untuk dirata-rata
                $baselineLog = Cache::get('hr_baseline_log', []);
                $baselineLog[] = $hr;
                Cache::put('hr_baseline_log', $baselineLog, 600);

                if ($elapsedSeconds >= self::BASELINE_DURATION_SECONDS) {
                    $baseline = array_sum($baselineLog) / count($baselineLog);
                    Cache::put('hr_baseline', $baseline, 3600);
                    Cache::forget('hr_baseline_log');
                    Cache::forget('baseline_elapsed_seconds');
                    Cache::forget('baseline_last_tick_at');
                }
            } else {
                // Jari tidak aktif → PAUSE, jangan update lastTickAt (supaya delta tidak dihitung saat resume)
                Cache::forget('baseline_last_tick_at');
            }
        } else {
            $drop = ($baseline - $hr) / $baseline * 100;
            $cameraWasActive = Cache::get('camera_active', false);

            if ($hr > 50) {
                if (!$cameraWasActive) {
                    // 3 polling berturut-turut drop ≥9.3% baru aktifkan kamera
                    $dropConfirmCount = Cache::get('hr_drop_confirm', 0);

                    if ($drop >= 9.3) {
                        $dropConfirmCount++;
                        Cache::put('hr_drop_confirm', $dropConfirmCount, 30);
                    } else {
                        // HR tidak drop → reset counter konfirmasi
                        Cache::put('hr_drop_confirm', 0, 30);
                    }

                    $hrLow = $dropConfirmCount >= 3; //hr drop selama 3x polling

                    if ($hrLow) {
                        // Kamera aktif → tidak perlu reset, biarkan tetap aktif
                        Cache::put('camera_active', true, 3600);
                    }
                } else {
                    // Kamera sudah aktif, tidak mati
                    $hrLow = true;
                    Cache::put('camera_active', true, 3600);
                }
            }
        }

        $elapsedSeconds = Cache::get('baseline_elapsed_seconds', 0);
        $remainingSeconds = $baseline ? 0 : max(0, self::BASELINE_DURATION_SECONDS - $elapsedSeconds);

        return response()->json([
            'hr'           => $sensor['hr'],
            'spo2'         => $sensor['spo2'],
            'timestamp'    => $sensor['timestamp'],
            'hr_low'       => $hrLow,
            'baseline'     => $baseline ? round($baseline, 1) : null,
            'baseline_ready'    => $baseline !== null,
            'baseline_remaining' => $remainingSeconds,  // sisa detik
            'baseline_active'    => $fingerActive && !$baseline, // apakah timer sedang jalan
        ]);
    }

    public function history(Request $request)
    {
        $minutes = (int) $request->query('minutes', 30);
        $minutes = min($minutes, 360); // cap 6 jam

        $logs = \App\Models\SensorLog::where('created_at', '>=', now()->subMinutes($minutes))
            ->orderBy('created_at', 'asc')
            ->get(['hr', 'created_at'])
            ->map(fn($row) => [
                't' => $row->created_at->timestamp * 1000, // milliseconds untuk Chart.js
                'hr' => $row->hr,
            ]);

        $baseline = Cache::get('hr_baseline');

        return response()->json([
            'data'     => $logs,
            'baseline' => $baseline ? round($baseline, 1) : null,
        ]);
    }

    public function resetBaseline()
    {
        Cache::forget('hr_baseline');
        Cache::forget('hr_baseline_log');
        Cache::forget('baseline_elapsed_seconds');
        Cache::forget('baseline_last_tick_at');
        Cache::forget('hr_drop_confirm');
        Cache::forget('camera_active');
        return response()->json(['status' => 'ok']);
    }
}

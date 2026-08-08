<?php

require __DIR__ . '/vendor/autoload.php';

use PhpMqtt\Client\MqttClient;
use PhpMqtt\Client\ConnectionSettings;

// Bootstrap Laravel (untuk akses Cache & Model)
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$client = new MqttClient('a469dd8f6d484da1a35f07a7b9e4f4ff.s1.eu.hivemq.cloud', 8883, 'laravel-subscriber');

$connectionSettings = (new ConnectionSettings())
    ->setUsername('alanganteng')
    ->setPassword('alanganteng123')
    ->setUseTls(true);

$client->connect($connectionSettings, true);

echo "MQTT subscriber running...\n";

$client->subscribe('oximeter/data', function (string $topic, string $message) {
    echo "Received: $message\n";

    $data = json_decode($message, true);
    if (!$data) return;

    // Simpan ke cache (realtime)
    \Illuminate\Support\Facades\Cache::put('sensor_data', [
        'hr'        => $data['hr']   ?? 0,
        'spo2'      => $data['spo2'] ?? 0,
        'timestamp' => now()->toTimeString(),
    ], 10);

    // Tandai waktu terakhir kali data HR diterima (untuk deteksi jari aktif)
    \Illuminate\Support\Facades\Cache::put('last_hr_received_at', now()->timestamp, 15);

    // Simpan ke DB tiap 5 detik
    $lastSaved = \Illuminate\Support\Facades\Cache::get('last_db_save');
    if (!$lastSaved || now()->diffInSeconds($lastSaved) >= 5) {
        \App\Models\SensorLog::create([
            'hr'   => $data['hr']   ?? 0,
            'spo2' => $data['spo2'] ?? 0,
        ]);
        \Illuminate\Support\Facades\Cache::put('last_db_save', now(), 10);
    }
}, 0);

$client->loop(true);

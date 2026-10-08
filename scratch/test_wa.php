<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$phone = $argv[1] ?? '628123456789';
$message = $argv[2] ?? "✨ [ALAN SALON] Halo! Ini adalah pesan tes notifikasi otomatis WhatsApp dari sistem Alan Salon. WhatsApp Service berhasil terhubung!";

echo "Mengirim pesan ke: {$phone}...\n";

try {
    $response = Illuminate\Support\Facades\Http::post('http://localhost:3000/send-message', [
        'phone' => $phone,
        'message' => $message,
    ]);

    echo "Status Code: " . $response->status() . "\n";
    echo "Response: " . $response->body() . "\n";
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    $service = App\Models\Service::first();
    $slot = App\Models\Slot::first();
    $user = App\Models\User::first();

    if (!$service || !$slot || !$user) {
        die("Missing initial data for testing.\n");
    }

    $booking = App\Models\Booking::create([
        'user_id' => $user->id,
        'service_id' => $service->id,
        'slot_id' => $slot->id,
        'nama_pelanggan' => 'Tes Eka',
        'no_whatsapp' => '6289523808660',
        'tanggal_kunjungan' => $slot->tanggal,
        'jam_kunjungan' => $slot->jam_mulai,
        'total_harga' => $service->harga,
        'dp_amount' => $service->harga * 0.2,
        'status' => 'menunggu_pembayaran',
        'catatan' => 'Tes booking',
    ]);

    echo "Booking created successfully with ID: " . $booking->id . " and status: " . $booking->status . "\n";
    
    // Clean up test booking
    $booking->delete();
    echo "Test booking cleaned up successfully.\n";
} catch (\Exception $e) {
    echo "Error inserting booking: " . $e->getMessage() . "\n";
}

<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    Illuminate\Support\Facades\DB::statement("ALTER TABLE bookings MODIFY COLUMN status VARCHAR(50) NOT NULL DEFAULT 'menunggu_pembayaran'");
    echo "Column status on table bookings successfully updated to VARCHAR(50)!\n";
} catch (\Exception $e) {
    echo "Error updating column: " . $e->getMessage() . "\n";
}

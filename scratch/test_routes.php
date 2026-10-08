<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Service;
use App\Models\Slot;
use App\Models\Booking;
use App\Models\Gallery;
use App\Models\Review;

echo "=== MEMULAI TEST SEMUA ROUTE & HALAMAN ALAN'S SALON ===" . PHP_EOL . PHP_EOL;

// 1. Get or create test users
$admin = User::firstOrCreate(
    ['email' => 'admin@alansalon.com'],
    [
        'name' => 'Admin Salon',
        'role' => 'admin',
        'no_whatsapp' => '628123456789',
        'password' => bcrypt('password'),
        'email_verified_at' => now(),
    ]
);
if ($admin->role !== 'admin') {
    $admin->update(['role' => 'admin']);
}

$customer = User::firstOrCreate(
    ['email' => 'customer@test.com'],
    [
        'name' => 'Customer Test',
        'role' => 'customer',
        'no_whatsapp' => '628987654321',
        'password' => bcrypt('password'),
        'email_verified_at' => now(),
    ]
);

// 2. Ensure test service & slot exist
$service = Service::firstOrCreate(
    ['nama_layanan' => 'Potong Rambut Pria Test'],
    [
        'kategori' => 'haircut',
        'deskripsi' => 'Deskripsi potong rambut pria test',
        'harga' => 75000,
        'durasi_menit' => 45,
        'status' => 'aktif',
    ]
);

$slot = Slot::firstOrCreate(
    ['tanggal' => now()->addDays(1)->toDateString(), 'jam_mulai' => '10:00:00'],
    [
        'jam_selesai' => '11:00:00',
        'kapster' => 'Liam Parker',
        'kuota' => 2,
        'terisi' => 0,
        'status' => 'tersedia',
    ]
);

$booking = Booking::firstOrCreate(
    ['user_id' => $customer->id, 'slot_id' => $slot->id],
    [
        'service_id' => $service->id,
        'nama_pelanggan' => $customer->name,
        'no_whatsapp' => $customer->no_whatsapp,
        'tanggal_kunjungan' => $slot->tanggal,
        'jam_kunjungan' => $slot->jam_mulai,
        'total_harga' => $service->harga,
        'dp_amount' => $service->harga * 0.2,
        'status' => 'menunggu_pembayaran',
        'catatan' => 'Catatan test booking',
    ]
);

$review = Review::firstOrCreate(
    ['booking_id' => $booking->id],
    [
        'user_id' => $customer->id,
        'rating' => 5,
        'komentar' => 'Layanan sangat bagus dan memuaskan!',
    ]
);

$routesToTest = [
    ['name' => 'Guest - Home (/)', 'url' => '/', 'user' => null, 'expected' => 200],
    ['name' => 'Guest - Login (/login)', 'url' => '/login', 'user' => null, 'expected' => 200],
    ['name' => 'Guest - Register (/register)', 'url' => '/register', 'user' => null, 'expected' => 200],
    ['name' => 'Guest - Forgot Password (/forgot-password)', 'url' => '/forgot-password', 'user' => null, 'expected' => 200],
    ['name' => 'Customer - Dashboard (/dashboard)', 'url' => '/dashboard', 'user' => $customer, 'expected' => 200],
    ['name' => 'Customer - Booking Create (/booking)', 'url' => '/booking', 'user' => $customer, 'expected' => 200],
    ['name' => 'Customer - Booking Detail (/booking/' . $booking->id . ')', 'url' => '/booking/' . $booking->id, 'user' => $customer, 'expected' => 200],
    ['name' => 'Customer - Profile Edit (/profile)', 'url' => '/profile', 'user' => $customer, 'expected' => 200],
    ['name' => 'Admin - Dashboard (/admin/dashboard)', 'url' => '/admin/dashboard', 'user' => $admin, 'expected' => 200],
    ['name' => 'Admin - Services List (/admin/services)', 'url' => '/admin/services', 'user' => $admin, 'expected' => 200],
    ['name' => 'Admin - Services Create (/admin/services/create)', 'url' => '/admin/services/create', 'user' => $admin, 'expected' => 200],
    ['name' => 'Admin - Services Edit (/admin/services/' . $service->id . '/edit)', 'url' => '/admin/services/' . $service->id . '/edit', 'user' => $admin, 'expected' => 200],
    ['name' => 'Admin - Slots List (/admin/slots)', 'url' => '/admin/slots', 'user' => $admin, 'expected' => 200],
    ['name' => 'Admin - Slots Create (/admin/slots/create)', 'url' => '/admin/slots/create', 'user' => $admin, 'expected' => 200],
    ['name' => 'Admin - Slots Edit (/admin/slots/' . $slot->id . '/edit)', 'url' => '/admin/slots/' . $slot->id . '/edit', 'user' => $admin, 'expected' => 200],
    ['name' => 'Admin - Bookings List (/admin/bookings)', 'url' => '/admin/bookings', 'user' => $admin, 'expected' => 200],
    ['name' => 'Admin - Bookings Detail (/admin/bookings/' . $booking->id . ')', 'url' => '/admin/bookings/' . $booking->id, 'user' => $admin, 'expected' => 200],
    ['name' => 'Admin - Galleries List (/admin/galleries)', 'url' => '/admin/galleries', 'user' => $admin, 'expected' => 200],
];

$allPassed = true;

foreach ($routesToTest as $test) {
    if ($test['user']) {
        auth()->login($test['user']);
    } else {
        auth()->logout();
    }

    $request = Request::create($test['url'], 'GET');
    
    // Bind session
    $session = $app['session']->driver();
    $session->start();
    $request->setLaravelSession($session);

    try {
        $response = $app->handle($request);
        $status = $response->getStatusCode();
        
        if ($status === $test['expected']) {
            echo "[PASS] " . str_pad($test['name'], 55) . " -> Status: " . $status . PHP_EOL;
        } else {
            $allPassed = false;
            echo "[FAIL] " . str_pad($test['name'], 55) . " -> Expected " . $test['expected'] . ", got " . $status . PHP_EOL;
            if (isset($response->exception) && $response->exception) {
                echo "       Exception: " . $response->exception->getMessage() . PHP_EOL;
                echo "       File: " . $response->exception->getFile() . ":" . $response->exception->getLine() . PHP_EOL;
            }
        }
    } catch (\Throwable $e) {
        $allPassed = false;
        echo "[ERROR] " . str_pad($test['name'], 55) . " -> " . $e->getMessage() . PHP_EOL;
        echo "        File: " . $e->getFile() . ":" . $e->getLine() . PHP_EOL;
    }
}

echo PHP_EOL . "=== HASIL TEST: " . ($allPassed ? "SEMUA HALAMAN BERHASIL LOLOS TEST (100% OK)" : "ADA ERROR") . " ===" . PHP_EOL;

<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

Route::get('/gallery-images/{filename}', function (string $filename) {
    abort_if(
        $filename === '.' ||
        $filename === '..' ||
        ! preg_match('/\A[A-Za-z0-9._-]+\z/', $filename),
        404
    );

    $path = 'galleries/' . $filename;
    $disk = Storage::disk('public');

    abort_unless($disk->exists($path), 404);

    return $disk->response($path);
})->name('gallery.image');

Route::get('/', function () {

        $services = \App\Models\Service::where('status', 'aktif')
            ->orderBy('nama_layanan')
            ->get();

            $galleries = \App\Models\Gallery::with('service')
                    ->latest()
                            ->take(8)
                                    ->get();

                                        $reviews = \App\Models\Review::with(['user', 'booking.services'])
                                                ->latest()
                                                        ->take(4)
                                                                ->get();

                                        $galleryFiles = collect(Storage::disk('public')->files('galleries'));


return view('welcome', compact('services', 'galleries', 'reviews', 'galleryFiles'));
});

Route::get('/layanan', function () {

    $services = \App\Models\Service::where('status', 'aktif')
        ->orderBy('nama_layanan')
        ->get();

    return view('services.index', compact('services'));

})->name('services.index');

Route::get('/galeri', function () {

    $galleries = \App\Models\Gallery::with('service')
        ->latest()
        ->get();

    $galleryFiles = collect(Storage::disk('public')->files('galleries'));

    return view('gallery.index', compact('galleries', 'galleryFiles'));

})->name('gallery.index');

Route::get('/dashboard', function () {

    $bookings = \App\Models\Booking::where('user_id', auth()->id())
        ->with(['services', 'slot', 'payment'])
        ->orderBy('created_at', 'desc')
        ->get();

    return view('dashboard', compact('bookings'));

})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/booking', [App\Http\Controllers\BookingController::class, 'create'])
        ->name('booking.create');

    Route::post('/booking', [App\Http\Controllers\BookingController::class, 'store'])
        ->name('booking.store');

    Route::get('/booking/{booking}', [App\Http\Controllers\BookingController::class, 'show'])
        ->name('booking.show');

    Route::get('/booking/{booking}/payment-proof', [App\Http\Controllers\BookingController::class, 'paymentProof'])
        ->name('booking.payment-proof');

    Route::post('/booking/{booking}/payment', [App\Http\Controllers\BookingController::class, 'uploadPayment'])
        ->name('booking.payment');

    Route::post('/booking/{booking}/review', [App\Http\Controllers\BookingController::class, 'storeReview'])
        ->name('booking.review');

    Route::post('/booking/{booking}/cancel', [App\Http\Controllers\BookingController::class, 'cancel'])
        ->name('booking.cancel');
});


Route::middleware('auth')->group(function () {

    Route::get('/profile', [ProfileController::class, 'edit'])
        ->name('profile.edit');

    Route::patch('/profile', [ProfileController::class, 'update'])
        ->name('profile.update');

    Route::delete('/profile', [ProfileController::class, 'destroy'])
        ->name('profile.destroy');

});


Route::middleware(['auth', 'admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        /*
        |--------------------------------------------------------------------------
        | Admin Dashboard
        |--------------------------------------------------------------------------
        */

        Route::get('/dashboard', [
            App\Http\Controllers\Admin\DashboardController::class,
            'index'
        ])->name('dashboard');

        Route::get('/reports', [
            App\Http\Controllers\Admin\DashboardController::class,
            'reportIndex'
        ])->name('reports.index');

        Route::get('/whatsapp/status', [
            App\Http\Controllers\Admin\WhatsAppController::class,
            'status'
        ])->name('whatsapp.status');

        Route::get('/whatsapp/qr', [
            App\Http\Controllers\Admin\WhatsAppController::class,
            'qr'
        ])->name('whatsapp.qr');

        Route::post('/whatsapp/pairing-code', [
            App\Http\Controllers\Admin\WhatsAppController::class,
            'pairingCode'
        ])->name('whatsapp.pairingCode');

        Route::post('/whatsapp/reset', [
            App\Http\Controllers\Admin\WhatsAppController::class,
            'reset'
        ])->name('whatsapp.reset');


        /*
        |--------------------------------------------------------------------------
        | DOWNLOAD LAPORAN PENDAPATAN PDF
        |--------------------------------------------------------------------------
        */

        Route::get('/dashboard/download-pdf', [
            App\Http\Controllers\Admin\DashboardController::class,
            'downloadPdf'
        ])->name('dashboard.downloadPdf');


        /*
        |--------------------------------------------------------------------------
        | Services
        |--------------------------------------------------------------------------
        */

        Route::resource(
            'services',
            App\Http\Controllers\Admin\ServiceController::class
        );

        Route::post('stylists', [App\Http\Controllers\Admin\StylistController::class, 'store'])->name('stylists.store');
        Route::patch('stylists/{stylist}/toggle', [App\Http\Controllers\Admin\StylistController::class, 'toggle'])->name('stylists.toggle');
        Route::delete('stylists/{stylist}', [App\Http\Controllers\Admin\StylistController::class, 'destroy'])->name('stylists.destroy');


        /*
        |--------------------------------------------------------------------------
        | Slots
        |--------------------------------------------------------------------------
        */

        Route::post(
            'slots/bulk-generate',
            [
                App\Http\Controllers\Admin\SlotController::class,
                'bulkGenerate'
            ]
        )->name('slots.bulkGenerate');

        Route::resource(
            'slots',
            App\Http\Controllers\Admin\SlotController::class
        );


        /*
        |--------------------------------------------------------------------------
        | Galleries
        |--------------------------------------------------------------------------
        */

        Route::resource(
            'galleries',
            App\Http\Controllers\Admin\GalleryController::class
        )->except([
            'create',
            'show',
            'edit',
            'update'
        ]);


        /*
        |--------------------------------------------------------------------------
        | Users
        |--------------------------------------------------------------------------
        */

        Route::resource(
            'users',
            App\Http\Controllers\Admin\UserController::class
        );


        /*
        |--------------------------------------------------------------------------
        | Bookings
        |--------------------------------------------------------------------------
        */

        Route::get(
            'bookings',
            [
                App\Http\Controllers\Admin\BookingController::class,
                'index'
            ]
        )->name('bookings.index');

        Route::get(
            'bookings/create',
            [
                App\Http\Controllers\Admin\BookingController::class,
                'create'
            ]
        )->name('bookings.create');

        Route::get(
            'bookings/{booking}',
            [
                App\Http\Controllers\Admin\BookingController::class,
                'show'
            ]
        )->name('bookings.show');

        Route::get(
            'bookings/{booking}/payment-proof',
            [
                App\Http\Controllers\BookingController::class,
                'paymentProof'
            ]
        )->name('bookings.payment-proof');

        Route::post(
            'bookings',
            [
                App\Http\Controllers\Admin\BookingController::class,
                'store'
            ]
        )->name('bookings.store');

        Route::post(
            'bookings/{booking}/approve',
            [
                App\Http\Controllers\Admin\BookingController::class,
                'approve'
            ]
        )->name('bookings.approve');

        Route::post(
            'bookings/{booking}/reject',
            [
                App\Http\Controllers\Admin\BookingController::class,
                'reject'
            ]
        )->name('bookings.reject');

        Route::post(
            'bookings/{booking}/complete',
            [
                App\Http\Controllers\Admin\BookingController::class,
                'complete'
            ]
        )->name('bookings.complete');


        /*
        |--------------------------------------------------------------------------
        | Cek Reservasi Baru
        |--------------------------------------------------------------------------
        */

        Route::get('bookings/check-new', function () {

            $latestBooking = \App\Models\Booking::with([
                'user',
                'service',
                'slot',
                'payment'
            ])
                ->latest('created_at')
                ->first();

            return response()->json([
                'latest_booking' => $latestBooking,
            ]);
        });

    });

require __DIR__ . '/auth.php';

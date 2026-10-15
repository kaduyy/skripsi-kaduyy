<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('slots:clean-expired', function () {
    $deleted = \App\Models\Slot::whereDate('tanggal', '<', today())
        ->whereDoesntHave('bookings')
        ->delete();

    $this->info("{$deleted} slot lama berhasil dihapus.");
})->purpose('Hapus slot lama yang tidak memiliki booking');

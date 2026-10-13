<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('slots:clean-expired', function () {
    $deleted = \App\Models\Slot::whereDate('tanggal', '<', today())
        ->whereDoesntHave('bookings')
        ->delete();

    $this->info("{$deleted} slot lama berhasil dihapus.");
})->purpose('Hapus slot lama yang tidak memiliki booking');

Artisan::command('bookings:close-no-show', function () {
    $now = \Carbon\Carbon::now('Asia/Jakarta');
    $processedGroups = [];
    $closed = 0;

    \App\Models\Booking::with(['transactionBookings.reservedSlots', 'user', 'services'])
        ->whereIn('status', ['menunggu_pembayaran', 'dibayar'])
        ->whereNotNull('jam_kunjungan')
        ->whereDate('tanggal_kunjungan', '<=', $now->toDateString())
        ->cursor()
        ->each(function ($booking) use ($now, &$processedGroups, &$closed) {
            $groupKey = $booking->transaction_group_id ?: 'booking-' . $booking->id;
            if (isset($processedGroups[$groupKey])) {
                return;
            }
            $processedGroups[$groupKey] = true;

            $transactionBookings = $booking->transactionBookings()->with(['reservedSlots', 'services'])->get();
            $expired = $transactionBookings->every(function ($item) use ($now) {
                if (! $item->jam_kunjungan) {
                    return false;
                }
                $duration = (int) ($item->durasi_total_menit ?: $item->services->sum('pivot.durasi_menit'));
                $end = \Carbon\Carbon::parse($item->tanggal_kunjungan . ' ' . $item->jam_kunjungan, 'Asia/Jakarta')
                    ->addMinutes($duration + 15);

                return $end->lte($now);
            });

            if (! $expired) {
                return;
            }

            $reservedSlots = $transactionBookings->flatMap->reservedSlots->unique('id');
            \Illuminate\Support\Facades\DB::transaction(function () use ($transactionBookings, $reservedSlots) {
                foreach ($transactionBookings as $item) {
                    if (in_array($item->status, ['menunggu_pembayaran', 'dibayar'], true)) {
                        $item->update([
                            'status' => 'tidak_hadir',
                            'catatan_admin' => trim(($item->catatan_admin ? $item->catatan_admin . ' ' : '') . 'Ditutup otomatis oleh sistem: pelanggan tidak hadir.'),
                        ]);
                    }
                }

                foreach ($reservedSlots as $slot) {
                    if ($slot->terisi > 0) {
                        $slot->decrement('terisi');
                    }
                    if ($slot->status === 'penuh' && $slot->terisi < $slot->kuota) {
                        $slot->update(['status' => 'tersedia']);
                    }
                }
            });

            if ($booking->user?->no_whatsapp) {
                app(\App\Services\WhatsappService::class)->sendNotification(
                    $booking,
                    'no_show',
                    \App\Services\WhatsappService::buildNoShowMessage($booking)
                );
            }
            $closed++;
        });

    $this->info("{$closed} reservasi ditutup sebagai Tidak Hadir.");
})->purpose('Menutup otomatis reservasi yang melewati jadwal dan toleransi keterlambatan');

Schedule::command('slots:clean-expired')->daily();
Schedule::command('bookings:close-no-show')->everyMinute();

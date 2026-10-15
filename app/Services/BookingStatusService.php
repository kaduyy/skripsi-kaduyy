<?php

namespace App\Services;

use App\Models\Booking;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class BookingStatusService
{
    public static function markExpired(): void
    {
        $now = Carbon::now('Asia/Jakarta');
        $bookings = Booking::with(['services', 'payment', 'reservedSlots'])
            ->whereIn('status', ['menunggu_pembayaran', 'dibayar'])
            ->whereNotNull('tanggal_kunjungan')
            ->whereNotNull('jam_kunjungan')
            ->get();

        foreach ($bookings as $booking) {
            $duration = (int) ($booking->durasi_total_menit ?: $booking->services->sum('pivot.durasi_menit'));
            $end = Carbon::parse($booking->tanggal_kunjungan . ' ' . $booking->jam_kunjungan, 'Asia/Jakarta')
                ->addMinutes($duration);

            if ($end->isFuture()) {
                continue;
            }

            DB::transaction(function () use ($booking) {
                $locked = Booking::whereKey($booking->id)->lockForUpdate()->first();
                if (! $locked || ! in_array($locked->status, ['menunggu_pembayaran', 'dibayar'], true)) {
                    return;
                }

                if ($locked->status === 'dibayar') {
                    $locked->update(['status' => 'tidak_hadir']);
                    return;
                }

                $locked->update(['status' => 'dibatalkan']);
                $locked->payment()?->update(['status_pembayaran' => 'ditolak']);

                foreach ($booking->reservedSlots as $reservedSlot) {
                    if ($reservedSlot->terisi > 0) {
                        $reservedSlot->decrement('terisi');
                    }
                    $reservedSlot->refresh();
                    if ($reservedSlot->status === 'penuh' && $reservedSlot->terisi < $reservedSlot->kuota) {
                        $reservedSlot->update(['status' => 'tersedia']);
                    }
                }
            });
        }
    }
}

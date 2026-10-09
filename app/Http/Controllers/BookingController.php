<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class BookingController extends Controller
{
public function paymentProof(\App\Models\Booking $booking)
{
    $user = auth()->user();

    abort_unless(
        $user && ($booking->user_id === $user->id || $user->role === 'admin'),
        403
    );

    $path = $booking->bukti_transfer;

    abort_unless(
        $path && preg_match('/\Apayments\/[A-Za-z0-9._-]+\.(?:jpe?g|png|webp)\z/i', $path),
        404
    );

    $disk = \Illuminate\Support\Facades\Storage::disk('public');
    abort_unless($disk->exists($path), 404);

    return $disk->response($path);
}

public function create(\Illuminate\Http\Request $request)
{
    $selectedServiceIds = $request->query('service_ids', $request->query('service_id'));

    if ($selectedServiceIds === null) {
        $selectedServiceIds = $_GET['service_ids'] ?? $_GET['service_id'] ?? $_GET['service_ids[]'] ?? [];
    }

    if ($selectedServiceIds === null && $request->server('QUERY_STRING')) {
        parse_str($request->server('QUERY_STRING'), $queryParams);
        $selectedServiceIds = $queryParams['service_ids'] ?? $queryParams['service_id'] ?? [];
    }

    if (!is_array($selectedServiceIds)) {
        $selectedServiceIds = $selectedServiceIds ? [$selectedServiceIds] : [];
    }
    $selectedServiceIds = array_values(array_unique(array_map('intval', $selectedServiceIds)));
    $selectedStylistId = $request->integer('stylist_id') ?: null;
    $selectedDateFrom = $request->query('date_from');
    $selectedDateTo = $request->query('date_to');

    // Gunakan waktu Indonesia (WIB)
    $now = \Carbon\Carbon::now('Asia/Jakarta');
        $today = $now->copy()->startOfDay();
        $maximumBookingDate = $today->copy()->addDays(30);

        (new \App\Services\WorkingHoursService())->ensureDefaultSlots($today, $maximumBookingDate);

    // Hapus slot yang sudah lewat dan tidak memiliki booking
    \App\Models\Slot::where('tanggal', '<', $today->toDateString())
        ->whereDoesntHave('bookings')
        ->delete();

    // Ambil layanan aktif
    $services = \App\Models\Service::where('status', 'aktif')
        ->orderBy('nama_layanan')
        ->get();
    $stylists = \App\Models\Stylist::where('status', 'aktif')
        ->orderBy('nama')
        ->get();

    // Tampilkan slot yang belum selesai berdasarkan waktu WIB
    $slots = \App\Models\Slot::with('stylist')->where(function ($query) use ($now) {
            $query->whereDate('tanggal', '>', $now->toDateString())
                ->orWhere(function ($query) use ($now) {
                    $query->whereDate('tanggal', $now->toDateString())
                        ->whereTime('jam_selesai', '>', $now->format('H:i:s'));
                });
        })
        ->where('status', '!=', 'tutup')
        ->whereDate('tanggal', '<=', $maximumBookingDate->toDateString())
        ->when($selectedDateFrom, fn ($query) => $query->whereDate('tanggal', '>=', $selectedDateFrom))
        ->when($selectedDateTo, fn ($query) => $query->whereDate('tanggal', '<=', $selectedDateTo))
        ->when($selectedStylistId, fn ($query) => $query->where('stylist_id', $selectedStylistId), fn ($query) => $query->whereRaw('1 = 0'))
        ->orderBy('tanggal')
        ->orderBy('jam_mulai')
        ->get();

    $selectedServices = $services->whereIn('id', $selectedServiceIds);
    $selectedDurationMinutes = (int) $selectedServices->sum('durasi_menit');
    $bookingDate = $selectedDateFrom ?: $today->toDateString();
    $availableStartTimes = collect();

    if ($selectedStylistId && $selectedDurationMinutes > 0) {
        $dailySlots = $slots
            ->filter(fn ($slot) => \Carbon\Carbon::parse($slot->tanggal)->toDateString() === $bookingDate)
            ->values();

        foreach ($dailySlots as $dailySlot) {
            $cursor = \Carbon\Carbon::parse($bookingDate . ' ' . $dailySlot->jam_mulai, 'Asia/Jakarta');
            $slotEnd = \Carbon\Carbon::parse($bookingDate . ' ' . $dailySlot->jam_selesai, 'Asia/Jakarta');

            while ($cursor->lt($slotEnd)) {
                if ($this->isBookingStartAvailable($bookingDate, $selectedStylistId, $cursor, $selectedDurationMinutes, $dailySlots)) {
                    $availableStartTimes->put($cursor->format('H:i'), [
                        'time' => $cursor->format('H:i'),
                        'slot_id' => $dailySlot->id,
                        'label' => $cursor->format('H:i') . ' - ' . $cursor->copy()->addMinutes($selectedDurationMinutes)->format('H:i'),
                    ]);
                }
                $cursor->addMinutes(30);
            }
        }
        $availableStartTimes = $availableStartTimes->sortKeys()->values();
    }

    $slotAvailability = [];
    foreach ($slots as $slot) {
        $slotAvailability[$slot->id] = $services
            ->filter(function ($service) use ($slot, $now) {
                return $this->getAvailableBookingStartTimeInSlot($slot, $service, $now) !== null;
            })
            ->pluck('id')
            ->values()
            ->all();
    }

    return view('booking.create', compact('services', 'stylists', 'slots', 'slotAvailability', 'availableStartTimes', 'selectedServiceIds', 'selectedStylistId', 'selectedDurationMinutes', 'selectedDateFrom', 'selectedDateTo', 'today', 'maximumBookingDate'));
}

    public function store(Request $request)
    {
        $serviceIds = $request->input('service_ids', $request->input('service_id'));
        if (!is_array($serviceIds)) {
            $serviceIds = $serviceIds ? [$serviceIds] : [];
        }

        $validated = $request->validate([
            'service_ids' => 'required|array|min:1',
            'service_ids.*' => 'required|integer|exists:services,id',
            'stylist_id' => 'required|integer|exists:stylists,id',
            'booking_date' => 'nullable|date',
            'booking_time' => 'nullable|date_format:H:i',
            'slot_id' => 'required|integer|exists:slots,id',
            'payment_method' => 'required|in:transfer_manual,tunai',
            'catatan' => 'nullable|string|max:500',
        ]);

        $serviceIds = array_values(array_unique(array_map('intval', $validated['service_ids'])));
        $now = \Carbon\Carbon::now('Asia/Jakarta');
        $transactionGroupId = (string) \Illuminate\Support\Str::uuid();

        try {
            $booking = \DB::transaction(function () use ($serviceIds, $validated, $now, $transactionGroupId) {
                $maximumBookingDate = $now->copy()->startOfDay()->addDays(30);
                $requestedSlot = \App\Models\Slot::findOrFail($validated['slot_id']);
                $bookingDate = \Carbon\Carbon::parse($validated['booking_date'] ?? $requestedSlot->tanggal, 'Asia/Jakarta')->toDateString();
                if ($bookingDate < $now->toDateString() || $bookingDate > $maximumBookingDate->toDateString()) {
                    throw new \RuntimeException('Reservasi hanya dapat dibuat untuk hari ini sampai maksimal 30 hari ke depan.');
                }
                if ((int) $requestedSlot->stylist_id !== (int) $validated['stylist_id'] || $requestedSlot->tanggal !== $bookingDate) {
                    throw new \RuntimeException('Stylist, tanggal, dan slot waktu yang dipilih tidak sesuai.');
                }

                $slots = \App\Models\Slot::whereDate('tanggal', $requestedSlot->tanggal)
                    ->where('stylist_id', $validated['stylist_id'])
                    ->where('status', '!=', 'tutup')
                    ->orderBy('tanggal')
                    ->orderBy('jam_mulai')
                    ->lockForUpdate()
                    ->get()
                    ->keyBy('id');
                $slot = $slots->get($requestedSlot->id);

                $services = \App\Models\Service::whereIn('id', $serviceIds)
                    ->where('status', 'aktif')
                    ->get();

                if ($services->count() !== count($serviceIds)) {
                    throw new \RuntimeException('Beberapa layanan yang dipilih tidak tersedia saat ini.');
                }

                $totalDurationMinutes = (int) $services->sum('durasi_menit');
                $totalHarga = $services->sum('harga');
                $dp_amount = $totalHarga * 0.20;

                $bookingTime = $validated['booking_time']
                    ?? \Carbon\Carbon::parse($requestedSlot->jam_mulai)->format('H:i');
                $bookingStart = \Carbon\Carbon::parse($bookingDate . ' ' . $bookingTime, 'Asia/Jakarta');
                if (! $this->isBookingStartAvailable($bookingDate, (int) $validated['stylist_id'], $bookingStart, $totalDurationMinutes, $slots)) {
                    throw new \RuntimeException('Slot berurutan untuk total durasi layanan tidak tersedia. Silakan pilih jam mulai lain.');
                }

                $reservedSlots = $validated['payment_method'] === 'transfer_manual'
                    ? $this->getSlotsCoveringDuration($slots, $bookingStart, $totalDurationMinutes)
                    : collect();

                if ($validated['payment_method'] === 'transfer_manual' && $reservedSlots === null) {
                    throw new \RuntimeException('Slot berurutan untuk total durasi layanan tidak tersedia. Silakan pilih jam mulai lain.');
                }

                $booking = \App\Models\Booking::create([
                    'user_id' => auth()->id(),
                    'stylist_id' => $validated['stylist_id'],
                    'transaction_group_id' => $transactionGroupId,
                    'slot_id' => $slot->id,
                    'nama_pelanggan' => auth()->user()->name,
                    'no_whatsapp' => auth()->user()->no_whatsapp ?? '080000000000',
                    'tanggal_kunjungan' => $bookingDate,
                    'jam_kunjungan' => $bookingStart->format('H:i:s'),
                    'durasi_total_menit' => $totalDurationMinutes,
                    'total_harga' => $totalHarga,
                    'dp_amount' => $dp_amount,
                    'status' => 'menunggu_pembayaran',
                    'catatan' => $validated['catatan'] ?? null,
                ]);

                $booking->services()->sync(
                    $services->mapWithKeys(function ($service) {
                        return [
                            $service->id => [
                                'harga' => $service->harga,
                                'durasi_menit' => $service->durasi_menit,
                            ],
                        ];
                    })->all()
                );
                if ($reservedSlots->isNotEmpty()) {
                    $booking->reservedSlots()->sync($reservedSlots->pluck('id')->all());
                }

                foreach ($reservedSlots as $reservedSlot) {
                    $reservedSlot->increment('terisi');
                    $reservedSlot->refresh();

                    if ($reservedSlot->terisi >= $reservedSlot->kuota) {
                        $reservedSlot->update(['status' => 'penuh']);
                    }
                }

                \App\Models\Payment::create([
                    'booking_id' => $booking->id,
                    'jumlah' => $validated['payment_method'] === 'tunai' ? $totalHarga : $dp_amount,
                    'metode' => $validated['payment_method'],
                    'status_pembayaran' => 'pending',
                ]);

                return $booking;
            });
        } catch (\Throwable $e) {
            return back()->with('error', $e->getMessage());
        }

        $message = $booking->payment->metode === 'tunai'
            ? 'Permintaan reservasi cash dibuat. Slot belum dikunci dan dapat terisi sebelum admin mengonfirmasi.'
            : 'Reservasi berhasil dibuat. Unggah bukti transfer untuk DP Rp ' . number_format($booking->dp_amount, 0, ',', '.') . '.';

        return redirect()->route('booking.show', $booking->id)->with('success', $message);
    }

    private function getAvailableBookingStartTimeInSlot($slot, $services, $now, $slots = null): ?\Carbon\Carbon
    {
        $slotStart = \Carbon\Carbon::parse($slot->tanggal . ' ' . $slot->jam_mulai, 'Asia/Jakarta');
        $durationMinutes = (int) collect($services)->sum('durasi_menit');

        if ($slotStart->lessThan($now) || $slot->status !== 'tersedia') {
            return null;
        }

        $availableSlots = collect($slots ?? \App\Models\Slot::whereDate('tanggal', $slot->tanggal)
            ->where('stylist_id', $slot->stylist_id)
            ->where('status', 'tersedia')
            ->orderBy('jam_mulai')
            ->get())
            ->where('tanggal', $slot->tanggal)
            ->where('stylist_id', $slot->stylist_id)
            ->where('status', 'tersedia')
            ->values();
        $reservedSlots = $this->getSlotsCoveringDuration($availableSlots, $slotStart, $durationMinutes);

        if ($reservedSlots === null) {
            return null;
        }

        foreach ($reservedSlots as $reservedSlot) {
            $activeReservations = \Illuminate\Support\Facades\DB::table('booking_slot')
                ->join('bookings', 'bookings.id', '=', 'booking_slot.booking_id')
                ->where('booking_slot.slot_id', $reservedSlot->id)
                ->whereNotIn('bookings.status', ['dibatalkan'])
                ->count();

            if (max($reservedSlot->terisi, $activeReservations) >= $reservedSlot->kuota) {
                return null;
            }
        }

        $candidateEnd = $slotStart->copy()->addMinutes($durationMinutes);
        $legacyConflict = \App\Models\Booking::whereDate('tanggal_kunjungan', $slot->tanggal)
            ->whereNotIn('status', ['dibatalkan'])
            ->whereDoesntHave('reservedSlots')
            ->whereDoesntHave('payment', function ($query) {
                $query->where('metode', 'tunai')
                    ->where('status_pembayaran', 'pending');
            })
            ->whereHas('slot', function ($query) use ($slot) {
                $query->where('stylist_id', $slot->stylist_id);
            })
            ->with('services')
            ->get()
            ->contains(function ($booking) use ($slotStart, $candidateEnd) {
                $bookingStart = \Carbon\Carbon::parse($booking->tanggal_kunjungan . ' ' . $booking->jam_kunjungan, 'Asia/Jakarta');
                $bookingDuration = (int) ($booking->durasi_total_menit ?: $booking->services->sum('pivot.durasi_menit'));
                $bookingEnd = $bookingStart->copy()->addMinutes($bookingDuration);

                return $slotStart->lt($bookingEnd) && $bookingStart->lt($candidateEnd);
            });

        return $legacyConflict ? null : $slotStart;
    }

    private function isBookingStartAvailable(string $date, int $stylistId, \Carbon\Carbon $start, int $durationMinutes, $slots): bool
    {
        if ($start->lt(\Carbon\Carbon::now('Asia/Jakarta'))) {
            return false;
        }

        $end = $start->copy()->addMinutes($durationMinutes);

        // The selected time must be fully covered by the stylist's working slots.
        if ($this->getSlotsCoveringDuration($slots, $start, $durationMinutes) === null) {
            return false;
        }

        $bookings = \App\Models\Booking::with('services')
            ->whereDate('tanggal_kunjungan', $date)
            ->where('stylist_id', $stylistId)
            ->whereNotIn('status', ['dibatalkan'])
            ->whereNotNull('jam_kunjungan')
            ->whereDoesntHave('payment', function ($query) {
                $query->where('metode', 'tunai')
                    ->where('status_pembayaran', 'pending');
            })
            ->get();

        foreach ($bookings as $booking) {
            $bookingStart = \Carbon\Carbon::parse($date . ' ' . $booking->jam_kunjungan, 'Asia/Jakarta');
            $bookingDuration = (int) ($booking->durasi_total_menit ?: $booking->services->sum('pivot.durasi_menit'));
            $bookingEnd = $bookingStart->copy()->addMinutes($bookingDuration);

            if ($start->lt($bookingEnd) && $bookingStart->lt($end)) {
                return false;
            }
        }

        return true;
    }

    private function getSlotsCoveringDuration($slots, \Carbon\Carbon $start, int $durationMinutes): ?\Illuminate\Support\Collection
    {
        $end = $start->copy()->addMinutes($durationMinutes);
        $cursor = $start->copy();
        $coveredSlots = collect();

        while ($cursor->lt($end)) {
            $coveringSlot = collect($slots)->first(function ($slot) use ($cursor) {
                $slotStart = \Carbon\Carbon::parse($slot->tanggal . ' ' . $slot->jam_mulai, 'Asia/Jakarta');
                $slotEnd = \Carbon\Carbon::parse($slot->tanggal . ' ' . $slot->jam_selesai, 'Asia/Jakarta');

                return $slotStart->lte($cursor) && $slotEnd->gt($cursor);
            });

            if (! $coveringSlot) {
                return null;
            }

            $coveredSlots->put($coveringSlot->id, $coveringSlot);
            $slotEnd = \Carbon\Carbon::parse($coveringSlot->tanggal . ' ' . $coveringSlot->jam_selesai, 'Asia/Jakarta');
            $cursor = $slotEnd->lt($end) ? $slotEnd : $end;
        }

        return $coveredSlots->values();
    }

    public function show(\App\Models\Booking $booking)
    {
        // Pastikan hanya pemilik atau admin yang bisa melihat
        if ($booking->user_id !== auth()->id() && auth()->user()->role !== 'admin') {
            abort(403);
        }

        $booking->load([
            'stylist',
            'services',
            'slot',
            'payment',
            'reservedSlots',
            'transactionBookings.service',
            'transactionBookings.services',
            'transactionBookings.stylist',
            'transactionBookings.slot',
            'transactionBookings.reservedSlots',
            'transactionBookings.payment',
        ]);
        $transactionBookings = $booking->transactionBookings->sortBy([
            ['tanggal_kunjungan', 'asc'],
            ['jam_kunjungan', 'asc'],
        ])->values();
        // Nilai decimal MySQL dikembalikan sebagai string; cast eksplisit
        // agar total tidak terbaca 0 saat dirender di halaman reservasi.
        $transactionTotal = $transactionBookings->sum(fn ($item) => (float) $item->total_harga);
        $transactionDpTotal = $transactionBookings->sum(fn ($item) => (float) $item->dp_amount);

        // Selalu tampilkan nilai booking utama sebagai fallback agar nominal
        // tidak menjadi Rp0 jika relasi transaksi belum termuat sempurna.
        $transactionTotal = $transactionTotal > 0
            ? $transactionTotal
            : (float) $booking->total_harga;
        $transactionDpTotal = $transactionDpTotal > 0
            ? $transactionDpTotal
            : (float) $booking->dp_amount;
        $bookingDurationMinutes = (int) ($booking->durasi_total_menit ?: $booking->services->sum('pivot.durasi_menit'));
        $bookingEnd = $booking->jam_kunjungan
            ? \Carbon\Carbon::parse($booking->tanggal_kunjungan . ' ' . $booking->jam_kunjungan, 'Asia/Jakarta')
                ->addMinutes($bookingDurationMinutes)
            : null;

        return view('booking.show', compact('booking', 'transactionBookings', 'transactionTotal', 'transactionDpTotal', 'bookingEnd'));
    }

    public function uploadPayment(Request $request, \App\Models\Booking $booking)
    {
        if ($booking->user_id !== auth()->id()) {
            abort(403);
        }

        if ($booking->payment?->metode === 'tunai') {
            return back()->with('error', 'Bukti transfer tidak diperlukan untuk pembayaran cash.');
        }

        $request->validate([
            'bukti_pembayaran' => 'required|image|max:2048',
        ]);

        $path = $request->file('bukti_pembayaran')->store('payments', 'public');
        $transactionBookings = $booking->transactionBookings()->get();
        $bookingIds = $transactionBookings->pluck('id');

        \DB::transaction(function () use ($transactionBookings, $bookingIds, $path) {
            \App\Models\Booking::whereIn('id', $bookingIds)->update([
                'bukti_transfer' => $path,
            ]);

            foreach ($transactionBookings as $transactionBooking) {
                \App\Models\Payment::updateOrCreate(
                    ['booking_id' => $transactionBooking->id],
                    [
                        'jumlah' => $transactionBooking->dp_amount,
                        'metode' => 'transfer_manual',
                        'status_pembayaran' => 'pending',
                        'verified_by' => null,
                        'verified_at' => null,
                    ]
                );
            }
        });

        return redirect()->route('booking.show', $booking->id)->with('success', 'Satu bukti transfer berhasil diunggah untuk seluruh booking dalam transaksi ini. Silakan tunggu verifikasi admin.');
    }

    public function storeReview(Request $request, \App\Models\Booking $booking)
    {
        if ($booking->user_id !== auth()->id() || $booking->status !== 'selesai') {
            abort(403);
        }

        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'komentar' => 'required|string|max:1000',
        ]);

        \App\Models\Review::create([
            'booking_id' => $booking->id,
            'user_id' => auth()->id(),
            'rating' => $request->rating,
            'komentar' => $request->komentar,
        ]);

        return redirect()->route('booking.show', $booking->id)->with('success', 'Terima kasih atas ulasan Anda!');
    }
    public function cancel(\App\Models\Booking $booking)
    {
        if ($booking->user_id !== auth()->id() || $booking->status !== 'menunggu_pembayaran') {
            abort(403);
        }

        $transactionBookings = $booking->transactionBookings()->with('reservedSlots')->get();
        $reservedSlots = $transactionBookings->flatMap->reservedSlots->unique('id');

        \DB::transaction(function () use ($transactionBookings, $reservedSlots) {
            foreach ($transactionBookings as $transactionBooking) {
                $transactionBooking->update(['status' => 'dibatalkan']);
            }

            foreach ($reservedSlots as $reservedSlot) {
                if ($reservedSlot->terisi > 0) {
                    $reservedSlot->decrement('terisi');
                    $reservedSlot->refresh();
                }

                if ($reservedSlot->status === 'penuh' && $reservedSlot->terisi < $reservedSlot->kuota) {
                    $reservedSlot->update(['status' => 'tersedia']);
                }
            }
        });

        return redirect()->route('dashboard')->with('success', 'Seluruh reservasi dalam transaksi berhasil dibatalkan.');
    }
}

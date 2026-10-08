<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Service;
use App\Models\Slot;
use App\Services\WhatsappService;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    protected WhatsappService $whatsapp;

    public function __construct(WhatsappService $whatsapp)
    {
        $this->whatsapp = $whatsapp;
    }

    // =====================================================
    // DAFTAR RESERVASI
    // =====================================================
    public function index(Request $request)
    {
        $status = $request->get('status');

        $allowedStatuses = [
            'menunggu_pembayaran',
            'dibayar',
            'dibatalkan',
            'selesai',
        ];

        $query = Booking::with([
            'user',
            'services',
            'slot',
            'payment'
        ])->orderBy('created_at', 'desc');

        // Jika memilih salah satu filter
        if ($status && in_array($status, $allowedStatuses)) {
            $query->where('status', $status);
        }

        $bookings = $query->paginate(15)->withQueryString();

        // Jumlah masing-masing status
        $waitingCount = Booking::where(
            'status',
            'menunggu_pembayaran'
        )->count();

        $paidCount = Booking::where(
            'status',
            'dibayar'
        )->count();

        $cancelledCount = Booking::where(
            'status',
            'dibatalkan'
        )->count();

        $completedCount = Booking::where(
            'status',
            'selesai'
        )->count();

        return view('admin.bookings.index', compact(
            'bookings',
            'status',
            'waitingCount',
            'paidCount',
            'cancelledCount',
            'completedCount'
        ));
    }

    public function create()
    {
        $services = Service::where('status', 'aktif')
            ->orderBy('nama_layanan')
            ->get();

        $slots = Slot::where('tanggal', '>=', now()->toDateString())
            ->with('stylist')
            ->whereColumn('terisi', '<', 'kuota')
            ->where('status', 'tersedia')
            ->orderBy('tanggal')
            ->orderBy('jam_mulai')
            ->get();

        return view('admin.bookings.create', compact('services', 'slots'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_pelanggan' => ['required', 'string', 'max:100'],
            'no_whatsapp' => ['required', 'string', 'max:20'],
            'service_id' => ['required', 'exists:services,id'],
            'slot_id' => ['required', 'exists:slots,id'],
            'catatan_admin' => ['nullable', 'string', 'max:1000'],
        ]);

        $booking = DB::transaction(function () use ($validated, $request) {
            $slot = Slot::whereKey($validated['slot_id'])
                ->lockForUpdate()
                ->firstOrFail();

            if (\Carbon\Carbon::parse($slot->tanggal)->isBefore(now()->startOfDay()) || $slot->terisi >= $slot->kuota || $slot->status !== 'tersedia') {
                abort(422, 'Slot yang dipilih sudah tidak tersedia.');
            }

            $service = Service::findOrFail($validated['service_id']);

            $booking = Booking::create([
                'user_id' => auth()->id(),
                'stylist_id' => $slot->stylist_id,
                'slot_id' => $slot->id,
                'nama_pelanggan' => $validated['nama_pelanggan'],
                'no_whatsapp' => $validated['no_whatsapp'],
                'tanggal_kunjungan' => $slot->tanggal,
                'jam_kunjungan' => $slot->jam_mulai,
                'durasi_total_menit' => $service->durasi_menit,
                'total_harga' => $service->harga,
                'dp_amount' => $service->harga,
                'status' => 'dibayar',
                'catatan_admin' => trim('Walk-in. ' . ($validated['catatan_admin'] ?? '')),
            ]);

            $booking->services()->sync([
                $service->id => [
                    'harga' => $service->harga,
                    'durasi_menit' => $service->durasi_menit,
                ],
            ]);
            $booking->reservedSlots()->sync([$slot->id]);

            \App\Models\Payment::create([
                'booking_id' => $booking->id,
                'jumlah' => $service->harga,
                'metode' => 'transfer_manual',
                'status_pembayaran' => 'terverifikasi',
                'verified_by' => $request->user()->id,
                'verified_at' => now(),
            ]);

            $slot->increment('terisi');
            if ($slot->terisi >= $slot->kuota) {
                $slot->update(['status' => 'penuh']);
            }

            return $booking;
        });

        return redirect()
            ->route('admin.bookings.show', $booking)
            ->with('success', 'Reservasi walk-in berhasil ditambahkan.');
    }

    // =====================================================
    // DETAIL RESERVASI
    // =====================================================
    public function show(Booking $booking)
    {
        $booking->load([
            'user',
            'stylist',
            'services',
            'slot',
            'payment',
            'review',
            'reservedSlots',
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
        $transactionTotal = $transactionBookings->sum(fn ($item) => (float) $item->total_harga);
        $transactionDpTotal = $transactionBookings->sum(fn ($item) => (float) $item->dp_amount);
        $transactionTotal = $transactionTotal > 0
            ? $transactionTotal
            : (float) $booking->total_harga;
        $transactionDpTotal = $transactionDpTotal > 0
            ? $transactionDpTotal
            : (float) $booking->dp_amount;
        $bookingDurationMinutes = (int) ($booking->durasi_total_menit ?: $booking->services->sum('pivot.durasi_menit'));
        $bookingEnd = \Carbon\Carbon::parse($booking->tanggal_kunjungan . ' ' . $booking->jam_kunjungan, 'Asia/Jakarta')
            ->addMinutes($bookingDurationMinutes);

        return view(
            'admin.bookings.show',
            compact('booking', 'transactionBookings', 'transactionTotal', 'transactionDpTotal', 'bookingEnd')
        );
    }

    // =====================================================
    // APPROVE / VERIFIKASI PEMBAYARAN
    // =====================================================
    public function approve(Booking $booking)
    {
        $approved = DB::transaction(function () use ($booking) {
            $transactionBookings = $booking->transactionBookings()->lockForUpdate()->get();

            if ($transactionBookings->isEmpty() || $transactionBookings->contains(fn ($item) => ! $item->bukti_transfer)) {
                return false;
            }

            $bookingIds = $transactionBookings->pluck('id');
            \App\Models\Payment::whereIn('booking_id', $bookingIds)->update([
                'status_pembayaran' => 'terverifikasi',
                'verified_by' => auth()->id(),
                'verified_at' => now(),
            ]);
            Booking::whereIn('id', $bookingIds)->update(['status' => 'dibayar']);

            return true;
        });

        if ($approved) {

            // Load data untuk pesan WhatsApp
            $booking->loadMissing([
                'user',
                'services',
                'slot'
            ]);

            // Kirim notifikasi WhatsApp ke pelanggan
            if ($booking->user?->no_whatsapp) {

                $message = WhatsappService::buildApprovalMessage(
                    $booking
                );

                $this->whatsapp->sendNotification(
                    $booking,
                    'approved',
                    $message
                );
            }

            return back()->with(
                'success',
                'Pembayaran DP telah diverifikasi dan reservasi disetujui.'
            );
        }

        return back()->with(
            'error',
            'Tidak dapat menyetujui, bukti pembayaran belum diunggah.'
        );
    }

    // =====================================================
    // REJECT / BATALKAN RESERVASI
    // =====================================================
    public function reject(
        Request $request,
        Booking $booking
    ) {
        $transactionBookings = $booking->transactionBookings()->with('reservedSlots')->get();
        $bookingIds = $transactionBookings->pluck('id');
        $reservedSlots = $transactionBookings->flatMap->reservedSlots->unique('id');

        DB::transaction(function () use ($bookingIds, $reservedSlots) {
            \App\Models\Payment::whereIn('booking_id', $bookingIds)->update([
                'status_pembayaran' => 'ditolak',
            ]);
            Booking::whereIn('id', $bookingIds)->update(['status' => 'dibatalkan']);

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

        // Kirim notifikasi WhatsApp ke pelanggan
        $booking->loadMissing([
            'user'
        ]);

        if ($booking->user?->no_whatsapp) {

            $message = WhatsappService::buildRejectionMessage(
                $booking
            );

            $this->whatsapp->sendNotification(
                $booking,
                'rejected',
                $message
            );
        }

        return back()->with(
            'success',
            'Reservasi telah dibatalkan.'
        );
    }

    // =====================================================
    // SELESAIKAN RESERVASI
    // =====================================================
    public function complete(Booking $booking)
    {
        $booking->update([
            'status' => 'selesai'
        ]);

        // Kirim notifikasi WhatsApp ke pelanggan
        $booking->loadMissing([
            'user'
        ]);

        if ($booking->user?->no_whatsapp) {

            $message = WhatsappService::buildCompletionMessage(
                $booking
            );

            $this->whatsapp->sendNotification(
                $booking,
                'completed',
                $message
            );
        }

        return back()->with(
            'success',
            'Reservasi telah diselesaikan.'
        );
    }
}

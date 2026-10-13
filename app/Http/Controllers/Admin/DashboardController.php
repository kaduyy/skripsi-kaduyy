<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Payment;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $today = now()->toDateString();
        $startDate = $request->get('start_date', now()->startOfMonth()->toDateString());
        $endDate = $request->get('end_date', now()->toDateString());

        if ($startDate && $endDate && $startDate > $endDate) {
            [$startDate, $endDate] = [$endDate, $startDate];
        }

        $status = $request->get('status');

        $allowedStatuses = [
            'menunggu_pembayaran',
            'dibayar',
            'dibatalkan',
        ];

        $incomeToday = $this->completedBookingsBetween($today, $today)->sum('total_harga');
        $incomeMonth = $this->completedBookingsBetween($startDate, $endDate)->sum('total_harga');
        $receivedMonth = $this->verifiedPaymentsBetween($startDate, $endDate)->sum('jumlah');

        $bookingsToday = Booking::whereHas('slot', function ($q) use ($today) {
            $q->where('tanggal', $today);
        })->count();

        $bookingsMonth = Booking::whereHas('slot', function ($q) use ($startDate, $endDate) {
            $q->whereBetween('tanggal', [$startDate, $endDate]);
        })->count();

        $waitingCount = Booking::where('status', 'menunggu_pembayaran')->count();
        $paidCount = $this->completedBookingsBetween($startDate, $endDate)->count();
        $cancelledCount = Booking::where('status', 'dibatalkan')->count();

        $recentBookingsQuery = Booking::with([
            'user',
            'services',
            'slot',
            'payment'
        ])->orderBy('created_at', 'desc');

        if ($status && in_array($status, $allowedStatuses)) {
            $recentBookingsQuery->where('status', $status);
        } else {
            $recentBookingsQuery->whereIn('status', $allowedStatuses);
        }

        $recentBookings = $recentBookingsQuery->take(5)->get();

        return view(
            'admin.dashboard',
            compact(
                'incomeToday',
                'incomeMonth',
                'receivedMonth',
                'bookingsToday',
                'bookingsMonth',
                'recentBookings',
                'waitingCount',
                'paidCount',
                'cancelledCount',
                'status',
                'startDate',
                'endDate'
            )
        );
    }

    public function reportIndex(Request $request)
    {
        $startDate = $request->get('start_date', now()->startOfMonth()->toDateString());
        $endDate = $request->get('end_date', now()->toDateString());

        if ($startDate && $endDate && $startDate > $endDate) {
            [$startDate, $endDate] = [$endDate, $startDate];
        }

        $incomeToday = $this->completedBookingsBetween(now()->toDateString(), now()->toDateString())->sum('total_harga');
        $incomeMonth = $this->completedBookingsBetween($startDate, $endDate)->sum('total_harga');
        $bookingsMonth = $this->completedBookingsBetween($startDate, $endDate)->count();
        $recentPayments = $this->verifiedPaymentsBetween($startDate, $endDate)
            ->with(['booking.user', 'booking.services'])
            ->orderByRaw('COALESCE(verified_at, updated_at) DESC')
            ->take(20)
            ->get();
        $receivedForCompletedMonth = $this->completedPaymentsBetween($startDate, $endDate)->sum('jumlah');
        $receivedMonth = $this->verifiedPaymentsBetween($startDate, $endDate)->sum('jumlah');
        $outstandingMonth = max(0, (float) $incomeMonth - (float) $receivedForCompletedMonth);
        $paidCount = $bookingsMonth;
        $status = null;

        return view(
            'admin.reports.index',
            compact(
                'incomeToday',
                'incomeMonth',
                'receivedMonth',
                'outstandingMonth',
                'bookingsMonth',
                'recentPayments',
                'paidCount',
                'status',
                'startDate',
                'endDate'
            )
        );
    }

    // =========================================================
    // DOWNLOAD LAPORAN PENDAPATAN PDF
    // =========================================================

    public function downloadPdf(Request $request)
    {
        $startDate = $request->get('start_date', now()->startOfMonth()->toDateString());
        $endDate = $request->get('end_date', now()->toDateString());

        if ($startDate && $endDate && $startDate > $endDate) {
            [$startDate, $endDate] = [$endDate, $startDate];
        }

        $payments = $this->verifiedPaymentsBetween($startDate, $endDate)
            ->with(['booking.user', 'booking.services'])
            ->orderByRaw('COALESCE(verified_at, updated_at)')
            ->get();
        $completedBookings = $this->completedBookingsBetween($startDate, $endDate)->get();
        $totalUangDiterima = $payments->sum('jumlah');
        $totalPendapatan = $completedBookings->sum('total_harga');
        $totalReservasi = $completedBookings->count();
        $totalDibayar = $payments
            ->filter(fn (Payment $payment) => $payment->booking?->status === 'dibayar')
            ->sum('jumlah');
        $totalSelesai = $payments
            ->filter(fn (Payment $payment) => $payment->booking?->status === 'selesai')
            ->sum('jumlah');

        // =====================================================
        // BUAT PDF
        // =====================================================

        $pdf = Pdf::loadView(
            'admin.dashboard-pdf',
            compact(
                'payments',
                'totalPendapatan',
                'totalUangDiterima',
                'totalReservasi',
                'totalDibayar',
                'totalSelesai',
                'startDate',
                'endDate'
            )
        );

        // Ukuran kertas
        $pdf->setPaper(
            'A4',
            'portrait'
        );

        // Nama file PDF
        $fileName =
            'laporan-pendapatan-' .
            date('Y-m-d-His') .
            '.pdf';

        return $pdf->download(
            $fileName
        );
    }

    private function verifiedPaymentsBetween(string $startDate, string $endDate)
    {
        $start = $startDate . ' 00:00:00';
        $end = $endDate . ' 23:59:59';

        return Payment::query()
            ->where('status_pembayaran', 'terverifikasi')
            ->where(function ($query) use ($start, $end) {
                $query->whereBetween('verified_at', [$start, $end])
                    ->orWhere(function ($query) use ($start, $end) {
                        $query->whereNull('verified_at')
                            ->whereBetween('updated_at', [$start, $end]);
                    });
            });
    }

    private function completedPaymentsBetween(string $startDate, string $endDate)
    {
        return $this->verifiedPaymentsBetween($startDate, $endDate)
            ->whereHas('booking', function ($query) use ($startDate, $endDate) {
                $query->where('status', 'selesai')
                    ->whereBetween('tanggal_kunjungan', [$startDate, $endDate]);
            });
    }

    private function completedBookingsBetween(string $startDate, string $endDate)
    {
        return Booking::query()
            ->where('status', 'selesai')
            ->whereBetween('tanggal_kunjungan', [$startDate, $endDate]);
    }
}

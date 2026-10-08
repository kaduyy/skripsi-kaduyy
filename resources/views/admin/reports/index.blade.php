@extends('layouts.admin')

@section('header', 'Laporan Pendapatan')

@section('content')

@php
    $startDate = request('start_date', now()->startOfMonth()->toDateString());
    $endDate = request('end_date', now()->toDateString());
    $rangeLabel = \Carbon\Carbon::parse($startDate)->translatedFormat('d F Y') . ' - ' . \Carbon\Carbon::parse($endDate)->translatedFormat('d F Y');
@endphp

<div class="space-y-6">
    <div class="rounded-2xl border border-[#E7E1DA] bg-white p-5 shadow-sm sm:p-6">
        <div class="flex flex-col gap-4 xl:flex-row xl:items-end xl:justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-[#E76F51]">Ringkasan Keuangan</p>
                <h3 class="mt-2 text-2xl font-semibold text-gray-900">Pendapatan Salon</h3>
                <p class="mt-1 text-sm text-gray-500">Periode: {{ $rangeLabel }}</p>
            </div>

            <div class="flex w-full flex-col gap-3 sm:flex-row sm:items-center xl:w-auto">
                <div class="flex items-center gap-2 rounded-xl border border-gray-200 bg-[#FAFAF9] px-2 py-2">
                    <label for="start_date" class="text-[10px] font-semibold uppercase tracking-wide text-gray-500">Dari</label>
                    <input
                        id="start_date"
                        type="date"
                        value="{{ $startDate }}"
                        onchange="window.location='{{ route('admin.reports.index') }}?start_date=' + this.value + '&end_date=' + document.getElementById('end_date').value"
                        class="rounded-lg border border-gray-200 bg-white px-2 py-1.5 text-sm text-gray-700 focus:border-[#E76F51] focus:outline-none focus:ring-2 focus:ring-[#E76F51]/20"
                    >
                </div>

                <div class="flex items-center gap-2 rounded-xl border border-gray-200 bg-[#FAFAF9] px-2 py-2">
                    <label for="end_date" class="text-[10px] font-semibold uppercase tracking-wide text-gray-500">Sampai</label>
                    <input
                        id="end_date"
                        type="date"
                        value="{{ $endDate }}"
                        onchange="window.location='{{ route('admin.reports.index') }}?start_date=' + document.getElementById('start_date').value + '&end_date=' + this.value"
                        class="rounded-lg border border-gray-200 bg-white px-2 py-1.5 text-sm text-gray-700 focus:border-[#E76F51] focus:outline-none focus:ring-2 focus:ring-[#E76F51]/20"
                    >
                </div>

                <a
                    href="{{ route('admin.dashboard.downloadPdf', ['start_date' => $startDate, 'end_date' => $endDate]) }}"
                    class="inline-flex items-center justify-center gap-2 rounded-xl bg-[#1A3B32] px-4 py-2.5 text-sm font-medium text-white transition hover:bg-[#244d41]"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="w-4 h-4">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M7.5 12l4.5 4.5m0 0 4.5-4.5m-4.5 4.5V3" />
                    </svg>
                    Download PDF
                </a>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-4">
        <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm">
            <p class="text-sm text-gray-500">Pendapatan Hari Ini</p>
            <h4 class="mt-3 text-2xl font-semibold text-gray-900">Rp {{ number_format($incomeToday, 0, ',', '.') }}</h4>
        </div>

        <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm">
            <p class="text-sm text-gray-500">Pendapatan Periode</p>
            <h4 class="mt-3 text-2xl font-semibold text-gray-900">Rp {{ number_format($incomeMonth, 0, ',', '.') }}</h4>
        </div>

        <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm">
            <p class="text-sm text-gray-500">Jumlah Booking</p>
            <h4 class="mt-3 text-2xl font-semibold text-gray-900">{{ $bookingsMonth }}</h4>
        </div>

        <div class="rounded-2xl border border-[#E76F51]/20 bg-[#FFF7F4] p-5 shadow-sm">
            <p class="text-sm text-gray-600">Status Terverifikasi</p>
            <h4 class="mt-3 text-2xl font-semibold text-[#1A3B32]">{{ $paidCount }}</h4>
        </div>
    </div>

    <div class="rounded-2xl border border-gray-200 bg-white shadow-sm overflow-hidden">
        <div class="flex items-center justify-between border-b border-gray-100 px-5 py-4 sm:px-6">
            <h3 class="text-lg font-semibold text-gray-900">Detail Transaksi</h3>
            <a href="{{ route('admin.bookings.index') }}" class="text-sm font-medium text-[#E76F51] hover:underline">Lihat semua</a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-gray-600">
                <thead class="bg-gray-50 text-gray-500">
                    <tr>
                        <th class="whitespace-nowrap border-b border-gray-100 px-4 py-4 sm:px-6">Pelanggan</th>
                        <th class="whitespace-nowrap border-b border-gray-100 px-4 py-4 sm:px-6">Layanan</th>
                        <th class="whitespace-nowrap border-b border-gray-100 px-4 py-4 sm:px-6">Tanggal</th>
                        <th class="whitespace-nowrap border-b border-gray-100 px-4 py-4 sm:px-6">Status</th>
                        <th class="whitespace-nowrap border-b border-gray-100 px-4 py-4 sm:px-6 text-right">Nominal</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-100">
                    @forelse($recentPayments as $payment)
                        @php
                            $booking = $payment->booking;
                            $paymentDate = $payment->verified_at ?? $payment->updated_at;
                            $paymentDateText = $paymentDate ? \Carbon\Carbon::parse($paymentDate)->format('d/m/Y') : '-';
                        @endphp
                        @continue(!$booking)
                        <tr class="hover:bg-gray-50/50 transition">
                            <td class="px-4 py-4 sm:px-6">
                                <span class="block font-semibold text-gray-900">{{ $booking->user?->name ?? $booking->nama_pelanggan }}</span>
                                <span class="text-xs text-gray-500">#{{ str_pad($booking->id, 5, '0', STR_PAD_LEFT) }}</span>
                            </td>
                            <td class="px-4 py-4 sm:px-6">{{ $booking->services->pluck('nama_layanan')->implode(', ') ?: '-' }}</td>
                            <td class="whitespace-nowrap px-4 py-4 sm:px-6">
                                {{ $paymentDateText }}
                            </td>
                            <td class="px-4 py-4 sm:px-6">
                                <span class="inline-flex items-center rounded-full bg-green-100 px-2.5 py-1 text-xs font-medium text-green-800">Terverifikasi</span>
                            </td>
                            <td class="px-4 py-4 text-right sm:px-6">
                                <span class="font-semibold text-gray-900">Rp {{ number_format($payment->jumlah, 0, ',', '.') }}</span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-gray-500">Belum ada data transaksi.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

@endsection

@extends('layouts.admin')

@section('header', 'Dashboard Admin')

@section('content')

@php
    $startDate = request('start_date', now()->startOfMonth()->toDateString());
    $endDate = request('end_date', now()->toDateString());
    $rangeLabel = \Carbon\Carbon::parse($startDate)->translatedFormat('d F Y') . ' - ' . \Carbon\Carbon::parse($endDate)->translatedFormat('d F Y');
@endphp

<div class="mb-[29px] space-y-4 rounded-2xl border border-[#E7E1DA] bg-white p-5 shadow-sm sm:p-6 md:min-h-[9.4375rem] md:p-7">
    <div class="flex flex-col gap-4 xl:flex-row xl:items-end xl:justify-between">
        <div>
            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-[#E76F51]">Overview</p>
            <h3 class="mt-2 text-2xl font-semibold text-gray-900">Ringkasan Aktivitas</h3>
            <p class="mt-1 text-sm text-gray-500">Periode: {{ $rangeLabel }}</p>
        </div>

        <div class="flex w-full flex-col gap-3 sm:flex-row sm:items-center xl:w-auto">
            <a
                href="{{ route('admin.reports.index', ['start_date' => $startDate, 'end_date' => $endDate]) }}"
                class="inline-flex items-center justify-center gap-2 rounded-xl border border-[#E76F51]/30 bg-[#FFF7F4] px-4 py-2.5 text-sm font-medium text-[#1A3B32] transition hover:bg-[#FFECE6]"
            >
                Lihat Laporan
            </a>

            <div class="flex items-center gap-2 rounded-xl border border-gray-200 bg-[#FAFAF9] px-2 py-2">
                <label for="start_date" class="text-[10px] font-semibold uppercase tracking-wide text-gray-500">Dari</label>
                <input
                    id="start_date"
                    type="date"
                    value="{{ $startDate }}"
                    onchange="window.location='{{ route('admin.dashboard') }}?start_date=' + this.value + '&end_date=' + document.getElementById('end_date').value"
                    class="rounded-lg border border-gray-200 bg-white px-2 py-1.5 text-sm text-gray-700 focus:border-[#E76F51] focus:outline-none focus:ring-2 focus:ring-[#E76F51]/20"
                >
            </div>

            <div class="flex items-center gap-2 rounded-xl border border-gray-200 bg-[#FAFAF9] px-2 py-2">
                <label for="end_date" class="text-[10px] font-semibold uppercase tracking-wide text-gray-500">Sampai</label>
                <input
                    id="end_date"
                    type="date"
                    value="{{ $endDate }}"
                    onchange="window.location='{{ route('admin.dashboard') }}?start_date=' + document.getElementById('start_date').value + '&end_date=' + this.value"
                    class="rounded-lg border border-gray-200 bg-white px-2 py-1.5 text-sm text-gray-700 focus:border-[#E76F51] focus:outline-none focus:ring-2 focus:ring-[#E76F51]/20"
                >
            </div>
        </div>
    </div>
</div>

@if($waitingCount > 0)
    <div class="mb-8 flex flex-col gap-3 rounded-xl border border-amber-200 bg-amber-50 px-4 py-4 text-amber-900 sm:flex-row sm:items-center sm:justify-between" role="status">
        <div class="flex items-start gap-3">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="mt-0.5 h-5 w-5 flex-shrink-0">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6l4 2m6-2a10 10 0 1 1-20 0 10 10 0 0 1 20 0Z" />
            </svg>
            <div>
                <p class="font-semibold">Ada {{ $waitingCount }} pesanan masuk</p>
                <p class="text-sm text-amber-800">Pesanan sedang menunggu pembayaran dan perlu diperiksa.</p>
            </div>
        </div>
        <a href="{{ route('admin.bookings.index', ['status' => 'menunggu_pembayaran']) }}" class="inline-flex items-center justify-center rounded-lg bg-amber-600 px-3 py-2 text-sm font-semibold text-white transition hover:bg-amber-700">
            Lihat pesanan
        </a>
    </div>
@endif

<div class="mb-[37px] grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-4">
    <div class="rounded-2xl border border-gray-200 bg-white px-5 py-5 shadow-sm md:px-6 md:py-[26px]">
        <p class="text-sm text-gray-500">Pendapatan Hari Ini</p>
        <h4 class="mt-4 text-3xl font-semibold text-gray-900">Rp {{ number_format($incomeToday, 0, ',', '.') }}</h4>
    </div>

    <div class="rounded-2xl border border-gray-200 bg-white px-5 py-5 shadow-sm md:px-6 md:py-[26px]">
        <p class="text-sm text-gray-500">Pendapatan Periode</p>
        <h4 class="mt-4 text-3xl font-semibold text-gray-900">Rp {{ number_format($incomeMonth, 0, ',', '.') }}</h4>
    </div>

    <div class="rounded-2xl border border-gray-200 bg-white px-5 py-5 shadow-sm md:px-6 md:py-[26px]">
        <p class="text-sm text-gray-500">Jumlah Booking</p>
        <h4 class="mt-4 text-3xl font-semibold text-gray-900">{{ $bookingsMonth }}</h4>
    </div>

    <div class="rounded-2xl border border-[#E76F51]/20 bg-[#FFF7F4] px-5 py-5 shadow-sm md:px-6 md:py-[26px]">
        <p class="text-sm text-gray-600">Status Terverifikasi</p>
        <h4 class="mt-4 text-3xl font-semibold text-[#1A3B32]">{{ $paidCount }}</h4>
    </div>
</div>

<div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
    <div class="flex items-center justify-between border-b border-gray-100 px-5 py-5 sm:px-6">
        <h3 class="text-lg font-semibold text-gray-900">Reservasi Terbaru</h3>
        <a href="{{ route('admin.bookings.index') }}" class="text-sm font-medium text-[#E76F51] hover:underline">Lihat semua</a>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm text-gray-600">
            <thead class="bg-gray-50 text-gray-500">
                <tr>
                    <th class="whitespace-nowrap border-b border-gray-100 px-4 py-5 sm:px-6">Pelanggan</th>
                    <th class="whitespace-nowrap border-b border-gray-100 px-4 py-5 sm:px-6">Layanan</th>
                    <th class="whitespace-nowrap border-b border-gray-100 px-4 py-5 sm:px-6">Jadwal</th>
                    <th class="whitespace-nowrap border-b border-gray-100 px-4 py-5 sm:px-6">Status</th>
                </tr>
            </thead>

            <tbody class="divide-y divide-gray-100">
                @forelse($recentBookings as $booking)
                    <tr class="transition hover:bg-gray-50/50">
                        <td class="px-4 py-5 sm:px-6">
                            <span class="block font-semibold text-gray-900">{{ $booking->user?->name ?? $booking->nama_pelanggan }}</span>
                            <span class="text-xs text-gray-500">#{{ str_pad($booking->id, 5, '0', STR_PAD_LEFT) }}</span>
                        </td>
                        <td class="px-4 py-5 sm:px-6">{{ $booking->services->pluck('nama_layanan')->implode(', ') ?: '-' }}</td>
                        <td class="whitespace-nowrap px-4 py-5 sm:px-6">
                            {{ $booking->slot ? \Carbon\Carbon::parse($booking->slot->tanggal)->format('d/m/Y') : \Carbon\Carbon::parse($booking->tanggal_kunjungan)->format('d/m/Y') }}
                            <br>
                            <span class="text-xs text-gray-500">
                                {{ $booking->slot ? \Carbon\Carbon::parse($booking->slot->jam_mulai)->format('H:i') . ' - ' . \Carbon\Carbon::parse($booking->slot->jam_selesai)->format('H:i') : \Carbon\Carbon::parse($booking->jam_kunjungan)->format('H:i') }}
                            </span>
                        </td>
                        <td class="px-4 py-5 sm:px-6">
                            @if($booking->status == 'menunggu_pembayaran')
                                <span class="inline-flex items-center rounded-full bg-yellow-100 px-2.5 py-1 text-xs font-medium text-yellow-800">Menunggu Pembayaran</span>
                            @elseif($booking->status == 'dibayar')
                                <span class="inline-flex items-center rounded-full bg-blue-100 px-2.5 py-1 text-xs font-medium text-blue-800">DP Terverifikasi</span>
                            @elseif($booking->status == 'selesai')
                                <span class="inline-flex items-center rounded-full bg-green-100 px-2.5 py-1 text-xs font-medium text-green-800">Selesai</span>
                            @elseif($booking->status == 'dibatalkan')
                                <span class="inline-flex items-center rounded-full bg-red-100 px-2.5 py-1 text-xs font-medium text-red-800">Dibatalkan</span>
                            @else
                                <span class="inline-flex items-center rounded-full bg-gray-100 px-2.5 py-1 text-xs font-medium text-gray-700">{{ ucfirst(str_replace('_', ' ', $booking->status)) }}</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-6 py-[41px] text-center text-gray-500">Belum ada data reservasi.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection

@extends('layouts.admin')

@section('header', 'Kelola Reservasi')

@section('content')

<div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">

    {{-- HEADER --}}
    <div class="p-6 border-b border-gray-100 bg-gray-50/50">

        <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">

            <div>
                <h3 class="text-lg font-medium text-gray-900">
                    Daftar Reservasi Pelanggan
                </h3>

                <p class="text-sm text-gray-500 mt-1">
                    Kelola reservasi pelanggan berdasarkan status.
                </p>
            </div>

            <a
                href="{{ route('admin.bookings.create') }}"
                class="inline-flex w-full items-center justify-center gap-2 rounded-lg bg-[#E76F51] px-4 py-2.5 text-sm font-medium text-white transition hover:bg-[#d85f42] md:w-auto"
            >
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-5 w-5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
                Tambah Walk-in
            </a>

        </div>

        {{-- FILTER STATUS --}}
        <div class="flex flex-wrap gap-2 mt-5">

            {{-- SEMUA --}}
            <a
                href="{{ route('admin.bookings.index') }}"
                class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium transition
                {{ !$status
                    ? 'bg-[#1A3B32] text-white'
                    : 'bg-white text-gray-600 border border-gray-200 hover:bg-gray-50'
                }}"
            >
                Semua
            </a>

            {{-- MENUNGGU PEMBAYARAN --}}
            <a
                href="{{ route('admin.bookings.index', ['status' => 'menunggu_pembayaran']) }}"
                class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium transition
                {{ $status === 'menunggu_pembayaran'
                    ? 'bg-yellow-600 text-white'
                    : 'bg-yellow-50 text-yellow-700 border border-yellow-200 hover:bg-yellow-100'
                }}"
            >
                Menunggu Pembayaran

                <span class="px-1.5 py-0.5 rounded-full text-[10px]
                    {{ $status === 'menunggu_pembayaran'
                        ? 'bg-white/20 text-white'
                        : 'bg-yellow-100 text-yellow-700'
                    }}">
                    {{ $waitingCount }}
                </span>
            </a>

            {{-- DP TERVERIFIKASI --}}
            <a
                href="{{ route('admin.bookings.index', ['status' => 'dibayar']) }}"
                class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium transition
                {{ $status === 'dibayar'
                    ? 'bg-blue-600 text-white'
                    : 'bg-blue-50 text-blue-700 border border-blue-200 hover:bg-blue-100'
                }}"
            >
                DP Terverifikasi

                <span class="px-1.5 py-0.5 rounded-full text-[10px]
                    {{ $status === 'dibayar'
                        ? 'bg-white/20 text-white'
                        : 'bg-blue-100 text-blue-700'
                    }}">
                    {{ $paidCount }}
                </span>
            </a>

            {{-- DIBATALKAN --}}
            <a
                href="{{ route('admin.bookings.index', ['status' => 'dibatalkan']) }}"
                class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium transition
                {{ $status === 'dibatalkan'
                    ? 'bg-red-600 text-white'
                    : 'bg-red-50 text-red-700 border border-red-200 hover:bg-red-100'
                }}"
            >
                Dibatalkan

                <span class="px-1.5 py-0.5 rounded-full text-[10px]
                    {{ $status === 'dibatalkan'
                        ? 'bg-white/20 text-white'
                        : 'bg-red-100 text-red-700'
                    }}">
                    {{ $cancelledCount }}
                </span>
            </a>

            {{-- SELESAI --}}
            <a
                href="{{ route('admin.bookings.index', ['status' => 'selesai']) }}"
                class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium transition
                {{ $status === 'selesai'
                    ? 'bg-green-600 text-white'
                    : 'bg-green-50 text-green-700 border border-green-200 hover:bg-green-100'
                }}"
            >
                Selesai

                <span class="px-1.5 py-0.5 rounded-full text-[10px]
                    {{ $status === 'selesai'
                        ? 'bg-white/20 text-white'
                        : 'bg-green-100 text-green-700'
                    }}">
                    {{ $completedCount }}
                </span>
            </a>

        </div>

    </div>


    {{-- TABLE --}}
    <div class="overflow-x-auto">

        <table class="w-full text-left text-sm text-gray-600">

            <thead class="bg-gray-50 text-gray-500 font-medium">

                <tr>

                    <th class="px-6 py-4 border-b border-gray-100">
                        ID / Pelanggan
                    </th>

                    <th class="px-6 py-4 border-b border-gray-100">
                        Layanan
                    </th>

                    <th class="px-6 py-4 border-b border-gray-100">
                        Jadwal Slot
                    </th>

                    <th class="px-6 py-4 border-b border-gray-100">
                        Status Pembayaran
                    </th>

                    <th class="px-6 py-4 border-b border-gray-100">
                        Status Booking
                    </th>

                    <th class="px-6 py-4 border-b border-gray-100 text-right">
                        Aksi
                    </th>

                </tr>

            </thead>


            <tbody class="divide-y divide-gray-100">

                @forelse($bookings as $booking)

                <tr class="hover:bg-gray-50/50 transition">

                    {{-- PELANGGAN --}}
                    <td class="px-6 py-4">

                        <span class="font-bold text-gray-900 block leading-tight">
                            {{ $booking->user?->name ?? $booking->nama_pelanggan }}
                        </span>

                        <span class="text-xs text-gray-400">
                            #{{ str_pad($booking->id, 5, '0', STR_PAD_LEFT) }}
                        </span>

                        @php
                            $waNum = $booking->no_whatsapp
                                ?? $booking->user?->no_whatsapp;
                        @endphp

                        @if($waNum)

                            <div class="mt-1">

                                <a
                                    href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $waNum) }}"
                                    target="_blank"
                                    class="inline-flex items-center gap-1.5
                                    text-[11px] text-emerald-700
                                    hover:text-emerald-900
                                    font-semibold
                                    bg-emerald-50
                                    hover:bg-emerald-100
                                    px-2 py-0.5
                                    rounded-lg
                                    border border-emerald-200/60
                                    transition"
                                >

                                    <i class="fa-brands fa-whatsapp text-emerald-600 text-xs"></i>

                                    <span>
                                        {{ $waNum }}
                                    </span>

                                </a>

                            </div>

                        @endif

                    </td>


                    {{-- LAYANAN --}}
                    <td class="px-6 py-4">

                        {{ $booking->services->pluck('nama_layanan')->implode(', ') ?: '-' }}

                    </td>


                    {{-- JADWAL --}}
                    <td class="px-6 py-4">

                        @if($booking->slot)

                            {{ \Carbon\Carbon::parse($booking->slot->tanggal)->format('d/m/Y') }}

                            <br>

                            <span class="text-xs text-gray-500">

                                {{ \Carbon\Carbon::parse($booking->slot->jam_mulai)->format('H:i') }}

                                -

                                {{ \Carbon\Carbon::parse($booking->slot->jam_selesai)->format('H:i') }}

                            </span>

                        @else

                            {{ \Carbon\Carbon::parse($booking->tanggal_kunjungan)->format('d/m/Y') }}

                            <br>

                            <span class="text-xs text-gray-500">

                                {{ \Carbon\Carbon::parse($booking->jam_kunjungan)->format('H:i') }}

                            </span>

                        @endif

                    </td>


                    {{-- STATUS PEMBAYARAN --}}
                    <td class="px-6 py-4">

                        @if(!$booking->payment)

                            <span class="text-gray-500 text-xs italic">
                                Belum ada
                            </span>

                        @else

                            @if($booking->payment->status_pembayaran == 'pending')

                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-medium bg-yellow-100 text-yellow-800">
                                    Menunggu Verifikasi
                                </span>

                            @elseif($booking->payment->status_pembayaran == 'terverifikasi')

                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-medium bg-green-100 text-green-800">
                                    DP Terverifikasi
                                </span>

                            @elseif($booking->payment->status_pembayaran == 'ditolak')

                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-medium bg-red-100 text-red-800">
                                    Ditolak
                                </span>

                            @else

                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-medium bg-gray-100 text-gray-700">
                                    {{ ucfirst($booking->payment->status_pembayaran) }}
                                </span>

                            @endif

                        @endif

                    </td>


                    {{-- STATUS BOOKING --}}
                    <td class="px-6 py-4">

                        @if($booking->status == 'menunggu_pembayaran')

                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                Menunggu Pembayaran
                            </span>

                        @elseif($booking->status == 'dibayar')

                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                DP Terverifikasi
                            </span>

                        @elseif($booking->status == 'dibatalkan')

                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                Dibatalkan
                            </span>

                        @elseif($booking->status == 'selesai')

                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                Selesai
                            </span>

                        @endif

                    </td>


                    {{-- AKSI --}}
                    <td class="px-6 py-4 text-right">

                        <a
                            href="{{ route('admin.bookings.show', $booking->id) }}"
                            class="inline-block p-2
                            text-gray-400
                            hover:text-blue-600
                            hover:bg-blue-50
                            rounded-lg
                            transition"
                            title="Lihat Detail"
                        >

                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke-width="1.5"
                                stroke="currentColor"
                                class="w-4 h-4"
                            >

                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z"
                                />

                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"
                                />

                            </svg>

                        </a>

                    </td>

                </tr>

                @empty

                <tr>

                    <td
                        colspan="6"
                        class="px-6 py-10 text-center text-gray-500"
                    >

                        <div class="flex flex-col items-center">

                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke-width="1.5"
                                stroke="currentColor"
                                class="w-10 h-10 text-gray-300 mb-3"
                            >

                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    d="M20.25 7.5l-.625 10.125a2.25 2.25 0 0 1-2.242 2.112H6.617a2.25 2.25 0 0 1-2.242-2.112L3.75 7.5M9 11.25v4.5m6-4.5v4.5M4.5 7.5h15"
                                />

                            </svg>

                            <p>
                                Tidak ada reservasi dengan status ini.
                            </p>

                        </div>

                    </td>

                </tr>

                @endforelse

            </tbody>

        </table>

    </div>


    {{-- PAGINATION --}}
    @if($bookings->hasPages())

        <div class="p-6 border-t border-gray-100">

            {{ $bookings->links() }}

        </div>

    @endif

</div>

@endsection

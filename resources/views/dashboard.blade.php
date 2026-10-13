@extends('layouts.main')

@section('title', 'Dashboard')

@section('content')
<div class="max-w-7xl mx-auto px-6 py-12">
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-10">
        <div>
            <h1 class="text-3xl font-medium playfair text-gray-900 mb-2">Halo, {{ auth()->user()->name }}</h1>
            <p class="text-gray-500">Selamat datang di dashboard Anda. Berikut adalah ringkasan reservasi Anda.</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('profile.edit') }}" class="bg-white text-gray-700 border border-gray-300 px-4 py-2.5 rounded-lg text-sm font-medium hover:bg-gray-50 transition flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="w-4 h-4 text-[#E76F51]"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" /></svg>
                Ubah Password & Profil
            </a>
            <a href="{{ route('booking.create') }}" class="bg-[#1A3B32] text-white px-5 py-2.5 rounded-lg text-sm font-medium hover:bg-opacity-90 transition shadow-lg shadow-[#1A3B32]/20 flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
                Buat Reservasi Baru
            </a>
        </div>
    </div>

    <!-- Riwayat Reservasi -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-6 border-b border-gray-100 bg-gray-50/50">
            <h3 class="text-lg font-medium text-gray-900">Riwayat Reservasi</h3>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-gray-600">
                <thead class="bg-gray-50/50 text-gray-500 font-medium">
                    <tr>
                        <th class="px-6 py-4 border-b border-gray-100">ID</th>
                        <th class="px-6 py-4 border-b border-gray-100">Layanan</th>
                        <th class="px-6 py-4 border-b border-gray-100">Jadwal</th>
                        <th class="px-6 py-4 border-b border-gray-100">Status</th>
                        <th class="px-6 py-4 border-b border-gray-100 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($bookings as $booking)
                    <tr class="hover:bg-gray-50/50 transition">
                        <td class="px-6 py-4 font-medium text-gray-900">#{{ str_pad($booking->id, 5, '0', STR_PAD_LEFT) }}</td>
                        <td class="px-6 py-4">
                            @php
                                $serviceNames = $booking->services->pluck('nama_layanan')->implode(', ') ?: 'Layanan';
                                $durationMinutes = (int) ($booking->durasi_total_menit ?: $booking->services->sum('pivot.durasi_menit'));
                                $bookingStart = \Carbon\Carbon::parse($booking->tanggal_kunjungan . ' ' . $booking->jam_kunjungan);
                            @endphp
                            <span class="block font-medium text-gray-900">{{ $serviceNames }}</span>
                            <span class="text-xs text-gray-500">Rp {{ number_format($booking->total_harga, 0, ',', '.') }}</span>
                        </td>
                        <td class="px-6 py-4">
                            {{ $bookingStart->format('d/m/Y') }}<br>
                            <span class="text-xs text-gray-500">{{ $bookingStart->format('H:i') }} - {{ $bookingStart->copy()->addMinutes($durationMinutes)->format('H:i') }}</span>
                        </td>
                        <td class="px-6 py-4">
                            @if($booking->status == 'menunggu_pembayaran')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">Menunggu Pembayaran</span>
                            @elseif($booking->status == 'dibayar')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">DP Terverifikasi</span>
                            @elseif($booking->status == 'selesai')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Selesai</span>
                            @elseif($booking->status == 'dibatalkan')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">Dibatalkan</span>
                            @elseif($booking->status == 'tidak_hadir')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-700">Tidak Hadir</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-right">
                            <a href="{{ route('booking.show', $booking->id) }}" class="inline-block px-3 py-1.5 text-xs font-medium text-[#1A3B32] bg-[#1A3B32]/10 hover:bg-[#1A3B32]/20 rounded-lg transition">Detail</a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-8 text-center">
                            <div class="flex flex-col items-center justify-center">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor" class="w-12 h-12 text-gray-300 mb-3"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5m-9-6h.008v.008H12v-.008ZM12 15h.008v.008H12V15Zm0 2.25h.008v.008H12v-.008ZM9.75 15h.008v.008H9.75V15Zm0 2.25h.008v.008H9.75v-.008ZM7.5 15h.008v.008H7.5V15Zm0 2.25h.008v.008H7.5v-.008Zm6.75-4.5h.008v.008h-.008v-.008Zm0 2.25h.008v.008h-.008V15Zm0 2.25h.008v.008h-.008v-.008Zm2.25-4.5h.008v.008H16.5v-.008Zm0 2.25h.008v.008H16.5V15Z" /></svg>
                                <p class="text-gray-500 mb-4">Anda belum memiliki riwayat reservasi.</p>
                                <a href="{{ route('booking.create') }}" class="text-[#E76F51] text-sm font-medium hover:underline">Mulai Buat Reservasi</a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

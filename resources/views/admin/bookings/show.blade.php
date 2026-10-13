@extends('layouts.admin')

@section('header', 'Detail Reservasi')

@section('content')
<div class="max-w-4xl">
    <div class="mb-6">
        <a href="{{ route('admin.bookings.index') }}" class="text-sm font-medium text-gray-500 hover:text-gray-900 flex items-center gap-2 transition inline-flex">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5" /></svg>
            Kembali ke Daftar Reservasi
        </a>
    </div>



    <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden mb-6">
        <div class="p-6 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
            <h3 class="text-lg font-medium text-gray-900">Informasi Pemesanan #{{ str_pad($booking->id, 5, '0', STR_PAD_LEFT) }}</h3>
            @if($booking->status == 'menunggu_pembayaran')
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800 border border-yellow-200">Menunggu Pembayaran</span>
            @elseif($booking->status == 'dibayar')
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800 border border-blue-200">DP Terverifikasi</span>
            @elseif($booking->status == 'selesai')
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800 border border-green-200">Selesai</span>
            @elseif($booking->status == 'dibatalkan')
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800 border border-red-200">Dibatalkan</span>
            @elseif($booking->status == 'tidak_hadir')
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-gray-100 text-gray-700 border border-gray-200">Tidak Hadir</span>
            @endif
        </div>
        
        <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <p class="text-xs text-gray-500 uppercase tracking-wider font-semibold mb-1">Data Pelanggan</p>
                <p class="font-bold text-gray-900">{{ $booking->user?->name ?? $booking->nama_pelanggan }}</p>
                <p class="text-sm text-gray-500">{{ $booking->user?->email ?? '-' }}</p>
                @php
                    $waNumber = $booking->no_whatsapp ?? $booking->user?->no_whatsapp;
                @endphp
                @if($waNumber)
                    <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $waNumber) }}" target="_blank" class="inline-flex items-center gap-2 text-xs text-emerald-700 hover:text-emerald-900 font-semibold bg-emerald-50 hover:bg-emerald-100 px-3 py-1.5 rounded-xl border border-emerald-200/60 mt-1.5 transition">
                        <i class="fa-brands fa-whatsapp text-emerald-600 text-base"></i>
                        <span>{{ $waNumber }} (Chat WhatsApp)</span>
                    </a>
                @else
                    <p class="text-sm text-gray-400 italic">Nomor WhatsApp tidak tersedia</p>
                @endif
            </div>
            <div>
                <p class="text-xs text-gray-500 uppercase tracking-wider font-semibold mb-1">Data Layanan</p>
                <p class="font-medium text-gray-900">{{ $booking->services->pluck('nama_layanan')->implode(', ') ?: '-' }}</p>
                <p class="text-sm text-gray-600">Total Biaya Transaksi: Rp {{ number_format($transactionTotal, 0, ',', '.') }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-500 uppercase tracking-wider font-semibold mb-1">Stylist / Kapster</p>
                <p class="font-medium text-gray-900">{{ $booking->stylist?->nama ?? $booking->slot?->kapster ?? '-' }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-500 uppercase tracking-wider font-semibold mb-1">Jadwal Reservasi</p>
                <p class="font-medium text-gray-900">{{ \Carbon\Carbon::parse($booking->tanggal_kunjungan)->translatedFormat('l, d F Y') }}</p>
                    <p class="text-sm text-gray-600">{{ $booking->jam_kunjungan ? 'Pukul ' . \Carbon\Carbon::parse($booking->jam_kunjungan)->format('H:i') . ' - ' . $bookingEnd->format('H:i') : 'Jam belum ditentukan' }}</p>
            </div>
            @if($booking->reservedSlots->isNotEmpty())
                <div>
                    <p class="text-xs text-gray-500 uppercase tracking-wider font-semibold mb-2">Slot yang ditahan</p>
                    <ul class="space-y-1 text-sm text-gray-700">
                        @foreach($booking->reservedSlots as $reservedSlot)
                            <li>{{ \Carbon\Carbon::parse($reservedSlot->jam_mulai)->format('H:i') }} - {{ \Carbon\Carbon::parse($reservedSlot->jam_selesai)->format('H:i') }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            @if($transactionBookings->count() > 1)
                <div class="md:col-span-2">
                    <p class="text-xs text-gray-500 uppercase tracking-wider font-semibold mb-2">Semua Jam dalam Transaksi</p>
                    <ul class="grid gap-2 sm:grid-cols-2">
                        @foreach($transactionBookings as $transactionBooking)
                            <li class="rounded-lg border border-gray-100 bg-gray-50 px-3 py-2 text-sm text-gray-700">
                                {{ \Carbon\Carbon::parse($transactionBooking->tanggal_kunjungan)->translatedFormat('d F Y') }}
                                &middot; {{ $transactionBooking->jam_kunjungan ? \Carbon\Carbon::parse($transactionBooking->jam_kunjungan)->format('H:i') : 'Jam menyusul' }}
                                &middot; {{ $transactionBooking->slot?->kapster ?? '-' }}
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <div>
                <p class="text-xs text-gray-500 uppercase tracking-wider font-semibold mb-1">Catatan Tambahan</p>
                <p class="text-sm text-gray-700 bg-gray-50 p-3 rounded-lg border border-gray-100">{{ $booking->catatan ?: 'Tidak ada catatan.' }}</p>
            </div>
        </div>
    </div>

    <!-- Modul Pembayaran & Aksi -->
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="p-6 border-b border-gray-100 bg-gray-50/50">
            <h3 class="text-lg font-medium text-gray-900">Verifikasi & Aksi</h3>
        </div>
        
        <div class="p-6 flex flex-col md:flex-row gap-8">
            <div class="flex-1">
                <h4 class="text-sm font-medium text-gray-700 mb-4">
                    {{ $booking->payment?->metode === 'tunai' ? 'Pembayaran di tempat' : 'Bukti Pembayaran Transaksi (Uang Muka 20%)' }}
                </h4>
                @if(!$booking->payment)
                    <p class="text-sm text-gray-500 italic">Data pembayaran belum dibuat.</p>
                @else
                    <div class="mb-4">
                        <p class="text-sm text-gray-600 mb-1">
                            {{ $booking->payment->metode === 'tunai' ? 'Jumlah pembayaran di tempat' : 'Total Uang Muka' }}:
                            <span class="font-medium text-gray-900">Rp {{ number_format($booking->payment->metode === 'tunai' ? $transactionTotal : $transactionDpTotal, 0, ',', '.') }}</span>
                        </p>
                        @if($booking->payment->metode === 'tunai')
                            <p class="text-sm text-gray-600 mb-3">Metode: <span class="font-medium text-gray-900">Bayar di tempat</span></p>
                        @endif
                        <p class="text-sm text-gray-600 mb-3">Status: 
                            @if($booking->payment->status_pembayaran == 'pending') <span class="text-yellow-600 font-medium">Menunggu Verifikasi</span>
                            @elseif($booking->payment->status_pembayaran == 'terverifikasi') <span class="text-green-600 font-medium">Terverifikasi</span>
                            @else <span class="text-red-600 font-medium">Gagal/Ditolak</span> @endif
                        </p>
                    </div>

                    @if($booking->bukti_transfer)
                        <a href="{{ route('admin.bookings.payment-proof', $booking) }}" target="_blank" class="block max-w-xs border border-gray-200 rounded-lg overflow-hidden hover:border-[#E76F51] transition">
                            <img src="{{ route('admin.bookings.payment-proof', $booking) }}" alt="Bukti Pembayaran" class="w-full h-auto object-cover">
                        </a>
                        <p class="text-xs text-gray-400 mt-2">Klik gambar untuk memperbesar.</p>
                    @elseif($booking->payment->metode === 'tunai')
                        <div class="p-4 bg-amber-50 rounded-lg border border-amber-200 text-center">
                            <p class="text-sm text-amber-800">Pelanggan akan melakukan pembayaran langsung saat datang ke salon.</p>
                        </div>
                    @else
                        <div class="p-4 bg-gray-50 rounded-lg border border-gray-100 text-center">
                            <p class="text-sm text-gray-500">Pelanggan belum mengunggah bukti pembayaran.</p>
                        </div>
                    @endif
                @endif
            </div>

            <div class="flex-1 border-t md:border-t-0 md:border-l border-gray-100 md:pl-8 pt-6 md:pt-0">
                <h4 class="text-sm font-medium text-gray-700 mb-4">Tindakan Admin</h4>
                <div class="space-y-3">
                    @if($booking->status == 'menunggu_pembayaran' && $booking->payment && ($booking->payment->metode === 'tunai' || $booking->bukti_transfer))
                        <form action="{{ route('admin.bookings.approve', $booking->id) }}" method="POST" data-confirm="Apakah Anda yakin ingin {{ $booking->payment->metode === 'tunai' ? 'mengonfirmasi pembayaran tunai dan menyetujui' : 'memverifikasi DP dan menyetujui' }} reservasi ini?">
                            @csrf
                            <button type="submit" class="w-full bg-green-600 text-white px-4 py-2.5 rounded-lg text-sm font-medium hover:bg-green-700 transition flex items-center justify-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" /></svg>
                                {{ $booking->payment->metode === 'tunai' ? 'Konfirmasi Tunai & Setujui' : 'Verifikasi & Setujui DP' }}
                            </button>
                        </form>
                    @endif
                    
                    @if(in_array($booking->status, ['menunggu_pembayaran', 'dibayar']))
                        <form action="{{ route('admin.bookings.reject', $booking->id) }}" method="POST" data-confirm="Apakah Anda yakin ingin membatalkan/menolak reservasi ini? Kuota slot akan dikembalikan secara otomatis.">
                            @csrf
                            <button type="submit" class="w-full border border-red-200 bg-red-50 text-red-600 px-4 py-2.5 rounded-lg text-sm font-medium hover:bg-red-100 transition flex items-center justify-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                                Batalkan Reservasi
                            </button>
                        </form>
                    @endif

                    @if($booking->status == 'dibayar')
                        <form action="{{ route('admin.bookings.complete', $booking->id) }}" method="POST" data-confirm="Apakah Anda yakin ingin menandai reservasi ini telah selesai dilayani?">
                            @csrf
                            <button type="submit" class="w-full bg-[#1A3B32] text-white px-4 py-2.5 rounded-lg text-sm font-medium hover:bg-opacity-90 transition flex items-center justify-center gap-2 mt-6">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /></svg>
                                Tandai Selesai Dilayani
                            </button>
                        </form>
                    @endif
                    
                    @if($booking->status == 'selesai' || $booking->status == 'dibatalkan')
                        <div class="p-4 bg-gray-50 rounded-lg border border-gray-100 text-center">
                            <p class="text-sm text-gray-500">Reservasi ini sudah berstatus akhir ({{ $booking->status }}). Tidak ada aksi lanjutan.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@extends('layouts.main')

@section('title', 'Detail Reservasi')

@section('content')
<div class="max-w-3xl mx-auto px-6 py-12 md:py-20">
    
    <div class="mb-8">
        <a href="{{ route('dashboard') }}" class="text-sm font-medium text-gray-500 hover:text-[#E76F51] flex items-center gap-2 transition inline-flex">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5" /></svg>
            Kembali ke Dashboard
        </a>
    </div>

    @if($errors->any())
    <div class="bg-red-50 text-red-600 p-4 rounded-xl mb-8 border border-red-100">
        <ul class="list-disc list-inside text-sm font-medium">
            @foreach($errors->all() as $err)
                <li>{{ $err }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <div class="bg-white rounded-2xl overflow-hidden shadow-sm border border-gray-100">
        <div class="p-6 md:p-8 border-b border-gray-100 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-gray-50/50">
            <div>
                <h1 class="text-2xl font-medium playfair text-gray-900 mb-1">ID Reservasi: #{{ str_pad($booking->id, 5, '0', STR_PAD_LEFT) }}</h1>
                <p class="text-sm text-gray-500">Dibuat pada {{ $booking->created_at->translatedFormat('d F Y, H:i') }}</p>
            </div>
            <div>
                @if($booking->status == 'menunggu_pembayaran')
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800 border border-yellow-200">Menunggu Pembayaran</span>
                @elseif($booking->status == 'dibayar')
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800 border border-blue-200">DP Terverifikasi</span>
                @elseif($booking->status == 'selesai')
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800 border border-green-200">Selesai</span>
                @elseif($booking->status == 'dibatalkan')
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800 border border-red-200">Dibatalkan</span>
                @endif
            </div>
        </div>

        <div class="p-6 md:p-8 grid grid-cols-1 md:grid-cols-2 gap-8">
            <div class="space-y-6">
                <div>
                    <h4 class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Layanan</h4>
                    @php
                        $serviceNames = $booking->services->pluck('nama_layanan')->implode(', ') ?: '-';
                    @endphp
                    <p class="text-gray-900 font-medium">{{ $serviceNames }}</p>
                </div>
                <div>
                    <h4 class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Jadwal Reservasi</h4>
                    <p class="text-gray-900 font-medium">{{ \Carbon\Carbon::parse($booking->tanggal_kunjungan)->translatedFormat('l, d F Y') }}</p>
                    <p class="text-gray-500 text-sm mt-1">
                        {{ $booking->jam_kunjungan ? 'Pukul ' . \Carbon\Carbon::parse($booking->jam_kunjungan)->format('H:i') . ' - ' . $bookingEnd->format('H:i') : 'Jam akan ditentukan oleh admin' }}
                    </p>
                </div>
                <div>
                    <h4 class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Stylist / Kapster</h4>
                    <p class="text-gray-900 font-medium">{{ $booking->stylist?->nama ?? $booking->slot?->kapster ?? '-' }}</p>
                </div>
                @if($booking->reservedSlots->isNotEmpty())
                    <div>
                        <h4 class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Slot yang ditahan</h4>
                        <ul class="space-y-1 text-sm text-gray-700">
                            @foreach($booking->reservedSlots as $reservedSlot)
                                <li>{{ \Carbon\Carbon::parse($reservedSlot->jam_mulai)->format('H:i') }} - {{ \Carbon\Carbon::parse($reservedSlot->jam_selesai)->format('H:i') }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                @if($transactionBookings->count() > 1)
                    <div>
                        <h4 class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Jam dalam transaksi ini</h4>
                        <ul class="space-y-2 text-sm text-gray-700">
                            @foreach($transactionBookings as $transactionBooking)
                                <li>
                                    {{ \Carbon\Carbon::parse($transactionBooking->tanggal_kunjungan)->translatedFormat('d F Y') }}
                                    &middot; {{ $transactionBooking->jam_kunjungan ? \Carbon\Carbon::parse($transactionBooking->jam_kunjungan)->format('H:i') : 'Jam menyusul' }}
                                    &middot; {{ $transactionBooking->slot?->kapster ?? '-' }}
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            </div>
            
            <div class="bg-gray-50 p-6 rounded-xl border border-gray-100">
                <h4 class="font-medium text-gray-900 mb-4">Rincian Pembayaran</h4>
                <div class="space-y-3 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-500">Total Biaya Transaksi</span>
                        <span class="font-medium">Rp {{ number_format($transactionTotal, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between pb-3 border-b border-gray-200">
                        <span class="text-gray-500">{{ $booking->payment?->metode === 'tunai' ? 'Total Pembayaran' : 'Total DP (20%)' }}</span>
                        <span class="font-medium">Rp {{ number_format($booking->payment?->metode === 'tunai' ? $transactionTotal : $transactionDpTotal, 0, ',', '.') }}</span>
                    </div>
                    
                    @if($booking->payment)
                        <div class="pt-3">
                            <span class="text-gray-500 block mb-2">Metode: {{ $booking->payment->metode === 'tunai' ? 'Bayar di tempat' : 'Transfer bank' }}</span>
                            <span class="text-gray-500 block mb-2">Status Pembayaran:</span>
                            @if($booking->payment->status_pembayaran == 'pending')
                                <span class="text-yellow-600 font-medium flex items-center gap-1">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /></svg>
                                    {{ $booking->payment->metode === 'tunai' ? 'Menunggu Konfirmasi Pembayaran' : 'Menunggu Verifikasi Admin' }}
                                </span>
                            @elseif($booking->payment->status_pembayaran == 'terverifikasi')
                                <span class="text-green-600 font-medium flex items-center gap-1">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /></svg>
                                    Berhasil Diverifikasi
                                </span>
                            @else
                                <span class="text-red-600 font-medium flex items-center gap-1">
                                    Ditolak
                                </span>
                            @endif
                            @if($booking->bukti_transfer)
                                <a href="{{ route('booking.payment-proof', $booking) }}" target="_blank" class="mt-3 inline-block text-sm text-blue-600 hover:underline">Lihat Bukti Terunggah</a>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>

        @if($booking->status == 'menunggu_pembayaran' && $booking->payment?->metode === 'tunai' && $booking->payment->status_pembayaran === 'pending')
            <div class="p-6 md:p-8 border-t border-amber-200 bg-amber-50 text-amber-950">
                <h3 class="text-lg font-medium playfair mb-2">Bayar di tempat</h3>
                <p class="text-sm mb-4">Siapkan pembayaran sebesar <strong>Rp {{ number_format($transactionTotal, 0, ',', '.') }}</strong> saat datang ke salon. Slot belum dikunci dan bisa terisi oleh pelanggan lain sebelum admin mengonfirmasi pembayaran Anda.</p>
                <form action="{{ route('booking.cancel', $booking->id) }}" method="POST" data-confirm="Yakin ingin membatalkan reservasi ini? Pembatalan tidak dapat refund. Anda akan menerima notifikasi WhatsApp setelah pembatalan diproses.">
                    @csrf
                    <button type="submit" class="text-sm font-medium text-amber-900 underline underline-offset-2">Batalkan permintaan</button>
                </form>
            </div>
        @elseif($booking->status == 'menunggu_pembayaran' && $booking->payment?->metode !== 'tunai' && (!$booking->payment || !$booking->bukti_transfer || $booking->payment->status_pembayaran == 'ditolak'))
            <div class="p-6 md:p-8 bg-[#1A3B32] text-white">
                <h3 class="text-lg font-medium playfair mb-2">Pembayaran DP</h3>
                <p class="text-sm text-white/80 mb-6">Silakan transfer total DP sebesar <strong>Rp {{ number_format($transactionDpTotal, 0, ',', '.') }}</strong> untuk seluruh jam dalam transaksi ini, lalu unggah satu bukti pembayarannya.</p>
                
                <div class="bg-white/10 border border-white/20 p-4 rounded-xl mb-6">
                    <p class="text-sm text-white/60 mb-1">Bank BCA</p>
                    <p class="font-mono text-xl tracking-wider mb-1">123 4567 890</p>
                    <p class="text-sm font-medium">a.n. Alan Salon Batam</p>
                </div>

                <form action="{{ route('booking.payment', $booking->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-white/90 mb-2">Unggah Bukti Transfer</label>
                        <input type="file" name="bukti_pembayaran" required accept="image/*" class="w-full text-sm text-white/80 file:mr-4 file:py-2.5 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-white file:text-[#1A3B32] hover:file:bg-gray-100 transition">
                        <p class="text-xs text-white/50 mt-2">Format: JPG, PNG. Maksimal: 2MB.</p>
                    </div>
                    <div class="flex items-center gap-4 mt-6">
                        <button type="submit" class="bg-[#E76F51] text-white px-6 py-2.5 rounded-lg text-sm font-medium hover:bg-opacity-90 transition">
                            Unggah Bukti untuk Semua Jam
                        </button>
                    </div>
                </form>
                <form action="{{ route('booking.cancel', $booking->id) }}" method="POST" class="mt-4" data-confirm="Yakin ingin membatalkan semua reservasi dalam transaksi ini? Pembatalan tidak dapat refund. Anda akan menerima notifikasi WhatsApp setelah pembatalan diproses.">
                    @csrf
                    <button type="submit" class="text-white/60 hover:text-white text-sm transition flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" /></svg>
                        Batalkan Reservasi
                    </button>
                </form>
            </div>
        @elseif($booking->status == 'dibayar')
            <div class="p-6 md:p-8 border-t border-red-200 bg-red-50 text-red-950">
                <h3 class="text-lg font-medium playfair mb-2">Batalkan Reservasi</h3>
                <p class="text-sm mb-4">Pembatalan tidak dapat refund. Setelah dibatalkan, Anda akan menerima notifikasi melalui WhatsApp.</p>
                <form action="{{ route('booking.cancel', $booking->id) }}" method="POST" data-confirm="Yakin ingin membatalkan reservasi ini? Pembatalan tidak dapat refund. Anda akan menerima notifikasi WhatsApp setelah pembatalan diproses.">
                    @csrf
                    <button type="submit" class="text-sm font-medium text-red-700 underline underline-offset-2">Batalkan Reservasi</button>
                </form>
            </div>
        @elseif($booking->status == 'selesai')
            <div class="p-6 md:p-8 border-t border-gray-100">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Ulasan Layanan</h3>
                
                @if(!$booking->review)
                    <form action="{{ route('booking.review', $booking->id) }}" method="POST" class="bg-gray-50 p-6 rounded-xl border border-gray-100">
                        @csrf
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Beri Nilai (1-5 Bintang)</label>
                            <div class="flex flex-row-reverse justify-end gap-1 rating-stars">
                                <input type="radio" id="star5" name="rating" value="5" class="peer hidden" required />
                                <label for="star5" class="cursor-pointer text-gray-300 peer-checked:text-yellow-400 hover:text-yellow-400 text-3xl">★</label>
                                
                                <input type="radio" id="star4" name="rating" value="4" class="peer hidden" />
                                <label for="star4" class="cursor-pointer text-gray-300 peer-checked:text-yellow-400 hover:text-yellow-400 text-3xl">★</label>
                                
                                <input type="radio" id="star3" name="rating" value="3" class="peer hidden" />
                                <label for="star3" class="cursor-pointer text-gray-300 peer-checked:text-yellow-400 hover:text-yellow-400 text-3xl">★</label>
                                
                                <input type="radio" id="star2" name="rating" value="2" class="peer hidden" />
                                <label for="star2" class="cursor-pointer text-gray-300 peer-checked:text-yellow-400 hover:text-yellow-400 text-3xl">★</label>
                                
                                <input type="radio" id="star1" name="rating" value="1" class="peer hidden" />
                                <label for="star1" class="cursor-pointer text-gray-300 peer-checked:text-yellow-400 hover:text-yellow-400 text-3xl">★</label>
                            </div>
                            <style>
                                .rating-stars label:hover, .rating-stars label:hover ~ label, .rating-stars input:checked ~ label {
                                    color: #fbbf24 !important;
                                }
                            </style>
                        </div>
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Komentar</label>
                            <textarea name="komentar" rows="3" required class="w-full border-gray-300 rounded-lg shadow-sm focus:border-[#E76F51] focus:ring-[#E76F51] resize-none" placeholder="Bagaimana pengalaman Anda dengan layanan ini?"></textarea>
                        </div>
                        <button type="submit" class="bg-[#1A3B32] text-white px-6 py-2.5 rounded-lg text-sm font-medium hover:bg-opacity-90 transition">
                            Kirim Ulasan
                        </button>
                    </form>
                @else
                    <div class="bg-gray-50 p-6 rounded-xl border border-gray-100 flex gap-4">
                        <div class="flex-shrink-0">
                            <img src="https://ui-avatars.com/api/?name={{ urlencode($booking->user->name) }}&background=EAECE4" class="w-12 h-12 rounded-full">
                        </div>
                        <div>
                            <div class="flex items-center gap-2 mb-1">
                                <h4 class="font-medium text-gray-900">{{ $booking->user->name }}</h4>
                                <span class="text-xs text-gray-500">{{ $booking->review->created_at->diffForHumans() }}</span>
                            </div>
                            <div class="flex text-yellow-400 text-sm mb-2">
                                @for($i = 1; $i <= 5; $i++)
                                    @if($i <= $booking->review->rating) ★ @else <span class="text-gray-300">★</span> @endif
                                @endfor
                            </div>
                            <p class="text-sm text-gray-700">{{ $booking->review->komentar }}</p>
                        </div>
                    </div>
                @endif
            </div>
        @endif
        
    </div>
</div>
@endsection

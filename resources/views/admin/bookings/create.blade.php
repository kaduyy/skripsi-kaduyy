@extends('layouts.admin')

@section('header', 'Tambah Reservasi Walk-in')

@section('content')
<div class="mx-auto max-w-3xl">
    <div class="mb-6 flex items-center gap-3">
        <a href="{{ route('admin.bookings.index') }}" class="inline-flex h-10 w-10 items-center justify-center rounded-xl text-gray-500 transition hover:bg-white hover:text-[#E76F51]" aria-label="Kembali ke reservasi">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-5 w-5">
                <path stroke-linecap="round" stroke-linejoin="round" d="m15.75 19.5-7.5-7.5 7.5-7.5" />
            </svg>
        </a>
        <div>
            <h3 class="text-xl font-semibold text-gray-900">Pelanggan Datang Langsung</h3>
            <p class="text-sm text-gray-500">Catat pelanggan walk-in tanpa perlu membuat akun.</p>
        </div>
    </div>

    @if($errors->any())
        <div class="mb-6 rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-700">
            <ul class="list-inside list-disc">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.bookings.store') }}" class="space-y-6 rounded-2xl border border-gray-100 bg-white p-5 shadow-sm sm:p-8">
        @csrf

        <div class="grid gap-5 sm:grid-cols-2">
            <div>
                <label for="nama_pelanggan" class="mb-2 block text-sm font-medium text-gray-700">Nama pelanggan</label>
                <input id="nama_pelanggan" name="nama_pelanggan" value="{{ old('nama_pelanggan') }}" required class="w-full rounded-xl border-gray-300 focus:border-[#E76F51] focus:ring-[#E76F51]" placeholder="Nama pelanggan">
            </div>
            <div>
                <label for="no_whatsapp" class="mb-2 block text-sm font-medium text-gray-700">Nomor WhatsApp</label>
                <input id="no_whatsapp" name="no_whatsapp" value="{{ old('no_whatsapp') }}" required class="w-full rounded-xl border-gray-300 focus:border-[#E76F51] focus:ring-[#E76F51]" placeholder="08xxxxxxxxxx">
            </div>
        </div>

        <div>
            <label for="service_id" class="mb-2 block text-sm font-medium text-gray-700">Layanan</label>
            <select id="service_id" name="service_id" required class="w-full rounded-xl border-gray-300 focus:border-[#E76F51] focus:ring-[#E76F51]">
                <option value="">Pilih layanan</option>
                @foreach($services as $service)
                    <option value="{{ $service->id }}" @selected(old('service_id') == $service->id)>{{ $service->nama_layanan }} - Rp {{ number_format($service->harga, 0, ',', '.') }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label for="slot_id" class="mb-2 block text-sm font-medium text-gray-700">Jadwal</label>
            <select id="slot_id" name="slot_id" required class="w-full rounded-xl border-gray-300 focus:border-[#E76F51] focus:ring-[#E76F51]">
                <option value="">Pilih slot tersedia</option>
                @foreach($slots as $slot)
                    <option value="{{ $slot->id }}" @selected(old('slot_id') == $slot->id)>
                        {{ \Carbon\Carbon::parse($slot->tanggal)->translatedFormat('d F Y') }} | {{ \Carbon\Carbon::parse($slot->jam_mulai)->format('H:i') }} - {{ \Carbon\Carbon::parse($slot->jam_selesai)->format('H:i') }} | {{ $slot->stylist?->nama ?? $slot->kapster }}
                    </option>
                @endforeach
            </select>
            @if($slots->isEmpty())
                <p class="mt-2 text-sm text-amber-600">Belum ada slot tersedia. Buat slot terlebih dahulu di menu Kelola Jadwal Booking.</p>
            @endif
        </div>

        <div>
            <label for="catatan_admin" class="mb-2 block text-sm font-medium text-gray-700">Catatan admin <span class="font-normal text-gray-400">(opsional)</span></label>
            <textarea id="catatan_admin" name="catatan_admin" rows="3" class="w-full resize-none rounded-xl border-gray-300 focus:border-[#E76F51] focus:ring-[#E76F51]" placeholder="Contoh: pembayaran tunai, permintaan khusus...">{{ old('catatan_admin') }}</textarea>
        </div>

        <div class="flex flex-col-reverse gap-3 border-t border-gray-100 pt-6 sm:flex-row sm:justify-end">
            <a href="{{ route('admin.bookings.index') }}" class="inline-flex items-center justify-center rounded-xl px-5 py-3 text-sm font-medium text-gray-600 transition hover:bg-gray-100">Batal</a>
            <button type="submit" class="inline-flex items-center justify-center gap-2 rounded-xl bg-[#1A3B32] px-5 py-3 text-sm font-medium text-white transition hover:bg-[#244d41]">
                Simpan Walk-in
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-5 w-5"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" /></svg>
            </button>
        </div>
    </form>
</div>
@endsection
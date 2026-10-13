@extends('layouts.admin')

@section('header', 'Kelola Jadwal Booking')

@section('content')
@if(session('success'))
<div class="bg-emerald-50 text-emerald-800 border border-emerald-200 p-4 rounded-xl mb-6 flex items-center gap-3">
    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5 text-emerald-600"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /></svg>
    <span class="text-sm font-medium">{{ session('success') }}</span>
</div>
@endif

@if(session('error'))
<div class="mb-6 rounded-xl border border-red-200 bg-red-50 p-4 text-sm font-medium text-red-700">{{ session('error') }}</div>
@endif

@if(false)
<!-- Legacy bulk generate card -->
<div class="bg-white rounded-2xl border border-gray-100 shadow-xs p-6 md:p-8 mb-8">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 pb-6 mb-6 border-b border-gray-100">
        <div class="flex items-center gap-3">
            <div class="w-12 h-12 rounded-xl bg-[#1A3B32]/10 text-[#1A3B32] flex items-center justify-center font-bold text-xl shrink-0">
                ⚡
            </div>
            <div>
                <h4 class="text-lg font-semibold text-gray-900 leading-tight">Generasi Jadwal Booking Otomatis</h4>
                <p class="text-xs text-gray-500 mt-1">Default otomatis 07:00-20:00. Admin bisa menutup atau menambah jam tertentu.</p>
            </div>
        </div>
        <div class="flex items-center gap-2 text-xs font-medium text-gray-500 bg-gray-50 px-3 py-1.5 rounded-full border border-gray-100 self-start md:self-auto">
            <span class="w-2 h-2 rounded-full bg-emerald-500 inline-block"></span>
            13 Slot / Hari (07:00–20:00)
        </div>
    </div>

    <form action="{{ route('admin.slots.bulkGenerate') }}" method="POST" class="space-y-4">
        @csrf
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-5 gap-4 items-end">
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1.5">Dari</label>
                <input type="date" name="tanggal_mulai" value="{{ date('Y-m-d') }}" required class="w-full bg-gray-50/50 border border-gray-200 rounded-xl px-3.5 py-2.5 text-sm text-gray-900 focus:bg-white focus:border-[#1A3B32] focus:ring-1 focus:ring-[#1A3B32] transition">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1.5">Sampai</label>
                <input type="date" name="tanggal_selesai" value="{{ date('Y-m-d') }}" required class="w-full bg-gray-50/50 border border-gray-200 rounded-xl px-3.5 py-2.5 text-sm text-gray-900 focus:bg-white focus:border-[#1A3B32] focus:ring-1 focus:ring-[#1A3B32] transition">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1.5">Nama Stylist</label>
                <input type="text" id="kapster_input" name="kapster" placeholder="Contoh: Peter Parker" required class="w-full bg-gray-50/50 border border-gray-200 rounded-xl px-3.5 py-2.5 text-sm text-gray-900 focus:bg-white focus:border-[#1A3B32] focus:ring-1 focus:ring-[#1A3B32] transition">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1.5">Jam Operasional (Buka - Tutup)</label>
                <div class="grid grid-cols-2 gap-2">
                    <input type="time" name="jam_mulai" value="07:00" required class="w-full bg-gray-50/50 border border-gray-200 rounded-xl px-2.5 py-2.5 text-xs text-gray-900 focus:bg-white focus:border-[#1A3B32] transition">
                    <input type="time" name="jam_selesai" value="20:00" required class="w-full bg-gray-50/50 border border-gray-200 rounded-xl px-2.5 py-2.5 text-xs text-gray-900 focus:bg-white focus:border-[#1A3B32] transition">
                </div>
            </div>
            <input type="hidden" name="kuota" value="1">
            <div>
                <button type="submit" class="w-full bg-[#1A3B32] hover:bg-[#1A3B32]/90 text-white font-medium px-5 py-2.5 rounded-xl text-sm transition shadow-xs flex items-center justify-center gap-2 active:scale-95">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4 text-[#E76F51]"><path stroke-linecap="round" stroke-linejoin="round" d="m3.75 13.5 10.5-11.25L12 10.5h8.25L9.75 21.75 12 13.5H3.75z" /></svg>
                    <span>Generate Jadwal</span>
                </button>
            </div>
        </div>

        <!-- Quick Select Pills -->
        <div class="flex items-center gap-2 pt-2 text-xs text-gray-500 flex-wrap">
            <span class="font-medium text-gray-400">Pilih Stylist Cepat:</span>
            <button type="button" onclick="document.getElementById('kapster_input').value='Peter Parker'" class="bg-gray-100 hover:bg-[#E76F51]/10 hover:text-[#E76F51] border border-gray-200 px-2.5 py-1 rounded-lg transition font-medium text-gray-700">Peter Parker</button>
            <button type="button" onclick="document.getElementById('kapster_input').value='Liam Parker'" class="bg-gray-100 hover:bg-[#E76F51]/10 hover:text-[#E76F51] border border-gray-200 px-2.5 py-1 rounded-lg transition font-medium text-gray-700">Liam Parker</button>
            <button type="button" onclick="document.getElementById('kapster_input').value='Daniel Brooks'" class="bg-gray-100 hover:bg-[#E76F51]/10 hover:text-[#E76F51] border border-gray-200 px-2.5 py-1 rounded-lg transition font-medium text-gray-700">Daniel Brooks</button>
            <button type="button" onclick="document.getElementById('kapster_input').value='James Miller'" class="bg-gray-100 hover:bg-[#E76F51]/10 hover:text-[#E76F51] border border-gray-200 px-2.5 py-1 rounded-lg transition font-medium text-gray-700">James Miller</button>
        </div>
    </form>
</div>

@endif

<div class="mb-8 grid gap-6 lg:grid-cols-[minmax(0,1fr)_minmax(0,2fr)]">
    <div class="rounded-2xl border border-gray-100 bg-white p-6 shadow-xs">
        <h3 class="text-lg font-semibold text-gray-900">Tambah Stylist</h3>
        <p class="mt-1 text-sm text-gray-500">Stylist baru otomatis mendapat jam kerja default 07:00-20:00.</p>
        <form action="{{ route('admin.stylists.store') }}" method="POST" class="mt-5 space-y-4">
            @csrf
            <input name="nama" value="{{ old('nama') }}" required maxlength="100" class="w-full rounded-xl border-gray-300 text-sm shadow-sm focus:border-[#E76F51] focus:ring-[#E76F51]" placeholder="Nama stylist">
            <button class="w-full rounded-xl bg-[#1A3B32] px-4 py-3 text-sm font-medium text-white transition hover:bg-[#244d41]">Tambah Stylist</button>
        </form>
    </div>
    <div class="overflow-hidden rounded-2xl border border-gray-100 bg-white shadow-xs">
        <div class="border-b border-gray-100 bg-gray-50/50 p-6">
            <h3 class="text-lg font-semibold text-gray-900">Daftar Stylist</h3>
            <p class="mt-1 text-sm text-gray-500">Aktifkan atau nonaktifkan stylist tanpa menghapus riwayat booking.</p>
        </div>
        <div class="divide-y divide-gray-100">
            @forelse($stylists as $stylist)
                <div class="flex items-center justify-between gap-4 px-6 py-4">
                    <div>
                        <p class="font-medium text-gray-900">{{ $stylist->nama }}</p>
                        <p class="text-xs text-gray-500">{{ $stylist->slots_count }} slot · {{ $stylist->bookings_count }} booking</p>
                    </div>
                    <div class="flex items-center gap-2">
                        <form action="{{ route('admin.stylists.toggle', $stylist) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <button class="rounded-lg border px-3 py-2 text-xs font-medium {{ $stylist->status === 'aktif' ? 'border-amber-200 text-amber-700 hover:bg-amber-50' : 'border-emerald-200 text-emerald-700 hover:bg-emerald-50' }}">
                                {{ $stylist->status === 'aktif' ? 'Nonaktifkan' : 'Aktifkan' }}
                            </button>
                        </form>
                        @if($stylist->bookings_count === 0)
                            <form action="{{ route('admin.stylists.destroy', $stylist) }}" method="POST" data-confirm="Yakin ingin menghapus stylist {{ $stylist->nama }}? Tindakan ini tidak dapat dibatalkan.">
                                @csrf
                                @method('DELETE')
                                <button class="rounded-lg border border-red-200 px-3 py-2 text-xs font-medium text-red-700 hover:bg-red-50">Hapus</button>
                            </form>
                        @endif
                    </div>
                </div>
            @empty
                <p class="px-6 py-8 text-center text-sm text-gray-500">Belum ada stylist.</p>
            @endforelse
        </div>
    </div>
</div>

@php
    $calendarSlotsByDate = $calendarSlots->groupBy(fn ($slot) => \Carbon\Carbon::parse($slot->tanggal)->format('Y-m-d'));
    $calendarFirstDayOffset = $calendarStart->dayOfWeekIso - 1;
@endphp

<div class="bg-white rounded-2xl border border-gray-100 shadow-xs p-6 md:p-8 mb-8">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h3 class="text-lg font-semibold text-gray-900">Kalender Jadwal</h3>
            <p class="text-sm text-gray-500 mt-1">Lihat slot per tanggal dan stylist untuk mengatur booking bulan depan.</p>
        </div>
        <div class="flex items-center gap-2">
            <form method="GET" action="{{ route('admin.slots.index') }}" class="flex items-center gap-2">
                <label for="calendar-month" class="text-sm font-medium text-gray-700">Bulan</label>
                <input id="calendar-month" type="month" name="month" value="{{ $calendarMonth }}" onchange="this.form.submit()" class="rounded-lg border-gray-200 bg-gray-50 px-3 py-2 text-sm focus:border-[#1A3B32] focus:ring-[#1A3B32]">
            </form>
        </div>
    </div>

    <div class="grid grid-cols-7 gap-2 text-center text-xs font-semibold uppercase tracking-wider text-gray-400 mb-2">
        @foreach(['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'] as $dayName)
            <div class="py-2">{{ $dayName }}</div>
        @endforeach
    </div>

    <div class="grid grid-cols-7 gap-2">
        @for($blank = 0; $blank < $calendarFirstDayOffset; $blank++)
            <div class="min-h-28 rounded-xl bg-gray-50/50"></div>
        @endfor

        @for($day = 1; $day <= $calendarStart->daysInMonth; $day++)
            @php
                $calendarDate = $calendarStart->copy()->day($day);
                $dateKey = $calendarDate->format('Y-m-d');
                $daySlots = $calendarSlotsByDate->get($dateKey, collect());
            @endphp
            <a href="{{ route('admin.slots.create', ['tanggal' => $dateKey]) }}" class="block min-h-28 rounded-xl border {{ $daySlots->isNotEmpty() ? 'border-emerald-200 bg-emerald-50/40' : 'border-gray-100 bg-gray-50/40' }} p-2 transition hover:border-[#E76F51] hover:bg-[#E76F51]/5 focus:outline-none focus:ring-2 focus:ring-[#E76F51]/40" title="Atur jadwal {{ $calendarDate->translatedFormat('d F Y') }}">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-sm font-bold {{ $calendarDate->isToday() ? 'text-[#E76F51]' : 'text-gray-700' }}">{{ $day }}</span>
                    @if($daySlots->isNotEmpty())
                        <span class="rounded-full bg-emerald-100 px-1.5 py-0.5 text-[10px] font-semibold text-emerald-700">{{ $daySlots->count() }}</span>
                    @endif
                </div>
                <div class="space-y-1">
                    @foreach($daySlots->groupBy(fn ($slot) => $slot->stylist?->nama ?? $slot->kapster ?? 'Stylist') as $stylistName => $stylistSlots)
                        <div class="truncate text-[10px] font-semibold text-gray-700" title="{{ $stylistName }}">{{ $stylistName }}</div>
                        <div class="truncate text-[10px] text-gray-500">{{ $stylistSlots->count() }} slot · {{ $stylistSlots->where('status', 'tersedia')->count() }} tersedia</div>
                    @endforeach
                </div>
                <span class="mt-2 inline-flex text-[10px] font-medium text-[#E76F51]">Klik untuk atur jadwal</span>
            </a>
        @endfor
    </div>
</div>

<div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
    <div class="p-6 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
        <h3 class="text-lg font-medium text-gray-900">Daftar Jadwal Booking</h3>
        <a href="{{ route('admin.slots.create') }}" class="inline-flex items-center gap-2 bg-[#1A3B32] text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-[#1A3B32]/90 transition">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
            Tambah Jadwal Booking
        </a>
    </div>
    
    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm text-gray-600">
            <thead class="bg-gray-50 text-gray-500 font-medium">
                <tr>
                    <th class="px-6 py-4 border-b border-gray-100">Tanggal</th>
                    <th class="px-6 py-4 border-b border-gray-100">Waktu</th>
                    <th class="px-6 py-4 border-b border-gray-100">Stylist</th>
                    <th class="px-6 py-4 border-b border-gray-100 text-center">Kuota (Terisi)</th>
                    <th class="px-6 py-4 border-b border-gray-100">Status</th>
                    <th class="px-6 py-4 border-b border-gray-100 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($slots as $slot)
                <tr class="hover:bg-gray-50/50 transition">
                    <td class="px-6 py-4 font-medium text-gray-900">
                        {{ \Carbon\Carbon::parse($slot->tanggal)->translatedFormat('d M Y') }}
                    </td>
                    <td class="px-6 py-4">
                        {{ \Carbon\Carbon::parse($slot->jam_mulai)->format('H:i') }} - {{ \Carbon\Carbon::parse($slot->jam_selesai)->format('H:i') }}
                    </td>
                    <td class="px-6 py-4">
                        {{ $slot->stylist?->nama ?? $slot->kapster }}
                    </td>
                    <td class="px-6 py-4 text-center">
                        {{ $slot->kuota }} ({{ $slot->terisi }})
                    </td>
                    <td class="px-6 py-4">
                        @if($slot->status == 'tersedia')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                Tersedia
                            </span>
                        @elseif($slot->status == 'penuh')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                Penuh
                            </span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                Tutup
                            </span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-right">
                        <div class="flex justify-end gap-2">
                            <a href="{{ route('admin.slots.edit', $slot->id) }}" class="p-2 text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition" title="Edit">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" /></svg>
                            </a>
                            <form action="{{ route('admin.slots.destroy', $slot->id) }}" method="POST" data-confirm="Apakah Anda yakin ingin menghapus slot waktu {{ $slot->stylist?->nama ?? $slot->kapster }} ({{ \Carbon\Carbon::parse($slot->jam_mulai)->format('H:i') }}) ini?" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-2 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition" title="Hapus">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" /></svg>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                        Tidak ada slot harian.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    @if($slots->hasPages())
    <div class="p-6 border-t border-gray-100">
        {{ $slots->links() }}
    </div>
    @endif
</div>
@endsection

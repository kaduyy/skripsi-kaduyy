@extends('layouts.admin')

@section('header', 'Kelola Stylist')

@section('content')
    @if(session('success'))
        <div class="mb-6 rounded-xl border border-emerald-200 bg-emerald-50 p-4 text-sm font-medium text-emerald-800">{{ session('success') }}</div>
    @endif

    <div class="grid gap-6 lg:grid-cols-[minmax(0,1fr)_minmax(0,2fr)]">
        <div class="rounded-2xl border border-gray-100 bg-white p-6 shadow-sm">
            <h3 class="text-lg font-semibold text-gray-900">Tambah Stylist</h3>
            <p class="mt-1 text-sm text-gray-500">Stylist baru otomatis mendapat jam kerja default 07:00-20:00.</p>
            <form action="{{ route('admin.stylists.store') }}" method="POST" class="mt-6 space-y-4">
                @csrf
                <div>
                    <label for="nama" class="mb-1.5 block text-sm font-medium text-gray-700">Nama stylist</label>
                    <input id="nama" name="nama" value="{{ old('nama') }}" required maxlength="100" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-[#E76F51] focus:ring-[#E76F51]" placeholder="Contoh: Alan">
                    @error('nama') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                <button class="w-full rounded-xl bg-[#1A3B32] px-4 py-3 text-sm font-medium text-white transition hover:bg-[#1A3B32]/90">Tambah Stylist</button>
            </form>
        </div>

        <div class="overflow-hidden rounded-2xl border border-gray-100 bg-white shadow-sm">
            <div class="border-b border-gray-100 bg-gray-50/50 p-6">
                <h3 class="text-lg font-semibold text-gray-900">Daftar Stylist</h3>
                <p class="mt-1 text-sm text-gray-500">Nonaktifkan stylist yang sedang tidak bekerja tanpa menghapus riwayat booking.</p>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-gray-50 text-gray-500">
                        <tr>
                            <th class="px-6 py-4 font-medium">Nama</th>
                            <th class="px-6 py-4 font-medium">Status</th>
                            <th class="px-6 py-4 font-medium">Jadwal</th>
                            <th class="px-6 py-4 text-right font-medium">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($stylists as $stylist)
                            <tr>
                                <td class="px-6 py-4 font-medium text-gray-900">{{ $stylist->nama }}</td>
                                <td class="px-6 py-4">
                                    <span class="rounded-full px-2.5 py-1 text-xs font-medium {{ $stylist->status === 'aktif' ? 'bg-emerald-100 text-emerald-700' : 'bg-gray-100 text-gray-600' }}">
                                        {{ ucfirst($stylist->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-gray-500">{{ $stylist->slots_count }} slot · {{ $stylist->bookings_count }} booking</td>
                                <td class="px-6 py-4 text-right">
                                    <form action="{{ route('admin.stylists.toggle', $stylist) }}" method="POST" class="inline">
                                        @csrf
                                        @method('PATCH')
                                        <button class="rounded-lg border px-3 py-2 text-xs font-medium {{ $stylist->status === 'aktif' ? 'border-amber-200 text-amber-700 hover:bg-amber-50' : 'border-emerald-200 text-emerald-700 hover:bg-emerald-50' }}">
                                            {{ $stylist->status === 'aktif' ? 'Nonaktifkan' : 'Aktifkan' }}
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="px-6 py-8 text-center text-gray-500">Belum ada stylist.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

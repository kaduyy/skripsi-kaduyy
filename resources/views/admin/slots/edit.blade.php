@extends('layouts.admin')

@section('header', 'Edit Jadwal Booking')

@section('content')
<div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden max-w-2xl">
    <div class="p-6 border-b border-gray-100 bg-gray-50/50">
        <h3 class="text-lg font-medium text-gray-900">Form Edit Jadwal Booking</h3>
    </div>
    
    <form action="{{ route('admin.slots.update', $slot->id) }}" method="POST" class="p-6 space-y-6">
        @csrf
        @method('PUT')
        
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal</label>
            <input type="date" name="tanggal" value="{{ old('tanggal', $slot->tanggal) }}" required class="w-full border-gray-300 rounded-lg shadow-sm focus:border-[#E76F51] focus:ring-[#E76F51]">
            @error('tanggal') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Jam Mulai</label>
                <input type="time" name="jam_mulai" value="{{ old('jam_mulai', \Carbon\Carbon::parse($slot->jam_mulai)->format('H:i')) }}" required class="w-full border-gray-300 rounded-lg shadow-sm focus:border-[#E76F51] focus:ring-[#E76F51]">
                @error('jam_mulai') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Jam Selesai</label>
                <input type="time" name="jam_selesai" value="{{ old('jam_selesai', \Carbon\Carbon::parse($slot->jam_selesai)->format('H:i')) }}" required class="w-full border-gray-300 rounded-lg shadow-sm focus:border-[#E76F51] focus:ring-[#E76F51]">
                @error('jam_selesai') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
            </div>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Nama Stylist</label>
            <input type="text" name="kapster" value="{{ old('kapster', $slot->kapster) }}" required class="w-full border-gray-300 rounded-lg shadow-sm focus:border-[#E76F51] focus:ring-[#E76F51]">
            @error('kapster') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
        </div>
        
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Kuota</label>
                <input type="number" name="kuota" value="{{ old('kuota', $slot->kuota) }}" min="1" required class="w-full border-gray-300 rounded-lg shadow-sm focus:border-[#E76F51] focus:ring-[#E76F51]">
                @error('kuota') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                <select name="status" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-[#E76F51] focus:ring-[#E76F51]">
                    <option value="tersedia" {{ old('status', $slot->status) == 'tersedia' ? 'selected' : '' }}>Tersedia</option>
                    <option value="penuh" {{ old('status', $slot->status) == 'penuh' ? 'selected' : '' }}>Penuh</option>
                    <option value="tutup" {{ old('status', $slot->status) == 'tutup' ? 'selected' : '' }}>Tutup</option>
                </select>
                @error('status') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
            </div>
        </div>
        
        <div class="flex items-center gap-3 pt-4 border-t border-gray-100">
            <button type="submit" class="bg-[#E76F51] text-white px-6 py-2.5 rounded-lg text-sm font-medium hover:bg-opacity-90 transition">
                Simpan Perubahan
            </button>
            <a href="{{ route('admin.slots.index') }}" class="px-6 py-2.5 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 transition">
                Batal
            </a>
        </div>
    </form>
</div>
@endsection

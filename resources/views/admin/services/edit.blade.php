@extends('layouts.admin')

@section('header', 'Edit Layanan')

@section('content')
<div class="max-w-3xl">
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
        <form action="{{ route('admin.services.update', $service) }}" method="POST" enctype="multipart/form-data" class="p-8">
            @csrf
            @method('PUT')
            
            <div class="space-y-6">
                <!-- Foto Layanan -->
                <div>
                    <label for="foto" class="block text-sm font-medium text-gray-700 mb-1">Foto Layanan</label>
                    @if($service->foto)
                        <div class="mb-3 flex items-center gap-3">
                            <img src="{{ asset('storage/' . $service->foto) }}" class="w-20 h-20 object-cover rounded-lg border border-gray-200">
                            <span class="text-xs text-gray-500">Foto saat ini</span>
                        </div>
                    @endif
                    <input type="file" name="foto" id="foto" accept="image/*" class="w-full text-sm text-gray-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-[#1A3B32]/10 file:text-[#1A3B32] hover:file:bg-[#1A3B32]/20 cursor-pointer">
                    @error('foto') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>

                <!-- Nama Layanan -->
                <div>
                    <label for="nama_layanan" class="block text-sm font-medium text-gray-700 mb-1">Nama Layanan</label>
                    <input type="text" name="nama_layanan" id="nama_layanan" value="{{ old('nama_layanan', $service->nama_layanan) }}" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-[#1A3B32] focus:ring-[#1A3B32]">
                </div>

                <!-- Kategori -->
                <div>
                    <label for="kategori" class="block text-sm font-medium text-gray-700 mb-1">Kategori</label>
                    <select name="kategori" id="kategori" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-[#1A3B32] focus:ring-[#1A3B32]">
                        <option value="haircut" {{ old('kategori', $service->kategori) == 'haircut' ? 'selected' : '' }}>Haircut & Styling</option>
                        <option value="coloring" {{ old('kategori', $service->kategori) == 'coloring' ? 'selected' : '' }}>Coloring</option>
                        <option value="treatment" {{ old('kategori', $service->kategori) == 'treatment' ? 'selected' : '' }}>Treatment & Spa</option>
                        <option value="makeup" {{ old('kategori', $service->kategori) == 'makeup' ? 'selected' : '' }}>Make-up</option>
                    </select>
                </div>

                <div class="grid grid-cols-2 gap-6">
                    <!-- Harga -->
                    <div>
                        <label for="harga" class="block text-sm font-medium text-gray-700 mb-1">Harga (Rp)</label>
                        <input type="number" name="harga" id="harga" value="{{ old('harga', $service->harga) }}" required min="0" class="w-full rounded-md border-gray-300 shadow-sm focus:border-[#1A3B32] focus:ring-[#1A3B32]">
                    </div>

                    <!-- Durasi -->
                    <div>
                        <label for="durasi_menit" class="block text-sm font-medium text-gray-700 mb-1">Durasi (Menit)</label>
                        <input type="number" name="durasi_menit" id="durasi_menit" value="{{ old('durasi_menit', $service->durasi_menit) }}" required min="1" class="w-full rounded-md border-gray-300 shadow-sm focus:border-[#1A3B32] focus:ring-[#1A3B32]">
                    </div>
                </div>

                <!-- Deskripsi -->
                <div>
                    <label for="deskripsi" class="block text-sm font-medium text-gray-700 mb-1">Deskripsi Layanan</label>
                    <textarea name="deskripsi" id="deskripsi" rows="4" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-[#1A3B32] focus:ring-[#1A3B32]">{{ old('deskripsi', $service->deskripsi) }}</textarea>
                </div>

                <!-- Status -->
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                    <select name="status" id="status" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-[#1A3B32] focus:ring-[#1A3B32]">
                        <option value="aktif" {{ old('status', $service->status) == 'aktif' ? 'selected' : '' }}>Aktif</option>
                        <option value="nonaktif" {{ old('status', $service->status) == 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                    </select>
                </div>
            </div>

            <div class="mt-8 flex gap-4">
                <button type="submit" class="bg-[#1A3B32] text-white px-6 py-2.5 rounded-lg text-sm font-medium hover:bg-[#1A3B32]/90 transition">
                    Perbarui Layanan
                </button>
                <a href="{{ route('admin.services.index') }}" class="bg-white border border-gray-300 text-gray-700 px-6 py-2.5 rounded-lg text-sm font-medium hover:bg-gray-50 transition">
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>
@endsection

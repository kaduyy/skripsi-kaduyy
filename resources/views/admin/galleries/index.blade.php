@extends('layouts.admin')

@section('header', 'Kelola Galeri Portofolio')

@section('content')
<div class="flex flex-col md:flex-row gap-8">
    
    <!-- Form Tambah Galeri -->
    <div class="w-full md:w-1/3">
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="p-6 border-b border-gray-100 bg-gray-50/50">
                <h3 class="text-lg font-medium text-gray-900">Tambah Foto Portofolio</h3>
            </div>
            <div class="p-6">
                <form action="{{ route('admin.galleries.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Judul Foto</label>
                        <input type="text" name="judul" required class="w-full bg-[#FAFAF9] border border-gray-200 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:border-[#E76F51]" placeholder="Misal: Hasil Smoothing layer">
                        @error('judul') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Pilih Layanan Terkait</label>
                        <select name="service_id" required class="w-full bg-[#FAFAF9] border border-gray-200 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:border-[#E76F51]">
                            <option value="">-- Pilih Layanan --</option>
                            @foreach($services as $service)
                                <option value="{{ $service->id }}">{{ $service->nama_layanan }}</option>
                            @endforeach
                        </select>
                        @error('service_id') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Unggah Foto</label>
                        <input type="file" name="gambar" required accept="image/*" class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-gray-100 file:text-gray-700 hover:file:bg-gray-200 transition">
                        @error('gambar') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <button type="submit" class="w-full bg-[#1A3B32] text-white px-4 py-2.5 rounded-lg text-sm font-medium hover:bg-opacity-90 transition mt-2">
                        Simpan ke Galeri
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Daftar Galeri -->
    <div class="w-full md:w-2/3">
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-6">Daftar Portofolio Galeri</h3>
            
            <div class="grid grid-cols-2 md:grid-cols-3 gap-6">
                @forelse($galleries as $gallery)
                    <div class="group relative rounded-xl overflow-hidden border border-gray-100 shadow-sm">
                        <img src="{{ asset('storage/' . $gallery->file_path) }}" alt="{{ $gallery->judul }}" class="w-full h-48 object-cover group-hover:scale-105 transition duration-300">
                        
                        <!-- Overlay -->
                        <div class="absolute inset-0 bg-black/60 opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex flex-col justify-end p-4">
                            <p class="text-white font-medium text-sm truncate">{{ $gallery->judul }}</p>
                            <p class="text-white/70 text-xs mb-3 truncate">{{ $gallery->service->nama_layanan }}</p>
                            
                            <form action="{{ route('admin.galleries.destroy', $gallery->id) }}" method="POST" data-confirm="Apakah Anda yakin ingin menghapus foto portofolio '{{ $gallery->judul }}' ini?">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="w-full bg-red-600/90 hover:bg-red-600 text-white text-xs font-medium py-1.5 rounded-lg transition backdrop-blur-sm">
                                    Hapus
                                </button>
                            </form>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full py-12 text-center border border-dashed border-gray-200 rounded-xl">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor" class="w-12 h-12 text-gray-300 mx-auto mb-3"><path stroke-linecap="round" stroke-linejoin="round" d="m2.25 15.75 5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5 1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 0 0 1.5-1.5V6a1.5 1.5 0 0 0-1.5-1.5H3.75A1.5 1.5 0 0 0 2.25 6v12a1.5 1.5 0 0 0 1.5 1.5Zm10.5-11.25h.008v.008h-.008V8.25Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" /></svg>
                        <p class="text-gray-500">Belum ada foto portofolio.</p>
                    </div>
                @endforelse
            </div>
            
            @if($galleries->hasPages())
                <div class="mt-6 pt-6 border-t border-gray-100">
                    {{ $galleries->links() }}
                </div>
            @endif
        </div>
    </div>

</div>
@endsection

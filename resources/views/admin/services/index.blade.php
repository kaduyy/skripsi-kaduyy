@extends('layouts.admin')

@section('header', 'Kelola Layanan')

@section('content')
<div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
    <div class="p-6 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
        <h3 class="text-lg font-medium text-gray-900">Daftar Layanan</h3>
        <a href="{{ route('admin.services.create') }}" class="inline-flex items-center gap-2 bg-[#1A3B32] text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-[#1A3B32]/90 transition">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
            Tambah Layanan
        </a>
    </div>
    
    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm text-gray-600">
            <thead class="bg-gray-50 text-gray-500 font-medium">
                <tr>
                    <th class="px-6 py-4 border-b border-gray-100">Foto</th>
                    <th class="px-6 py-4 border-b border-gray-100">Nama Layanan</th>
                    <th class="px-6 py-4 border-b border-gray-100">Kategori</th>
                    <th class="px-6 py-4 border-b border-gray-100">Harga</th>
                    <th class="px-6 py-4 border-b border-gray-100">Durasi (Menit)</th>
                    <th class="px-6 py-4 border-b border-gray-100">Status</th>
                    <th class="px-6 py-4 border-b border-gray-100 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($services as $service)
                <tr class="hover:bg-gray-50/50 transition group">
                    <td class="px-6 py-4">
                        @if($service->foto)
                            <img src="{{ asset('storage/' . $service->foto) }}" class="w-12 h-12 object-cover rounded-lg border border-gray-200 shadow-xs">
                        @else
                            <div class="w-12 h-12 bg-gray-100 rounded-lg border border-gray-200 flex items-center justify-center text-gray-400 text-xs font-semibold">
                                No Pic
                            </div>
                        @endif
                    </td>
                    <td class="px-6 py-4 font-medium text-gray-900">{{ $service->nama_layanan }}</td>
                    <td class="px-6 py-4 capitalize">{{ $service->kategori }}</td>
                    <td class="px-6 py-4">Rp {{ number_format($service->harga, 0, ',', '.') }}</td>
                    <td class="px-6 py-4">{{ $service->durasi_menit }}</td>
                    <td class="px-6 py-4">
                        @if($service->status === 'aktif')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                Aktif
                            </span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                Nonaktif
                            </span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-right">
                        <div class="flex justify-end gap-3 opacity-0 group-hover:opacity-100 transition">
                            <a href="{{ route('admin.services.edit', $service) }}" class="text-blue-600 hover:text-blue-800" title="Edit">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" /></svg>
                            </a>
                            <form action="{{ route('admin.services.destroy', $service) }}" method="POST" class="inline-block" data-confirm="Apakah Anda yakin ingin menghapus layanan {{ $service->nama_layanan }} ini?">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-800" title="Hapus">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" /></svg>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                        Belum ada data layanan.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

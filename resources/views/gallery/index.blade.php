@extends('layouts.main')

@section('title', 'Galeri')

@section('content')
<section class="px-6 py-16 md:px-12 md:py-24">
    <div class="mx-auto max-w-7xl">
        <div class="mb-10 flex flex-col gap-5 md:flex-row md:items-end md:justify-between">
            <div>
                <p class="mb-3 text-xs font-semibold uppercase tracking-[0.25em] text-[#E76F51]">Galeri</p>
                <h1 class="playfair text-4xl text-gray-900 md:text-6xl">Hasil karya kami.</h1>
                <p class="mt-4 max-w-xl text-gray-500">Temukan inspirasi gaya rambut dan perawatan yang kami kerjakan untuk pelanggan Alan's Salon.</p>
            </div>

            <a href="{{ url('/') }}#galeri" class="inline-flex w-fit items-center rounded border border-gray-300 px-4 py-2.5 text-sm font-semibold text-gray-800 transition hover:bg-gray-100">Kembali ke beranda</a>
        </div>

        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
            @forelse($galleryFiles as $galleryFile)
                @php
                    $gallery = $galleries->firstWhere('file_path', $galleryFile);
                    $title = $gallery?->judul ?: basename($galleryFile);
                @endphp
                <figure class="group overflow-hidden rounded-[1.5rem] bg-white shadow-[0_16px_40px_rgba(15,23,42,0.08)] ring-1 ring-black/5 transition duration-300 hover:-translate-y-1 hover:shadow-[0_20px_45px_rgba(15,23,42,0.12)]">
                    <img src="{{ route('gallery.image', basename($galleryFile)) }}" alt="{{ $title }}" class="h-72 w-full object-cover transition duration-500 group-hover:scale-105">
                    @if($gallery?->judul || $gallery?->service)
                        <figcaption class="p-4">
                            @if($gallery?->judul)
                                <p class="font-medium text-gray-900">{{ $gallery->judul }}</p>
                            @endif
                            @if($gallery?->service)
                                <p class="mt-1 text-sm text-gray-500">{{ $gallery->service->nama_layanan }}</p>
                            @endif
                        </figcaption>
                    @endif
                </figure>
            @empty
                <div class="col-span-full rounded-lg bg-[#EAECE4] px-6 py-12 text-center">
                    <p class="text-gray-600">Belum ada foto galeri yang tersedia.</p>
                </div>
            @endforelse
        </div>
    </div>
</section>
@endsection

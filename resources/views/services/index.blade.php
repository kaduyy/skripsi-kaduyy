@extends('layouts.main')

@section('title', 'Layanan')

@section('content')

<section class="px-6 py-16 md:px-12 md:py-20">
    <div class="mx-auto max-w-7xl">
        <div class="mb-10 text-center md:mb-14">
            <p class="mb-3 text-xs font-semibold uppercase tracking-[0.22em] text-[#E76F51]">Layanan kami</p>
            <h1 class="playfair text-4xl text-gray-900 md:text-6xl">Setiap gaya dimulai dari layanan yang tepat.</h1>
            <p class="mx-auto mt-4 max-w-2xl text-sm leading-relaxed text-gray-600 md:text-base">
                Nikmati perawatan rambut dan kecantikan profesional yang dibuat sesuai kebutuhan, bentuk wajah, dan preferensi style Anda.
            </p>
        </div>

        @if($services->isEmpty())
            <div class="rounded-[1.5rem] border border-dashed border-gray-300 bg-gray-50 p-10 text-center text-gray-500">
                Belum ada layanan yang tersedia saat ini.
            </div>
        @else
            <div class="grid gap-6 md:grid-cols-2 xl:grid-cols-3">
                @foreach($services as $service)
                    <a href="{{ route('booking.create', ['service_id' => $service->id]) }}" class="group block overflow-hidden rounded-[1.5rem] border border-[#1A3B32]/10 bg-white shadow-[0_18px_45px_rgba(15,23,42,0.06)] transition duration-300 hover:-translate-y-1 hover:shadow-[0_22px_50px_rgba(15,23,42,0.1)]">
                        <div class="h-56 overflow-hidden bg-[#F6F1EC]">
                            @php
                                $imgUrl = $service->foto ? asset('storage/' . $service->foto) : (
                                    $service->kategori == 'haircut' ? 'https://images.unsplash.com/photo-1599351431202-1e0f0137899a?q=80&w=900&auto=format&fit=crop' : (
                                    $service->kategori == 'coloring' ? 'https://images.unsplash.com/photo-1621607512214-68297480165e?q=80&w=900&auto=format&fit=crop' : (
                                    $service->kategori == 'treatment' ? 'https://images.unsplash.com/photo-1562322140-8baeececf3df?q=80&w=900&auto=format&fit=crop' :
                                    'https://images.unsplash.com/photo-1521590832167-7e7d2d0f06d4?q=80&w=900&auto=format&fit=crop'
                                    ))
                                );
                            @endphp
                            <img src="{{ $imgUrl }}" alt="{{ $service->nama_layanan }}" class="h-full w-full object-cover transition duration-500 group-hover:scale-105">
                        </div>

                        <div class="p-6">
                            <div class="mb-3 flex items-center justify-between gap-3">
                                <h2 class="text-xl font-semibold text-gray-900">{{ $service->nama_layanan }}</h2>
                                <span class="rounded-full bg-[#E76F51]/10 px-2.5 py-1 text-[10px] font-semibold uppercase tracking-[0.12em] text-[#E76F51]">
                                    {{ $service->kategori }}
                                </span>
                            </div>

                            <p class="text-sm leading-relaxed text-gray-600">
                                {{ $service->deskripsi }}
                            </p>

                            <div class="mt-5 flex items-center justify-between gap-3 border-t border-gray-100 pt-4">
                                <div>
                                    <p class="text-xs uppercase tracking-[0.12em] text-gray-400">Harga</p>
                                    <p class="mt-1 text-xl font-semibold text-[#1A3B32]">Rp {{ number_format($service->harga, 0, ',', '.') }}</p>
                                </div>
                                <div class="text-right">
                                    <p class="text-xs uppercase tracking-[0.12em] text-gray-400">Durasi</p>
                                    <p class="mt-1 text-sm font-medium text-gray-700">{{ $service->durasi_menit }} menit</p>
                                </div>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
        @endif

        <div class="mt-12 text-center">
            <a href="{{ route('booking.create') }}" class="inline-flex items-center justify-center rounded bg-[#1A3B32] px-6 py-3 text-sm font-semibold text-white transition hover:bg-[#244a40]">
                Booking sekarang
            </a>
        </div>
    </div>
</section>

@endsection

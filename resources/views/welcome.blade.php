@extends('layouts.main')

@section('title', 'Beranda')

@section('content')

<section class="px-6 py-16 md:px-12 md:py-24">

    <div class="mx-auto grid max-w-7xl items-center gap-10 md:grid-cols-2">

        <div>

            <p class="mb-4 text-xs font-semibold uppercase tracking-[0.2em] text-[#E76F51]">Alan's Salon &middot; Batam</p>

            <h1 class="font-['Playfair_Display'] text-5xl leading-[0.95] tracking-[-0.04em] text-gray-900 md:text-7xl">Gaya yang menginspirasi.</h1>

            <p class="mt-5 max-w-lg text-base leading-relaxed text-gray-600">Perawatan rambut profesional dengan hasil yang sesuai gaya dan kebutuhan Anda.</p>

            <div class="mt-8 flex flex-wrap gap-3">

                <a href="{{ route('booking.create') }}" class="rounded bg-[#E76F51] px-5 py-3 text-sm font-semibold tracking-[0.08em] text-white transition hover:bg-[#d85f43]">Booking sekarang</a>

                <a href="{{ route('services.index') }}" class="rounded border border-gray-300 px-5 py-3 text-sm font-semibold tracking-[0.08em] text-gray-800 transition hover:bg-white">Lihat layanan</a>

            </div>

            <div class="mt-8 flex flex-wrap gap-3 text-xs font-medium text-gray-600">
                <span class="rounded-full border border-[#E76F51]/20 bg-[#E76F51]/5 px-3 py-2">10+ tahun pengalaman</span>
                <span class="rounded-full border border-[#1A3B32]/10 bg-white px-3 py-2">Stylist terlatih</span>
                <span class="rounded-full border border-[#1A3B32]/10 bg-white px-3 py-2">Produk premium</span>
            </div>

            <div class="mt-8 grid max-w-md grid-cols-3 gap-3">
                <div class="rounded-2xl border border-gray-200 bg-white p-3 shadow-sm">
                    <p class="text-2xl font-semibold text-[#1A3B32]">3.5k+</p>
                    <p class="mt-1 text-[11px] uppercase tracking-[0.12em] text-gray-500">Pelanggan</p>
                </div>
                <div class="rounded-2xl border border-gray-200 bg-white p-3 shadow-sm">
                    <p class="text-2xl font-semibold text-[#1A3B32]">4.9</p>
                    <p class="mt-1 text-[11px] uppercase tracking-[0.12em] text-gray-500">Rating</p>
                </div>
                <div class="rounded-2xl border border-gray-200 bg-white p-3 shadow-sm">
                    <p class="text-2xl font-semibold text-[#1A3B32]">Daily</p>
                    <p class="mt-1 text-[11px] uppercase tracking-[0.12em] text-gray-500">Buka</p>
                </div>
            </div>

            <p class="mt-6 text-xs font-medium uppercase tracking-[0.12em] text-gray-400">Buka setiap hari &middot; 09:00&ndash;22:00 WIB &middot;<br>Jalan Taman Baloi Mas, Kompleks Baloi Indah
Lubuk Baja, Batam</p>

        </div>

        <div class="grid grid-cols-2 gap-4">

            <img src="{{ asset('images/5248963336303483733.jpg') }}" alt="Potongan rambut di Alan's Salon" class="mt-8 h-64 w-full rounded-t-full object-cover md:h-80">

            <img src="{{ asset('images/as3.jpg') }}" alt="Interior salon" class="h-64 w-full rounded-b-full object-cover md:h-80">

        </div>

    </div>

</section>

<section id="layanan" class="bg-[#123C33] px-6 pb-10 pt-8 md:px-12">
    <div class="mx-auto max-w-7xl">

        <div class="mb-8 flex items-end justify-between gap-4">
            <div class="max-w-2xl">
                <p class="mb-4 text-xs font-semibold uppercase tracking-[0.22em] text-[#E8CDB3]">Layanan kami</p>
                <h1 class="playfair text-5xl leading-[0.95] tracking-[-0.05em] text-white md:text-7xl">
                    Layanan ahli yang<br>
                    dirancang khusus untuk<br>
                    Anda.
                </h1>
            </div>

            <a href="{{ route('services.index') }}" class="hidden text-sm font-medium text-white/80 underline decoration-white/60 underline-offset-4 transition hover:text-white md:inline-flex">
                Lihat Layanan >
            </a>
        </div>

        <div class="grid gap-4 md:grid-cols-3">
            @forelse($services->take(3) as $service)
                <a href="{{ route('services.index') }}" class="block rounded-[1.25rem] bg-[#1A4D45] p-6 text-white shadow-[0_8px_24px_rgba(0,0,0,0.12)] ring-1 ring-white/5 transition hover:-translate-y-1 hover:ring-[#E76F51]/60">
                    <h3 class="text-[1.05rem] font-semibold leading-snug text-white md:text-[1.2rem]">{{ $service->nama_layanan }}</h3>
                    <p class="mt-3 min-h-16 text-sm leading-relaxed text-white/75">
                        {{ $service->deskripsi }}
                    </p>
                    <p class="mt-6 text-xl font-semibold text-[#F0B695]">Rp {{ number_format($service->harga, 0, ',', '.') }}</p>
                </a>
            @empty
                <article class="rounded-[1.25rem] bg-[#1A4D45] p-6 text-white ring-1 ring-white/5">
                    <p class="text-sm text-white/75">Belum ada layanan tersedia.</p>
                </article>
                <article class="rounded-[1.25rem] bg-[#1A4D45] p-6 text-white ring-1 ring-white/5">
                    <p class="text-sm text-white/75">Belum ada layanan tersedia.</p>
                </article>
                <article class="rounded-[1.25rem] bg-[#1A4D45] p-6 text-white ring-1 ring-white/5">
                    <p class="text-sm text-white/75">Belum ada layanan tersedia.</p>
                </article>
            @endforelse
        </div>

    </div>
</section>

<section class="px-6 py-16 md:px-12">

    <div class="mx-auto max-w-7xl">

        <div class="mb-8 text-center">
            <p class="mb-3 text-xs font-semibold uppercase tracking-[0.25em] text-[#E76F51]">Kenapa pilih kami</p>
            <h2 class="playfair text-4xl text-gray-900 md:text-5xl">Salon yang memperhatikan detail dan kenyamanan Anda.</h2>
        </div>

        <div class="grid gap-4 md:grid-cols-4">
            <div class="rounded-[1.5rem] border border-[#1A3B32]/10 bg-[#F7F4F0] p-6">
                <div class="mb-4 inline-flex h-12 w-12 items-center justify-center rounded-full bg-[#E76F51] text-lg text-white"><i class="fa-solid fa-scissors"></i></div>
                <h3 class="text-xl font-semibold text-gray-900">Stylist ahli</h3>
                <p class="mt-2 text-sm leading-relaxed text-gray-600">Teknik potong dan styling yang disesuaikan dengan bentuk wajah dan kebutuhan harian.</p>
            </div>

            <div class="rounded-[1.5rem] border border-[#1A3B32]/10 bg-[#F7F4F0] p-6">
                <div class="mb-4 inline-flex h-12 w-12 items-center justify-center rounded-full bg-[#1A3B32] text-lg text-white"><i class="fa-solid fa-wand-sparkles"></i></div>
                <h3 class="text-xl font-semibold text-gray-900">Konsultasi personal</h3>
                <p class="mt-2 text-sm leading-relaxed text-gray-600">Setiap layanan dibuat sesuai gaya favorit Anda agar hasil tetap natural dan sesuai tren.</p>
            </div>

            <div class="rounded-[1.5rem] border border-[#1A3B32]/10 bg-[#F7F4F0] p-6">
                <div class="mb-4 inline-flex h-12 w-12 items-center justify-center rounded-full bg-[#E9D7B6] text-lg text-[#1A3B32]"><i class="fa-solid fa-bottle-droplet"></i></div>
                <h3 class="text-xl font-semibold text-gray-900">Produk premium</h3>
                <p class="mt-2 text-sm leading-relaxed text-gray-600">Menggunakan produk berkualitas untuk menjaga kesehatan rambut dan hasil lebih tahan lama.</p>
            </div>

            <div class="rounded-[1.5rem] border border-[#1A3B32]/10 bg-[#F7F4F0] p-6">
                <div class="mb-4 inline-flex h-12 w-12 items-center justify-center rounded-full bg-[#D9E8E3] text-lg text-[#1A3B32]"><i class="fa-solid fa-calendar-check"></i></div>
                <h3 class="text-xl font-semibold text-gray-900">Booking mudah</h3>
                <p class="mt-2 text-sm leading-relaxed text-gray-600">Reservasi cepat via website agar Anda bisa menyesuaikan waktu dengan gaya hidup.</p>
            </div>
        </div>

    </div>

</section>

<section id="galeri" class="px-6 py-16 md:px-12">

    <div class="mx-auto max-w-7xl">

        <div class="mb-10 flex flex-col gap-4 md:flex-row md:items-end md:justify-between">

            <div>

                <p class="mb-3 text-xs font-semibold uppercase tracking-[0.25em] text-[#E76F51]">Galeri</p>

                <h2 class="playfair text-4xl text-gray-900 md:text-5xl">Lihat hasil karya kami.</h2>

            </div>

            <div class="flex items-center gap-3">
                <span class="h-px w-12 bg-[#E76F51]"></span>
                <span class="text-sm font-medium text-gray-500">Gaya terbaik dimulai dari sini</span>
            </div>

            <a href="{{ route('gallery.index') }}" class="inline-flex w-fit items-center rounded border border-[#1A3B32] px-4 py-2 text-sm font-semibold text-[#1A3B32] transition hover:bg-[#1A3B32] hover:text-white">Lihat semua galeri</a>

        </div>

        <div class="grid grid-cols-2 gap-4 md:grid-cols-4">

            @forelse($galleries->take(4) as $gallery)

                <figure class="group overflow-hidden rounded-[1.5rem] bg-white shadow-[0_16px_40px_rgba(15,23,42,0.08)] ring-1 ring-black/5 transition duration-300 hover:-translate-y-1 hover:shadow-[0_20px_45px_rgba(15,23,42,0.12)]">
                    <img src="{{ route('gallery.image', basename($gallery->file_path)) }}" alt="{{ $gallery->judul }}" class="h-56 w-full object-cover transition duration-500 group-hover:scale-105 md:h-72">
                </figure>

            @empty
                @forelse($galleryFiles->take(4) as $galleryFile)
                <figure class="group overflow-hidden rounded-[1.5rem] bg-white shadow-[0_16px_40px_rgba(15,23,42,0.08)] ring-1 ring-black/5">
                    <img src="{{ route('gallery.image', basename($galleryFile)) }}" alt="Hasil karya Alan's Salon" class="h-56 w-full object-cover transition duration-500 group-hover:scale-105 md:h-72">
                </figure>
                @empty
                    <p class="col-span-full text-sm text-gray-500">Belum ada foto galeri.</p>
                @endforelse
            @endforelse

        </div>

    </div>

</section>

<section class="bg-[#EAECE4] px-6 py-16 md:px-12">

    <div class="mx-auto grid max-w-7xl gap-8 md:grid-cols-[1fr_2fr] md:items-center">

        <div>

            <p class="text-6xl font-semibold text-[#E76F51]">4.9</p>

            <p class="mt-2 text-sm text-gray-500">Rating pelanggan Alan's Salon</p>

        </div>

        <blockquote class="playfair text-2xl leading-snug text-gray-900 md:text-3xl">

            &ldquo;{{ $reviews->first()?->komentar ?? 'Stylist profesional, pelayanan cepat, dan hasilnya selalu memuaskan.' }}&rdquo;

            <footer class="mt-4 font-sans text-sm text-gray-500">{{ $reviews->first()?->user?->name ?? "Pelanggan Alan's Salon" }}</footer>

        </blockquote>

    </div>

</section>

<section class="px-6 py-16 text-center md:px-12">

    <h2 class="playfair text-4xl text-gray-900 md:text-5xl">Siap tampil lebih percaya diri?</h2>

    <p class="mx-auto mt-3 max-w-md text-gray-500">Pilih layanan dan jadwal yang sesuai untuk kunjungan Anda.</p>

    <a href="{{ route('booking.create') }}" class="mt-7 inline-block rounded bg-[#1A3B32] px-6 py-3 text-sm font-medium text-white transition hover:bg-[#244a40]">Reservasi sekarang</a>

</section>

@endsection

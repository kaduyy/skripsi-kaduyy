<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Alan's Salon - @yield('title', 'Welcome')</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&family=Playfair+Display:ital,wght@0,400..900;1,400..900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @else
        <style>
            body { font-family: 'Outfit', sans-serif; background-color: #FAFAF9; margin: 0; padding: 0; }
        </style>
    @endif
    
    <style>
        body { font-family: 'Outfit', sans-serif; }
        h1, h2, h3, h4, h5, h6, .playfair {
            font-family: 'Playfair Display', serif;
        }
    </style>
</head>
<body class="font-sans antialiased text-gray-800 bg-[#FAFAF9]">

    <header class="sticky top-0 z-50 w-full border-b border-white/10 bg-[#123C33] px-4 py-4 md:px-12">
        <div class="mx-auto flex max-w-7xl items-center justify-between gap-3">
            <a href="{{ url('/') }}" class="flex items-center gap-2 transition hover:opacity-80">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="h-6 w-6 text-[#E76F51] md:h-7 md:w-7">
                    <path fill-rule="evenodd" d="M3 6a3 3 0 0 1 3-3h12a3 3 0 0 1 3 3v12a3 3 0 0 1-3 3H6a3 3 0 0 1-3-3V6Zm14.25 6a1.5 1.5 0 0 1-1.5 1.5H8.25a1.5 1.5 0 0 1-1.5-1.5V9.75a1.5 1.5 0 0 1 1.5-1.5h9a1.5 1.5 0 0 1 1.5 1.5V12Z" clip-rule="evenodd" />
                </svg>
                <span class="text-xl font-semibold tracking-tight text-white md:text-2xl">Alan's Salon</span>
            </a>

            <nav class="hidden items-center gap-8 text-sm font-medium text-white/80 md:flex">
                <a href="{{ url('/') }}" class="transition hover:text-white">Beranda</a>
                <a href="{{ url('/#layanan') }}" class="transition hover:text-white">Layanan</a>
                <a href="{{ url('/#galeri') }}" class="transition hover:text-white">Galeri</a>
                <a href="{{ url('/#kontak') }}" class="transition hover:text-white">Kontak</a>
            </nav>

            <div class="hidden items-center gap-5 md:flex">
                <a href="https://wa.me/6281255552938" target="_blank" class="inline-flex items-center gap-2 text-sm font-medium text-white/80 transition hover:text-white" title="Chat via WhatsApp">
                    <i class="fa-brands fa-whatsapp text-lg text-[#25D366]"></i>
                    <span>(+62) 812-5555-2938</span>
                </a>

                @auth
                    <div class="flex items-center gap-4">
                        <a href="{{ url('/dashboard') }}" class="text-sm font-medium text-white/80 hover:text-white">Dashboard</a>
                        <a href="{{ route('profile.edit') }}" class="flex items-center gap-2 text-sm font-medium text-white/80 hover:text-white">
                            <img src="{{ auth()->user()->avatar_url }}" alt="{{ auth()->user()->name }}" class="h-8 w-8 rounded-full border border-white/20 object-cover">
                            <span>Pengaturan Profil</span>
                        </a>
                        <form method="POST" action="{{ route('logout') }}" class="inline">
                            @csrf
                            <button type="submit" class="rounded-xl bg-[#E76F51] px-4 py-2 text-sm font-medium text-white transition hover:bg-[#d85f43]">
                                Keluar
                            </button>
                        </form>
                    </div>
                @else
                    <a href="{{ route('login') }}" class="inline-flex items-center gap-2 rounded-md bg-[#E76F51] px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-[#d85f43]">
                        <span>Get Started</span>
                    </a>
                @endauth
            </div>

            <div class="flex items-center gap-2 md:hidden">
                @auth
                    <a href="{{ url('/dashboard') }}" class="rounded-full bg-white/10 px-3 py-2 text-[10px] font-semibold uppercase tracking-[0.12em] text-white">Dashboard</a>
                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit" class="rounded-full border border-white/20 px-3 py-2 text-[10px] font-semibold uppercase tracking-[0.12em] text-white">
                            Keluar
                        </button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="rounded-full bg-[#E76F51] px-3 py-2 text-[10px] font-semibold uppercase tracking-[0.12em] text-white">
                        Masuk
                    </a>
                @endauth
            </div>
        </div>
    </header>

    <main>
        @yield('content')
    </main>

    <footer id="kontak" class="bg-[#1A3B32] text-white pt-20 px-6 md:px-12 mt-20 relative overflow-hidden">
        <div class="max-w-7xl mx-auto flex flex-col md:flex-row justify-between items-start gap-12 relative z-10">
            <div class="max-w-xs space-y-6">
                <div class="flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-6 h-6 text-[#E76F51]">
                        <path fill-rule="evenodd" d="M3 6a3 3 0 0 1 3-3h12a3 3 0 0 1 3 3v12a3 3 0 0 1-3 3H6a3 3 0 0 1-3-3V6Zm14.25 6a1.5 1.5 0 0 1-1.5 1.5H8.25a1.5 1.5 0 0 1-1.5-1.5V9.75a1.5 1.5 0 0 1 1.5-1.5h9a1.5 1.5 0 0 1 1.5 1.5V12Z" clip-rule="evenodd" />
                    </svg>
                    <span class="text-2xl font-semibold tracking-tight">Alan's Salon</span>
                </div>
                <div>
                    <p class="text-sm text-gray-400 mb-1">Lokasi Kami</p>
                    <p class="font-medium">Alan's Art of Hair<br>Jalan Taman Baloi Mas, Kompleks Baloi Indah Lubuk Baja, Batam</p>
                    <div class="mt-5 overflow-hidden rounded-lg border border-white/10 bg-white/5">
                        <iframe
                            src="https://www.google.com/maps?q=Alan%27s%20Art%20of%20Hair%20Batam&z=15&output=embed"
                            title="Peta lokasi Alan's Art of Hair di Batam"
                            class="h-56 w-full border-0"
                            loading="lazy"
                            referrerpolicy="no-referrer-when-downgrade"
                            allowfullscreen>
                        ></iframe>
                    </div>
                    <a href="https://www.google.com/maps/search/?api=1&query=Alan%27s+Art+of+Hair+Batam" target="_blank" rel="noopener noreferrer" class="mt-3 inline-flex text-sm font-medium text-emerald-400 transition hover:text-emerald-300">
                        Buka lokasi di Google Maps
                    </a>
                </div>
                <div>
                    <p class="text-sm text-gray-400 mb-1">WhatsApp & Telepon</p>
                    <a href="https://wa.me/6281255552938" target="_blank" class="font-medium text-emerald-400 hover:text-emerald-300 transition inline-flex items-center gap-2">
                        <i class="fa-brands fa-whatsapp text-emerald-400 text-lg"></i>
                        <span>+62 812-5555-2938</span>
                    </a>
                </div>
            </div>
            
            <div class="max-w-sm flex-1 md:text-right pt-2 md:pt-0">
                <h3 class="text-3xl font-medium playfair text-white mb-10 text-center md:text-left leading-tight">Salon terbaik di Batam<br>kini lebih dekat dengan Anda.</h3>
                <p class="text-gray-400 text-sm mb-6 max-w-xs md:ml-auto">At our salon, every detail is designed to make you feel confident and beautiful.</p>
                <a href="{{ route('login') }}" class="text-sm font-medium hover:text-[#E76F51] transition">Masuk</a>
                <a href="{{ route('register') }}" class="bg-[#1A3B32] text-white px-5 py-2.5 rounded text-sm font-medium hover:bg-opacity-90 transition shadow-lg shadow-[#1A3B32]/20">Daftar</a>
            </div>
        </div>
        
        <div class="mt-8 relative z-0 flex justify-center text-center overflow-hidden">
            <h1 class="text-[8rem] md:text-[16rem] font-bold text-white/5 leading-[0.8] tracking-tighter select-none whitespace-nowrap">Alan's Salon</h1>
        </div>
        
        <div class="max-w-7xl mx-auto flex flex-col md:flex-row justify-between items-center text-xs text-gray-400 gap-4 py-6 border-t border-white/10 relative z-10 mt-[-2rem]">
            <p>&copy; {{ date('Y') }} Alan's Salon Batam. Seluruh hak cipta dilindungi.</p>
            <div class="flex gap-6">
                <a href="#" class="hover:text-white transition">Kebijakan Privasi</a>
                <a href="#" class="hover:text-white transition">Syarat & Ketentuan</a>
            </div>
        </div>
    </footer>

    <a href="https://wa.me/6281255552938?text=Halo%20Alan's%20Salon,%20saya%20ingin%20bertanya%20mengenai%20layanan%20reservasi." target="_blank" class="fixed bottom-6 right-6 z-50 flex items-center gap-2.5 bg-[#25D366] hover:bg-[#20ba59] text-white px-4 py-3 rounded-full shadow-xl hover:shadow-2xl hover:scale-105 transition-all duration-200 group" title="Chat WhatsApp Alan's Salon">
        <i class="fa-brands fa-whatsapp text-white text-2xl"></i>
        <span class="text-xs font-bold hidden sm:inline-block tracking-wide">Chat WhatsApp</span>
    </a>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            document.addEventListener('submit', function (e) {
                const form = e.target;
                if (form.dataset.confirmed === 'true') {
                    return;
                }
                const confirmMsg = form.getAttribute('data-confirm');
                const rawOnsubmit = form.getAttribute('onsubmit');
                let message = confirmMsg;
                if (!message && rawOnsubmit && rawOnsubmit.includes('confirm(')) {
                    const match = rawOnsubmit.match(/confirm\(['\"](.*?)['\"]\)/);
                    if (match && match[1]) {
                        message = match[1];
                    }
                }

                if (message) {
                    e.preventDefault();
                    e.stopImmediatePropagation();
                    form.removeAttribute('onsubmit');

                    let title = 'Konfirmasi Tindakan';
                    let icon = 'warning';
                    let confirmButtonText = 'Ya, Lanjutkan';
                    let confirmButtonColor = '#1A3B32';

                    const msgLower = message.toLowerCase();
                    if (msgLower.includes('hapus') || msgLower.includes('batal') || msgLower.includes('tolak')) {
                        confirmButtonColor = '#E76F51';
                        confirmButtonText = msgLower.includes('hapus') ? 'Ya, Hapus' : (msgLower.includes('batal') ? 'Ya, Batalkan' : 'Ya, Tolak');
                    } else if (msgLower.includes('setuju') || msgLower.includes('selesai') || msgLower.includes('terima')) {
                        confirmButtonColor = '#1A3B32';
                        confirmButtonText = 'Ya, Setuju';
                        icon = 'question';
                    }

                    Swal.fire({
                        heightAuto: false,
                        title: title,
                        text: message,
                        icon: icon,
                        showCancelButton: true,
                        confirmButtonColor: confirmButtonColor,
                        cancelButtonColor: '#9CA3AF',
                        confirmButtonText: confirmButtonText,
                        cancelButtonText: 'Batal',
                        customClass: {
                            popup: 'rounded-2xl shadow-2xl font-sans',
                            confirmButton: 'px-5 py-2.5 rounded-xl font-medium text-sm',
                            cancelButton: 'px-5 py-2.5 rounded-xl font-medium text-sm'
                        }
                    }).then((result) => {
                        if (result.isConfirmed) {
                            form.dataset.confirmed = 'true';
                            form.submit();
                        }
                    });
                }
            }, true);

            @if(session('success'))
                Swal.fire({
                    heightAuto: false,
                    toast: true,
                    position: 'top-end',
                    icon: 'success',
                    title: "{{ session('success') }}",
                    showConfirmButton: false,
                    timer: 3500,
                    timerProgressBar: true
                });
            @endif

            @if(session('error'))
                Swal.fire({
                    heightAuto: false,
                    icon: 'error',
                    title: 'Perhatian',
                    text: "{{ session('error') }}",
                    confirmButtonColor: '#E76F51',
                    customClass: {
                        popup: 'rounded-2xl shadow-2xl font-sans',
                        confirmButton: 'px-5 py-2.5 rounded-xl font-medium text-sm'
                    }
                });
            @endif
        });
    </script>

</body>
</html>

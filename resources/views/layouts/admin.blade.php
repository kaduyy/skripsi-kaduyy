<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin Dashboard - Alan's Salon</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&family=Playfair+Display:ital,wght@0,400..900;1,400..900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased text-gray-800 bg-[#FAFAF9] flex h-screen overflow-hidden">
    <style>
        html.swal2-height-auto, body.swal2-height-auto {
            height: 100% !important;
            min-height: 100vh !important;
        }
        aside {
            min-height: 100vh !important;
        }
    </style>

    <div id="adminSidebarOverlay" class="fixed inset-0 z-30 hidden bg-black/40 md:hidden"></div>

    <!-- Sidebar -->
    <aside id="adminSidebar" class="fixed inset-y-0 left-0 z-40 flex w-64 -translate-x-full flex-col bg-[#1A3B32] text-white shadow-2xl transition-transform duration-200 md:sticky md:top-0 md:z-auto md:flex md:h-screen md:w-[18.875rem] md:translate-x-0 md:shadow-none">
        <div class="h-20 flex items-center justify-center border-b border-white/10">
            <div class="flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-6 h-6 text-[#E76F51]">
                    <path fill-rule="evenodd" d="M3 6a3 3 0 0 1 3-3h12a3 3 0 0 1 3 3v12a3 3 0 0 1-3 3H6a3 3 0 0 1-3-3V6Zm14.25 6a1.5 1.5 0 0 1-1.5 1.5H8.25a1.5 1.5 0 0 1-1.5-1.5V9.75a1.5 1.5 0 0 1 1.5-1.5h9a1.5 1.5 0 0 1 1.5 1.5V12Z" clip-rule="evenodd" />
                </svg>
                <span class="text-xl font-semibold tracking-tight">Alan's Salon</span>
            </div>
        </div>
        <nav class="min-h-0 flex-1 py-6 px-4 space-y-1 overflow-y-auto">
            <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl {{ request()->routeIs('admin.dashboard') ? 'bg-[#E76F51] text-white' : 'text-[#A0B0AA] hover:bg-[#1A3B32] hover:text-white' }} transition">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 0 1 6 3.75h2.25A2.25 2.25 0 0 1 10.5 6v2.25a2.25 2.25 0 0 1-2.25 2.25H6a2.25 2.25 0 0 1-2.25-2.25V6ZM3.75 15.75A2.25 2.25 0 0 1 6 13.5h2.25a2.25 2.25 0 0 1 2.25 2.25V18a2.25 2.25 0 0 1-2.25 2.25H6A2.25 2.25 0 0 1 3.75 18v-2.25ZM13.5 6a2.25 2.25 0 0 1 2.25-2.25H18A2.25 2.25 0 0 1 20.25 6v2.25A2.25 2.25 0 0 1 18 10.5h-2.25a2.25 2.25 0 0 1-2.25-2.25V6ZM13.5 15.75a2.25 2.25 0 0 1 2.25-2.25H18a2.25 2.25 0 0 1 2.25 2.25V18A2.25 2.25 0 0 1 18 20.25h-2.25A2.25 2.25 0 0 1 13.5 18v-2.25Z" /></svg>
                <span class="font-medium text-sm">Dashboard</span>
            </a>

            <a href="{{ route('admin.services.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl {{ request()->routeIs('admin.services.*') ? 'bg-[#E76F51] text-white' : 'text-[#A0B0AA] hover:bg-[#1A3B32] hover:text-white' }} transition">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 6h9.75M10.5 6a1.5 1.5 0 1 1-3 0m3 0a1.5 1.5 0 1 0-3 0M3.75 6H7.5m3 12h9.75m-9.75 0a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m-3.75 0H7.5m9-6h3.75m-3.75 0a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m-9.75 0h9.75" /></svg>
                <span class="font-medium text-sm">Layanan</span>
            </a>

            <a href="{{ route('admin.slots.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl {{ request()->routeIs('admin.slots.*') ? 'bg-[#E76F51] text-white' : 'text-[#A0B0AA] hover:bg-[#1A3B32] hover:text-white' }} transition">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /></svg>
                <span class="font-medium text-sm">Kelola Jadwal Booking</span>
            </a>

            <a href="{{ route('admin.bookings.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl {{ request()->routeIs('admin.bookings.*') ? 'bg-[#E76F51] text-white' : 'text-[#A0B0AA] hover:bg-[#1A3B32] hover:text-white' }} transition">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 0 0-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 0 0-16.536-1.84M7.5 14.25 5.106 5.272M6 20.25a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Zm12.75 0a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Z" /></svg>
                <span class="font-medium text-sm">Reservasi</span>
            </a>
            <a href="{{ route('admin.galleries.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl {{ request()->routeIs('admin.galleries.*') ? 'bg-[#E76F51] text-white' : 'text-[#A0B0AA] hover:bg-[#1A3B32] hover:text-white' }} transition">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="m2.25 15.75 5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5 1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 0 0 1.5-1.5V6a1.5 1.5 0 0 0-1.5-1.5H3.75A1.5 1.5 0 0 0 2.25 6v12a1.5 1.5 0 0 0 1.5 1.5Zm10.5-11.25h.008v.008h-.008V8.25Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" /></svg>
                <span class="font-medium text-sm">Galeri</span>
            </a>

            <a href="{{ route('admin.users.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl {{ request()->routeIs('admin.users.*') ? 'bg-[#E76F51] text-white' : 'text-[#A0B0AA] hover:bg-[#1A3B32] hover:text-white' }} transition">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z" /></svg>
                <span class="font-medium text-sm">Kelola Akun</span>
            </a>

            <a href="{{ route('admin.reports.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl {{ request()->routeIs('admin.reports.*') ? 'bg-[#E76F51] text-white' : 'text-[#A0B0AA] hover:bg-[#1A3B32] hover:text-white' }} transition">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M3 13.5 12 4.5l9 9M5.25 11.25V19.5a1.5 1.5 0 0 0 1.5 1.5h10.5a1.5 1.5 0 0 0 1.5-1.5v-8.25M9 19.5v-6h6v6" /></svg>
                <span class="font-medium text-sm">Laporan Pendapatan</span>
            </a>
        </nav>

        {{-- WhatsApp Status Widget --}}
        @include('admin.partials.whatsapp-status')

        <div class="flex-shrink-0 p-4 border-t border-white/10">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="w-full flex items-center gap-3 px-4 py-3 rounded text-red-400 hover:bg-red-500/10 transition">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15M12 9l-3 3m0 0 3 3m-3-3h12.75" /></svg>
                    <span class="text-sm font-medium">Log Out</span>
                </button>
            </form>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="flex min-w-0 flex-1 flex-col h-full overflow-hidden bg-[#FAFAF9]">
        <!-- Top Header -->
        <header class="flex min-h-20 flex-shrink-0 items-center justify-between gap-3 border-b border-gray-200 bg-white px-4 sm:px-6 md:min-h-24 md:px-9">
            <div class="flex min-w-0 items-center gap-3">
                <button id="adminSidebarToggle" type="button" class="inline-flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-xl text-gray-600 hover:bg-gray-100 md:hidden" aria-label="Buka menu admin" aria-controls="adminSidebar" aria-expanded="false">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-6 w-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                    </svg>
                </button>
                <h2 class="truncate text-lg font-semibold text-gray-800 sm:text-xl">
                @yield('header')
                </h2>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('profile.edit') }}" class="flex items-center gap-2 rounded-xl px-1 py-2 transition hover:bg-gray-100 group sm:gap-3 sm:px-3" title="Buka Pengaturan Profil & Ganti Password">
                    <div class="text-right hidden sm:block">
                        <p class="text-sm font-bold text-gray-900 leading-tight group-hover:text-[#1A3B32] transition">{{ auth()->user()->name }}</p>
                        <p class="text-xs text-gray-400">Pengaturan Profil →</p>
                    </div>
                    <img src="{{ auth()->user()->avatar_url }}" alt="{{ auth()->user()->name }}" class="w-10 h-10 rounded-xl object-cover border border-gray-200 shadow-2xs group-hover:scale-105 transition">
                </a>
            </div>
        </header>

        <!-- Content Area -->
        <div class="min-w-0 flex-1 overflow-y-auto p-4 sm:p-6 md:pb-9 md:pl-9 md:pr-6 md:pt-9">
            @if(session('success'))
                <div class="mb-6 flex items-start gap-3 rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-green-800" role="status">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="mt-0.5 h-5 w-5 flex-shrink-0">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                    </svg>
                    <p class="text-sm font-medium">{{ session('success') }}</p>
                </div>
            @endif

            @if(session('error'))
                <div class="mb-6 flex items-start gap-3 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-red-700" role="alert">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="mt-0.5 h-5 w-5 flex-shrink-0">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m0 3.75h.008v.008H12v-.008ZM10.29 3.86 1.82 18a1.875 1.875 0 0 0 1.605 2.812h17.15A1.875 1.875 0 0 0 22.18 18L13.71 3.86a1.875 1.875 0 0 0-3.42 0Z" />
                    </svg>
                    <p class="text-sm font-medium">{{ session('error') }}</p>
                </div>
            @endif

            @if(isset($errors) && $errors->any())
                <div class="mb-6 bg-red-50 text-red-700 p-4 rounded-lg border border-red-200">
                    <ul class="list-disc list-inside text-sm font-medium">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @yield('content')
        </div>
    </main>

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const sidebar = document.getElementById('adminSidebar');
            const overlay = document.getElementById('adminSidebarOverlay');
            const toggle = document.getElementById('adminSidebarToggle');

            function closeSidebar() {
                sidebar.classList.add('-translate-x-full');
                overlay.classList.add('hidden');
                toggle.setAttribute('aria-expanded', 'false');
            }

            function openSidebar() {
                sidebar.classList.remove('-translate-x-full');
                overlay.classList.remove('hidden');
                toggle.setAttribute('aria-expanded', 'true');
            }

            toggle.addEventListener('click', function () {
                sidebar.classList.contains('-translate-x-full') ? openSidebar() : closeSidebar();
            });
            overlay.addEventListener('click', closeSidebar);
            sidebar.querySelectorAll('a').forEach(function (link) {
                link.addEventListener('click', closeSidebar);
            });
        });

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
                    const match = rawOnsubmit.match(/confirm\(['"](.*?)['"]\)/);
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

        });
    </script>
</body>
</html>

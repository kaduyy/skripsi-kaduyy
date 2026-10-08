<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login - Alan's Salon</title>

    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&family=Playfair+Display:ital,wght@0,400..900;1,400..900&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased text-gray-800 bg-[#FAFAF9] h-screen overflow-hidden flex">

    <!-- Left side: Image -->
    <div class="hidden md:block w-1/2 h-full relative">
        <div class="absolute inset-0 bg-[#1A3B32]/40 mix-blend-multiply z-10"></div>
        <img src="https://images.unsplash.com/photo-1560066984-138dadb4c035?q=80&w=1600&auto=format&fit=crop" class="w-full h-full object-cover z-0">
        
        <!-- Overlay Text -->
        <div class="absolute inset-0 z-20 flex flex-col justify-end p-16">
            <h1 class="text-6xl font-medium text-white font-['Playfair_Display'] leading-tight mb-4">
                Keindahan bertemu<br>kesempurnaan.
            </h1>
            <p class="text-white/80 text-lg max-w-md">Masuk untuk mengelola janji temu, melihat penawaran eksklusif, dan menjelajahi layanan premium kami.</p>
        </div>
    </div>

    <!-- Right side: Form -->
    <div class="w-full md:w-1/2 h-full flex flex-col justify-center px-8 md:px-24 bg-white relative">
        <!-- Back to Home -->
        <a href="/" class="absolute top-8 left-8 text-sm font-medium text-gray-500 hover:text-[#E76F51] flex items-center gap-2 transition">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5" /></svg>
            Kembali ke<br> Beranda
        </a>

        <div class="max-w-sm w-full mx-auto">
            <!-- Logo -->
            <div class="flex items-center gap-2 mb-12">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-8 h-8 text-[#E76F51]">
                    <path fill-rule="evenodd" d="M3 6a3 3 0 0 1 3-3h12a3 3 0 0 1 3 3v12a3 3 0 0 1-3 3H6a3 3 0 0 1-3-3V6Zm14.25 6a1.5 1.5 0 0 1-1.5 1.5H8.25a1.5 1.5 0 0 1-1.5-1.5V9.75a1.5 1.5 0 0 1 1.5-1.5h9a1.5 1.5 0 0 1 1.5 1.5V12Z" clip-rule="evenodd" />
                </svg>
                <span class="text-3xl font-semibold text-gray-900 tracking-tight">Alan's Salon</span>
            </div>

            <h2 class="text-3xl font-medium font-['Playfair_Display'] text-gray-900 mb-2">Selamat datang kembali</h2>
            <p class="text-sm text-gray-500 mb-8">Silakan masuk ke akun Anda untuk melihat jadwal dan reservasi.</p>

            <!-- Session Status -->
            <x-auth-session-status class="mb-4" :status="session('status')" />

            <form method="POST" action="{{ route('login') }}" class="space-y-5">
                @csrf

                <!-- Email Address -->
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Alamat Email</label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username" class="w-full bg-[#FAFAF9] border border-gray-200 rounded px-4 py-3 text-sm focus:outline-none focus:border-[#E76F51]">
                    <x-input-error :messages="$errors->get('email')" class="mt-2 text-red-500 text-xs" />
                </div>

                <!-- Password -->
                <div>
                    <div class="flex justify-between items-center mb-1">
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Kata Sandi</label>
                        @if (Route::has('password.request'))
                            <a class="text-sm text-[#E76F51] hover:underline font-medium" href="{{ route('password.request') }}">
                                Lupa sandi?
                            </a>
                        @endif
                    </div>
                    <input id="password" type="password" name="password" required autocomplete="current-password" class="w-full bg-[#FAFAF9] border border-gray-200 rounded px-4 py-3 text-sm focus:outline-none focus:border-[#E76F51]">
                    <x-input-error :messages="$errors->get('password')" class="mt-2 text-red-500 text-xs" />
                </div>

                <!-- Remember Me -->
                <div class="flex items-center">
                    <input id="remember_me" type="checkbox" name="remember" class="w-4 h-4 text-[#E76F51] border-gray-300 rounded focus:ring-[#E76F51]">
                    <label for="remember_me" class="ml-2 block text-sm text-gray-600 cursor-pointer">Ingat saya</label>
                </div>

                <div class="pt-2">
                    <button type="submit" class="w-full bg-[#1A3B32] text-white font-medium py-2.5 rounded hover:bg-opacity-90 transition shadow-lg shadow-[#1A3B32]/30">
                        Masuk
                    </button>
                </div>
                
                <p class="mt-8 text-center text-sm text-gray-500">
                    Belum punya akun? <a href="{{ route('register') }}" class="text-[#E76F51] font-semibold hover:underline">Daftar sekarang</a>
                </p>
            </form>
        </div>
    </div>
</body>
</html>

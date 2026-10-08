<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Register - Alan's Salon</title>

    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&family=Playfair+Display:ital,wght@0,400..900;1,400..900&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased text-gray-800 bg-[#FAFAF9] min-h-screen flex">

    <!-- Left side: Form -->
    <div class="w-full md:w-1/2 min-h-screen flex flex-col justify-center px-8 md:px-24 bg-white relative py-12">
        <!-- Back to Home -->
        <a href="/" class="absolute top-8 left-8 text-sm font-medium text-gray-500 hover:text-[#E76F51] flex items-center gap-2 transition">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5" /></svg>
            kembali ke<br> Beranda
        </a>

        <div class="max-w-sm w-full mx-auto">
            <!-- Logo -->
            <div class="flex items-center gap-2 mb-8">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-8 h-8 text-[#E76F51]">
                    <path fill-rule="evenodd" d="M3 6a3 3 0 0 1 3-3h12a3 3 0 0 1 3 3v12a3 3 0 0 1-3 3H6a3 3 0 0 1-3-3V6Zm14.25 6a1.5 1.5 0 0 1-1.5 1.5H8.25a1.5 1.5 0 0 1-1.5-1.5V9.75a1.5 1.5 0 0 1 1.5-1.5h9a1.5 1.5 0 0 1 1.5 1.5V12Z" clip-rule="evenodd" />
                </svg>
                <span class="text-3xl font-semibold text-gray-900 tracking-tight">Alan's Salon</span>
            </div>

            <h2 class="text-3xl font-medium font-['Playfair_Display'] text-gray-900 mb-2">Buat akun baru</h2>
            <p class="text-sm text-gray-500 mb-8">Bergabunglah untuk melakukan reservasi di salon kami.</p>

            <form method="POST" action="{{ route('register') }}" class="space-y-4">
                @csrf

                <!-- Name -->
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap</label>
                    <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus autocomplete="name" class="w-full bg-[#FAFAF9] border border-gray-200 rounded px-4 py-2.5 text-sm focus:outline-none focus:border-[#E76F51]">
                    <x-input-error :messages="$errors->get('name')" class="mt-1 text-red-500 text-xs" />
                </div>

                <!-- Email Address -->
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Alamat Email</label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="username" class="w-full bg-[#FAFAF9] border border-gray-200 rounded px-4 py-2.5 text-sm focus:outline-none focus:border-[#E76F51]">
                    <x-input-error :messages="$errors->get('email')" class="mt-1 text-red-500 text-xs" />
                </div>
                
                <!-- Phone (No WhatsApp) -->
                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <label for="no_whatsapp" class="block text-sm font-medium text-gray-700">Nomor WhatsApp</label>
                        <span class="inline-flex items-center gap-1 text-[11px] font-medium text-emerald-700 bg-emerald-50 border border-emerald-200/60 px-2 py-0.5 rounded-full">
                            <svg class="w-3 h-3 fill-emerald-600" viewBox="0 0 24 24"><path d="M12.04 2c-5.46 0-9.91 4.45-9.91 9.91 0 1.75.46 3.45 1.32 4.95L2.05 22l5.25-1.38c1.45.79 3.08 1.21 4.74 1.21 5.46 0 9.91-4.45 9.91-9.91 0-2.65-1.03-5.14-2.9-7.01A9.816 9.816 0 0 0 12.04 2m.01 1.67c2.2 0 4.26.86 5.82 2.42a8.225 8.225 0 0 1 2.41 5.83c0 4.54-3.7 8.24-8.24 8.24-1.48 0-2.93-.4-4.2-1.15l-.3-.18-3.12.82.83-3.04-.2-.31a8.196 8.196 0 0 1-1.26-4.38c0-4.54 3.7-8.24 8.24-8.24m4.52 11.66c-.19.53-1.09 1.04-1.54 1.09-.41.05-.94.07-2.83-.67-2.27-.89-3.72-3.18-3.83-3.33-.11-.15-.92-1.22-.92-2.33 0-1.11.58-1.65.79-1.88.21-.23.46-.29.61-.29.15 0 .31 0 .44.01.14.01.32-.05.5.38.19.46.65 1.58.71 1.69.06.12.09.25.02.4-.08.15-.12.25-.23.38-.11.14-.24.3-.34.4-.11.11-.23.24-.1.46.13.22.58.96 1.25 1.56.86.76 1.58 1 1.81 1.11.23.11.36.09.5-.06.13-.15.58-.67.73-.9.15-.23.31-.19.52-.11.21.08 1.34.63 1.57.75.23.11.38.17.44.27.06.1.06.59-.13 1.12Z"/></svg>
                            Notifikasi Reservasi
                        </span>
                    </div>
                    <div class="relative flex items-center">
                        <!-- Flag & +62 Prefix Badge -->
                        <div class="absolute left-0 inset-y-0 flex items-center pl-3 pr-2.5 pointer-events-none border-r border-gray-200/90 my-1 bg-gray-100/60 rounded-l">
                            <span class="inline-flex items-center gap-1.5 font-medium text-xs text-gray-700">
                                <svg class="w-4 h-3 rounded-xs shadow-xs" viewBox="0 0 640 480">
                                    <path fill="#e70011" d="M0 0h640v240H0z"/>
                                    <path fill="#fff" d="M0 240h640v240H0z"/>
                                </svg>
                                <span class="font-semibold">+62</span>
                            </span>
                        </div>
                        
                        <input id="no_whatsapp" type="tel" name="no_whatsapp" value="{{ old('no_whatsapp') }}" required autocomplete="tel"
                            class="w-full bg-[#FAFAF9] border border-gray-200 rounded pl-20 pr-10 py-2.5 text-sm font-medium text-gray-900 focus:outline-none focus:border-[#E76F51] focus:bg-white transition placeholder:text-gray-400 placeholder:font-normal"
                            placeholder="812 3456 7890"
                            oninput="formatWhatsAppNumber(this)">

                        <!-- Validation Indicator Icon -->
                        <div id="wa-valid-icon" class="absolute right-3 text-emerald-500 hidden pointer-events-none">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                        </div>
                    </div>
                    <p class="text-[11px] text-gray-400 mt-1 flex items-center gap-1">
                        <svg class="w-3.5 h-3.5 text-gray-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        Masukkan nomor setelah +62 (contoh: 8123456789 atau 0812...)
                    </p>
                    <x-input-error :messages="$errors->get('no_whatsapp')" class="mt-1 text-red-500 text-xs" />
                </div>

                <!-- Password -->
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Kata Sandi</label>
                    <input id="password" type="password" name="password" required autocomplete="new-password" class="w-full bg-[#FAFAF9] border border-gray-200 rounded px-4 py-2.5 text-sm focus:outline-none focus:border-[#E76F51]">
                    <x-input-error :messages="$errors->get('password')" class="mt-1 text-red-500 text-xs" />
                </div>

                <!-- Confirm Password -->
                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">Konfirmasi Kata Sandi</label>
                    <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password" class="w-full bg-[#FAFAF9] border border-gray-200 rounded px-4 py-2.5 text-sm focus:outline-none focus:border-[#E76F51]">
                    <x-input-error :messages="$errors->get('password_confirmation')" class="mt-1 text-red-500 text-xs" />
                </div>

                <div class="pt-4">
                    <button type="submit" class="w-full bg-[#1A3B32] text-white font-medium py-2.5 rounded hover:bg-opacity-90 transition shadow-lg shadow-[#1A3B32]/30 mt-2">
                Daftar Sekarang
            </button>
                </div>
                
                <p class="mt-8 text-center text-sm text-gray-500">
                Sudah punya akun? <a href="{{ route('login') }}" class="text-[#E76F51] font-semibold hover:underline">Masuk di sini</a>
            </p>
            </form>
        </div>
    </div>
    
    <!-- Right side: Image -->
    <div class="hidden md:block w-1/2 min-h-screen relative">
        <div class="absolute inset-0 bg-gradient-to-t from-[#1A3B32] via-[#1A3B32]/20 to-transparent z-10"></div>
        <img src="https://images.unsplash.com/photo-1599351431202-1e0f0137899a?q=80&w=1600&auto=format&fit=crop" class="w-full h-full object-cover z-0">
        
        <!-- Overlay Text -->
        <div class="absolute bottom-0 left-0 z-20 p-16">
            <h1 class="text-5xl font-medium text-white font-['Playfair_Display'] leading-tight mb-4">
                Crafting perfection <br>every single day.
            </h1>
            <p class="text-white/80 text-lg max-w-sm">Experience premium grooming and beauty services tailored just for you.</p>
        </div>
    </div>
    
    <script>
        function formatWhatsAppNumber(input) {
            // Hapus semua karakter selain angka
            let value = input.value.replace(/\D/g, '');
            
            // Jika user mengetik atau menempelkan awalan 62 atau 0, hilangkan otomatis
            if (value.startsWith('62')) {
                value = value.substring(2);
            } else if (value.startsWith('0')) {
                value = value.substring(1);
            }
            
            input.value = value;
            
            // Tampilkan ikon centang jika nomor valid (9 - 13 digit)
            const validIcon = document.getElementById('wa-valid-icon');
            if (validIcon) {
                if (value.length >= 9 && value.length <= 13) {
                    validIcon.classList.remove('hidden');
                    input.classList.add('border-emerald-500', 'bg-emerald-50/20');
                    input.classList.remove('border-gray-200');
                } else {
                    validIcon.classList.add('hidden');
                    input.classList.remove('border-emerald-500', 'bg-emerald-50/20');
                    input.classList.add('border-gray-200');
                }
            }
        }

        document.addEventListener('DOMContentLoaded', () => {
            const waInput = document.getElementById('no_whatsapp');
            if (waInput && waInput.value) {
                formatWhatsAppNumber(waInput);
            }
        });
    </script>
</body>
</html>

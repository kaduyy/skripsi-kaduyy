@php
    $isAdmin = auth()->user()->role === 'admin';
    $layout = $isAdmin ? 'layouts.admin' : 'layouts.main';
@endphp

@extends($layout)

@section($isAdmin ? 'header' : 'title', 'Pengaturan Profil & Password')

@section('content')
<div class="{{ $isAdmin ? 'max-w-4xl' : 'max-w-4xl mx-auto px-6 py-12' }} space-y-8">
    
    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl md:text-3xl font-bold text-gray-900 tracking-tight {{ $isAdmin ? '' : 'playfair' }}">
                Pengaturan Profil & Keamanan
            </h1>
            <p class="text-sm text-gray-500 mt-1">Kelola foto profil, informasi akun, dan perbarui kata sandi Anda.</p>
        </div>
        @if(!$isAdmin)
            <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-2 text-xs font-semibold text-gray-600 hover:text-gray-900 px-4 py-2 rounded-xl border border-gray-200 bg-white hover:bg-gray-50 transition shadow-2xs">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-3.5 h-3.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
                </svg>
                Kembali ke Dashboard
            </a>
        @endif
    </div>

    <!-- Status Alerts -->
    @if (session('status') === 'profile-updated')
        <div class="bg-emerald-50 text-emerald-800 border border-emerald-200/80 p-4 rounded-2xl flex items-center gap-3 shadow-xs">
            <div class="w-8 h-8 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center shrink-0">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-5 h-5">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 1 0 0-16 8 8 0 0 0 0 16Zm3.857-9.809a.75.75 0 0 0-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 1 0-1.06 1.061l2.5 2.5a.75.75 0 0 0 1.137-.089l4-5.5Z" clip-rule="evenodd" />
                </svg>
            </div>
            <div>
                <p class="text-sm font-bold text-emerald-900">Profil Berhasil Diperbarui</p>
                <p class="text-xs text-emerald-700">Foto profil dan informasi akun Anda telah tersimpan dengan aman.</p>
            </div>
        </div>
    @elseif (session('status') === 'password-updated')
        <div class="bg-emerald-50 text-emerald-800 border border-emerald-200/80 p-4 rounded-2xl flex items-center gap-3 shadow-xs">
            <div class="w-8 h-8 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center shrink-0">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-5 h-5">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 1 0 0-16 8 8 0 0 0 0 16Zm3.857-9.809a.75.75 0 0 0-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 1 0-1.06 1.061l2.5 2.5a.75.75 0 0 0 1.137-.089l4-5.5Z" clip-rule="evenodd" />
                </svg>
            </div>
            <div>
                <p class="text-sm font-bold text-emerald-900">Kata Sandi Berhasil Diperbarui</p>
                <p class="text-xs text-emerald-700">Gunakan kata sandi baru Anda pada sesi login berikutnya.</p>
            </div>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        
        <!-- KOLOM 1: Form Informasi Profil & Foto (7 cols) -->
        <div class="lg:col-span-7 bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden flex flex-col justify-between">
            <div>
                <div class="p-6 border-b border-gray-100 bg-gray-50/40 flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-[#1A3B32]/10 text-[#1A3B32] flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="w-5 h-5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-base font-bold text-gray-900">Informasi Profil</h2>
                        <p class="text-xs text-gray-500">Perbarui foto profil, nama, email, dan WhatsApp.</p>
                    </div>
                </div>

                <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="p-6 md:p-8 space-y-6">
                    @csrf
                    @method('patch')

                    <!-- UPLOAD FOTO PROFIL -->
                    <div class="flex flex-col sm:flex-row items-center gap-6 p-4 rounded-2xl bg-gray-50/60 border border-gray-100">
                        <div class="relative group">
                            <img id="avatar-preview" src="{{ $user->avatar_url }}" alt="{{ $user->name }}" class="w-20 h-20 rounded-2xl object-cover border-2 border-white shadow-md">
                            <label for="foto-input" class="absolute -bottom-2 -right-2 p-2 rounded-xl bg-[#1A3B32] text-white hover:bg-[#132c25] cursor-pointer shadow-sm transition hover:scale-105" title="Ganti Foto">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.827 6.175A2.31 2.31 0 0 1 5.186 7.23c-.38.054-.757.112-1.134.175C2.999 7.58 2.25 8.507 2.25 9.574V18a2.25 2.25 0 0 0 2.25 2.25h15A2.25 2.25 0 0 0 21.75 18V9.574c0-1.067-.75-1.994-1.802-2.169a47.865 47.865 0 0 0-1.134-.175 2.31 2.31 0 0 1-1.64-1.055l-.822-1.316a2.192 2.192 0 0 0-1.736-1.039 48.774 48.774 0 0 0-5.232 0 2.192 2.192 0 0 0-1.736 1.039l-.821 1.316Z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 12.75a4.5 4.5 0 1 1-9 0 4.5 4.5 0 0 1 9 0ZM18.75 10.5h.008v.008h-.008V10.5Z" />
                                </svg>
                            </label>
                            <input type="file" name="foto" id="foto-input" accept="image/*" class="hidden" onchange="previewAvatar(this)">
                        </div>

                        <div class="flex-1 text-center sm:text-left space-y-1">
                            <h4 class="text-sm font-bold text-gray-900">Foto Profil</h4>
                            <p class="text-xs text-gray-500">Format JPG, PNG, atau WebP. Maksimal 2MB.</p>
                            @if($user->foto)
                                <label class="inline-flex items-center gap-1.5 text-xs text-red-500 hover:text-red-700 cursor-pointer font-medium mt-1">
                                    <input type="checkbox" name="hapus_foto" value="1" class="rounded border-gray-300 text-red-500 focus:ring-red-500">
                                    <span>Hapus foto dan gunakan avatar default</span>
                                </label>
                            @endif
                        </div>
                    </div>
                    @error('foto') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror

                    <!-- Nama Lengkap -->
                    <div>
                        <label for="name" class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-2">Nama Lengkap</label>
                        <div class="relative">
                            <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" required class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-gray-200 text-sm focus:ring-2 focus:ring-[#1A3B32] focus:border-transparent focus:outline-none transition">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="w-4 h-4 text-gray-400 absolute left-3.5 top-3">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
                            </svg>
                        </div>
                        @error('name') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <!-- Email -->
                    <div>
                        <label for="email" class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-2">Alamat Email</label>
                        <div class="relative">
                            <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}" required class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-gray-200 text-sm focus:ring-2 focus:ring-[#1A3B32] focus:border-transparent focus:outline-none transition">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="w-4 h-4 text-gray-400 absolute left-3.5 top-3">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75" />
                            </svg>
                        </div>
                        @error('email') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <!-- Nomor WhatsApp -->
                    <div>
                        <label for="no_whatsapp" class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-2">Nomor WhatsApp</label>
                        <div class="relative">
                            <input type="text" name="no_whatsapp" id="no_whatsapp" value="{{ old('no_whatsapp', $user->no_whatsapp) }}" placeholder="Contoh: 08123456789" class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-gray-200 text-sm focus:ring-2 focus:ring-[#1A3B32] focus:border-transparent focus:outline-none transition">
                            <i class="fa-brands fa-whatsapp text-emerald-600 text-base absolute left-3.5 top-3"></i>
                        </div>
                        <span class="text-[11px] text-gray-400 mt-1 block">Notifikasi status reservasi akan dikirimkan ke nomor ini.</span>
                        @error('no_whatsapp') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div class="pt-4 border-t border-gray-100 flex justify-end">
                        <button type="submit" class="bg-[#1A3B32] hover:bg-[#132c25] text-white px-6 py-2.5 rounded-xl text-xs font-semibold transition shadow-sm hover:shadow-md flex items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                            </svg>
                            Simpan Perubahan Profil
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- KOLOM 2: Form Ubah Password (5 cols) -->
        <div class="lg:col-span-5 bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden flex flex-col justify-between">
            <div>
                <div class="p-6 border-b border-gray-100 bg-gray-50/40 flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-[#E76F51]/10 text-[#E76F51] flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="w-5 h-5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z" />
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-base font-bold text-gray-900">Ubah Password</h2>
                        <p class="text-xs text-gray-500">Gunakan kata sandi baru yang kuat.</p>
                    </div>
                </div>

                <form method="POST" action="{{ route('password.update') }}" class="p-6 md:p-8 space-y-5">
                    @csrf
                    @method('put')

                    <!-- Password Saat Ini -->
                    <div>
                        <label for="current_password" class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-2">Password Saat Ini</label>
                        <div class="relative">
                            <input type="password" name="current_password" id="current_password" required autocomplete="current-password" placeholder="Masukkan password lama" class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-gray-200 text-sm focus:ring-2 focus:ring-[#E76F51] focus:border-transparent focus:outline-none transition">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="w-4 h-4 text-gray-400 absolute left-3.5 top-3">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 5.25a3 3 0 0 1 3 3m3 0a6 6 0 0 1-7.029 5.912c-.563-.097-1.159.026-1.563.43L10.5 17.25H8.25v2.25H6v2.25H2.25v-2.818c0-.597.237-1.17.659-1.591l6.499-6.499c.404-.404.527-1 .43-1.563A6 6 0 1 1 21.75 8.25Z" />
                            </svg>
                        </div>
                        @if($errors->updatePassword->has('current_password'))
                            <span class="text-red-500 text-xs mt-1 block">{{ $errors->updatePassword->first('current_password') }}</span>
                        @endif
                    </div>

                    <!-- Password Baru -->
                    <div>
                        <label for="update_password" class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-2">Password Baru</label>
                        <div class="relative">
                            <input type="password" name="password" id="update_password" required autocomplete="new-password" placeholder="Minimal 8 karakter" class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-gray-200 text-sm focus:ring-2 focus:ring-[#E76F51] focus:border-transparent focus:outline-none transition">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="w-4 h-4 text-gray-400 absolute left-3.5 top-3">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z" />
                            </svg>
                        </div>
                        @if($errors->updatePassword->has('password'))
                            <span class="text-red-500 text-xs mt-1 block">{{ $errors->updatePassword->first('password') }}</span>
                        @endif
                    </div>

                    <!-- Konfirmasi Password Baru -->
                    <div>
                        <label for="update_password_confirmation" class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-2">Konfirmasi Password Baru</label>
                        <div class="relative">
                            <input type="password" name="password_confirmation" id="update_password_confirmation" required autocomplete="new-password" placeholder="Ulangi password baru" class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-gray-200 text-sm focus:ring-2 focus:ring-[#E76F51] focus:border-transparent focus:outline-none transition">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="w-4 h-4 text-gray-400 absolute left-3.5 top-3">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75m-3-7.036A11.959 11.959 0 0 1 3.598 6 11.99 11.99 0 0 0 3 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285Z" />
                            </svg>
                        </div>
                        @if($errors->updatePassword->has('password_confirmation'))
                            <span class="text-red-500 text-xs mt-1 block">{{ $errors->updatePassword->first('password_confirmation') }}</span>
                        @endif
                    </div>

                    <div class="pt-4 border-t border-gray-100 flex justify-end">
                        <button type="submit" class="w-full bg-[#E76F51] hover:bg-[#d55f42] text-white px-6 py-2.5 rounded-xl text-xs font-semibold transition shadow-sm hover:shadow-md flex items-center justify-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z" />
                            </svg>
                            Perbarui Kata Sandi
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</div>

<script>
function previewAvatar(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('avatar-preview').src = e.target.result;
        }
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
@endsection

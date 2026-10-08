@extends('layouts.admin')

@section('header', 'Edit Akun')

@section('content')
<div class="max-w-3xl">
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <!-- Card Header -->
        <div class="p-6 border-b border-gray-100 bg-gray-50/40 flex items-center justify-between">
            <div class="flex items-center gap-3.5">
                <div class="w-11 h-11 rounded-xl {{ $user->role === 'admin' ? 'bg-[#1A3B32] text-white' : 'bg-gray-100 text-gray-700' }} flex items-center justify-center font-bold text-base shrink-0 border border-gray-200/60 shadow-2xs">
                    {{ strtoupper(substr($user->name, 0, 2)) }}
                </div>
                <div>
                    <h2 class="text-lg font-bold text-gray-900 leading-snug">{{ $user->name }}</h2>
                    <p class="text-xs text-gray-400">{{ $user->email }}</p>
                </div>
            </div>
            <a href="{{ route('admin.users.index') }}" class="inline-flex items-center gap-1.5 text-xs text-gray-500 hover:text-gray-900 font-semibold px-3 py-1.5 rounded-lg border border-gray-200 hover:bg-gray-50 transition">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-3.5 h-3.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
                </svg>
                Kembali
            </a>
        </div>

        <form action="{{ route('admin.users.update', $user) }}" method="POST" class="p-8 space-y-6">
            @csrf
            @method('PUT')
            
            <!-- Section 1: Profil Pengguna -->
            <div class="space-y-5">
                <h3 class="text-xs font-bold text-gray-400 uppercase tracking-wider">Informasi Profil</h3>

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

                <!-- Nomor WhatsApp & Role -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div>
                        <label for="no_whatsapp" class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-2">Nomor WhatsApp</label>
                        <div class="relative">
                            <input type="text" name="no_whatsapp" id="no_whatsapp" value="{{ old('no_whatsapp', $user->no_whatsapp) }}" placeholder="08123456789" class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-gray-200 text-sm focus:ring-2 focus:ring-[#1A3B32] focus:border-transparent focus:outline-none transition">
                            <i class="fa-brands fa-whatsapp text-emerald-600 text-base absolute left-3.5 top-3"></i>
                        </div>
                        <span class="text-[11px] text-gray-400 mt-1 block">Digunakan untuk notifikasi WhatsApp otomatis.</span>
                        @error('no_whatsapp') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label for="role" class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-2">Peran Akun</label>
                        <div class="relative">
                            <select name="role" id="role" required class="w-full pl-10 pr-8 py-2.5 rounded-xl border border-gray-200 text-sm focus:ring-2 focus:ring-[#1A3B32] focus:border-transparent focus:outline-none bg-white font-medium text-gray-700 appearance-none cursor-pointer {{ $user->id === auth()->id() ? 'bg-gray-100 cursor-not-allowed opacity-75' : '' }}" {{ $user->id === auth()->id() ? 'disabled' : '' }}>
                                <option value="customer" {{ old('role', $user->role) === 'customer' ? 'selected' : '' }}>Customer (Pelanggan)</option>
                                <option value="admin" {{ old('role', $user->role) === 'admin' ? 'selected' : '' }}>Administrator</option>
                            </select>
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="w-4 h-4 text-gray-400 absolute left-3.5 top-3">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75m-3-7.036A11.959 11.959 0 0 1 3.598 6 11.99 11.99 0 0 0 3 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285Z" />
                            </svg>
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-3.5 h-3.5 text-gray-400 absolute right-3 top-3.5 pointer-events-none">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                            </svg>
                        </div>
                        @if($user->id === auth()->id())
                            <input type="hidden" name="role" value="{{ $user->role }}">
                            <span class="text-[11px] text-amber-600 mt-1 block">Anda tidak dapat mengubah peran akun Anda sendiri yang sedang aktif.</span>
                        @endif
                        @error('role') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>

            <!-- Section 2: Reset Password (Opsional) -->
            <div class="pt-6 border-t border-gray-100 space-y-5">
                <div>
                    <h3 class="text-xs font-bold text-gray-400 uppercase tracking-wider">Ubah Password (Opsional)</h3>
                    <p class="text-xs text-gray-400 mt-0.5">Biarkan kedua kolom di bawah kosong jika tidak ingin mengubah kata sandi.</p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div>
                        <label for="password" class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-2">Password Baru</label>
                        <div class="relative">
                            <input type="password" name="password" id="password" placeholder="Minimal 8 karakter" class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-gray-200 text-sm focus:ring-2 focus:ring-[#1A3B32] focus:border-transparent focus:outline-none transition">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="w-4 h-4 text-gray-400 absolute left-3.5 top-3">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z" />
                            </svg>
                        </div>
                        @error('password') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label for="password_confirmation" class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-2">Konfirmasi Password Baru</label>
                        <div class="relative">
                            <input type="password" name="password_confirmation" id="password_confirmation" placeholder="Ulangi password baru" class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-gray-200 text-sm focus:ring-2 focus:ring-[#1A3B32] focus:border-transparent focus:outline-none transition">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="w-4 h-4 text-gray-400 absolute left-3.5 top-3">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75m-3-7.036A11.959 11.959 0 0 1 3.598 6 11.99 11.99 0 0 0 3 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285Z" />
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex items-center justify-end gap-3 pt-6 border-t border-gray-100">
                <a href="{{ route('admin.users.index') }}" class="px-5 py-2.5 rounded-xl border border-gray-200 text-xs font-semibold text-gray-600 hover:bg-gray-50 transition">
                    Batal
                </a>
                <button type="submit" class="bg-[#1A3B32] hover:bg-[#132c25] text-white px-6 py-2.5 rounded-xl text-xs font-semibold transition shadow-sm hover:shadow-md flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                    </svg>
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

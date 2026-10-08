@extends('layouts.admin')

@section('header', 'Kelola Akun')

@section('content')
<div class="space-y-8">

    <!-- Header Section with Title & CTA -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Manajemen Akun Pengguna</h1>
            <p class="text-sm text-gray-500 mt-1">Kelola data administrator dan pelanggan terdaftar di sistem Alan's Salon.</p>
        </div>
        <a href="{{ route('admin.users.create') }}" class="inline-flex items-center justify-center gap-2 bg-[#1A3B32] hover:bg-[#132c25] text-white px-5 py-2.5 rounded-xl text-sm font-semibold transition-all duration-200 shadow-sm hover:shadow-md">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
            </svg>
            Tambah Akun Baru
        </a>
    </div>

    <!-- Stat Cards Overview -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
        <!-- Total Users Card -->
        <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-xs hover:border-gray-200 transition">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Total Pengguna</span>
                <div class="w-10 h-10 rounded-xl bg-[#1A3B32]/10 text-[#1A3B32] flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="w-5 h-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z" />
                    </svg>
                </div>
            </div>
            <div class="mt-4">
                <p class="text-3xl font-bold text-gray-900 tracking-tight">{{ number_format($totalUsers) }}</p>
                <p class="text-xs text-gray-500 mt-1">Keseluruhan akun terdaftar</p>
            </div>
        </div>

        <!-- Total Customers Card -->
        <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-xs hover:border-gray-200 transition">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Pelanggan (Customer)</span>
                <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="w-5 h-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
                    </svg>
                </div>
            </div>
            <div class="mt-4">
                <p class="text-3xl font-bold text-gray-900 tracking-tight">{{ number_format($totalCustomers) }}</p>
                <p class="text-xs text-gray-500 mt-1">Akun pemesan layanan</p>
            </div>
        </div>

        <!-- Total Admins Card -->
        <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-xs hover:border-gray-200 transition">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Administrator</span>
                <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="w-5 h-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75m-3-7.036A11.959 11.959 0 0 1 3.598 6 11.99 11.99 0 0 0 3 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285Z" />
                    </svg>
                </div>
            </div>
            <div class="mt-4">
                <p class="text-3xl font-bold text-gray-900 tracking-tight">{{ number_format($totalAdmins) }}</p>
                <p class="text-xs text-gray-500 mt-1">Pengelola sistem salon</p>
            </div>
        </div>
    </div>

    <!-- Main Table Card -->
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        
        <!-- Filter & Search Toolbar -->
        <div class="p-6 border-b border-gray-100 bg-gray-50/40 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <form action="{{ route('admin.users.index') }}" method="GET" class="flex flex-wrap items-center gap-3 w-full md:w-auto">
                <!-- Search Input -->
                <div class="relative flex-1 sm:w-80">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama, email, atau WhatsApp..." class="w-full pl-10 pr-4 py-2.5 text-xs rounded-xl border border-gray-200 focus:ring-2 focus:ring-[#1A3B32] focus:border-transparent focus:outline-none bg-white transition shadow-2xs">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4 text-gray-400 absolute left-3.5 top-3">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                    </svg>
                </div>

                <!-- Role Filter -->
                <div class="relative">
                    <select name="role" onchange="this.form.submit()" class="py-2.5 pl-3 pr-8 text-xs rounded-xl border border-gray-200 focus:ring-2 focus:ring-[#1A3B32] focus:outline-none bg-white font-medium text-gray-700 appearance-none cursor-pointer">
                        <option value="">Semua Peran</option>
                        <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>Admin</option>
                        <option value="customer" {{ request('role') === 'customer' ? 'selected' : '' }}>Customer</option>
                    </select>
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-3.5 h-3.5 text-gray-400 absolute right-2.5 top-3.5 pointer-events-none">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                    </svg>
                </div>

                <button type="submit" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2.5 rounded-xl text-xs font-semibold transition">
                    Cari
                </button>

                @if(request()->hasAny(['search', 'role']))
                    <a href="{{ route('admin.users.index') }}" class="text-xs text-red-500 hover:text-red-700 font-medium px-2 py-1 transition flex items-center gap-1">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-3.5 h-3.5">
                            <path d="M6.28 5.22a.75.75 0 0 0-1.06 1.06L8.94 10l-3.72 3.72a.75.75 0 1 0 1.06 1.06L10 11.06l3.72 3.72a.75.75 0 1 0 1.06-1.06L11.06 10l3.72-3.72a.75.75 0 0 0-1.06-1.06L10 8.94 6.28 5.22Z" />
                        </svg>
                        Reset Filter
                    </a>
                @endif
            </form>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-gray-600">
                <thead class="bg-gray-50 text-gray-500 font-semibold text-xs uppercase tracking-wider border-b border-gray-100">
                    <tr>
                        <th class="px-6 py-4">Profil Pengguna</th>
                        <th class="px-6 py-4">Kontak WhatsApp</th>
                        <th class="px-6 py-4">Peran (Role)</th>
                        <th class="px-6 py-4 text-center">Aktivitas Reservasi</th>
                        <th class="px-6 py-4">Tanggal Daftar</th>
                        <th class="px-6 py-4 text-right">Tindakan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($users as $user)
                    <tr class="hover:bg-gray-50/70 transition-colors group">
                        <!-- User Name & Avatar -->
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3.5">
                                <div class="w-10 h-10 rounded-xl {{ $user->role === 'admin' ? 'bg-[#1A3B32] text-white' : 'bg-gray-100 text-gray-700' }} flex items-center justify-center font-bold text-sm shrink-0 border border-gray-200/60 shadow-2xs">
                                    {{ strtoupper(substr($user->name, 0, 2)) }}
                                </div>
                                <div class="min-w-0">
                                    <p class="font-semibold text-gray-900 truncate leading-tight">{{ $user->name }}</p>
                                    <p class="text-xs text-gray-400 truncate mt-0.5">{{ $user->email }}</p>
                                </div>
                            </div>
                        </td>

                        <!-- WhatsApp -->
                        <td class="px-6 py-4">
                            @if($user->no_whatsapp)
                                <a href="https://wa.me/{{ $user->no_whatsapp }}" target="_blank" class="inline-flex items-center gap-1.5 text-xs text-emerald-700 hover:text-emerald-900 font-semibold bg-emerald-50 hover:bg-emerald-100/80 px-2.5 py-1 rounded-lg border border-emerald-200/60 transition group/link">
                                    <i class="fa-brands fa-whatsapp text-emerald-600 text-sm"></i>
                                    <span>{{ $user->no_whatsapp }}</span>
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-3 h-3 opacity-0 group-hover/link:opacity-100 transition">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 19.5 15-15m0 0H8.25m11.25 0v11.25" />
                                    </svg>
                                </a>
                            @else
                                <span class="text-xs text-gray-400 italic">Belum diisi</span>
                            @endif
                        </td>

                        <!-- Role Badge -->
                        <td class="px-6 py-4">
                            @if($user->role === 'admin')
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-[#1A3B32]/10 text-[#1A3B32] border border-[#1A3B32]/20">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-3.5 h-3.5 text-[#1A3B32]">
                                        <path fill-rule="evenodd" d="M10 1a4.5 4.5 0 0 0-4.5 4.5V9H5a2 2 0 0 0-2 2v6a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2v-6a2 2 0 0 0-2-2h-.5V5.5A4.5 4.5 0 0 0 10 1Zm3 8V5.5a3 3 0 1 0-6 0V9h6Z" clip-rule="evenodd" />
                                    </svg>
                                    Admin
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-medium bg-blue-50 text-blue-700 border border-blue-100">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-3.5 h-3.5 text-blue-500">
                                        <path d="M10 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6ZM3.465 14.493a1.23 1.23 0 0 0 .41 1.412A9.957 9.957 0 0 0 10 18c2.31 0 4.438-.784 6.131-2.1.43-.333.604-.903.408-1.41a7.002 7.002 0 0 0-13.074.003Z" />
                                    </svg>
                                    Customer
                                </span>
                            @endif
                        </td>

                        <!-- Total Bookings -->
                        <td class="px-6 py-4 text-center">
                            <span class="inline-flex items-center gap-1 text-xs font-bold text-gray-800 bg-gray-100 px-2.5 py-1 rounded-lg">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="w-3.5 h-3.5 text-gray-500">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5" />
                                </svg>
                                {{ $user->bookings_count }}
                            </span>
                        </td>

                        <!-- Registered At -->
                        <td class="px-6 py-4 text-xs text-gray-500">
                            {{ $user->created_at ? $user->created_at->translatedFormat('d M Y') : '-' }}
                        </td>

                        <!-- Actions -->
                        <td class="px-6 py-4 text-right">
                            <div class="flex justify-end items-center gap-1.5 opacity-90 group-hover:opacity-100 transition">
                                <a href="{{ route('admin.users.edit', $user) }}" class="p-2 text-gray-500 hover:text-[#1A3B32] hover:bg-[#1A3B32]/10 rounded-xl transition" title="Edit Akun">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="w-4 h-4">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                                    </svg>
                                </a>

                                @if($user->id !== auth()->id())
                                <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="inline-block" data-confirm="Apakah Anda yakin ingin menghapus akun {{ $user->name }} ({{ $user->email }})?">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-2 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-xl transition" title="Hapus Akun">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="w-4 h-4">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                        </svg>
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-16 text-center text-gray-400">
                            <div class="flex flex-col items-center justify-center gap-3">
                                <div class="w-12 h-12 rounded-2xl bg-gray-100 flex items-center justify-center text-gray-400">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="font-semibold text-gray-700">Tidak ada akun yang ditemukan</p>
                                    <p class="text-xs text-gray-400 mt-0.5">Coba ubah kata kunci pencarian atau sesuaikan filter peran.</p>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($users->hasPages())
        <div class="p-4 border-t border-gray-100 bg-gray-50/50">
            {{ $users->links() }}
        </div>
        @endif
    </div>
</div>
@endsection

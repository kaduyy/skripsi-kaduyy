@extends('layouts.main')



@section('title', 'Buat Reservasi')



@section('content')

<div class="max-w-4xl mx-auto px-6 py-12 md:py-20">

    <div class="mb-6 flex items-center justify-between gap-3">

        <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-2 text-sm font-medium text-gray-600 hover:text-[#E76F51] transition">

            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4">

                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5" />

            </svg>

            Kembali ke Dashboard

        </a>

        <span class="text-xs font-medium uppercase tracking-[0.2em] text-[#E76F51]">Reservasi</span>

    </div>



    <div class="mb-10 text-center">

        <h1 class="text-4xl font-medium playfair text-gray-900 mb-4">Buat Reservasi Baru</h1>

        <p class="text-gray-500">Pilih layanan dan waktu yang sesuai dengan jadwal Anda.</p>

    </div>



    @if(session('error'))

    <div class="bg-red-50 text-red-600 p-4 rounded-xl mb-8 flex items-start gap-3 border border-red-100">

        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 shrink-0"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3Z" /></svg>

        <span class="text-sm font-medium">{{ session('error') }}</span>

    </div>

    @endif



    <form action="{{ route('booking.store') }}" method="POST" class="bg-white rounded-2xl p-8 md:p-12 shadow-sm border border-gray-100 space-y-8">

        @csrf



        <!-- Pilihan Layanan -->

        <div>

            <h3 class="text-lg font-medium text-gray-900 mb-4 flex items-center gap-2">

                <span class="bg-[#1A3B32] text-white w-6 h-6 rounded-full flex items-center justify-center text-xs font-bold">1</span>

                Pilih Layanan

            </h3>

            <p class="mb-4 text-base text-gray-600">Pilih layanan yang Anda inginkan. Anda dapat memilih lebih dari satu layanan.</p>

            <div class="flex flex-wrap gap-3" data-service-options>
                @foreach($services as $service)
                    <button type="button" data-service-option data-id="{{ $service->id }}" data-label="{{ e($service->nama_layanan) }}" class="rounded-full border border-gray-200 bg-white px-4 py-2.5 text-sm font-semibold text-gray-700 transition hover:border-[#E76F51] hover:text-[#E76F51]">
                        {{ $service->nama_layanan }}
                    </button>
                @endforeach
            </div>

            <div class="hidden grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-5">

                @foreach($services as $service)

                @php

                    $imgUrl = $service->foto ? asset('storage/' . $service->foto) : (

                        $service->kategori == 'haircut' ? 'https://images.unsplash.com/photo-1599351431202-1e0f0137899a?q=80&w=400&auto=format&fit=crop' : (

                        $service->kategori == 'coloring' ? 'https://images.unsplash.com/photo-1621607512214-68297480165e?q=80&w=400&auto=format&fit=crop' : (

                        $service->kategori == 'treatment' ? 'https://images.unsplash.com/photo-1562322140-8baeececf3df?q=80&w=400&auto=format&fit=crop' :

                        'https://images.unsplash.com/photo-1585747860715-2ba37e788b70?q=80&w=400&auto=format&fit=crop'

                        ))

                    );

                @endphp

                <label class="relative block cursor-pointer group">

                    <input type="checkbox" name="service_ids[]" value="{{ $service->id }}" data-duration="{{ $service->durasi_menit }}" data-name="{{ e($service->nama_layanan) }}" data-category="{{ e(ucfirst($service->kategori ?? 'Layanan')) }}" data-price="{{ (float) $service->harga }}" data-description="{{ e($service->deskripsi ?: 'Layanan profesional dari Alan Salon.') }}" data-image="{{ e($imgUrl) }}" class="peer sr-only" {{ in_array((string) $service->id, array_map('strval', (array) ($selectedServiceIds ?? [])), true) ? 'checked' : '' }}>

                    <div class="h-full border border-gray-200 bg-white rounded-2xl overflow-hidden transition duration-300 group-hover:-translate-y-1 group-hover:shadow-lg group-hover:border-[#E76F51]/60 peer-checked:border-[#E76F51] peer-checked:ring-2 peer-checked:ring-[#E76F51]/20 peer-checked:bg-[#fff8f5] shadow-sm">

                        <div class="h-44 w-full overflow-hidden bg-gray-100 relative">

                            <img src="{{ $imgUrl }}" alt="{{ $service->nama_layanan }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-500">

                            <div class="absolute inset-0 bg-gradient-to-t from-black/55 via-black/5 to-transparent"></div>

                            <span class="absolute left-3 bottom-3 rounded-full bg-white/90 backdrop-blur px-2.5 py-1 text-[10px] font-bold uppercase tracking-wider text-[#1A3B32]">
                                {{ ucfirst($service->kategori ?? 'Layanan') }}
                            </span>

                            <span class="absolute top-3 right-3 bg-[#1A3B32]/90 text-white px-2.5 py-1 rounded-full text-[11px] font-bold shadow-sm">

                                {{ $service->durasi_menit }} menit

                            </span>

                            <span class="absolute top-3 left-3 flex items-center gap-1 rounded-full bg-[#E76F51] px-2.5 py-1 text-[10px] font-bold text-white opacity-0 transition peer-checked:opacity-100" aria-hidden="true">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="h-3.5 w-3.5"><path fill-rule="evenodd" d="M16.704 5.29a1 1 0 0 1 .006 1.414l-7.25 7.3a1 1 0 0 1-1.42 0l-3.25-3.27a1 1 0 1 1 1.42-1.408l2.54 2.557 6.54-6.587a1 1 0 0 1 1.414-.006Z" clip-rule="evenodd" /></svg>
                                Dipilih
                            </span>

                        </div>

                        <div class="p-5">

                            <p class="font-semibold text-gray-900 text-lg leading-snug mb-2">{{ $service->nama_layanan }}</p>

                            <p class="min-h-[4.5rem] text-sm leading-6 text-gray-500 line-clamp-3">
                                {{ $service->deskripsi ?: 'Layanan profesional dari Alan Salon dengan perawatan terbaik untuk kebutuhan Anda.' }}
                            </p>

                            <div class="mt-4 flex items-center justify-between gap-3 border-t border-gray-100 pt-4">
                                <div>
                                    <span class="block text-[10px] font-semibold uppercase tracking-wider text-gray-400">Harga mulai</span>
                                    <p class="mt-0.5 text-[#E76F51] font-bold text-base">Rp {{ number_format($service->harga, 0, ',', '.') }}</p>
                                </div>
                                <span class="inline-flex items-center gap-1.5 rounded-lg bg-gray-100 px-2.5 py-2 text-xs font-semibold text-gray-600">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-4 w-4"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6l4 2m6-2a10 10 0 1 1-20 0 10 10 0 0 1 20 0Z" /></svg>
                                    {{ $service->durasi_menit }} menit
                                </span>
                            </div>

                            <div class="mt-3 flex items-center justify-between gap-3">
                                <button type="button"
                                        class="service-detail-button text-[11px] font-semibold text-[#1A3B32] underline underline-offset-2 hover:text-[#E76F51]"
                                        data-service-detail
                                        data-name="{{ e($service->nama_layanan) }}"
                                        data-category="{{ e(ucfirst($service->kategori ?? 'Layanan')) }}"
                                        data-price="Rp {{ number_format($service->harga, 0, ',', '.') }}"
                                        data-duration="{{ $service->durasi_menit }} menit"
                                        data-description="{{ e($service->deskripsi ?: 'Layanan profesional dari Alan Salon.') }}"
                                        data-image="{{ e($imgUrl) }}">
                                    Lihat Detail
                                </button>
                                <span class="text-[11px] font-medium text-gray-400">Klik kartu untuk memilih</span>
                            </div>

                        </div>

                    </div>

                </label>

                @endforeach

            </div>

            <div class="mt-6 hidden" data-active-service-card>
                <p class="mb-3 text-xs font-bold uppercase tracking-[0.18em] text-[#1A3B32]">Layanan Terpilih</p>
                <div class="overflow-hidden rounded-2xl border-2 border-[#E76F51] bg-[#fff8f5] shadow-sm">
                    <img data-active-service-image src="" alt="" class="h-52 w-full object-cover">
                    <div class="p-5">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <span data-active-service-category class="text-[10px] font-bold uppercase tracking-wider text-[#1A3B32]"></span>
                                <h4 data-active-service-name class="mt-1 text-xl font-semibold text-gray-900"></h4>
                            </div>
                            <span class="rounded-full bg-[#E76F51] px-2.5 py-1 text-[10px] font-bold text-white">✓ Dipilih</span>
                        </div>
                        <p data-active-service-description class="mt-3 text-sm leading-6 text-gray-600"></p>
                        <div class="mt-4 flex items-center justify-between border-t border-[#E76F51]/20 pt-4 text-sm">
                            <span data-active-service-duration class="font-semibold text-gray-600"></span>
                            <span data-active-service-price class="font-bold text-[#E76F51]"></span>
                        </div>
                        <p data-active-service-status class="mt-4 text-center text-base font-bold text-[#1A3B32]"></p>
                        <button type="button" data-active-service-action class="mt-5 min-h-12 w-full rounded-xl bg-[#1A3B32] px-4 py-3 text-base font-bold text-white transition hover:bg-[#123027]"></button>
                    </div>
                </div>
            </div>

            <div class="mt-6 rounded-xl bg-[#1A3B32]/5 px-4 py-3 text-sm text-gray-700" aria-live="polite">
                <strong data-service-count>0</strong> layanan dipilih
                <span class="mx-1 text-gray-400">&middot;</span>
                Total <strong data-service-duration>0</strong> menit
                <span class="mx-1 text-gray-400">&middot;</span>
                <strong data-service-total>Rp 0</strong>
            </div>





        </div>

        <hr class="border-gray-100">



        <!-- Pilihan Slot Waktu -->

        <div>

            <h3 class="text-lg font-medium text-gray-900 mb-4 flex items-center gap-2">

                <span class="bg-[#1A3B32] text-white w-6 h-6 rounded-full flex items-center justify-center text-xs font-bold">2</span>

                Pilih Stylist

            </h3>

            <p class="mb-4 text-sm text-gray-500">Pilih satu jam mulai dan stylist. Sistem akan menahan slot berurutan sesuai total durasi layanan.</p>



            <div class="mb-5 max-w-sm">

                <label for="stylist_id" class="mb-1.5 block text-sm font-medium text-gray-700">Stylist</label>

                <select id="stylist_id" name="stylist_id" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#E76F51] focus:ring-[#E76F51]">

                    <option value="">Pilih stylist</option>

                    @foreach($stylists as $stylist)

                        <option value="{{ $stylist->id }}" @selected((string) $selectedStylistId === (string) $stylist->id)>{{ $stylist->nama }}</option>

                    @endforeach

                </select>

                @error('stylist_id') <span class="text-red-500 text-xs mt-2 block">{{ $message }}</span> @enderror

            </div>



            <!-- Pilih satu tanggal reservasi -->
            <div class="mt-5">
                <h3 class="text-lg font-medium text-gray-900 mb-3 flex items-center gap-2">
                    <span class="bg-[#1A3B32] text-white w-6 h-6 rounded-full flex items-center justify-center text-xs font-bold">3</span>
                    Pilih Tanggal Reservasi
                </h3>
                <input id="booking_date" name="booking_date" type="date" required
                       min="{{ $today->toDateString() }}"
                       @if($maximumBookingDate) max="{{ $maximumBookingDate->toDateString() }}" @endif
                       value="{{ $selectedDateFrom ?: $today->toDateString() }}"
                       class="w-full max-w-sm rounded-lg border-gray-300 shadow-sm focus:border-[#1A3B32] focus:ring-[#1A3B32]">
                <p class="mt-2 text-xs text-gray-500">Anda dapat reservasi mulai hari ini dan seterusnya selama jadwal stylist tersedia.</p>
            </div>

            <div class="mt-6">
                <h3 class="text-lg font-medium text-gray-900 mb-3 flex items-center gap-2">
                    <span class="bg-[#1A3B32] text-white w-6 h-6 rounded-full flex items-center justify-center text-xs font-bold">4</span>
                    Pilih Jam Reservasi
                </h3>
                <p class="mb-4 text-sm text-gray-500">Slot diperbarui otomatis setelah memilih layanan, stylist, atau tanggal.</p>
                <p class="mb-3 hidden text-sm text-[#1A3B32]" data-loading-slots role="status">Memuat jadwal tersedia...</p>

                @php $bookingDate = $selectedDateFrom ?: $today->toDateString(); @endphp
                <div data-slot-empty class="text-center py-8 bg-gray-50 rounded-xl border border-gray-100 {{ $selectedStylistId && !empty($selectedServiceIds) && $availableStartTimes->isNotEmpty() ? 'hidden' : '' }}">
                    <p class="text-gray-500">{{ !$selectedStylistId || empty($selectedServiceIds) ? 'Pilih layanan dan stylist untuk melihat jam yang tersedia.' : 'Tidak ada slot yang tersedia untuk tanggal ini. Silakan pilih tanggal lain.' }}</p>
                </div>
                <div data-slot-picker class="max-w-sm {{ $selectedStylistId && !empty($selectedServiceIds) && $availableStartTimes->isNotEmpty() ? '' : 'hidden' }}">
                    <label for="booking_time" class="mb-1.5 block text-sm font-medium text-gray-700">Jam Reservasi</label>
                    <select id="booking_time" name="booking_time" required class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#E76F51] focus:ring-[#E76F51]">
                        <option value="">Pilih jam</option>
                        @foreach($availableStartTimes as $availableTime)
                            <option value="{{ $availableTime['time'] }}" data-slot-id="{{ $availableTime['slot_id'] }}" @selected(old('booking_time') === $availableTime['time'])>{{ $availableTime['label'] }}</option>
                        @endforeach
                    </select>
                    <input type="hidden" id="slot_id" name="slot_id" value="{{ old('slot_id') }}">
                </div>
            </div>
            @error('slot_id') <span class="text-red-500 text-xs mt-2 block">{{ $message }}</span> @enderror

        </div>



        <hr class="border-gray-100">



        <!-- Catatan -->

        <div>

            <h3 class="text-lg font-medium text-gray-900 mb-4 flex items-center gap-2">

                <span class="bg-[#1A3B32] text-white w-6 h-6 rounded-full flex items-center justify-center text-xs font-bold">3</span>

                Catatan Tambahan (Opsional)

            </h3>

            <textarea name="catatan" rows="3" class="w-full border-gray-300 rounded-xl shadow-sm focus:border-[#E76F51] focus:ring-[#E76F51] resize-none" placeholder="Masukkan catatan tambahan untuk stylist...">{{ old('catatan') }}</textarea>

            @error('catatan') <span class="text-red-500 text-xs mt-2 block">{{ $message }}</span> @enderror

        </div>



        <div>

            <h3 class="text-lg font-medium text-gray-900 mb-4 flex items-center gap-2">

                <span class="bg-[#1A3B32] text-white w-6 h-6 rounded-full flex items-center justify-center text-xs font-bold">4</span>

                Metode Pembayaran

            </h3>

            <div class="grid gap-3 sm:grid-cols-2">

                <label class="cursor-pointer">

                    <input type="radio" name="payment_method" value="transfer_manual" class="peer sr-only" @checked(old('payment_method', 'transfer_manual') === 'transfer_manual') required>

                    <span class="block rounded-lg border border-gray-200 p-4 transition peer-checked:border-[#1A3B32] peer-checked:bg-[#1A3B32]/5">

                        <span class="block font-semibold text-gray-900">Transfer bank</span>

                        <span class="mt-1 block text-sm text-gray-500">Bayar uang muka 20% sekarang dan unggah bukti transfer.</span>

                    </span>

                </label>

                <label class="cursor-pointer">

                    <input type="radio" name="payment_method" value="tunai" class="peer sr-only" @checked(old('payment_method') === 'tunai') required>

                    <span class="block rounded-lg border border-gray-200 p-4 transition peer-checked:border-[#E76F51] peer-checked:bg-[#E76F51]/5">

                        <span class="block font-semibold text-gray-900">Bayar di tempat</span>

                        <span class="mt-1 block text-sm text-gray-500">Bayar di tempat saat datang ke salon.</span>

                    </span>

                </label>

            </div>

            <div data-cash-warning class="mt-3 hidden rounded-lg border border-amber-200 bg-amber-50 p-4 text-sm text-amber-900" role="alert">

                Slot belum dikunci untuk pembayaran di tempat. Stylist dan jam yang dipilih dapat dipesan pelanggan lain sebelum admin mengonfirmasi pembayaran Anda.

            </div>

            @error('payment_method') <span class="text-red-500 text-xs mt-2 block">{{ $message }}</span> @enderror

        </div>



        <div class="pt-6">

            <button type="submit" class="w-full bg-[#1A3B32] text-white px-6 py-4 rounded-xl font-medium hover:bg-opacity-90 transition shadow-lg shadow-[#1A3B32]/20 flex items-center justify-center gap-2">

                Lanjutkan Pemesanan

                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M17.25 8.25 21 12m0 0-3.75 3.75M21 12H3" /></svg>

            </button>

            <p class="text-xs text-center text-gray-500 mt-4">Pilih metode pembayaran yang sesuai sebelum melanjutkan reservasi.</p>

        </div>

    </form>

</div>

<div id="service-detail-modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-gray-900/60 p-4" role="dialog" aria-modal="true" aria-labelledby="service-detail-title">
    <div class="relative max-h-[90vh] w-full max-w-lg overflow-y-auto rounded-3xl bg-white shadow-2xl">
        <button type="button" data-close-service-modal class="absolute right-4 top-4 z-10 flex h-9 w-9 items-center justify-center rounded-full bg-white/90 text-xl text-gray-600 shadow hover:text-[#E76F51]" aria-label="Tutup detail layanan">&times;</button>
        <img data-service-detail-image src="" alt="" class="h-56 w-full object-cover">
        <div class="p-6">
            <span data-service-detail-category class="inline-flex rounded-full bg-[#1A3B32]/10 px-3 py-1 text-[11px] font-bold uppercase tracking-wider text-[#1A3B32]"></span>
            <h2 id="service-detail-title" data-service-detail-name class="mt-3 text-2xl font-semibold text-gray-900"></h2>
            <div class="mt-4 flex flex-wrap gap-3 text-sm">
                <span data-service-detail-price class="rounded-lg bg-[#E76F51]/10 px-3 py-2 font-bold text-[#E76F51]"></span>
                <span data-service-detail-duration class="rounded-lg bg-gray-100 px-3 py-2 font-medium text-gray-600"></span>
            </div>
            <p data-service-detail-description class="mt-5 text-sm leading-7 text-gray-600"></p>
            <button type="button" data-close-service-modal class="mt-6 w-full rounded-xl bg-[#1A3B32] px-4 py-3 text-sm font-semibold text-white transition hover:bg-[#123027]">Tutup</button>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const serviceInputs = Array.from(document.querySelectorAll('input[name="service_ids[]"]'));
    const serviceOptions = Array.from(document.querySelectorAll('[data-service-option]'));
    const activeServiceCard = document.querySelector('[data-active-service-card]');
    const totalPriceLabel = document.querySelector('[data-service-total]');
    const stylistSelect = document.getElementById('stylist_id');
    const dateInput = document.getElementById('booking_date');
    const bookingTime = document.getElementById('booking_time');
    const slotInput = document.getElementById('slot_id');
    const countLabel = document.querySelector('[data-service-count]');
    const durationLabel = document.querySelector('[data-service-duration]');
    const loadingLabel = document.querySelector('[data-loading-slots]');
    const slotPicker = document.querySelector('[data-slot-picker]');
    const slotEmpty = document.querySelector('[data-slot-empty]');
    const form = document.querySelector('form[action="{{ route('booking.store') }}"]');
    const paymentInputs = Array.from(document.querySelectorAll('input[name="payment_method"]'));
    const cashWarning = document.querySelector('[data-cash-warning]');
    const serviceModal = document.getElementById('service-detail-modal');
    const serviceDetailButtons = Array.from(document.querySelectorAll('[data-service-detail]'));
    let reloadTimeout;
    let loadingSlots = false;
    let lastSelectedServiceId = null;

    function renderSelectedServices() {
        const selected = serviceInputs.filter(input => input.checked);
        let totalPrice = 0;
        selected.forEach(input => {
            totalPrice += Number(input.dataset.price || 0);
        });
        countLabel.textContent = selected.length;
        durationLabel.textContent = selected.reduce((sum, input) => sum + Number(input.dataset.duration || 0), 0);
        totalPriceLabel.textContent = `Rp ${totalPrice.toLocaleString('id-ID')}`;
        const activeInput = serviceInputs.find(input => input.value === String(lastSelectedServiceId)) || selected[selected.length - 1];
        if (activeInput) {
            lastSelectedServiceId = activeInput.value;
            activeServiceCard.classList.remove('hidden');
            activeServiceCard.querySelector('[data-active-service-image]').src = activeInput.dataset.image;
            activeServiceCard.querySelector('[data-active-service-image]').alt = activeInput.dataset.name;
            activeServiceCard.querySelector('[data-active-service-category]').textContent = activeInput.dataset.category || 'Layanan';
            activeServiceCard.querySelector('[data-active-service-name]').textContent = activeInput.dataset.name;
            activeServiceCard.querySelector('[data-active-service-description]').textContent = activeInput.dataset.description;
            activeServiceCard.querySelector('[data-active-service-duration]').textContent = `${activeInput.dataset.duration} menit`;
            activeServiceCard.querySelector('[data-active-service-price]').textContent = `Rp ${Number(activeInput.dataset.price || 0).toLocaleString('id-ID')}`;
            const actionButton = activeServiceCard.querySelector('[data-active-service-action]');
            activeServiceCard.querySelector('[data-active-service-status]').textContent = activeInput.checked ? '✓ Layanan Sudah Dipilih' : 'Layanan belum dipilih';
            actionButton.textContent = activeInput.checked ? 'Batalkan Pilihan' : '+ Pilih Layanan';
            actionButton.classList.toggle('bg-[#1A3B32]', !activeInput.checked);
            actionButton.classList.toggle('bg-[#E76F51]', activeInput.checked);
        } else {
            lastSelectedServiceId = null;
            activeServiceCard.classList.add('hidden');
        }
        serviceOptions.forEach(option => {
            const active = selected.some(input => input.value === option.dataset.id);
            option.textContent = active ? `✓ ${option.dataset.label}` : option.dataset.label;
            option.classList.toggle('border-[#E76F51]', active);
            option.classList.toggle('bg-[#E76F51]/10', active);
            option.classList.toggle('text-[#E76F51]', active);
        });
    }

    function closeServiceModal() {
        serviceModal.classList.add('hidden');
        serviceModal.classList.remove('flex');
    }

    serviceDetailButtons.forEach(button => button.addEventListener('click', function (event) {
        event.preventDefault();
        event.stopPropagation();
        serviceModal.querySelector('[data-service-detail-image]').src = button.dataset.image;
        serviceModal.querySelector('[data-service-detail-image]').alt = button.dataset.name;
        serviceModal.querySelector('[data-service-detail-name]').textContent = button.dataset.name;
        serviceModal.querySelector('[data-service-detail-category]').textContent = button.dataset.category;
        serviceModal.querySelector('[data-service-detail-price]').textContent = button.dataset.price;
        serviceModal.querySelector('[data-service-detail-duration]').textContent = button.dataset.duration;
        serviceModal.querySelector('[data-service-detail-description]').textContent = button.dataset.description;
        serviceModal.classList.remove('hidden');
        serviceModal.classList.add('flex');
    }));
    serviceModal.querySelectorAll('[data-close-service-modal]').forEach(button => button.addEventListener('click', closeServiceModal));
    serviceModal.addEventListener('click', event => { if (event.target === serviceModal) closeServiceModal(); });
    document.addEventListener('keydown', event => { if (event.key === 'Escape') closeServiceModal(); });

    function updateSummary() {
        const selected = serviceInputs.filter(input => input.checked);
        renderSelectedServices();
    }

    async function reloadAvailability() {
        if (loadingSlots) return;
        const params = new URLSearchParams();
        serviceInputs.filter(input => input.checked).forEach(input => params.append('service_ids[]', input.value));
        if (stylistSelect.value) params.set('stylist_id', stylistSelect.value);
        if (dateInput.value) params.set('booking_date', dateInput.value);
        loadingSlots = true;
        if (loadingLabel) loadingLabel.classList.remove('hidden');
        if (bookingTime) bookingTime.disabled = true;
        try {
            const response = await fetch(@json(route('booking.availability')) + '?' + params.toString(), {
                headers: { 'Accept': 'application/json' }
            });
            if (!response.ok) throw new Error('Gagal memuat jadwal');
            const data = await response.json();
            bookingTime.innerHTML = '<option value="">Pilih jam</option>';
            data.times.forEach(time => {
                const option = new Option(time.label, time.time);
                option.dataset.slotId = time.slot_id;
                bookingTime.add(option);
            });
            slotInput.value = '';
            const hasTimes = data.times.length > 0;
            slotPicker.classList.toggle('hidden', !hasTimes);
            slotEmpty.classList.toggle('hidden', hasTimes);
            slotEmpty.querySelector('p').textContent = hasTimes
                ? ''
                : (data.reason || 'Tidak ada slot yang tersedia untuk tanggal ini. Silakan pilih tanggal lain.');
        } catch (error) {
            slotPicker.classList.add('hidden');
            slotEmpty.classList.remove('hidden');
            slotEmpty.querySelector('p').textContent = 'Jadwal tidak dapat dimuat. Silakan coba lagi.';
        } finally {
            loadingSlots = false;
            if (loadingLabel) loadingLabel.classList.add('hidden');
            if (bookingTime) bookingTime.disabled = false;
        }
    }

    function queueReload() {
        updateSummary();
        clearTimeout(reloadTimeout);
        reloadTimeout = setTimeout(reloadAvailability, 200);
    }

    serviceInputs.forEach(input => input.addEventListener('change', queueReload));
    serviceOptions.forEach(option => option.addEventListener('click', () => {
        lastSelectedServiceId = option.dataset.id;
        renderSelectedServices();
    }));
    activeServiceCard.querySelector('[data-active-service-action]').addEventListener('click', () => {
        const input = serviceInputs.find(item => item.value === String(lastSelectedServiceId));
        if (!input) return;
        input.checked = !input.checked;
        input.dispatchEvent(new Event('change', { bubbles: true }));
    });
    stylistSelect.addEventListener('change', queueReload);
    dateInput.addEventListener('change', function () {
        if (!dateInput.checkValidity()) {
            dateInput.reportValidity();
            return;
        }
        queueReload();
    });

    function updateCashWarning() {
        const selected = paymentInputs.find(input => input.checked);
        cashWarning.classList.toggle('hidden', !selected || selected.value !== 'tunai');
    }
    paymentInputs.forEach(input => input.addEventListener('change', updateCashWarning));
    if (bookingTime) {
        bookingTime.addEventListener('change', function () {
            slotInput.value = bookingTime.selectedOptions[0]?.dataset.slotId || '';
        });
    }
    updateCashWarning();
    updateSummary();

    if (form) {
        form.addEventListener('submit', function (event) {
            if (loadingSlots) { event.preventDefault(); return; }
            if (!serviceInputs.some(input => input.checked)) {
                event.preventDefault(); alert('Silakan pilih minimal satu layanan.'); return;
            }
            if (!stylistSelect.value) {
                event.preventDefault(); alert('Silakan pilih stylist.'); return;
            }
            if (!bookingTime?.value || !bookingTime.selectedOptions[0]?.dataset.slotId) {
                event.preventDefault(); alert('Silakan pilih jam reservasi yang tersedia.');
                return;
            }
            slotInput.value = bookingTime.selectedOptions[0].dataset.slotId;
        });
    }
});
</script>

@endsection

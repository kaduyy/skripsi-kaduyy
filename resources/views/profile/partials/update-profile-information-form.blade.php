<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('Profile Information') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            {{ __("Update your account's profile information and email address.") }}
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-6">
        @csrf
        @method('patch')

        <div>
            <x-input-label for="name" :value="__('Name')" />
            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $user->name)" required autofocus autocomplete="name" />
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>

        <div>
            <div class="flex items-center justify-between">
                <x-input-label for="no_whatsapp" value="Nomor WhatsApp" />
                <span class="text-[11px] font-medium text-emerald-700 bg-emerald-50 border border-emerald-200/60 px-2 py-0.5 rounded-full">
                    Notifikasi Booking
                </span>
            </div>
            <div class="relative flex items-center mt-1">
                <div class="absolute left-0 inset-y-0 flex items-center pl-3 pr-2.5 pointer-events-none border-r border-gray-200/90 my-1 bg-gray-100/60 rounded-l">
                    <span class="inline-flex items-center gap-1.5 font-medium text-xs text-gray-700">
                        <svg class="w-4 h-3 rounded-xs shadow-xs" viewBox="0 0 640 480">
                            <path fill="#e70011" d="M0 0h640v240H0z"/>
                            <path fill="#fff" d="M0 240h640v240H0z"/>
                        </svg>
                        <span class="font-semibold">+62</span>
                    </span>
                </div>
                @php
                    $displayWa = old('no_whatsapp', $user->no_whatsapp);
                    if ($displayWa && str_starts_with($displayWa, '62')) {
                        $displayWa = substr($displayWa, 2);
                    }
                @endphp
                <x-text-input id="no_whatsapp" name="no_whatsapp" type="tel" class="block w-full pl-20 pr-4" :value="$displayWa" autocomplete="tel" placeholder="812 3456 7890" oninput="this.value = this.value.replace(/\D/g, '').replace(/^(62|0)/, '')" />
            </div>
            <p class="text-[11px] text-gray-400 mt-1">Masukkan nomor setelah +62 (contoh: 81234567890)</p>
            <x-input-error class="mt-2" :messages="$errors->get('no_whatsapp')" />
        </div>

        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email', $user->email)" required autocomplete="username" />
            <x-input-error class="mt-2" :messages="$errors->get('email')" />

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div>
                    <p class="text-sm mt-2 text-gray-800">
                        {{ __('Your email address is unverified.') }}

                        <button form="send-verification" class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            {{ __('Click here to re-send the verification email.') }}
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 font-medium text-sm text-green-600">
                            {{ __('A new verification link has been sent to your email address.') }}
                        </p>
                    @endif
                </div>
            @endif
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('Save') }}</x-primary-button>

            @if (session('status') === 'profile-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-gray-600"
                >{{ __('Saved.') }}</p>
            @endif
        </div>
    </form>
</section>

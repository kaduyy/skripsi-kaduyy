<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        // Normalisasi format nomor WhatsApp ke 628xxx
        $rawWa = $request->no_whatsapp ? preg_replace('/[^0-9]/', '', $request->no_whatsapp) : '';
        if (str_starts_with($rawWa, '0')) {
            $normalizedWa = '62' . substr($rawWa, 1);
        } elseif (str_starts_with($rawWa, '8')) {
            $normalizedWa = '62' . $rawWa;
        } elseif (str_starts_with($rawWa, '62')) {
            $normalizedWa = $rawWa;
        } else {
            $normalizedWa = '62' . $rawWa;
        }
        $request->merge(['no_whatsapp' => $normalizedWa]);

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'no_whatsapp' => ['required', 'string', 'regex:/^628[0-9]{8,12}$/'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ], [
            'no_whatsapp.required' => 'Nomor WhatsApp wajib diisi untuk konfirmasi reservasi.',
            'no_whatsapp.regex' => 'Format nomor WhatsApp tidak valid. Masukkan nomor HP aktif (contoh: 81234567890 atau 081234567890).',
        ]);

        $user = User::create([
            'name' => $request->name,
            'no_whatsapp' => $request->no_whatsapp,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        event(new Registered($user));

        Auth::login($user);

        return redirect(route('dashboard', absolute: false));
    }
}

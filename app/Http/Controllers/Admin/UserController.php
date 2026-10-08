<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::withCount('bookings');

        // Search by name, email, or whatsapp
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('no_whatsapp', 'like', "%{$search}%");
            });
        }

        // Filter by role
        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        $users = $query->orderBy('created_at', 'desc')->paginate(15)->withQueryString();

        // Statistics
        $totalUsers = User::count();
        $totalCustomers = User::where('role', 'customer')->count();
        $totalAdmins = User::where('role', 'admin')->count();

        return view('admin.users.index', compact('users', 'totalUsers', 'totalCustomers', 'totalAdmins'));
    }

    public function create()
    {
        return view('admin.users.create');
    }

    public function store(Request $request)
    {
        // Normalize whatsapp number before validation
        if ($request->filled('no_whatsapp')) {
            $rawWa = preg_replace('/[^0-9]/', '', $request->no_whatsapp);
            if (str_starts_with($rawWa, '0')) {
                $rawWa = '62' . substr($rawWa, 1);
            } elseif (!str_starts_with($rawWa, '62') && !empty($rawWa)) {
                $rawWa = '62' . $rawWa;
            }
            $request->merge(['no_whatsapp' => $rawWa]);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'no_whatsapp' => ['nullable', 'string', 'regex:/^628[0-9]{8,13}$/'],
            'role' => ['required', Rule::in(['admin', 'customer'])],
            'password' => 'required|string|min:8|confirmed',
        ], [
            'name.required' => 'Nama pengguna wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.unique' => 'Email ini sudah terdaftar.',
            'no_whatsapp.regex' => 'Format nomor WhatsApp tidak valid (contoh: 08123456789).',
            'password.required' => 'Password wajib diisi.',
            'password.min' => 'Password minimal terdiri dari 8 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
        ]);

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'no_whatsapp' => $validated['no_whatsapp'] ?? null,
            'role' => $validated['role'],
            'password' => Hash::make($validated['password']),
            'email_verified_at' => now(),
        ]);

        return redirect()->route('admin.users.index')->with('success', "Akun {$validated['name']} berhasil ditambahkan.");
    }

    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        // Normalize whatsapp number before validation
        if ($request->filled('no_whatsapp')) {
            $rawWa = preg_replace('/[^0-9]/', '', $request->no_whatsapp);
            if (str_starts_with($rawWa, '0')) {
                $rawWa = '62' . substr($rawWa, 1);
            } elseif (!str_starts_with($rawWa, '62') && !empty($rawWa)) {
                $rawWa = '62' . $rawWa;
            }
            $request->merge(['no_whatsapp' => $rawWa]);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'no_whatsapp' => ['nullable', 'string', 'regex:/^628[0-9]{8,13}$/'],
            'role' => ['required', Rule::in(['admin', 'customer'])],
            'password' => 'nullable|string|min:8|confirmed',
        ], [
            'name.required' => 'Nama pengguna wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.unique' => 'Email ini sudah digunakan oleh akun lain.',
            'no_whatsapp.regex' => 'Format nomor WhatsApp tidak valid (contoh: 08123456789).',
            'password.min' => 'Password minimal terdiri dari 8 karakter.',
            'password.confirmed' => 'Konfirmasi password baru tidak cocok.',
        ]);

        $updateData = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'no_whatsapp' => $validated['no_whatsapp'] ?? null,
            'role' => $validated['role'],
        ];

        // Only update password if filled
        if (!empty($validated['password'])) {
            $updateData['password'] = Hash::make($validated['password']);
        }

        $user->update($updateData);

        return redirect()->route('admin.users.index')->with('success', "Data akun {$user->name} berhasil diperbarui.");
    }

    public function destroy(User $user)
    {
        // Prevent deleting own account
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Anda tidak dapat menghapus akun Anda sendiri yang sedang aktif digunakan.');
        }

        $userName = $user->name;
        $user->delete();

        return redirect()->route('admin.users.index')->with('success', "Akun {$userName} berhasil dihapus.");
    }
}

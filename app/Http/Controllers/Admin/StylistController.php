<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Stylist;
use Illuminate\Http\Request;

class StylistController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:100', 'unique:stylists,nama'],
        ]);

        Stylist::create([
            'nama' => trim($validated['nama']),
            'status' => 'aktif',
        ]);

        return redirect()->route('admin.slots.index')
            ->with('success', 'Stylist berhasil ditambahkan. Jadwal 07:00-20:00 akan dibuat otomatis saat diperlukan.');
    }

    public function toggle(Stylist $stylist)
    {
        $stylist->update([
            'status' => $stylist->status === 'aktif' ? 'nonaktif' : 'aktif',
        ]);

        return redirect()->route('admin.slots.index')
            ->with('success', $stylist->status === 'aktif'
                ? 'Stylist diaktifkan kembali.'
                : 'Stylist dinonaktifkan. Riwayat booking tetap aman.');
    }

    public function destroy(Stylist $stylist)
    {
        if ($stylist->bookings()->exists()) {
            return redirect()->route('admin.slots.index')
                ->with('error', 'Stylist tidak dapat dihapus karena sudah memiliki riwayat booking. Nonaktifkan saja.');
        }

        $stylist->delete();

        return redirect()->route('admin.slots.index')
            ->with('success', 'Stylist berhasil dihapus.');
    }
}

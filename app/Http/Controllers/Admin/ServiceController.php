<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Service;

class ServiceController extends Controller
{
    public function index()
    {
        $services = Service::latest()->get();
        return view('admin.services.index', compact('services'));
    }

    public function create()
    {
        return view('admin.services.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_layanan' => 'required|string|max:150',
            'kategori' => 'required|string|max:50',
            'deskripsi' => 'required|string',
            'harga' => 'required|numeric|min:0',
            'durasi_menit' => 'required|integer|min:1',
            'status' => 'required|in:aktif,nonaktif',
            'foto' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('foto')) {
            $validated['foto'] = $request->file('foto')->store('services', 'public');
        }

        Service::create($validated);

        return redirect()->route('admin.services.index')->with('success', 'Layanan berhasil ditambahkan.');
    }

    public function edit(Service $service)
    {
        return view('admin.services.edit', compact('service'));
    }

    public function update(Request $request, Service $service)
    {
        $validated = $request->validate([
            'nama_layanan' => 'required|string|max:150',
            'kategori' => 'required|string|max:50',
            'deskripsi' => 'required|string',
            'harga' => 'required|numeric|min:0',
            'durasi_menit' => 'required|integer|min:1',
            'status' => 'required|in:aktif,nonaktif',
            'foto' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('foto')) {
            if ($service->foto && \Illuminate\Support\Facades\Storage::disk('public')->exists($service->foto)) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($service->foto);
            }
            $validated['foto'] = $request->file('foto')->store('services', 'public');
        }

        $service->update($validated);

        return redirect()->route('admin.services.index')->with('success', 'Layanan berhasil diperbarui.');
    }

    public function destroy(Service $service)
    {
        if ($service->foto && \Illuminate\Support\Facades\Storage::disk('public')->exists($service->foto)) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($service->foto);
        }

        $service->delete();
        return redirect()->route('admin.services.index')->with('success', 'Layanan berhasil dihapus.');
    }
}

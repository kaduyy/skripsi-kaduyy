<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class GalleryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $galleries = \App\Models\Gallery::with('service')->latest()->paginate(12);
        $services = \App\Models\Service::all();
        return view('admin.galleries.index', compact('galleries', 'services'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'service_id' => 'required|exists:services,id',
            'judul' => 'required|string|max:255',
            'gambar' => 'required|image|max:2048',
        ]);

        $path = $request->file('gambar')->store('galleries', 'public');

        \App\Models\Gallery::create([
            'service_id' => $request->service_id,
            'judul' => $request->judul,
            'file_path' => $path,
        ]);

        return back()->with('success', 'Foto portofolio berhasil ditambahkan.');
    }

    public function destroy(\App\Models\Gallery $gallery)
    {
        if (\Illuminate\Support\Facades\Storage::disk('public')->exists($gallery->file_path)) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($gallery->file_path);
        }
        
        $gallery->delete();
        
        return back()->with('success', 'Foto portofolio berhasil dihapus.');
    }
}

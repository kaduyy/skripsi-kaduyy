<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SlotController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $now = \Carbon\Carbon::now('Asia/Jakarta');

        (new \App\Services\WorkingHoursService())->ensureDefaultSlots(
            $now->copy()->startOfDay(),
            $now->copy()->startOfDay()->addDays(30)
        );

        // Tandai slot yang sudah selesai sebagai tutup, tanpa menghapus riwayatnya.
        \App\Models\Slot::where(function ($query) use ($now) {
                $query->whereDate('tanggal', '<', $now->toDateString())
                    ->orWhere(function ($query) use ($now) {
                        $query->whereDate('tanggal', $now->toDateString())
                            ->whereTime('jam_selesai', '<=', $now->format('H:i:s'));
                    });
            })
            ->where('status', '!=', 'tutup')
            ->update(['status' => 'tutup']);

        $slots = \App\Models\Slot::with('stylist')->orderBy('tanggal', 'desc')->orderBy('jam_mulai')->paginate(10);

        $calendarMonth = request('month');
        try {
            $calendarStart = $calendarMonth
                ? \Carbon\Carbon::createFromFormat('Y-m', $calendarMonth)->startOfMonth()
                : $now->copy()->startOfMonth();
        } catch (\Throwable $e) {
            $calendarStart = $now->copy()->startOfMonth();
        }

        $calendarMonth = $calendarStart->format('Y-m');
        $calendarSlots = \App\Models\Slot::with('stylist')
            ->whereBetween('tanggal', [
                $calendarStart->toDateString(),
                $calendarStart->copy()->endOfMonth()->toDateString(),
            ])
            ->orderBy('tanggal')
            ->orderBy('jam_mulai')
            ->get();

        $stylists = \App\Models\Stylist::withCount(['slots', 'bookings'])
            ->orderBy('nama')
            ->get();

        return view('admin.slots.index', compact('slots', 'calendarMonth', 'calendarStart', 'calendarSlots', 'stylists'));
    }

    public function create(Request $request)
    {
        return view('admin.slots.create', [
            'defaultDate' => $request->query('tanggal', now('Asia/Jakarta')->toDateString()),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'jam_mulai' => 'required|date_format:H:i',
            'jam_selesai' => 'required|date_format:H:i|after:jam_mulai',
            'kapster' => 'required|string|max:100',
            'kuota' => 'required|integer|min:1',
            'status' => 'required|in:tersedia,penuh,tutup',
            'mode_per_jam' => 'nullable|boolean',
        ]);

        $modePerJam = $request->has('mode_per_jam') ? $request->boolean('mode_per_jam') : true;
        $stylist = \App\Models\Stylist::firstOrCreate(
            ['nama' => trim($validated['kapster'])],
            ['status' => 'aktif']
        );

        if ($modePerJam) {
            $start = \Carbon\Carbon::createFromFormat('H:i', $validated['jam_mulai']);
            $end = \Carbon\Carbon::createFromFormat('H:i', $validated['jam_selesai']);

            $countCreated = 0;
            $current = $start->copy();

            while ($current->lt($end)) {
                $slotStart = $current->format('H:i:s');
                $next = $current->copy()->addHour();
                if ($next->gt($end)) {
                    $next = $end->copy();
                }
                $slotEnd = $next->format('H:i:s');

                // Hindari duplikat jika slot sudah ada
                $exists = \App\Models\Slot::where('tanggal', $validated['tanggal'])
                    ->where('stylist_id', $stylist->id)
                    ->where('jam_mulai', $slotStart)
                    ->exists();

                if (!$exists) {
                    \App\Models\Slot::create([
                        'tanggal' => $validated['tanggal'],
                        'jam_mulai' => $slotStart,
                        'jam_selesai' => $slotEnd,
                        'kapster' => $validated['kapster'],
                        'stylist_id' => $stylist->id,
                        'kuota' => $validated['kuota'],
                        'terisi' => 0,
                        'status' => $validated['status'],
                    ]);
                    $countCreated++;
                }

                $current->addHour();
            }

            return redirect()->route('admin.slots.index')->with('success', ($countCreated > 0 ? $countCreated : 0) . ' Slot harian berhasil ditambahkan otomatis per jam.');
        }

        $validated['stylist_id'] = $stylist->id;
        \App\Models\Slot::create($validated);
        return redirect()->route('admin.slots.index')->with('success', 'Slot berhasil ditambahkan.');
    }

    public function show(string $id)
    {
        //
    }

    public function edit(string $id)
    {
        $slot = \App\Models\Slot::findOrFail($id);
        return view('admin.slots.edit', compact('slot'));
    }

    public function update(Request $request, string $id)
    {
        $slot = \App\Models\Slot::findOrFail($id);
        if ($slot->reservedBookings()->whereNotIn('status', ['dibatalkan'])->exists()) {
            return back()->with('error', 'Slot yang sedang digunakan booking tidak dapat diubah.');
        }

        $validated = $request->validate([
            'tanggal' => 'required|date',
            'jam_mulai' => 'required|date_format:H:i',
            'jam_selesai' => 'required|date_format:H:i|after:jam_mulai',
            'kapster' => 'required|string|max:100',
            'kuota' => 'required|integer|min:1',
            'status' => 'required|in:tersedia,penuh,tutup',
        ]);

        $stylist = \App\Models\Stylist::firstOrCreate(
            ['nama' => trim($validated['kapster'])],
            ['status' => 'aktif']
        );
        $validated['stylist_id'] = $stylist->id;

        $slot->update($validated);
        return redirect()->route('admin.slots.index')->with('success', 'Slot berhasil diperbarui');
    }

    public function destroy(string $id)
    {
        $slot = \App\Models\Slot::findOrFail($id);
        if ($slot->reservedBookings()->whereNotIn('status', ['dibatalkan'])->exists()) {
            return back()->with('error', 'Slot yang sedang digunakan booking tidak dapat dihapus.');
        }

        $slot->delete();
        return redirect()->route('admin.slots.index')->with('success', 'Slot berhasil dihapus');
    }

    public function bulkGenerate(Request $request)
    {
        $validated = $request->validate([
            'tanggal' => 'required|date',
            'jam_mulai' => 'required|date_format:H:i',
            'jam_selesai' => 'required|date_format:H:i|after:jam_mulai',
            'kapster' => 'required|string|max:100',
            'kuota' => 'required|integer|min:1',
        ]);

        $start = \Carbon\Carbon::createFromFormat('H:i', $validated['jam_mulai']);
        $end = \Carbon\Carbon::createFromFormat('H:i', $validated['jam_selesai']);
        $stylist = \App\Models\Stylist::firstOrCreate(
            ['nama' => trim($validated['kapster'])],
            ['status' => 'aktif']
        );

        $countCreated = 0;
        $date = \Carbon\Carbon::parse($validated['tanggal_mulai'])->startOfDay();
        $lastDate = \Carbon\Carbon::parse($validated['tanggal_selesai'])->startOfDay();

        while ($date->lte($lastDate)) {
            $current = $start->copy();

            while ($current->lt($end)) {
                $slotStart = $current->format('H:i:s');
                $next = $current->copy()->addHour();
                if ($next->gt($end)) {
                    $next = $end->copy();
                }
                $slotEnd = $next->format('H:i:s');

                $exists = \App\Models\Slot::where('tanggal', $date->toDateString())
                    ->where('stylist_id', $stylist->id)
                    ->where('jam_mulai', $slotStart)
                    ->exists();

                if (! $exists) {
                    \App\Models\Slot::create([
                        'tanggal' => $date->toDateString(),
                        'jam_mulai' => $slotStart,
                        'jam_selesai' => $slotEnd,
                        'kapster' => $validated['kapster'],
                        'stylist_id' => $stylist->id,
                        'kuota' => $validated['kuota'],
                        'terisi' => 0,
                        'status' => 'tersedia',
                    ]);
                    $countCreated++;
                }

                $current->addHour();
            }

            $date->addDay();
        }

        return redirect()->route('admin.slots.index')->with('success', "Berhasil membuat {$countCreated} slot untuk periode {$validated['tanggal_mulai']} sampai {$validated['tanggal_selesai']} untuk stylist {$validated['kapster']}.");
    }
}

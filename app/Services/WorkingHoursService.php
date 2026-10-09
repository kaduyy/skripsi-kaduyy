<?php

namespace App\Services;

use App\Models\Slot;
use App\Models\Stylist;
use Carbon\Carbon;

class WorkingHoursService
{
    public function ensureDefaultSlots(Carbon $from, Carbon $to): void
    {
        $stylists = Stylist::where('status', 'aktif')->get();

        for ($date = $from->copy()->startOfDay(); $date->lte($to->copy()->startOfDay()); $date->addDay()) {
            foreach ($stylists as $stylist) {
                for ($hour = 7; $hour < 20; $hour++) {
                    Slot::firstOrCreate(
                        [
                            'tanggal' => $date->toDateString(),
                            'stylist_id' => $stylist->id,
                            'jam_mulai' => sprintf('%02d:00:00', $hour),
                        ],
                        [
                            'jam_selesai' => sprintf('%02d:00:00', $hour + 1),
                            'kapster' => $stylist->nama,
                            'kuota' => 1,
                            'terisi' => 0,
                            'status' => 'tersedia',
                        ]
                    );
                }
            }
        }
    }
}

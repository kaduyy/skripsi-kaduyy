<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Slot;
use App\Models\Stylist;

class SlotSeeder extends Seeder
{
    public function run(): void
    {
        $stylistNames = ['Peter Parker', 'Liam Parker', 'Daniel Brooks', 'James Miller'];
        
        // Generate for today and next 14 days
        for ($day = 0; $day <= 14; $day++) {
            $targetDate = now()->addDays($day)->toDateString();
            
            foreach ($stylistNames as $stylistName) {
                $stylist = Stylist::firstOrCreate(
                    ['nama' => $stylistName],
                    ['status' => 'aktif']
                );

                for ($hour = 9; $hour < 22; $hour++) {
                    $start = sprintf('%02d:00:00', $hour);
                    $end = sprintf('%02d:00:00', $hour + 1);

                    Slot::firstOrCreate(
                        [
                            'tanggal' => $targetDate,
                            'stylist_id' => $stylist->id,
                            'jam_mulai' => $start,
                        ],
                        [
                            'jam_selesai' => $end,
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

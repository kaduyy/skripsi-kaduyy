<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::firstOrCreate(
            ['email' => 'admin@alansalon.com'],
            [
                'name' => 'Admin Salon',
                'role' => 'admin',
                'no_whatsapp' => '628123456789',
                'password' => bcrypt('password'),
                'email_verified_at' => now(),
            ]
        );

        $this->call([
            ServiceSeeder::class,
            GallerySeeder::class,
            SlotSeeder::class,
        ]);
    }
}

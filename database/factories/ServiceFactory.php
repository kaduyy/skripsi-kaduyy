<?php

namespace Database\Factories;

use App\Models\Service;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Service>
 */
class ServiceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $kategori = $this->faker->randomElement(['Potong', 'Warna', 'Treatment', 'Styling']);
        
        $names = [
            'Potong' => ['Potong Rambut Pria', 'Potong Rambut Wanita', 'Trim Poni & Layer'],
            'Warna' => ['Pewarnaan Basic Full Color', 'Highlight & Balayage Premium', 'Fashion Color Bleaching'],
            'Treatment' => ['Creambath & Head Spa', 'Keratin Smooth Treatment', 'Scalp Detox Care'],
            'Styling' => ['Wash & Blow Dry Natural', 'Catok & Wave Express Styling', 'Hair Styling Event'],
        ];

        return [
            'nama_layanan' => $this->faker->randomElement($names[$kategori]),
            'kategori' => $kategori,
            'deskripsi' => 'Perawatan dan layanan profesional untuk kecantikan dan kesehatan rambut Anda di Alan Salon.',
            'harga' => $this->faker->numberBetween(5, 45) * 10000,
            'durasi_menit' => $this->faker->randomElement([30, 45, 60, 90, 120]),
            'status' => 'aktif',
        ];
    }
}

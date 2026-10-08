<?php

namespace Database\Seeders;

use App\Models\Service;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $services = [
            [
                'nama_layanan_lama' => 'Creambath & Head Spa Relaxation',
                'nama_layanan' => 'Creambath',
                'kategori' => 'Treatment',
                'deskripsi' => 'Perawatan rambut dan kulit kepala menggunakan krim khusus untuk membantu menjaga kelembapan, menutrisi rambut, dan memberikan sensasi rileks.',
                'harga' => 80000,
                'durasi_menit' => 60,
                'status' => 'aktif',
            ],
            [
                'nama_layanan_lama' => 'Pewarnaan Basic (Full Color)',
                'nama_layanan' => 'Hair Coloring',
                'kategori' => 'Warna',
                'deskripsi' => 'Pewarnaan rambut dengan berbagai pilihan warna yang dapat disesuaikan dengan karakter, gaya, dan keinginan Anda.',
                'harga' => 150000,
                'durasi_menit' => 90,
                'status' => 'aktif',
            ],
            [
                'nama_layanan_lama' => 'Wash, Blow Dry & Natural Style',
                'nama_layanan' => 'Hair Styling',
                'kategori' => 'Styling',
                'deskripsi' => 'Penataan rambut untuk kebutuhan sehari-hari maupun acara spesial, disesuaikan dengan gaya dan bentuk rambut Anda.',
                'harga' => 70000,
                'durasi_menit' => 45,
                'status' => 'aktif',
            ],
        ];

        DB::transaction(function () use ($services) {
            Service::query()->update(['status' => 'nonaktif']);

            foreach ($services as $serviceData) {
                $legacyName = $serviceData['nama_layanan_lama'];
                unset($serviceData['nama_layanan_lama']);

                $service = Service::query()
                    ->where('nama_layanan', $legacyName)
                    ->first();

                if (! $service) {
                    $service = Service::query()->firstOrNew([
                        'nama_layanan' => $serviceData['nama_layanan'],
                    ]);
                }

                $service->fill($serviceData);
                $service->save();
            }
        });
    }
}

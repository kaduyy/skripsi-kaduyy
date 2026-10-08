<?php

namespace Database\Seeders;

use App\Models\Gallery;
use App\Models\Service;
use Illuminate\Database\Seeder;
use RuntimeException;

class GallerySeeder extends Seeder
{
    public function run(): void
    {
        $entries = [
            [
                'file_path' => 'galleries/7YwyaSCMlFYfUEYtiyPDk5YzWdn1MGCTaNNui1KT.jpg',
                'judul' => 'Pewarnaan rambut hijau toska',
                'service' => 'Pewarnaan Basic (Full Color)',
            ],
            [
                'file_path' => 'galleries/SApwiihhHzMvYzTczZIEiJ6vwSdlPfCNxrYlWdmT.jpg',
                'judul' => 'Potongan rambut pria',
                'service' => 'Potong Rambut Pria (Gentleman Cut)',
            ],
            [
                'file_path' => 'galleries/fQWlzhal35ks44gqA1Gki3lCQwc4Bd5Y0aQKk0NO.jpg',
                'judul' => 'Potongan rambut anak',
                'service' => 'Potong Rambut Pria (Gentleman Cut)',
            ],
            [
                'file_path' => 'galleries/gNZG7soRaAUWOJLB3BvmHxR4YgTcupRX21oxoEDY.jpg',
                'judul' => 'Braids dengan gaya fade',
                'service' => 'Potong Rambut Pria (Gentleman Cut)',
            ],
            [
                'file_path' => 'galleries/wtjO4r5C2oAIfUx79NuMhusvO8rUijanJYt3LtsJ.jpg',
                'judul' => 'Rambut panjang berlayer',
                'service' => 'Potong Rambut Wanita (Ladies Layer Cut)',
            ],
        ];

        foreach ($entries as $entry) {
            $service = Service::where('nama_layanan', $entry['service'])->first();

            if (!$service) {
                throw new RuntimeException("Layanan galeri tidak ditemukan: {$entry['service']}");
            }

            if (!is_file(storage_path('app/public/' . $entry['file_path']))) {
                throw new RuntimeException("File foto galeri tidak ditemukan: {$entry['file_path']}");
            }

            Gallery::updateOrCreate(
                ['file_path' => $entry['file_path']],
                [
                    'service_id' => $service->id,
                    'judul' => $entry['judul'],
                ]
            );
        }
    }
}

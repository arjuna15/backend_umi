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

        \App\Models\News::insert([
            [
                'title' => 'Penerimaan Mahasiswa Baru Semester Gasal 2025/2026 Resmi Dibuka',
                'date' => '8 Juni 2026',
                'image_url' => 'https://umiba.ac.id/wp-content/uploads/2024/05/bannerUMIBA26_1.png',
                'source' => 'kompaskampus.id'
            ],
            [
                'title' => 'Seminar Nasional Teknologi Informasi & Aktuaria 2025',
                'date' => '5 Juni 2026',
                'image_url' => 'https://umiba.ac.id/wp-content/uploads/2024/05/bannerUMIBA26_2.png',
                'source' => 'wartaekonomi.co.id'
            ],
            [
                'title' => 'Mahasiswa UMIBA Raih Juara 1 Kompetisi Nasional 2025',
                'date' => '1 Juni 2026',
                'image_url' => 'https://umiba.ac.id/wp-content/uploads/2024/05/bannerUMIBA26_3.png',
                'source' => 'teropongsenayan.com'
            ],
            [
                'title' => 'Universitas Mitra Bangsa Selenggarakan Gebyar Kemerdekaan HUT-RI Ke-80',
                'date' => '17 Agustus 2025',
                'image_url' => 'https://umiba.ac.id/wp-content/uploads/2025/08/umiba-upacara.jpg',
                'source' => 'newsdetik.co'
            ],
            [
                'title' => 'BEM UMIBA Desak Pemkot Jakarta Selatan Atasi Penumpukan Sampah',
                'date' => '20 Juli 2025',
                'image_url' => 'https://umiba.ac.id/wp-content/uploads/2025/07/pilarparlemen.jpg',
                'source' => 'pilarparlemen.id'
            ],
            [
                'title' => 'Kampus UMIBA Terima Kunjungan Kehormatan DPR RI',
                'date' => '15 Mei 2026',
                'image_url' => 'https://umiba.ac.id/wp-content/uploads/2026/05/audensiUMIBA-300x158.webp',
                'source' => 'kompaskampus.id'
            ]
        ]);

        \App\Models\Testimonial::insert([
            ['image_url' => 'https://umiba.ac.id/wp-content/uploads/2024/05/1-1.png'],
            ['image_url' => 'https://umiba.ac.id/wp-content/uploads/2024/05/2-1.png'],
            ['image_url' => 'https://umiba.ac.id/wp-content/uploads/2024/05/3-1.png'],
            ['image_url' => 'https://umiba.ac.id/wp-content/uploads/2024/05/1-2.png'],
            ['image_url' => 'https://umiba.ac.id/wp-content/uploads/2024/05/2-2.png'],
            ['image_url' => 'https://umiba.ac.id/wp-content/uploads/2024/05/3-2.png'],
        ]);
    }
}

<?php

namespace Database\Seeders;

use App\Models\Hero;
use Illuminate\Database\Seeder;

class HeroSeeder extends Seeder
{
    public function run(): void
    {
        $heroes = [
            ['judul' => 'International Conference 2026', 'deskripsi' => 'Join ARPS members at the upcoming conference in Ankara, Turkey.', 'urutan' => 1],
            ['judul' => 'Latest Journal Publications', 'deskripsi' => 'Explore research from our academic community.', 'urutan' => 2],
            ['judul' => 'Become a Member', 'deskripsi' => 'Free membership open to academics, researchers, and practitioners.', 'urutan' => 3],
            ['judul' => 'Kolaborasi Riset Lintas Disiplin', 'deskripsi' => 'Engineering, sosial, akademik — satu wadah kolaborasi.', 'urutan' => 4],
            ['judul' => 'Inovasi Teknologi untuk Masyarakat', 'deskripsi' => 'Dari IoT lingkungan hingga VR otomotif — karya anggota.', 'urutan' => 5],
            ['judul' => 'Program Pengabdian Berdampak', 'deskripsi' => 'Dampak nyata untuk UMKM, sekolah, dan pesisir.', 'urutan' => 6],
        ];
        foreach ($heroes as $h) {
            Hero::firstOrCreate(['judul' => $h['judul']], $h);
        }
    }
}

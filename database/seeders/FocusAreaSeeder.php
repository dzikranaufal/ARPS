<?php

namespace Database\Seeders;

use App\Models\FocusArea;
use Illuminate\Database\Seeder;

class FocusAreaSeeder extends Seeder
{
    public function run(): void
    {
        $areas = [
            ['judul' => 'Engineering', 'deskripsi' => 'Inovasi teknik dan rekayasa untuk solusi nyata.', 'urutan' => 1],
            ['judul' => 'Sosial', 'deskripsi' => 'Kajian dan program sosial kemasyarakatan.', 'urutan' => 2],
            ['judul' => 'Akademik', 'deskripsi' => 'Pendidikan, kurikulum, dan pengembangan keilmuan.', 'urutan' => 3],
            ['judul' => 'Penelitian', 'deskripsi' => 'Riset kolaboratif lintas disiplin dan institusi.', 'urutan' => 4],
            ['judul' => 'Praktik / Profesional', 'deskripsi' => 'Jembatan antara dunia akademik dan praktik industri.', 'urutan' => 5],
        ];
        foreach ($areas as $a) {
            FocusArea::firstOrCreate(['judul' => $a['judul']], $a);
        }
    }
}

<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $names = ['Akademik', 'Penelitian', 'Praktik/Profesional', 'Engineering', 'Sosial', 'Inovasi Teknologi'];

        foreach ($names as $nama) {
            Category::firstOrCreate(['nama' => $nama]);
        }
    }
}

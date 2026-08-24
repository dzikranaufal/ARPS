<?php

namespace Database\Seeders;

use App\Models\OrganizationProfile;
use Illuminate\Database\Seeder;

class OrganizationProfileSeeder extends Seeder
{
    /**
     * Seed the single-row organization profile (sample data).
     */
    public function run(): void
    {
        OrganizationProfile::create([
            'nama' => 'Academics, Researchers, and Practitioners Society (ARPS)',
            'deskripsi' => 'Perkumpulan akademisi, peneliti, dan praktisi untuk berkolaborasi, berbagi ilmu, serta mengembangkan program berbasis teknologi.',
            'visi' => 'Menjadi wadah kolaborasi yang unggul dalam pengembangan ilmu pengetahuan, teknologi, dan praktik profesional yang berdampak bagi masyarakat.',
            'misi' => "Memfasilitasi kolaborasi riset antara akademisi, peneliti, dan praktisi.\nMenyebarluaskan hasil penelitian dan inovasi melalui publikasi dan program.\nMendorong pengembangan program berbasis teknologi yang relevan dengan kebutuhan masyarakat.",
        ]);
    }
}

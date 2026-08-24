<?php

namespace Database\Seeders;

use App\Enums\JournalStatus;
use App\Models\Journal;
use Illuminate\Database\Seeder;

class JournalSeeder extends Seeder
{
    /**
     * Seed the 4 external reference journals from PRD §3.6 (sample data).
     */
    public function run(): void
    {
        $journals = [
            [
                'nama' => 'Journal of Advanced Research in Fluid Mechanics and Thermal Sciences',
                'slug' => 'fluid-mechanics-thermal-sciences',
                'deskripsi' => 'Jurnal internasional yang mencakup riset mekanika fluida dan ilmu termal — Semarak Ilmu, Malaysia.',
                'e_issn' => '2289-7879',
                'link_eksternal' => 'https://semarakilmu.com.my/journals/index.php/fluid_mechanics_thermal_sciences',
            ],
            [
                'nama' => 'PIJAR',
                'slug' => 'pijar',
                'deskripsi' => 'Jurnal Puspitur — PPI Turki.',
                'e_issn' => '2798-6244',
                'link_eksternal' => 'https://puspitur.org/pijar',
            ],
            [
                'nama' => 'MOTOR: Journal of Automotive Engineering',
                'slug' => 'motor-automotive-engineering',
                'deskripsi' => 'Jurnal teknik otomotif — Universitas Pendidikan Indonesia.',
                'e_issn' => '2807-2287',
                'link_eksternal' => 'https://ejournal.upi.edu/index.php/motor',
            ],
            [
                'nama' => 'ATIKANOTO: Journal of Automotive Engineering Education',
                'slug' => 'atikanoto-automotive-engineering-education',
                'deskripsi' => 'Jurnal pendidikan teknik otomotif — Universitas Pendidikan Indonesia.',
                'e_issn' => '2807-2279',
                'link_eksternal' => 'https://ejournal.upi.edu/index.php/atikanoto',
            ],
            // 8 jurnal dummy tambahan (total 12) — variasi status arsip/aktif
            [
                'nama' => 'ARPS Journal of Engineering & Technology',
                'slug' => 'arps-engineering-technology',
                'deskripsi' => 'Jurnal rekayasa & teknologi terapan ARPS.',
                'e_issn' => '2807-2290',
                'link_eksternal' => 'https://example.com/arps-eng',
            ],
            [
                'nama' => 'ARPS Journal of Social Research',
                'slug' => 'arps-social-research',
                'deskripsi' => 'Jurnal riset sosial, pendidikan, dan kemasyarakatan.',
                'e_issn' => '2807-2291',
                'link_eksternal' => 'https://example.com/arps-social',
            ],
            [
                'nama' => 'Journal of Vocational Education',
                'slug' => 'journal-vocational-education',
                'deskripsi' => 'Publikasi pendidikan vokasi — kolaborasi UPI & politeknik.',
                'e_issn' => '2807-2292',
                'link_eksternal' => 'https://example.com/vocational',
            ],
            [
                'nama' => 'ARPS Review of Applied Research',
                'slug' => 'arps-applied-research',
                'deskripsi' => 'Review riset terapan lintas disiplin.',
                'e_issn' => '2807-2293',
                'link_eksternal' => 'https://example.com/applied',
            ],
            [
                'nama' => 'International Journal of Community Service',
                'slug' => 'ij-community-service',
                'deskripsi' => 'Jurnal pengabdian masyarakat internasional.',
                'e_issn' => '2807-2294',
                'link_eksternal' => 'https://example.com/community',
            ],
            [
                'nama' => 'ARPS Journal of Innovation',
                'slug' => 'arps-innovation',
                'deskripsi' => 'Jurnal inovasi teknologi — arsip 2024.',
                'e_issn' => '2807-2295',
                'link_eksternal' => 'https://example.com/innovation',
                'status' => JournalStatus::Arsip,
            ],
            [
                'nama' => 'Jurnal Pendidikan Teknik Mesin',
                'slug' => 'jptm',
                'deskripsi' => 'Jurnal pendidikan teknik mesin — arsip.',
                'e_issn' => '2807-2296',
                'link_eksternal' => 'https://example.com/jptm',
                'status' => JournalStatus::Arsip,
            ],
            [
                'nama' => 'Journal of Digital Literacy',
                'slug' => 'journal-digital-literacy',
                'deskripsi' => 'Literasi digital & teknologi pendidikan.',
                'e_issn' => '2807-2297',
                'link_eksternal' => 'https://example.com/digital-literacy',
            ],
        ];

        foreach ($journals as $journal) {
            Journal::create([
                'nama' => $journal['nama'],
                'slug' => $journal['slug'],
                'deskripsi' => $journal['deskripsi'],
                'e_issn' => $journal['e_issn'],
                'link_eksternal' => $journal['link_eksternal'],
                'status' => $journal['status'] ?? JournalStatus::Aktif,
            ]);
        }
    }
}

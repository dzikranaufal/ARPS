<?php

namespace Database\Seeders;

use App\Models\OrganizationStructure;
use Illuminate\Database\Seeder;

class OrganizationStructureSeeder extends Seeder
{
    public function run(): void
    {
        $structures = [
            ['nama_pengurus' => 'Prof. Dr. H. Dudung Maolana', 'jabatan' => 'Ketua Umum', 'afiliasi' => 'Universitas Pendidikan Indonesia'],
            ['nama_pengurus' => 'Dr. Siti Nurhayati, M.Pd.', 'jabatan' => 'Sekretaris Jenderal', 'afiliasi' => 'Universitas Negeri Jakarta'],
            ['nama_pengurus' => 'Dr. Ir. Bambang Setiawan, M.T.', 'jabatan' => 'Bendahara Umum', 'afiliasi' => 'Institut Teknologi Bandung'],
            ['nama_pengurus' => 'Prof. Dr. Rina Kartika', 'jabatan' => 'Ketua Bidang Akademik', 'afiliasi' => 'Universitas Gadjah Mada'],
            ['nama_pengurus' => 'Dr. Andi Wijaya Kusuma', 'jabatan' => 'Ketua Bidang Penelitian', 'afiliasi' => 'Universitas Padjadjaran'],
            ['nama_pengurus' => 'Ir. Maya Sari, M.Sc.', 'jabatan' => 'Ketua Bidang Praktik & Industri', 'afiliasi' => 'Praktisi Industri'],
            ['nama_pengurus' => 'Dr. Hendra Gunawan', 'jabatan' => 'Ketua Bidang Engineering', 'afiliasi' => 'Politeknik Negeri Bandung'],
            ['nama_pengurus' => 'Dr. Lilis Handayani', 'jabatan' => 'Ketua Bidang Sosial & PkM', 'afiliasi' => 'Universitas Brawijaya'],
            ['nama_pengurus' => 'Dr. Farhan Alamsyah', 'jabatan' => 'Managing Editor Jurnal', 'afiliasi' => 'Semarak Ilmu Malaysia'],
            ['nama_pengurus' => 'Bella Kusuma, M.Pd.', 'jabatan' => 'Koordinator Publikasi', 'afiliasi' => 'PPI Turki'],
            // 5 dummy tambahan
            ['nama_pengurus' => 'Gilang Ramadhan, S.T., M.Eng.', 'jabatan' => 'Koordinator Teknologi', 'afiliasi' => 'ITS Surabaya'],
            ['nama_pengurus' => 'Dina Marlina, S.Sos., M.Si.', 'jabatan' => 'Koordinator Humas', 'afiliasi' => 'Universitas Airlangga'],
            ['nama_pengurus' => 'Eko Prasetyo, M.Kom.', 'jabatan' => 'Koordinator IT & Data', 'afiliasi' => 'Politeknik Negeri Jakarta'],
            ['nama_pengurus' => 'Fitri Handayani, M.Pd.', 'jabatan' => 'Koordinator Membership', 'afiliasi' => 'Universitas Hasanuddin'],
            ['nama_pengurus' => 'Ahmad Fauzi, M.T.', 'jabatan' => 'Koordinator Kerjasama Internasional', 'afiliasi' => 'Universitas Diponegoro'],
        ];

        foreach ($structures as $s) {
            OrganizationStructure::firstOrCreate(
                ['nama_pengurus' => $s['nama_pengurus'], 'jabatan' => $s['jabatan']],
                $s
            );
        }
    }
}

<?php

namespace Database\Seeders;

use App\Enums\PublicationCategory;
use App\Enums\PublicationStatus;
use App\Models\Publication;
use App\Models\User;
use Illuminate\Database\Seeder;

class PublicationSeeder extends Seeder
{
    public function run(): void
    {
        $members = User::where('role', 'member')->pluck('id');
        if ($members->isEmpty()) {
            return;
        }

        $reviewerId = User::whereIn('role', ['superadmin', 'admin_manager'])->value('id');

        $kategoris = ['tulisan', 'prestasi', 'produk', 'pkm'];
        $statuses = [
            // 24 publikasi: 12 approved (tampil publik), 6 pending, 6 rejected — variasi pagination 12/page
            ...array_fill(0, 12, 'approved'),
            ...array_fill(0, 6, 'pending'),
            ...array_fill(0, 6, 'rejected'),
        ];
        shuffle($statuses);

        $judulTemplates = [
            'tulisan' => ['Opini: Masa Depan Pendidikan Vokasi', 'Artikel Populer: Literasi Digital Desa', 'Tulisan Ilmiah: Etika Publikasi', 'Esai: Kolaborasi Riset Lintas Kampus', 'Opini Media: Inovasi Kurikulum Merdeka', 'Kajian: Transformasi Digital UMKM'],
            'prestasi' => ['Juara 1 Lomba Inovasi Mahasiswa Nasional', 'Best Paper Conference ARPS 2025', 'Penghargaan Peneliti Muda Berprestasi', 'Finalis Kompetisi IoT Internasional', 'Juara Harapan PkM Nasional'],
            'produk' => ['Prototipe Smart Irrigation System', 'Aplikasi Bank Sampah Digital v2', 'VR Lab Otomotif: Modul Engine', 'Platform Portfolio Praktisi', 'Alat Monitoring Kualitas Air IoT'],
            'pkm' => ['Laporan PkM: Literasi Finansial UMKM Cirebon', 'PkM Desa Wisata Edukasi', 'Pelatihan Guru SMK: 3D Printing', 'Pendampingan UMKM Go Digital', 'PkM Pesisir: Budidaya Rumput Laut'],
        ];

        // Curated 6 publikasi unggulan (approved) agar halaman publik tidak kosong dengan judul menarik
        $curated = [
            ['judul' => 'Opini: Masa Depan Pendidikan Vokasi di Era AI', 'kategori' => 'tulisan', 'deskripsi' => 'Kajian tentang integrasi AI dalam kurikulum vokasi — peluang dan tantangan.', 'status' => 'approved'],
            ['judul' => 'Juara 1 Lomba Inovasi Mahasiswa Nasional 2025', 'kategori' => 'prestasi', 'deskripsi' => 'Tim ARPS UPI meraih juara 1 dengan inovasi Smart Irrigation.', 'status' => 'approved'],
            ['judul' => 'Prototipe Smart Irrigation System', 'kategori' => 'produk', 'deskripsi' => 'Prototipe irigasi presisi berbasis IoT untuk pertanian lahan kering.', 'status' => 'approved'],
            ['judul' => 'Laporan PkM: Literasi Finansial UMKM Cirebon', 'kategori' => 'pkm', 'deskripsi' => 'Pendampingan 200 UMKM — peningkatan literasi finansial 35%.', 'status' => 'approved'],
            ['judul' => 'Artikel Populer: Literasi Digital Desa', 'kategori' => 'tulisan', 'deskripsi' => 'Strategi literasi digital untuk desa 3T — studi kasus Indramayu.', 'status' => 'pending'],
            ['judul' => 'Aplikasi Bank Sampah Digital v2', 'kategori' => 'produk', 'deskripsi' => 'Update aplikasi dengan fitur insentif token.', 'status' => 'rejected'],
        ];

        foreach ($curated as $c) {
            Publication::create([
                'member_id' => $members->random(),
                'judul' => $c['judul'],
                'deskripsi' => $c['deskripsi'],
                'kategori' => $c['kategori'],
                'file' => null,
                'status' => $c['status'],
                'reviewer_id' => $c['status'] !== 'pending' ? $reviewerId : null,
            ]);
        }

        // 18 dummy tambahan (total 24)
        for ($i = 0; $i < 18; $i++) {
            $kat = fake()->randomElement($kategoris);
            $status = $statuses[$i] ?? fake()->randomElement(['approved', 'pending', 'rejected']);
            $judul = fake()->randomElement($judulTemplates[$kat]) . ' #' . ($i + 1) . ' ' . fake()->words(2, true);
            Publication::create([
                'member_id' => $members->random(),
                'judul' => $judul,
                'deskripsi' => fake()->paragraphs(mt_rand(1, 2), true),
                'kategori' => $kat,
                'file' => null,
                'status' => $status,
                'reviewer_id' => $status !== 'pending' ? $reviewerId : null,
            ]);
        }
    }
}

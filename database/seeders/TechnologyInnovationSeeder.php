<?php

namespace Database\Seeders;

use App\Models\TechnologyInnovation;
use Illuminate\Database\Seeder;

class TechnologyInnovationSeeder extends Seeder
{
    public function run(): void
    {
        $items = [
            ['judul' => 'Platform Kolaborasi Riset Digital', 'deskripsi' => '<p>Pengembangan platform untuk mempertemukan <strong>peneliti dan kolaborasi riset daring</strong> — chat, repositori, dan manajemen proyek.</p>'],
            ['judul' => 'Sistem Monitoring Lingkungan IoT', 'deskripsi' => '<p>Prototipe sensor IoT untuk pemantauan <em>kualitas lingkungan</em> secara real-time.</p><ul><li>Suhu & kelembapan</li><li>Kualitas udara</li></ul>'],
            ['judul' => 'Aplikasi Literasi Digital Masyarakat', 'deskripsi' => '<p>Inisiatif aplikasi edukasi untuk meningkatkan <span style="color: #0d6efd;">literasi digital masyarakat</span>.</p>'],
            ['judul' => 'Drone Pemetaan Pertanian Presisi', 'deskripsi' => '<p>Drone multispektral untuk pemetaan kesehatan tanaman — uji lapangan di Subang.</p>'],
            ['judul' => 'Sistem Manajemen Penelitian (SIM-Pen)', 'deskripsi' => '<p>Dashboard pengelolaan <strong>hibah & luaran riset</strong> terintegrasi SINTA.</p>'],
            ['judul' => 'Bank Sampah Digital', 'deskripsi' => '<p>Aplikasi pelaporan & insentif bank sampah berbasis <a href="https://example.com">IoT timbangan</a>.</p>'],
            ['judul' => 'VR Lab Teknik Otomotif', 'deskripsi' => '<p>Simulasi <em>virtual reality</em> untuk praktik bongkar-pasang mesin.</p>'],
            ['judul' => 'Chatbot Layanan Akademik ARPS', 'deskripsi' => '<p>Chatbot <span style="text-align: center;">AI generatif</span> untuk tanya-jawab keanggotaan & program.</p>'],
            ['judul' => 'E-Portofolio Praktisi Industri', 'deskripsi' => '<p>Portofolio digital untuk memamerkan <strong>produk & prestasi</strong> anggota praktisi.</p>'],
            ['judul' => 'Sistem Deteksi Plagiarisme ARPS', 'deskripsi' => '<p>Integrasi deteksi kemiripan naskah — terhubung repositori publikasi.</p>'],
            ['judul' => 'Platform PkM Terintegrasi', 'deskripsi' => '<p>Manajemen program PkM dari proposal hingga <a href="https://example.com">laporan akhir</a>.</p>'],
            ['judul' => 'Smart Classroom Analytics', 'deskripsi' => '<p>Analitik kehadiran & engagement — arsip 2024.</p>', 'status' => \App\Enums\JournalStatus::Arsip],
        ];

        foreach ($items as $item) {
            TechnologyInnovation::firstOrCreate(['judul' => $item['judul']], array_merge(['status' => \App\Enums\JournalStatus::Aktif], $item));
        }
    }
}

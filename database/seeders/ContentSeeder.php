<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Event;
use App\Models\News;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ContentSeeder extends Seeder
{
    /**
     * Seed sample content (news, events, programs) — sample data only, not final.
     */
    public function run(): void
    {
        // 3 berita utama (curated)
        News::create([
            'judul' => 'ARPS Gelar Seminar Kolaborasi Riset Nasional',
            'isi' => '<p>Seminar tahunan yang mempertemukan <strong>akademisi, peneliti, dan praktisi</strong> untuk membahas arah kolaborasi riset nasional.</p><p>Tahun ini menghadirkan pembicara dari <a href="https://example.com">mitra internasional</a> dengan topik <em>Engineering & Sosial</em>.</p>',
            'tanggal_publish' => now()->subDays(3),
        ]);

        News::create([
            'judul' => 'Penandatanganan MoU dengan Mitra Internasional',
            'isi' => '<p>ARPS menjalin kerja sama dengan mitra luar negeri untuk program <strong>pertukaran peneliti</strong> dan publikasi bersama.</p><ul><li>Pertukaran 10 peneliti/tahun</li><li>Joint publication Scopus</li></ul>',
            'tanggal_publish' => now()->subDays(11),
        ]);

        News::create([
            'judul' => 'Program Pelatihan Teknologi untuk Mahasiswa',
            'isi' => '<p>Pelatihan keterampilan teknologi bagi <span style="color: #0d6efd;">mahasiswa anggota ARPS</span> sebagai bekal memasuki dunia riset dan industri.</p>',
            'tanggal_publish' => now()->subDays(18),
        ]);

        // 12 berita dummy tambahan (total 15) — variasi kaya HTML untuk uji Purifier & pagination
        $dummyNews = [
            'ARPS Luncurkan Beasiswa Riset 2026',
            'Workshop Penulisan Jurnal Scopus Batch 3',
            'Kolaborasi ABBEI–ARPS Tingkatkan Jejaring Peneliti Muda',
            'Webinar Etika Riset & Publikasi Ilmiah',
            'ARPS Goes to Campus: Sosialisasi di 5 PTN',
            'Call for Papers: Conference ARPS 2026',
            'PPI Turki Apresiasi Kolaborasi ARPS',
            'Literasi Digital: Modul Baru untuk Anggota',
            'Riset Produk Mahasiswa Dipamerkan di Bandung',
            'ARPS Terima Hibah Riset DIKTI 2026',
            'Ngobrol Riset #12: AI Generatif di Dunia Akademik',
            'ARPS x Industri: MoU dengan Kawasan Cikarang',
        ];
        foreach ($dummyNews as $i => $judul) {
            News::create([
                'judul' => $judul,
                'isi' => '<p>' . fake()->paragraphs(mt_rand(2, 3), true) . '</p><p><strong>Highlight:</strong> ' . fake()->sentence() . ' <a href="https://example.com/' . ($i+1) . '">selengkapnya</a></p>',
                'tanggal_publish' => now()->subDays(mt_rand(20, 120))->setTime(mt_rand(7, 16), 0),
            ]);
        }

        // 3 event utama
        Event::create([
            'judul' => 'Webinar Nasional: Tren Riset 2026',
            'deskripsi' => '<p>Diskusi perkembangan <strong>riset lintas disiplin</strong> bersama akademisi dan praktisi. Topik: keberlanjutan, transformasi digital, dan kolaborasi.</p>',
            'tanggal_waktu' => now()->addDays(33)->setTime(9, 0),
            'lokasi' => 'Online (Zoom)',
            'info_kontak_pendaftaran' => 'https://wa.me/6281234567890',
        ]);

        Event::create([
            'judul' => 'Kuliah Umum: AI untuk Masyarakat',
            'deskripsi' => '<p>Pemaparan penerapan <em>kecerdasan buatan</em> dalam kehidupan sehari-hari — dari kesehatan, pendidikan, hingga UMKM.</p>',
            'tanggal_waktu' => now()->addDays(48)->setTime(13, 0),
            'lokasi' => 'Universitas Pendidikan Indonesia, Bandung',
        ]);

        Event::create([
            'judul' => 'Kunjungan Industri: Kolaborasi Riset',
            'deskripsi' => '<p>Kunjungan untuk menjajaki kerja sama riset dan praktik industri dengan 10 mitra.</p><ul><li>Tour lab manufaktur</li><li>Diskusi MoU</li></ul>',
            'tanggal_waktu' => now()->addDays(60)->setTime(8, 0),
            'lokasi' => 'Kawasan Industri Cikarang',
            'info_kontak_pendaftaran' => 'mailto:events@arps.org',
        ]);

        // 9 event dummy tambahan (total 12) — variasi waktu/lokasi/kontak
        $dummyEvents = [
            ['judul' => 'Conference ARPS 2026: Engineering & Society', 'lokasi' => 'Ankara, Turki', 'kontak' => 'https://conference.arps.org/register'],
            ['judul' => 'Bootcamp IoT & Sensor Lingkungan', 'lokasi' => 'Bandung Tech Hub', 'kontak' => 'https://wa.me/6281234567891'],
            ['judul' => 'Seminar Nasional: PkM Berdampak', 'lokasi' => 'Universitas Gadjah Mada', 'kontak' => 'mailto:pkm@arps.org'],
            ['judul' => 'Workshop Prototipe: 3D Printing untuk Riset', 'lokasi' => 'Politeknik Bandung', 'kontak' => 'https://wa.me/6281234567892'],
            ['judul' => 'Rapat Kerja Tahunan ARPS', 'lokasi' => 'Jakarta', 'kontak' => null],
            ['judul' => 'Webinar: Publikasi di Jurnal Internasional', 'lokasi' => 'Online (YouTube Live)', 'kontak' => 'https://youtube.com/arps'],
            ['judul' => 'Field Trip: Riset Sosial Masyarakat Pesisir', 'lokasi' => 'Indramayu', 'kontak' => 'mailto:research@arps.org'],
            ['judul' => 'Expo Teknologi Mahasiswa ARPS 2026', 'lokasi' => 'UPI, Bandung', 'kontak' => 'https://expo.arps.org'],
            ['judul' => 'Pelatihan Manajemen Proyek Riset', 'lokasi' => 'Online (Zoom)', 'kontak' => null],
        ];
        foreach ($dummyEvents as $i => $ev) {
            Event::create([
                'judul' => $ev['judul'],
                'deskripsi' => '<p>' . fake()->paragraphs(mt_rand(1, 2), true) . '</p>',
                'tanggal_waktu' => now()->addDays(mt_rand(5, 90))->setTime(mt_rand(8, 15), [0, 30][array_rand([0, 1])]),
                'lokasi' => $ev['lokasi'],
                'info_kontak_pendaftaran' => $ev['kontak'],
            ]);
        }

        $map = [
            'akademik' => 'Akademik',
            'penelitian' => 'Penelitian',
            'praktik' => 'Praktik/Profesional',
            'engineering' => 'Engineering',
            'sosial' => 'Sosial',
            'inovasi' => 'Inovasi Teknologi',
        ];

        $programs = [
            ['judul' => 'Program Beasiswa & Pendampingan Akademik', 'kategori' => 'akademik', 'deskripsi' => 'Mendukung anggota dalam pengembangan akademik melalui beasiswa dan mentoring.'],
            ['judul' => 'Program Hibah Riset Kolaboratif', 'kategori' => 'penelitian', 'deskripsi' => 'Fasilitasi pendanaan dan kolaborasi riset lintas institusi.'],
            ['judul' => 'Program Magang & Jejaring Profesional', 'kategori' => 'praktik', 'deskripsi' => 'Menghubungkan anggota dengan peluang magang dan industri.'],
            ['judul' => 'Program Inovasi Teknik & Prototipe', 'kategori' => 'engineering', 'deskripsi' => 'Pengembangan prototipe dan solusi rekayasa untuk kebutuhan nyata.'],
            ['judul' => 'Program Pengabdian Masyarakat', 'kategori' => 'sosial', 'deskripsi' => 'Kegiatan sosial dan pengabdian yang berdampak bagi masyarakat.'],
            ['judul' => 'Program Inkubasi Teknologi', 'kategori' => 'inovasi', 'deskripsi' => 'Inkubasi gagasan dan produk teknologi dari anggota.'],
        ];

        foreach ($programs as $program) {
            $kategoriId = Category::where('nama', $map[$program['kategori']])->value('id');

            DB::table('programs')->insert([
                'judul' => $program['judul'],
                'deskripsi' => $program['deskripsi'],
                'kategori_id' => $kategoriId,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // 12 program dummy tambahan (total 18) — 2 per kategori, deskripsi kaya HTML
        $dummyPrograms = [
            ['judul' => 'Riset Aksi Sosial Pesisir', 'kategori' => 'sosial', 'deskripsi' => '<p>Riset partisipatif untuk pemberdayaan masyarakat pesisir melalui <strong>literasi lingkungan</strong>.</p>'],
            ['judul' => 'Laboratorium Inovasi Pembelajaran', 'kategori' => 'akademik', 'deskripsi' => '<p>Pengembangan <em>model pembelajaran</em> berbasis proyek untuk kurikulum merdeka.</p><ul><li>Modul 1: STEM</li><li>Modul 2: Literasi digital</li></ul>'],
            ['judul' => 'Jejaring Riset AI & Data', 'kategori' => 'penelitian', 'deskripsi' => '<p>Kolaborasi 5 kampus untuk riset <strong>AI generatif</strong> bidang pendidikan.</p>'],
            ['judul' => 'Klinik Prototipe Mahasiswa', 'kategori' => 'engineering', 'deskripsi' => '<p>Pendampingan prototipe dari ide hingga <a href="https://example.com">pilot industri</a>.</p>'],
            ['judul' => 'Magang Riset Industri 2026', 'kategori' => 'praktik', 'deskripsi' => '<p>Penempatan 50 mahasiswa di <span style="color: #198754;">mitra industri</span> selama 6 bulan.</p>'],
            ['judul' => 'Inkubator Startup Teknologi Sosial', 'kategori' => 'inovasi', 'deskripsi' => '<p>Inkubasi 20 ide teknologi sosial — mentoring & seed funding.</p>'],
            ['judul' => 'Konferensi Pendidikan Vokasional', 'kategori' => 'akademik', 'deskripsi' => '<p>Konferensi tahunan pendidikan vokasi dengan 300 peserta.</p>'],
            ['judul' => 'Hibah Riset Kolaboratif Internasional', 'kategori' => 'penelitian', 'deskripsi' => '<p>Hibah joint-research ARPS–Semarak Ilmu Malaysia senilai 500jt.</p>'],
            ['judul' => 'Sertifikasi Kompetensi Praktisi', 'kategori' => 'praktik', 'deskripsi' => '<p>Skema sertifikasi praktisi industri — <strong>bersertifikat BNSP</strong>.</p>'],
            ['judul' => 'Desain Sistem Energi Terbarukan', 'kategori' => 'engineering', 'deskripsi' => '<p>Riset panel surya low-cost untuk desa terpencil.</p>'],
            ['judul' => 'Pengabdian: Literasi Finansial UMKM', 'kategori' => 'sosial', 'deskripsi' => '<p>Edukasi literasi finansial untuk 200 UMKM binaan.</p>'],
            ['judul' => 'Demo Day Teknologi ARPS', 'kategori' => 'inovasi', 'deskripsi' => '<p>Pameran 30 produk teknologi anggota — <a href="https://demo.arps.org">demo.arps.org</a></p>'],
        ];
        foreach ($dummyPrograms as $p) {
            $kategoriId = Category::where('nama', $map[$p['kategori']])->value('id');
            DB::table('programs')->insert([
                'judul' => $p['judul'],
                'deskripsi' => $p['deskripsi'],
                'kategori_id' => $kategoriId,
                'created_at' => now()->subDays(mt_rand(1, 90)),
                'updated_at' => now(),
            ]);
        }
    }
}

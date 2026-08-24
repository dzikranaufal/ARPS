# Task — Fase 7: Polish & Launch

**Lingkup**: penyempurnaan terakhir sebelum production — SEO, responsive, test, checklist deploy, backup.
**Tujuan**: aplikasi siap production sesuai NFR PRD §8 dan checklist risiko `risiko.md` E1–E8.
**Cara pakai**: kerjakan subtask **secara berurutan** (7.0 → 7.7). Selesaikan satu, verifikasi acceptance-nya, baru lanjut.

> **Keputusan yang sudah diambil (rekomendasi yang disetujui)**:
> - SEO: meta title/description per halaman via `@section('meta')` di layout; heading terstruktur; URL deskriptif sudah ada dari awal.
> - Test: PHPUnit/Pest — hanya kontrak penting (auth, role, approval, validasi upload, kepemilikan/IDOR). Bukan test dangkal/plumbing.
> - Deploy: checklist tertulis (debug off, config:cache, storage:link, backup, maintenance, permission, composer --no-dev).
> - Backup: prosedur DB **+** `storage/app/public`, dengan uji restore.

---

## Pranala Dokumen (WAJIB dibaca berurutan sebelum mulai)

1. `context/PRD.md` — requirement (fokus §8 NFR)
2. `context/glossary.md` — istilah baku
3. `context/architecture.md` — struktur, routing, layout
4. `context/directory.md` — peta file/route/controller/view
5. `context/schema.md` — acuan skema
6. `context/risiko.md` — **wajib**: seluruh checklist (A–F), khususnya E1–E8
7. `notes/progress.md` — status progres
8. `context/rules.md` — aturan perilaku

---

## Aturan Fase Ini (wajib)

- **JANGAN** mengubah logika fungsional kecuali untuk memperbaiki bug yang ditemukan saat verifikasi.
- **Risiko wajib**: seluruh daftar `risiko.md` adalah checklist — fase ini adalah audit terakhir terhadapnya.
- **Verifikasi**: setiap subtask, jalankan acceptance check.

---

## Subtask 7.0 — SEO (meta, heading, robots/sitemap)

**Tindakan**:
1. **Layout** (`layouts/app.blade.php`, `layouts/admin.blade.php`, `layouts/member.blade.php`, `layouts/journal-site.blade.php`):
   - Pastikan `<title>` memakai `@yield('title')` (sudah).
   - Tambahkan block `@yield('meta')` di `<head>` — tiap halaman publik bisa mendefinisikan `<meta name="description">` & Open Graph dasar (title, description, og:type).
2. **Halaman publik** (home, about, organization, programs, technology-innovation, journals, publications, news, events, register, login, contact): tambahkan `@section('meta')` dengan description unik per halaman (1–2 kalimat).
3. **Heading terstruktur**: audit setiap halaman publik — satu `<h1>` per halaman, hierarki h2/h3 benar (PRD §8.4).
4. **robots.txt** (`public/robots.txt`): pastikan ada & wajar (boleh `User-agent: * / Allow: /`); **jangan blokir** halaman publik.
5. **Sitemap**: buat `routes/web.php` route `GET /sitemap.xml` sederhana (XML statis berisi URL utama — home, about, organization, programs, technology-innovation, journals, publications, news, events, contact, register, login). Tandai komentar bahwa ini bisa diganti generator sitemap jika volume halaman bertambah.

**Acceptance**:
- Setiap halaman publik punya `<title>` unik + `<meta name="description">`.
- Satu `<h1>` per halaman; hierarki heading valid.
- `/sitemap.xml` & `/robots.txt` bisa diakses; URL publik tercantum.

---

## Subtask 7.1 — Responsive Audit

**Tindakan**:
1. Buka semua halaman publik + admin di browser dengan viewport: **desktop (1366px), tablet (768px), mobile (375px)**.
2. Perbaiki yang rusak:
   - Navbar publik collapse mobile berfungsi.
   - Grid kartu (programs, journals, publications, organization) menumpuk benar di mobile.
   - Tabel admin: pastikan `table-responsive` (sudah) atau scroll horizontal.
   - Form member/admin tidak meluber.
3. JANGAN mengubah desain keseluruhan — hanya perbaikan responsivitas.

**Acceptance**:
- Semua halaman tampil benar di 3 viewport; tidak ada elemen terpotong/overlap.
- Navigasi mobile berfungsi.

---

## Subtask 7.2 — Test (kontrak penting)

**Konteks**: test WAJIB melindungi kontrak yang bisa rusak (bukan plumbing). Gunakan PHPUnit (sudah terpasang) atau Pest bila sudah ada.

**Tindakan** — tulis test untuk:
1. **Auth**:
   - Register member → DB role `member`, status `aktif`, auto-login.
   - Login salah password 5× → rate limited (429).
   - Member nonaktif → login ditolak.
2. **Role middleware** (A1):
   - Member akses `/admin` → 403 (atau redirect sesuai implementasi).
   - Admin akses `/admin` → 200.
   - Admin Manager akses `/admin/admin-users` → 403 (Fase 6).
3. **Publications approval** (C2):
   - Member upload → status `pending`.
   - Admin approve → `approved` + `reviewer_id` terisi.
   - Approve dua kali (simulasi race: panggil dua kali) → hanya pertama berhasil.
   - `/publications` publik hanya menampilkan `approved`.
4. **Upload validasi** (B1):
   - Upload `.txt` / file >10MB → ditolak.
5. **Kepemilikan/IDOR** (A3):
   - Member A tidak bisa mengunduh/lihat karya member B.
6. **Profil member** (A2):
   - Update profil tidak mengubah `role`/`status`/`email`.

**Acceptance**:
- `php artisan test` (atau `vendor/bin/phpunit`) — semua test **hijau**.
- Test benar-benar gagal jika kontrak dilanggar (bukan test kosong).

---

## Subtask 7.3 — Audit Risiko Akhir

**Tindakan**: jalankan checklist `context/risiko.md` baris per baris terhadap kode:
1. A1–A9: cek middleware semua grup admin/member; fillable semua model; tidak ada `{!! !!}` untuk input user; CSRF semua form; rate limit login.
2. B1–B5: semua upload (gambar modul, foto member, file publications) — mime asli, whitelist, limit, random filename, hapus file lama.
3. C1–C6: unique constraint (email, slug, kategori); update kondisional approval; transaksi multi-step; timezone WIB.
4. D1–D5: tidak ada N+1 di list; semua list pagination; index kolom difilter; branding pakai cache bila memungkinkan.
5. E1–E8: checklist deploy (7.4) & backup (7.5).
6. F1–F3: direktori publik tanpa email/telepon; register tanpa verifikasi (mitigasi rate limit); slug unik.

**Acceptance**:
- Tidak ada pelanggaran tersisa; setiap temuan diperbaiki (atau dicatat + dilaporkan jika butuh keputusan).

---

## Subtask 7.4 — Checklist Deploy

**Tindakan**: tulis file **`notes/deploy-checklist.md`** (baru) berisi langkah deploy production:

1. Backup DB sebelum migrate (`mysqldump`).
2. `.env` production: `APP_ENV=production`, `APP_DEBUG=false`, `APP_URL=https://...`, DB credentials aman, `APP_TIMEZONE` tidak perlu (config app).
3. `composer install --no-dev --optimize-autoloader`.
4. `php artisan migrate --force`.
5. `php artisan storage:link`.
6. `php artisan config:cache` + `route:cache` + `view:cache` (setelah semua perubahan).
7. `npm ci && npm run build` (asset Vite).
8. Permission: `storage/` & `bootstrap/cache/` writable (shared hosting).
9. `php artisan down` sebelum maintenance besar; `up` setelah selesai.
10. Seeder admin: pastikan password diganti/kuat (risiko E3).
11. HTTPS: pastikan SSL aktif & `APP_URL` https; `.htaccess` proteksi `storage/logs` bila perlu.
12. Test smoke pasca-deploy: buka halaman publik, login admin, upload file.

**Acceptance**:
- File `notes/deploy-checklist.md` ada, lengkap, urut, executable.
- Checklist mencakup E1–E8.

---

## Subtask 7.5 — Prosedur Backup

**Tindakan**: tulis di `notes/deploy-checklist.md` (atau file terpisah `notes/backup-procedure.md`):
1. Backup DB: `mysqldump` berkala (cron di hosting) — termasuk struktur + data.
2. Backup storage: folder `storage/app/public` (upload publications, gambar, foto) — **jangan pernah cuma DB** (risiko E5).
3. Frekuensi: harian (atau sesuai kebijakan hosting).
4. **Uji restore**: prosedur restore + verifikasi data & file (jadwalkan uji berkala).
5. Simpan backup di lokasi berbeda dari server (object storage/off-site bila memungkinkan).

**Acceptance**:
- Prosedur backup + restore tertulis; mencakup DB + storage.

---

## Subtask 7.6 — Verifikasi Final & Smoke Test

**Tindakan**:
1. `php artisan migrate:fresh --seed` + `php artisan test` — hijau.
2. Smoke test lengkap via browser (mode production-ish: `APP_DEBUG=false` lokal):
   - Publik: semua halaman (home, about, organization, programs, technology-innovation, journals, publications, news, events, register, login, contact).
   - Register → dashboard member → upload publikasi → logout.
   - Login admin → approval → publik tampil.
   - Responsive spot-check (mobile).
   - `/sitemap.xml`, `/robots.txt`.
3. Cek log (`storage/logs/laravel.log`) — tidak ada error/warning baru.

**Acceptance**:
- Semua halaman & alur inti berfungsi; test hijau; log bersih.

---

## Subtask 7.7 — Sinkronkan Dokumentasi & Tutup Fase

**Tindakan**:
1. `notes/progress.md`: tandai Fase 7 selesai — semua modul 🟢; catat item yang masih `[TBD]`/placeholder (jika ada).
2. `context/directory.md`: tambah file baru (sitemap, test, docs) bila relevan.
3. `notes/task.md`: update daftar fase — semua fase ditandai selesai.
4. Laporan akhir ke user: ringkasan apa yang selesai, apa yang tersisa (placeholder/STATIC SAMPLE yang disengaja), dan rekomendasi sebelum deploy.

**Acceptance**:
- Semua dokumentasi mencerminkan kondisi final.
- Laporan akhir disampaikan.

---

## Checklist Akhir (sebelum menyatakan Fase 7 selesai)

- [ ] 7.0–7.7 semua selesai & acceptance terpenuhi.
- [ ] SEO: meta description + satu h1 per halaman + sitemap/robots.
- [ ] Responsive: desktop/tablet/mobile.
- [ ] Test hijau; melindungi kontrak penting.
- [ ] Audit risiko A–F: tidak ada pelanggaran tersisa.
- [ ] Deploy checklist & backup procedure tertulis (E1–E8).
- [ ] Smoke test production-ish bersih.
- [ ] Dokumentasi & laporan akhir.

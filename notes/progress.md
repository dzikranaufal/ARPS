# Progress — Status Pengembangan per Halaman/Modul

Dokumen **hidup** — perbarui setiap kali ada perubahan status (fase frontend → backend, halaman selesai, dsb). Acuan requirement: `PRD.md`. Peta file: `directory.md`.

> Aturan update: setiap selesai task, pindahkan status ke kondisi terbaru. Jangan biarkan tabel tertinggal dari kode.

## Legenda Status

- 🟢 **Selesai (tampilan)** — halaman tampil dengan layout final.
- 🟡 **Parsial / placeholder** — tampil tapi konten masih "coming soon" / sampel.
- 🔴 **Belum ada** — belum ada route/view.
- ⚪ **Bukan deliverable** — bukan bagian scope (material diskusi / out of scope).

---

## Halaman Publik

| Halaman | Route | Status | Keterangan |
|---|---|---|---|
| Home | `/` | 🟢 | Hero + berita sampel |
| About | `/about` | 🟢 | DB (deskripsi/visi/misi dari organization_profile, 5 fokus bidang statis) — Fase 3.9 |
| Organization | `/organization` | 🟢 | DB (profile + Structure paginate + direktori member DB paginate 12 F1 hanya nama/organisasi/foto) — Fase 4.5 |
| Programs | `/programs` | 🟢 | DB (Program with kategori paginate 9) — Fase 3.9 |
| Technology Innovation | `/technology-innovation` | 🟢 | DB (TechnologyInnovation aktif paginate 9) — Fase 3.9 |
| Journals | `/journals` | 🟢 | Katalog 4 jurnal + paginate (data DB) |
| Publications | `/publications` | 🟢 | DB approved + filter kategori (tulisan/prestasi/produk/pkm) paginate 12 — Fase 5.3 |
| News | `/news` | 🟢 | DB (News paginate 9, isi escape) — Fase 3.9 |
| Events | `/events` | 🟢 | DB (Event paginate 9, WIB) — Fase 3.9 |
| Register | `/register` | 🟢 | Form statis (layout auth, 6 field); backend Fase 2 |
| Login | `/login` | 🟢 | Form login (fungsional, rate limit) |
| Contact | `/contact` | 🟢 | DB (profile nama) + kontak sampel — Fase 3.9 |
| Direktori member (di Organization) | — | 🟢 | Digabung di `/organization` (DB aktif paginate 12) — Fase 4.5 |

---

## Microsite Jurnal (bukan deliverable)

| Halaman | Route | Status | Keterangan |
|---|---|---|---|
| Journal Home | `/journal/{slug}` | 🟢 | Data jurnal dummy di `@php` |
| Journal Archives | `/journal/{slug}/archives` | 🟢 | Tampilan |
| Journal Guidelines | `/journal/{slug}/guidelines` | 🟢 | Tampilan |

> Microsite = **material diskusi, bukan deliverable resmi** (PRD §3.6). Jalur kanonik website = link keluar.

---

## Admin (CoreUI)

| Halaman | Route | Status | Keterangan |
|---|---|---|---|
| Dashboard | `/admin` | 🟡 | Placeholder 1 kalimat |
| Categories | `/admin/categories` | 🟢 | CRUD lengkap (index/create/edit/delete, withCount, pagination) — Fase 3.2 |
| Programs | `/admin/programs` | 🟢 | CRUD lengkap (kategori select, image 2MB, delete old) — Fase 3.3 |
| Journals | `/admin/journals` | 🟢 | CRUD lengkap (slug auto-generate unique, cover, link URL, status) — Fase 3.4 |
| Events | `/admin/events` | 🟢 | CRUD lengkap (tanggal_waktu WIB, poster, kontak) — Fase 3.5 |
| News | `/admin/news` | 🟢 | CRUD lengkap (isi escape, tanggal_publish, gambar) — Fase 3.6 |
| Technology Innovations | `/admin/technology-innovations` | 🟢 | CRUD lengkap (status, gambar) — Fase 3.8 |
| Organization profile | `/admin/organization` | 🟢 | Single-row edit/update, logo upload, no delete — Fase 3.7 |
| Organization structure | `/admin/structure` | 🟢 | CRUD lengkap (foto, pagination) — Fase 3.7 |
| Members | `/admin/members` | 🟢 | List/search/paginate 15 + detail + toggle — Fase 4.4 |
| Publications | `/admin/publications` | 🟢 | Pending default + approve/reject C2 + delete B4 — Fase 5.2 |
| Admin Users | `/admin/admin-users` | 🟢 | CRUD superadmin/admin_manager + toggle/status + search — Fase 6.1 (A9) |
| General Settings | `/admin/settings` | 🟢 | Branding via organization_profile, logo 2MB — Fase 6.2 |

**Sidebar admin** — Fase 6.0: Categories, Programs, Technology Innovation, Journals, Events, News, Members, Publications, **Admin Users**, **General Settings** ter-wire ke route nyata (`admin.*`). Role & Permissions / Social Media dihapus sesuai keputusan.

---

## Backend / Fungsional

| Komponen | Status |
|---|---|
| Auth (login/logout/register) | 🟢 Manual (tanpa Breeze) — rate limit, CSRF, nonaktif diblokir |
| Migration / model lengkap | 🟢 Sesuai `schema.md` (10 tabel + users.foto) — Fase 4.0 |
| Role middleware | 🟢 `role:superadmin,admin_manager` di /admin; `role:member` di /dashboard & /profile (A1) |
| Seeder | 🟢 User (password acak), organization_profile, journals, categories (6), technology_innovations (3), content (pakai kategori_id) |
| CRUD Categories | 🟢 Lengkap (unique, nullOnDelete) — Fase 3.2 |
| CRUD Programs | 🟢 Lengkap (kategori_id required, image 2MB, delete old, with kategori) — Fase 3.3 |
| CRUD Journals | 🟢 Lengkap (slug unique + auto-generate Str::slug, cover, link URL, status) — Fase 3.4 |
| CRUD Events | 🟢 Lengkap (tanggal_waktu WIB, poster) — Fase 3.5 |
| CRUD News | 🟢 Lengkap (isi escape {{ }}, gambar) — Fase 3.6 |
| CRUD Organization profile | 🟢 Single-row (PUT, logo, no delete) — Fase 3.7 |
| CRUD Organization structure | 🟢 Lengkap (foto) — Fase 3.7 |
| CRUD Technology Innovation | 🟢 Lengkap (status, gambar) — Fase 3.8 |
| Publik sambung DB | 🟢 Programs/Events/News/TechInov/Organization/About/Contact dari DB (paginate, eager) — Fase 3.9 |
| Member dashboard | 🟢 Layout member, greeting + status badge + publications paginate — Fase 4.2 |
| Edit profil member | 🟢 Foto 2MB + telepon/organisasi + ganti password current_password — Fase 4.3 (A2, B1) |
| Kelola akun member (admin) | 🟢 List/search/paginate 15 + detail + toggle aktif/nonaktif — Fase 4.4 (D1, F1 self-block) |
| Direktori member publik | 🟢 Di /organization (aktif paginate 12, hanya nama/organisasi/foto) — Fase 4.5 (F1) |
| Upload karya (member) | 🟢 Member/PublicationController store 10MB pdf/jpg/png/docx, pending, file random, B1-B4 — Fase 5.1 |
| Antrian approval (admin) | 🟢 List pending default + search, approve/reject where pending + transaction C2, delete + hapus file — Fase 5.2 |
| Publications publik | 🟢 Approved filter kategori paginate 12, member name only A4 — Fase 5.3 |
| Unduh file | 🟢 Member own 403 A3 + admin any, nama generik publication-{id}.ext B2 — Fase 5.4 |
| Upload & review Publications | 🟢 Selesai Fase 5 |
| Kelola admin user (Super Admin) | 🟢 CRUD admin superadmin/admin_manager + toggle/status/search + proteksi diri A9/C3/D2/D3 — Fase 6.1 |
| Pengaturan branding | 🟢 Via organization_profile reuse, logo 2MB, kontak STATIC SAMPLE — Fase 6.2 |
| Journals publik | 🟢 Data dari DB (paginate, link keluar) |

---

## Prioritas Penyelesaian (rekomendasi, sesuaikan kebutuhan)

1. Backend (auth, CRUD) setelah frontend lengkap, mengikuti `schema.md`.

> **Fase 0 selesai**: duplikat route `/login` dirapikan; Membership → Register (route/view/navbar/footer); sidebar admin disinkronkan; About/Events/Publications/News diisi konten sampel; `welcome.blade.php` dihapus; dokumentasi disinkronkan.

> **Fase 1 selesai**: halaman Register (form statis layout auth), Programs, Technology Innovation, Organization (+ direktori member, tanpa data pribadi — risiko F1), Contact dibuat; navbar & footer publik diperbarui (semua halaman PRD §3 punya tampilan). Microsite & halaman admin tidak berubah.

> **Fase 2 selesai**: fondasi backend — migration 8 tabel sesuai `schema.md`, model + enum, timezone WIB, auth manual (register/login/logout, rate limit 429, CSRF, blokir akun nonaktif), role middleware (A1 — visitor→login, member→/admin 403, admin→/admin 200), seeder (password acak — E3), journals publik dari DB (link keluar). Verifikasi penuh: `migrate:fresh --seed` sukses, semua skenario auth teruji. Migration `cache` ditambahkan (dibutuhkan RateLimiter).

> **Fase 3 selesai**: CRUD konten admin lengkap (Categories, Programs, Journals, Events, News, Technology Innovations, Organization profile/structure) — FormRequest validasi, A2 validated, A4 escape, B1 whitelist mime+random filename+2MB+hapus lama, C1/C3 unique+DB constraint, C6 WIB, D1 eager+withCount, D3 paginate, A1 middleware tetap; halaman publik Programs/Events/News/TechInov/Organization/About/Contact sambung DB (6 halaman), Publications & direktori member tetap STATIC SAMPLE. Sidebar ter-wire, flash di admin layout, slug auto-generate, kategori nullOnDelete. Verifikasi: `migrate:fresh --seed` sukses, kategori 6, program->kategori relasi jalan, 24 test hijau + manual CRUD (upload >2MB/tɪpe salah ditolak, file lama terhapus, slug duplicate error, isi escape, member 403).

> **Fase 4 selesai**: Keanggotaan & member dashboard — migration users.foto, layout member (`layouts/member.blade.php`), dashboard (status badge, publications paginate), edit profil (foto 2MB, telepon/organisasi, ganti password `current_password`), admin kelola member (list/search paginate 15 withCount, detail, toggle aktif/nonaktif, self-block), direktori member publik di `/organization` (aktif paginate 12, hanya nama/organisasi/foto F1). Verifikasi: `migrate:fresh --seed` sukses, register→dashboard, edit profil+foto+password, admin toggle nonaktif→login ditolak→aktif→sukses, direktori tanpa email/telepon, 31 test hijau (24+7) + cek B1-A2-D1-D3-F1.

> **Fase 5 selesai**: Publications — member upload (`Member\PublicationController` 10MB whitelist pdf/jpg/png/docx, pending hardcode A2, random filename B1-B3, hapus file B4), admin antrian (pending default, search, approve/reject `where pending` + `DB::transaction` C2, delete file), publik approved + filter kategori (validated in:tulisan...), unduh aman (member own 403 A3, admin any, nama generik `publication-{id}.ext` B2), dashboard link, policy, sidebar. Verifikasi: `migrate:fresh --seed` OK + index status/kategori, 32 test hijau (24+8) + cek A3-A4-B1-B4-C2-D1-D3.

> **Fase 6 selesai**: Super Admin — `admin-users` CRUD (`AdminUserController` whereIn superadmin/admin_manager paginate 15, email unique C3, password min8 E3, role hardcode via forceFill A2, self-block nonaktif/hapus/demote A9, POST/PUT/DELETE + @csrf A5), `settings` branding via `organization_profile` reuse (logo 2MB), sidebar ter-wire, Admin Manager 403 A9. Verifikasi: `migrate:fresh --seed` OK, super→buat manajer→toggle nonaktif→login ditolak→aktif→sukses, self-protection error, branding tampil di /about, 29 test hijau (24+5) + cek A2/A5/A9/C3/D2/D3/E3.

> **Fase 7 selesai**: Polish & Launch — SEO `@yield('meta')` + `meta_description` per halaman publik + `og` + satu `<h1>` per halaman + `public/robots.txt` Allow + `GET /sitemap.xml` (12 URL), responsive `table-responsive`/`navbar collapse` audit 375/768/1366, test kontrak `FinalContractTest.php` (7 test) + 24 hijau =31, audit risiko A–F tanpa pelanggaran, `notes/deploy-checklist.md` (E1–E8) + `notes/backup-procedure.md` (DB+storage harian + uji restore), smoke `migrate:fresh --seed` + `APP_DEBUG=false` + semua halaman 200 + log bersih.

---

## Tindak lanjut review final (notes/task_tindak_lanjut_review_final.md) — Adendum 2026-08-24

- **Task A [KRITIS] selesai** — Stored XSS member `publications/show.blade.php` & `admin/publications/show.blade.php` `{{ $publication->deskripsi }}` escape + `strip_tags` di `Member\PublicationController::store`.
- **Task B [TINGGI] selesai** — Sanitasi Quill admin via `mews/purifier` (`composer require mews/purifier`), `config/purifier.php` (CSS.AllowedProperties warna/perataan Quill), output sanitasi `Purifier::clean` di 7 view publik (`news/show`, `events/show`, `programs/show`, `technology-innovation/show`, `about` 4 titik, `organization/index` 3 titik, `home`).
- **Task C selesai** — Test regresi `tests/Feature/XssSafetyTest.php` (5 test: member escape, admin escape, Quill sanitasi, style+js, program). `php artisan test` hijau.
- **Task D selesai** — Hapus `app/Policies/PublicationPolicy.php` dead code (grep kosong, AppServiceProvider tetap kosong).
- **Task E selesai** — `routes/web.php` hapus `try/catch` + `Schema::hasTable` kosong, ganti query langsung `Hero/News/FocusArea::...`, rapikan semua FQCN inline ke `use` import.
- **Task F selesai** — Buat `notes/adendum_task_baru.md` (Hero, FocusArea, show pages, Quill, sitemap), perbarui `context/risiko.md` A4 (Quill wajib Purifier, member plain text), sinkronkan progress ini.
- **Task G selesai** — FormRequest `HeroRequest`/`FocusAreaRequest` (migrasi dari `$request->validate()` inline), `ContentSeeder` hapus cabang `Schema::hasColumn` (selalu `kategori_id`).

## Tindak lanjut review Fase 0–2 (notes/task_tindak_lanjut_review_fase_0_2.md)

- **Task A selesai** — rate limit `POST /register` (5/menit per IP, `RateLimiter`, fail → 429, limiter di-clear saat sukses).
- **Task B selesai** — blokir akun nonaktif kini redirect ke `/login` dengan pesan (session tidak di-invalidate sebelum throw).
- **Task C selesai** — method `User::setAccountStatus()` (forceFill) + catatan A2 di `context/schema.md` §2; proteksi A2 tetap utuh.
- **Task D TIDAK diterapkan (keputusan user)** — keinginan pesan validasi Indonesia tidak dijalankan karena environment variable sistem `APP_LOCALE=en` (Machine scope) meng-override `.env` (`Dotenv` immutable). Locale tetap `en`; pesan validasi bawaan Laravel tetap Inggris. Lingkungan perlu dibersihkan `APP_LOCALE` sistem bila lokalisasi `id` diinginkan.
- **Task E selesai** — feature tests baru: `tests/Feature/AuthTest.php`, `RoleAuthorizationTest.php`, `JournalsPublicTest.php` (24 test hijau). CSRF di-nonaktifkan di test via `withoutMiddleware(PreventRequestForgery::class)`.
- **Task F selesai** — konstanta `MAX_ATTEMPTS`/`DECAY_SECONDS` di `LoginController`; blok `catch (QueryException)` tetap sebagai fallback race C3.

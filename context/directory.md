# Directory — Peta Proyek ARPS

Inventaris file/route/controller/view proyek ARPS. Proyek dikerjakan dalam fase frontend (tampilan) lalu fungsionalitas. Status per halaman: `notes/progress.md`. Struktur & arsitektur: `architecture.md`.

> Catatan: perbarui file ini bila menambah/mengubah route, controller, atau view.

---

## 1. Root

| Path | Keterangan |
|---|---|
| `artisan` | CLI Laravel |
| `composer.json` / `composer.lock` | Dependensi PHP |
| `package.json` / `.npmrc` | Dependensi JS / build |
| `vite.config.js` | Konfigurasi Vite |
| `phpunit.xml` | Konfigurasi test |
| `.env.example` | Contoh env (tidak ada `.env`) |
| `README.md` | Deskripsi singkat project |
| `context/` | PRD + dokumentasi konteks (folder ini) |

---

## 2. Routes

| File | Isi |
|---|---|
| `routes/web.php` | Semua route web: publik + admin + microsite |
| `routes/console.php` | Route console |

### Route web (`routes/web.php`)

**Publik (tanpa prefix):**
| Route | Name | View / Controller |
|---|---|---|
| `GET /` | `home` | `home` (Hero aktif, News latest 2, Profile) — Task Baru: dynamic heroes/news/about |
| `GET /about` | `about` | `about` (OrganizationProfile + FocusArea) — Task Baru: focus dinamis |
| `GET /programs` | `programs.index` | `programs.index` (Program with kategori paginate) |
| `GET /programs/{program}` | `programs.show` | `programs.show` (detail HTML TinyMCE) — Task Baru |
| `GET /technology-innovation` | `technology-innovation.index` | `technology-innovation.index` |
| `GET /technology-innovation/{innovation}` | `technology-innovation.show` | `technology-innovation.show` — Task Baru |
| `GET /organization` | `organization.index` | `organization.index` (profile + structure + members aktif) |
| `GET /contact` | `contact.index` | `contact.index` |
| `GET /events` | `events.index` | `events.index` |
| `GET /events/{event}` | `events.show` | `events.show` — Task Baru |
| `GET /publications` | `publications.index` | `publications.index` (approved + filter kategori, paginate 12) — Fase 5.3 |
| `GET /publications/{publication}` | `publications.show` | `publications.show` (approved only) — Task Baru |
| `GET /news` | `news.index` | `news.index` |
| `GET /news/{news}` | `news.show` | `news.show` — Task Baru |
| `GET /login` | `login` | `auth.login` |
| `POST /login` | `login.attempt` | — (LoginController@store) |
| `POST /logout` | `logout` | — (LoginController@destroy, auth) |
| `GET /register` | `register` | `register.index` |
| `POST /register` | `register.store` | — (RegisterController@store) |
| `GET /journals` | `journals.index` | `journals.index` (data dari DB) |
| `GET /sitemap.xml` | `sitemap` | XML 12 URL utama — Fase 7.0 |

**Member (auth + role:member):**
| Route | Name | View / Controller |
|---|---|---|
| `GET /dashboard` | `member.dashboard` | `Member\MemberDashboardController@index` → `member.dashboard` (member + publications paginate) |
| `GET /profile` | `member.profile.edit` | `Member\MemberProfileController@edit` → `member.profile` |
| `PUT /profile` | `member.profile.update` | `Member\MemberProfileController@update` (nama/telepon/organisasi/foto/password, A2, B1 2MB) |
| `GET /member/publications` | `member.publications.index` | `Member\PublicationController@index` (own, paginate) — Fase 5.1 |
| `GET /member/publications/create` | `member.publications.create` | `Member\PublicationController@create` — Fase 5.1 |
| `POST /member/publications` | `member.publications.store` | `Member\PublicationController@store` (10MB, mime, pending) — Fase 5.1 |
| `GET /member/publications/{publication}/download` | `member.publications.download` | `Member\PublicationController@download` (A3 own only) — Fase 5.4 |

**Microsite (`/journal/{slug}`):**
| Route | Name | View |
|---|---|---|
| `GET /journal/{slug}` | `journal.home` | `journal-site.home` |
| `GET /journal/{slug}/archives` | `journal.archives` | `journal-site.archives` |
| `GET /journal/{slug}/guidelines` | `journal.guidelines` | `journal-site.guidelines` |

**Admin (`/admin` — resource CRUD, kecuali `organization` single-row):**
| Route | Name | View / Controller |
|---|---|---|
| `GET /admin` | `admin.dashboard` | `admin.dashboard` |
| `resource categories` | `admin.categories.*` | `Admin\CategoryController` + `CategoryRequest` |
| `resource programs` | `admin.programs.*` | `Admin\ProgramController` + `ProgramRequest` |
| `resource journals` | `admin.journals.*` | `Admin\JournalController` + `JournalRequest` |
| `resource events` | `admin.events.*` | `Admin\EventController` + `EventRequest` |
| `resource news` | `admin.news.*` | `Admin\NewsController` + `NewsRequest` |
| `resource technology-innovations` | `admin.technology-innovations.*` | `Admin\TechnologyInnovationController` + `TechnologyInnovationRequest` |
| `resource structure` | `admin.structure.*` | `Admin\OrganizationStructureController` + `OrganizationStructureRequest` |
| `resource heroes` | `admin.heroes.*` | `Admin\HeroController` — Task Baru: hero carousel |
| `resource focus-areas` | `admin.focus-areas.*` | `Admin\FocusAreaController` — Task Baru: about fokus dinamis |
| `GET /admin/members` | `admin.members.index` | `Admin\MemberController@index` (search, paginate 15, withCount) — Fase 4.4 |
| `GET /admin/members/{member}` | `admin.members.show` | `Admin\MemberController@show` (detail + publications) — Fase 4.4 |
| `PUT /admin/members/{member}/status` | `admin.members.update-status` | `Admin\MemberController@updateStatus` (toggle aktif/nonaktif) — Fase 4.4 |
| `GET /admin/publications` | `admin.publications.index` | `Admin\PublicationController@index` (pending default, filter, with member/reviewer) — Fase 5.2 |
| `GET /admin/publications/{publication}` | `admin.publications.show` | `Admin\PublicationController@show` — Fase 5.2 |
| `PUT /admin/publications/{publication}/approve` | `admin.publications.approve` | `Admin\PublicationController@approve` (where pending + transaction C2) — Fase 5.2 |
| `PUT /admin/publications/{publication}/reject` | `admin.publications.reject` | `Admin\PublicationController@reject` (C2) — Fase 5.2 |
| `DELETE /admin/publications/{publication}` | `admin.publications.destroy` | `Admin\PublicationController@destroy` (hapus file B4) — Fase 5.2 |
| `GET /admin/publications/{publication}/download` | `admin.publications.download` | `Admin\PublicationController@download` — Fase 5.4 |
| `GET /admin/organization` | `admin.organization.edit` | `Admin\OrganizationProfileController@edit` |
| `PUT /admin/organization` | `admin.organization.update` | `Admin\OrganizationProfileController@update` |
| `POST /admin/upload/image` | `admin.upload.image` | `Admin\UploadController@image` (TinyMCE 2MB) — Task Baru |

> Catatan: duplikat route `/login` telah dirapikan (Fase 0) — hanya satu definisi di bagian Auth routes.

---

## 3. App / PHP

| Path | Keterangan |
|---|---|
| `app/Models/User.php` | Model `users` (fillable minimal + foto, enum casts role/status, relasi publications, setAccountStatus) — Fase 4.0 foto |
| `app/Models/Program.php`, `Journal.php`, `Event.php`, `Publication.php`, `OrganizationProfile.php`, `OrganizationStructure.php`, `News.php`, `Category.php`, `TechnologyInnovation.php`, `Hero.php`, `FocusArea.php` | Model tabel konten sesuai `schema.md` (Fase 3 + Task Baru) |
| `app/Models/Category.php` | `categories` — relasi hasMany programs |
| `app/Models/TechnologyInnovation.php` | `technology_innovations` — status cast JournalStatus |
| `app/Models/Hero.php` | `heroes` — hero carousel (urutan, status) — Task Baru |
| `app/Models/FocusArea.php` | `focus_areas` — about fokus dinamis — Task Baru |
| `app/Enums/UserRole.php`, `AccountStatus.php`, `PublicationCategory.php`, `PublicationStatus.php`, `JournalStatus.php` | Enum cast |
| `app/Http/Controllers/Controller.php` | Base controller |
| `app/Http/Controllers/Auth/RegisterController.php` | Register (role/status hardcode, risiko A2) |
| `app/Http/Controllers/Auth/LoginController.php` | Login (rate limit A7, redirect by role) + logout POST |
| `app/Http/Middleware/EnsureUserHasRole.php` | Middleware role (alias `role`, risiko A1, null-safe) |
| `app/Http/Controllers/Admin/CategoryController.php`, `ProgramController.php`, `JournalController.php`, `EventController.php`, `NewsController.php`, `OrganizationStructureController.php`, `OrganizationProfileController.php`, `TechnologyInnovationController.php`, `MemberController.php`, `PublicationController.php`, `AdminUserController.php`, `SettingController.php`, `HeroController.php`, `FocusAreaController.php`, `UploadController.php` | CRUD lengkap Fase 3–6 + Task Baru (TinyMCE, heroes, focus-areas) |
| `app/Http/Controllers/Member/MemberDashboardController.php` | Dashboard member (status badge, publications paginate) — Fase 4.2 |
| `app/Http/Controllers/Member/MemberProfileController.php` | Edit profil (foto, password current_password) — Fase 4.3 |
| `app/Http/Controllers/Member/PublicationController.php` | Member upload karya (10MB, pending) + download own — Fase 5.1/5.4 (A3, B1) |
| `app/Policies/PublicationPolicy.php` | Policy A3 (view own vs admin) — Fase 5.1 |
| `app/Http/Requests/Admin/{Category,Program,Journal,Event,News,OrganizationProfile,OrganizationStructure,TechnologyInnovation}Request.php` | FormRequest validasi per modul |
| `app/Providers/AppServiceProvider.php` | |

---

## 3b. Database / Migration & Seeder

| Path | Keterangan |
|---|---|
| `database/migrations/0001_01_01_000000_create_users_table.php` | Tabel `users` (nama/role/telepon/organisasi/status sesuai `schema.md`) + password_reset_tokens |
| `database/migrations/2026_08_22_072307_create_sessions_table.php` | Tabel `sessions` |
| `database/migrations/2026_08_22_072308_create_content_tables.php` | programs, journals, events, publications, organization_profile, organization_structure, news |
| `database/migrations/2026_08_24_082917_create_categories_table.php` | `categories` (nama unique) — Fase 3.0 |
| `database/migrations/2026_08_24_082927_create_technology_innovations_table.php` | `technology_innovations` — Fase 3.0 |
| `database/migrations/2026_08_24_082953_add_kategori_id_to_programs_table.php` | Tambah `kategori_id` FK nullOnDelete + hapus `kategori` string — Fase 3.0 |
| `database/migrations/2026_08_24_084902_drop_kategori_column_from_programs.php` | Drop `kategori` untuk sqlite (fallback) — Fase 3.0 |
| `database/migrations/2026_08_24_085504_add_foto_to_users_table.php` | Tambah `foto` nullable ke users — Fase 4.0 |
| `database/migrations/2026_08_24_091456_add_indexes_to_publications_table.php` | Index `status`,`kategori` — Fase 5.0 (D2) |
| `database/migrations/2026_08_24_105141_create_heroes_table.php` | `heroes` (judul, deskripsi, gambar, link, urutan, status) — Task Baru |
| `database/migrations/2026_08_24_105142_create_focus_areas_table.php` | `focus_areas` (judul, deskripsi, icon, urutan) — Task Baru |
| `database/seeders/DatabaseSeeder.php` | Memanggil semua seeder (urutan: categories sebelum content, + heroes, focus) |
| `database/seeders/UserSeeder.php` | 1 superadmin, 1 admin manager, 8 member (password acak dicetak — E3) |
| `database/seeders/OrganizationProfileSeeder.php` | 1 baris profil organisasi |
| `database/seeders/JournalSeeder.php` | 4 jurnal referensi (PRD §3.6) |
| `database/seeders/CategorySeeder.php` | 6 kategori PRD (Akademik, Penelitian, dll) — Fase 3.0 |
| `database/seeders/TechnologyInnovationSeeder.php` | 3 inovasi teknologi sampel — Fase 3.0 |
| `database/seeders/ContentSeeder.php` | 3 news, 3 events, 6 programs (pakai kategori_id) |
| `database/seeders/HeroSeeder.php` | 3 hero carousel — Task Baru |
| `database/seeders/FocusAreaSeeder.php` | 5 fokus bidang — Task Baru |

---

## 4. Views — Layouts

| Path | Keterangan |
|---|---|
| `resources/views/layouts/app.blade.php` | Layout situs publik |
| `resources/views/layouts/admin.blade.php` | Layout admin (CoreUI + TinyMCE `tinymce.min.js` + upload `admin.upload.image`) — Task Baru |
| `resources/views/layouts/auth.blade.php` | Layout auth (login/register) |
| `resources/views/layouts/member.blade.php` | Layout member (header ringan + logout POST) — Fase 4.1 |
| `resources/views/layouts/journal-site.blade.php` | Layout microsite jurnal |

---

## 5. Views — Partials

| Path | Keterangan |
|---|---|
| `partials/sidebar.blade.php` | Sidebar admin (sebagian link `href="#"`) |
| `partials/header.blade.php` | Header admin |
| `partials/footer.blade.php` | Footer admin |
| `partials/public/navbar.blade.php` | Navbar situs publik |
| `partials/public/footer.blade.php` | Footer situs publik |
| `partials/journal-site/header.blade.php` | Header microsite |

---

## 6. Views — Halaman

| Path | Status konten |
|---|---|
| `home.blade.php` | **DB** (Hero aktif order urutan + News latest 2 + Profile) — Task Baru |
| `about.blade.php` | **DB** (visi/misi/deskripsi + FocusArea dinamis) — Task Baru |
| `programs/index.blade.php` | **DB** (with kategori paginate, klik → show) — Task Baru |
| `programs/show.blade.php` | **DB** (detail HTML TinyMCE, kategori, gambar) — Task Baru |
| `technology-innovation/index.blade.php` | **DB** (aktif paginate, klik → show) — Task Baru |
| `technology-innovation/show.blade.php` | **DB** (detail HTML) — Task Baru |
| `organization/index.blade.php` | **DB** (profile + Structure paginate, direktori member DB paginate 12) — Fase 4.5 |
| `contact/index.blade.php` | **DB + STATIC SAMPLE** — Fase 3.9 |
| `events/index.blade.php` | **DB** (orderBy tanggal_waktu paginate, klik → show, strip_tags) — Task Baru |
| `events/show.blade.php` | **DB** (detail HTML, poster, kontak) — Task Baru |
| `register/index.blade.php` | **Form statis** |
| `publications/index.blade.php` | **DB** (approved filter kategori, klik → show) — Task Baru |
| `publications/show.blade.php` | **DB** (detail HTML, member, download) — Task Baru |
| `news/index.blade.php` | **DB** (orderByDesc paginate, klik → show, strip_tags) — Task Baru |
| `news/show.blade.php` | **DB** (detail HTML, gambar, sidebar) — Task Baru |
| `journals/index.blade.php` | **Data dari DB** |
| `member/dashboard.blade.php` | **DB** (greeting + status badge + publications paginate + link upload) — Fase 5.1 |
| `member/profile.blade.php` | **DB** (foto, telepon, organisasi, ganti password) — Fase 4.3 |
| `member/publications/index.blade.php` | **DB** (own list) — Fase 5.1 |
| `member/publications/create.blade.php` | Form — Fase 5.1 |
| `admin/members/index.blade.php` | **DB** — Fase 4.4 |
| `admin/members/show.blade.php` | **DB** — Fase 4.4 |
| `admin/publications/index.blade.php` | **DB** — Fase 5.2 |
| `admin/publications/show.blade.php` | **DB** — Fase 5.2 |
| `admin/admin-users/index.blade.php` | **DB** — Fase 6.1 |
| `admin/admin-users/create.blade.php` | Form — Fase 6.1 |
| `admin/admin-users/edit.blade.php` | Form — Fase 6.1 |
| `admin/settings/edit.blade.php` | **DB** — Fase 6.2 |
| `admin/heroes/index.blade.php` | **DB** (gambar, urutan, status) — Task Baru |
| `admin/heroes/create.blade.php` | Form (TinyMCE deskripsi, gambar, link) — Task Baru |
| `admin/heroes/edit.blade.php` | Form — Task Baru |
| `admin/focus-areas/index.blade.php` | **DB** (urutan) — Task Baru |
| `admin/focus-areas/create.blade.php` | Form (TinyMCE) — Task Baru |
| `admin/focus-areas/edit.blade.php` | Form — Task Baru |
| `auth/login.blade.php` | Form login (fungsional) |
| `journal-site/home.blade.php` | **Statis sampel** (data jurnal dummy di `@php`) |
| `journal-site/archives.blade.php` | Tampilan |
| `journal-site/guidelines.blade.php` | Tampilan |
| `admin/dashboard.blade.php` | Placeholder 1 kalimat |
| `admin/categories/index.blade.php` | **DB** (withCount programs paginate, modal delete) — Fase 3.2 |
| `admin/categories/create.blade.php` | Form (validasi unique) — Fase 3.2 |
| `admin/categories/edit.blade.php` | Form — Fase 3.2 |
| `admin/programs/index.blade.php` | **DB** (with kategori paginate) — Fase 3.3 |
| `admin/programs/create.blade.php` | Form (kategori select, image 2MB) — Fase 3.3 |
| `admin/programs/edit.blade.php` | Form (preview + delete old) — Fase 3.3 |
| `admin/journals/index.blade.php` | **DB** (paginate, badge status, cover) — Fase 3.4 |
| `admin/journals/create.blade.php` | Form (slug auto-generate, cover, link URL, status) — Fase 3.4 |
| `admin/journals/edit.blade.php` | Form — Fase 3.4 |
| `admin/events/index.blade.php` | **DB** (paginate, WIB) — Fase 3.5 |
| `admin/events/create.blade.php` | Form (datetime-local, poster) — Fase 3.5 |
| `admin/events/edit.blade.php` | Form — Fase 3.5 |
| `admin/news/index.blade.php` | **DB** (paginate, escape isi) — Fase 3.6 |
| `admin/news/create.blade.php` | Form (isi plain text, tanggal_publish, gambar) — Fase 3.6 |
| `admin/news/edit.blade.php` | Form — Fase 3.6 |
| `admin/technology-innovations/index.blade.php` | **DB** (paginate, status) — Fase 3.8 |
| `admin/technology-innovations/create.blade.php` | Form — Fase 3.8 |
| `admin/technology-innovations/edit.blade.php` | Form — Fase 3.8 |
| `admin/organization/profile.blade.php` | **DB** (single-row, PUT, logo upload, no delete) — Fase 3.7 |
| `admin/organization/structure/index.blade.php` | **DB** (paginate, foto) — Fase 3.7 |
| `admin/organization/structure/create.blade.php` | Form (foto) — Fase 3.7 |
| `admin/organization/structure/edit.blade.php` | Form — Fase 3.7 |

---

## 7. Aset / Vendor

| Path | Keterangan |
|---|---|
| `public/vendors/@coreui/` | CoreUI (icons, coreui bundle) |
| `public/vendors/simplebar/` | SimpleBar |
| `public/vendors/chart.js/` | Chart.js |
| `public/robots.txt` | `Allow: /` + Sitemap — Fase 7.0 |
| `resources/js/app.js`, `resources/css/app.css` | Entry Vite |
| `tests/Feature/FinalContractTest.php` | Kontrak penting (auth, role, approval, upload, IDOR, SEO) — Fase 7.2 |
| `notes/deploy-checklist.md` | Checklist deploy E1–E8 — Fase 7.4 |
| `notes/backup-procedure.md` | Prosedur backup DB+storage + uji restore — Fase 7.5 |

---

## 8. Halaman Publik Lainnya (sesuai PRD §3)

Semua halaman publik PRD §3 sudah punya tampilan (Fase 1): Programs, Technology Innovation, Contact, Register, Organization publik (+ direktori member di dalamnya). Status ketersediaan tiap halaman & modul dilacak di `notes/progress.md`.

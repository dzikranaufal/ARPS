# Adendum — Fitur "Task Baru" (Hero, Focus Areas, Halaman Show, Quill, Sitemap)

Dokumen ini mencatat fitur yang ditambahkan setelah PRD v0.3 dan ditandai `Task Baru` di `directory.md`/`progress.md`, namun belum tercantum di PRD. Status: **disetujui, tetap dipertahankan**, perlu sinkronisasi PRD bila PRD di-revisi berikutnya.

Acuan: `context/PRD.md`, `context/schema.md`, `context/directory.md`, `notes/progress.md`, `context/risiko.md` A4.

---

## 1. Heroes (Carousel Home)

- **Model**: `heroes` (`database/migrations/2026_08_24_105141_create_heroes_table.php`, `app/Models/Hero.php:9`)
  - Kolom: `judul` string, `deskripsi` text nullable (HTML dari Quill), `gambar` string nullable, `link` string nullable, `urutan` integer, `status` enum `aktif|arsip` default `aktif`
  - Cast `status => JournalStatus`
- **Tampil di**: `GET /` (`routes/web.php` home) — `Hero::where(status=aktif)->orderBy(urutan)->get()` → carousel `resources/views/home.blade.php:9` (CoreUI `data-coreui-ride`). Fallback placeholder jika kosong.
- **Admin CRUD**: `App\Http\Controllers\Admin\HeroController` → `admin.heroes.*` (`routes/web.php`), view `admin/heroes/*`, validasi gambar `mimes:jpg,jpeg,png,webp max:2048`, link `url`, urutan `integer`.
- **Seeder**: `HeroSeeder` 3 data.

## 2. Focus Areas (About)

- **Model**: `focus_areas` (`2026_08_24_105142_create_focus_areas_table.php`, `app/Models/FocusArea.php:1`)
  - Kolom: `judul` string, `deskripsi` text nullable (HTML Quill), `icon` string nullable `max:50`, `urutan` integer
- **Tampil di**: `GET /about` (`routes/web.php`) — `FocusArea::orderBy(urutan)->get()` → `resources/views/about.blade.php:69` (`@forelse $focusAreas`). Fallback 5 kartu statis bila kosong.
- **Admin CRUD**: `FocusAreaController` → `admin.focus-areas.*`, validasi `judul required`, `deskripsi nullable`, `icon max:50`.
- **Seeder**: `FocusAreaSeeder` 5 bidang (Engineering, Sosial, Akademik, Penelitian, Praktik/Profesional) sesuai PRD §3.2.

## 3. Halaman Detail / Show

PRD awal hanya menyebut list; detail show ditambahkan untuk UX:

| Route | Controller/View | Catatan |
|---|---|---|
| `GET /programs/{program}` `programs.show` | `resources/views/programs/show.blade.php` | `Program::with(kategori)`, deskripsi HTML Quill (sanitasi via Purifier Task B) |
| `GET /technology-innovation/{innovation}` `technology-innovation.show` | `technology-innovation/show.blade.php` | `where status=aktif else 404` |
| `GET /events/{event}` `events.show` | `events/show.blade.php` | poster, `info_kontak_pendaftaran` |
| `GET /news/{news}` `news.show` | `news/show.blade.php` | `isi` HTML Quill (sanitasi) |
| `GET /publications/{publication}` `publications.show` | `publications/show.blade.php` | `status=approved else 404`, deskripsi **escaped** `{{ }}` (Task A) |
| `GET /journals` `journals.index` | tetap katalog, tidak ada show (sesuai PRD) |  |
| `GET /journal/{slug}` microsite | tetap material diskusi, bukan deliverable PRD §3.6 |  |

Semua show public memakai `layouts.app`, `paginate` di index, dan meta deskripsi `strip_tags`.

## 4. Editor Rich Text Quill

- **Lokasi**: `resources/views/layouts/admin.blade.php:91` — Quill 2 via CDN (`quill.snow.css` + `quill.min.js`), handler upload gambar ke `POST admin.upload.image` (`UploadController@image` max 2MB, whitelist `mimes:jpg,jpeg,png,webp`).
- **Dipakai di**: News `isi`, Programs `deskripsi`, Events `deskripsi`, Journals `deskripsi`, Technology Innovations `deskripsi`, Heroes `deskripsi`, Focus Areas `deskripsi`, Organization Profile `deskripsi/visi/misi` (via `OrganizationProfileRequest`/`SettingController`).
- **Output**: disimpan sebagai HTML mentah di DB. Ditampilkan sanitasi whitelist `Mews\Purifier` saat output (keputusan Task B, Opsi 1 — sanitasi saat output). Konten lama otomatis aman tanpa migrasi.
- **Config Purifier**: `config/purifier.php` — `HTML.Allowed` = `div,p[style],br,span[style],b,strong,i,em,u,s,ul,ol,li,h1-h4[style],a[href|title|target|rel],img[src|alt|width|height|style],blockquote[style],pre,code`; `CSS.AllowedProperties` mengizinkan `color,background-color,font-size,font-family,text-align,line-height,margin,padding,border,width,height` agar warna/perataan Quill tetap tampil; `script/iframe/on*`/`javascript:` otomatis dibuang.
- **Konten member**: TIDAK pakai Quill — `member/publications/create.blade.php` `textarea` polos, outputescaped `{{ }}` + `strip_tags` (Task A).

## 5. Sitemap & Robots

- `GET /sitemap.xml` (`routes/web.php:163`) — 12 URL utama (`/`, `/about`, `/organization`, `/programs`, `/technology-innovation`, `/journals`, `/publications`, `/news`, `/events`, `/contact`, `/register`, `/login`), `Content-Type: text/xml`.
- `public/robots.txt` — `Allow: /` + `Sitemap: /sitemap.xml` (Fase 7.0).

## 6. Status PRD & Rekomendasi

- Bagian 1–5 di atas **belum tercantum di PRD v0.3**. Tidak dianggap pelanggaran `rules.md §3` karena merupakan adendum yang disetujui (instruksi eksplisit via task). 
- **Rekomendasi**: saat PRD naik ke v0.4, masukkan:
  - §3.1 Home: tambahan Heroes carousel
  - §3.2 About: Focus Areas dinamis (ganti "5 fokus bidang statis" → "dinamis via CRUD")
  - §3.4–3.8, §3.7: tambahkan halaman detail/show untuk tiap modul
  - §8.4: catat penggunaan Quill + HTMLPurifier (sanitasi whitelist) sebagai implementasi A4
  - §3.6/§8.4: sitemap & robots sebagai deliverable SEO
- Jika tidak dimasukkan ke PRD, dokumen ini menjadi referensi sah bahwa fitur tersebut **di luar scope PRD tapi disetujui**.

## 7. Perubahan Risiko

- `context/risiko.md` A4 diperbarui: Quill dipakai untuk konten admin → wajib sanitasi whitelist HTMLPurifier; konten member tetap plain text + escape.

---

*Dibuat sebagai tindak lanjut Task F `notes/task_tindak_lanjut_review_final.md`. Tidak ada perubahan perilaku fungsional — dokumen saja.*

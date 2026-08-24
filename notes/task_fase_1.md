# Task — Fase 1: Lengkapi Halaman Publik (frontend)

**Lingkup**: frontend murni. TIDAK ada backend, TIDAK ada perubahan data/DB.
**Tujuan**: melengkapi halaman publik yang belum ada (Register, Programs, Technology Innovation, Organization + direktori member, Contact) agar seluruh halaman PRD §3 punya tampilan.
**Cara pakai**: kerjakan subtask **secara berurutan** (1.1 → 1.8). Selesaikan satu, verifikasi acceptance-nya, baru lanjut.

---

## Pranala Dokumen (WAJIB dibaca berurutan sebelum mulai)

1. `context/PRD.md` — acuan utama requirement & scope (fokus §3.3, §3.4, §3.5, §3.9, §3.10, §3.11)
2. `context/glossary.md` — istilah baku
3. `context/architecture.md` — struktur, routing, layout, batas
4. `context/directory.md` — peta file/route/controller/view
5. `context/schema.md` — skema database (tidak relevan; lewati bila tidak menyentuh DB)
6. `context/risiko.md` — daftar masalah potensial & mitigasi (khusus F1: jangan tampilkan email/telepon member di direktori)
7. `notes/progress.md` — status progres per halaman/modul
8. `context/rules.md` — aturan perilaku

---

## Aturan Fase Ini (wajib)

- **Scope**: frontend murni. JANGAN membuat controller baru, model, migration, atau fungsionalitas apa pun. Route baru hanya mengembalikan `view(...)` via closure.
- **Konten statis**: semua konten baru WAJIB ditandai komentar `{{-- STATIC SAMPLE ... --}}` — placeholder untuk layout, bukan data final.
- **Tema**: halaman publik = `layouts.app` + custom CSS; halaman auth = `layouts.auth`. JANGAN campur `data-bs-*` di area CoreUI.
- **Penamaan route**: publik tanpa prefix (`programs.index`, `technology-innovation.index`, `organization.index`, `contact.index`). Ikuti pola yang sudah ada di `routes/web.php`.
- **Verifikasi**: setiap subtask selesai, jalankan acceptance check yang tercantum.
- **Jangan** mengubah halaman di luar daftar subtask (home, about, events, journals, publications, news, microsite, admin) — kecuali navbar/footer (Subtask 1.6).

---

## Subtask 1.1 — Halaman Register (`/register`)

**Konteks**: route `/register` (name `register.index`) sudah ada dari Fase 0, tapi view masih placeholder. Halaman login memakai layout `layouts/auth.blade.php` — Register harus konsisten.

**Tindakan**:
1. Tulis ulang `resources/views/register/index.blade.php`:
   - Ganti `@extends('layouts.app')` → `@extends('layouts.auth')` (konsisten dengan login).
   - Section `title` = `Register`.
   - Card/panel di tengah (pola serupa `auth/login.blade.php`): heading "Daftar" / "Register".
   - **Form statis (non-fungsional)** dengan field, urut:
     1. `nama` — text, required
     2. `email` — email, required
     3. `telepon` — text, required
     4. `organisasi` — text, **opsional** (placeholder "Organisasi/Lembaga (opsional)")
     5. `password` — password, required
     6. `password_confirmation` — password, required
   - Tombol submit "Daftar" / "Register".
   - Link "Sudah punya akun? Login" → `route('login')`.
   - Komentar `{{-- STATIC SAMPLE --}}` + komentar backend: form action/@csrf akan diisi di Fase 2 (auth).
2. JANGAN menambah validasi JS yang berfungsi — cukup atribut HTML (`required`, `type="email"`, dll) sebagai tampilan.

**Acceptance**:
- Buka `/register` → form 6 field tampil dalam layout auth (bukan navbar publik).
- Form tidak mengirim apa pun (belum ada action) — klik submit tidak error.
- Navbar/footer publik tidak berubah (Register tidak perlu ada di navbar — sudah ada tombol Login; link Register muncul di halaman login nanti di Fase 2, atau cukup link "Sudah punya akun?").

---

## Subtask 1.2 — Halaman Programs (`/programs`)

**Konteks**: PRD §3.4 — Programs dengan kategori: akademik, penelitian, praktik, engineering, sosial, inovasi teknologi. Halaman belum ada.

**Tindakan**:
1. `routes/web.php` — tambah di bagian **Public routes** (setelah route `about`):
   ```php
   Route::get('/programs', function () {
       return view('programs.index');
   })->name('programs.index');
   ```
2. Buat `resources/views/programs/index.blade.php`:
   - `@extends('layouts.app')`, title `Programs`.
   - Container: heading `Programs` + paragraf deskripsi singkat.
   - Grid kartu (`row g-4`, `col-md-6 col-lg-4`) berisi **6 kartu** — satu per kategori: Akademik, Penelitian, Praktik/Profesional, Engineering, Sosial, Inovasi Teknologi.
   - Tiap kartu: badge kategori, judul program sampel, deskripsi singkat sampel, gambar placeholder `[ image ]`.
   - Tandai `{{-- STATIC SAMPLE --}}`.
3. JANGAN membuat route/view lain.

**Acceptance**:
- `/programs` render 6 kartu (semua kategori PRD ada).
- `php artisan route:list` menampilkan `programs.index`.

---

## Subtask 1.3 — Halaman Technology Innovation (`/technology-innovation`)

**Konteks**: PRD §3.5 — highlight inisiatif inovasi teknologi. Halaman belum ada.

**Tindakan**:
1. `routes/web.php` — tambah di bagian **Public routes**:
   ```php
   Route::get('/technology-innovation', function () {
       return view('technology-innovation.index');
   })->name('technology-innovation.index');
   ```
2. Buat folder `resources/views/technology-innovation/` + `index.blade.php`:
   - `@extends('layouts.app')`, title `Technology Innovation`.
   - Heading + deskripsi.
   - Grid kartu **3 inisiatif sampel**: judul, deskripsi, badge status (`[ TBD ]` atau label generik), gambar placeholder.
   - Tandai `{{-- STATIC SAMPLE --}}`.

**Acceptance**:
- `/technology-innovation` render 3 kartu.
- `php artisan route:list` menampilkan `technology-innovation.index`.

---

## Subtask 1.4 — Halaman Organization + Direktori Member (`/organization`)

**Konteks**: PRD §3.3 (struktur pengurus) + §3.10 (direktori member digabung di halaman Organization; tampil nama + institusi + foto, pagination). Halaman belum ada.

**Tindakan**:
1. `routes/web.php` — tambah di bagian **Public routes**:
   ```php
   Route::get('/organization', function () {
       return view('organization.index');
   })->name('organization.index');
   ```
2. Buat folder `resources/views/organization/` + `index.blade.php`:
   - `@extends('layouts.app')`, title `Organization`.
   - **Bagian 1 — Struktur Pengurus**: heading + grid kartu **6 pengurus sampel** (2 baris × 3): nama, jabatan, afiliasi/institusi, foto placeholder `[ foto ]`.
   - **Bagian 2 — Direktori Member**: heading + list/grid **9 member sampel** (nama + institusi + foto placeholder).
     - **Pagination visual non-fungsional**: `nav` dengan link halaman 1/2/3 (`href="#"`), tandai STATIC SAMPLE (backend di Fase 4).
   - **PENTING (risiko F1)**: tampilkan HANYA nama, institusi, foto. JANGAN menampilkan email/telepon/field pribadi lain.
   - Tandai `{{-- STATIC SAMPLE --}}`.

**Acceptance**:
- `/organization` render struktur pengurus (6) + direktori member (9) + pagination visual.
- Tidak ada email/telepon member di halaman.
- `php artisan route:list` menampilkan `organization.index`.

---

## Subtask 1.5 — Halaman Contact (`/contact`)

**Konteks**: PRD §3.11 — info kontak resmi ARPS. Halaman belum ada.

**Tindakan**:
1. `routes/web.php` — tambah di bagian **Public routes**:
   ```php
   Route::get('/contact', function () {
       return view('contact.index');
   })->name('contact.index');
   ```
2. Buat folder `resources/views/contact/` + `index.blade.php`:
   - `@extends('layouts.app')`, title `Contact`.
   - Heading + deskripsi.
   - Informasi kontak sampel (boleh kartu/kolom): **email**, **telepon/WA**, **alamat**, **social media** (link `href="#"` placeholder).
   - Tandai `{{-- STATIC SAMPLE --}}`.

**Acceptance**:
- `/contact` render info kontak.
- `php artisan route:list` menampilkan `contact.index`.

---

## Subtask 1.6 — Perbarui Navbar & Footer Publik

**Konteks**: halaman baru harus bisa dijangkau dari navbar & footer.

**Tindakan**:
1. `resources/views/partials/public/navbar.blade.php`:
   - Tambah link dengan active state (`request()->routeIs(...)`):
     - Programs → `route('programs.index')` (`routeIs('programs.*')`)
     - Organization → `route('organization.index')` (`routeIs('organization.*')`)
     - Technology Innovation → `route('technology-innovation.index')` (`routeIs('technology-innovation.*')`)
     - Contact → `route('contact.index')` (`routeIs('contact.index')`)
   - Susun ulang urutan item agar tidak penuh (prioritas): **Programs, Journals, Publications, News, Events, Organization, About, Contact** — tombol Register/Login di kanan. (Register link: `route('register.index')` — tambahkan tombol "Register" di samping "Login" bila ruang memungkinkan; jika terlalu padat, cukup pertahankan tombol Login dan tambah link Register di halaman login.)
2. `resources/views/partials/public/footer.blade.php`:
   - Quick Links: tambah Programs, Organization, Contact (sesuai ruang).
   - Kolom Contact: isi email/WA/alamat **sampel** (mis. `info@arps.org`, `+62 xxx`, alamat) — tandai STATIC SAMPLE.
3. JANGAN ubah layout, CSS, atau halaman lain.

**Acceptance**:
- Semua link navbar/footer mengarah ke route valid (tidak ada error `Route [...] not defined`).
- Setiap halaman baru bisa dicapai dengan klik dari navbar.
- Active state highlight bekerja saat berada di halaman terkait.

---

## Subtask 1.7 — Verifikasi Fase 1

**Tindakan**:
1. `php artisan route:list` — pastikan ada: `register.index`, `programs.index`, `technology-innovation.index`, `organization.index`, `contact.index`.
2. Jalankan `php artisan serve` dan buka di browser:
   - `/`, `/about`, `/events`, `/programs`, `/technology-innovation`, `/organization`, `/journals`, `/publications`, `/news`, `/register`, `/login`, `/contact`
3. Untuk setiap halaman: render tanpa error; tidak ada `Route [...] not defined`; navbar/footer tampil benar; konten sampel tampil.
4. Klik tiap link navbar — halaman tujuan terbuka.

**Acceptance**:
- Semua URL terbuka tanpa error.
- Tidak ada `route()` error di log.
- 5 halaman baru (register, programs, technology-innovation, organization, contact) menampilkan konten sampel sesuai subtask.

---

## Subtask 1.8 — Sinkronkan Dokumentasi

**Tindakan**:
1. `context/directory.md`:
   - Route publik: tambah `programs.index` (`/programs`), `technology-innovation.index` (`/technology-innovation`), `organization.index` (`/organization`), `contact.index` (`/contact`).
   - Views: tambah `programs/index`, `technology-innovation/index`, `organization/index`, `contact/index`; update `register/index` (layout auth, form).
2. `notes/progress.md`:
   - Halaman publik: Register, Programs, Technology Innovation, Organization (+direktori), Contact → 🟢 (konten sampel).
   - Catatan: navbar/footer diperbarui; microsite tidak berubah.

**Acceptance**:
- `directory.md` mencerminkan kode aktual (5 route baru ada).
- `progress.md` mencatat hasil Fase 1.

---

## Checklist Akhir (sebelum menyatakan Fase 1 selesai)

- [ ] 1.1–1.8 semua selesai & acceptance terpenuhi.
- [ ] Tidak ada kode backend baru (controller/model/migration) ditambahkan — route baru hanya closure `view()`.
- [ ] Halaman di luar daftar subtask tidak diubah (home, about, events, journals, publications, news, microsite, admin tetap asli).
- [ ] Semua konten sampel baru berkomentar `{{-- STATIC SAMPLE ... --}}`.
- [ ] Cek risiko (`context/risiko.md`): direktori member tidak menampilkan email/telepon (F1); tidak ada `{!! !!}`; tidak ada atribut campuran.
- [ ] Dokumentasi (`directory.md`, `progress.md`) sudah disinkronkan.

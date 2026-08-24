# Task — Fase 3: CRUD Modul Konten (Admin Manager)

**Lingkup**: backend CRUD lengkap untuk modul konten admin + sambung halaman publik ke data DB.
**Tujuan**: semua modul konten (Programs, Journals, Events, News, Organization, Technology Innovation) bisa dikelola admin dengan C/R/U/D lengkap, dan tampil di halaman publik dari database.
**Cara pakai**: kerjakan subtask **secara berurutan** (3.0 → 3.10). Selesaikan satu, verifikasi acceptance-nya, baru lanjut.

> **Aturan CRUD (keputusan user)**: SETIAP modul WAJIB memiliki create, read (index/show), update, delete — jangan ada yang tertinggal. Pengecualian eksplisit: `organization_profile` (single-row — tanpa delete).

---

## Pranala Dokumen (WAJIB dibaca berurutan sebelum mulai)

1. `context/PRD.md` — requirement (fokus §3.3–3.6, §3.8, §5.2, §6)
2. `context/glossary.md` — istilah baku
3. `context/architecture.md` — struktur, routing, layout
4. `context/directory.md` — peta file/route/controller/view
5. `context/schema.md` — acuan skema (perlu diupdate di 3.0)
6. `context/risiko.md` — **wajib**: A1, A2, A4, B1–B5, C1, C3, C6, D1–D3
7. `notes/progress.md` — status progres
8. `context/rules.md` — aturan perilaku

---

## Aturan Fase Ini (wajib)

- **CRUD lengkap**: setiap modul wajib C/R/U/D (kecuali organization_profile: single-row tanpa delete).
- **Risiko wajib**:
  - A1: semua route di grup admin (sudah aman dari Fase 2 — jangan keluar dari grup).
  - A2: `$fillable` minimal; gunakan `$request->validated()`; jangan `$request->all()`.
  - A4: tampilan output selalu `{{ }}` — **JANGAN `{!! !!}`** untuk isi/deskripsi (news isi = plain text, tanpa rich text editor).
  - B1–B5: upload aman (mime asli + whitelist + random filename + limit 2MB untuk gambar modul + hapus file lama saat replace/delete).
  - C1/C3: unique constraint di DB (slug jurnal, nama kategori) + tangani duplicate entry.
  - C6: tanggal_waktu tampil WIB.
  - D1/D2/D3: eager loading, pagination di semua list, index kolom yang difilter.
- **Pola seragam**: setiap modul = Controller di `App\Http\Controllers\Admin\` + FormRequest + view index/create/edit + route `admin.<modul>.*` lengkap.
- **Verifikasi**: setiap subtask, jalankan acceptance check.

---

## Subtask 3.0 — Update Schema & Migration: kategori + technology_innovations

**Konteks**: keputusan user — (a) Technology Innovation jadi tabel terpisah (Opsi A), (b) kategori Programs memakai tabel terpisah.

**Tindakan**:
1. **`context/schema.md`** — tambahkan dua entitas baru (dan hapus/ubah kolom `kategori` string pada programs):
   ```
   categories
     id, nama (unique), timestamps

   technology_innovations
     id, judul, deskripsi (nullable), gambar (nullable),
     status (enum aktif|arsip, default aktif), timestamps

   programs  -- kolom kategori (string) DIGANTI:
     kategori_id (FK → categories.id, nullable, nullOnDelete)
   ```
2. **Migration baru**:
   - `create_categories_table` (id, nama string unique, timestamps).
   - `create_technology_innovations_table` (sesuai di atas).
   - **Migration ubah `programs`**: tambah `kategori_id` FK nullable `nullOnDelete`, hapus kolom `kategori` (string). Buat migration terpisah `add_kategori_id_to_programs_table` — jangan edit migration lama (sudah jalan).
3. **Model baru**: `App\Models\Category`, `App\Models\TechnologyInnovation`.
   - `Program`: relasi `kategori()` belongsTo Category; hapus atribut kategori string dari fillable.
   - `Category`: relasi `programs()` hasMany.
4. **Seeder**: `CategorySeeder` — 6 kategori PRD: Akademik, Penelitian, Praktik/Profesional, Engineering, Sosial, Inovasi Teknologi. Update `ProgramSeeder` (jika ada) memakai kategori_id.

**Acceptance**:
- `php artisan migrate` jalan; `schema.md` sinkron dengan migration.
- `php artisan tinker`: `App\Models\Category::count()` = 6; relasi `program->kategori` jalan.

---

## Subtask 3.1 — Pola CRUD Terpusat

**Konteks**: 7 modul admin harus konsisten. Tetapkan pola dulu.

**Tindakan**:
1. **Controller**: tiap modul di `App\Http\Controllers\Admin\{X}Controller` dengan method: `index()`, `create()`, `store()`, `edit($id)`, `update($id)`, `destroy($id)`.
2. **FormRequest**: `App\Http\Requests\Admin\{X}Request` per modul — aturan validasi (lihat subtask masing-masing).
3. **View**: `resources/views/admin/{modul}/index.blade.php` (tabel/list + pagination + tombol edit/delete + flash), `create.blade.php`, `edit.blade.php` (form sama — bisa partial form).
   - Flash: di layout `layouts/admin.blade.php` — tampilkan `session('success')` (alert success) & `session('error')` (alert danger).
   - Delete: **form POST + `@method('DELETE')` + `@csrf`** dalam modal konfirmasi (pola `admin/journals/index.blade.php` yang sudah ada).
4. **Route** (dalam grup `admin` yang sudah ada):
   ```php
   Route::resource('programs', ProgramController::class);
   Route::resource('journals', JournalController::class);
   Route::resource('events', EventController::class);
   Route::resource('news', NewsController::class);
   Route::resource('categories', CategoryController::class);
   Route::resource('technology-innovations', TechnologyInnovationController::class);
   Route::resource('structure', OrganizationStructureController::class);
   Route::get('organization', [OrganizationProfileController::class, 'edit'])->name('organization.edit');
   Route::put('organization', [OrganizationProfileController::class, 'update'])->name('organization.update');
   ```
   - **Catatan**: `admin.journals.*`, `admin.structure.*`, `admin.organization.edit` sudah ada sebagai closure — **ganti** closure dengan controller, jangan duplikat. Perhatikan nama route yang sudah dipakai view: `admin.journals.index/create/edit`, `admin.structure.index/create/edit`, `admin.organization.edit` — route resource `structure` menghasilkan `admin.structure.index/create/store/edit/update/destroy` (cocok).
   - `journals` resource menghasilkan `admin.journals.index/create/store/edit/update/destroy` (cocok dengan view yang ada).
5. **Sidebar** (`partials/sidebar.blade.php`): ganti link `href="#"` modul yang sudah punya route → route nyata (`admin.programs.index`, `admin.technology-innovations.index`, `admin.events.index`, `admin.news.index`, `admin.categories.index` di bawah Content/Programs). Item yang belum ada route (Publications, Members, Users, Settings) tetap `href="#"`.

**Acceptance**:
- `php artisan route:list` — semua route resource admin ada (index/create/store/edit/update/destroy per modul).
- Sidebar menautkan ke route nyata untuk modul yang sudah jadi.
- Flash success/error tampil di halaman admin.

---

## Subtask 3.2 — CRUD Categories

**Konteks**: kategori Programs (tabel baru dari 3.0). CRUD lengkap.

**Tindakan**:
1. `CategoryController` + `CategoryRequest`:
   - Validasi: `nama` required|string|max:100|unique:categories,nama (unique di update: `unique:categories,nama,{id}`).
   - Index: list semua kategori + count programs per kategori (pakai `withCount` — risiko D1) + pagination.
   - Create/Store, Edit/Update, Destroy.
   - Destroy: karena `kategori_id` nullOnDelete, hapus kategori → programs jadi kategori null. Tampilkan konfirmasi "program dengan kategori ini akan kehilangan kategori".
2. View: `admin/categories/{index,create,edit}.blade.php`.

**Acceptance**:
- C/R/U/D kategori jalan; duplicate nama → error validasi (risiko C3).
- Hapus kategori → program terkait tidak error (kategori null).

---

## Subtask 3.3 — CRUD Programs

**Tindakan**:
1. `ProgramController` + `ProgramRequest`:
   - Validasi: `judul` required|string|max:255; `kategori_id` required|exists:categories,id; `deskripsi` nullable; `gambar` nullable|image|mimes:jpg,jpeg,png|max:2048 (**2MB**).
   - Upload: `$file->store('programs', 'public')` (random filename — risiko B1/B2); hapus file lama saat update ganti / destroy (risiko B4).
   - Index: eager load `kategori` + pagination.
2. View: `admin/programs/{index,create,edit}.blade.php` — form: judul, select kategori (dari `categories`), deskripsi textarea, gambar file input + preview file lama.

**Acceptance**:
- C/R/U/D program jalan; upload gambar 2MB valid; file >2MB / tipe salah ditolak.
- File lama terhapus saat diganti/dihapus (cek `storage/app/public/programs`).

---

## Subtask 3.4 — CRUD Journals (ganti stub lama)

**Tindakan**:
1. **Hapus** stub `App\Http\Controllers\Admin\JournalController` lama → ganti `JournalController` nyata + `JournalRequest`:
   - Validasi: `nama` required|string|max:255; `slug` required|string|max:100|unique:journals,slug (unique di update); `deskripsi` nullable; `e_issn` nullable|string|max:20; `cover` nullable|image|mimes:jpg,jpeg,png|max:2048; `link_eksternal` required|url; `status` required|in:aktif,arsip.
   - **Slug auto-generate**: jika field slug kosong di form, generate dari nama (`Str::slug`) — tapi tetap wajib unik; tampilkan error jika duplikat (risiko C1/F3).
   - Upload cover ke `storage/app/public/journals`; hapus cover lama saat replace/delete.
   - Index: pagination; filter status opsional.
2. View: `admin/journals/{index,create,edit}.blade.php` — perbarui yang ada (index sudah ada pola modal delete; create/edit sudah ada form) agar memakai `$journal` data nyata + `@method('DELETE')` form.

**Acceptance**:
- C/R/U/D jurnal jalan; slug unik ter-enforce di DB + validasi; link_eksternal harus URL valid.
- `/journals` publik (dari Fase 2.7) menampilkan data yang di-CRUD admin.

---

## Subtask 3.5 — CRUD Events

**Tindakan**:
1. `EventController` + `EventRequest`:
   - Validasi: `judul` required|string|max:255; `deskripsi` nullable; `tanggal_waktu` required|date (format datetime); `lokasi` nullable|string; `poster` nullable|image|mimes:jpg,jpeg,png|max:2048; `info_kontak_pendaftaran` nullable|string|max:255.
   - Upload poster `storage/app/public/events`; hapus lama saat replace/delete.
   - Index: pagination; tampilkan tanggal_waktu format WIB.
2. View: `admin/events/{index,create,edit}.blade.php`.

**Acceptance**:
- C/R/U/D event jalan; tanggal_waktu tersimpan & ditampilkan konsisten WIB (risiko C6).
- Halaman publik Events (disambung di 3.8) menampilkan data.

---

## Subtask 3.6 — CRUD News

**Tindakan**:
1. `NewsController` + `NewsRequest`:
   - Validasi: `judul` required|string|max:255; `isi` required|string (plain text); `tanggal_publish` required|date; `gambar` nullable|image|mimes:jpg,jpeg,png|max:2048.
   - Upload `storage/app/public/news`; hapus lama saat replace/delete.
   - Index: pagination; urut tanggal_publish desc.
2. View: `admin/news/{index,create,edit}.blade.php`.
3. **Tampilan publik isi**: WAJIB `{{ $news->isi }}` (escape) — JANGAN `{!! !!}` (risiko A4).

**Acceptance**:
- C/R/U/D news jalan; isi plain text tampil ter-escape (input `<script>` tampil sebagai teks).

---

## Subtask 3.7 — CRUD Organization

**Tindakan**:
1. **OrganizationProfile** (single-row — **TANPA delete**):
   - `OrganizationProfileController`: `edit()` (ambil baris pertama; jika kosong, seed/create dulu) + `update()`.
   - Validasi: `nama` required; `deskripsi`, `visi`, `misi` nullable; `logo` nullable|image|mimes:png,jpg,jpeg|max:2048.
   - Ganti closure route `admin.organization` yang ada → controller (`admin.organization.edit` tetap, tambah `admin.organization.update` PUT).
   - Update view `admin/organization/profile.blade.php` yang ada → form `@csrf` + `@method('PUT')` + value dari DB + upload logo (hapus logo lama saat replace).
   - **Tidak ada route destroy/create** untuk profile.
2. **OrganizationStructure** (multi-baris — CRUD lengkap):
   - Ganti `Admin\OrganizationStructureController` (route resource `structure` dari 3.1).
   - Validasi: `nama_pengurus` required; `jabatan` required; `afiliasi` nullable; `foto` nullable|image|mimes:jpg,jpeg,png|max:2048.
   - Upload `storage/app/public/organization`; hapus lama saat replace/delete.
   - View: `admin/organization/structure/{index,create,edit}.blade.php` (perbarui yang ada).
3. Perbarui view `admin/organization/profile.blade.php` yang sudah ada — hapus bagian "STATIC SAMPLE", isi value dari `$organization`.

**Acceptance**:
- Profile: edit + update tersimpan; logo upload; **tidak ada** tombol/route delete profile.
- Structure: C/R/U/D lengkap.
- Halaman publik Organization (3.8) menampilkan data.

---

## Subtask 3.8 — CRUD Technology Innovation

**Tindakan**:
1. `TechnologyInnovationController` + `TechnologyInnovationRequest`:
   - Validasi: `judul` required|string|max:255; `deskripsi` nullable; `gambar` nullable|image|mimes:jpg,jpeg,png|max:2048; `status` required|in:aktif,arsip.
   - Upload `storage/app/public/technology-innovations`; hapus lama saat replace/delete.
   - Index: pagination; filter status opsional.
2. View: `admin/technology-innovations/{index,create,edit}.blade.php`.

**Acceptance**:
- C/R/U/D tech innovation jalan.
- Halaman publik Technology Innovation (3.9) menampilkan data.

---

## Subtask 3.9 — Sambung Halaman Publik ke Data DB

**Konteks**: halaman publik yang modulnya sudah punya CRUD harus menampilkan data DB (Publications tetap STATIC SAMPLE — Fase 5).

**Tindakan** (tiap halaman: ganti closure route → query data; update view; hapus blok STATIC SAMPLE; pakai pagination + eager loading):
1. **`/programs`** (`programs.index`): `$programs = Program::with('kategori')->whereHas('kategori')->orWhereNull('kategori_id')->paginate(...)` — tampil kartu: judul, badge kategori, deskripsi, gambar.
2. **`/events`** (`events.index`): `Event::orderBy('tanggal_waktu')->paginate(...)` — kartu: judul, tanggal (WIB), lokasi, poster, info kontak.
3. **`/news`** (`news.index`): `News::orderByDesc('tanggal_publish')->paginate(...)` — list: judul, tanggal, ringkasan (Str::limit), gambar.
4. **`/technology-innovation`**: `TechnologyInnovation::where('status','aktif')->paginate(...)` — kartu.
5. **`/organization`**: `OrganizationProfile::first()` untuk profil + `OrganizationStructure::paginate(...)` untuk pengurus + member (direktori — **tetap sampel di Fase 4**, karena data member & pagination-nya belum dibangun; pertahankan STATIC SAMPLE untuk bagian direktori saja).
6. **`/contact`**: dari `OrganizationProfile::first()` (nama, email/telepon — tambahkan kolom kontak jika perlu? **JANGAN** — pertahankan statis/teks dari profile yang ada; email/telepon sampel tetap, tandai STATIC SAMPLE).
7. **`/about`**: dari `OrganizationProfile::first()` (deskripsi, visi, misi) + 5 fokus bidang tetap statis (tandai STATIC SAMPLE).

**Acceptance**:
- 6 halaman publik (programs, events, news, technology-innovation, organization, about) menampilkan data dari DB.
- Publications & direktori member tetap STATIC SAMPLE (belum waktunya).
- Semua list berpagination; tidak ada N+1 (eager load).

---

## Subtask 3.10 — Verifikasi Fase 3

**Tindakan**:
1. `php artisan migrate:fresh --seed` — bersih.
2. Login sebagai admin (superadmin/admin_manager), test via browser **setiap modul**:
   - Categories: create → index → edit → delete (C/R/U/D).
   - Programs: C/R/U/D + upload gambar.
   - Journals: C/R/U/D + slug unik + link_eksternal.
   - Events: C/R/U/D + tanggal WIB.
   - News: C/R/U/D + isi ter-escape.
   - Organization: profile edit/update (tanpa delete); structure C/R/U/D + foto.
   - Technology Innovation: C/R/U/D.
3. Verifikasi publik: buat data baru di admin → halaman publik menampilkannya.
4. Checklist risiko:
   - Upload >2MB / tipe salah → ditolak (semua modul).
   - File lama terhapus saat replace/delete.
   - Slug/email/nama kategori duplicate → error, bukan crash.
   - `{!! !!}` tidak dipakai untuk isi user.
   - Semua route admin masih dalam grup middleware (member → 403).

**Acceptance**:
- Semua C/R/U/D terverifikasi berfungsi.
- Tidak ada error di log; semua risiko terpenuhi.

---

## Subtask 3.11 — Sinkronkan Dokumentasi

**Tindakan**:
1. `context/directory.md`: tambah controller (Category, Program, Journal, Event, News, OrganizationProfile, OrganizationStructure, TechnologyInnovation), FormRequest, view admin baru; hapus catatan "JournalController stub".
2. `notes/progress.md`: CRUD modul 🟢; halaman publik programs/events/news/tech-innovation/organization/about 🟢 (data DB); publications & direktori member tetap 🟡.
3. `context/schema.md`: sudah diupdate di 3.0 — pastikan sinkron dengan migration final.

**Acceptance**:
- `directory.md` & `progress.md` mencerminkan kode aktual.

---

## Checklist Akhir (sebelum menyatakan Fase 3 selesai)

- [ ] 3.0–3.11 semua selesai & acceptance terpenuhi.
- [ ] SETIAP modul CRUD lengkap C/R/U/D (kecuali organization_profile — single-row tanpa delete).
- [ ] Upload aman: mime asli, whitelist, random filename, 2MB, hapus file lama (B1–B5).
- [ ] Output ter-escape; tidak ada `{!! !!}` untuk input user (A4).
- [ ] Unique constraint di DB + handling duplicate (C1/C3).
- [ ] Pagination + eager loading + index di semua list (D1–D3).
- [ ] Route admin tetap dalam grup middleware (A1).
- [ ] Dokumentasi sinkron.

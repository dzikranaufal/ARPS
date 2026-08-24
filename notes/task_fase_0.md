# Task — Fase 0: Konsolidasi Frontend

**Lingkup**: frontend murni. TIDAK ada backend, TIDAK ada perubahan data/DB.
**Tujuan**: merapikan fondasi frontend agar konsisten dengan PRD v0.3 sebelum fase berikutnya.
**Cara pakai**: kerjakan subtask **secara berurutan** (0.1 → 0.7). Selesaikan satu, verifikasi acceptance-nya, baru lanjut. Jangan lompat.

---

## Pranala Dokumen (WAJIB dibaca berurutan sebelum mulai)

1. `context/PRD.md` — acuan utama requirement & scope
2. `context/glossary.md` — istilah baku
3. `context/architecture.md` — struktur, routing, layout, batas
4. `context/directory.md` — peta file/route/controller/view
5. `context/schema.md` — skema database (tidak relevan untuk fase ini; lewati bila tidak menyentuh DB)
6. `context/risiko.md` — daftar masalah potensial & mitigasi (cek kode terhadap daftar ini)
7. `notes/progress.md` — status progres per halaman/modul
8. `context/rules.md` — aturan perilaku

---

## Aturan Fase Ini (wajib)

- **Scope**: frontend murni. JANGAN membuat controller baru, model, migration, atau fungsionalitas apa pun.
- **Konten statis**: semua konten baru WAJIB ditandai komentar `{{-- STATIC SAMPLE ... --}}` — placeholder untuk layout, bukan data final.
- **Tema**: situs publik = `layouts.app` + custom CSS; admin = `layouts.admin` (CoreUI). JANGAN campur `data-bs-*` di area CoreUI.
- **Penamaan route**: publik tanpa prefix; admin `admin.*`; microsite `journal.*`. Jangan menambah prefix baru.
- **Verifikasi**: setiap subtask selesai, jalankan acceptance check yang tercantum.
- **Jangan** mengubah isi halaman yang tidak disebut dalam subtask (mis. `home.blade.php`, `journals/index.blade.php`, microsite, admin journals/org) — biarkan apa adanya.

---

## Subtask 0.1 — Rapikan duplikasi route `/login`

**Konteks**: `routes/web.php` mendefinisikan route `GET /login` **dua kali** — sekali di bagian "Auth routes" (atas file) dan sekali di bagian "Public routes" (tengah file).

**Tindakan**:
1. Buka `routes/web.php`.
2. Temukan route `GET /login` di bagian **"Public routes"** (yang berada di antara route `/news` dan `/journals`).
3. **Hapus blok duplikat itu** — pertahankan hanya yang di bagian "Auth routes" (bagian paling atas, `->name('login')`).
4. Jangan mengubah route lain.

**Acceptance**:
- `php artisan route:list` (atau `php artisan route:list --name=login`) menampilkan route `login` **tepat satu baris**.
- Buka `/login` → halaman login tetap tampil tanpa error.

---

## Subtask 0.2 — Sinkronkan terminologi Membership → Register

**Konteks**: PRD v0.3 mengganti "Membership" → "Register" (gratis, tanpa tier, tanpa bayar). Route/view/tautan yang memakai "membership" harus konsisten.

**Tindakan**:
1. **Route**: di `routes/web.php`, ubah
   ```php
   Route::get('/membership', function () { return view('membership.index'); })->name('membership.index');
   ```
   menjadi
   ```php
   Route::get('/register', function () { return view('register.index'); })->name('register.index');
   ```
2. **View**: pindahkan file `resources/views/membership/index.blade.php` → `resources/views/register/index.blade.php` (rename folder/file). Hapus folder `membership/` jika sudah kosong.
3. **Navbar publik** (`resources/views/partials/public/navbar.blade.php`): ubah item menu:
   - Label "Membership" → "Register"
   - `href="{{ route('membership.index') }}"` → `href="{{ route('register.index') }}"`
   - `request()->routeIs('membership.*')` → `request()->routeIs('register.*')`
4. **Footer publik** (`resources/views/partials/public/footer.blade.php`): ubah "Membership" → "Register" dan route-nya.
5. Pastikan tidak ada lagi referensi `membership` di kode (lihat acceptance).

**Acceptance**:
- `grep` untuk `membership` di `routes/` dan `resources/views/` → **nol hasil** (kecuali `partials/sidebar.blade.php` yang memakai kata "Membership" — periksa; jika ada, sesuaikan di Subtask 0.3).
- Buka `/register` → halaman tampil (konten placeholder lama boleh dibiarkan; akan diganti di Fase 1).
- Navbar & footer menampilkan "Register" dan link mengarah ke `/register`.
- Tidak ada error `Route [membership.index] not defined` di halaman mana pun.

---

## Subtask 0.3 — Sinkronkan sidebar admin dengan PRD

**Konteks**: `resources/views/partials/sidebar.blade.php` punya menu yang tidak sesuai PRD: grup "Membership" dengan submenu "Requests" + badge "3" (tidak ada alur request/approval membership), belum ada Programs & Technology Innovation, dan menu Users/Settings tidak ditandai hak Super Admin.

**Tindakan**:
1. **Grup Membership → Members**:
   - Ubah judul grup `Membership` → `Members`.
   - Hapus submenu **"Requests"** beserta badge `3` (tidak ada konsep request).
   - Pertahankan submenu "All Members" (link tetap `#` — halaman dibangun di fase backend).
2. **Tambah modul yang belum ada** di grup "Content Management" (setelah/sekitar Journals):
   - **Programs** (icon `cil-puzzle` atau icon CoreUI lain yang sesuai; link `href="#"`).
   - **Technology Innovation** (icon sesuai; link `href="#"`).
3. **Tandai hak akses**:
   - Grup "Users" (Admin Users, Role & Permissions) → tambahkan teks kecil `(Super Admin)` di judul grup atau di item, mis. "Admin Users (Super Admin)".
   - Grup "Settings" (General Settings, Social Media) → sama, tandai `(Super Admin)`.
4. Pertahankan struktur CoreUI (class `sidebar-nav`, `nav-group`, dll) dan item yang sudah ter-wire (Dashboard, Journals, Organization Profile, Structure) **tanpa perubahan**.

**Acceptance**:
- Sidebar menampilkan: Dashboard, Members (All Members), Content Management (Programs, Technology Innovation, Journals, Events, News, Publications), Organization (Profile, Structure), Users (Admin Users, Role & Permissions) dengan tanda Super Admin, Settings dengan tanda Super Admin.
- Tidak ada "Requests" / badge "3" di mana pun.
- Halaman admin yang sudah ada tetap render tanpa error.

---

## Subtask 0.4 — Ganti placeholder "Content coming soon" jadi konten sampel

**Konteks**: halaman About, Events, Publications, News hanya berisi satu paragraf "Content coming soon". Isi konten sampel statis sesuai struktur PRD §3, ditandai STATIC SAMPLE.

**Tindakan — per halaman (masing-masing wajib pakai `@extends('layouts.app')` dan `@section('title', ...)`)**:

1. **`resources/views/about.blade.php`** — section `About`:
   - Heading `About ARPS`.
   - Paragraf profil singkat ARPS.
   - Sub-bagian "Visi" dan "Misi" (2–3 poin misi).
   - Sub-bagian "Fokus Bidang" berisi 5 item: Engineering, Sosial, Akademik, Penelitian, Praktik/profesional (bisa list-group / grid card).
   - Semua teks = sampel dummy (mis. lorem/deskripsi generik) dengan komentar `{{-- STATIC SAMPLE --}}`.

2. **`resources/views/events/index.blade.php`** — section `Events`:
   - Heading `Events` + deskripsi singkat.
   - Grid kartu (row g-4 / card) berisi **3 event sampel**: judul, tanggal/waktu, lokasi (fisik/online), poster placeholder `[ poster ]`, dan field kontak pendaftaran (opsional, contoh `wa.me/...`).
   - Tandai STATIC SAMPLE.

3. **`resources/views/publications/index.blade.php`** — section `Publications`:
   - Heading `Publications` + deskripsi.
   - **Filter kategori** (visual saja, tombol/select statis): Tulisan, Prestasi, Produk, PkM — belum berfungsi, tandai STATIC SAMPLE.
   - Grid kartu **4 karya sampel** (1 per kategori): judul, kategori (badge), nama penulis, deskripsi singkat.
   - Tandai STATIC SAMPLE.

4. **`resources/views/news/index.blade.php`** — section `News`:
   - Heading `News`.
   - List berita sampel (3 item): judul, tanggal, ringkasan, gambar placeholder `[ image ]`.
   - Tandai STATIC SAMPLE.

**Acceptance**:
- Keempat halaman menampilkan konten sampel sesuai struktur di atas — bukan teks "Content coming soon".
- Setiap blok konten sampel diapit/berisi komentar `{{-- STATIC SAMPLE ... --}}`.
- Semua link `route()` di dalamnya valid (jalankan `php artisan route:list` untuk memastikan nama route yang dipakai ada).
- Tampil rapi di browser (cek minimal 2 halaman).

---

## Subtask 0.5 — Bersihkan aset & konvensi tak terpakai

**Konteks**: `resources/views/welcome.blade.php` adalah halaman default Laravel (Tailwind, 223 baris) yang **tidak dipakai** — route `/` memakai `home.blade.php`.

**Tindakan**:
1. Konfirmasi `welcome.blade.php` tidak dirujuk: `grep -r "welcome" routes/ resources/views/` → hanya hasil di file itu sendiri / tidak ada `view('welcome')`.
2. Jika tidak dirujuk, **hapus** `resources/views/welcome.blade.php`.
3. Periksa `resources/views/` — pastikan semua halaman publik memakai `layouts.app` dan tidak ada sisa file view yatim yang tidak dirujuk route mana pun (selain partial/layout yang di-include).

**Acceptance**:
- `welcome.blade.php` terhapus; tidak ada error `view [welcome] not found` di halaman mana pun (karena memang tidak dipakai).
- Daftar view publik konsisten dengan route di `web.php`.

---

## Subtask 0.6 — Verifikasi Fase 0

**Tindakan**:
1. `php artisan route:list` — periksa semua route valid, route `login` tidak duplikat, ada `register` (bukan `membership`).
2. Jalankan dev server (`php artisan serve`) dan buka di browser:
   - Publik: `/`, `/about`, `/events`, `/register`, `/publications`, `/news`, `/journals`, `/login`
   - Admin: `/admin`, `/admin/journals`, `/admin/organization`, `/admin/structure`
3. Untuk setiap halaman: pastikan render tanpa error, tidak ada `Route [...] not defined`, navbar/footer tampil benar (publik), sidebar tampil benar (admin).

**Acceptance**:
- Semua URL di atas terbuka tanpa error.
- Tidak ada `route()` error di log.
- Sidebar admin: tidak ada "Requests"/badge; menu sesuai PRD.

---

## Subtask 0.7 — Sinkronkan dokumentasi

**Tindakan**:
1. `context/directory.md`: perbarui —
   - Bagian route: hapus baris `membership.index`, tambah `register.index` (`/register`); catatan duplikat `/login` dihapus (sudah dibereskan).
   - Bagian views: ganti `membership/index.blade.php` → `register/index.blade.php`; hapus `welcome.blade.php` dari daftar; update status konten About/Events/Publications/News (kini sampel, bukan "coming soon").
2. `notes/progress.md`: perbarui status Fase 0 — tandai halaman About, Events, Publications, News dari 🟡 "coming soon" → 🟢 "konten sampel"; catat bahwa sidebar admin sudah disinkronkan & route register sudah dibuat.

**Acceptance**:
- `directory.md` mencerminkan kode aktual (tidak ada referensi `membership.index` / `welcome`).
- `progress.md` mencatat hasil Fase 0.

---

## Checklist Akhir (sebelum menyatakan Fase 0 selesai)

- [ ] 0.1–0.7 semua selesai & acceptance terpenuhi.
- [ ] Tidak ada kode backend baru (controller/model/migration) ditambahkan.
- [ ] Tidak ada halaman di luar daftar subtask yang diubah (home, journals, microsite, admin journals/org tetap asli).
- [ ] Semua konten sampel baru berkomentar `{{-- STATIC SAMPLE ... --}}`.
- [ ] Cek risiko (`context/risiko.md`): tidak ada pelanggaran baru (fase ini frontend murni, tapi pastikan tidak ada `{!! !!}`, atribut campuran, atau link rusak).
- [ ] Dokumentasi (`directory.md`, `progress.md`) sudah disinkronkan.

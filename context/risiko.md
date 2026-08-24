# Risiko — Daftar Masalah Potensial & Pencegahan

Daftar masalah teknis yang kemungkinan muncul setelah production, beserta mitigasinya. **Patokan kerja untuk AI worker**: setiap kode yang ditulis WAJIB dicek terhadap daftar ini — apakah kode tersebut menyebabkan salah satu masalah di bawah. Acuan requirement: `PRD.md`. Skema: `schema.md`.

> Aturan: jika task menyentuh area yang ada di daftar ini, worker WAJIB menerapkan mitigasinya **sejak awal** (bukan menunda ke fase polish). Risiko yang bersifat keputusan produk ditandai `[KEPUTUSAN]`.

---

## A. Keamanan & Authorization

### A1. Authorization di server, bukan cuma UI
- **Masalah**: Tombol/menu admin disembunyikan di UI, tapi route `/admin/*` tetap bisa diakses member dengan mengetik URL langsung.
- **Contoh di project**: sidebar admin menyembunyikan menu, tapi route `admin.journals.index` tanpa middleware.
- **Mitigasi**:
  - Middleware role di **route** (`admin.` → hanya superadmin/admin_manager; `member.` → hanya member).
  - Gate/Policy di controller — jangan hanya `@if` di Blade.
  - Semua route admin dalam grup yang dilindungi, bukan per-view.

### A2. Mass Assignment
- **Masalah**: Input dari form langsung di-assign ke model → attacker menyisipkan field tersembunyi (mis. `role=superadmin`).
- **Contoh di project**: form register member; jika pakai `$request->all()` dan `role` ada di fillable, member bisa jadi admin.
- **Mitigasi**:
  - `$fillable`/`$guarded` eksplisit dan **minimal** (hanya field yang boleh diisi user).
  - `role`, `status`, `reviewer_id` TIDAK boleh ada di fillable form publik.
  - Jangan pakai `$request->all()` — pakai `$request->validated()` atau array field eksplisit.

### A3. IDOR (Insecure Direct Object Reference)
- **Masalah**: User mengakses/mengubah data milik orang lain dengan mengganti `id` di URL.
- **Contoh di project**: member edit/delete publications milik member lain (`/publications/{id}/edit`).
- **Mitigasi**: Policy `$publication->member_id === auth()->id()`; cek kepemilikan di controller sebelum aksi.

### A4. XSS (Cross-Site Scripting)
- **Masalah**: Input user/admin ditampilkan tanpa escaping → script berjalan di browser.
- **Contoh di project**: isi berita/deskripsi event/publications.
- **Mitigasi**:
  - Blade `{{ }}` (escape default) — JANGAN `{!! !!}` untuk input user tanpa sanitasi.
  - `[KEPUTUSAN — diperbarui adendum 2026-08-24]`:
    - **Konten admin** (News `isi`, Programs/Events/Journals/Technology Innovations/Heroes/Focus Areas `deskripsi`, Organization `deskripsi/visi/misi`): dipakai editor rich text **Quill** (`layouts/admin.blade.php` `quill-editor`). HTML disimpan mentah, ditampilkan **sanitasi whitelist HTMLPurifier** saat output (`Mews\Purifier::clean` di view publik: `news/show`, `events/show`, `programs/show`, `technology-innovation/show`, `about`, `organization/index`, `home`). Config `config/purifier.php` mengizinkan `p, br, strong, em, ul, ol, li, h1-h4, a, img, blockquote, pre` dan properti CSS Quill `color, background-color, font-size, font-family, text-align, line-height, margin, padding, border, width, height`; `script`, `iframe`, event-handler `on*`, `javascript:` URL otomatis dibuang. **Opsi 1 — sanitasi saat output** (data lama aman tanpa migrasi).
    - **Konten member** (Publications `deskripsi`): tetap **plain text + escape output** (`{{ $publication->deskripsi }}` di `publications/show.blade.php` & `admin/publications/show.blade.php`, plus `strip_tags` defense-in-depth di `Member\PublicationController::store`). Tidak pakai Quill.

### A5. CSRF (Cross-Site Request Forgery)
- **Masalah**: Form/request tanpa token → attacker bisa mengirim aksi atas nama user.
- **Mitigasi**: `@csrf` di semua form POST; jangan nonaktifkan middleware CSRF; API (jika ada) pakai token.

### A6. SQL Injection
- **Masalah**: Input mentah masuk ke query → manipulasi database.
- **Mitigasi**: Eloquent/query builder; hindari `whereRaw`/`DB::raw` dengan input user; bind parameter.

### A7. Rate Limiting
- **Masalah**: Brute force login, spam register, upload berulang.
- **Mitigasi**:
  - `throttle` middleware: login (mis. 5/menit), register, upload.
  - Khusus endpoint admin lebih ketat.

### A8. Email Enumeration
- **Masalah**: Pesan "email sudah terdaftar" mengungkap email valid.
- **Contoh di project**: halaman register.
- **Mitigasi**: pesan generik pada register; tidak membocorkan status eksistensi akun.

### A9. Privilege Escalation (Admin Manager → Super Admin)
- **Masalah**: Admin Manager mengubah role sendiri menjadi superadmin.
- **Mitigasi**: Hanya Super Admin yang punya akses kelola user/role; Admin Manager tidak punya route/aksi itu sama sekali (bukan hanya disembunyikan).

---

## B. Input & Upload File

### B1. Upload file berbahaya
- **Masalah**: File executable/berbahaya ter-upload dan bisa dieksekusi server.
- **Contoh di project**: Publications (PDF/JPG/PNG/DOCX) — publik upload.
- **Mitigasi**:
  - Cek **mime asli** (`finfo`/`getMimeType`), bukan ekstensi.
  - Whitelist ekstensi + whitelist mime.
  - Randomize filename (`$file->store()`), jangan nama asli.
  - Simpan di `storage/` (di luar `public/`), sajikan via route/`Storage::url` — jangan taruh langsung di `public/`.
  - Validasi double-layer: aplikasi `max:10240` KB + `upload_max_filesize`/`post_max_size` server.

### B2. Path Traversal
- **Masalah**: Nama file dari user dipakai di path → akses file lain.
- **Mitigasi**: `$file->store()` menghasilkan nama hash; jangan pernah menggabung nama asli user ke path.

### B3. Ukuran/tipe lolos
- **Masalah**: File besar/tipe tak terduga membebani server.
- **Mitigasi**: validasi aplikasi + limit server (lihat B1).

### B4. File orphan (sampah storage)
- **Masalah**: Upload sukses tapi penyimpanan record gagal → file yatim menumpuk.
- **Contoh di project**: publications/cover/poster.
- **Mitigasi**: simpan file **setelah** semua validasi sukses; hapus file saat record dihapus (event/model hook); jalankan di dalam transaksi yang benar.

### B5. Gambar tanpa optimasi
- **Masalah**: Gambar ukuran besar (cover/poster/foto) ditampilkan mentah → halaman lambat, kuota cepat habis.
- **Mitigasi**: resize/compress saat upload; lazy loading; format modern bila memungkinkan.

---

## C. Data Integrity — Race Condition & Idempotency

### C1. Double Submit (Idempotency)
- **Masalah**: Klik submit 2× → data duplikat (register, publications, event).
- **Mitigasi**:
  - Unique constraint di DB sebagai lapisan terakhir (email, slug).
  - Nonaktifkan tombol setelah submit (JS) + validasi server (jangan andalkan JS saja).
  - Cek-before-insert untuk kasus duplikat (mis. email).

### C2. Race Condition Approval
- **Masalah**: Dua admin approve/reject publikasi bersamaan → status akhir tak tentu.
- **Contoh di project**: antrian approval Publications.
- **Mitigasi**: update kondisional `where('status','pending')` di dalam transaksi; optimistic lock bila perlu.

### C3. Race di Insert
- **Masalah**: Validasi "email unik" di aplikasi lolos dua request bersamaan → duplikat.
- **Mitigasi**: unique constraint DB sebagai lapisan terakhir; tangani exception duplicate entry dengan pesan ramah.

### C4. Operasi Multi-Step Tidak Atomik
- **Masalah**: Sebagian langkah berhasil, sebagian gagal → data tidak konsisten.
- **Contoh di project**: register (buat user + inisialisasi profil), upload publication (file + record).
- **Mitigasi**: `DB::transaction` untuk operasi multi-tabel.

### C5. Soft-Deactivate & Relasi
- **Masalah**: Member dinonaktifkan tapi publikasinya masih tampil / data reviewer hilang.
- **Mitigasi**: kebijakan relasi eksplisit di migration (`nullOnDelete` untuk reviewer_id, keputusan untuk member_id) — dokumentasikan di `schema.md`.

### C6. Timezone
- **Masalah**: Waktu event tersimpan salah zona → user lihat jadwal keliru.
- **KEPUTUSAN**: timezone aplikasi = **WIB** (`Asia/Jakarta`). Simpan & tampilkan konsisten; `config('app.timezone')` = `Asia/Jakarta`.

---

## D. Performa & Query

### D1. N+1 Query
- **Masalah**: Query tambahan per baris (mis. load member untuk tiap publication).
- **Contoh di project**: list publications, direktori member, list news.
- **Mitigasi**: eager loading `with('member')`, `withCount()`; hindari query di dalam loop.

### D2. Query Tanpa Index
- **Masalah**: Filter/pencarian lambat saat data banyak.
- **Contoh di project**: filter publications by kategori/status, direktori member, slug jurnal.
- **Mitigasi**: index kolom FK & kolom yang difilter (`member_id`, `kategori`, `status`, `slug`, `role`).

### D3. Tanpa Pagination
- **Masalah**: `->get()` semua baris → halaman berat.
- **Contoh di project**: direktori member (PRD wajib pagination), list publications.
- **Mitigasi**: `paginate()`/`cursorPaginate()` di semua list publik & admin.

### D4. Data Branding Di-query Tiap Request
- **Masalah**: `organization_profile` (nama/logo) di-load di tiap halaman.
- **Mitigasi**: cache (mis. `Cache::remember`), invalidasi saat di-update.

### D5. Asset Tidak Optimal
- **Masalah**: Bundle CoreUI besar, gambar tanpa cache-busting.
- **Mitigasi**: `npm run build` (Vite) di production, optimasi aset, gunakan `asset()`/`@vite`.

---

## E. Operasional & Deploy

### E1. `APP_DEBUG=true` di Production
- **Masalah**: Stack trace bocor env, kredensial, struktur kode.
- **Mitigasi**: checklist deploy: `APP_DEBUG=false`, `php artisan config:cache`.

### E2. `storage:link` Tidak Dijalankan
- **Masalah**: Semua file/gambar 404.
- **Mitigasi**: wajib `php artisan storage:link` di checklist deploy.

### E3. Admin Default dengan Password Lemah
- **Masalah**: Seeder admin `admin/admin123` tidak diganti → admin diketahui publik.
- **Mitigasi**: password acak/kuat saat seeder; wajib ganti saat produksi; simpan di tempat aman.

### E4. Migrasi Tanpa Backup
- **Masalah**: Rollback sulit / data hilang saat migrasi gagal.
- **Mitigasi**: backup DB sebelum migrate di production; uji restore.

### E5. Backup Tidak Mencakup Storage
- **Masalah**: Upload hilang jika server rusak.
- **Mitigasi**: prosedur backup DB **+** `storage/app/public`; uji restore berkala.

### E6. Log Terpapar
- **Masalah**: `laravel.log` bisa diakses publik.
- **Mitigasi**: log di luar `public/` (default Laravel sudah benar); proteksi `.htaccess`; jangan expose.

### E7. Maintenance Mode
- **Masalah**: Deploy tanpa `php artisan down` → user lihat error mentah.
- **Mitigasi**: `down`/`up` saat maintenance; halaman maintenance ramah.

### E8. Permission Storage
- **Masalah**: Shared hosting storage tidak writable → upload/login gagal.
- **Mitigasi**: cek permission saat deploy.

---

## F. Khusus Project Ini

### F1. Direktori Member Publik
- **Masalah**: Nama+institusi+foto member tampil publik (keputusan PRD) — berisiko scraping data.
- **Mitigasi**: tampilkan **hanya** field publik (nama, institusi, foto) — JANGAN email/telepon; pagination; patuh PRD §3.10.

### F2. Registrasi Langsung Aktif (tanpa verifikasi email)
- **Masalah**: Akun bot/spam karena tidak ada verifikasi.
- **KEPUTUSAN**: verifikasi email **tidak dipasang** — dapat menjadi future item. Mitigasi sekarang: rate limit register (A7), honeypot/captcha opsional, admin bisa nonaktifkan akun manual (PRD §3.9).

### F3. Slug Jurnal Duplikat
- **Masalah**: Dua jurnal slug sama → link keluar salah.
- **Mitigasi**: unique constraint `slug` + generate otomatis dari nama.

---

## Prioritas Penerapan

1. **WAJIB dari awal (Fase 2–5 saat backend dibangun)**: A1, A2, A3, A4, A5, A6, B1, B2, B3, C1, C2, C3, C4, C6, D1, D2, D3.
2. **Saat pembuatan modul terkait**: A7 (auth & upload), A9 (kelola user), B4, B5, D4, F1, F3.
3. **Checklist deploy (Fase 7)**: E1–E8.

---

## Checklist Review (dipakai worker sebelum menyelesaikan task)

- [ ] Route admin dilindungi middleware role, bukan cuma UI?
- [ ] Tidak ada `$request->all()` / mass assignment ke kolom sensitif?
- [ ] Kepemilikan data dicek (IDOR) untuk aksi edit/delete?
- [ ] Input user di-escape; tidak ada `{!! !!}` tanpa sanitasi?
- [ ] Upload: mime asli + whitelist + random filename + ukuran limit?
- [ ] Unique constraint ada di DB untuk email/slug?
- [ ] Double submit / race dicegah (transaksi, update kondisional)?
- [ ] Timezone WIB konsisten?
- [ ] Tidak ada N+1; list pakai pagination + index?
- [ ] Operasi multi-step dalam transaksi?
- [ ] Backup/storage:link/debug termasuk checklist deploy?

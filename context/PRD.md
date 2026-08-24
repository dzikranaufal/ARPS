# Product Requirements Document — Website ARPS

**ARPS** = Academics, Researchers, and Practitioners Society
**Versi:** Draft v0.3 · **Tanggal:** 22 Agustus 2026
**Status:** Requirement dikonfirmasi client. Ambiguitas dari v0.1 telah dibereskan (lihat changelog di bagian akhir).

Dokumen ini adalah acuan teknis untuk development (termasuk AI coding agent). Scope PRD ini murni **aplikasi Laravel (website ARPS)**. Instalasi OJS untuk jurnal ARPS sendiri, domain, dan hosting **di luar scope** dokumen ini — lihat bagian "Out of Scope".

---

## 1. Ringkasan Sistem

| Aspek | Keterangan |
|---|---|
| Tech stack | Laravel (Blade) + **CoreUI** + custom CSS + MySQL — monolith, frontend & backend satu aplikasi |
| Tim development | 2 developer (Frontend & Backend) |
| Target user | Mahasiswa, dosen, tenaga kependidikan, laboran, akademisi, peneliti, praktisi, organisasi mahasiswa (HIMA/BEM/LEPPIM), masyarakat umum, mitra dalam & luar negeri |
| Status pengembangan | Proyek dikerjakan dalam fase: **frontend (tampilan)** lalu fungsionalitas (CRUD, auth, approval). Status progres lanjutan dipantau di `notes/progress.md` |
| Model keanggotaan | **Register gratis** — cukup registrasi & login, **tanpa pembayaran, tanpa tier, tanpa masa berlaku**. Keputusan final: **tidak akan dibuat berbayar** |
| Storage file | Local storage Laravel (`storage/app/public`) di server hosting, fase awal. Migrasi ke cloud object storage (mis. Cloudflare R2) bisa dilakukan nanti jika kuota mepet — cukup ganti filesystem driver |

---

## 2. User Roles & Permission Matrix

3 role sistem. Jabatan organisasi (Ketua, Sekjen, Managing Editor, dll) adalah **data** yang ditampilkan di halaman Organization — bukan role login, kecuali orang tsb juga didaftarkan sebagai Super Admin/Admin Manager. **Visitor bukan role** — hanya status sebelum login; tidak ada perubahan/perlakuan khusus di aplikasi.

Semua pengguna web (termasuk visitor yang register) adalah **Member**. Role sistem: `superadmin`, `admin_manager`, `member`.

| Fitur / Akses | Super Admin | Admin Manager | Member |
|---|---|---|---|
| Kelola user & role (buat/hapus admin) | ✅ | ❌ | ❌ |
| Kelola pengaturan sistem & branding | ✅ | ❌ | ❌ |
| Kelola Programs, Organization, News, Technology Innovation | ✅ | ✅ | ❌ |
| Kelola Journals (katalog referensi) | ✅ | ✅ | ❌ |
| Kelola Events (info kegiatan) | ✅ | ✅ | ❌ |
| Review & approve Publications/karya dari member | ✅ | ✅ | ❌ |
| Kelola data & status akun (aktif/nonaktif) | ✅ | ✅ | ❌ |
| Login & lihat dashboard pribadi | ✅ | ✅ | ✅ |
| Edit profil sendiri | — | — | ✅ |
| Upload karya/tulisan/prestasi/produk (Publications) | — | — | ✅ (masuk antrian review) |
| Register & login keanggotaan | — | — | ✅ (semua yang register jadi Member) |
| Browse semua info publik | ✅ | ✅ | ✅ |

Catatan:
- Admin Manager bisa **lebih dari 1 akun**.
- Personel (siapa menjabat Super Admin/Admin Manager) di luar scope PRD — akun tinggal dibuat sesuai role.
- Member adalah **semua pengguna web**; tidak ada entitas terpisah antara "user" dan "member" (lihat §6).

---

## 3. Struktur & Requirement Halaman Publik

Halaman publik yang wajib tampil: Home, About, Organization (+ direktori member), Programs, Technology Innovation, Journals, Publications, News, Events, Register, Contact. Ketersediaan tampilan tiap halaman dilacak di `notes/progress.md`.

### 3.1 Home / Landing Page
- Nama & deskripsi singkat ARPS
- Tujuan perkumpulan
- Highlight program & kegiatan terbaru
- Highlight informasi jurnal
- CTA pendaftaran (register)
- Ringkasan info organisasi

### 3.2 About
- Profil, latar belakang, tujuan, visi & misi
- 5 fokus bidang: Engineering, Sosial, Akademik, Penelitian, Praktik/profesional

### 3.3 Organization
- Nama pengurus, jabatan/posisi, afiliasi/institusi, **foto**
- Struktur fleksibel (jumlah & posisi bisa berkembang)
- Referensi awal: pola susunan ABBEI, gabungan dosen/mahasiswa/praktisi/peneliti
- **Menggabungkan direktori member publik** (lihat 3.10)

### 3.4 Programs
- CRUD standar oleh admin (akademik, penelitian, praktik, engineering, sosial, inovasi teknologi)

### 3.5 Technology Innovation
- CRUD standar oleh admin — highlight inisiatif inovasi teknologi ARPS

### 3.6 Journals (katalog referensi, bukan submission system)

Halaman **katalog/direktori** jurnal. Setiap jurnal adalah **kartu referensi dengan link keluar** — **bukan** sistem submission/peer-review.

**Field data per jurnal (final):**
- `nama` (judul jurnal)
- `slug` / `subdomain` (contoh: `namajurnal.arps.org`)
- `deskripsi`
- `e_issn`
- `cover` (gambar sampul)
- `link_eksternal` (URL tujuan saat kartu diklik)
- `status` (mis. aktif/arsip)

**Alur tanpa OJS:**
- Klik kartu jurnal → **link keluar** ke sumber eksternal (Semarak Ilmu Malaysia, PPI Turki, UPI, dll).

**Alur dengan OJS aktif (future):**
- Link keluar diarahkan ke subdomain OJS (`namajurnal.arps.org`).
- Seluruh halaman jurnal (home, archives, guidelines, submission) **disediakan OJS**, bukan oleh website Laravel.

**Catatan microsite `/journal/{slug}`:**
- Microsite jurnal yang ada di kode (`/journal/{slug}` → home/archives/guidelines) **dipertahankan sebagai material diskusi** — **bukan deliverable resmi** PRD ini.
- Jalur resmi yang dipakai adalah **link keluar** (Opsi A). Microsite boleh dijadikan halaman eksplorasi/diskusi, tetapi tidak dianggap bagian dari scope website.

**Referensi jurnal eksternal (contoh, data awal):**
- Journal of Advanced Research in Fluid Mechanics and Thermal Sciences — Semarak Ilmu Malaysia
- PIJAR — Puspitur, PPI Turki
- MOTOR: Journal of Automotive Engineering — UPI
- ATIKANOTO: Journal of Automotive Engineering Education — UPI

Jumlah & judul jurnal **tidak dibatasi** — data dikelola lewat CRUD, berapa pun bisa ditambahkan kemudian.

**Catatan OJS (untuk pembahasan terpisah, bukan bagian MVP Laravel):**
- Setiap jurnal yang dikelola ARPS di OJS mendapat subdomain sendiri: `namajurnal.arps.org` (menyesuaikan domain final, mis. `arpsindo.id`)
- Estimasi jumlah jurnal tahun pertama: **5–10**
- Wewenang membuat jurnal baru di OJS: **Admin Manager & Super Admin**. Publish jurnal oleh member = future item.
- OJS di-setup **manual di hosting** (link instalasi disediakan); bisa di-install **lokal** untuk pengecekan (di luar repo Laravel)
- Implikasi teknis: perlu **wildcard subdomain DNS** (`*.arps.org`) sejak awal instalasi, OJS mode multi-journal, dan hosting yang support wildcard subdomain
- Volume storage OJS berpotensi tumbuh cepat (banyak jurnal × banyak artikel × PDF) — rencana migrasi ke object storage (S3-compatible) sebaiknya dipikirkan sejak instalasi awal

### 3.7 Publications
Wadah karya member, digabung dalam satu section, terdiri dari beberapa **kategori**:
- Tulisan/artikel (media massa, opini, dll)
- Prestasi member
- Produk teknologi
- Karya PkM / tugas mahasiswa

**Alur:** Member upload → status `pending` → Admin Manager approve/reject → tayang publik jika `approved`.
Setiap entri punya field kategori agar bisa difilter di tampilan publik.

### 3.8 Events
- Info kegiatan (webinar, kuliah umum, kunjungan industri, seminar, conference): judul, deskripsi, tanggal/waktu, lokasi (fisik/online), poster
- **Tidak ada** fitur pendaftaran online di website — pendaftaran peserta dilakukan manual via kontak yang dicantumkan di halaman event (WA/email/link eksternal)
- **Kontak pendaftaran: opsional, dan bisa berbeda per event** (field kontak tersendiri per event)

### 3.9 Register (Pendaftaran & Login)
Form ringkas: **Nama, Email, Telepon, Organisasi/Lembaga (opsional)**, ditambah password untuk keperluan login.

**Alur:**
1. Visitor isi form registrasi (termasuk buat password)
2. Submit → akun **langsung aktif** (gratis, tanpa pembayaran, tanpa approval admin)
3. Member dapat akses dashboard pribadi via login

Keanggotaan **gratis, tanpa masa berlaku, dan tidak akan dibuat berbayar**. Jika akun dinonaktifkan (mis. karena bermasalah): **nonaktif manual oleh admin** — data tidak dihapus (soft-deactivate), dan **reaktivasi juga manual oleh admin**.

### 3.10 Direktori Member (Publik)
- Ditampilkan **di dalam halaman Organization** (bukan halaman terpisah)
- Field publik: **nama + institusi + foto (jika ada)** — tidak menampilkan data pribadi lain
- Semua member berstatus aktif otomatis tampil (tidak ada opt-out)
- Ditampilkan dengan **pagination**

### 3.11 Contact
- Info kontak resmi ARPS

---

## 4. Member Dashboard
- Edit profil sendiri
- Lihat status akun (aktif/nonaktif)
- Upload karya ke Publications (pilih kategori) → status `pending`
- Lihat status karya yang sudah di-submit (`pending` / `approved` / `rejected`)

---

## 5. Admin Dashboard

Dua role admin (dibedakan jelas):

### 5.1 Super Admin
- Kelola user & role (buat/hapus/atur akses Admin Manager)
- Kelola pengaturan sistem & branding
- Akses penuh ke seluruh modul konten (sama seperti Admin Manager)

### 5.2 Admin Manager
- Kelola Programs, Journals (katalog), Events, Organization, News, Technology Innovation
- Kelola data & status akun member (aktif/nonaktif)
- Antrian approval — review semua konten yang di-upload member (Publications) sebelum tayang publik
- Bisa lebih dari 1 akun

---

## 6. Data Model (Entitas Utama)

```
users  -- tabel tunggal, menampung SEMUA pengguna web (member & admin)
  id, nama, email, password, role (superadmin | admin_manager | member)
  telepon (nullable), organisasi (nullable), status (aktif | nonaktif)
  -- tidak ada expired_at: keanggotaan gratis & tanpa masa berlaku
  -- tidak ada relasi payment: tidak ada pembayaran
  -- aktif/nonaktif & reaktivasi dilakukan manual oleh admin
  -- field profil member (telepon/organisasi) nullable; hanya diisi untuk role member

programs
  judul, deskripsi, kategori, gambar

journals
  nama, slug (subdomain), deskripsi, e_issn, cover, link_eksternal, status

events
  judul, deskripsi, tanggal_waktu, lokasi, poster, info_kontak_pendaftaran (opsional, per-event)

publications
  member_id (FK ke users, uploader), judul, deskripsi, kategori (tulisan | prestasi | produk | pkm)
  file/konten, status (pending | approved | rejected), reviewer_id (FK)

organization_profile  -- single-row (pengaturan profil organisasi)
  nama, deskripsi, visi, misi, logo

organization_structure  -- multi-baris (pengurus)
  nama_pengurus, jabatan, afiliasi, foto

news / articles
  judul, isi, tanggal_publish, gambar
```

**Catatan:**
- **Tidak ada entitas `members` terpisah** — `users` adalah satu-satunya tabel pengguna; `role` menentukan jenis akses, `status` khusus untuk member.
- Tidak ada entitas `event_registrations` (pendaftaran event di luar sistem).
- Tidak ada entitas `payments`/`transactions` (keanggotaan gratis, tidak ada pembayaran).
- Tidak ada `expired_at` (keanggotaan gratis & tanpa masa berlaku, hanya field `status`).
- `organization` dipecah menjadi `organization_profile` (single-row) dan `organization_structure` (pengurus) — menyesuaikan struktur kode admin (Profile & Structure).

---

## 7. Alur Utama (Key Flows)

**7.1 Registrasi & Login Member**
Visitor isi form registrasi (+ buat password) → submit → status Member otomatis `aktif` (gratis, tanpa approval) → login → dashboard tersedia.

**7.2 Upload & Review Karya (Publications)**
Member login → upload karya + pilih kategori → status `pending` → Admin Manager buka antrian approval → approve (tayang publik) / reject → Member lihat status di dashboard.

**7.3 Nonaktif & Aktif Kembali Akun**
Admin nonaktifkan akun member (data tidak dihapus). Reaktivasi juga **manual oleh admin** — tidak ada alur pembayaran ulang karena keanggotaan gratis.

---

## 8. Non-Functional Requirements

### 8.1 Keamanan
- Authentication & authorization berbasis role
- Validasi input di sisi server
- Password hashing
- Proteksi CSRF, XSS, SQL Injection
- Validasi jenis & ukuran file upload
- HTTPS
- Backup database berkala (mencakup folder storage, bukan cuma DB)
- Pembatasan akses data pribadi member — tidak semua data tampil publik

### 8.2 Upload File (Publications)
- Batas ukuran: **10MB per file**
- Jenis file dibatasi eksplisit (PDF, JPG, PNG, DOCX) — cegah upload file executable
- Validasi double-layer:
  - Server: `upload_max_filesize` & `post_max_size` di MultiPHP INI Editor cPanel (set ≥12MB, beri buffer)
  - Aplikasi: form request validation Laravel (`max:10240` KB)

### 8.3 Storage
- Fase awal: local disk Laravel di server hosting yang sama (`storage:link` wajib dijalankan saat deploy)
- Pastikan kuota storage hosting cukup & backup otomatis mencakup folder storage
- Upgrade ke cloud object storage (Cloudflare R2, dll) sebagai opsi jika kuota mepet — Laravel filesystem driver native support S3-compatible

### 8.4 Responsive, SEO & Performance
- Desktop, laptop, tablet, mobile (CoreUI / Bootstrap 5 grid)
- Struktur HTML & heading terstruktur, meta title/description
- URL deskriptif, optimasi gambar, loading performance

---

## 9. Di Luar Scope PRD Ini
- Instalasi & konfigurasi OJS untuk jurnal ARPS sendiri (di-setup manual di hosting, bisa di-install lokal untuk pengecekan)
- Keputusan domain & hosting final (tidak memengaruhi pembangunan web)
- Fitur pendaftaran online per event (pendaftaran manual via kontak)
- **Future item:** Member submit / publish jurnal ke OJS dari website

---

## 10. Open Items — Menunggu Konfirmasi Client
Semua item yang sebelumnya `[TBD]` telah dikonfirmasi. Tidak ada open item yang memblokir development.

---

## Changelog

**v0.3 (22 Agustus 2026)**
- **Role sistem diubah dari 4 → 3**: `superadmin`, `admin_manager`, `member`. Visitor bukan role (hanya status sebelum login).
- **`users` & `members` digabung menjadi satu tabel `users`** — tidak ada entitas `members` terpisah. Semua pengguna web adalah Member; `role` menentukan akses; field profil member (telepon/organisasi/status) nullable di tabel `users`.
- `organization` dipecah menjadi `organization_profile` (single-row) & `organization_structure` (pengurus) — menyesuaikan struktur kode admin.
- Matriks role di §2 diperbarui tanpa kolom Visitor.

**v0.2 (22 Agustus 2026)**
- Stack diubah: Bootstrap 5 → **CoreUI** (sesuai implementasi aktual)
- Status proyek ditegaskan: **frontend (tampilan)**, fungsional menyusul (lihat `notes/progress.md`)
- **Membership → Register**: gratis, tidak akan berbayar, tanpa masa berlaku; nonaktif & reaktivasi akun manual oleh admin
- Journal diklarifikasi sebagai **katalog referensi + link keluar** (Opsi A); microsite `/journal/{slug}` = material diskusi, bukan deliverable
- Field data journal di-finalisasi (nama, slug, deskripsi, e_issn, cover, link_eksternal, status)
- OJS: subdomain `namajurnal.arps.org`, estimasi 5–10 jurnal, wewenang Admin Manager + Super Admin, setup manual, member publish = future item
- Kontak pendaftaran event: opsional & berbeda per event
- Direktori member: digabung ke halaman Organization, tampil nama+institusi+foto, pagination, semua member aktif tampil
- Daftar halaman publik di-finalisasi; ketersediaan tampilan dilacak di `notes/progress.md`
- Role admin dipertahankan 2 (Super Admin & Admin Manager) dan diperjelas batasannya
- Seluruh `[TBD]` di §10 dibereskan

**v0.1 (13 Agustus 2026)** — Draft awal.

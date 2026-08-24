# Task — Fase 4: Keanggotaan & Member Dashboard

**Lingkup**: backend keanggotaan — dashboard member, edit profil, admin kelola akun (aktif/nonaktif), direktori member publik.
**Tujuan**: alur keanggotaan PRD §3.9, §3.10, §4, §5.2 berfungsi end-to-end (kecuali upload karya — Fase 5).
**Cara pakai**: kerjakan subtask **secara berurutan** (4.0 → 4.7). Selesaikan satu, verifikasi acceptance-nya, baru lanjut.

> **Keputusan yang sudah diambil (rekomendasi yang disetujui)**:
> - Member punya kolom `foto` (migration baru).
> - Layout member terpisah `layouts/member.blade.php`.
> - Kelola akun member: **TANPA create & delete** — register via halaman publik (Fase 2), nonaktif = soft-deactivate (data tidak dihapus, PRD §3.9). Admin hanya: list, lihat detail, toggle status.
> - Edit profil member: nama, telepon, organisasi, foto + ganti password opsional; **email tidak bisa diubah** (identifier login).

---

## Pranala Dokumen (WAJIB dibaca berurutan sebelum mulai)

1. `context/PRD.md` — requirement (fokus §3.9, §3.10, §4, §5.2, §7.1, §7.3)
2. `context/glossary.md` — istilah baku
3. `context/architecture.md` — struktur, routing, layout
4. `context/directory.md` — peta file/route/controller/view
5. `context/schema.md` — acuan skema (diupdate di 4.0)
6. `context/risiko.md` — **wajib**: A2, B1–B4, D1–D3, F1
7. `notes/progress.md` — status progres
8. `context/rules.md` — aturan perilaku

---

## Aturan Fase Ini (wajib)

- **Risiko wajib**:
  - A2: update profil member HANYA field yang diizinkan (nama, telepon, organisasi, foto, password) — JANGAN sentuh `role`, `status`, `email`.
  - B1–B4: upload foto member aman (mime asli, whitelist jpg/png, 2MB, random filename, hapus foto lama saat replace).
  - D1–D3: list member (admin & direktori) pakai pagination + index; hindari N+1.
  - F1: direktori publik HANYA nama + institusi + foto — JANGAN email/telepon/field pribadi lain.
- **Pengecualian CRUD (keputusan)**: member management TANPA create/delete.
- **Verifikasi**: setiap subtask, jalankan acceptance check.

---

## Subtask 4.0 — Migration & Schema: kolom foto member

**Tindakan**:
1. Buat migration `add_foto_to_users_table`: `foto` string **nullable** pada `users`.
2. `app/Models/User.php`: tambah `foto` ke `#[Fillable]` — tetap TANPA `role`/`status` (risiko A2).
3. `context/schema.md` §2 users: tambah baris kolom `foto | string | yes | Foto profil member; null jika tidak ada`.

**Acceptance**:
- `php artisan migrate` jalan; `Schema::getColumnListing('users')` memuat `foto`.
- `schema.md` sinkron.

---

## Subtask 4.1 — Layout & Route Member

**Tindakan**:
1. Buat `resources/views/layouts/member.blade.php` (struktur mirip `layouts/app.blade.php`):
   - `<head>` sama (aset CoreUI/icons/css/style).
   - Body: header ringan (brand "ARPS" → `route('member.dashboard')`, link "Dashboard", "Profil", dan **form POST logout** → `route('logout')` + `@csrf`).
   - `@yield('content')` di dalam container.
   - Footer minimal + `@stack('styles')` / `@stack('scripts')`.
2. `routes/web.php` — grup member (middleware `auth`, `role:member`) — **ganti placeholder Fase 2**:
   ```php
   Route::middleware(['auth', 'role:member'])->group(function () {
       Route::get('/dashboard', [MemberDashboardController::class, 'index'])->name('member.dashboard');
       Route::get('/profile', [MemberProfileController::class, 'edit'])->name('member.profile.edit');
       Route::put('/profile', [MemberProfileController::class, 'update'])->name('member.profile.update');
   });
   ```
3. Buat `App\Http\Controllers\Member\MemberDashboardController` (view dashboard) & `Member\MemberProfileController` (isi di 4.3).
4. Pindahkan/rename view placeholder `resources/views/member/dashboard.blade.php` agar memakai `layouts.member`.

**Acceptance**:
- Member login → `/dashboard` render dengan layout member (bukan navbar publik).
- Logout dari layout member berfungsi (POST).
- Admin akses `/dashboard` → 403 (role member saja).

---

## Subtask 4.2 — Dashboard Member

**Tindakan**:
1. `MemberDashboardController@index`:
   - `$member = auth()->user()`.
   - `$publications = auth()->user()->publications()->latest()->paginate(5)` (untuk daftar status karya — kosong dulu).
2. View `member/dashboard.blade.php`:
   - Greeting: "Selamat datang, {nama}".
   - **Status akun**: badge `aktif` (success) / `nonaktif` (danger) dari `$member->status`.
   - Kartu "Profil" → link `member.profile.edit`.
   - Kartu "Karya (Publications)": daftar `$publications` (judul + badge status) — jika kosong, teks "Belum ada karya. Upload karya tersedia di Fase 5." **Komentar STATIC SAMPLE / placeholder** karena upload belum dibangun.
   - Kartu "Upload Karya" → tombol disabled/placeholder (Fase 5).

**Acceptance**:
- Dashboard menampilkan nama member & badge status akun benar.
- List karya kosong → pesan placeholder; tidak error.

---

## Subtask 4.3 — Edit Profil Member

**Tindakan**:
1. `Member\MemberProfileController`:
   - `edit()` → view `member/profile.blade.php` (form pre-filled dari `auth()->user()`).
   - `update(Request $request)`:
     - Validasi:
       - `nama` required|string|max:255
       - `telepon` required|string|max:20
       - `organisasi` nullable|string|max:255
       - `foto` nullable|image|mimes:jpg,jpeg,png|max:2048
       - `current_password` — **required** jika `password` baru diisi (pakai rule `current_password` Laravel)
       - `password` nullable|confirmed|min:8
     - Update field: `nama`, `telepon`, `organisasi`, `foto` (upload ke `storage/app/public/profiles`, hapus foto lama saat replace), dan `password` (jika diisi — pakai hash cast model).
     - **JANGAN** menyentuh `role`, `status`, `email` (risiko A2).
     - Redirect kembali dengan flash `success`.
2. View `member/profile.blade.php`: form dengan semua field + preview foto lama + bagian ganti password (password lama, baru, konfirmasi — ketiganya optional bersama-sama).

**Acceptance**:
- Profil (nama/telepon/organisasi/foto) tersimpan & tampil ulang.
- Ganti password: butuh `current_password` benar; salah → error; sukses → login ulang berfungsi dengan password baru.
- `role`/`status`/`email` tidak berubah (cek di DB).

---

## Subtask 4.4 — Admin Kelola Akun Member

**Tindakan**:
1. `Admin\MemberController`:
   - `index()`: `User::where('role', 'member')->withCount('publications')->paginate(15)` + search opsional (`nama`/`email` LIKE — pakai `when()` + escape) (risiko D1/D2/D3).
   - `show($id)`: detail member + list publications-nya (eager load).
   - `updateStatus($id)`: toggle `status` aktif ↔ nonaktif.
     - Hanya user `role=member` yang boleh diubah (validasi).
     - **JANGAN** izinkan menonaktifkan diri sendiri / user non-member.
     - PUT + `@csrf` (risiko A5).
2. Route (grup admin):
   ```php
   Route::get('members', [MemberController::class, 'index'])->name('admin.members.index');
   Route::get('members/{id}', [MemberController::class, 'show'])->name('admin.members.show');
   Route::put('members/{id}/status', [MemberController::class, 'updateStatus'])->name('admin.members.update-status');
   ```
3. View `admin/members/index.blade.php`: tabel (nama, email, telepon, organisasi, status badge, jumlah karya, aksi: detail + toggle status via form PUT + modal konfirmasi) + search box + pagination.
   - View `admin/members/show.blade.php` (detail + karya).
4. Sidebar (`partials/sidebar.blade.php`): submenu "All Members" `href="#"` → `route('admin.members.index')`; pastikan tidak ada "Requests" (sudah dihapus Fase 0).
5. **Verifikasi ulang** alur login: member `nonaktif` tidak bisa login (sudah di Fase 2.4 — pastikan masih berlaku).

**Acceptance**:
- Admin list + search member; toggle status berfungsi; badge status update.
- Member nonaktif → logout → login ditolak (pesan akun dinonaktifkan) → admin aktifkan → login sukses.
- Tidak ada route delete/create member.
- Admin tidak bisa menonaktifkan diri sendiri.

---

## Subtask 4.5 — Direktori Member Publik (di halaman Organization)

**Tindakan**:
1. `routes/web.php` — route `/organization` (closure dari Fase 3.9): tambahkan data direktori:
   ```php
   $members = App\Models\User::where('role', 'member')
       ->where('status', 'aktif')
       ->paginate(12);  // PRD §3.10: semua member aktif otomatis tampil, pagination
   ```
   (Import model di atas file; jangan inline FQCN berulang.)
2. `resources/views/organization/index.blade.php` — ganti bagian direktori STATIC SAMPLE:
   - List/grid member: **nama, institusi (`organisasi`), foto (jika ada, fallback placeholder)**.
   - **JANGAN** email/telepon (risiko F1).
   - Pagination links (`$members->links()`).
   - Bagian struktur pengurus tetap dari DB (Fase 3.9).
3. Hapus komentar STATIC SAMPLE pada bagian direktori (sudah data nyata).

**Acceptance**:
- `/organization` menampilkan member aktif (nama + institusi + foto) dari DB dengan pagination.
- Member nonaktif & admin tidak tampil.
- Tidak ada email/telepon di halaman (cek DOM).

---

## Subtask 4.6 — Verifikasi Fase 4

**Tindakan**:
1. `php artisan migrate:fresh --seed` — bersih.
2. Skenario end-to-end via browser:
   - Register member baru → auto login → `/dashboard` (badge aktif).
   - Edit profil: ubah nama, upload foto → tersimpan; ganti password (dengan password lama) → logout → login password baru sukses.
   - Login admin → `/admin/members` → search member → toggle status `nonaktif` → logout.
   - Login member nonaktif → ditolak.
   - Admin toggle kembali `aktif` → login member sukses.
   - `/organization` → direktori menampilkan member aktif + pagination; tanpa email/telepon.
3. Checklist risiko:
   - Profil update tidak mengubah role/status/email (A2).
   - Foto upload 2MB/tipe salah ditolak; foto lama terhapus saat replace (B1–B4).
   - List member berpagination; query efisien (D1–D3).
   - Direktori tanpa field pribadi (F1).

**Acceptance**:
- Semua skenario berjalan sesuai; tidak ada error di log.

---

## Subtask 4.7 — Sinkronkan Dokumentasi

**Tindakan**:
1. `context/directory.md`: tambah `Member\MemberDashboardController`, `Member\MemberProfileController`, `Admin\MemberController`, view `member/{dashboard,profile}`, `layouts/member`, `admin/members/*`; tambah route member.
2. `notes/progress.md`: dashboard member 🟢, edit profil 🟢, kelola akun 🟢, direktori member 🟢; Publications upload tetap 🟡 (Fase 5).
3. `context/schema.md`: sudah diupdate di 4.0 (kolom foto).

**Acceptance**:
- `directory.md` & `progress.md` mencerminkan kode aktual.

---

## Checklist Akhir (sebelum menyatakan Fase 4 selesai)

- [ ] 4.0–4.7 semua selesai & acceptance terpenuhi.
- [ ] Member management TANPA create/delete (register via publik; nonaktif = soft-deactivate).
- [ ] Update profil tidak menyentuh role/status/email (A2).
- [ ] Upload foto aman (B1–B4).
- [ ] Direktori publik tanpa email/telepon (F1).
- [ ] Pagination + index + eager loading (D1–D3).
- [ ] Dokumentasi sinkron.

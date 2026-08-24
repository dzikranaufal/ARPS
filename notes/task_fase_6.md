# Task — Fase 6: Super Admin & Pengaturan

**Lingkup**: backend hak eksklusif Super Admin — kelola admin user & role, pengaturan sistem/branding.
**Tujuan**: PRD §5.1 terpenuhi — Super Admin bisa kelola admin (buat/hapus/atur akses Admin Manager) dan mengubah branding.
**Cara pakai**: kerjakan subtask **secara berurutan** (6.0 → 6.6). Selesaikan satu, verifikasi acceptance-nya, baru lanjut.

> **Keputusan yang sudah diambil (rekomendasi yang disetujui)**:
> - Kelola user: fokus pada user ber-role **admin** (`superadmin` & `admin_manager`). Member dikelola di Fase 4 (`admin.members.*`) — JANGAN tercampur.
> - Branding memakai tabel `organization_profile` yang sudah ada (nama, deskripsi, visi, misi, logo) — TIDAK membuat tabel settings baru (hindari duplikasi data).
> - Proteksi: seluruh halaman ini hanya untuk **Super Admin** (middleware `role:superadmin`) — Admin Manager TIDAK boleh akses.
> - Seorang admin **tidak bisa menonaktifkan/menghapus akun sendiri** (cegah lockout & privilege abuse).

---

## Pranala Dokumen (WAJIB dibaca berurutan sebelum mulai)

1. `context/PRD.md` — requirement (fokus §2, §5.1, §6 users)
2. `context/glossary.md` — istilah baku
3. `context/architecture.md` — struktur, routing, layout
4. `context/directory.md` — peta file/route/controller/view
5. `context/schema.md` — acuan skema (users, organization_profile)
6. `context/risiko.md` — **wajib**: A2, A5, A9, E3, D2/D3
7. `notes/progress.md` — status progres
8. `context/rules.md` — aturan perilaku

---

## Aturan Fase Ini (wajib)

- **Risiko wajib**:
  - A9 (privilege escalation): hanya Super Admin yang akses halaman ini; Admin Manager tidak punya route/menu ini sama sekali.
  - A2: `$fillable` aman; buat user admin via controller dengan role eksplisit (bukan dari input form yang bisa disalahgunakan).
  - A5: semua aksi (create/update/delete/toggle) via POST/PUT/DELETE + `@csrf`.
  - E3: password admin baru = minimal 8, wajib kuat; jangan pernah menampilkan password.
  - D2/D3: list user admin + pagination + search.
- **Verifikasi**: setiap subtask, jalankan acceptance check.

---

## Subtask 6.0 — Middleware & Route Admin-Only (Super Admin)

**Konteks**: grup admin saat ini mengizinkan `superadmin,admin_manager` (Fase 2). Kelola user & pengaturan harus **khusus Super Admin**.

**Tindakan**:
1. Gunakan middleware `role` yang sudah ada (Fase 2.5) — tidak perlu middleware baru:
   ```php
   Route::prefix('admin')->name('admin.')->middleware(['auth', 'role:superadmin'])->group(function () {
       // users (admin) & settings
   });
   ```
2. Route:
   ```php
   Route::resource('admin-users', AdminUserController::class)->except(['show']);
   Route::put('admin-users/{id}/status', [AdminUserController::class, 'updateStatus'])->name('admin-users.update-status');
   Route::get('settings', [SettingController::class, 'edit'])->name('settings.edit');
   Route::put('settings', [SettingController::class, 'update'])->name('settings.update');
   ```
   (dalam grup middleware `role:superadmin` — gunakan prefix/name yang sesuai; sesuaikan dengan pola route yang sudah ada)
3. Sidebar (`partials/sidebar.blade.php`):
   - Grup Users: link "Admin Users" `href="#"` → `route('admin.admin-users.index')`; "Role & Permissions" → **hapus** (tidak ada halaman role terpisah — role dikelola via form admin user) atau arahkan ke `admin-users.index`.
   - Grup Settings: "General Settings" `href="#"` → `route('admin.settings.edit')`; "Social Media" → **hapus** (tidak ada data sosmed di schema; branding via organization_profile) — atau tandai STATIC SAMPLE.
   - Tambahkan catatan "(Super Admin)" pada grup yang sudah ada (Fase 0) — pertahankan.

**Acceptance**:
- Admin Manager akses `/admin/admin-users` atau `/admin/settings` → **403**.
- Super Admin akses → halaman terbuka.
- Sidebar menautkan ke route nyata; item yang dihapus tidak menimbulkan error.

---

## Subtask 6.1 — Kelola Admin Users (CRUD, role superadmin/admin_manager)

**Tindakan**:
1. `Admin\AdminUserController`:
   - `index()`: `User::whereIn('role', ['superadmin', 'admin_manager'])->paginate(15)` + search nama/email (risiko D2/D3).
   - `create()` → view form; `store()`:
     - Validasi: `nama` required|string|max:255; `email` required|email|unique:users,email; `password` required|min:8|confirmed; `role` required|in:superadmin,admin_manager.
     - Simpan user dengan role dari input (dibolehkan — ini khusus admin oleh Super Admin) + `status` **hardcode `aktif`**.
   - `edit($id)` / `update($id)`:
     - Validasi: nama, email unique (`ignore` id), role in:superadmin,admin_manager, password **nullable**|min:8|confirmed (hanya jika diisi).
     - **Proteksi**: jika mengubah role admin yang sedang login → cegah menurunkan dirinya sendiri dari `superadmin` (risiko A9/lockout). Implementasi: jika `$id === auth()->id()` dan role baru ≠ superadmin → error "tidak bisa mengubah role sendiri".
   - `destroy($id)`: cegah menghapus diri sendiri (`$id === auth()->id()` → error); hapus user.
   - `updateStatus($id)`: toggle `aktif`/`nonaktif`; cegah menonaktifkan diri sendiri.
2. View `admin/admin-users/{index,create,edit}.blade.php`:
   - Index: tabel (nama, email, role badge, status badge, aksi edit/status/delete via form + modal konfirmasi) + search + pagination.
   - Create/Edit: form nama, email, role select, password (+ konfirmasi; edit: optional), flash error untuk proteksi diri.
3. **Catatan penting**: admin yang dinonaktifkan → tidak bisa login (alur login Fase 2.4 sudah cek status — verifikasi berlaku untuk admin juga).

**Acceptance**:
- Super Admin: C/R/U/D admin user lengkap (create superadmin & admin_manager, edit, toggle status, delete).
- Admin Manager tidak bisa akses halaman ini (403).
- Tidak bisa menonaktifkan/menghapus/menurunkan role diri sendiri.
- Admin dinonaktifkan → login ditolak.
- Duplicate email → error validasi (risiko C3).

---

## Subtask 6.2 — Pengaturan Branding (via organization_profile)

**Konteks**: branding (nama, deskripsi, visi, misi, logo) sudah ada di `organization_profile` dengan form admin (`admin/organization/profile.blade.php`, Fase 3.7). Halaman "Settings" akan **memakai data yang sama** — jangan duplikat.

**Tindakan**:
1. `Admin\SettingController`:
   - `edit()`: `$organization = OrganizationProfile::first()` → view `admin/settings/edit.blade.php`.
   - `update()`: validasi sama seperti profile (nama required; deskripsi/visi/misi nullable; logo nullable image 2MB) → update baris pertama.
   - **Keputusan**: halaman Settings menampilkan form branding (nama org, deskripsi, visi, misi, logo) + **info kontak** (email/telepon/alamat). Karena schema `organization_profile` TIDAK punya kolom kontak → **jangan tambah kolom**; bagian kontak tetap STATIC SAMPLE placeholder (diisi langsung di view bila perlu, atau ditandai untuk keputusan lanjutan).
2. View `admin/settings/edit.blade.php`: form branding + placeholder kontak (STATIC SAMPLE).
3. Sidebar link "General Settings" sudah di-wire (6.0).

**Acceptance**:
- Super Admin ubah nama/deskripsi/visi/misi/logo → tersimpan di `organization_profile`.
- Halaman publik About/Organization/Contact menampilkan perubahan (sudah di-wire ke data di Fase 3.9).
- Tidak ada tabel/kolom baru untuk settings.

---

## Subtask 6.3 — Verifikasi Fase 6

**Tindakan**:
1. `php artisan migrate:fresh --seed` — bersih (seeder Fase 2 sudah membuat superadmin + admin manager).
2. Skenario via browser:
   - Login Super Admin → `/admin/admin-users` → buat Admin Manager baru → edit → toggle nonaktif → login admin tersebut ditolak → aktifkan kembali → login sukses.
   - Coba hapus/menonaktifkan/menurunkan role diri sendiri → error.
   - Login Admin Manager → akses `/admin/admin-users` & `/admin/settings` → 403.
   - Super Admin ubah branding → cek `/about` & `/contact` menampilkan perubahan.
3. Checklist risiko: A2, A5, A9, C3 (email unique), D2/D3 (pagination+search), E3 (password min 8).

**Acceptance**:
- Semua skenario berjalan sesuai; tidak ada error di log.

---

## Subtask 6.4 — Sinkronkan Dokumentasi

**Tindakan**:
1. `context/directory.md`: tambah `Admin\AdminUserController`, `Admin\SettingController`, view `admin/admin-users/*`, `admin/settings/edit`.
2. `notes/progress.md`: kelola admin user 🟢, pengaturan branding 🟢.
3. `context/schema.md`: tidak berubah.

**Acceptance**:
- `directory.md` & `progress.md` mencerminkan kode aktual.

---

## Checklist Akhir (sebelum menyatakan Fase 6 selesai)

- [ ] 6.0–6.4 semua selesai & acceptance terpenuhi.
- [ ] Hanya Super Admin yang akses (A9).
- [ ] Proteksi diri: tidak bisa menonaktifkan/menghapus/menurunkan role sendiri.
- [ ] Semua aksi POST/PUT/DELETE + CSRF (A5).
- [ ] Password min 8, tidak pernah ditampilkan (E3).
- [ ] Email unique + handling duplicate (C3).
- [ ] Pagination + search (D2/D3).
- [ ] Dokumentasi sinkron.

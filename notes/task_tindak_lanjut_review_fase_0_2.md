# Task — Tindak Lanjut Hasil Review Fase 0–2

**Tujuan dokumen**: menindaklanjuti hasil quality review terhadap pekerjaan Fase 0–2. Setiap task di bawah berasal dari temuan audit yang sudah diverifikasi. Kerjakan secara berurutan (A → F). Setiap task bersifat **mandiri** — jangan menambah scope di luar yang disebut.

> **Status**: semua temuan ini sudah diverifikasi oleh reviewer (bukan dugaan). Baca bukti/kode yang dirujuk, terapkan perbaikan, lalu penuhi acceptance. Jangan ragu bertanya bila ada yang tidak jelas sebelum mengubah kode.

---

## Pranala Dokumen (WAJIB dibaca berurutan sebelum mulai)

1. `context/PRD.md` — acuan utama requirement & scope
2. `context/glossary.md` — istilah baku
3. `context/architecture.md` — struktur, routing, layout, batas
4. `context/directory.md` — peta file/route/controller/view
5. `context/schema.md` — skema database (Task C wajib penuh)
6. `context/risiko.md` — daftar masalah potensial & mitigasi (Task A merujuk A7/F2, Task C merujuk A2)
7. `notes/progress.md` — status progres
8. `context/rules.md` — aturan perilaku
9. `notes/task_tindak_lanjut_review_fase_0_2.md` — dokumen ini

---

## Aturan (ringkas — detail di rules.md)

- **Scope discipline**: jangan bangun payment / event-registration online / OJS; jangan hapus microsite; jangan ganti tabel `users`.
- **Tema**: CoreUI admin (`data-coreui-*`) vs custom CSS publik; jangan campur `data-bs-*` di area CoreUI.
- **Penamaan route**: publik tanpa prefix; admin `admin.*`; microsite `journal.*`.
- **Verifikasi**: setiap perubahan fungsional signifikan WAJIB diverifikasi (jalankan aplikasi / test / tinker), bukan hanya "sepertinya benar".
- **Konflik file konteks**: prioritas `PRD.md` > `architecture.md`/`schema.md` > `directory.md`/`notes/progress.md` > `rules.md` > `glossary.md`. Laporkan konflik, jangan pilih diam-diam.

---

# Task A — Rate limit pada registrasi (risiko A7/F2)

**Latar belakang**: endpoint `POST /register` (`RegisterController::store`) **tidak punya rate limiting**. `risiko.md` A7 (prioritas 1, "WAJIB dari awal") mewajibkan rate limit untuk register, dan F2 merekomendasikan rate limit register karena registrasi **langsung aktif tanpa verifikasi email**. Tanpa ini, bot bisa membuat akun tanpa batas.

**Tujuan**: menambahkan rate limiting pada `POST /register` dengan pola yang sama seperti login.

**Lingkup (file yang disentuh)**:
- `app/Http/Controllers/Auth/RegisterController.php` (satu-satunya file kode yang diubah)

**Perubahan**:
1. Buka `app/Http/Controllers/Auth/RegisterController.php`. Perhatikan `store()`.
2. Tambahkan `use Illuminate\Support\Facades\RateLimiter;` jika belum ada.
3. Di **awal** method `store()`, sebelum validasi `$request->validate(...)`, tambahkan pengecekan rate limit:
   ```php
   $key = 'register:' . $request->ip();

   if (RateLimiter::tooManyAttempts($key, 5)) {
       throw ValidationException::withMessages([
           'nama' => 'Terlalu banyak percobaan pendaftaran. Coba lagi dalam ' . RateLimiter::availableIn($key) . ' detik.',
       ])->status(429);
   }
   ```
   - Tambahkan `use Illuminate\Validation\ValidationException;` jika belum ada.
4. Setelah user berhasil dibuat (setelah `User::create(...)` berhasil), panggil `RateLimiter::clear($key);` supaya pendaftaran yang sah tidak terhukum.
5. **JANGAN** mengubah logika validasi field, hardcode role/status, penanganan duplicate email, atau redirect.

**Acceptance (hasil teramati)**:
- `POST /register` dengan 6+ request dalam 1 menit dari IP sama → request ke-6 ditolak (untuk request JSON/`Accept: application/json` → status `429`; untuk request browser form → di-redirect kembali dengan pesan "Terlalu banyak percobaan pendaftaran").
- Register normal (1 kali) tetap sukses dan **tidak** terblokir setelah sukses (limiter di-clear).
- Role member + status aktif tetap di-hardcode (A2 tidak berubah).
- Tidak ada perubahan pada `LoginController`, middleware, route, atau view.

**Non-goals**: TIDAK menambah captcha/honeypot; TIDAK mengubah batas (5/menit) tanpa instruksi; TIDAK menambah rate limit ke endpoint lain.

---

# Task B — Perbaiki pesan error saat login akun nonaktif

**Latar belakang**: Di `LoginController::store()`, saat member berstatus `nonaktif` login:
```php
Auth::logout();
$request->session()->invalidate();
$request->session()->regenerateToken();

throw ValidationException::withMessages([
    'email' => 'Akun Anda sedang dinonaktifkan. Hubungi administrator.',
]);
```
`session()->invalidate()` menghapus seluruh data sesi — termasuk error yang baru saja di-flash oleh `ValidationException` dan `_previous.url`. Akibatnya user **di-redirect ke `/` (home) tanpa pesan**, bukan kembali ke `/login` dengan pesan "Akun sedang dinonaktifkan". Terverifikasi: redirect ke `/` dan pesan tidak tampil.

**Tujuan**: pastikan user akun nonaktif tetap **tidak bisa login**, tapi diarahkan kembali ke form login **dan melihat pesan** "Akun sedang dinonaktifkan".

**Lingkup (file yang disentuh)**:
- `app/Http/Controllers/Auth/LoginController.php` (blok status `Nonaktif` di method `store()`)

**Perubahan**:
1. Pada blok `if ($user->status === AccountStatus::Nonaktif) { ... }`:
   - **PERTAHANKAN** `Auth::logout();` (pastikan user tidak terautentikasi).
   - **HAPUS** kedua baris `$request->session()->invalidate();` dan `$request->session()->regenerateToken();`.
   - **PERTAHANKAN** `throw ValidationException::withMessages(['email' => 'Akun Anda sedang dinonaktifkan. Hubungi administrator.']);`
2. Jangan ubah blok login normal, rate limit, atau redirect-by-role.

**Acceptance (hasil teramati)**:
- Login dengan akun nonaktif (kredensial benar) → **di-redirect kembali ke `/login`** (bukan home), status 302 ke `/login`.
- Halaman `/login` menampilkan pesan "Akun Anda sedang dinonaktifkan. Hubungi administrator.".
- User tersebut **tidak terautentikasi** (mengakses `/dashboard` setelahnya → redirect ke login).
- Login akun aktif (member & admin) tetap berjalan normal, redirect sesuai role.

**Non-goals**: TIDAK mengubah logika rate limit login; TIDAK mengubah pesan teksnya; TIDAK menambah fitur baru.

---

# Task C — Sediakan jalur aman untuk ubah status/role akun (persiapan Fase 4/6)

**Latar belakang**: `status` dan `role` **sengaja TIDAK** ada di `$fillable` model `User` (proteksi mass-assignment, risiko A2). Akibatnya `User::update(['status' => 'nonaktif'])` atau `User::create([... 'role' => ...])` **diam-diam no-op** (tidak error, tidak tersimpan). Ini benar untuk keamanan, tapi merupakan **landmine untuk Fase 4** (admin kelola akun aktif/nonaktif) dan **Fase 6** (kelola role) — jika worker Fase 4 memakai `update(['status'=>...])`, perubahan akan gagal diam-diam.

**Tujuan**: menyiapkan mekanisme eksplisit + aman untuk mengubah `status`/`role` dan **mendokumentasikan constraint** ini supaya Fase 4/6 tidak tersandung.

**Lingkup (file yang disentuh)**:
- `app/Models/User.php`
- `context/schema.md` (hanya tambah catatan, jangan ubah skema)

**Perubahan**:
1. Di `app/Models/User.php`, tambahkan method bantu yang memakai `forceFill` (satu-satunya cara aman untuk field yang tidak fillable):
   ```php
   /**
    * Set account status. SAFE PATH for admins (Phase 4).
    * status is intentionally NOT mass-assignable (risk A2), so this
    * uses forceFill instead of update(). Never pass user input here.
    */
   public function setAccountStatus(AccountStatus $status): void
   {
       $this->forceFill(['status' => $status])->save();
   }
   ```
   - Tambahkan `use App\Enums\AccountStatus;` jika belum ada (sudah ada untuk cast).
2. Tambah komentar singkat di class `User` yang menegaskan: `status`/`role` tidak boleh diisi dari input user (A2); admin wajib pakai method/`forceFill`, bukan mass assignment.
3. Di `context/schema.md` bagian §2 `users`, tambahkan catatan (di bawah tabel kolom) kurang lebih:
   > **Catatan akses**: `role` & `status` TIDAK ada di `$fillable` (proteksi mass-assignment, risiko A2) — tidak boleh diisi dari input form. Admin mengubahnya lewat method khusus (`User::setAccountStatus()`) / `forceFill`, bukan `update()`.
4. **JANGAN** menambahkan `status`/`role` ke `$fillable`/`#[Fillable]`. Proteksi A2 tetap utuh.

**Acceptance (hasil teramati)**:
- `User::find($id)->setAccountStatus(AccountStatus::Nonaktif)` mengubah nilai `status` di DB menjadi `nonaktif` (verifikasi via `php artisan tinker` + `fresh()`).
- `User::create([... 'role' => UserRole::SuperAdmin])` dan `User::update(['status' => 'nonaktif'])` **TETAP** diabaikan (role=member, status tidak berubah) — A2 tidak dilonggarkan.
- `context/schema.md` memuat catatan akses di §2.

**Non-goals**: TIDAK membangun UI/admin kelola akun (itu Fase 4); TIDAK menambah method selain yang disebut; TIDAK mengubah migration/skema DB.

---

# Task D — Konsistensi bahasa pesan validasi (Indonesia)

**Latar belakang**: UI berbahasa Indonesia, tetapi pesan validasi bawaan Laravel berbahasa **Inggris** karena `APP_LOCALE=en` dan tidak ada file terjemahan `lang/id`. Terverifikasi: register dengan email duplikat menampilkan *"The email has already been taken."*. Sementara pesan custom di `LoginController`/`RegisterController` sudah Indonesia ("Kredensial tidak cocok", "Akun sedang dinonaktifkan") — tidak konsisten.

**Tujuan**: semua pesan validasi bawaan Laravel tampil dalam Bahasa Indonesia, konsisten dengan UI.

**Lingkup (file yang disentuh)**:
- `config/app.php` (locale default)
- `.env` (`APP_LOCALE`)
- File baru `lang/id/validation.php` dan `lang/id/auth.php` (buat jika folder `lang/` belum ada)

**Perubahan**:
1. Pastikan folder `lang/id/` ada (buat `lang/id/` bila belum). Jika folder `lang/` belum ada di root proyek, buat.
2. Buat `lang/id/validation.php` berisi terjemahan Bahasa Indonesia untuk setidaknya aturan yang dipakai saat ini: `required`, `email`, `unique`, `confirmed`, `min`, `max`, `string`. Contoh struktur (sesuaikan atribut & nilai):
   ```php
   return [
       'required' => ':attribute wajib diisi.',
       'email'    => ':attribute harus berupa alamat email yang valid.',
       'unique'   => ':attribute sudah terdaftar.',
       'confirmed'=> 'Konfirmasi :attribute tidak cocok.',
       'min'      => [
           'string' => ':attribute minimal :min karakter.',
       ],
       'max'      => [
           'string' => ':attribute maksimal :max karakter.',
       ],
       'string'   => ':attribute harus berupa teks.',
       'attributes' => [
           'nama' => 'Nama',
           'email' => 'Email',
           'telepon' => 'No. Telepon',
           'organisasi' => 'Organisasi',
           'password' => 'Password',
       ],
   ];
   ```
3. Buat `lang/id/auth.php` dengan setidaknya:
   ```php
   return [
       'failed' => 'Kredensial tidak cocok dengan catatan kami.',
       'password' => 'Password yang diberikan salah.',
       'throttle' => 'Terlalu banyak percobaan login. Coba lagi dalam :seconds detik.',
   ];
   ```
4. Set locale aplikasi ke Indonesia:
   - `config/app.php`: `'locale' => env('APP_LOCALE', 'id')`.
   - `.env`: `APP_LOCALE=id`.
5. Jalankan `php artisan config:clear` (dan `config:cache` bila di produksi) setelah perubahan `.env`.

**Acceptance (hasil teramati)**:
- Register dengan email duplikat → pesan tampil Bahasa Indonesia (mis. "Email sudah terdaftar."), bukan *"The email has already been taken."*.
- Register field kosong / password <8 / konfirmasi tidak sama → pesan Bahasa Indonesia.
- Login kredensial salah → pesan konsisten ("Kredensial tidak cocok dengan catatan kami.").

**Non-goals**: TIDAK mengubah teks UI halaman; TIDAK menambah aturan validasi baru; TIDAK menerjemahkan seluruh halaman (hanya pesan validasi/auth).

---

# Task E — Tambah Feature Tests untuk Auth & Role

**Latar belakang**: saat ini hanya ada 2 test default (`tests/Feature/ExampleTest.php`, `tests/Unit/ExampleTest.php`). Seluruh fungsionalitas Fase 2 (auth, role middleware, rate limit, mass assignment, journals) **tidak di-cover test**.

**Tujuan**: menambah feature test yang meng-cover happy path **dan** error path untuk auth & otorisasi.

**Lingkup (file yang disentuh)**:
- File test baru di `tests/Feature/` (mis. `AuthTest.php`, `RoleAuthorizationTest.php`, `JournalsPublicTest.php`)
- `tests/TestCase.php` (hanya jika perlu, jangan ubah yang berfungsi)

**Perubahan**:
1. Buat file test di `tests/Feature/` memakai `RefreshDatabase` dan (bila perlu) `DatabaseMigrations` atau seeder. Gunakan `php artisan make:test` bila mau.
2. Seeder: pastikan test punya akses ke user dengan role superadmin/admin_manager/member. Bisa via `User::factory()` (buat factory jika belum ada) **atau** panggil seeder dalam test. Pastikan cara yang dipakai konsisten.
3. Implementasikan test berikut (nama boleh disesuaikan, tetapi **semua skenario wajib ada**):

   **AuthTest**
   - `test_register_creates_member_and_redirects_to_dashboard`: POST `/register` data valid → user baru di DB ber-role `member`, status `aktif`, terautentikasi, redirect ke `/dashboard`.
   - `test_register_duplicate_email_returns_error`: register dua kali email sama → error pada `email`, tidak crash, tidak ada user duplikat.
   - `test_register_validates_required_fields`: kirim data tidak lengkap → error validasi (field `nama`, `email`, `telepon`, `password`).
   - `test_register_requires_min_password_length`: password < 8 karakter → error.
   - `test_register_requires_password_confirmation`: `password_confirmation` tidak sama → error.
   - `test_login_member_redirects_to_dashboard`: login member → redirect `/dashboard`.
   - `test_login_admin_redirects_to_admin_dashboard`: login admin/superadmin → redirect `/admin`.
   - `test_login_wrong_credentials_returns_error`: login email/password salah → error, tidak terautentikasi.
   - `test_login_rate_limit_blocks_after_five_attempts`: 6x login gagal → request ke-6 diblokir (untuk JSON: status `429`).
   - `test_login_nonaktif_account_blocked`: login akun berstatus `nonaktif` (set via `forceFill`) → **tidak** terautentikasi, redirect kembali ke `/login`, muncul pesan dinonaktifkan.
   - `test_logout_logs_out_and_redirects_home`: POST `/logout` → tidak terautentikasi, redirect `/`.

   **RoleAuthorizationTest**
   - `test_guest_redirected_from_admin`: GET `/admin` tanpa login → redirect ke `/login`.
   - `test_guest_redirected_from_dashboard`: GET `/dashboard` tanpa login → redirect ke `/login`.
   - `test_member_forbidden_from_admin`: login member → GET `/admin` → `403`.
   - `test_admin_forbidden_from_dashboard`: login admin → GET `/dashboard` → `403`.
   - `test_admin_can_access_admin_dashboard`: login admin → GET `/admin` → `200`.
   - `test_member_can_access_dashboard`: login member → GET `/dashboard` → `200`.
   - `test_role_not_mass_assignable`: `User::create([... 'role' => 'superadmin'])` → user tersimpan ber-role `member` (A2). Juga cek `update(['status'=>'nonaktif'])` tidak mengubah status.

   **JournalsPublicTest**
   - `test_public_journals_only_shows_aktif`: seed jurnal aktif + arsip → GET `/journals` hanya menampilkan jurnal berstatus `aktif`.
   - `test_public_journals_sorted_by_name`: GET `/journals` → urut abjad `nama`.
   - (Opsional) `test_public_journals_card_links_to_external_url`: pastikan `link_eksternal` muncul.

4. Jalankan seluruh suite sampai **hijau**.

**Acceptance (hasil teramati)**:
- `php artisan test` (atau `vendor/bin/phpunit`) **lulus semua** tanpa error.
- Test di atas mencakup happy path **dan** error path (validasi, 403, 429, blokir nonaktif, mass assignment).

**Non-goals**: TIDAK mengubah logika aplikasi hanya untuk membuat test lewat; TIDAK menambah test ke modul di luar Fase 2 (Programs/Events/News CRUD belum ada — itu Fase 3).

---

# Task F — Bersihkan code smell minor

**Latar belakang**: hasil review menemukan beberapa hal kecil yang perlu dirapikan agar konsisten dan mudah dipelihara.

**Tujuan**: membersihkan code smell minor tanpa mengubah perilaku.

**Lingkup (file yang disentuh)**:
- `app/Http/Controllers/Auth/RegisterController.php`
- `app/Http/Controllers/Auth/LoginController.php`

**Perubahan**:
1. **Magic number rate limit** — di `LoginController`, nilai `5` (batas percobaan) dan `60` (decay detik) adalah literal inline. Ekstrak ke konstanta bernama di class, mis.:
   ```php
   private const MAX_ATTEMPTS = 5;
   private const DECAY_SECONDS = 60;
   ```
   lalu pakai `self::MAX_ATTEMPTS` dan `self::DECAY_SECONDS`. (Boleh juga definisikan sebagai constant public/private sesuai gaya proyek.)
2. **Dead-code pesan duplicate email** — di `RegisterController::store()`, blok `catch (QueryException $e)` berisi pesan "Email sudah terdaftar. Gunakan email lain." Ini hampir tidak pernah terpacu karena aturan `unique:users,email` menangkap duplikat lebih dulu. **PERTAHANKAN** blok catch ini sebagai fallback race-condition (risiko C3), TAPI samakan pesannya agar konsisten dengan hasil dari Task D (jika Task D dipakai, pastikan pesan catch juga Bahasa Indonesia dan serupa dengan pesan validasi `unique`). Jangan menghapus bloknya.

**Acceptance (hasil teramati)**:
- Tidak ada literal `5`/`60` yang "terapung" di `LoginController` — memakai konstanta bernama.
- Blok `catch (QueryException)` di `RegisterController` tetap ada dan pesannya konsisten Bahasa Indonesia.
- Seluruh test dari Task E (jika sudah dikerjakan) tetap lulus.

**Non-goals**: TIDAK melakukan refactor besar; TIDAK mengubah perilaku auth; TIDAK menyentuh file di luar dua controller di atas.

---

## Checklist Sebelum Selesai

- [ ] Semua file konteks di §Pranala sudah dibaca.
- [ ] Task A–F dikerjakan berurutan; masing-masing acceptance terpenuhi.
- [ ] Perubahan sesuai scope discipline & konvensi penamaan; tidak ada modul di luar PRD.
- [ ] Proteksi A2 tetap utuh (status/role tidak bisa di-mass-assign dari input).
- [ ] Task E: `php artisan test` lulus semua.
- [ ] Dokumentasi yang tersentuh (`schema.md` pada Task C) sudah disinkronkan.
- [ ] `notes/progress.md` diperbarui jika ada perubahan status yang relevan.

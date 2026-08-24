# Task — Fase 2: Fondasi Backend

**Lingkup**: backend pertama. Migration, model, auth (manual, tanpa Breeze), role middleware, seeder.
**Tujuan**: fondasi database + auth + otorisasi role sesuai `schema.md`, dengan mitigasi risiko (`risiko.md`) diterapkan sejak awal.
**Cara pakai**: kerjakan subtask **secara berurutan** (2.1 → 2.9). Selesaikan satu, verifikasi acceptance-nya, baru lanjut.

---

## Pranala Dokumen (WAJIB dibaca berurutan sebelum mulai)

1. `context/PRD.md` — requirement & scope (fokus §2, §3.9, §4, §7.1, §8)
2. `context/glossary.md` — istilah baku
3. `context/architecture.md` — struktur, routing, layout, batas
4. `context/directory.md` — peta file/route/controller/view
5. `context/schema.md` — **acuan utama skema (wajib penuh — ini fase implementasinya)**
6. `context/risiko.md` — **wajib**: A1, A2, A5, A7, C1, C3, C6, E3 berlaku di fase ini
7. `notes/progress.md` — status progres
8. `context/rules.md` — aturan perilaku

---

## Aturan Fase Ini (wajib)

- **Auth**: manual. JANGAN install Laravel Breeze/Jetstream/Fortify.
- **Schema**: migration & model WAJIB mengikuti `context/schema.md` persis (nama tabel, kolom, enum, constraint).
- **Risiko wajib diterapkan** (dari `risiko.md`):
  - A1: authorization via middleware role di route, bukan cuma UI.
  - A2: `$fillable` minimal; `role`/`status`/`reviewer_id` TIDAK boleh diisi dari input form.
  - A5: semua POST pakai `@csrf`; logout via POST.
  - A7: rate limit login (dan register).
  - C1/C3: unique constraint DB (email, slug) + tangani duplicate entry.
  - C6: timezone WIB (`Asia/Jakarta`).
  - E3: seeder admin pakai password kuat/random — BUKAN `admin123`.
- **Verifikasi**: setiap subtask, jalankan acceptance check.

---

## Subtask 2.1 — Migration Database (sesuai `schema.md`)

**Konteks**: Laravel 12 sudah menyertakan migration default `0001_01_01_000000_create_users_table.php` (tabel users + password_reset_tokens + sessions). Tabel `users` di `schema.md` punya kolom tambahan (role, telepon, organisasi, status) — **edit migration default, JANGAN buat duplikat `users`**.

**Tindakan**:
1. **Edit** `database/migrations/0001_01_01_000000_create_users_table.php` — pada tabel `users`, tambahkan kolom sesuai `schema.md` §2:
   - `nama` (string, no) — perhatikan: default Laravel memakai `name`; ikuti `schema.md` (`nama`). Update `User` model & factory & seeder sesuai.
   - `role` (enum `superadmin|admin_manager|member`, default `member`)
   - `telepon` (string, nullable)
   - `organisasi` (string, nullable)
   - `status` (enum `aktif|nonaktif`, nullable, default `aktif`)
   - Pertahankan kolom default lain (email unique, password, timestamps, remember_token).
2. **Buat migration baru** untuk 7 tabel lain sesuai `schema.md` §3–§9:
   - `programs` (judul, deskripsi nullable, kategori, gambar nullable)
   - `journals` (nama, slug **unique**, deskripsi nullable, e_issn nullable, cover nullable, link_eksternal, status enum `aktif|arsip` default aktif)
   - `events` (judul, deskripsi nullable, tanggal_waktu datetime, lokasi nullable, poster nullable, info_kontak_pendaftaran nullable)
   - `publications` (member_id FK→users cascadeOnDelete, judul, kategori enum `tulisan|prestasi|produk|pkm`, file nullable, status enum `pending|approved|rejected` default pending, reviewer_id FK→users nullable nullOnDelete)
   - `organization_profile` (nama, deskripsi nullable, visi nullable, misi nullable, logo nullable)
   - `organization_structure` (nama_pengurus, jabatan, afiliasi nullable, foto nullable)
   - `news` (judul, isi, tanggal_publish datetime, gambar nullable)
3. Semua tabel: `$table->timestamps()`.
4. JANGAN membuat tabel `members`, `payments`, `event_registrations` — tidak ada di schema (risiko: jangan tambah di luar scope).

**Acceptance**:
- `php artisan migrate` jalan tanpa error.
- `php artisan migrate:status` — semua migration tercatat.
- Struktur kolom sesuai `schema.md` (cek via `php artisan tinker`: `Schema::getColumnListing('users')` dst).

---

## Subtask 2.2 — Model Eloquent

**Tindakan**:
1. **Update** `app/Models/User.php`:
   - `#[Fillable([...])]` → `['nama', 'email', 'password', 'telepon', 'organisasi']` — **TANPA** `role` & `status` (risiko A2: tidak bisa diisi via mass assignment dari input).
   - Cast: `role` → enum class (buat `App\Enums\UserRole` dengan case SuperAdmin/AdminManager/Member), `status` → enum (`App\Enums\AccountStatus`: Aktif/Nonaktif), `password` → hashed (pertahankan default).
   - Relasi: `publications()` (hasMany, member_id), `reviewedPublications()` (hasMany, reviewer_id).
2. **Buat model** (masing-masing `$fillable` minimal sesuai kolom publik):
   - `App\Models\Program`, `Journal`, `Event`, `Publication`, `OrganizationProfile`, `OrganizationStructure`, `News`.
   - `Publication`: enum cast kategori (`App\Enums\PublicationCategory`) & status (`App\Enums\PublicationStatus`); relasi `member()` belongsTo User, `reviewer()` belongsTo User.
   - `Journal`: cast status enum; jangan simpan logika di model (tetap model tipis).
3. JANGAN menambah method/scope yang tidak dipakai.

**Acceptance**:
- `php artisan tinker`:
  - `App\Models\User::create([...])` tanpa role → default `member`.
  - `App\Models\Publication::find(1)?->member` relasi jalan (setelah seeder 2.6).
  - Mass assignment role via `create(['role' => 'superadmin'])` **tidak** mengubah role (karena tidak di fillable).

---

## Subtask 2.3 — Timezone WIB (risiko C6)

**Tindakan**:
- `config/app.php`: ubah `'timezone' => 'UTC'` → `'timezone' => 'Asia/Jakarta'`.

**Acceptance**:
- `php artisan tinker`: `config('app.timezone')` = `Asia/Jakarta`; `now()->format('e')` = `Asia/Jakarta`.

---

## Subtask 2.4 — Auth Manual: Register, Login, Logout

**Konteks**: form register sudah ada di Fase 1 (statis). Sekarang jadikan fungsional.

**Tindakan**:
1. **RegisterController** (`App\Http\Controllers\Auth\RegisterController`):
   - `showRegistrationForm()` → view `auth.register` **atau** pakai view `register.index` yang sudah ada. Pilih: **pakai view `register.index` yang sudah ada** (jangan buat duplikat) — update view di langkah 4.
   - `store()`: validasi server:
     - `nama` required|string|max:255
     - `email` required|email|unique:users,email
     - `telepon` required|string|max:20
     - `organisasi` nullable|string|max:255
     - `password` required|confirmed|min:8
   - Simpan user dengan **hardcode** `role = UserRole::Member`, `status = AccountStatus::Aktif` — TIDAK dari input (risiko A2).
   - Setelah simpan: `auth()->login($user)` → redirect ke dashboard member (PRD §7.1: langsung aktif, langsung login).
   - Tangani `QueryException` duplicate email (race C3) → kembali ke form dengan pesan email sudah dipakai.
2. **LoginController** (`App\Http\Controllers\Auth\LoginController`):
   - `showLoginForm()` → view `auth.login` (yang sudah ada).
   - `store()`: `$request->validate([...])`; `Auth::attempt($credentials, $request->boolean('remember'))`.
   - Redirect: role `superadmin`/`admin_manager` → `admin.dashboard`; role `member` → dashboard member (route `member.dashboard` — lihat 2.5). Jika member `nonaktif` → jangan login (cek status), pesan akun dinonaktifkan.
   - **Rate limit** (risiko A7): `RateLimiter` — maks 5 percobaan per menit per email/IP; setelah lewat → `429` dengan pesan.
3. **Logout**: route `POST /logout` (bukan GET — risiko A5) → `auth()->logout()` + invalidate session + regenerate token → redirect home. View: tombol logout di header admin (dan dashboard member) berbentuk **form POST + @csrf**.
4. **Update view**:
   - `resources/views/register/index.blade.php`: tambah `action="{{ route('register') }}" method="POST"` + `@csrf` + `name` di tiap field + `@error` display; hapus komentar "STATIC SAMPLE" pada form (sekarang fungsional) — pertahankan komentar untuk data dummy lain.
   - `resources/views/auth/login.blade.php`: tambah `action` + `@csrf` + `@error`; tambah link "Belum punya akun? Daftar" → `route('register')`.
5. **Routes** di `routes/web.php`:
   - `GET /register` → `[RegisterController::class, 'showRegistrationForm']` (ganti closure)
   - `POST /register` → `[RegisterController::class, 'store']` name `register`
   - `GET /login` → `[LoginController::class, 'showLoginForm']` (ganti closure; **hapus duplikat** jika masih ada)
   - `POST /login` → `[LoginController::class, 'store']` name `login`
   - `POST /logout` → `[LoginController::class, 'destroy']` name `logout` (middleware auth)
6. JANGAN install package auth apa pun.

**Acceptance**:
- Register akun baru → masuk DB: role `member`, status `aktif`; otomatis login → dashboard member.
- Register dengan email sama → error validasi, tidak crash (duplicate handling).
- Login benar → redirect sesuai role; login salah 5× → rate limited (429).
- Member status nonaktif → tidak bisa login.
- Logout (tombol form POST) → kembali ke home; halaman yang butuh auth redirect ke login.

---

## Subtask 2.5 — Role Middleware & Route Protection (risiko A1)

**Tindakan**:
1. Buat middleware `App\Http\Middleware\EnsureUserHasRole` (alias `role`):
   - Terima parameter role yang diizinkan (variadic).
   - Jika user belum login → redirect `login`.
   - Jika `!in_array(auth()->user()->role->value, $roles)` → `abort(403)`.
   - Daftarkan di `bootstrap/app.php` (alias `role`).
2. **Grup admin**: bungkus SEMUA route `/admin/*` yang sudah ada dalam:
   ```php
   Route::prefix('admin')->name('admin.')->middleware(['auth', 'role:superadmin,admin_manager'])->group(function () { ... });
   ```
3. **Grup member**: tambah route dashboard member:
   ```php
   Route::middleware(['auth', 'role:member'])->group(function () {
       Route::get('/dashboard', fn () => view('member.dashboard'))->name('member.dashboard');
   });
   ```
   - Buat view `resources/views/member/dashboard.blade.php` — **placeholder minimal**: extends `layouts.app` (atau layout member jika dibuat), heading "Dashboard", teks "Konten dashboard member menyusul di Fase 4". Ditandai komentar.
4. **Redirect setelah login**: role admin → `/admin`; member → `/dashboard` (di LoginController).
5. JANGAN mengubah route mikro/publik lain.

**Acceptance**:
- Visitor akses `/admin` → redirect login.
- Member akses `/admin` → 403.
- Admin akses `/admin` → dashboard admin.
- Member akses `/dashboard` → view placeholder; admin akses `/dashboard` → 403 (karena role member saja).

---

## Subtask 2.6 — Seeder Data Awal

**Tindakan**:
1. `database/seeders/DatabaseSeeder.php` — panggil seeder baru:
   - `UserSeeder`: 1 Super Admin, 1–2 Admin Manager, 8–10 Member. **Password kuat/acak** (mis. `Str::random(16)` dicetak ke console, atau password env) — JANGAN `admin/admin123` (risiko E3). Email super admin: `superadmin@arps.org` (catat di komentar seeder).
   - `OrganizationProfileSeeder`: 1 baris `organization_profile` (nama = "Academics, Researchers, and Practitioners Society (ARPS)", deskripsi, visi, misi sampel).
   - `JournalSeeder`: 4 jurnal referensi dari PRD §3.6 (Semarak Ilmu, PIJAR, MOTOR, ATIKANOTO) — nama, slug, deskripsi, e_issn (boleh sampel), link_eksternal, status aktif.
   - `ContentSeeder` (opsional): 3–5 news, 3 events, 6 programs sampel.
2. Semua data sampel ditandai jelas sebagai seed (bukan data final).

**Acceptance**:
- `php artisan migrate:fresh --seed` jalan tanpa error.
- `php artisan tinker`: `App\Models\User::count()` > 10; role terdistribusi; journals 4; organization_profile 1.
- Login super admin (kredensial dari seeder) → `/admin` terbuka.

---

## Subtask 2.7 — Sambung Halaman Journals ke Data DB (bukti alur)

**Konteks**: keputusan — sambungkan 1 halaman sederhana ke data DB sebagai bukti alur. Pilih **Journals** (data sudah di-seed).

**Tindakan**:
1. `routes/web.php` — ganti closure `journals.index`:
   ```php
   Route::get('/journals', function () {
       $journals = App\Models\Journal::where('status', 'aktif')->orderBy('nama')->get();
       return view('journals.index', ['journals' => $journals]);
   })->name('journals.index');
   ```
   (Import `App\Models\Journal` di atas file; jangan inline FQCN berulang.)
2. `resources/views/journals/index.blade.php`:
   - Ganti 4 kartu statis dengan `@foreach ($journals as $journal)` → kartu: nama, e_issn, deskripsi singkat, cover placeholder `[ cover ]`, **link ke `$journal->link_eksternal`** (target `_blank`) — sesuai PRD §3.6 (klik → link keluar).
   - Hapus blok STATIC SAMPLE lama untuk journals (sekarang data nyata).
   - Pertahankan style `.journal-card` yang ada.
3. JANGAN mengubah microsite (`/journal/{slug}`) dan JANGAN menghapus view microsite.

**Acceptance**:
- `/journals` menampilkan 4 jurnal dari DB (bukan hardcode view).
- Klik kartu → membuka `link_eksternal` di tab baru.
- Halaman lain tetap STATIC SAMPLE (belum diubah — itu Fase 3).

---

## Subtask 2.8 — Verifikasi Fase 2

**Tindakan**:
1. `php artisan migrate:fresh --seed` — bersih, tanpa error.
2. Test manual via browser (`php artisan serve`):
   - Register akun baru → auto login → `/dashboard` terbuka (placeholder).
   - Logout → logout jalan.
   - Login member → `/admin` → 403.
   - Login super admin → `/admin` → dashboard admin.
   - Login 5× salah → rate limited.
   - `/journals` → 4 jurnal dari DB.
3. Cek risiko (checklist `risiko.md`):
   - `role`/`status` tidak bisa di-mass-assign (2.2).
   - Semua route admin dilindungi middleware (2.5).
   - Logout POST + CSRF (2.4).
   - Timezone WIB (2.3).
   - Unique email/slug di DB (2.1).

**Acceptance**:
- Semua skenario di atas berjalan sesuai.
- Tidak ada warning/error di log.

---

## Subtask 2.9 — Sinkronkan Dokumentasi

**Tindakan**:
1. `notes/progress.md`: tandai Fase 2 selesai — Auth (register/login/logout) 🟢, Migration/model 🟢, role middleware 🟢, seeder 🟢; Journals publik 🟢 (data DB); dashboard member 🟡 placeholder.
2. `context/directory.md`: tambah controller (`Auth/RegisterController`, `Auth/LoginController`), middleware, model, migration, view `member/dashboard`; hapus catatan "tidak ada migration".
3. `context/schema.md` / `context/risiko.md`: tidak berubah (sudah jadi acuan yang diimplementasikan).

**Acceptance**:
- `directory.md` mencerminkan kode aktual.
- `progress.md` mencatat hasil Fase 2.

---

## Checklist Akhir (sebelum menyatakan Fase 2 selesai)

- [ ] 2.1–2.9 semua selesai & acceptance terpenuhi.
- [ ] Migration & model sesuai `schema.md` persis; tidak ada tabel di luar schema (members/payments/event_registrations).
- [ ] Risiko diterapkan: A1 (middleware role), A2 (fillable aman), A5 (POST+CSRF), A7 (rate limit), C1/C3 (unique DB), C6 (WIB), E3 (password kuat).
- [ ] Auth manual — tidak ada Breeze/Jetstream/Fortify.
- [ ] Halaman di luar daftar subtask tidak diubah (home, about, events, publications, news, microsite, admin tetap asli — kecuali journals yang disambung di 2.7).
- [ ] Dokumentasi (`directory.md`, `progress.md`) sudah disinkronkan.

# Schema — Skema Database ARPS

Skema database untuk website ARPS. Acuan requirement & entitas: `PRD.md` §6. Aturan perilaku: `rules.md`.

**Catatan penting:** skema di bawah adalah **target implementasi** — AI worker yang membuat migration/model harus mengikuti dokumen ini. Keberadaan folder `database/migrations` dan progres implementasi DB dipantau di `notes/progress.md`.

---

## 1. Konvensi

- Engine: **InnoDB**, kolasi UTF-8 (`utf8mb4`).
- `id` = big integer auto-increment (kecuali dinyatakan lain).
- Timestamps `created_at` / `updated_at` di semua tabel (Laravel default).
- Enum role: `superadmin | admin_manager | member`.
- Enum status publications: `pending | approved | rejected`.

---

## 2. users (tabel tunggal pengguna web)

Semua pengguna web (member & admin) ada di **satu tabel `users`**. Tidak ada tabel `members` terpisah. `role` menentukan jenis akses; field profil member nullable.

| Kolom | Tipe | Null | Keterangan |
|---|---|---|---|
| id | bigint PK | no | |
| nama | string | no | Nama lengkap |
| email | string unique | no | Untuk login |
| password | string | no | Hash (Laravel `hashed` cast) |
| role | enum(`superadmin`,`admin_manager`,`member`) | no | Default `member` |
| telepon | string | **yes** | Profil member; null untuk admin |
| organisasi | string | **yes** | Lembaga/afiliasi; null untuk admin |
| foto | string | **yes** | Foto profil member; null jika tidak ada |
| status | enum(`aktif`,`nonaktif`) | **yes** | Khusus member; `aktif` default; null untuk admin |
| email_verified_at | timestamp | yes | |
| remember_token | string | yes | |
| created_at / updated_at | timestamp | yes | |

- **Relasi**: satu `users` → banyak `publications` (sebagai uploader & reviewer).
- **Tidak ada** `expired_at`, tidak ada relasi payment.
- Status aktif/nonaktif & reaktivasi dilakukan **manual oleh admin**.

> **Catatan akses**: `role` & `status` TIDAK ada di `$fillable` (proteksi mass-assignment, risiko A2) — tidak boleh diisi dari input form. Admin mengubahnya lewat method khusus (`User::setAccountStatus()`) / `forceFill`, bukan `update()`.

### Contoh migration
```php
Schema::create('users', function (Blueprint $table) {
    $table->id();
    $table->string('nama');
    $table->string('email')->unique();
    $table->string('password');
    $table->enum('role', ['superadmin', 'admin_manager', 'member'])->default('member');
    $table->string('telepon')->nullable();
    $table->string('organisasi')->nullable();
    $table->string('foto')->nullable();
    $table->enum('status', ['aktif', 'nonaktif'])->nullable()->default('aktif');
    $table->timestamp('email_verified_at')->nullable();
    $table->rememberToken();
    $table->timestamps();
});
```

---

## 3. programs

| Kolom | Tipe | Null | Keterangan |
|---|---|---|---|
| id | bigint PK | no | |
| judul | string | no | |
| deskripsi | text | yes | |
| kategori_id | bigint FK → categories.id | **yes** | `nullOnDelete` — jika kategori dihapus, program jadi null |
| gambar | string | yes | Path file |
| created_at / updated_at | timestamp | yes | |

### Contoh migration
```php
Schema::create('programs', function (Blueprint $table) {
    $table->id();
    $table->string('judul');
    $table->text('deskripsi')->nullable();
    $table->foreignId('kategori_id')->nullable()->constrained('categories')->nullOnDelete();
    $table->string('gambar')->nullable();
    $table->timestamps();
});
```

## 3a. categories

| Kolom | Tipe | Null | Keterangan |
|---|---|---|---|
| id | bigint PK | no | |
| nama | string unique | no | Akademik, Penelitian, Praktik/Profesional, Engineering, Sosial, Inovasi Teknologi |
| created_at / updated_at | timestamp | yes | |

### Contoh migration
```php
Schema::create('categories', function (Blueprint $table) {
    $table->id();
    $table->string('nama')->unique();
    $table->timestamps();
});
```

## 3b. technology_innovations

| Kolom | Tipe | Null | Keterangan |
|---|---|---|---|
| id | bigint PK | no | |
| judul | string | no | |
| deskripsi | text | yes | |
| gambar | string | yes | Path file |
| status | enum(`aktif`,`arsip`) | no | Default `aktif` |
| created_at / updated_at | timestamp | yes | |

### Contoh migration
```php
Schema::create('technology_innovations', function (Blueprint $table) {
    $table->id();
    $table->string('judul');
    $table->text('deskripsi')->nullable();
    $table->string('gambar')->nullable();
    $table->enum('status', ['aktif', 'arsip'])->default('aktif');
    $table->timestamps();
});
```

---

## 4. journals (katalog referensi)

Halaman `/journals` = katalog link keluar (Opsi A). Field final sesuai PRD §3.6.

| Kolom | Tipe | Null | Keterangan |
|---|---|---|---|
| id | bigint PK | no | |
| nama | string | no | Judul jurnal |
| slug | string unique | no | Subdomain: `namajurnal.arps.org` |
| deskripsi | text | yes | |
| e_issn | string | yes | |
| cover | string | yes | Path gambar sampul |
| link_eksternal | string | no | URL tujuan saat kartu diklik |
| status | enum(`aktif`,`arsip`) | no | Default `aktif` |
| created_at / updated_at | timestamp | yes | |

- Relasi: tanpa relasi ke entitas lain (referensi eksternal murni).
- Saat OJS aktif, `link_eksternal` diarahkan ke subdomain OJS.

### Contoh migration
```php
Schema::create('journals', function (Blueprint $table) {
    $table->id();
    $table->string('nama');
    $table->string('slug')->unique();
    $table->text('deskripsi')->nullable();
    $table->string('e_issn')->nullable();
    $table->string('cover')->nullable();
    $table->string('link_eksternal');
    $table->enum('status', ['aktif', 'arsip'])->default('aktif');
    $table->timestamps();
});
```

---

## 5. events

| Kolom | Tipe | Null | Keterangan |
|---|---|---|---|
| id | bigint PK | no | |
| judul | string | no | |
| deskripsi | text | yes | |
| tanggal_waktu | datetime | no | |
| lokasi | string | yes | Fisik/online |
| poster | string | yes | Path poster |
| info_kontak_pendaftaran | string | **yes** | Opsional, **bisa beda per event** (WA/email/link) |
| created_at / updated_at | timestamp | yes | |

- Tidak ada `event_registrations` — pendaftaran manual via kontak.

### Contoh migration
```php
Schema::create('events', function (Blueprint $table) {
    $table->id();
    $table->string('judul');
    $table->text('deskripsi')->nullable();
    $table->dateTime('tanggal_waktu');
    $table->string('lokasi')->nullable();
    $table->string('poster')->nullable();
    $table->string('info_kontak_pendaftaran')->nullable();
    $table->timestamps();
});
```

---

## 6. publications

| Kolom | Tipe | Null | Keterangan |
|---|---|---|---|
| id | bigint PK | no | |
| member_id | bigint FK → users.id | no | Uploader |
| judul | string | no | |
| deskripsi | text | yes | Deskripsi singkat karya |
| kategori | enum(`tulisan`,`prestasi`,`produk`,`pkm`) | no | |
| file | string | yes | Path file/konten (PDF/JPG/PNG/DOCX) |
| status | enum(`pending`,`approved`,`rejected`) | no | Default `pending` |
| reviewer_id | bigint FK → users.id | **yes** | Admin yang mereview |
| created_at / updated_at | timestamp | yes | |

- Alur: member upload → `pending` → admin approve/reject → tayang publik jika `approved`.

### Contoh migration
```php
Schema::create('publications', function (Blueprint $table) {
    $table->id();
    $table->foreignId('member_id')->constrained('users')->cascadeOnDelete();
    $table->string('judul');
    $table->text('deskripsi')->nullable();
    $table->enum('kategori', ['tulisan', 'prestasi', 'produk', 'pkm']);
    $table->string('file')->nullable();
    $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
    $table->foreignId('reviewer_id')->nullable()->constrained('users')->nullOnDelete();
    $table->timestamps();
});
```

---

## 7. organization_profile (single-row)

Profil organisasi — **satu baris** (pengaturan). Sesuai struktur kode admin `admin.organization.profile`.

| Kolom | Tipe | Null | Keterangan |
|---|---|---|---|
| id | bigint PK | no | Hanya 1 baris dipakai |
| nama | string | no | Nama organisasi |
| deskripsi | text | yes | Deskripsi singkat |
| visi | text | yes | |
| misi | text | yes | |
| logo | string | yes | Path logo |
| created_at / updated_at | timestamp | yes | |

### Contoh migration
```php
Schema::create('organization_profile', function (Blueprint $table) {
    $table->id();
    $table->string('nama');
    $table->text('deskripsi')->nullable();
    $table->text('visi')->nullable();
    $table->text('misi')->nullable();
    $table->string('logo')->nullable();
    $table->timestamps();
});
```

---

## 8. organization_structure (multi-baris pengurus)

Struktur pengurus — **banyak baris** (CRUD). Sesuai struktur kode admin `admin.structure`.

| Kolom | Tipe | Null | Keterangan |
|---|---|---|---|
| id | bigint PK | no | |
| nama_pengurus | string | no | |
| jabatan | string | no | |
| afiliasi | string | yes | Institusi |
| foto | string | yes | Path foto |
| created_at / updated_at | timestamp | yes | |

- Tidak ada relasi ke `users` — pengurus adalah data (bisa non-member/admin).

### Contoh migration
```php
Schema::create('organization_structure', function (Blueprint $table) {
    $table->id();
    $table->string('nama_pengurus');
    $table->string('jabatan');
    $table->string('afiliasi')->nullable();
    $table->string('foto')->nullable();
    $table->timestamps();
});
```

---

## 9. news / articles

| Kolom | Tipe | Null | Keterangan |
|---|---|---|---|
| id | bigint PK | no | |
| judul | string | no | |
| isi | text | no | |
| tanggal_publish | datetime | no | |
| gambar | string | yes | |
| created_at / updated_at | timestamp | yes | |

### Contoh migration
```php
Schema::create('news', function (Blueprint $table) {
    $table->id();
    $table->string('judul');
    $table->text('isi');
    $table->dateTime('tanggal_publish');
    $table->string('gambar')->nullable();
    $table->timestamps();
});
```

---

## 10. Ringkasan Relasi

- `users` 1 ──< `publications` (member_id = uploader)
- `users` 1 ──< `publications` (reviewer_id = reviewer)
- `categories` 1 ──< `programs` (kategori_id, nullOnDelete)
- `technology_innovations` independen (tidak ada FK).
- Tidak ada relasi antar tabel konten lain (journals/events/news) — semuanya independen kecuali programs→categories.
- `organization_profile` single-row; `organization_structure` multi-baris independen.

## 11. Hal yang TIDAK Ada (jangan dibuat)

- ❌ Tabel `members` terpisah — pakai `users` (role + status).
- ❌ Tabel `payments` / `transactions` — keanggotaan gratis.
- ❌ Tabel `event_registrations` — pendaftaran event manual.
- ❌ Kolom `expired_at` — tanpa masa berlaku.
- ❌ Entitas OJS/jurnal-internal — jurnal adalah referensi eksternal (`link_eksternal`).

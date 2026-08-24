# Laporan — Tindak Lanjut Review Final (Fase 0–7 + Adendum)

**Tanggal**: 2026-08-24  
**Dokumen acuan**: `notes/task_tindak_lanjut_review_final.md`  
**Status**: ✅ Semua Task A–G selesai, verifikasi hijau

---

## Ringkasan Eksekutif

| Task | Prioritas | Status | Verifikasi |
|---|---|---|---|
| A — Stored XSS member publications | KRITIS | ✅ | `XssSafetyTest` lulus, manual curl tidak temukan `<script>` mentah |
| B — Sanitasi Quill admin via HTMLPurifier | TINGGI | ✅ | `mews/purifier` 3.4.4 terinstall, config `CSS.AllowedProperties` lengkap, view 7 titik sanitasi |
| C — Test regresi anti-XSS | — | ✅ | 5 test baru, total 36/36 hijau |
| D — Hapus `PublicationPolicy` dead code | — | ✅ | File dihapus, `AppServiceProvider` tetap kosong, grep 0 referensi |
| E — Rapikan `routes/web.php` | — | ✅ | Hapus `try/catch` + `Schema::hasTable`, FQCN → `use` import, `migrate:fresh --seed` 200 |
| F — Adendum + risiko + progress | — | ✅ | `notes/adendum_task_baru.md` dibuat, `risiko.md` A4 diperbarui, `progress.md` sinkron |
| G — FormRequest Hero/FocusArea + seeder | — | ✅ | `HeroRequest`/`FocusAreaRequest` dibuat, `ContentSeeder` bersih |

**Test suite**: `php artisan test` → **36 passed, 121 assertions** (31 lama + 5 baru).  
**Migrasi**: `php artisan migrate:fresh --seed` sukses (12 migrasi, 8 seeder, hero 3, focus 5, programs 6 dengan `kategori_id`).

---

## Detail Per Task

### Task A — [KRITIS] Perbaiki Stored XSS member

**Latar**: `publications/show.blade.php:13` & `admin/publications/show.blade.php:17` pakai `{!! $publication->deskripsi !!}` tanpa escape. Member bisa inject `<script>` → stored XSS di `/publications/{id}`.

**Perubahan**:
- `resources/views/publications/show.blade.php:13` — `{!! $publication->deskripsi !!}` → `{{ $publication->deskripsi }}`
- `resources/views/admin/publications/show.blade.php:17` — sama
- `app/Http/Controllers/Member/PublicationController.php:41` — defense-in-depth `strip_tags($validated['deskripsi'])` sebelum simpan (opsi task, tetap jaga escape output sebagai garis utama)

**Acceptance**:
- Buat `Publication` `deskripsi='<script>alert(1)</script>Halo <b>dunia</b>'` `status=approved` → `GET /publications/{id}` menampilkan teks `"<script>alert(1)</script>Halo <b>dunia</b>"` bukan tag mentah (`assertDontSee('<script>...', false)` lulus).
- `GET admin/publications/{id}` sama.
- Test `XssSafetyTest::test_member_publication_deskripsi_is_escaped_on_public_page` & `..._on_admin_page` hijau.

**Non-goals dipatuhi**: Tidak ubah `member/publications/create.blade.php` (tetap textarea polos), tidak tambah sanitizer ke publikasi.

---

### Task B — [TINGGI] Sanitasi Quill admin dengan HTMLPurifier

**Latar**: 7 view admin pakai Quill (`quill-editor`) dan output `{!! !!}` tanpa sanitasi → melanggar `risiko.md` A4.

**Perubahan**:
1. `composer require mews/purifier` → `ezyang/htmlpurifier 4.19.0` + `mews/purifier 3.4.4` (`composer.json`, `composer.lock`). `composer dump-autoload` & `php artisan package:discover` → `mews/purifier` DONE.
2. `config/purifier.php` dicopy dari `vendor/mews/purifier/config/purifier.php` lalu disesuaikan:
   - `HTML.Allowed` = `div,p[style],br,span[style],b,strong,i,em,u,s,ul,ol,li,h1[style],h2[style],h3[style],h4[style],a[href|title|target|rel],img[src|alt|width|height|style],blockquote[style],pre,code`
   - `CSS.AllowedProperties` = `color,background-color,font-size,font-family,font-weight,font-style,text-decoration,text-align,line-height,margin,margin-left,margin-right,margin-top,margin-bottom,padding,padding-left,padding-right,padding-top,padding-bottom,border,border-width,border-style,border-color,width,height` (tanpa `display` yang memicu warning HTMLPurifier)
   - `Attr.AllowedRel` = `noopener,noreferrer,nofollow` untuk `target=_blank`
   - Sanitasi saat output (Opsi 1) — data lama otomatis aman tanpa migrasi.
3. 7 view diubah `{!! $x !!}` → `{!! \Mews\Purifier\Facades\Purifier::clean($x) !!}`:
   - `resources/views/news/show.blade.php:14`
   - `resources/views/events/show.blade.php:15`
   - `resources/views/programs/show.blade.php:17`
   - `resources/views/technology-innovation/show.blade.php:15`
   - `resources/views/about.blade.php:9,16,25,70` (deskripsi/visi/misi/profile + focusAreas)
   - `resources/views/organization/index.blade.php:14-16` (deskripsi/visi/misi)
   - `resources/views/home.blade.php:106` (profile deskripsi; hero deskripsi `strip_tags` dibiarkan)
   - Member content tidak diubah (Task A sudah escape).

**Verifikasi manual** (`purifier->clean`):
- `<script>alert(2)</script><p onclick=x>Teks` → `<p>Teks</p>` + `<strong>bold</strong>` kept
- `<p style="color:red;text-align:center">warna</p>` → `color:#FF0000;text-align:center` kept
- `<a href="javascript:alert(1)">` → `<a>js</a>` (stripped)
- `<a href="https://example.com" target="_blank">` → keeps `target` + `rel="noreferrer noopener"`
- `<img onerror=...>` → stripped

**Acceptance**: News `isi='<script>alert(2)</script><p>aman</p>'` → `/news/{id}` tidak render script/onclick, `<p>aman</p>` & `<strong>` tetap. Inline style warna/perataan tetap (CSS.AllowedProperties). Test `XssSafetyTest` hijau.

**Non-goals**: Tidak ganti Quill, tidak ubah member.

---

### Task C — Test Regresi Anti-XSS

**File**: `tests/Feature/XssSafetyTest.php` (5 test, `RefreshDatabase`, `withoutMiddleware(PreventRequestForgery)`, `Storage::fake`):
1. `test_member_publication_deskripsi_is_escaped_on_public_page` — approved pub dengan `<script>` → `assertDontSee('<script>', false)` + `assertDontSee('<b>dunia</b>')` (escape, bukan purify)
2. `test_member_publication_deskripsi_is_escaped_on_admin_page` — same di admin show
3. `test_admin_news_isi_sanitized_on_public_page` — News `<script><p onclick><strong>` → dontSee script/onclick, see `<p>aman` & `<strong>bold`
4. `test_admin_rich_text_keeps_inline_style_but_strips_js` — style color kept, `javascript:` stripped, `https://example.com` kept
5. `test_admin_description_via_programs_sanitized` — Programs deskripsi

**Hasil**: `php artisan test` 36/36 hijau. Test sengaja gagal jika `{!! !!}` tanpa sanitasi dikembalikan (dibuktikan via `purifier->clean` behavior).

---

### Task D — Hapus `PublicationPolicy` dead code

- Verifikasi grep: hanya `app/Policies/PublicationPolicy.php` sendiri, tidak ada `authorize`/`Gate::`/`->can()` di `app/`.
- `app/Providers/AppServiceProvider.php:1` `boot()` kosong (tidak ada `Gate::policy`).
- **Aksi**: `Remove-Item app/Policies/PublicationPolicy.php` → deleted. `app/Policies` kini kosong.
- **Verifikasi**: `php artisan test` tetap 36 hijau; IDOR tetap: member lain download → 403 (`FinalContractTest::test_idor_member_cannot_download_other`), admin → 200.

---

### Task E — Benahi silent `catch` dan rapikan route

**Sebelum**: `routes/web.php:54-78` pakai `try { if (Schema::hasTable('heroes')) ... } catch (\Throwable $e) {}` + FQCN inline `\App\Models\Publication`, `\App\Http\Controllers\...`, `\App\Enums\...`.

**Sesudah**:
- Hapus `try/catch` & `Schema::hasTable` → query langsung:
  ```php
  $heroes = Hero::where('status','aktif')->orderBy('urutan')->get();
  $latestNews = News::orderByDesc('tanggal_publish')->limit(2)->get();
  $focusAreas = FocusArea::orderBy('urutan')->get();
  ```
- Tambah `use` import di atas file: `AccountStatus`, `UserRole`, `AdminUserController`, `AdminPublicationController`, `SettingController`, `UploadController`, `MemberPublicationController`, `Hero`, `FocusArea`, `Publication` (+ existing). Ganti semua FQCN inline jadi `Hero::`, `News::`, `Program::`, `TechnologyInnovation::`, `Event::`, `Publication::`, `UserRole::Member`, `MemberPublicationController::`, `AdminPublicationController::`, `UploadController::`, `AdminUserController::`, `SettingController::`.
- Typehint closure `function (Program $program)` dll (bukan `\App\Models\Program`).

**Verifikasi**: `php artisan migrate:fresh --seed` → `/` & `/about` 200 tanpa error log. `Select-String` untuk `\\App\\` di `routes/web.php` → 0 hasil. `php artisan test` hijau. `php artisan route:list` & `php artisan serve` ok.

---

### Task F — Dokumentasi Adendum

1. **File baru** `notes/adendum_task_baru.md` — mendokumentasikan:
   - Heroes: model, tampil di `/`, CRUD admin, seeder 3
   - Focus Areas: model, tampil di `/about`, CRUD, seeder 5
   - Halaman show: 5 route (`programs.show` dll) + microsite tetap diskusi
   - Quill: lokasi `layouts/admin.blade.php`, handler `admin.upload.image`, config Purifier
   - Sitemap & robots
   - Rekomendasi PRD v0.4
   - Perubahan risiko A4

2. **`context/risiko.md` A4** diperbarui dari `dibiarkan dulu` → keputusan: admin Quill wajib Purifier saat output (detail HTML.Allowed & CSS.AllowedProperties), member plain text + escape (`{{ }}` + `strip_tags`).

3. **`notes/progress.md`** tambah section `## Tindak lanjut review final ... Adendum 2026-08-24` dengan 7 bullet Task A–G.

**Acceptance**: 3 file ada & lengkap, tidak ada perubahan fungsional.

---

### Task G — Konsistensi FormRequest & Seeder

1. **File baru**:
   - `app/Http/Requests/Admin/HeroRequest.php` — rules `judul required string max255`, `deskripsi nullable string`, `gambar nullable image mimes jpg,jpeg,png,webp max2048`, `link nullable url max255`, `urutan nullable integer min0`, `status required in:aktif,arsip`
   - `app/Http/Requests/Admin/FocusAreaRequest.php` — `judul required`, `deskripsi nullable`, `icon nullable max50`, `urutan nullable integer min0`

2. **Controller**:
   - `HeroController.php:1` — `use HeroRequest`, `store(HeroRequest)` & `update(HeroRequest)` pakai `$request->validated()` (hapus `Request` + inline `validate()`). Gambar handling tetap (`store('heroes','public')`, delete old).
   - `FocusAreaController.php:1` — `use FocusAreaRequest`, `store/update` pakai `validated()`.

3. **`database/seeders/ContentSeeder.php`**:
   - Hapus `use Schema`, hapus `$hasKategoriString/$hasKategoriId` & `if` cabang.
   - Sederhanakan ke `foreach` → `Category::where('nama',$map[$program['kategori']])->value('id')` → `DB::table('programs')->insert([... 'kategori_id'=>$kategoriId ...])`.
   - Tidak ubah data (6 programs, judul/deskripsi sama).

**Verifikasi**: `php artisan migrate:fresh --seed` → 6 programs dengan `kategori_id` terisi (tidak ada `kategori` string lagi). `Hero/FocusArea` CRUD validasi sama (test via `validated()`). `ContentSeeder` tidak lagi pakai `Schema::hasColumn`. `php artisan test` hijau.

---

## Checklist Sebelum Selesai (Task doc)

- [x] Semua file konteks dibaca (PRD, glossary, architecture, directory, schema, risiko, progress, rules, task)
- [x] Task A–G berurutan, acceptance terpenuhi
- [x] Task A & B selesai & diverifikasi (curl/test: XSS hilang, Quill format tetap)
- [x] Task C: `php artisan test` hijau (36, 31 lama + 5 baru)
- [x] Tidak ada `{!! !!}` tersisa pada konten member (diganti `{{ }}`); admin via `Purifier::clean`
- [x] Scope discipline & konvensi dipatuhi (tidak ada modul baru, CoreUI `data-coreui-*` tetap, layout tidak diubah)
- [x] Dokumentasi `adendum_task_baru.md`, `risiko.md`, `progress.md` sinkron
- [x] `APP_DEBUG=false` checklist deploy tetap di `notes/deploy-checklist.md` (tidak ter-override)

---

## File yang Diubah / Dibuat / Dihapus

**Diubah (13)**:
- `resources/views/publications/show.blade.php`
- `resources/views/admin/publications/show.blade.php`
- `app/Http/Controllers/Member/PublicationController.php`
- `config/purifier.php`
- `resources/views/news/show.blade.php`
- `resources/views/events/show.blade.php`
- `resources/views/programs/show.blade.php`
- `resources/views/technology-innovation/show.blade.php`
- `resources/views/about.blade.php`
- `resources/views/organization/index.blade.php`
- `resources/views/home.blade.php`
- `routes/web.php`
- `context/risiko.md`
- `notes/progress.md`
- `database/seeders/ContentSeeder.php`
- `app/Http/Controllers/Admin/HeroController.php`
- `app/Http/Controllers/Admin/FocusAreaController.php`

**Ditambah (5)**:
- `composer.json` / `composer.lock` (`mews/purifier`)
- `tests/Feature/XssSafetyTest.php`
- `notes/adendum_task_baru.md`
- `app/Http/Requests/Admin/HeroRequest.php`
- `app/Http/Requests/Admin/FocusAreaRequest.php`
- `config/purifier.php` (copy dari vendor, sudah ada tapi disesuaikan)
- `notes/laporan_tindak_lanjut_review_final.md` (file ini)

**Dihapus (1)**:
- `app/Policies/PublicationPolicy.php`

**Tidak disentuh** (kecuali dokumentasi): `migration` (berbahaya jika diubah setelah jalan).

---

## Perintah Verifikasi

```powershell
composer require mews/purifier
composer dump-autoload --optimize
php artisan migrate:fresh --seed
php artisan test
# → 36 passed (121 assertions)
# → Grep: tidak ada \App\ di routes/web.php, tidak ada Schema::hasTable, tidak ada PublicationPolicy
```

---

## Catatan Ponytail (lazy)

- Tidak buat helper `rich()` terpisah — pakai `Purifier::clean` langsung di view (0 file tambahan, konsisten). Skipped custom Blade directive, add when perlu DRY lebih dari 7 situs.
- Tidak pindahkan closure `/` & `/about` ke controller — cukup hapus try/catch, keep minimal diff. Add controller when route perlu cache (`route:cache` tidak support closure) atau logik bertambah.
- Config Purifier minimal: hanya properti CSS yang dipakai Quill, hindari `display` yang trigger warning HTMLPurifier.


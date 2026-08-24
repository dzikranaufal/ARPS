# Task — Tindak Lanjut Hasil Review Final (Fase 0–7 + Adendum)

**Tujuan dokumen**: menindaklanjuti quality review akhir terhadap seluruh proyek ARPS. Setiap task berasal dari temuan audit yang **sudah diverifikasi** oleh reviewer (termasuk bukti serangan XSS end-to-end). Kerjakan berurutan (A → G). Task A & B adalah **prioritas keamanan** — jangan dilompati.

> **Status temuan**: XSS member→publik telah dibuktikan (inject `<script>` pada deskripsi karya → dirender mentah di halaman publik `/publications/{id}`). Baca bukti/kode yang dirujuk, terapkan perbaikan, lalu penuhi acceptance. Jangan ragu bertanya sebelum mengubah kode bila ada yang tidak jelas.

---

## Pranala Dokumen (WAJIB dibaca berurutan sebelum mulai)

1. `context/PRD.md` — requirement & scope (fokus §3.7, §8.2)
2. `context/glossary.md` — istilah baku
3. `context/architecture.md` — struktur, routing, layout
4. `context/directory.md` — peta file/route/controller/view
5. `context/schema.md` — skema database
6. `context/risiko.md` — **wajib**: A3, A4 (keputusan rich text → sanitasi), B1–B4, C2, D1–D3, E1
7. `notes/progress.md` — status progres
8. `context/rules.md` — aturan perilaku
9. `notes/task_tindak_lanjut_review_final.md` — dokumen ini

---

## Aturan (ringkas — detail di rules.md)

- **Scope discipline**: jangan bangun payment / event-registration online / OJS; jangan hapus microsite; jangan ganti tabel `users`.
- **JANGAN mengubah perilaku fungsional** di luar yang diminta tiap task (kecuali untuk memperbaiki bug XSS yang menjadi tujuan task ini).
- **Tema**: CoreUI admin (`data-coreui-*`) vs custom CSS publik; jangan campur `data-bs-*` di area CoreUI.
- **Verifikasi**: setiap perubahan fungsional WAJIB diverifikasi (jalankan aplikasi / test / curl). Task B wajib `composer require` (install paket baru) — verifikasi setelahnya.
- **Konflik file konteks**: prioritas `PRD.md` > `architecture.md`/`schema.md` > `directory.md`/`notes/progress.md` > `rules.md` > `glossary.md`. Laporkan konflik, jangan pilih diam-diam.

---

# Task A — [KRITIS] Perbaiki Stored XSS pada konten member (Publications)

**Latar belakang**: halaman publik `resources/views/publications/show.blade.php` (baris ~13) menampilkan deskripsi karya dengan `{!! $publication->deskripsi !!}` **tanpa escape**. `deskripsi` di-input **member** (bebas, validasi hanya `string|max:2000`). Terverifikasi: karya dengan deskripsi `<script>alert(1)</script><b>bold</b>` yang berstatus `approved` dirender **mentah** di `/publications/{id}` → **stored XSS**. Member bisa eksekusi JS arbitrer untuk semua pengunjung begitu karyanya di-approve. Halaman admin `admin/publications/show.blade.php` juga `{!! !!}` pada deskripsi yang sama.

**Konteks penting**: form upload member (`resources/views/member/publications/create.blade.php`) memakai **`<textarea>` polos** (TIDAK ada rich text editor). Artinya **men-escape output TIDAK menghilangkan fitur apa pun** — ini fix yang paling aman untuk konten member.

**Tujuan**: pastikan konten yang di-input member selalu tampil sebagai **teks polos** (ter-escape), tidak pernah sebagai HTML mentah.

**Lingkup (file yang disentuh)**:
- `resources/views/publications/show.blade.php`
- `resources/views/admin/publications/show.blade.php`
- (opsional, defense-in-depth) `app/Http/Controllers/Member/PublicationController.php`

**Perubahan**:
1. `resources/views/publications/show.blade.php`: ganti
   ```blade
   {!! $publication->deskripsi !!}
   ```
   menjadi
   ```blade
   {{ $publication->deskripsi }}
   ```
2. `resources/views/admin/publications/show.blade.php`: lakukan hal yang sama untuk `{!! $publication->deskripsi !!}` → `{{ $publication->deskripsi }}`.
3. (Opsional, disarankan) Di `Member\PublicationController::store`, tambahkan `strip_tags` pada `deskripsi` sebelum disimpan sebagai lapisan pertahanan kedua (mis. `'deskripsi' => strip_tags($validated['deskripsi'] ?? null)`). Ini opsional karena escape output sudah cukup; hanya bila ingin menghapus HTML dari sumber.
4. JANGAN mengubah judul/kategori/field lain; JANGAN mengubah `member/publications/create.blade.php`.

**Acceptance (hasil teramati)**:
- Buat karya (member) dengan `deskripsi = '<script>alert(1)</script>Halo <b>dunia</b>'`, set `status='approved'`, lalu buka `/publications/{id}` → halaman menampilkan teks **"`<script>alert(1)</script>Halo <b>dunia</b>`"** (terlihat sebagai teks, tag tidak dirender). Verifikasi via `curl`/browser: `grep -o '<script>alert(1)</script>'` pada body **tidak** menemukan tag mentah.
- Halaman `admin/publications/{id}` juga menampilkan teks, bukan HTML mentah.
- Test anti-XSS baru (Task C) lulus.

**Non-goals**: TIDAK mengubah editor konten admin (Quill) — itu Task B; TIDAK menambah sanitizer ke publikasi.

---

# Task B — [TINGGI] Sanitasi konten rich text admin (Quill) dengan HTMLPurifier

**Latar belakang**: konten admin di-input lewat editor rich text **Quill** (`class="quill-editor"`) pada modul News (`isi`), Programs (`deskripsi`), Events (`deskripsi`), Journals (`deskripsi`), Technology Innovations (`deskripsi`), Heroes (`deskripsi`), Focus Areas (`deskripsi`), Organization Profile / Settings (`deskripsi`/`visi`/`misi`). Semua di-output dengan `{!! !!}` **tanpa sanitasi**. Ini melanggar `risiko.md` A4: keputusan "jika memakai rich text editor, WAJIB sanitasi whitelist (mis. HTMLPurifier)". Terverifikasi: tidak ada paket sanitizer di `composer.json`, dan `<script>`/event handler dari admin akan dirender mentah. Risiko nyata bila akun admin dikompromikan; juga melanggar A4.

**Tujuan**: sanitasi HTML whitelist pada semua output rich text admin sehingga skrip/event-handler dinonaktifkan, tapi format aman (paragraf, heading, list, bold, link, gambar) tetap tampil. **Escape polos TIDAK dipakai di sini** karena akan merusak format rich text yang memang disengaja — gunakan sanitasi whitelist.

> **KEPUTUSAN (dikonfirmasi user): Opsi 1 — sanitasi saat output** (data disimpan apa adanya, dibersihkan saat ditampilkan via `Purifier::clean`). Keunggulan: data lama di DB langsung aman tanpa migrasi, rich text tetap tampil. Konsekuensi yang harus dikelola: sedikit beban CPU per-render (diabaikan di skala ini) dan perlu tuning config CSS agar format inline Quill (warna teks, ukuran font, perataan) tetap dipertahankan.

**Lingkup (file yang disentuh)**:
- `composer.json` / `composer.lock` (tambah paket)
- `config/purifier.php` (hasil publish, sesuaikan bila perlu)
- View yang saat ini memakai `{!! !!}` pada konten **admin** (daftar lengkap di bawah)
- (opsional) satu helper/directive Blade untuk `Purifier::clean`

**Perubahan**:
1. Install HTMLPurifier: jalankan `composer require mews/purifier` lalu `php artisan vendor:publish --provider="Mews\Purifier\PurifierServiceProvider"`.
2. Terapkan sanitasi **pada saat output** (di view) supaya data lama di DB ikut aman. Cara yang disarankan: gunakan `\Mews\Purifier\Facades\Purifier::clean($value)`. Bila ingin rapi, daftarkan helper/Blade directive bernama `rich($value)` (satu tempat) yang memanggil `Purifier::clean`, lalu pakai di semua situs di bawah. (Pilih satu cara konsisten — jangan campur.)
3. Ganti `{!! $x !!}` menjadi `{!! rich($x) !!}` (atau setara) pada SEMUA situs konten admin berikut:
   - `resources/views/news/show.blade.php` → `{!! $news->isi !!}`
   - `resources/views/events/show.blade.php` → `{!! $event->deskripsi !!}`
   - `resources/views/programs/show.blade.php` → `{!! $program->deskripsi !!}`
   - `resources/views/technology-innovation/show.blade.php` → `{!! $innovation->deskripsi !!}`
   - `resources/views/about.blade.php` → `{!! $profile->deskripsi !!}`, `{!! $profile->visi !!}`, `{!! $profile->misi !!}`, `{!! $area->deskripsi !!}`
   - `resources/views/organization/index.blade.php` → `{!! $profile->deskripsi !!}`, `{!! $profile->visi !!}`, `{!! $profile->misi !!}`
   - `resources/views/home.blade.php` → `{!! $profile->deskripsi !!}` (hero deskripsi sudah `strip_tags` — boleh dibiarkan, tapi lebih baik disamakan ke `rich()` untuk konsistensi)
4. Pastikan default config Purifier mengizinkan tag umum yang dipakai Quill: `p, br, strong, em, ul, ol, li, h1-h4, a, img, blockquote, pre` dan **menonaktifkan** `script`, `iframe`, `object`, `embed`, semua event-handler (`on*`), dan `javascript:` URL. Sesuaikan `config/purifier.php` bila perlu.
   - **PENTING (agar tampilan Quill tidak rusak):** Quill memakai inline style (`<span style="...">`) untuk warna teks, ukuran font, dan perataan. Atur `CSS.AllowedProperties` di config Purifier agar mengizinkan properti yang dipakai Quill (mis. `color`, `background-color`, `font-size`, `font-family`, `text-align`, `line-height`, `margin`, `padding`, `border`, `width`, `height`) supaya format itu tetap tampil. Tanpa ini, warna/perataan yang dipakai admin bisa hilang saat disanitasi. Verifikasi dengan konten yang memakai warna & perataan (lihat Acceptance).
   - Periksa juga apakah `a` dengan `target="_blank"`/`rel` perlu diizinkan (`HTML.TargetAllowed`/`Attr.AllowedRel`) agar link buka tab baru tetap berfungsi.
5. **JANGAN** mengubah output konten member (Task A sudah menangani escape).

**Acceptance (hasil teramati)**:
- Simpan konten admin (mis. News `isi`) berisi `<script>alert(1)</script><p onclick="x()">Teks</p>` → halaman publik `news/{id}` **tidak** merender `<script>` / `onclick` mentah; teks tampil, tag aman (mis. `<p>`/`<strong>`) tetap tampil.
- **Format Quill tetap tampil:** konten yang memakai bold (`<strong>`), italic (`<em>`), list (`<ul>/<ol>/<li>`), link (`<a>`), dan gambar (`<img>`) tampil dengan format seperti sebelumnya.
- **Inline style Quill (warna/perataan) tetap tampil:** konten dengan warna teks / `text-align` tetap ditampilkan (verifikasi bahwa `CSS.AllowedProperties` sudah diatur). Jika ada format yang hilang, tuning config — jangan ubah kode view.
- Berkas format (paragraf, bold, link) dari Quill tetap tampil normal.
- Test anti-XSS baru (Task C) lulus.

**Non-goals**: TIDAK menurunkan/menghapus fitur rich text; TIDAK mengganti Quill; TIDAK mengubah konten member (Task A).

---

# Task C — Tambah Test Regresi Anti-XSS

**Latar belakang**: tidak ada test yang menegaskan output konten di-escape/disanitasi — justru inilah yang membiarkan XSS lolos hingga ditemukan audit.

**Tujuan**: menambah feature test yang gagal jika XSS kembali muncul (Task A & B).

**Lingkup (file yang disentuh)**:
- File test baru `tests/Feature/XssSafetyTest.php` (atau tambah method ke file yang ada)

**Perubahan** — tulis test (pakai `RefreshDatabase`, `Storage::fake`, dan `withoutMiddleware(PreventRequestForgery::class)` seperti test lain):
1. **Member publikasi** (menguji Task A):
   - Buat member + karya `status='approved'` dengan `deskripsi = '<script>alert(1)</script>'`.
   - `GET /publications/{id}` → `assertDontSee('<script>alert(1)</script>', false)` (body tidak memuat tag mentah).
2. **Konten admin News** (menguji Task B):
   - Buat News dengan `isi = '<script>alert(2)</script><p>aman</p>'` (langsung via model).
   - `GET /news/{id}` → `assertDontSee('<script>alert(2)</script>', false)`.
   - (opsional) `assertSee('<p>aman</p>', false)` bila sanitasi mempertahankan tag aman — sesuaikan dengan perilaku Purifier.
3. (Opsional) uji konten admin Events/Programs dengan pola yang sama.

**Acceptance (hasil teramati)**:
- `php artisan test` semua hijau (test baru + 31 test lama).
- Test di atas **benar-benar gagal** jika `{!! !!}` tanpa sanitasi dikembalikan (buktikan sebentar sebelum fix, atau yakinkan dengan logic).

**Non-goals**: TIDAK menambah test untuk fitur yang tidak ada (heroes/focus-areas belum wajib — lihat Task F).

---

# Task D — Hapus `PublicationPolicy` yang tidak terpakai (dead code)

**Latar belakang**: `app/Policies/PublicationPolicy.php` didefinisikan tapi **tidak pernah dipanggil** — tidak ada `$this->authorize()`, `Gate::`, atau `->can()` di controller mana pun (terverifikasi via grep). Otorisasi nyata sudah berjalan lewat: route middleware (`role:...`) + cek inline kepemilikan di controller (`Member\PublicationController::download` cek `auth()->id() === member_id`; route publik cek `status==='approved'`). Policy ini murni dead code dan sumber kebenaran kedua yang tidak dipakai.

**Tujuan**: menghapus dead code sehingga tidak ada dua sumber kebenaran yang membingungkan.

**Lingkup (file yang disentuh)**:
- `app/Policies/PublicationPolicy.php` (hapus)
- Cek `app/Providers/AppServiceProvider.php` — pastikan tidak ada `Gate::policy` yang merujuknya (saat ini `boot()` kosong — tidak ada).

**Perubahan**:
1. Hapus file `app/Policies/PublicationPolicy.php`.
2. Pastikan tidak ada referensi ke `PublicationPolicy` di seluruh `app/` (grep). Jika ada `Gate::policy`/`authorize`, pindahkan ke cek inline atau hapus.
3. JANGAN mengubah controller yang sudah berfungsi (otentikasi inline & middleware tetap).

**Acceptance (hasil teramati)**:
- File `app/Policies/PublicationPolicy.php` tidak ada; tidak ada referensi `PublicationPolicy` di `app/`.
- `php artisan test` tetap hijau (31 + test baru).
- Otorisasi berfungsi seperti sebelumnya: member lain unduh karya → 403; admin → 200.

**Non-goals**: TIDAK menulis ulang otorisasi; TIDAK mengubah controller.

---

# Task E — Benahi silent `catch` dan rapikan route publik

**Latar belakang**: di `routes/web.php`, route `/` dan `/about` memakai `try { ... } catch (\Throwable $e) {}` kosong + `Schema::hasTable('heroes')`/`hasTable('news')`/`hasTable('focus_areas')` untuk meng-guard query. Ini **menelan semua exception diam-diam** (termasuk DB error/query error) sehingga kegagalan tersembunyi; dan guard `hasTable` hanya relevan sebelum migrasi (bukan skenario production — deploy selalu `migrate` dulu). Banyak FQCN inline (`\App\Models\X`, `\App\Http\Controllers\...`) dipakai di tengah route padahal sudah ada `use` import di atas.

**Tujuan**: menghilangkan penanganan error yang menyembunyikan masalah; route publik lebih bersih.

**Lingkup (file yang disentuh)**:
- `routes/web.php`

**Perubahan**:
1. Di route `/` dan `/about`: **hapus blok `try/catch (\Throwable $e) {}` dan guard `Schema::hasTable(...)`**. Ganti menjadi query langsung:
   - `/`: `$heroes = Hero::where('status','aktif')->orderBy('urutan')->get(); $latestNews = News::orderByDesc('tanggal_publish')->limit(2)->get(); $profile = OrganizationProfile::first();`
   - `/about`: `$profile = OrganizationProfile::first(); $focusAreas = FocusArea::orderBy('urutan')->get();`
   - (Model yang dipakai sudah ter-`use` di atas file — tambahkan `use App\Models\Hero; use App\Models\FocusArea;` bila belum.)
2. Ganti FQCN inline di `routes/web.php` (mis. `\App\Models\Publication`, `\App\Http\Controllers\Member\PublicationController`, `\App\Enums\...`) dengan `use` import di atas file, konsisten dengan style yang sudah ada.
3. JANGAN mengubah logika query lain; JANGAN memindahkan route ke controller (kecuali ingin — opsional).

**Acceptance (hasil teramati)**:
- `php artisan migrate:fresh --seed` lalu `php artisan serve` → `/`, `/about` tetap 200 tanpa error di log.
- Tidak ada `try/catch (\Throwable` kosong dan `Schema::hasTable` di `routes/web.php`.
- Tidak ada FQCN inline `\App\...` di body route (semua lewat `use`).
- `php artisan test` tetap hijau.

**Non-goals**: TIDAK mengubah behavior halaman; TIDAK menambahkan caching; TIDAK memindahkan semua closure ke controller (kecuali Anda memilihnya secara konsisten).

---

# Task F — Dokumentasikan adendum "Task Baru" (hero, focus-areas, halaman show, Quill) + keputusan A4

**Latar belakang**: fitur Heroes (carousel), Focus Areas (about), halaman detail/show (programs, events, news, technology-innovation, publications), sitemap, dan editor rich text **Quill** ditandai "Task Baru" di `directory.md`/`progress.md`, tetapi **tidak ada dokumen requirement-nya** di PRD maupun task fase mana pun. Ini menyalahi `rules.md §3` ("NEVER menambah modul di luar PRD tanpa instruksi eksplisit") **kecuali** memang ada instruksi eksplisit yang belum tercatat. Selain itu, penggunaan Quill mengubah keputusan `risiko.md` A4 (semula "plain text sampai ada keputusan").

**Tujuan**: mendokumentasikan adendum ini (requirement + keputusan) agar konsisten dan tidak dianggap scope violation, serta mencatat keputusan rich-text-sanitasi.

**Lingkup (file yang disentuh)**:
- File baru `notes/adendum_task_baru.md`
- `context/risiko.md` (perbarui catatan A4)
- `notes/progress.md` (tambahkan catatan)

**Perubahan**:
1. Buat `notes/adendum_task_baru.md` yang mendokumentasikan (jelas, ringkas):
   - **Heroes**: model `heroes` (judul, deskripsi, gambar, link, urutan, status aktif/arsip), tampil di `/` sebagai carousel, admin CRUD.
   - **Focus Areas**: model `focus_areas` (judul, deskripsi, icon, urutan), tampil di `/about`, admin CRUD.
   - **Halaman detail/show**: `programs.show`, `technology-innovation.show`, `events.show`, `news.show`, `publications.show` (khusus `approved`), `journals` tetap katalog.
   - **Editor Quill**: dipakai untuk konten deskripsi/isi modul admin; menghasilkan HTML rich text.
   - **Sitemap & robots**: `/sitemap.xml` + `public/robots.txt`.
   - Tandai bagian mana yang **belum** tercantum di PRD dan rekomendasikan apakah perlu ditambahkan ke PRD atau ditandai "di luar scope PRD tapi disetujui" (butuh konfirmasi user).
2. `context/risiko.md` A4: perbarui keputusan menjadi: rich text editor dipakai (Quill) untuk konten admin → **WAJIB sanitasi whitelist HTMLPurifier** (sudah diterapkan di Task B). Konten member tetap **plain text + escape output** (Task A).
3. `notes/progress.md`: tambahkan catatan bahwa adendum sudah didokumentasikan.

**Acceptance (hasil teramati)**:
- `notes/adendum_task_baru.md` ada dan lengkap.
- `risiko.md` A4 mencerminkan keputusan baru (sanitasi whitelist utk admin, escape utk member).
- Tidak ada fitur yang berubah (dokumentasi saja).

**Non-goals**: TIDAK menghapus/menambah fitur; TIDAK mengubah kode.

---

# Task G — Konsistensi kecil: FormRequest untuk Hero/FocusArea & bersihkan workaround seeder

**Latar belakang**: modul "Task Baru" (`HeroController`, `FocusAreaController`) memakai `$request->validate()` inline, sementara modul Fase 3 memakai FormRequest — tidak seragam. Selain itu `ContentSeeder` penuh cabang `Schema::hasColumn` (SQLite vs MySQL) dan ada dua migration yang menangani drop kolom `kategori` (`add_kategori_id_to_programs_table` + `drop_kategori_column_from_programs`) — rapuh.

**Tujuan**: menyamakan pola dan mengurangi kerapuhan tanpa mengubah perilaku.

**Lingkup (file yang disentuh)**:
- `app/Http/Controllers/Admin/HeroController.php`, `FocusAreaController.php`
- File baru `app/Http/Requests/Admin/HeroRequest.php`, `FocusAreaRequest.php`
- `database/seeders/ContentSeeder.php`

**Perubahan**:
1. Buat `HeroRequest` & `FocusAreaRequest` (pola sama seperti `JournalRequest`/`ProgramRequest`: `authorize(): bool` → `return true`, `rules()` memuat validasi yang sekarang ada inline). Pindahkan validasi di `store`/`update` Hero & FocusArea ke FormRequest (panggil `$request->validated()`).
2. `ContentSeeder`: hilangkan cabang `Schema::hasColumn('programs', 'kategori')` / `kategori_id`. Karena setelah migrasi final kolom yang ada adalah `kategori_id`, cukup selalu isi `kategori_id` (cari via `Category::where('nama', $map[...])->value('id')`). JANGAN mengubah isi data seed.
3. JANGAN menyentuh migration yang sudah jalan (menghapus migration setelah dijalankan = berbahaya); hanya bersihkan seeder.

**Acceptance (hasil teramati)**:
- `php artisan migrate:fresh --seed` jalan tanpa error; data seed sama seperti sebelumnya (6 program, kategori terisi).
- Hero/FocusArea CRUD tetap berfungsi (validasi sama).
- `ContentSeeder` tidak lagi memakai `Schema::hasColumn` untuk `kategori`.
- `php artisan test` tetap hijau.

**Non-goals**: TIDAK mengubah migration yang sudah dijalankan; TIDAK mengubah data seed; TIDAK merombak controller lain.

---

## Checklist Sebelum Selesai

- [ ] Semua file konteks di §Pranala sudah dibaca.
- [ ] Task A–G dikerjakan berurutan; masing-masing acceptance terpenuhi.
- [ ] **Task A & B selesai & diverifikasi** — XSS member & admin hilang (lihat acceptance A/B).
- [ ] Task C: `php artisan test` hijau (test baru + 31 test lama).
- [ ] Tidak ada `{!! !!}` yang tersisa pada konten member; konten admin lewat sanitasi whitelist.
- [ ] Perubahan sesuai scope discipline & konvensi; tidak ada modul baru di luar yang diminta.
- [ ] Dokumentasi (`adendum_task_baru.md`, `risiko.md`, `progress.md`) sudah disinkronkan.
- [ ] Cek deploy: `APP_DEBUG=false` saat produksi (E1) — sudah ada di `notes/deploy-checklist.md`; pastikan tidak ter-override saat build.

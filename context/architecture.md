# Architecture — Website ARPS

Dokumen arsitektur teknis. Acuan utama requirement & scope: `PRD.md`. Istilah baku: `glossary.md`.

---

## 1. Ringkasan Arsitektur

| Aspek | Keputusan |
|---|---|
| Bentuk aplikasi | **Monolith** Laravel — frontend & backend satu aplikasi |
| Rendering | Server-side **Blade** (tidak ada SPA/frontend JS terpisah) |
| UI framework | **CoreUI** (tema admin) + custom CSS untuk situs publik; Bootstrap 5 sebagai basis grid |
| Build tool | **Vite** (`vite.config.js`) |
| Database | MySQL |
| Auth | Laravel native auth (guard default `web`), tabel `users` |
| Storage | Local disk (`storage/app/public`), `storage:link`; future: object storage S3-compatible |
| Status | Proyek dikerjakan dalam fase: frontend (tampilan) lalu fungsionalitas. Status progres: `notes/progress.md` |

---

## 2. Struktur Direktori Aplikasi

```
app/
  Http/
    Controllers/
      Controller.php              # base controller
      Admin/JournalController.php # STUB sementara (data palsu)
  Models/
    User.php                      # satu-satunya model; tabel users
  Providers/
    AppServiceProvider.php

bootstrap/
config/
database/
  factories/
  seeders/
public/
  vendors/                        # CoreUI, simplebar, chart.js
resources/
  views/                          # semua template Blade
  js/app.js
  css/app.css
routes/
  web.php                         # semua route (publik + admin + microsite)
  console.php
storage/
tests/
```

Detail peta file/route ada di `directory.md`.

---

## 3. Routing

Semua route didefinisikan di `routes/web.php`. Pola:

| Prefix | Route name prefix | Contoh | Jenis |
|---|---|---|---|
| `/` (tanpa prefix) | tanpa prefix | `home`, `about`, `events.index`, `journals.index` | **Publik** — situs luar ARPS |
| `/journal/{slug}` | `journal.` | `journal.home`, `journal.archives`, `journal.guidelines` | **Microsite** — material diskusi, **bukan deliverable** |
| `/admin` | `admin.` | `admin.dashboard`, `admin.journals.index` | **Admin** — CoreUI |

**Aturan penamaan route:** route publik tanpa prefix; route admin selalu `admin.<nama>`; route microsite selalu `journal.<nama>`.

**Microsite `/journal/{slug}`:** memakai path parameter `{slug}` sebagai pengganti subdomain. Komentar di `web.php` menegaskan bahwa ini bisa ditukar ke `Route::domain('{journal}.arps.org')` tanpa mengubah view/controller. Microsite **dipertahankan sebagai material diskusi** — jalur kanonik website adalah **link keluar** (lihat PRD §3.6).

---

## 4. Layout & Tema

Empat layout di `resources/views/layouts/`:

| Layout | File | Dipakai untuk | Konten |
|---|---|---|---|
| `app` | `layouts/app.blade.php` | Situs publik | `partials/public/navbar` + `@yield('content')` + `partials/public/footer` |
| `admin` | `layouts/admin.blade.php` | Halaman admin | `partials/sidebar` + `partials/header` + `@yield('content')` + `partials/footer` |
| `journal-site` | `layouts/journal-site.blade.php` | Microsite jurnal | `partials/journal-site/header` + `@yield('content')` + footer; **tanpa** navbar utama ARPS (sengaja self-contained) |
| `auth` | `layouts/auth.blade.php` | Halaman login/register | Area sempit untuk form auth |

**Aturan tema penting:**
- Halaman publik memakai `layouts.app`.
- Halaman admin memakai `layouts.admin` (CoreUI, sidebar).
- Microsite memakai `layouts.journal-site`.
- Halaman auth memakai `layouts.auth`.
- **Jangan campur atribut**: CoreUI memakai `data-coreui-*` (bukan `data-bs-*`). JS bundle CoreUI memakai namespace sendiri.

---

## 5. Komponen & Partial

`resources/views/partials/`:
- `sidebar.blade.php` — sidebar admin (CoreUI). Sebagian link `href="#"` (placeholder) menunggu route; Dashboard, Journals, Organization Profile, Structure ter-wire ke route.
- `header.blade.php`, `footer.blade.php` — header/footer admin.
- `public/navbar.blade.php`, `public/footer.blade.php` — navbar/footer situs publik.
- `journal-site/header.blade.php` — header microsite jurnal.

---

## 6. Controller & Model

- Hanya **satu** controller nyata: `Admin/JournalController.php` — **stub sementara**, mengembalikan data palsu (`collect`) untuk keperluan layout. Method `store/update/destroy` hanya redirect dengan pesan stub.
- Hanya **satu** model: `User.php` (tabel `users`). Atribut `role`, `telepon`, `organisasi`, `status` mengikuti `schema.md`.
- **Migration** dibuat sesuai `schema.md` (folder `database/migrations` diisi saat backend).

---

## 7. Titik Integrasi OJS

- Jurnal ARPS sendiri kelak di-hosting di **OJS terpisah** (di luar repo Laravel), tiap jurnal di subdomain `namajurnal.arps.org`.
- Website Laravel hanya menyediakan **katalog link keluar** (`/journals`) — seluruh halaman jurnal disediakan OJS.
- Link keluar menunjuk ke jurnal mitra eksternal; saat OJS aktif, `link_eksternal` diarahkan ke subdomain OJS.
- Setup OJS, DNS wildcard, dan hosting OJS **di luar scope aplikasi ini** (PRD §9).

---

## 8. Storage & Upload

- Fase awal: local disk Laravel, `storage:link` wajib saat deploy.
- Upload Publications dibatasi 10MB/file, jenis PDF/JPG/PNG/DOCX, validasi double-layer (server + aplikasi). Detail di PRD §8.2.
- Upgrade ke object storage S3-compatible dimungkinkan dengan mengganti filesystem driver.

---

## 9. Batas Arsitektur (jangan dilanggar)

- **Jangan bangun** payment/subscription, event-registration online, atau OJS di dalam aplikasi ini — semua di luar scope (PRD §9).
- **Jangan hapus** microsite journal — material diskusi (PRD §3.6).
- **Jangan** mengganti `users` menjadi `members` sebagai nama tabel — tabel tetap `users` (default Laravel), `role` menentukan akses (PRD §6).
- Rendering server-side Blade; jangan pindah ke SPA tanpa keputusan baru.

# Rules — Aturan Development (untuk AI Coding Agent)

Aturan perilaku untuk AI worker yang mengerjakan repo ARPS. Bacaan wajib **sebelum** memulai task apa pun. Acuan utama requirement: `PRD.md`.

---

## 1. Bacaan Wajib (Wajib dibaca, berurutan, sebelum mulai task)

Worker WAJIB membaca file berikut **secara berurutan** sebelum mengerjakan task:

1. `context/PRD.md` — acuan utama requirement, scope, & keputusan.
2. `context/glossary.md` — istilah baku (hindari salah paham).
3. `context/architecture.md` — struktur, routing, layout, batas arsitektur.
4. `context/directory.md` — peta file/route/controller/view yang ada.
5. `context/schema.md` — skema database (wajib bila menyentuh DB/migration/model).
6. `context/risiko.md` — daftar masalah potensial & mitigasi (wajib: cek kode terhadap daftar ini).
7. `notes/progress.md` — status progres per halaman/modul (mana yang selesai / belum).
8. `context/rules.md` — aturan perilaku ini.

`notes/task.md` (bila ada) berisi task spesifik dan merujuk ke rules.md ini — jangan menganggap task.md menggantikan bacaan di atas.

---

## 2. Standar Kata Kunci

- **MUST / WAJIB** — aturan mutlak; melanggar = bug.
- **SHOULD / SEBAIKNYA** — rekomendasi; langgar hanya dengan alasan kuat.
- **NEVER / JANGAN PERNAH** — dilarang keras.

---

## 3. Scope Discipline

- **NEVER** bangun payment/subscription, event-registration online, atau OJS di dalam repo ini — semua di luar scope (PRD §9).
- **NEVER** menghapus microsite journal (`/journal/{slug}`, folder `resources/views/journal-site/`) — material diskusi, bukan deliverable (PRD §3.6).
- **NEVER** mengganti nama tabel `users` menjadi `members` — tabel tetap `users`; `role` menentukan akses (PRD §6, `schema.md`).
- **NEVER** menambah modul/fitur di luar yang tercantum di PRD tanpa instruksi eksplisit.
- Jangan mengerjakan hal di luar task yang diberikan (tidak menambah validasi, telemetri, atau abstraksi "sekalian").

---

## 4. Status Frontend vs Backend

- Proyek dikerjakan dalam fase **frontend (tampilan)** lalu fungsionalitas (CRUD, auth, approval). Fase aktif tercatat di `notes/progress.md`.
- Konten yang statis/sampel dianggap **placeholder untuk layout**. Saat mengerjakan tampilan baru:
  - **WAJIB** memakai konten sampel yang jelas + komentar pembatas `{{-- STATIC SAMPLE ... --}}`, supaya backend dev tahu itu harus diganti dengan `@foreach` dkk.
  - **JANGAN** menulis ulang halaman yang sudah ada tanpa alasan — periksa `directory.md` & `notes/progress.md` dulu.
- Controller yang masih stub (mis. `Admin/JournalController.php`) **JANGAN** dianggap final — data palsu hanya untuk layout.

---

## 5. Konvensi Penamaan

- **Route**: publik tanpa prefix; admin `admin.<nama>`; microsite `journal.<nama>`. Ikuti pola yang sudah ada di `routes/web.php`.
- **View**: letakkan di `resources/views/` mengikuti struktur folder yang ada (`publications/`, `events/`, `admin/`, `journal-site/`, `layouts/`, `partials/`).
- **Layout**: situs publik → `layouts.app`; admin → `layouts.admin`; auth → `layouts.auth`; microsite → `layouts.journal-site`. JANGAN membuat layout baru kalau yang ada sudah memadai.
- **Controller**: `app/Http/Controllers/` dengan namespace sesuai folder.
- **Model**: mengikuti `schema.md`.

---

## 6. Tema CoreUI vs Bootstrap

- Admin memakai **CoreUI** dengan atribut `data-coreui-*`. **NEVER** mencampur `data-bs-*` (bootstrap) di area CoreUI.
- Situs publik memakai custom CSS dengan basis grid Bootstrap 5.
- Periksa `architecture.md` §4 sebelum menyentuh layout/tema.

---

## 7. Standar Kode

- Blade: ikuti pola template & partial yang ada; gunakan `@stack('styles')` / `@stack('scripts')` untuk konten per-halaman.
- PHP: ikuti PSR-12 & konvensi Laravel.
- **NEVER** menulis ulang bagian yang berfungsi; ubah minimal & presisi.
- Setiap perubahan fungsional yang signifikan **WAJIB** diverifikasi (jalankan aplikasi / test), bukan hanya "sepertinya benar".

---

## 8. Perilaku Saat Berganti AI Worker

Rules.md ini dibuat agar **perilaku konsisten walau AI worker berganti**. Setiap worker WAJIB:
- Membaca seluruh file di §1 sebelum bertindak.
- Tidak berasumsi isi file dari namanya — baca dulu.
- Mengikuti scope discipline (§3) dan konvensi (§5) tanpa perlu ditanya ulang.

Jika ada konflik antara file konteks, urutan prioritas: `PRD.md` > `architecture.md`/`schema.md` > `directory.md`/`notes/progress.md` > `rules.md` > `glossary.md`. Laporkan konflik jika ditemukan — jangan diam-diam memilih.

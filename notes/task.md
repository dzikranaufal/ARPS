# Task — Instruksi Pekerjaan (per-task)

File ini berisi **instruksi task untuk AI worker** pada satu siklus kerja. Bukan pengganti rules — **WAJIB baca `context/rules.md` §1 (urutan bacaan) sebelum mengerjakan isi task ini.**

> Template ini dipakai per task. Isi bagian "Task Spesifik" sesuai pekerjaan yang sedang dikerjakan. Bagian "Pranala" & "Aturan" di bawah tetap berlaku untuk semua task.

---

## Pranala Dokumen (wajib dibaca sesuai urutan)

1. `context/PRD.md` — acuan utama requirement & scope
2. `context/glossary.md` — istilah baku
3. `context/architecture.md` — struktur, routing, layout, batas
4. `context/directory.md` — peta file/route/controller/view
5. `context/schema.md` — skema database (jika menyentuh DB/model/migration)
6. `context/risiko.md` — daftar masalah potensial & mitigasi (cek kode terhadap daftar ini)
7. `notes/progress.md` — status progres per halaman/modul
8. `context/rules.md` — aturan perilaku ini

---

## Aturan (ringkas — detail di rules.md)

- **Scope discipline**: jangan bangun payment / event-registration online / OJS; jangan hapus microsite; jangan ganti tabel `users`.
- **Fase frontend**: konten baru yang statis = placeholder, tandai dengan komentar `{{-- STATIC SAMPLE ... --}}`.
- **Tema**: CoreUI admin (`data-coreui-*`) vs custom CSS publik; jangan campur `data-bs-*` di area CoreUI.
- **Penamaan**: route publik tanpa prefix; admin `admin.*`; microsite `journal.*`. Ikuti pola yang ada.
- **Verifikasi**: perubahan fungsional signifikan wajib diverifikasi (jalankan aplikasi/test).
- **Konflik file konteks**: prioritas `PRD.md` > `architecture.md`/`schema.md` > `directory.md`/`notes/progress.md` > `rules.md` > `glossary.md`. Laporkan konflik, jangan pilih diam-diam.

---

## Rencana Fase (disimpan sementara — belum dirinci jadi task)

> Status: menunggu pembahasan risiko production sebelum dirinci. Setiap fase nanti dipecah jadi task detail (CRUD mencakup create/read/update/delete lengkap).

- **Fase 0 — Konsolidasi Frontend**: rapikan duplikasi route `/login`, sinkronkan sidebar admin, bersihkan placeholder jadi konten sampel konsisten.
- **Fase 1 — Lengkapi Halaman Publik (frontend)**: Register, Programs, Technology Innovation, Organization (+ direktori member), Contact. Semua konten statis sampel.
- **Fase 2 — Fondasi Backend**: migration + model (sesuai `schema.md`), auth (register/login/logout), role middleware, seeder awal.
- **Fase 3 — CRUD Modul Konten (Admin Manager)**: Programs, Journals (katalog + link keluar), Events, News, Organization (profile + structure), Technology Innovation.
- **Fase 4 — Keanggotaan & Member Dashboard**: dashboard member (profil, status), admin kelola akun (aktif/nonaktif), direktori member publik.
- **Fase 5 — Publications & Approval Flow**: upload karya member, antrian approval admin (pending → approved/rejected), tampilan publik + filter kategori.
- **Fase 6 — Super Admin & Pengaturan**: kelola admin user & role, pengaturan sistem/branding.
- **Fase 7 — Polish & Launch**: SEO, responsive, test, seed final, dokumentasi deploy, storage:link, backup.

---

## Task Spesifik

<!-- Isi di bawah ini per task yang sedang dikerjakan. Contoh: -->

- **Tujuan**: ...
- **Lingkup (file yang disentuh)**: ...
- **Acceptance criteria** (hasil yang teramati): ...
- **Non-goals** (yang TIDAK dikerjakan): ...

---

## Checklist Sebelum Selesai

- [ ] Semua file konteks di §Pranala sudah dibaca.
- [ ] Perubahan sesuai scope discipline & konvensi penamaan.
- [ ] Tidak ada modul di luar PRD yang ditambahkan.
- [ ] Konten statis baru ditandai sebagai placeholder.
- [ ] Perubahan fungsional sudah diverifikasi.

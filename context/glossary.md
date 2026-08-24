# Glossary — Istilah Baku ARPS

Daftar istilah & keputusan terminologi. Acuan requirement: `PRD.md`. Bacalah untuk menghindari salah paham (beberapa istilah telah berubah dari versi lama).

---

## Istilah Inti

| Istilah | Makna | Catatan |
|---|---|---|
| **ARPS** | Academics, Researchers, and Practitioners Society | Nama perkumpulan |
| **Member** | Semua pengguna web yang terdaftar (hasil register) | **Bukan** hanya yang membayar — keanggotaan gratis |
| **Register** | Aksi visitor mendaftar → menjadi Member | **Menggantikan istilah lama "Membership"** — gratis, tanpa bayar, tanpa masa berlaku |
| **Visitor** | Pengunjung web sebelum login/register | **Bukan role sistem** — hanya status, tanpa perlakuan khusus |
| **Super Admin** | Role admin tertinggi | Kelola user/role + pengaturan + semua modul konten |
| **Admin Manager** | Role admin konten | Kelola modul konten + akun member + approval; bisa lebih dari 1 akun |
| **OJS** | Open Journal Systems (PKP) | Aplikasi **terpisah** di hosting untuk jurnal ARPS; **di luar scope repo Laravel** |
| **Microsite** | Halaman `/journal/{slug}` di repo ini | **Material diskusi, bukan deliverable** — jalur kanonik = link keluar |

---

## Modul / Halaman

| Istilah | Makna |
|---|---|
| **Programs** | Modul CRUD program (akademik, penelitian, praktik, engineering, sosial, inovasi) |
| **Technology Innovation** | Modul CRUD inisiatif inovasi teknologi |
| **Publications** | Wadah karya member (tulisan, prestasi, produk, PkM) — upload → pending → approve/reject |
| **Events** | Info kegiatan; pendaftaran manual via kontak (bukan online) |
| **Journals** | Katalog referensi + link keluar — **bukan** sistem submission |
| **News / Articles** | Berita & artikel |
| **Organization** | Profil + struktur pengurus organisasi |
| **Contact** | Info kontak resmi ARPS |

---

## Status & Alur

| Istilah | Makna |
|---|---|
| **status akun** | `aktif` / `nonaktif` pada tabel `users`; diubah **manual oleh admin**; data tidak dihapus |
| **reaktivasi** | Mengembalikan akun `nonaktif` → `aktif`; **manual oleh admin** (tidak ada alur bayar ulang) |
| **status publications** | `pending` / `approved` / `rejected` |
| **kategori publications** | `tulisan` / `prestasi` / `produk` / `pkm` |

---

## Istilah yang SUDAH DIHAPUS / BERUBAH

| Istilah lama | Kini menjadi | Alasan |
|---|---|---|
| **Membership** (berbayar, tier) | **Register** (gratis, tanpa tier) | Keanggotaan digratiskan |
| **members** (tabel terpisah) | **users** (tabel tunggal, kolom `role`) | Disederhanakan — semua pengguna web = member |
| **4 role** (Super Admin, Admin Manager, Member, Visitor) | **3 role** (superadmin, admin_manager, member) | Visitor bukan role |
| **organization** (tabel tunggal) | **organization_profile** (single-row) + **organization_structure** (multi-baris) | Menyesuaikan struktur admin Profile & Structure |
| **Jurnal milik ARPS di website** | **OJS terpisah** (subdomain `namajurnal.arps.org`) | Jurnal di-host OJS, bukan di website |

---

## Batasan Scope (jangan dikira fitur)

- **TIDAK ada** payment/subscription/berbayar — keanggotaan gratis selamanya.
- **TIDAK ada** pendaftaran event online — manual via kontak.
- **TIDAK ada** sistem submission jurnal di website — jurnal di OJS (future: member bisa submit ke OJS).
- **TIDAK ada** entitas `members` terpisah, `payments`, `event_registrations`, `expired_at`.

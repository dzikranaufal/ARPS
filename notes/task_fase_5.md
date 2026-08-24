# Task — Fase 5: Publications & Approval Flow

**Lingkup**: backend alur Publications — member upload karya, admin approval (pending → approved/rejected), tampilan publik.
**Tujuan**: alur PRD §3.7, §4, §5.2, §7.2 berfungsi end-to-end; upload aman sesuai PRD §8.2.
**Cara pakai**: kerjakan subtask **secara berurutan** (5.0 → 5.7). Selesaikan satu, verifikasi acceptance-nya, baru lanjut.

> **Keputusan yang sudah diambil (rekomendasi yang disetujui)**:
> - Halaman publik `/publications` hanya menampilkan karya status `approved`, dengan filter kategori.
> - Upload file: PDF/JPG/PNG/DOCX, maks **10MB** (PRD §8.2), validasi mime asli + whitelist + random filename (risiko B1–B3).
> - Approve/reject memakai **update kondisional** `where('status','pending')` dalam transaksi (risiko C2 — dua admin tidak bisa saling timpa).
> - **Kolom `deskripsi` DITAMBAHKAN** ke tabel `publications` (text nullable) — keputusan user; schema.md & PRD sudah diupdate.

---

## Pranala Dokumen (WAJIB dibaca berurutan sebelum mulai)

1. `context/PRD.md` — requirement (fokus §3.7, §4, §5.2, §7.2, §8.2)
2. `context/glossary.md` — istilah baku
3. `context/architecture.md` — struktur, routing, layout
4. `context/directory.md` — peta file/route/controller/view
5. `context/schema.md` — acuan skema (§6 publications)
6. `context/risiko.md` — **wajib**: A3 (kepemilikan), A4 (escape), B1–B4 (upload), C2 (race approval), D1–D3 (query)
7. `notes/progress.md` — status progres
8. `context/rules.md` — aturan perilaku

---

## Aturan Fase Ini (wajib)

- **Risiko wajib**:
  - A3 (IDOR): member hanya bisa melihat/edit karyanya sendiri (`member_id === auth()->id()` via Policy).
  - A4: semua output `{{ }}` — deskripsi/judul karya ter-escape.
  - B1–B4: upload aman — mime asli (bukan ekstensi), whitelist `pdf|jpg|png|docx`, max 10240 KB, random filename, simpan di `storage/app/public/publications`, hapus file saat karya dihapus.
  - C2: approve/reject = update kondisional `where('status','pending')` + transaksi.
  - D1–D3: list publik & antrian admin pakai pagination + eager load (`member`, `reviewer`); index pada `status`, `kategori`, `member_id` (sudah dari migration Fase 2 — verifikasi).
- **CRUD lengkap**: publications = create (member upload), read (publik + member + admin), update (tidak ada edit oleh member — status diubah admin; file/judul tidak bisa diedit member setelah submit; **destroy: admin boleh hapus**). Konfirmasi di bawah.
- **Verifikasi**: setiap subtask, jalankan acceptance check.

---

## Subtask 5.0 — Verifikasi Fondasi Publications

**Konteks**: tabel `publications` & model sudah dibuat di Fase 2. Pastikan siap sebelum dibangun.

**Tindakan**:
1. Cek migration `publications` (Fase 2): kolom `member_id` FK cascadeOnDelete, `judul`, **`deskripsi` (text nullable — tambahkan via migration baru `add_deskripsi_to_publications_table` jika belum ada)**, `kategori` enum `tulisan|prestasi|produk|pkm`, `file` nullable, `status` enum `pending|approved|rejected` default pending, `reviewer_id` FK nullable nullOnDelete.
2. Cek index: tambahkan index pada kolom yang difilter — `status`, `kategori`, `member_id` (buat migration `add_indexes_to_publications_table` jika belum ada — risiko D2).
3. Cek model `App\Models\Publication`: enum cast kategori & status; relasi `member()`, `reviewer()`.
4. `context/schema.md` §6: pastikan kolom `deskripsi` (text nullable) ada — sudah diupdate. Model `Publication` perlu `deskripsi` di `$fillable`.

**Acceptance**:
- `php artisan migrate` jalan; index `status`/`kategori`/`member_id` ada.
- Model & relasi berfungsi (`php artisan tinker`).

---

## Subtask 5.1 — Upload Karya (Member)

**Tindakan**:
1. `App\Http\Controllers\Member\PublicationController`:
   - `index()`: list karya milik user (paginate, latest) — view menampilkan judul + kategori + status + file.
   - `create()` → view form.
   - `store()`: validasi —
     - `judul` required|string|max:255
     - `deskripsi` nullable|string|max:2000 (deskripsi singkat karya)
     - `kategori` required|in:tulisan,prestasi,produk,pkm
     - `file` required|file|mimes:pdf,jpg,jpeg,png,docx|max:10240 (**10MB**)
     - Simpan: `$file->store('publications', 'public')` (random filename), `member_id = auth()->id()`, `status = pending` (hardcode — jangan dari input, risiko A2), `reviewer_id = null`.
     - Redirect ke list karya dengan flash success "Karya terkirim, menunggu review".
   - **Tidak ada** edit/update oleh member setelah submit (keputusan: karya tidak bisa diubah member; jika salah, member bisa hubungi admin atau upload ulang).
2. View `member/publications/index.blade.php` (list karya + badge status + link unduh file) & `member/publications/create.blade.php` (form: judul, **deskripsi textarea**, select kategori, file input — tampilkan batasan jenis/ukuran).
3. Route grup member:
   ```php
   Route::get('/publications', [PublicationController::class, 'index'])->name('member.publications.index');
   Route::get('/publications/create', [PublicationController::class, 'create'])->name('member.publications.create');
   Route::post('/publications', [PublicationController::class, 'store'])->name('member.publications.store');
   ```
4. Dashboard member (Fase 4.2): ganti placeholder "Upload Karya" → link nyata `member.publications.create`; daftar karya sudah menampilkan data nyata (query sudah ada).
5. **Policy** `PublicationPolicy`: `update/delete` hanya admin; `viewAny/view` member hanya karya miliknya (risiko A3).

**Acceptance**:
- Member upload karya → DB: `status=pending`, `reviewer_id=null`, file tersimpan di `storage/app/public/publications`.
- File >10MB / tipe tidak di whitelist (mis. `.exe`, `.php`) → ditolak validasi.
- Member hanya melihat karyanya sendiri di list.
- Karya tidak bisa diedit/dihapus member (403/tiada route).

---

## Subtask 5.2 — Antrian Approval (Admin)

**Tindakan**:
1. `Admin\PublicationController`:
   - `index()`: list publications dengan filter tab (semua / pending / approved / rejected) + search judul + pagination + eager load `member`, `reviewer` (risiko D1/D3). **Default tampil antrian `pending`**.
   - `show($id)`: detail karya + file (preview/download) + info member uploader.
   - `approve($id)`: 
     ```php
     DB::transaction(fn () =>
         Publication::where('id', $id)->where('status', 'pending')
             ->update(['status' => 'approved', 'reviewer_id' => auth()->id()])
     );
     ```
     Jika `updated` = 0 → flash "karya sudah diproses" (risiko C2). Redirect kembali.
   - `reject($id)`: sama, `status => 'rejected'`.
   - `destroy($id)`: hapus karya + **hapus file fisik** (`Storage::delete`) — risiko B4.
2. Route (grup admin):
   ```php
   Route::get('publications', [PublicationController::class, 'index'])->name('admin.publications.index');
   Route::get('publications/{id}', [PublicationController::class, 'show'])->name('admin.publications.show');
   Route::put('publications/{id}/approve', [PublicationController::class, 'approve'])->name('admin.publications.approve');
   Route::put('publications/{id}/reject', [PublicationController::class, 'reject'])->name('admin.publications.reject');
   Route::delete('publications/{id}', [PublicationController::class, 'destroy'])->name('admin.publications.destroy');
   ```
3. View `admin/publications/index.blade.php` (tabel: judul, kategori, member, tanggal, status badge, aksi show/approve/reject/delete — approve/reject via form PUT + modal konfirmasi) & `admin/publications/show.blade.php`.
4. Sidebar: link "Publications" `href="#"` → `route('admin.publications.index')`.

**Acceptance**:
- Antrian pending tampil; approve → status approved + reviewer_id terisi; reject → rejected.
- Dua request approve bersamaan → hanya satu yang berhasil (yang kedua dapat "sudah diproses") — risiko C2.
- Delete → record + file fisik terhapus.

---

## Subtask 5.3 — Halaman Publik Publications (filter kategori)

**Tindakan**:
1. `routes/web.php` — ganti closure `/publications`:
   ```php
   Route::get('/publications', function (Request $request) {
       $query = App\Models\Publication::with('member')->where('status', 'approved');
       if ($request->filled('kategori')) {
           $query->where('kategori', $request->kategori);  // pastikan nilai valid (in:...)
       }
       $publications = $query->latest()->paginate(12)->withQueryString();
       return view('publications.index', ['publications' => $publications]);
   })->name('publications.index');
   ```
   - Validasi `kategori` query: hanya `tulisan|prestasi|produk|pkm` (tolak nilai lain / abaikan).
2. `resources/views/publications/index.blade.php` — ganti STATIC SAMPLE:
   - Filter kategori (link `?kategori=...` — semua/tulisan/prestasi/produk/pkm) dengan active state.
   - Grid kartu: judul, badge kategori, **deskripsi singkat (Str::limit)**, nama member (uploader), **link unduh file** (`Storage::url` / route download).
   - Pagination (`$publications->links()`).
   - Hanya `approved` yang tampil.

**Acceptance**:
- `/publications` menampilkan karya approved dari DB; filter kategori berfungsi.
- Karya pending/rejected TIDAK tampil.
- File bisa diunduh; nama member tampil (field publik — tidak ada email/telepon).

---

## Subtask 5.4 — Unduh File (aman)

**Tindakan**:
1. Buat route download untuk member & admin (file di storage, bukan public langsung):
   - Member: `GET /member/publications/{id}/download` → hanya pemilik (Policy — risiko A3).
   - Admin: `GET /admin/publications/{id}/download` → admin mana pun.
   - Implementasi: `Storage::disk('public')->download($path, $nama_asli)` — simpan nama asli? **JANGAN** menyimpan nama asli user di DB (risiko B2); tampilkan nama generik (mis. `publication-{id}.{ext}`) atau simpan `original_name` terpisah jika benar-benar perlu. **Keputusan**: nama unduhan = `publication-{id}.{ext}` (hindari menyimpan nama asli).
2. Di view list/admin: link unduh memakai route download (bukan `Storage::url` langsung jika ingin terkontrol).

**Acceptance**:
- Unduh berfungsi untuk pemilik (member) & admin; member lain → 403.
- Nama file unduhan generik (tidak memakai nama asli user).

---

## Subtask 5.5 — Verifikasi Fase 5

**Tindakan**:
1. `php artisan migrate:fresh --seed` — bersih. (Seeder boleh menambah 2–3 publications sampel: 1 pending, 1 approved, 1 rejected.)
2. Skenario end-to-end via browser:
   - Member A login → upload karya (file valid) → list menampilkan `pending`.
   - Member A coba akses/unduh karya member B → 403.
   - Admin login → antrian pending → approve karya A → member A lihat status `approved`.
   - `/publications` publik → karya approved tampil; filter kategori berfungsi.
   - Admin reject karya lain → tidak tampil publik; member lihat `rejected`.
   - Upload `.exe`/file >10MB → ditolak.
   - Admin delete karya → record & file terhapus.
3. Checklist risiko: A3, A4, B1–B4, C2, D1–D3.

**Acceptance**:
- Semua skenario berjalan sesuai; tidak ada error di log; file upload valid.

---

## Subtask 5.6 — Sinkronkan Dokumentasi

**Tindakan**:
1. `context/directory.md`: tambah `Member\PublicationController`, `Admin\PublicationController`, `PublicationPolicy`, view `member/publications/*`, `admin/publications/*`.
2. `notes/progress.md`: Publications — upload member 🟢, antrian approval 🟢, tampilan publik 🟢, unduh 🟢.
3. `context/schema.md`: tidak berubah (kecuali index — jika ditambahkan di 5.0, catat).

**Acceptance**:
- `directory.md` & `progress.md` mencerminkan kode aktual.

---

## Checklist Akhir (sebelum menyatakan Fase 5 selesai)

- [ ] 5.0–5.6 semua selesai & acceptance terpenuhi.
- [ ] Upload aman: mime asli, whitelist pdf/jpg/png/docx, 10MB, random filename, file terhapus saat delete (B1–B4).
- [ ] Member hanya akses karyanya sendiri (A3); output ter-escape (A4).
- [ ] Approve/reject kondisional + transaksi (C2).
- [ ] Pagination + eager load + index (D1–D3).
- [ ] Publik hanya menampilkan approved.
- [ ] Dokumentasi sinkron.

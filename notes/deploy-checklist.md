# Deploy Checklist — ARPS Production

Checklist deploy production (PRD §8, risiko.md E1–E8). Jalankan berurutan; jangan skip.

## Pre-Deploy

- [ ] **E4 Backup DB** — `mysqldump -u USER -p arps_prod > backup_$(date +%Y%m%d_%H%M).sql` (sebelum `migrate`). Simpan off-site.
- [ ] **E5 Backup storage** — `tar -czf storage_$(date +%Y%m%d).tar.gz storage/app/public`. Jangan hanya DB.
- [ ] **Maintenance** `php artisan down --render="errors.503"` (E7) — tampil halaman ramah.

## Env & Config (E1)

- [ ] `.env` production:
  ```
  APP_ENV=production
  APP_DEBUG=false
  APP_URL=https://arps.example.com
  APP_KEY=base64:... (jangan share)
  DB_CONNECTION=mysql (credentials aman)
  SESSION_DRIVER=database
  CACHE_STORE=database
  ```
- [ ] Pastikan `APP_DEBUG=false` — hindari bocor stack trace (E1).

## Install & Migrasi

- [ ] `composer install --no-dev --optimize-autoloader`
- [ ] `php artisan migrate --force` (sudah backup)
- [ ] `php artisan storage:link` **wajib** (E2) — cek `public/storage` symlink.
- [ ] `php artisan config:cache` + `route:cache` + `view:cache`

## Aset (D5)

- [ ] `npm ci && npm run build` (Vite) — pastikan `public/build/manifest.json` ada.
- [ ] Cek `asset()` / `@vite` ter-cache bust.

## Permission (E8)

- [ ] `chmod -R 775 storage bootstrap/cache` dan `chown` ke user web (www-data / cPanel user). Test upload 1 file.

## Keamanan (E3, E6)

- [ ] Seeder admin: password acak di `UserSeeder` sudah kuat; **ganti/manual** password superadmin di production, jangan pakai `admin/admin123`.
- [ ] Log di `storage/logs/laravel.log` (di luar `public/`) — tambah `.htaccess` `Deny from all` jika di shared hosting (E6).
- [ ] HTTPS aktif, `APP_URL` https, HSTS, cek `public/.htaccess` tidak expose.

## Post-Deploy Smoke (E7)

- [ ] `php artisan up`
- [ ] Buka: `/` `/about` `/organization` `/programs` `/journals` `/publications` `/news` `/events` `/login` — status 200.
- [ ] Login superadmin → `/admin` → upload 1 file (publications) → approve → cek publik.
- [ ] Register member baru → `/dashboard` → upload → cek direktori `/organization` tanpa email.
- [ ] `php artisan test` (atau curl) — 24 test hijau.
- [ ] Cek `storage/logs/laravel.log` bersih, tidak ada `ERROR`.

## Rollback

- Jika gagal: `php artisan down`, restore DB `mysql arps_prod < backup.sql`, restore `storage/app/public`, `php artisan up`.

---

*Checklist ini mencakup risiko.md E1–E8. Simpan sebagai runbook.*

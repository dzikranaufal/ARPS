# Backup Procedure — ARPS

Prosedur backup DB + storage (risiko.md E5) dan uji restore.

## Apa yang di-backup

1. **DB MySQL** — struktur + data semua tabel (`users`, `publications`, `programs`, dll).
2. **Storage** — `storage/app/public` (publications file 10MB, gambar modul, foto member, logo). **Jangan pernah hanya DB** (E5).

## Frekuensi & Lokasi

- Harian via cron cPanel / systemd timer.
- Simpan di lokasi berbeda dari server produksi (mis. Google Drive, S3, atau server backup). Retensi 7 hari harian + 4 mingguan.

## Perintah

### Backup DB
```bash
mysqldump -u $DB_USER -p$DB_PASS $DB_NAME | gzip > /backup/arps_db_$(date +%Y%m%d).sql.gz
```

### Backup Storage
```bash
tar -czf /backup/arps_storage_$(date +%Y%m%d).tar.gz -C storage/app/public .
# atau rsync ke object storage (R2/S3):
rclone sync storage/app/public remote:arps-backup/storage
```

### Cron contoh (cPanel > Cron Jobs, tiap jam 2 pagi)
```
0 2 * * * /usr/local/bin/mysqldump -uUSER -pPASS arps_prod | gzip > /home/user/backup/arps_db_$(date +\%Y\%m\%d).sql.gz && tar -czf /home/user/backup/arps_storage_$(date +\%Y\%m\%d).tar.gz -C /home/user/arps/storage/app/public . >> /home/user/backup/cron.log 2>&1
```

## Uji Restore (wajib berkala — risiko.md E5)

**Jadwal:** tiap bulan, atau setelah perubahan schema.

1. Buat DB kosong `arps_restore_test`.
2. Restore DB:
   ```bash
   gunzip < /backup/arps_db_YYYYMMDD.sql.gz | mysql -u USER -p arps_restore_test
   ```
3. Restore storage ke folder temp:
   ```bash
   mkdir /tmp/restore && tar -xzf /backup/arps_storage_YYYYMMDD.tar.gz -C /tmp/restore && ls -lh /tmp/restore
   ```
4. Verifikasi:
   - `php artisan migrate:status` di DB restore tidak error.
   - `SELECT COUNT(*) FROM users;` dan `SELECT COUNT(*) FROM publications;` sesuai.
   - Buka 1 file dari restore di browser (cek inode, bukan 404).
   - Jalankan `php artisan test` dengan DB restore (ubah `DB_DATABASE` sementara).

Jika restore gagal → perbaiki cron/permission segera.

## Catatan

- `APP_DEBUG=false` di production (E1) agar error tidak bocor saat restore test.
- `storage:link` harus ada setelah restore (E2).
- Enkripsi backup jika berisi data sensitif (opsional).

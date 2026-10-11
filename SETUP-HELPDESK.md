# Setup Backend Helpdesk (2 Database)

Aplikasi ini memakai **dua koneksi database**:

1. `mysql` (default) → **db_helpdesk** — dikelola penuh oleh aplikasi ini
   (roles, users, tickets, ticket_replies, settings, notifications).
2. `indukk` → **db_indukk** — database HR/master milik sistem lain.
   Helpdesk **hanya membaca** (read-only) tabel `divisis` & `karyawans` dari sini.

## Langkah setup

1. Buat database kosong `db_helpdesk` di MySQL/MariaDB.
2. Import skema HR yang sudah ada:
   ```
   mysql -u root db_indukk < db_indukk.sql
   ```
   (buat database `db_indukk` dulu kalau belum ada: `CREATE DATABASE db_indukk;`)
3. Sesuaikan kredensial di `.env` kalau host/user/password MySQL Anda beda
   dari default (`127.0.0.1`, `root`, tanpa password):
   ```
   DB_DATABASE=db_helpdesk
   DB_USERNAME=root
   DB_PASSWORD=

   DB_INDUKK_DATABASE=db_indukk
   DB_INDUKK_USERNAME=root
   DB_INDUKK_PASSWORD=
   ```
4. Jalankan migrasi + seeder untuk **db_helpdesk** saja (db_indukk sudah
   diisi lewat file .sql di atas, jadi tidak ada migrasi Laravel untuknya):
   ```
   php artisan migrate --seed
   ```
5. Jalankan servernya:
   ```
   php artisan serve
   ```

## Akun login demo (password semua: `password`)

| Email | Role |
|---|---|
| superadmin@mitratani27.co.id | Superadmin |
| andre@mitratani27.co.id | Admin IT |
| admin.hrga@mitratani27.co.id | Admin HRGA |
| agent@mitratani27.co.id | Agent |

## Catatan penting

- **Divisi & Karyawan bersifat read-only** di helpdesk — tidak ada tombol
  tambah/edit/hapus untuk 2 menu ini. Kalau datanya perlu diubah, ubah di
  sistem HR/induk (db_indukk), bukan lewat helpdesk.
- Kolom karyawan yang ditarik ke helpdesk sengaja dibatasi ("seperlunya"):
  nik, nama, email, no_hp, jabatan, divisi, status_aktif — bukan seluruh
  data HR (alamat, KTP, tanggal lahir, keluarga, dll tidak pernah disentuh).
- Katalog permission ada di 2 tempat yang harus tetap sinkron manual:
  `assets/js/app.js` (variabel `permissionCatalog`, sisi tampilan) dan
  `app/Support/PermissionCatalog.php` (sisi validasi backend).

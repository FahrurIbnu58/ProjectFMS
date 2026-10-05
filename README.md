# File Management System (FMS)

Aplikasi pengelolaan dokumen perusahaan: Mendukung folder hierarkis tanpa batas level, manajemen file, department sebagai metadata, Administrator / Viewer, dashboard, search & filter, drag & drop upload, preview PDF/gambar, activity log, soft delete, dark mode, responsive, CRUD users (dari laman admin), pagination, dan testing.

## Fitur

| Area | Detail |
|---|---|
| Auth | Login Sanctum (Bearer token), role `administrator` / `viewer` |
| Folder | Create / rename / delete (rekursif), parent-child, root `parent_id=NULL`, breadcrumb, tree, cegah cycle |
| File | Upload (title, department, file wajib), edit metadata/ganti file, hapus (soft delete), download, preview inline, search nama file/title, filter department & folder |
| File detail | Folder, nama file, title, department, uploaded by, upload date |
| Dashboard | Total folder, total file, total department, 10 file terbaru |
| Department | CRUD (admin), dipakai sebagai metadata file |

## Tech stack

- PHP 8.2, Laravel 11, Sanctum
- Vue 3 + Vue Router + Axios (SPA di `resources/js`, dibundle Vite)
- Tailwind CSS (dark mode `class`)
- PostgreSQL 16 (produksi/Docker), SQLite (dev lokal bila tanpa Postgres)
- Storage: Laravel `public` disk (`storage/app/public/documents`)


## Requirement

- PHP >= 8.2 + ekstensi `pdo_pgsql` (produksi) / `pdo_sqlite` (dev), Composer
- Node >= 20, npm
- PostgreSQL 16 (atau Docker), atau SQLite untuk coba cepat

## Konfigurasi environment

Salin dan sesuaikan `.env`:

```bash
cp .env.example .env
php artisan key:generate
```

PostgreSQL lokal (tanpa Docker):

```env
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=postgres
DB_USERNAME=postgres
DB_PASSWORD=secret
FILESYSTEM_DISK=public
```

SQLite cepat (dev):

```env
DB_CONNECTION=sqlite
DB_DATABASE=C:\path\to\database\database.sqlite
```

## Cara instalasi & menjalankan

```bash
composer install
npm install
npm run build            # atau npm run dev saat develop, agar saling terintegrasi hingga web bisa dijalankan.
php artisan storage:link
php artisan migrate --seed
php artisan serve        # http://localhost:8000
```

Frontend SPA dilayani Laravel (`/` → `resources/views/app.blade.php`), API di `/api/*`.

## Migration & seeder

```bash
php artisan migrate
php artisan db:seed
# atau fresh:
php artisan migrate:fresh --seed
```

Akun login :

| Akun | Email | Password | Role |
| Administrator | admin@example.com | password | administrator | #login sebagai admin
| Viewer | viewer@example.com | password | viewer | #login sebagai user atau viewer



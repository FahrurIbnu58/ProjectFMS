# File Management System (FMS)

Aplikasi pengelolaan dokumen perusahaan: **Laravel 11 (backend, REST API + Sanctum)** + **Vue.js 3 (frontend SPA)** + **Tailwind CSS** + **PostgreSQL**. Mendukung folder hierarkis tanpa batas level, manajemen file, department sebagai metadata, RBAC (Administrator / Viewer), dashboard, search & filter, breadcrumb, drag & drop upload, preview PDF/gambar, activity log, soft delete, dark mode, responsive, service/repository pattern, pagination, dan testing.

## Fitur

| Area | Detail |
|---|---|
| Auth | Login Sanctum (Bearer token), role `administrator` / `viewer` |
| Folder | Create / rename / delete (rekursif), parent-child, root `parent_id=NULL`, breadcrumb, tree, cegah cycle |
| File | Upload (title, department, file wajib), edit metadata/ganti file, hapus (soft delete), download, preview inline, search nama file/title, filter department & folder |
| File detail | Folder, nama file, title, department, uploaded by, upload date |
| Dashboard | Total folder, total file, total department, 10 file terbaru |
| Department | CRUD (admin), dipakai sebagai metadata file |
| Bonus | Search & filter, breadcrumb, drag & drop upload, preview PDF/image, activity log, soft delete, feature test, Docker, API docs (OpenAPI + Postman), responsive, dark mode, service/repository |

## Tech stack

- PHP 8.2, Laravel 11, Sanctum
- Vue 3 + Vue Router + Axios (SPA di `resources/js`, dibundle Vite)
- Tailwind CSS (dark mode `class`)
- PostgreSQL 16 (produksi/Docker), SQLite (dev lokal bila tanpa Postgres)
- Storage: Laravel `public` disk (`storage/app/public/documents`)

## Struktur repo (ringkas)

```
app/{Enums,Models,Policies,Repositories,Services,Traits,Http/{Controllers/Api,Middleware,Requests,Resources}}
bootstrap/app.php            # registrasi api routes + alias middleware 'role'
routes/{api.php,web.php}     # api + SPA fallback
database/{migrations,seeders,factories}
resources/{views/app.blade.php, js/{app.js,router,store,components,views}, css/app.css}
tests/Feature                # Auth, Folder, File, Department, Dashboard
docs/{openapi.yaml,postman_collection.json}
Dockerfile, docker-compose.yml
```

Relasi DB: `users 1—n folders/files/activity_logs`; `folders self-ref parent/children, 1—n files`; `departments 1—n files`; `files n—1 folder/department/uploader`.

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
DB_DATABASE=fms
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
npm run build            # atau npm run dev saat develop
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

Seeder membuat:

| Akun | Email | Password | Role |
|---|---|---|---|
| Administrator | admin@example.com | password | administrator |
| Viewer | viewer@example.com | password | viewer |

Plus department (HR, Finance, IT, Legal, Marketing, Operations), folder hierarkis contoh, dan ±12 file contoh.

## Menjalankan test

```bash
php artisan test
# per modul:
php artisan test --filter=FolderTest
```

## Docker

```bash
docker compose up --build -d
docker compose run --rm app php artisan migrate --seed --force
# buka http://localhost:8000
```

Service `db` = Postgres 16 (`fms`/`fms`/`secret`), `app` = PHP 8.2 + Apache.

## API ringkas

Auth: `POST /api/login` → `{token, user}`. Sertakan `Authorization: Bearer <token>` untuk sisanya.

- `GET /api/me`, `POST /api/logout`
- `GET /api/dashboard`
- `GET/POST /api/folders`, `GET/PUT/DELETE /api/folders/{id}`, `GET /api/folders-tree`, `GET /api/folders/{id}/breadcrumbs`
- `GET/POST /api/files?q=&department_id=&folder_id=`, `GET/PUT/DELETE /api/files/{id}`, `GET /api/files/{id}/download`, `GET /api/files/{id}/preview`
- `GET/POST /api/departments`, `GET/PUT/DELETE /api/departments/{id}`
- `GET /api/activity-logs` (admin)

Dokumen lengkap: `docs/openapi.yaml` (Swagger) dan `docs/postman_collection.json`.

Aturan akses: endpoint tulis (POST/PUT/DELETE folder/file/department) + activity log = `administrator` (middleware `role:administrator` + Policy, 403 bila viewer). Viewer boleh: lihat folder/file, detail, download, preview, search, filter, dashboard.

## Git

Gunakan commit kecil dan jelas, contoh: `feat: authentication`, `feat: folder management`, `feat: upload document`, `fix: validation upload`, `refactor: move business logic to service`.

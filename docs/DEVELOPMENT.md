# Development Setup (Mac + MySQL)

Dokumen ini berisi baseline setup Fase 0.

## Prasyarat

- PHP 8.2+
- Composer
- Node.js + npm
- MySQL (lokal)

## Konfigurasi Database

Project ini menggunakan MySQL pada `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=vcr_rt_rw
DB_USERNAME=root
DB_PASSWORD=
```

Buat database jika belum ada:

```bash
mysql -u root -e "CREATE DATABASE IF NOT EXISTS vcr_rt_rw CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
```

## Setup Lokal

```bash
composer install
npm install
php artisan key:generate
php artisan migrate
```

## Standar Kualitas Kode

Jalankan format check:

```bash
composer lint
```

Jalankan test baseline:

```bash
composer test
```

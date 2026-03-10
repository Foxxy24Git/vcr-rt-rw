# RT/RW Internet Management System - Implementation Plan

## 1) Ringkasan Kondisi Project Saat Ini

Hasil audit project:
- Framework: Laravel 12 (`laravel/framework:^12.0`) dengan PHP `^8.2`
- Frontend: Blade + Tailwind CSS v4 + Vite
- Database default `.env`: `sqlite` (belum MySQL)
- Auth scaffolding: belum ada (masih route default `welcome`)
- Struktur: masih skeleton Laravel murni

Implikasi:
- Fondasi arsitektur masih bersih dan ideal untuk dibentuk dari awal.
- Perlu setup MySQL dan auth baseline sebelum masuk fitur bisnis.

---

## 2) Target Arsitektur (Clean + Scalable, Tetap Pragmatic)

Pendekatan: **Layered Modular Monolith**.

Prinsip:
- Controller tipis (hanya HTTP concern).
- Semua business use-case berjalan di **Service layer**.
- Akses data kompleks di **Repository layer** (kontrak + implementasi Eloquent).
- Integrasi eksternal (MikroTik) diisolasi lewat **Adapter/Client interface** agar mudah di-mock.
- Domain rule utama dijaga via service + validation + policy.

Alur request:
`Route -> Controller -> FormRequest -> Service -> Repository -> Model/DB`

Untuk integrasi MikroTik:
`Service -> MikroTik Client Interface -> Fake Adapter (fase awal)`

---

## 3) Struktur Folder yang Disarankan

```text
app/
  Http/
    Controllers/
      Admin/
      Reseller/
      Auth/
    Middleware/
    Requests/
      Admin/
      Reseller/
  Models/
    User.php
    Wallet.php
    WalletTransaction.php
    InternetPackage.php
    Voucher.php
    VoucherBatch.php
    MikrotikServer.php
    MikrotikLog.php
  Enums/
    UserRole.php
    WalletTransactionType.php
    WalletTransactionSource.php
    VoucherStatus.php
  Services/
    Auth/
      AuthService.php
    Wallet/
      WalletService.php
      WalletTopUpService.php
      WalletDebitService.php
    Package/
      PackageService.php
    Voucher/
      VoucherGenerationService.php
      VoucherPricingService.php
      VoucherLifecycleService.php
    Mikrotik/
      Contracts/
        MikrotikClientInterface.php
      Clients/
        FakeMikrotikClient.php
      MikrotikService.php
  Repositories/
    Contracts/
      UserRepositoryInterface.php
      WalletRepositoryInterface.php
      WalletTransactionRepositoryInterface.php
      PackageRepositoryInterface.php
      VoucherRepositoryInterface.php
      VoucherBatchRepositoryInterface.php
    Eloquent/
      UserRepository.php
      WalletRepository.php
      WalletTransactionRepository.php
      PackageRepository.php
      VoucherRepository.php
      VoucherBatchRepository.php
  Policies/
    PackagePolicy.php
    VoucherPolicy.php
    WalletPolicy.php
  Providers/
    RepositoryServiceProvider.php
    AppServiceProvider.php

database/
  migrations/
  seeders/
    RoleAdminSeeder.php
    AdminUserSeeder.php
    DemoResellerSeeder.php
```

Catatan:
- Tidak semua query wajib repository. Gunakan repository untuk query bisnis yang berulang/kompleks.
- Query sederhana tetap boleh lewat model langsung di service agar tidak over-engineering.

---

## 4) Desain Skema Database (MySQL)

### 4.1 users (update tabel bawaan)
Tambahan kolom:
- `role` enum/string: `admin|reseller`
- `status` enum/string: `active|inactive`
- `phone` nullable
- `last_login_at` nullable timestamp

Index:
- `email` unique (existing)
- `role`, `status` index

### 4.2 wallets
- `id`
- `user_id` (FK ke `users`, unique untuk 1 wallet per reseller)
- `balance` decimal(18,2) default 0
- `currency` char(3) default `IDR`
- `is_locked` boolean default false
- timestamps

Index:
- unique `user_id`

### 4.3 wallet_transactions
- `id`
- `wallet_id` FK
- `type` enum: `credit|debit`
- `source` enum: `topup|voucher_purchase|manual_adjustment|refund`
- `amount` decimal(18,2)
- `balance_before` decimal(18,2)
- `balance_after` decimal(18,2)
- `reference_type` nullable string
- `reference_id` nullable bigint
- `description` nullable string
- `created_by` nullable FK ke users (admin/operator)
- timestamps

Index:
- `wallet_id`, `source`, `created_at`
- `reference_type + reference_id`

### 4.4 internet_packages
- `id`
- `code` unique string
- `name` string
- `description` nullable text
- `price` decimal(18,2) -> harga ke reseller
- `validity_value` int (contoh: 1, 7, 30)
- `validity_unit` enum: `hour|day|month`
- `bandwidth_up_kbps` nullable int
- `bandwidth_down_kbps` nullable int
- `quota_mb` nullable bigint
- `mikrotik_profile` nullable string
- `is_active` boolean default true
- timestamps

Index:
- `code` unique
- `is_active`

### 4.5 voucher_batches
- `id`
- `reseller_id` FK ke users
- `package_id` FK ke internet_packages
- `batch_code` unique string
- `qty_requested` int
- `qty_generated` int
- `unit_price` decimal(18,2)
- `total_cost` decimal(18,2)
- `status` enum: `draft|paid|generated|failed|cancelled`
- `paid_at` nullable timestamp
- `generated_at` nullable timestamp
- timestamps

Index:
- `reseller_id`, `status`
- `package_id`

### 4.6 vouchers
- `id`
- `batch_id` FK ke voucher_batches
- `reseller_id` FK ke users
- `package_id` FK ke internet_packages
- `code` unique string (voucher code)
- `username` nullable string
- `password` nullable string (untuk fase awal bisa plain; target berikutnya encrypted)
- `status` enum: `ready|sold|used|expired|revoked`
- `cost_price` decimal(18,2)
- `sold_price` nullable decimal(18,2)
- `generated_at` timestamp
- `sold_at` nullable timestamp
- `used_at` nullable timestamp
- `expires_at` nullable timestamp
- timestamps

Index:
- `code` unique
- `reseller_id + status`
- `batch_id`

### 4.7 mikrotik_servers
- `id`
- `name` string
- `host` string
- `port` integer default 8728
- `username` string
- `password` text (encrypted cast)
- `is_active` boolean
- `notes` nullable text
- timestamps

### 4.8 mikrotik_logs
- `id`
- `server_id` nullable FK ke mikrotik_servers
- `action` string (create_hotspot_user, disable_user, dll)
- `request_payload` json nullable
- `response_payload` json nullable
- `status` enum: `success|failed|simulated`
- `message` nullable string
- timestamps

Index:
- `server_id`, `action`, `status`, `created_at`

---

## 5) Strategi Authentication & Authorization

Rekomendasi:
- Gunakan **Laravel Breeze (Blade)** untuk login/register/reset password baseline.
- Tetap **single guard `web`**.
- Multi-role cukup dengan kolom `users.role` + middleware role.

Detail:
- Middleware `EnsureUserRole` menerima role (`admin` atau `reseller`).
- Route group:
  - `/admin/*` -> `auth` + `role:admin`
  - `/reseller/*` -> `auth` + `role:reseller`
- Gunakan **Policy** untuk pembatasan level record:
  - Reseller hanya lihat data miliknya.
  - Admin bisa akses semua data.
- Login page tunggal. Redirect pasca login sesuai role.

Kenapa ini dipilih:
- Paling ringan untuk 2 role.
- Mudah diskalakan (nanti jika role bertambah bisa migrasi ke permission package).

---

## 6) Roadmap Implementasi Bertahap (Tidak Sekaligus)

## Fase 0 - Foundation Setup
Output:
- Ubah koneksi DB ke MySQL di `.env` (lokal Mac)
- Jalankan migrasi default + verifikasi koneksi
- Tambahkan `RepositoryServiceProvider` (binding interface)
- Standarisasi code style dan baseline testing

Checklist:
- MySQL local siap (`DB_CONNECTION=mysql`)
- Environment dev terdokumentasi
- CI lokal: `php artisan test` hijau

## Fase 1 - Auth + Role Baseline
Output:
- Install Breeze (Blade)
- Tambah kolom `role`, `status` pada `users`
- Seeder admin awal
- Middleware role + redirect dashboard per role

Checklist:
- Admin dan reseller bisa login
- Akses route terpisah sesuai role
- Unauthorized access tertolak (403/redirect)

## Fase 2 - Master Data Internet Package
Output:
- CRUD `internet_packages` untuk admin
- Listing paket untuk reseller (read-only)
- Validation rules + Policy

Checklist:
- Admin bisa create/edit/disable paket
- Reseller hanya bisa melihat paket aktif
- Test CRUD dan authorization tersedia

## Fase 3 - Wallet System Reseller
Output:
- Tabel `wallets` dan `wallet_transactions`
- Service:
  - top up wallet (admin)
  - debit wallet (saat beli voucher)
  - transaction ledger
- Locking transaksi untuk mencegah saldo minus akibat race condition

Checklist:
- Tiap reseller punya 1 wallet
- Debit gagal jika saldo tidak cukup
- Ledger konsisten (`before`, `after`, `amount`)

## Fase 4 - Voucher Generation
Output:
- Tabel `voucher_batches` dan `vouchers`
- Flow:
  - reseller pilih paket + qty
  - sistem cek saldo -> debit wallet
  - generate batch + voucher code unik
- Admin bisa audit semua batch
- Reseller hanya melihat batch/voucher miliknya

Checklist:
- Code voucher unik dan terindeks
- Pembuatan batch atomic (transaction DB)
- Rollback saat gagal generate

## Fase 5 - MikroTik Service Layer (Simulasi Dulu)
Output:
- `MikrotikClientInterface`
- `FakeMikrotikClient` (no real connection)
- `MikrotikService` dipanggil saat voucher dibuat/diaktivasi
- Logging di `mikrotik_logs`

Checklist:
- Integrasi eksternal terabstraksi interface
- Business flow tetap jalan walau koneksi real belum ada
- Unit test bisa mock client dengan mudah

## Fase 6 - UI Blade + Tailwind (Simple Modern)
Output:
- Dashboard admin: KPI ringkas (saldo total reseller, voucher generated, dll)
- Dashboard reseller: saldo wallet, paket, histori voucher
- Tabel + filter dasar (status, tanggal, paket)
- Komponen Blade reusable (cards, tables, badge status)

Checklist:
- UI responsif desktop/mobile
- Navigasi role-based jelas
- Konsisten style Tailwind

## Fase 7 - Hardening, Audit, Testing
Output:
- Feature test end-to-end untuk flow kritikal:
  - login role
  - topup wallet
  - generate voucher
  - authorization boundary
- Logging dan error handling lebih rapi
- Seed data demo untuk UAT

Checklist:
- Coverage flow inti memadai
- Tidak ada bug saldo negatif
- Tidak ada data leakage antar reseller

---

## 7) Aturan Implementasi Teknis

- Semua logic bisnis ditempatkan di `app/Services/*`.
- Controller tidak boleh berisi query kompleks.
- Gunakan `DB::transaction()` untuk proses finansial dan generation batch.
- Semua nominal uang pakai `decimal(18,2)`, tidak pakai float.
- Tambahkan index sejak awal pada kolom filter utama.
- Gunakan FormRequest untuk validasi input.
- Gunakan Policy untuk akses data level record.

---

## 8) Risiko dan Mitigasi

Risiko:
- Race condition saat debit wallet bersamaan.
- Kode voucher bentrok jika generator tidak aman.
- Data voucher reseller tercampur jika policy lemah.

Mitigasi:
- Lock row wallet saat debit (`SELECT ... FOR UPDATE` via transaction).
- Generator voucher pakai random + prefix + retry unique collision.
- Semua query reseller dibatasi `where reseller_id = auth()->id()`.
- Tambah test konkurensi sederhana untuk wallet debit.

---

## 9) Urutan Eksekusi yang Direkomendasikan Sekarang

1. Fase 0 (setup MySQL + provider binding).
2. Fase 1 (auth + role).
3. Fase 2 (package management).
4. Fase 3 (wallet).
5. Fase 4 (voucher generation).
6. Fase 5 (MikroTik service simulation).
7. Fase 6 dan 7 (UI polish + hardening).

Ini menjaga delivery bertahap, mudah diuji, dan menghindari implementasi “sekaligus”.


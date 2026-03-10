# PROJECT STATE REPORT

**Application:** RT/RW Voucher Management System (Laravel)  
**Generated:** Summary of current implementation  
**Scope:** Full codebase analysis — routes, controllers, services, repositories, models, migrations, middleware, config.

---

## 1. PROJECT OVERVIEW

This is a **Laravel (Breeze) web application** for managing hotspot vouchers in an RT/RW (neighbourhood) network context. The system supports:

- **Two user roles:** Admin and Reseller (enum `UserRole::ADMIN`, `UserRole::RESELLER`).
- **Resellers** purchase voucher batches (deducted from their wallet), then view/print/export vouchers.
- **Admins** manage packages, resellers, wallets (top-up, manual adjust, ledger), voucher batches, MikroTik logs, failed jobs, VCR settings, and system settings.
- **Root route** (`/`) redirects to the login page; no public API (`routes/api.php` does not exist).
- **Stack:** Laravel (web), Blade + TailwindCSS, Vite, MySQL (assumed from migrations), queue (database driver), optional MikroTik integration for hotspot user sync.

---

## 2. APPLICATION ARCHITECTURE

| Layer | Pattern | Location |
|-------|---------|----------|
| **Routing** | Web-only, auth + role-based groups | `routes/web.php`, `routes/auth.php`, `routes/console.php` |
| **Controllers** | Thin; delegate to services | `app/Http/Controllers/` (Admin/, Reseller/, Auth/) |
| **Services** | Business logic, orchestration | `app/Services/` (Voucher/, Wallet/, User/, Mikrotik/, Audit/, Dashboard/) |
| **Repositories** | Data access abstraction | `app/Repositories/` (Contracts/ + Eloquent/) |
| **Models** | Eloquent models, relationships | `app/Models/` |
| **Policies** | Authorization (Gate) | `app/Policies/` (InternetPackage, Wallet, VoucherBatch) |
| **Middleware** | Role enforcement | `app/Http/Middleware/EnsureUserRole` (`role:admin`, `role:reseller`) |
| **Form Requests** | Validation + authorize | `app/Http/Requests/` (Admin/, Reseller/, Auth/) |

- **Service layer:** Voucher generation, pricing (with reseller discount), wallet debit/top-up, reseller management, audit logging, MikroTik sync, dashboard KPIs.
- **Repository pattern:** User, Wallet, WalletTransaction, VoucherBatch, Voucher, Package (internet packages); interfaces in `Contracts/`, implementations in `Eloquent/`.
- **Controller logic:** Controllers validate (Form Requests), authorize (policies), call services, return views or redirects. No business logic in controllers.

---

## 3. CORE FEATURES IMPLEMENTED

### Authentication & roles

- **Laravel Breeze:** Login, register, logout, forgot password, reset password, email verification, password confirmation.
- **Roles:** `User` has `role` (enum: admin, reseller), `status` (active/inactive), `discount_percent` (0–100). Helpers: `isAdmin()`, `isReseller()`.
- **Role middleware:** `EnsureUserRole` — `role:admin` or `role:reseller` on route groups.
- **Post-login redirect:** `DashboardRedirectController` → `RoleRedirectService` → `admin.dashboard` or `reseller.dashboard`.

### Reseller features

- **Reseller dashboard:** Wallet balance, today’s voucher count, quick voucher generator (package + quantity), recent vouchers list. Uses `ResellerDashboardController`, `reseller.dashboard` view.
- **Voucher batch generation:** Choose package + quantity → debit wallet → create batch + vouchers in one transaction. Route: `POST reseller/voucher-batches` (`reseller.voucher-batches.store`) with `throttle:voucher-generation`.
- **Voucher batches:** Index (filter by status), create, show (with paginated vouchers), print/thermal/card views.
- **Packages (read-only):** List active internet packages for reseller.
- **Wallet:** Single wallet per reseller; view balance and ledger (`reseller.wallet.show`).

### Admin features

- **Admin dashboard:** KPIs (e.g. active users, reseller count, wallet totals), role-based view.
- **Packages (CRUD):** Internet packages — code, name, price, validity, bandwidth, MikroTik profile, `is_active`; toggle active.
- **Wallets:** List reseller wallets (search), top-up, **manual adjust** (amount + description, modal UI), ledger per wallet.
- **Resellers (CRUD):** Create/edit resellers (name, email, phone, status, **discount_percent**), toggle status, reset password.
- **Voucher batches:** List and show (read-only).
- **MikroTik logs:** Index of MikroTik API logs.
- **Failed jobs:** Index and retry failed queue jobs.
- **VCR settings:** Edit/update voucher code/password generation settings (format, length, character set).
- **System settings:** App-level settings (e.g. MikroTik host/port/timeout, hotspot name) via `Setting` model and `settings.index` / `settings.update`.

### Reports

- **Voucher report:** List and export (e.g. Excel) voucher data; routes under `reports.vouchers.*`.

### Wallet & financial

- **Wallet:** One wallet per user (resellers); balance, currency (IDR), `is_locked`.
- **Wallet transactions:** Every debit/credit recorded (type, source, amount, balance_before, balance_after, description, created_by). Sources: topup, voucher_purchase, manual_adjustment, refund.
- **Voucher generation cost:** Unit price from package with optional reseller `discount_percent`; total cost = unit price × quantity; debited in one go before creating batch and vouchers.

### MikroTik integration

- **Services:** `MikrotikService`, `HotspotUserSyncService`; clients: `RealMikrotikClient`, `FakeMikrotikClient` (interface).
- **Jobs:** `PushVoucherToMikrotikJob` (after voucher batch generation), `SyncHotspotUsersJob` (scheduled).
- **Logs:** MikroTik API calls logged to `mikrotik_logs` (e.g. status, message).
- **Config:** `config/mikrotik.php`; part of config can be overridden by `Setting` (e.g. host, port, timeout).

### Other

- **Audit log:** `AuditLogService` logs actions (e.g. reseller created/updated, wallet top-up) with actor, model, old/new values, IP.
- **Active user snapshots:** `CaptureActiveUsersSnapshotJob` scheduled; snapshot data for dashboard/analytics.
- **Scheduled tasks:** `vouchers:sync` and `CaptureActiveUsersSnapshotJob` every five minutes; `queue:prune-failed --days=7` daily.
- **Custom Artisan:** `BackupDatabaseCommand`, `PruneFailedJobsCommand`, `vouchers:sync`.

---

## 4. FINANCIAL SAFETY MECHANISMS

- **Single DB::transaction for voucher generation:**  
  `VoucherGenerationService::generateBatch()` runs inside one `DB::transaction()`. In order:
  1. Reseller overload check (optional hard limit).
  2. Unit price and total cost (with reseller discount).
  3. Get reseller wallet.
  4. **Debit wallet** via `WalletDebitService::debitWithinTransaction()`.
  5. Create voucher batch record.
  6. Build and insert voucher records (`insertMany`).
  7. `DB::afterCommit()` dispatch `PushVoucherToMikrotikJob`.

- **Wallet debit rules:**
  - `debitWithinTransaction()` **must** run inside an open transaction (`ensureActiveTransaction()`).
  - Wallet row is locked with **`lockForUpdate()`** in `WalletRepository::lockById()` before balance read/update.
  - Balance checked in cents; throws `InsufficientBalanceException` if insufficient; balance never goes negative.
  - One wallet transaction row per debit (balance_before, balance_after, amount, source, description, created_by).

- **Rollback behaviour:**  
  Any exception in `generateBatch()` (insufficient balance, overload, unique code failure, DB error) rolls back the whole transaction: no wallet debit, no batch, no vouchers.  
  Controller catches `InsufficientBalanceException` and `ResellerOverloadException` and returns validation-style errors without committing.

- **Admin wallet adjust:**  
  `WalletController::adjust()` uses its own `DB::transaction()`, locks wallet by id, updates balance, creates a `manual_adjustment` transaction row; prevents negative balance with a check before update.

---

## 5. CONCURRENCY / RACE CONDITION PROTECTION

- **Row-level lock:**  
  On debit, the wallet is loaded with `Wallet::query()->whereKey($walletId)->lockForUpdate()->firstOrFail()`. Concurrent requests that debit the same wallet block on this lock until the holding transaction commits or rolls back.

- **Single transaction boundary:**  
  Wallet debit, batch creation, and voucher inserts happen in one transaction, so no partial state is visible to other transactions.

- **Guarantee:**  
  Two simultaneous voucher-generation requests for the same reseller: one acquires the wallet lock, completes debit + batch + vouchers and commits; the other waits, then runs with the updated balance (or fails balance check). Prevents double spend and negative balances.

- **Wallet `is_locked`:**  
  Checked in debit flow; can be used for maintenance lockout (implementation is present).

---

## 6. DATABASE STRUCTURE

| Table | Purpose |
|-------|--------|
| **users** | Auth + role (admin/reseller), status, phone, discount_percent. |
| **wallets** | One per user; balance, currency, is_locked. |
| **wallet_transactions** | All balance changes; type (credit/debit), source, amount, balance_before/after, description, created_by. |
| **internet_packages** | Packages (code, name, price, validity, bandwidth, mikrotik_profile, is_active). |
| **voucher_batches** | Batch per generation; reseller_id, package_id, batch_code, qty_requested/generated, unit_price, total_cost, status, paid_at, generated_at. |
| **vouchers** | Per-voucher rows; batch_id, reseller_id, package_id, code (unique), username, password (encrypted), status, cost_price, sold_price, generated_at. |
| **vcr_settings** | Voucher code/password generation (format, length, character set, etc.). |
| **settings** | Key-value app settings (e.g. MikroTik, hotspot name). |
| **mikrotik_logs** | MikroTik API call logs. |
| **mikrotik_servers** | MikroTik server config (if used). |
| **audit_logs** | Audit trail (actor, action, model, old/new values, IP). |
| **active_user_snapshots** | Snapshot data for dashboard/analytics. |
| **jobs** | Queue jobs (database driver). |
| **failed_jobs** | Failed queue jobs. |
| **cache** | Cache table. |
| **activity_logs** | Migration present (e.g. for activity logging). |

Indexes exist on reseller_id/status for vouchers and voucher_batches, wallet_id/source/created_at for wallet_transactions, and code uniqueness for vouchers and batch_code for voucher_batches.

---

## 7. SECURITY FEATURES

- **Authentication:** Breeze; login throttling via `LoginRequest` (e.g. 5 attempts, then throttle message).
- **Authorization:** Policies for InternetPackage, Wallet, VoucherBatch; `authorize()` in controllers; role middleware on admin and reseller route groups.
- **Rate limiting:**
  - **Voucher generation:** `throttle:voucher-generation` — 5 requests per minute per user (or per IP if no user); custom 429 response (Indonesian message).
  - **Email verification:** `throttle:6,1` on verify and send routes.
- **Validation:** Form Requests for all relevant inputs (e.g. store voucher batch: internet_package_id exists and is active, quantity 1–500).
- **CSRF:** Laravel CSRF on all state-changing web routes.
- **Passwords:** Voucher passwords encrypted (e.g. `Crypt::encryptString`) before storage.
- **Sensitive config:** `.env` not in repo; `.env.example` present; settings table for runtime config.

---

## 8. DEPLOYMENT READINESS

- **Environment:** `.env` for app key, DB, queue, cache, mail, optional MikroTik; `.env.example` documents variables.
- **Queue:** Database driver; jobs for MikroTik push and sync; failed job pruning scheduled.
- **Scheduler:** `routes/console.php` schedules sync, snapshots, and prune; cron must run `schedule:run`.
- **Backup:** Custom `BackupDatabaseCommand`; optional cron (e.g. documented in BACKUP.md).
- **Frontend:** Vite (Tailwind, Alpine.js); `npm run build` for production assets.
- **Migrations:** Sequential migrations; foreign keys and indexes in place.
- **Seeding:** DatabaseSeeder, AdminUserSeeder, SettingsSeeder (and any package/feature seeders).

---

## 9. POTENTIAL RISKS OR MISSING HARDENING

- **Unique voucher code collision:** Generation uses in-batch uniqueness and DB check; under very high concurrency, duplicate code could theoretically be inserted by another process before commit. Mitigation: unique index on `vouchers.code` causes transaction failure and rollback; retry or user message can be used.
- **Reseller overload:** Only “hard” mode throws; “soft” mode is config-based and may need clear product definition to avoid unbounded active vouchers per reseller.
- **Email verification:** Optional (Breeze default); if turned on, ensure mail config and rate limits are suitable for production.
- **API absence:** No `routes/api.php`; no token-based API. If future API is added, consider separate auth and rate limiting.
- **Logging/monitoring:** Standard Laravel logging; consider structured logs and monitoring for failed jobs, debit failures, and MikroTik errors in production.
- **Backup/restore:** Backup command exists; ensure backup storage, retention, and restore procedure are defined and tested.
- **Activity_logs table:** Migration present; if unused, consider removing or documenting intended use to avoid confusion.

---

## Summary

The project is a **role-based, transaction-safe voucher and wallet system** with a clear service/repository architecture, **DB::transaction()** and **lockForUpdate()** for wallet and voucher generation, rate limiting on voucher generation, policies for authorization, and scheduled jobs for MikroTik sync and maintenance. Financial operations are atomic and protected against race conditions; the main remaining hardening is around operational concerns (monitoring, backup/restore, and optional edge cases like voucher code uniqueness under extreme concurrency).

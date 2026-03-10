<?php

use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\InternetPackageController as AdminInternetPackageController;
use App\Http\Controllers\Admin\MikrotikLogController as AdminMikrotikLogController;
use App\Http\Controllers\Admin\ResellerController as AdminResellerController;
use App\Http\Controllers\Admin\VcrSettingController as AdminVcrSettingController;
use App\Http\Controllers\Admin\VoucherBatchController as AdminVoucherBatchController;
use App\Http\Controllers\Admin\WalletController as AdminWalletController;
use App\Http\Controllers\Admin\FailedJobController as AdminFailedJobController;
use App\Http\Controllers\DashboardRedirectController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\Reseller\DashboardController as ResellerDashboardController;
use App\Http\Controllers\Reseller\InternetPackageController as ResellerInternetPackageController;
use App\Http\Controllers\Reseller\VoucherBatchController as ResellerVoucherBatchController;
use App\Http\Controllers\Reseller\WalletController as ResellerWalletController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', DashboardRedirectController::class)->name('dashboard');

    Route::prefix('reports')
        ->as('reports.')
        ->group(function (): void {
            Route::get('/vouchers', [ReportController::class, 'index'])->name('vouchers.index');
            Route::get('/vouchers/export', [ReportController::class, 'exportVoucherReport'])->name('vouchers.export');
        });

    Route::prefix('admin')
        ->as('admin.')
        ->middleware('role:admin')
        ->group(function (): void {
            Route::get('/dashboard', AdminDashboardController::class)->name('dashboard');

            Route::prefix('packages')
                ->as('packages.')
                ->group(function (): void {
                    Route::get('/', [AdminInternetPackageController::class, 'index'])->name('index');
                    Route::get('/create', [AdminInternetPackageController::class, 'create'])->name('create');
                    Route::post('/', [AdminInternetPackageController::class, 'store'])->name('store');
                    Route::get('/{internetPackage}/edit', [AdminInternetPackageController::class, 'edit'])->name('edit');
                    Route::put('/{internetPackage}', [AdminInternetPackageController::class, 'update'])->name('update');
                    Route::patch('/{internetPackage}/toggle-active', [AdminInternetPackageController::class, 'toggleActive'])->name('toggle-active');
                });

            Route::prefix('wallets')
                ->as('wallets.')
                ->group(function (): void {
                    Route::get('/', [AdminWalletController::class, 'index'])->name('index');
                    Route::post('/{wallet}/topup', [AdminWalletController::class, 'topUp'])->name('topup');
                    Route::post('/{wallet}/adjust', [AdminWalletController::class, 'adjust'])->name('adjust');
                    Route::get('/{wallet}/ledger', [AdminWalletController::class, 'ledger'])->name('ledger');
                });

            Route::prefix('resellers')
                ->as('resellers.')
                ->group(function (): void {
                    Route::get('/', [AdminResellerController::class, 'index'])->name('index');
                    Route::get('/create', [AdminResellerController::class, 'create'])->name('create');
                    Route::post('/', [AdminResellerController::class, 'store'])->name('store');
                    Route::get('/{reseller}/edit', [AdminResellerController::class, 'edit'])->whereNumber('reseller')->name('edit');
                    Route::put('/{reseller}', [AdminResellerController::class, 'update'])->whereNumber('reseller')->name('update');
                    Route::patch('/{reseller}/toggle-status', [AdminResellerController::class, 'toggleStatus'])->whereNumber('reseller')->name('toggle-status');
                    Route::patch('/{reseller}/reset-password', [AdminResellerController::class, 'resetPassword'])->whereNumber('reseller')->name('reset-password');
                });

            Route::prefix('voucher-batches')
                ->as('voucher-batches.')
                ->group(function (): void {
                    Route::get('/', [AdminVoucherBatchController::class, 'index'])->name('index');
                    Route::get('/{voucherBatch}', [AdminVoucherBatchController::class, 'show'])->name('show');
                });

            Route::prefix('mikrotik-logs')
                ->as('mikrotik-logs.')
                ->group(function (): void {
                    Route::get('/', [AdminMikrotikLogController::class, 'index'])->name('index');
                });

            Route::prefix('failed-jobs')
                ->as('failed-jobs.')
                ->group(function (): void {
                    Route::get('/', [AdminFailedJobController::class, 'index'])->name('index');
                    Route::post('/{failedJob}/retry', [AdminFailedJobController::class, 'retry'])->whereNumber('failedJob')->name('retry');
                });

            Route::prefix('vcr-settings')
                ->as('vcr-settings.')
                ->group(function (): void {
                    Route::get('/', [AdminVcrSettingController::class, 'edit'])->name('edit');
                    Route::put('/', [AdminVcrSettingController::class, 'update'])->name('update');
                });
        });

    Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');
    Route::post('/settings', [SettingsController::class, 'update'])->name('settings.update');

    Route::prefix('reseller')
        ->as('reseller.')
        ->middleware('role:reseller')
        ->group(function (): void {
            Route::get('/dashboard', ResellerDashboardController::class)->name('dashboard');
            Route::get('/packages', [ResellerInternetPackageController::class, 'index'])->name('packages.index');
            Route::get('/wallet', [ResellerWalletController::class, 'show'])->name('wallet.show');

            Route::prefix('voucher-batches')
                ->as('voucher-batches.')
                ->group(function (): void {
                    Route::get('/', [ResellerVoucherBatchController::class, 'index'])->name('index');
                    Route::get('/create', [ResellerVoucherBatchController::class, 'create'])->name('create');
                    Route::post('/', [ResellerVoucherBatchController::class, 'store'])
                        ->middleware('throttle:voucher-generation')
                        ->name('store');
                    Route::get('/{batch}/print', [ResellerVoucherBatchController::class, 'printView'])->name('print');
                    Route::get('/{batch}/thermal', [ResellerVoucherBatchController::class, 'thermalView'])->name('thermal');
                    Route::get('/{batch}/card', [ResellerVoucherBatchController::class, 'cardView'])->name('card');
                    Route::get('/{voucherBatch}', [ResellerVoucherBatchController::class, 'show'])->name('show');
                });
        });

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';

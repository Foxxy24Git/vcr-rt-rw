<?php

namespace App\Providers;

use App\Repositories\Contracts\PackageRepositoryInterface;
use App\Repositories\Contracts\UserRepositoryInterface;
use App\Repositories\Contracts\VoucherBatchRepositoryInterface;
use App\Repositories\Contracts\VoucherRepositoryInterface;
use App\Repositories\Contracts\WalletRepositoryInterface;
use App\Repositories\Contracts\WalletTransactionRepositoryInterface;
use App\Repositories\Eloquent\PackageRepository;
use App\Repositories\Eloquent\UserRepository;
use App\Repositories\Eloquent\VoucherBatchRepository;
use App\Repositories\Eloquent\VoucherRepository;
use App\Repositories\Eloquent\WalletRepository;
use App\Repositories\Eloquent\WalletTransactionRepository;
use App\Services\Mikrotik\Clients\RealMikrotikClient;
use App\Services\Mikrotik\Contracts\MikrotikClientInterface;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register repository bindings.
     */
    public function register(): void
    {
        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
        $this->app->bind(PackageRepositoryInterface::class, PackageRepository::class);
        $this->app->bind(WalletRepositoryInterface::class, WalletRepository::class);
        $this->app->bind(WalletTransactionRepositoryInterface::class, WalletTransactionRepository::class);
        $this->app->bind(VoucherBatchRepositoryInterface::class, VoucherBatchRepository::class);
        $this->app->bind(VoucherRepositoryInterface::class, VoucherRepository::class);
        $this->app->bind(MikrotikClientInterface::class, RealMikrotikClient::class);
    }
}

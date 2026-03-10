<?php

namespace App\Providers;

use App\Models\InternetPackage;
use App\Models\User;
use App\Models\VoucherBatch;
use App\Models\Wallet;
use App\Observers\UserObserver;
use App\Policies\InternetPackagePolicy;
use App\Policies\VoucherBatchPolicy;
use App\Policies\WalletPolicy;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::policy(InternetPackage::class, InternetPackagePolicy::class);
        Gate::policy(Wallet::class, WalletPolicy::class);
        Gate::policy(VoucherBatch::class, VoucherBatchPolicy::class);

        RateLimiter::for('voucher-generation', function (Request $request): Limit {
            $identifier = $request->user()?->id
                ? 'voucher-generation-user:'.$request->user()->id
                : 'voucher-generation-ip:'.$request->ip();

            return Limit::perMinute(5)
                ->by($identifier)
                ->response(function (Request $request, array $headers) {
                    $message = 'Terlalu banyak percobaan generate voucher. Silakan coba lagi dalam 1 menit.';

                    if ($request->expectsJson()) {
                        return response()->json([
                            'message' => $message,
                        ], 429, $headers);
                    }

                    return response($message, 429, $headers);
                });
        });

        User::observe(UserObserver::class);
    }
}

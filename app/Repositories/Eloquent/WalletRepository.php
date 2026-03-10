<?php

namespace App\Repositories\Eloquent;

use App\Enums\UserRole;
use App\Models\Wallet;
use App\Repositories\Contracts\WalletRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class WalletRepository implements WalletRepositoryInterface
{
    public function firstOrCreateForUser(int $userId): Wallet
    {
        return Wallet::query()->firstOrCreate(
            ['user_id' => $userId],
            [
                'balance' => 0,
                'currency' => 'IDR',
                'is_locked' => false,
            ]
        );
    }

    public function findByUserId(int $userId): ?Wallet
    {
        return Wallet::query()->where('user_id', $userId)->first();
    }

    public function findByIdOrFail(int $walletId): Wallet
    {
        return Wallet::query()->findOrFail($walletId);
    }

    public function lockById(int $walletId): Wallet
    {
        return Wallet::query()->whereKey($walletId)->lockForUpdate()->firstOrFail();
    }

    public function lockByUserId(int $userId): Wallet
    {
        return Wallet::query()->where('user_id', $userId)->lockForUpdate()->firstOrFail();
    }

    public function updateBalance(Wallet $wallet, string $balance): Wallet
    {
        $wallet->update([
            'balance' => $balance,
        ]);

        return $wallet->refresh();
    }

    public function paginateResellerWallets(int $perPage = 10, ?string $search = null): LengthAwarePaginator
    {
        return Wallet::query()
            ->with('user:id,name,email,role,status')
            ->whereHas('user', fn ($query) => $query->where('role', UserRole::RESELLER->value))
            ->when($search, function ($query, string $search): void {
                $query->whereHas('user', function ($subQuery) use ($search): void {
                    $subQuery
                        ->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->orderByDesc('id')
            ->paginate($perPage)
            ->withQueryString();
    }
}

<?php

namespace App\Repositories\Contracts;

use App\Models\Wallet;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface WalletRepositoryInterface
{
    public function firstOrCreateForUser(int $userId): Wallet;

    public function findByUserId(int $userId): ?Wallet;

    public function findByIdOrFail(int $walletId): Wallet;

    public function lockById(int $walletId): Wallet;

    public function lockByUserId(int $userId): Wallet;

    public function updateBalance(Wallet $wallet, string $balance): Wallet;

    public function paginateResellerWallets(int $perPage = 10, ?string $search = null): LengthAwarePaginator;
}

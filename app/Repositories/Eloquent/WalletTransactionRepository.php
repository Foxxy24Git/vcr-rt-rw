<?php

namespace App\Repositories\Eloquent;

use App\Models\WalletTransaction;
use App\Repositories\Contracts\WalletTransactionRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class WalletTransactionRepository implements WalletTransactionRepositoryInterface
{
    public function create(array $payload): WalletTransaction
    {
        return WalletTransaction::query()->create($payload);
    }

    public function paginateByWallet(int $walletId, int $perPage = 20): LengthAwarePaginator
    {
        return WalletTransaction::query()
            ->with('creator:id,name,email')
            ->where('wallet_id', $walletId)
            ->latest()
            ->paginate($perPage)
            ->withQueryString();
    }
}

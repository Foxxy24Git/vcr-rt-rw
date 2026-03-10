<?php

namespace App\Repositories\Contracts;

use App\Models\WalletTransaction;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface WalletTransactionRepositoryInterface
{
    /**
     * @param  array<string, mixed>  $payload
     */
    public function create(array $payload): WalletTransaction;

    public function paginateByWallet(int $walletId, int $perPage = 20): LengthAwarePaginator;
}

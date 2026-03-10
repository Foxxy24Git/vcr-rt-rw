<?php

namespace App\Services\Wallet;

use App\Models\User;
use App\Models\Wallet;
use App\Repositories\Contracts\WalletRepositoryInterface;
use App\Repositories\Contracts\WalletTransactionRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use InvalidArgumentException;

class WalletService
{
    public function __construct(
        private readonly WalletRepositoryInterface $walletRepository,
        private readonly WalletTransactionRepositoryInterface $walletTransactionRepository
    ) {}

    public function ensureWalletForReseller(User $user): Wallet
    {
        if (! $user->isReseller()) {
            throw new InvalidArgumentException('Wallet hanya berlaku untuk reseller.');
        }

        return $this->walletRepository->firstOrCreateForUser($user->id);
    }

    public function getWalletByUser(User $user): Wallet
    {
        return $this->ensureWalletForReseller($user);
    }

    public function getWalletById(int $walletId): Wallet
    {
        return $this->walletRepository->findByIdOrFail($walletId);
    }

    public function getWalletsForAdmin(?string $search = null, int $perPage = 10): LengthAwarePaginator
    {
        return $this->walletRepository->paginateResellerWallets(
            perPage: $perPage,
            search: $search
        );
    }

    public function getWalletLedger(Wallet $wallet, int $perPage = 20): LengthAwarePaginator
    {
        return $this->walletTransactionRepository->paginateByWallet(
            walletId: $wallet->id,
            perPage: $perPage
        );
    }
}

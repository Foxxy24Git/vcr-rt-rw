<?php

namespace App\Services\Wallet;

use App\Models\Wallet;
use App\Models\WalletTransaction;
use App\Repositories\Contracts\WalletRepositoryInterface;
use App\Repositories\Contracts\WalletTransactionRepositoryInterface;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class WalletTopUpService
{
    public function __construct(
        private readonly WalletRepositoryInterface $walletRepository,
        private readonly WalletTransactionRepositoryInterface $walletTransactionRepository
    ) {}

    public function topUp(
        Wallet $wallet,
        float|string $amount,
        int $adminUserId,
        ?string $description = null
    ): WalletTransaction {
        return DB::transaction(function () use ($wallet, $amount, $adminUserId, $description): WalletTransaction {
            $lockedWallet = $this->walletRepository->lockById($wallet->id);

            if ($lockedWallet->is_locked) {
                throw new InvalidArgumentException('Wallet sedang dikunci.');
            }

            $amountCents = $this->toCents($amount);

            if ($amountCents <= 0) {
                throw new InvalidArgumentException('Nominal top up harus lebih dari nol.');
            }

            $beforeCents = $this->toCents((string) $lockedWallet->balance);
            $afterCents = $beforeCents + $amountCents;

            $balanceBefore = $this->fromCents($beforeCents);
            $balanceAfter = $this->fromCents($afterCents);

            $this->walletRepository->updateBalance($lockedWallet, $balanceAfter);

            return $this->walletTransactionRepository->create([
                'wallet_id' => $lockedWallet->id,
                'type' => WalletTransaction::TYPE_CREDIT,
                'source' => WalletTransaction::SOURCE_TOPUP,
                'amount' => $this->fromCents($amountCents),
                'balance_before' => $balanceBefore,
                'balance_after' => $balanceAfter,
                'description' => $description ?: 'Top up wallet reseller',
                'created_by' => $adminUserId,
            ]);
        });
    }

    private function toCents(float|string $amount): int
    {
        return (int) round(((float) $amount) * 100);
    }

    private function fromCents(int $amount): string
    {
        return number_format($amount / 100, 2, '.', '');
    }
}

<?php

namespace App\Services\Wallet;

use App\Exceptions\InsufficientBalanceException;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use App\Repositories\Contracts\WalletRepositoryInterface;
use App\Repositories\Contracts\WalletTransactionRepositoryInterface;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class WalletDebitService
{
    public function __construct(
        private readonly WalletRepositoryInterface $walletRepository,
        private readonly WalletTransactionRepositoryInterface $walletTransactionRepository
    ) {}

    public function debit(
        Wallet $wallet,
        float|string $amount,
        ?int $actorUserId = null,
        string $source = WalletTransaction::SOURCE_MANUAL_ADJUSTMENT,
        ?string $description = null,
        ?string $referenceType = null,
        ?int $referenceId = null
    ): WalletTransaction {
        return DB::transaction(fn (): WalletTransaction => $this->debitWithinTransaction(
            wallet: $wallet,
            amount: $amount,
            actorUserId: $actorUserId,
            source: $source,
            description: $description,
            referenceType: $referenceType,
            referenceId: $referenceId
        ));
    }

    public function debitWithinTransaction(
        Wallet $wallet,
        float|string $amount,
        ?int $actorUserId = null,
        string $source = WalletTransaction::SOURCE_MANUAL_ADJUSTMENT,
        ?string $description = null,
        ?string $referenceType = null,
        ?int $referenceId = null
    ): WalletTransaction {
        $this->ensureActiveTransaction();

        // Kunci row wallet agar debit paralel tidak bisa membaca saldo lama.
        $lockedWallet = $this->walletRepository->lockById($wallet->id);

        if ($lockedWallet->is_locked) {
            throw new InvalidArgumentException('Wallet sedang dikunci.');
        }

        $amountCents = $this->toCents($amount);

        if ($amountCents <= 0) {
            throw new InvalidArgumentException('Nominal debit harus lebih dari nol.');
        }

        $beforeCents = $this->toCents((string) $lockedWallet->balance);

        if ($beforeCents < $amountCents) {
            throw new InsufficientBalanceException('Saldo wallet tidak mencukupi.');
        }

        $afterCents = $beforeCents - $amountCents;

        if ($afterCents < 0) {
            throw new InsufficientBalanceException('Saldo wallet tidak boleh negatif.');
        }

        $balanceBefore = $this->fromCents($beforeCents);
        $balanceAfter = $this->fromCents($afterCents);

        $this->walletRepository->updateBalance($lockedWallet, $balanceAfter);

        return $this->walletTransactionRepository->create([
            'wallet_id' => $lockedWallet->id,
            'type' => WalletTransaction::TYPE_DEBIT,
            'source' => $source,
            'amount' => $this->fromCents($amountCents),
            'balance_before' => $balanceBefore,
            'balance_after' => $balanceAfter,
            'reference_type' => $referenceType,
            'reference_id' => $referenceId,
            'description' => $description ?: 'Debit wallet reseller',
            'created_by' => $actorUserId,
        ]);
    }

    private function toCents(float|string $amount): int
    {
        return (int) round(((float) $amount) * 100);
    }

    private function fromCents(int $amount): string
    {
        return number_format($amount / 100, 2, '.', '');
    }

    private function ensureActiveTransaction(): void
    {
        if (DB::transactionLevel() < 1) {
            throw new InvalidArgumentException('Debit wallet harus dijalankan di dalam DB transaction.');
        }
    }
}

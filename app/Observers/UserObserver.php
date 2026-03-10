<?php

namespace App\Observers;

use App\Models\User;
use App\Services\Wallet\WalletService;

class UserObserver
{
    public function __construct(
        private readonly WalletService $walletService
    ) {}

    public function created(User $user): void
    {
        if ($user->isReseller()) {
            $this->walletService->ensureWalletForReseller($user);
        }
    }
}

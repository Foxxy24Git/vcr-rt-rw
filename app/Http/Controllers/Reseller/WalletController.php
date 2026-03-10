<?php

namespace App\Http\Controllers\Reseller;

use App\Http\Controllers\Controller;
use App\Services\Wallet\WalletService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class WalletController extends Controller
{
    public function __construct(
        private readonly WalletService $walletService
    ) {}

    public function show(Request $request): View
    {
        $user = $request->user();
        $wallet = $this->walletService->getWalletByUser($user);

        $this->authorize('view', $wallet);

        $transactions = $this->walletService->getWalletLedger($wallet);

        return view('reseller.wallet.show', [
            'wallet' => $wallet,
            'transactions' => $transactions,
        ]);
    }
}

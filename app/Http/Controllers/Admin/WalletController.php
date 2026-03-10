<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\WalletTopUpRequest;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use App\Services\Audit\AuditLogService;
use App\Services\Wallet\WalletService;
use App\Services\Wallet\WalletTopUpService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class WalletController extends Controller
{
    public function __construct(
        private readonly WalletService $walletService,
        private readonly WalletTopUpService $walletTopUpService,
        private readonly AuditLogService $auditLogService
    ) {}

    public function index(Request $request): View
    {
        $this->authorize('viewAny', Wallet::class);

        $search = $request->string('search')->toString();
        $wallets = $this->walletService->getWalletsForAdmin(
            search: $search ?: null
        );

        return view('admin.wallets.index', [
            'wallets' => $wallets,
            'search' => $search,
        ]);
    }

    public function topUp(WalletTopUpRequest $request, Wallet $wallet): RedirectResponse
    {
        $balanceBefore = (string) $wallet->balance;
        $amount = (float) $request->validated('amount');

        $this->walletTopUpService->topUp(
            wallet: $wallet,
            amount: $amount,
            adminUserId: (int) $request->user()->id,
            description: $request->validated('description')
        );
        $wallet->refresh();

        $this->auditLogService->logAction(
            actor: $request->user(),
            action: 'wallet.topup',
            model: $wallet,
            oldValues: [
                'balance' => $balanceBefore,
            ],
            newValues: [
                'balance' => (string) $wallet->balance,
                'amount' => number_format($amount, 2, '.', ''),
            ],
            ipAddress: $request->ip()
        );

        return redirect()
            ->route('admin.wallets.index')
            ->with('status', 'Top up wallet berhasil.');
    }

    public function ledger(Wallet $wallet): View
    {
        $this->authorize('viewLedger', $wallet);

        $wallet->load('user');
        $transactions = $this->walletService->getWalletLedger($wallet);

        return view('admin.wallets.ledger', [
            'wallet' => $wallet,
            'transactions' => $transactions,
        ]);
    }

    public function adjust(Request $request, Wallet $wallet): RedirectResponse
    {
        $this->authorize('adjust', $wallet);

        $request->validate([
            'amount' => 'required|numeric',
            'description' => 'required|string|max:255',
        ]);

        $amount = (float) $request->input('amount');
        $description = $request->input('description');

        DB::transaction(function () use ($wallet, $amount, $description, $request): void {
            $locked = Wallet::query()->whereKey($wallet->id)->lockForUpdate()->firstOrFail();
            $balanceBefore = (float) $locked->balance;
            $balanceAfter = $balanceBefore + $amount;

            if ($balanceAfter < 0) {
                throw new \InvalidArgumentException('Saldo wallet tidak boleh negatif setelah penyesuaian.');
            }

            $locked->update(['balance' => $balanceAfter]);

            WalletTransaction::create([
                'wallet_id' => $locked->id,
                'type' => $amount >= 0 ? WalletTransaction::TYPE_CREDIT : WalletTransaction::TYPE_DEBIT,
                'source' => WalletTransaction::SOURCE_MANUAL_ADJUSTMENT,
                'amount' => abs($amount),
                'balance_before' => $balanceBefore,
                'balance_after' => $balanceAfter,
                'description' => $description,
                'created_by' => $request->user()->id,
            ]);
        });

        return back()->with('success', 'Wallet adjusted successfully.');
    }
}

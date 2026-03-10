<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Exceptions\InsufficientBalanceException;
use App\Models\User;
use App\Models\WalletTransaction;
use App\Services\Wallet\WalletDebitService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WalletSystemTest extends TestCase
{
    use RefreshDatabase;

    public function test_successful_topup(): void
    {
        $admin = User::factory()->create([
            'role' => UserRole::ADMIN->value,
        ]);

        $reseller = User::factory()->create([
            'role' => UserRole::RESELLER->value,
        ]);

        $wallet = $reseller->wallet()->first();

        $this->assertNotNull($wallet);
        $this->assertSame('0.00', $wallet->balance);

        $response = $this->actingAs($admin)->post(route('admin.wallets.topup', $wallet), [
            'amount' => 150000,
            'description' => 'Top up awal reseller',
        ]);

        $response
            ->assertRedirect(route('admin.wallets.index'))
            ->assertSessionHas('status', 'Top up wallet berhasil.');

        $wallet->refresh();

        $this->assertSame('150000.00', $wallet->balance);

        $this->assertDatabaseHas('wallet_transactions', [
            'wallet_id' => $wallet->id,
            'type' => WalletTransaction::TYPE_CREDIT,
            'source' => WalletTransaction::SOURCE_TOPUP,
            'amount' => '150000.00',
            'balance_before' => '0.00',
            'balance_after' => '150000.00',
            'created_by' => $admin->id,
        ]);
    }

    public function test_failed_debit_when_insufficient_balance(): void
    {
        $reseller = User::factory()->create([
            'role' => UserRole::RESELLER->value,
        ]);

        $wallet = $reseller->wallet()->firstOrFail();
        $service = app(WalletDebitService::class);

        $this->expectException(InsufficientBalanceException::class);

        try {
            $service->debit(
                wallet: $wallet,
                amount: 10000,
                actorUserId: null,
                source: WalletTransaction::SOURCE_MANUAL_ADJUSTMENT,
                description: 'Debit manual'
            );
        } finally {
            $wallet->refresh();
            $this->assertSame('0.00', $wallet->balance);
            $this->assertDatabaseCount('wallet_transactions', 0);
        }
    }

    public function test_reseller_cannot_see_other_reseller_wallet(): void
    {
        $resellerA = User::factory()->create([
            'role' => UserRole::RESELLER->value,
        ]);

        $resellerB = User::factory()->create([
            'role' => UserRole::RESELLER->value,
        ]);

        $walletB = $resellerB->wallet()->firstOrFail();

        $this->actingAs($resellerA)
            ->get(route('admin.wallets.ledger', $walletB))
            ->assertForbidden();
    }

    public function test_audit_log_created_when_admin_topup_wallet(): void
    {
        $admin = User::factory()->create([
            'role' => UserRole::ADMIN->value,
            'status' => 'active',
        ]);

        $reseller = User::factory()->create([
            'role' => UserRole::RESELLER->value,
            'status' => 'active',
        ]);

        $wallet = $reseller->wallet()->firstOrFail();

        $this->actingAs($admin)->post(route('admin.wallets.topup', $wallet), [
            'amount' => 50000,
            'description' => 'Top up audit log test',
        ])->assertRedirect(route('admin.wallets.index'));

        $this->assertDatabaseHas('audit_logs', [
            'actor_id' => $admin->id,
            'action' => 'wallet.topup',
            'model_type' => \App\Models\Wallet::class,
            'model_id' => $wallet->id,
            'ip_address' => '127.0.0.1',
        ]);
    }

    public function test_simulated_concurrent_debit_keeps_balance_consistent(): void
    {
        $reseller = User::factory()->create([
            'role' => UserRole::RESELLER->value,
        ]);

        $wallet = $reseller->wallet()->firstOrFail();
        $wallet->update([
            'balance' => '10000.00',
        ]);

        $service = app(WalletDebitService::class);
        $successCount = 0;
        $failedCount = 0;

        // Simulasi dua request debit paralel dengan nominal yang sama.
        for ($attempt = 0; $attempt < 2; $attempt++) {
            try {
                $service->debit(
                    wallet: $wallet,
                    amount: 10000,
                    actorUserId: null,
                    source: WalletTransaction::SOURCE_MANUAL_ADJUSTMENT,
                    description: 'Simulasi concurrent debit'
                );

                $successCount++;
            } catch (InsufficientBalanceException) {
                $failedCount++;
            }
        }

        $wallet->refresh();

        $this->assertSame('0.00', $wallet->balance);
        $this->assertGreaterThanOrEqual(0, (float) $wallet->balance);
        $this->assertSame(1, $successCount);
        $this->assertSame(1, $failedCount);

        $this->assertDatabaseCount('wallet_transactions', 1);
        $this->assertDatabaseHas('wallet_transactions', [
            'wallet_id' => $wallet->id,
            'type' => WalletTransaction::TYPE_DEBIT,
            'source' => WalletTransaction::SOURCE_MANUAL_ADJUSTMENT,
            'amount' => '10000.00',
            'balance_before' => '10000.00',
            'balance_after' => '0.00',
        ]);
    }
}

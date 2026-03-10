<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Jobs\PushVoucherToMikrotikJob;
use App\Models\InternetPackage;
use App\Models\MikrotikLog;
use App\Models\User;
use App\Models\VcrSetting;
use App\Models\Voucher;
use App\Models\VoucherBatch;
use App\Models\WalletTransaction;
use App\Services\Mikrotik\MikrotikService;
use App\Services\Wallet\WalletTopUpService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class VoucherGenerationTest extends TestCase
{
    use RefreshDatabase;

    public function test_successful_generation(): void
    {
        $admin = User::factory()->create([
            'role' => UserRole::ADMIN->value,
        ]);

        $reseller = User::factory()->create([
            'role' => UserRole::RESELLER->value,
        ]);

        $package = InternetPackage::factory()->create([
            'price' => 10000,
            'is_active' => true,
        ]);

        $wallet = $reseller->wallet()->firstOrFail();
        app(WalletTopUpService::class)->topUp($wallet, 100000, $admin->id, 'Top up test');

        $response = $this->actingAs($reseller)->post(route('reseller.voucher-batches.store'), [
            'internet_package_id' => $package->id,
            'quantity' => 3,
        ]);

        $batch = VoucherBatch::query()->latest()->first();

        $this->assertNotNull($batch);

        $response->assertRedirect(route('reseller.voucher-batches.show', $batch));

        $this->assertDatabaseHas('voucher_batches', [
            'id' => $batch->id,
            'reseller_id' => $reseller->id,
            'package_id' => $package->id,
            'qty_requested' => 3,
            'qty_generated' => 3,
            'status' => 'generated',
            'unit_price' => '10000.00',
            'total_cost' => '30000.00',
        ]);

        $this->assertDatabaseCount('vouchers', 3);

        $wallet->refresh();
        $this->assertSame('70000.00', $wallet->balance);

        $this->assertDatabaseHas('wallet_transactions', [
            'wallet_id' => $wallet->id,
            'type' => WalletTransaction::TYPE_DEBIT,
            'source' => WalletTransaction::SOURCE_VOUCHER_PURCHASE,
            'amount' => '30000.00',
            'balance_after' => '70000.00',
        ]);
    }

    public function test_rollback_when_insufficient_balance(): void
    {
        $reseller = User::factory()->create([
            'role' => UserRole::RESELLER->value,
        ]);

        $package = InternetPackage::factory()->create([
            'price' => 50000,
            'is_active' => true,
        ]);

        $wallet = $reseller->wallet()->firstOrFail();

        $response = $this->actingAs($reseller)->from(route('reseller.voucher-batches.create'))->post(route('reseller.voucher-batches.store'), [
            'internet_package_id' => $package->id,
            'quantity' => 2,
        ]);

        $response
            ->assertRedirect(route('reseller.voucher-batches.create'))
            ->assertSessionHasErrors('quantity');

        $this->assertDatabaseCount('voucher_batches', 0);
        $this->assertDatabaseCount('vouchers', 0);

        $wallet->refresh();
        $this->assertSame('0.00', $wallet->balance);

        $this->assertDatabaseMissing('wallet_transactions', [
            'wallet_id' => $wallet->id,
            'source' => WalletTransaction::SOURCE_VOUCHER_PURCHASE,
        ]);
    }

    public function test_reseller_cannot_see_other_reseller_batch(): void
    {
        $admin = User::factory()->create([
            'role' => UserRole::ADMIN->value,
        ]);

        $resellerA = User::factory()->create([
            'role' => UserRole::RESELLER->value,
        ]);

        $resellerB = User::factory()->create([
            'role' => UserRole::RESELLER->value,
        ]);

        $package = InternetPackage::factory()->create([
            'price' => 10000,
            'is_active' => true,
        ]);

        $walletB = $resellerB->wallet()->firstOrFail();
        app(WalletTopUpService::class)->topUp($walletB, 50000, $admin->id, 'Top up reseller B');

        $this->actingAs($resellerB)->post(route('reseller.voucher-batches.store'), [
            'internet_package_id' => $package->id,
            'quantity' => 2,
        ]);

        $batch = VoucherBatch::query()->latest()->firstOrFail();

        $this->actingAs($resellerA)
            ->get(route('reseller.voucher-batches.show', $batch))
            ->assertForbidden();
    }

    public function test_mikrotik_log_created_after_voucher_generation(): void
    {
        $admin = User::factory()->create([
            'role' => UserRole::ADMIN->value,
        ]);

        $reseller = User::factory()->create([
            'role' => UserRole::RESELLER->value,
        ]);

        $package = InternetPackage::factory()->create([
            'price' => 15000,
            'is_active' => true,
        ]);

        $wallet = $reseller->wallet()->firstOrFail();
        app(WalletTopUpService::class)->topUp($wallet, 100000, $admin->id, 'Top up untuk test mikrotik log');

        $this->actingAs($reseller)->post(route('reseller.voucher-batches.store'), [
            'internet_package_id' => $package->id,
            'quantity' => 2,
        ])->assertRedirect();

        $batch = VoucherBatch::query()->latest()->firstOrFail();
        (new PushVoucherToMikrotikJob($batch->id))->handle(app(MikrotikService::class));

        $log = MikrotikLog::query()->latest()->first();

        $this->assertNotNull($log);
        $this->assertSame(MikrotikLog::ACTION_VOUCHER_BATCH_GENERATION, $log->action);
        $this->assertContains($log->status, [
            MikrotikLog::STATUS_SUCCESS,
            MikrotikLog::STATUS_FAILED,
            MikrotikLog::STATUS_SIMULATED,
        ]);
        $this->assertIsArray($log->request_payload);
        $this->assertCount(2, $log->request_payload['vouchers'] ?? []);
    }

    public function test_push_voucher_job_is_dispatched_after_batch_generation(): void
    {
        Queue::fake();

        $admin = User::factory()->create([
            'role' => UserRole::ADMIN->value,
        ]);

        $reseller = User::factory()->create([
            'role' => UserRole::RESELLER->value,
        ]);

        $package = InternetPackage::factory()->create([
            'price' => 12000,
            'is_active' => true,
        ]);

        $wallet = $reseller->wallet()->firstOrFail();
        app(WalletTopUpService::class)->topUp($wallet, 100000, $admin->id, 'Top up test dispatch job');

        $this->actingAs($reseller)->post(route('reseller.voucher-batches.store'), [
            'internet_package_id' => $package->id,
            'quantity' => 2,
        ])->assertRedirect();

        $batch = VoucherBatch::query()->latest()->firstOrFail();

        Queue::assertPushed(PushVoucherToMikrotikJob::class, function (PushVoucherToMikrotikJob $job) use ($batch): bool {
            return $job->voucherBatchId === $batch->id
                && $job->connection === 'database'
                && $job->tries === 3
                && $job->timeout === 15;
        });
    }

    public function test_voucher_generation_rate_limit_returns_429_after_five_attempts(): void
    {
        Queue::fake();

        $admin = User::factory()->create([
            'role' => UserRole::ADMIN->value,
        ]);

        $reseller = User::factory()->create([
            'role' => UserRole::RESELLER->value,
        ]);

        $package = InternetPackage::factory()->create([
            'price' => 1000,
            'is_active' => true,
        ]);

        $wallet = $reseller->wallet()->firstOrFail();
        app(WalletTopUpService::class)->topUp($wallet, 100000, $admin->id, 'Top up untuk test rate limit voucher');

        for ($attempt = 1; $attempt <= 5; $attempt++) {
            $this->actingAs($reseller)
                ->post(route('reseller.voucher-batches.store'), [
                    'internet_package_id' => $package->id,
                    'quantity' => 1,
                ])
                ->assertRedirect();
        }

        $this->actingAs($reseller)
            ->postJson(route('reseller.voucher-batches.store'), [
                'internet_package_id' => $package->id,
                'quantity' => 1,
            ])
            ->assertStatus(429)
            ->assertJson([
                'message' => 'Terlalu banyak percobaan generate voucher. Silakan coba lagi dalam 1 menit.',
            ]);
    }

    public function test_voucher_password_is_encrypted_in_database_and_decrypted_in_model(): void
    {
        Queue::fake();

        $admin = User::factory()->create([
            'role' => UserRole::ADMIN->value,
        ]);

        $reseller = User::factory()->create([
            'role' => UserRole::RESELLER->value,
        ]);

        $package = InternetPackage::factory()->create([
            'price' => 10000,
            'is_active' => true,
        ]);

        $wallet = $reseller->wallet()->firstOrFail();
        app(WalletTopUpService::class)->topUp($wallet, 100000, $admin->id, 'Top up test enkripsi password voucher');

        $this->actingAs($reseller)->post(route('reseller.voucher-batches.store'), [
            'internet_package_id' => $package->id,
            'quantity' => 1,
        ])->assertRedirect();

        $voucher = Voucher::query()->firstOrFail();
        $rawPassword = DB::table('vouchers')
            ->where('id', $voucher->id)
            ->value('password');

        $this->assertIsString($voucher->password);
        $this->assertNotSame('', $voucher->password);
        $this->assertIsString($rawPassword);
        $this->assertNotSame($voucher->password, $rawPassword);

        $voucherRefetch = Voucher::query()->findOrFail($voucher->id);
        $this->assertSame($voucher->password, $voucherRefetch->password);
    }

    public function test_changing_setting_affects_generated_voucher_format(): void
    {
        Queue::fake();

        $admin = User::factory()->create([
            'role' => UserRole::ADMIN->value,
        ]);

        $reseller = User::factory()->create([
            'role' => UserRole::RESELLER->value,
        ]);

        $package = InternetPackage::factory()->create([
            'price' => 5000,
            'is_active' => true,
        ]);

        VcrSetting::query()->create([
            'username_format' => '{CODE}',
            'password_format' => '{RANDOM}',
            'length' => 6,
            'allow_numbers' => true,
            'allow_uppercase' => false,
            'allow_lowercase' => false,
            'user_equals_password' => true,
        ]);

        $wallet = $reseller->wallet()->firstOrFail();
        app(WalletTopUpService::class)->topUp($wallet, 100000, $admin->id, 'Top up test format voucher');

        $this->actingAs($reseller)->post(route('reseller.voucher-batches.store'), [
            'internet_package_id' => $package->id,
            'quantity' => 1,
        ])->assertRedirect();

        $voucher = Voucher::query()->latest('id')->firstOrFail();

        $this->assertMatchesRegularExpression('/^\d{6}$/', $voucher->code);
        $this->assertSame($voucher->code, $voucher->username);
        $this->assertSame($voucher->username, $voucher->password);
    }
}

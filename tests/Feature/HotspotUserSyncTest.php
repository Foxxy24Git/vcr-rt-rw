<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\InternetPackage;
use App\Models\User;
use App\Models\Voucher;
use App\Models\VoucherBatch;
use App\Services\Mikrotik\Contracts\MikrotikClientInterface;
use App\Services\Mikrotik\HotspotUserSyncService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use RuntimeException;
use Tests\TestCase;

class HotspotUserSyncTest extends TestCase
{
    use RefreshDatabase;

    public function test_sync_maps_router_users_to_ready_disabled_and_expired_statuses(): void
    {
        $reseller = User::factory()->create([
            'role' => UserRole::RESELLER->value,
        ]);

        $package = InternetPackage::factory()->create();

        $batch = VoucherBatch::factory()->create([
            'reseller_id' => $reseller->id,
            'package_id' => $package->id,
        ]);

        $voucherReady = Voucher::factory()->create([
            'batch_id' => $batch->id,
            'reseller_id' => $reseller->id,
            'package_id' => $package->id,
            'code' => 'SYNCREADY01',
            'username' => 'sync-ready',
            'status' => Voucher::STATUS_ACTIVE,
            'generated_at' => now(),
        ]);

        $voucherReadySecond = Voucher::factory()->create([
            'batch_id' => $batch->id,
            'reseller_id' => $reseller->id,
            'package_id' => $package->id,
            'code' => 'SYNCRDY0002',
            'username' => 'sync-ready-2',
            'status' => Voucher::STATUS_READY,
            'generated_at' => now(),
        ]);

        $voucherDisabled = Voucher::factory()->create([
            'batch_id' => $batch->id,
            'reseller_id' => $reseller->id,
            'package_id' => $package->id,
            'code' => 'SYNCDSBL001',
            'username' => 'sync-disabled',
            'status' => Voucher::STATUS_READY,
            'generated_at' => now(),
        ]);

        $voucherExpired = Voucher::factory()->create([
            'batch_id' => $batch->id,
            'reseller_id' => $reseller->id,
            'package_id' => $package->id,
            'code' => 'SYNCEXPR001',
            'username' => 'sync-expired',
            'status' => Voucher::STATUS_READY,
            'generated_at' => now(),
        ]);

        $this->app->instance(MikrotikClientInterface::class, new class implements MikrotikClientInterface
        {
            /**
             * @param  array<string, mixed>  $payload
             * @return array<string, mixed>
             */
            public function provisionVoucherBatch(array $payload): array
            {
                throw new RuntimeException('Tidak dipakai dalam test ini.');
            }

            /**
             * @return array<int, array<string, mixed>>
             */
            public function fetchHotspotUsers(): array
            {
                return [
                    [
                        'name' => 'sync-ready',
                        'disabled' => 'false',
                        'uptime' => '0s',
                    ],
                    [
                        'name' => 'sync-ready-2',
                        'disabled' => 'false',
                        'uptime' => '4d12h32m14s',
                    ],
                    [
                        'name' => 'sync-disabled',
                        'disabled' => 'true',
                        'uptime' => '0s',
                    ],
                ];
            }

            /**
             * @return array<int, array{name: string}>
             */
            public function fetchHotspotActive(): array
            {
                return [];
            }
        });

        $synced = app(HotspotUserSyncService::class)->sync();

        $this->assertSame(4, $synced);

        $voucherReady->refresh();
        $voucherReadySecond->refresh();
        $voucherDisabled->refresh();
        $voucherExpired->refresh();

        $this->assertSame(Voucher::STATUS_READY, $voucherReady->status);
        $this->assertNull($voucherReady->uptime);
        $this->assertNotNull($voucherReady->last_sync_at);

        $this->assertSame(Voucher::STATUS_READY, $voucherReadySecond->status);
        $this->assertNull($voucherReadySecond->uptime);
        $this->assertNotNull($voucherReadySecond->last_sync_at);

        $this->assertSame(Voucher::STATUS_DISABLED, $voucherDisabled->status);
        $this->assertNull($voucherDisabled->uptime);
        $this->assertNotNull($voucherDisabled->last_sync_at);

        $this->assertSame(Voucher::STATUS_EXPIRED, $voucherExpired->status);
        $this->assertNull($voucherExpired->uptime);
        $this->assertNotNull($voucherExpired->last_sync_at);
    }

    public function test_sync_maps_active_hotspot_session_to_active_status(): void
    {
        $reseller = User::factory()->create([
            'role' => UserRole::RESELLER->value,
        ]);

        $package = InternetPackage::factory()->create();

        $batch = VoucherBatch::factory()->create([
            'reseller_id' => $reseller->id,
            'package_id' => $package->id,
        ]);

        $voucherActive = Voucher::factory()->create([
            'batch_id' => $batch->id,
            'reseller_id' => $reseller->id,
            'package_id' => $package->id,
            'code' => 'SYNCACTV01',
            'username' => 'sync-active',
            'status' => Voucher::STATUS_READY,
            'generated_at' => now(),
        ]);

        $this->app->instance(MikrotikClientInterface::class, new class implements MikrotikClientInterface
        {
            /**
             * @param  array<string, mixed>  $payload
             * @return array<string, mixed>
             */
            public function provisionVoucherBatch(array $payload): array
            {
                throw new RuntimeException('Tidak dipakai dalam test ini.');
            }

            /**
             * @return array<int, array<string, mixed>>
             */
            public function fetchHotspotUsers(): array
            {
                return [
                    [
                        'name' => 'sync-active',
                        'disabled' => 'false',
                        'uptime' => '0s',
                    ],
                ];
            }

            /**
             * @return array<int, array{name: string}>
             */
            public function fetchHotspotActive(): array
            {
                return [
                    ['name' => 'sync-active'],
                ];
            }
        });

        $synced = app(HotspotUserSyncService::class)->sync();

        $this->assertSame(1, $synced);

        $voucherActive->refresh();

        $this->assertSame(Voucher::STATUS_ACTIVE, $voucherActive->status);
        $this->assertNull($voucherActive->uptime);
        $this->assertNotNull($voucherActive->last_sync_at);
    }
}

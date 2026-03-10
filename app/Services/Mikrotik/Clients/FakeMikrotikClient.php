<?php

namespace App\Services\Mikrotik\Clients;

use App\Services\Mikrotik\Contracts\MikrotikClientInterface;
use RuntimeException;

class FakeMikrotikClient implements MikrotikClientInterface
{
    /**
     * @param  array<string, mixed>  $payload
     * @return array<string, mixed>
     */
    public function provisionVoucherBatch(array $payload): array
    {
        if (($payload['simulate_failure'] ?? false) === true) {
            throw new RuntimeException('Simulasi kegagalan MikroTik client.');
        }

        $voucherCount = is_array($payload['vouchers'] ?? null) ? count($payload['vouchers']) : 0;

        return [
            'simulated' => true,
            'success' => true,
            'processed_vouchers' => $voucherCount,
            'processed_at' => now()->toDateTimeString(),
            'message' => 'Provisioning voucher disimulasikan oleh FakeMikrotikClient.',
        ];
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function fetchHotspotUsers(): array
    {
        return [];
    }

    /**
     * @return array<int, array{name: string}>
     */
    public function fetchHotspotActive(): array
    {
        return [];
    }
}

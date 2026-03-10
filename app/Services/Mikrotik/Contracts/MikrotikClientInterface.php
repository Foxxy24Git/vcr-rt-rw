<?php

namespace App\Services\Mikrotik\Contracts;

interface MikrotikClientInterface
{
    /**
     * @param  array<string, mixed>  $payload
     * @return array<string, mixed>
     */
    public function provisionVoucherBatch(array $payload): array;

    /**
     * @return array<int, array<string, mixed>>
     */
    public function fetchHotspotUsers(): array;

    /**
     * Returns array of active hotspot usernames.
     *
     * @return array<int, array{name: string}>
     */
    public function fetchHotspotActive(): array;
}

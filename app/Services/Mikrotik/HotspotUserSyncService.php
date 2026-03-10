<?php

namespace App\Services\Mikrotik;

use App\Models\Voucher;
use App\Services\Mikrotik\Contracts\MikrotikClientInterface;

class HotspotUserSyncService
{
    public function __construct(
        private readonly MikrotikClientInterface $mikrotikClient
    ) {}

    public function sync(int $chunkSize = 200): int
    {
        $hotspotUsers = $this->indexHotspotUsers($this->mikrotikClient->fetchHotspotUsers());
        $activeNames = collect($this->mikrotikClient->fetchHotspotActive())
            ->pluck('name')
            ->filter(fn ($name): bool => is_string($name) && trim($name) !== '')
            ->map(fn (string $name): string => trim($name))
            ->values()
            ->toArray();
        $syncedCount = 0;
        $syncedAt = now();

        Voucher::query()
            ->select(['id', 'username'])
            ->whereNotNull('username')
            ->orderBy('id')
            ->chunkById($chunkSize, function ($vouchers) use ($hotspotUsers, $activeNames, $syncedAt, &$syncedCount): void {
                foreach ($vouchers as $voucher) {
                    $username = (string) $voucher->username;

                    if ($username === '') {
                        continue;
                    }

                    $statusAndUptime = $this->resolveStatusAndUptime($username, $hotspotUsers, $activeNames);

                    Voucher::query()
                        ->whereKey($voucher->id)
                        ->update([
                            'status' => $statusAndUptime['status'],
                            'uptime' => $statusAndUptime['uptime'],
                            'last_sync_at' => $syncedAt,
                        ]);

                    $syncedCount++;
                }
            });

        return $syncedCount;
    }

    /**
     * @param  array<int, array<string, mixed>>  $users
     * @return array<string, array<string, mixed>>
     */
    private function indexHotspotUsers(array $users): array
    {
        $indexed = [];

        foreach ($users as $user) {
            $name = trim((string) ($user['name'] ?? ''));

            if ($name === '') {
                continue;
            }

            $indexed[$name] = $user;
        }

        return $indexed;
    }

    /**
     * @param  array<string, array<string, mixed>>  $hotspotUsers
     * @param  array<int, string>  $activeNames
     * @return array{status: string, uptime: ?string}
     */
    private function resolveStatusAndUptime(string $username, array $hotspotUsers, array $activeNames): array
    {
        if (in_array($username, $activeNames, true)) {
            return [
                'status' => Voucher::STATUS_ACTIVE,
                'uptime' => null,
            ];
        }

        if (! isset($hotspotUsers[$username])) {
            return [
                'status' => Voucher::STATUS_EXPIRED,
                'uptime' => null,
            ];
        }

        $user = $hotspotUsers[$username];
        $isDisabled = (($user['disabled'] ?? 'false') === 'true');

        if ($isDisabled) {
            return [
                'status' => Voucher::STATUS_DISABLED,
                'uptime' => null,
            ];
        }

        return [
            'status' => Voucher::STATUS_READY,
            'uptime' => null,
        ];
    }
}

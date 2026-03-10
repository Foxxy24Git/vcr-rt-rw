<?php

namespace App\Jobs;

use App\Services\Mikrotik\HotspotUserSyncService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class SyncHotspotUsersJob implements ShouldQueue
{
    use Queueable;

    public int $tries = 1;

    public int $timeout = 120;

    public function __construct()
    {
        $this->onConnection('database');
    }

    public function handle(HotspotUserSyncService $hotspotUserSyncService): void
    {
        $hotspotUserSyncService->sync();
    }
}

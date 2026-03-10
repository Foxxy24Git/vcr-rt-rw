<?php

namespace App\Jobs;

use App\Services\Dashboard\ActiveUserSnapshotService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class CaptureActiveUsersSnapshotJob implements ShouldQueue
{
    use Queueable;

    public function __construct()
    {
        $this->onConnection('database');
    }

    public function handle(ActiveUserSnapshotService $activeUserSnapshotService): void
    {
        $activeUserSnapshotService->capture();
    }
}

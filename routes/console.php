<?php

use App\Console\Commands\BackupDatabaseCommand;
use App\Console\Commands\PruneFailedJobsCommand;
use App\Jobs\CaptureActiveUsersSnapshotJob;
use App\Jobs\SyncHotspotUsersJob;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::addCommands([
    BackupDatabaseCommand::class,
    PruneFailedJobsCommand::class,
]);

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('vouchers:sync', function (): int {
    SyncHotspotUsersJob::dispatch();

    $this->info('SyncHotspotUsersJob berhasil didispatch.');

    return self::SUCCESS;
})->purpose('Sinkronisasi status voucher dengan hotspot user di MikroTik.');

Schedule::command('vouchers:sync')->everyFiveMinutes();
Schedule::job(new CaptureActiveUsersSnapshotJob)->everyFiveMinutes();
Schedule::command('queue:prune-failed --days=7')->daily();

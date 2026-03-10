<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class PruneFailedJobsCommand extends Command
{
    protected $signature = 'queue:prune-failed
                            {--days=7 : Delete failed jobs older than this many days}';

    protected $description = 'Delete failed jobs older than the specified number of days (default: 7)';

    public function handle(): int
    {
        $days = (int) $this->option('days');

        if ($days < 1) {
            $this->error('The days option must be at least 1.');

            return self::FAILURE;
        }

        $cutoff = now()->subDays($days);
        $deleted = DB::table('failed_jobs')
            ->where('failed_at', '<', $cutoff)
            ->delete();

        $this->info("Pruned {$deleted} failed job(s) older than {$days} day(s).");

        return self::SUCCESS;
    }
}

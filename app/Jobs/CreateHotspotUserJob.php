<?php

namespace App\Jobs;

use App\Services\MikrotikService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use Throwable;

class CreateHotspotUserJob implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;

    public int $timeout = 30;

    public function __construct(
        public readonly string $username,
        public readonly string $password,
        public readonly string $profile
    ) {
        $this->onConnection('database');
    }

    public function handle(MikrotikService $mikrotikService): void
    {
        $mikrotikService->createHotspotUser(
            $this->username,
            $this->password,
            $this->profile
        );
    }

    public function failed(Throwable $exception): void
    {
        Log::error('CreateHotspotUserJob failed after all retries.', [
            'job' => self::class,
            'username' => $this->username,
            'profile' => $this->profile,
            'attempts' => $this->attempts(),
            'message' => $exception->getMessage(),
            'exception' => $exception::class,
        ]);
    }
}

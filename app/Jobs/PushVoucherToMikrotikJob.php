<?php

namespace App\Jobs;

use App\Models\MikrotikLog;
use App\Models\VoucherBatch;
use App\Services\Mikrotik\MikrotikService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Throwable;

class PushVoucherToMikrotikJob implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;

    public int $timeout = 15;

    public function __construct(
        public readonly int $voucherBatchId
    ) {
        $this->onConnection('database');
    }

    public function handle(MikrotikService $mikrotikService): void
    {
        $batch = VoucherBatch::query()
            ->with([
                'package:id,code,mikrotik_profile',
                'reseller:id,name,phone',
                'vouchers:id,batch_id,code,username,password',
            ])
            ->find($this->voucherBatchId);

        if (! $batch) {
            $this->safeCreateFailureLog('Voucher batch tidak ditemukan saat proses push ke MikroTik.');

            return;
        }

        $vouchersPayload = $batch->vouchers
            ->map(
                fn ($voucher): array => [
                    'code' => $voucher->code,
                    'username' => $voucher->username,
                    'password' => $voucher->password,
                ]
            )
            ->values()
            ->all();

        $mikrotikService->simulateVoucherBatchProvision(
            batch: $batch,
            vouchersPayload: $vouchersPayload,
            profile: $batch->package?->mikrotik_profile
        );
    }

    public function failed(Throwable $exception): void
    {
        $this->safeCreateFailureLog($exception->getMessage(), $exception);
    }

    private function safeCreateFailureLog(string $message, ?Throwable $exception = null): void
    {
        try {
            MikrotikLog::query()->create([
                'server_id' => null,
                'action' => MikrotikLog::ACTION_VOUCHER_BATCH_GENERATION,
                'request_payload' => [
                    'batch_id' => $this->voucherBatchId,
                ],
                'response_payload' => [
                    'success' => false,
                    'status' => MikrotikLog::STATUS_FAILED,
                    'message' => $message,
                    'exception' => $exception ? $exception::class : null,
                ],
                'status' => MikrotikLog::STATUS_FAILED,
                'message' => $message,
            ]);
        } catch (Throwable $logException) {
            report($logException);
        }
    }
}

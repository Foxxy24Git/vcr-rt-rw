<?php

namespace App\Services\Mikrotik;

use App\Models\MikrotikLog;
use App\Models\MikrotikServer;
use App\Models\VoucherBatch;
use App\Services\Mikrotik\Contracts\MikrotikClientInterface;
use Throwable;

class MikrotikService
{
    public function __construct(
        private readonly MikrotikClientInterface $mikrotikClient
    ) {}

    /**
     * @param  array<int, array<string, mixed>>  $vouchersPayload
     */
    public function simulateVoucherBatchProvision(
        VoucherBatch $batch,
        array $vouchersPayload,
        ?string $profile = null,
        ?MikrotikServer $server = null
    ): void {
        $requestPayload = $this->buildRequestPayload($batch, $vouchersPayload, $profile, $server);

        try {
            $responsePayload = $this->mikrotikClient->provisionVoucherBatch($requestPayload);

            if (($responsePayload['logged_by_client'] ?? false) === true) {
                return;
            }

            $status = $this->resolveStatusFromResponse($responsePayload);
            $message = $this->resolveMessageFromResponse($responsePayload);

            $this->safeCreateLog(
                serverId: $server?->id,
                requestPayload: $requestPayload,
                responsePayload: $responsePayload,
                status: $status,
                message: $message
            );
        } catch (Throwable $exception) {
            $this->safeCreateLog(
                serverId: $server?->id,
                requestPayload: $requestPayload,
                responsePayload: [
                    'exception' => $exception::class,
                    'error' => $exception->getMessage(),
                ],
                status: MikrotikLog::STATUS_FAILED,
                message: $exception->getMessage()
            );
        }
    }

    /**
     * @param  array<int, array<string, mixed>>  $vouchersPayload
     * @return array<string, mixed>
     */
    private function buildRequestPayload(
        VoucherBatch $batch,
        array $vouchersPayload,
        ?string $profile = null,
        ?MikrotikServer $server = null
    ): array {
        $resellerName = $batch->reseller?->name
            ?? $batch->reseller()->value('name');
        $resellerPhone = $batch->reseller?->phone
            ?? $batch->reseller()->value('phone');
        $packageCode = $batch->package?->code
            ?? $batch->package()->value('code');

        return [
            'server_id' => $server?->id,
            'reseller_id' => $batch->reseller_id,
            'package_id' => $batch->package_id,
            'reseller_name' => $resellerName,
            'reseller_phone' => $resellerPhone,
            'package_code' => $packageCode,
            'batch_id' => $batch->id,
            'batch_code' => $batch->batch_code,
            'profile' => $profile,
            'vouchers' => collect($vouchersPayload)
                ->map(
                    fn (array $voucher): array => [
                        'code' => $voucher['code'] ?? null,
                        'username' => $voucher['username'] ?? null,
                        'password' => $voucher['password'] ?? null,
                        'profile' => $profile,
                    ]
                )
                ->values()
                ->all(),
        ];
    }

    /**
     * @param  array<string, mixed>  $responsePayload
     */
    private function resolveStatusFromResponse(array $responsePayload): string
    {
        if (($responsePayload['simulated'] ?? false) === true) {
            return MikrotikLog::STATUS_SIMULATED;
        }

        if (($responsePayload['success'] ?? false) === true) {
            return MikrotikLog::STATUS_SUCCESS;
        }

        return MikrotikLog::STATUS_FAILED;
    }

    /**
     * @param  array<string, mixed>  $responsePayload
     */
    private function resolveMessageFromResponse(array $responsePayload): string
    {
        $message = $responsePayload['message'] ?? 'MikroTik simulation request selesai.';

        return is_string($message) ? $message : 'MikroTik simulation request selesai.';
    }

    /**
     * @param  array<string, mixed>|null  $requestPayload
     * @param  array<string, mixed>|null  $responsePayload
     */
    private function safeCreateLog(
        ?int $serverId,
        ?array $requestPayload,
        ?array $responsePayload,
        string $status,
        ?string $message
    ): void {
        try {
            MikrotikLog::query()->create([
                'server_id' => $serverId,
                'action' => MikrotikLog::ACTION_VOUCHER_BATCH_GENERATION,
                'request_payload' => $requestPayload,
                'response_payload' => $responsePayload,
                'status' => $status,
                'message' => $message,
            ]);
        } catch (Throwable $exception) {
            report($exception);
        }
    }
}

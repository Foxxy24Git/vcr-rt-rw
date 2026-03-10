<?php

namespace App\Services\Mikrotik\Clients;

use App\Models\MikrotikLog;
use App\Services\Mikrotik\Contracts\MikrotikClientInterface;
use RouterOS\Client;
use RouterOS\Config;
use RouterOS\Exceptions\BadCredentialsException;
use RouterOS\Exceptions\ClientException;
use RouterOS\Exceptions\ConfigException;
use RouterOS\Exceptions\ConnectException;
use RouterOS\Exceptions\QueryException;
use RouterOS\Query;
use RuntimeException;
use Throwable;

class RealMikrotikClient implements MikrotikClientInterface
{
    /**
     * @param  array<string, mixed>  $payload
     * @return array<string, mixed>
     */
    public function provisionVoucherBatch(array $payload): array
    {
        $responses = [];
        $status = MikrotikLog::STATUS_SUCCESS;
        $message = 'Semua voucher berhasil diprovisikan ke MikroTik.';

        try {
            $client = $this->createClientFromEnv();
        } catch (BadCredentialsException $exception) {
            return $this->buildFailureResponse(
                payload: $payload,
                responses: [],
                errorType: 'authentication_failure',
                message: $exception->getMessage(),
                exception: $exception
            );
        } catch (ConnectException|ClientException|ConfigException|QueryException $exception) {
            return $this->buildFailureResponse(
                payload: $payload,
                responses: [],
                errorType: 'connection_error',
                message: $exception->getMessage(),
                exception: $exception
            );
        } catch (Throwable $exception) {
            return $this->buildFailureResponse(
                payload: $payload,
                responses: [],
                errorType: 'unexpected_error',
                message: $exception->getMessage(),
                exception: $exception
            );
        }

        /** @var array<int, array<string, mixed>> $vouchers */
        $vouchers = is_array($payload['vouchers'] ?? null) ? $payload['vouchers'] : [];

        foreach ($vouchers as $voucher) {
            $username = (string) ($voucher['username'] ?? $voucher['code'] ?? '');
            $password = (string) ($voucher['password'] ?? '');
            $profile = (string) ($voucher['profile'] ?? $payload['profile'] ?? '');
            $comment = (string) ($voucher['comment'] ?? $payload['comment'] ?? $this->formatHotspotComment(
                resellerPhone: isset($payload['reseller_phone']) ? (string) $payload['reseller_phone'] : null,
                resellerName: isset($payload['reseller_name']) ? (string) $payload['reseller_name'] : null,
                packageCode: isset($payload['package_code']) ? (string) $payload['package_code'] : null
            ));

            try {
                $query = (new Query('/ip/hotspot/user/add'))
                    ->equal('name', $username)
                    ->equal('password', $password)
                    ->equal('profile', $profile)
                    ->equal('comment', $comment);

                $rawResponse = $client->query($query)->read();
                $commandFailureMessage = $this->extractCommandFailureMessage($rawResponse);

                if ($commandFailureMessage !== null) {
                    $status = MikrotikLog::STATUS_FAILED;
                    $message = 'Sebagian atau semua command add hotspot user gagal.';

                    $responses[] = [
                        'username' => $username,
                        'comment' => $comment,
                        'success' => false,
                        'error_type' => 'command_failure',
                        'message' => $commandFailureMessage,
                        'response' => $rawResponse,
                    ];

                    continue;
                }

                $responses[] = [
                    'username' => $username,
                    'comment' => $comment,
                    'success' => true,
                    'error_type' => null,
                    'message' => 'Hotspot user berhasil dibuat.',
                    'response' => $rawResponse,
                ];
            } catch (BadCredentialsException $exception) {
                $status = MikrotikLog::STATUS_FAILED;
                $message = 'Autentikasi MikroTik gagal.';

                $responses[] = [
                    'username' => $username,
                    'comment' => $comment,
                    'success' => false,
                    'error_type' => 'authentication_failure',
                    'message' => $exception->getMessage(),
                    'exception' => $exception::class,
                ];
            } catch (ConnectException|ClientException|ConfigException|QueryException $exception) {
                $status = MikrotikLog::STATUS_FAILED;
                $message = 'Koneksi MikroTik gagal saat menjalankan command.';

                $responses[] = [
                    'username' => $username,
                    'comment' => $comment,
                    'success' => false,
                    'error_type' => 'connection_error',
                    'message' => $exception->getMessage(),
                    'exception' => $exception::class,
                ];
            } catch (Throwable $exception) {
                $status = MikrotikLog::STATUS_FAILED;
                $message = 'Terjadi error tidak terduga saat provisioning MikroTik.';

                $responses[] = [
                    'username' => $username,
                    'comment' => $comment,
                    'success' => false,
                    'error_type' => 'unexpected_error',
                    'message' => $exception->getMessage(),
                    'exception' => $exception::class,
                ];
            }
        }

        $responsePayload = [
            'success' => $status === MikrotikLog::STATUS_SUCCESS,
            'status' => $status,
            'message' => $message,
            'processed_vouchers' => count($responses),
            'results' => $responses,
            'logged_by_client' => true,
        ];

        $this->safeCreateLog(
            serverId: isset($payload['server_id']) ? (int) $payload['server_id'] : null,
            requestPayload: $payload,
            responsePayload: $responsePayload,
            status: $status,
            message: $message
        );

        return $responsePayload;
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function fetchHotspotUsers(): array
    {
        try {
            $client = $this->createClientFromEnv();
            $rawResponse = $client->query(new Query('/ip/hotspot/user/print'))->read();
        } catch (BadCredentialsException $exception) {
            throw new RuntimeException('Autentikasi MikroTik gagal saat sinkronisasi hotspot user.', 0, $exception);
        } catch (ConnectException|ClientException|ConfigException|QueryException $exception) {
            throw new RuntimeException('Koneksi MikroTik gagal saat sinkronisasi hotspot user.', 0, $exception);
        } catch (Throwable $exception) {
            throw new RuntimeException('Terjadi error tidak terduga saat sinkronisasi hotspot user.', 0, $exception);
        }

        if (! is_array($rawResponse)) {
            return [];
        }

        return collect($rawResponse)
            ->filter(fn ($item): bool => is_array($item))
            ->map(function (array $item): array {
                return [
                    'name' => isset($item['name']) ? (string) $item['name'] : '',
                    'disabled' => $item['disabled'] ?? false,
                    'uptime' => isset($item['uptime']) ? (string) $item['uptime'] : null,
                ];
            })
            ->filter(fn (array $item): bool => $item['name'] !== '')
            ->values()
            ->all();
    }

    /**
     * @return array<int, array{name: string}>
     */
    public function fetchHotspotActive(): array
    {
        try {
            $client = $this->createClientFromEnv();
            $rawResponse = $client->query(new Query('/ip/hotspot/active/print'))->read();
        } catch (BadCredentialsException $exception) {
            throw new RuntimeException('Autentikasi MikroTik gagal saat mengambil hotspot active.', 0, $exception);
        } catch (ConnectException|ClientException|ConfigException|QueryException $exception) {
            throw new RuntimeException('Koneksi MikroTik gagal saat mengambil hotspot active.', 0, $exception);
        } catch (Throwable $exception) {
            throw new RuntimeException('Terjadi error tidak terduga saat mengambil hotspot active.', 0, $exception);
        }

        if (! is_array($rawResponse)) {
            return [];
        }

        return collect($rawResponse)
            ->filter(fn ($item): bool => is_array($item))
            ->map(function (array $item): array {
                return [
                    'name' => isset($item['user']) ? (string) $item['user'] : (isset($item['name']) ? (string) $item['name'] : ''),
                ];
            })
            ->filter(fn (array $item): bool => trim($item['name']) !== '')
            ->values()
            ->all();
    }

    /**
     * @throws BadCredentialsException
     * @throws ClientException
     * @throws ConfigException
     * @throws ConnectException
     * @throws QueryException
     */
    private function createClientFromEnv(): Client
    {
        $config = new Config([
            'host' => (string) config('mikrotik.host'),
            'port' => (int) config('mikrotik.port', 8728),
            'user' => (string) config('mikrotik.user'),
            'pass' => (string) config('mikrotik.pass'),
            'ssl' => (bool) config('mikrotik.ssl', false),
        ]);

        return new Client($config);
    }

    private function extractCommandFailureMessage(mixed $response): ?string
    {
        if (! is_array($response)) {
            return 'Response MikroTik tidak valid.';
        }

        $after = $response['after'] ?? null;

        if (is_array($after) && isset($after['message']) && is_string($after['message']) && $after['message'] !== '') {
            return $after['message'];
        }

        if (is_array($after) && isset($after['error']) && is_string($after['error']) && $after['error'] !== '') {
            return $after['error'];
        }

        if (isset($response['message']) && is_string($response['message']) && $response['message'] !== '') {
            return $response['message'];
        }

        return null;
    }

    public function formatHotspotComment(?string $resellerPhone, ?string $resellerName, ?string $packageCode): string
    {
        $phone = $this->normalizeCommentSegment($resellerPhone);
        $name = $this->normalizeCommentSegment($resellerName);
        $code = $this->normalizeCommentSegment($packageCode);

        return "vc-{$phone}-[{$name}]-[{$code}]";
    }

    private function normalizeCommentSegment(?string $value): string
    {
        $normalized = trim((string) $value);
        $normalized = str_replace(['[', ']'], '', $normalized);

        return $normalized !== '' ? $normalized : '-';
    }

    /**
     * @param  array<string, mixed>  $payload
     * @param  array<int, array<string, mixed>>  $responses
     * @return array<string, mixed>
     */
    private function buildFailureResponse(
        array $payload,
        array $responses,
        string $errorType,
        string $message,
        Throwable $exception
    ): array {
        $responsePayload = [
            'success' => false,
            'status' => MikrotikLog::STATUS_FAILED,
            'error_type' => $errorType,
            'message' => $message,
            'processed_vouchers' => count($responses),
            'results' => $responses,
            'exception' => $exception::class,
            'logged_by_client' => true,
        ];

        $this->safeCreateLog(
            serverId: isset($payload['server_id']) ? (int) $payload['server_id'] : null,
            requestPayload: $payload,
            responsePayload: $responsePayload,
            status: MikrotikLog::STATUS_FAILED,
            message: $message
        );

        return $responsePayload;
    }

    /**
     * @param  array<string, mixed>  $requestPayload
     * @param  array<string, mixed>  $responsePayload
     */
    private function safeCreateLog(
        ?int $serverId,
        array $requestPayload,
        array $responsePayload,
        string $status,
        string $message
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

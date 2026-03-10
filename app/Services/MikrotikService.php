<?php

namespace App\Services;

use App\Exceptions\MikrotikConnectionException;
use App\Models\Setting;
use Illuminate\Support\Facades\Log;
use RouterOS\Client;
use RouterOS\Config;
use RouterOS\Query;
use Throwable;

class MikrotikService
{
    private const MAX_RETRIES = 3;

    private const RETRY_DELAY_SECONDS = 1;

    /**
     * Connect to MikroTik with retries. Logs each failure and throws after all retries are exhausted.
     *
     * @throws MikrotikConnectionException
     */
    public function connectWithRetry(): Client
    {
        $lastException = null;

        for ($attempt = 1; $attempt <= self::MAX_RETRIES; $attempt++) {
            try {
                $client = $this->createClient();
                $this->verifyConnection($client);

                return $client;
            } catch (Throwable $e) {
                $lastException = $e;

                Log::error('MikroTik connection attempt failed.', [
                    'attempt' => $attempt,
                    'max_attempts' => self::MAX_RETRIES,
                    'host' => Setting::get('mikrotik_host'),
                    'port' => Setting::get('mikrotik_port', 8728),
                    'message' => $e->getMessage(),
                    'exception' => $e::class,
                ]);

                if ($attempt < self::MAX_RETRIES) {
                    sleep(self::RETRY_DELAY_SECONDS);
                }
            }
        }

        $message = sprintf(
            'MikroTik connection failed after %d attempt(s). Host: %s:%s. Last error: %s',
            self::MAX_RETRIES,
            Setting::get('mikrotik_host'),
            Setting::get('mikrotik_port', 8728),
            $lastException !== null ? $lastException->getMessage() : 'Unknown'
        );

        throw new MikrotikConnectionException($message, 0, $lastException);
    }

    /**
     * Create a hotspot user on the MikroTik router.
     *
     * @throws MikrotikConnectionException
     * @throws \RuntimeException When the router returns an error for the add command
     */
    public function createHotspotUser(string $username, string $password, string $profile): void
    {
        $client = $this->connectWithRetry();

        $query = (new Query('/ip/hotspot/user/add'))
            ->equal('name', $username)
            ->equal('password', $password)
            ->equal('profile', $profile);

        try {
            $response = $client->query($query)->read();
            $errorMessage = $this->extractCommandError($response);

            if ($errorMessage !== null) {
                Log::error('MikroTik create hotspot user failed.', [
                    'username' => $username,
                    'profile' => $profile,
                    'router_message' => $errorMessage,
                ]);

                throw new \RuntimeException(
                    "MikroTik failed to create hotspot user \"{$username}\": {$errorMessage}"
                );
            }
        } catch (MikrotikConnectionException $e) {
            throw $e;
        } catch (\RuntimeException $e) {
            throw $e;
        } catch (Throwable $e) {
            Log::error('MikroTik create hotspot user error.', [
                'username' => $username,
                'message' => $e->getMessage(),
                'exception' => $e::class,
            ]);

            throw new \RuntimeException(
                "MikroTik error while creating hotspot user \"{$username}\": " . $e->getMessage(),
                0,
                $e
            );
        }
    }

    /**
     * Get currently active hotspot users from the MikroTik router.
     *
     * @return array<int, array{user: string, uptime?: string, id?: string}>
     * @throws MikrotikConnectionException
     */
    public function getActiveUsers(): array
    {
        $client = $this->connectWithRetry();

        try {
            $rawResponse = $client->query(new Query('/ip/hotspot/active/print'))->read();
        } catch (Throwable $e) {
            Log::error('MikroTik get active users failed.', [
                'message' => $e->getMessage(),
                'exception' => $e::class,
            ]);

            throw new MikrotikConnectionException(
                'MikroTik failed to fetch active users: ' . $e->getMessage(),
                0,
                $e
            );
        }

        if (! is_array($rawResponse)) {
            return [];
        }

        return collect($rawResponse)
            ->filter(fn (mixed $item): bool => is_array($item))
            ->map(function (array $item): array {
                $user = isset($item['user']) ? (string) $item['user'] : (isset($item['name']) ? (string) $item['name'] : '');
                $result = ['user' => $user];
                if (isset($item['uptime'])) {
                    $result['uptime'] = (string) $item['uptime'];
                }
                if (isset($item['.id'])) {
                    $result['id'] = (string) $item['.id'];
                }

                return $result;
            })
            ->filter(fn (array $item): bool => trim($item['user']) !== '')
            ->values()
            ->all();
    }

    /**
     * Build RouterOS config with timeout and credentials from config.
     */
    private function createClient(): Client
    {
        $params = [
            'host' => (string) (Setting::get('mikrotik_host') ?? config('mikrotik.host', '')),
            'port' => (int) Setting::get('mikrotik_port', 8728),
            'user' => (string) config('mikrotik.user'),
            'pass' => (string) config('mikrotik.pass'),
            'ssl' => (bool) config('mikrotik.ssl', false),
        ];

        $timeout = (int) Setting::get('mikrotik_timeout', 10);
        if ($timeout > 0) {
            $params['timeout'] = $timeout;
        }

        $config = new Config($params);

        return new Client($config);
    }

    /**
     * Run a minimal query to verify the connection is alive.
     *
     * @throws Throwable
     */
    private function verifyConnection(Client $client): void
    {
        $client->query(new Query('/system/resource/get'))->read();
    }

    /**
     * Extract error message from RouterOS command response.
     */
    private function extractCommandError(mixed $response): ?string
    {
        if (! is_array($response)) {
            return 'Invalid response from router.';
        }

        $after = $response['after'] ?? null;

        if (is_array($after)) {
            if (! empty($after['message']) && is_string($after['message'])) {
                return $after['message'];
            }
            if (! empty($after['error']) && is_string($after['error'])) {
                return $after['error'];
            }
        }

        if (! empty($response['message']) && is_string($response['message'])) {
            return $response['message'];
        }

        return null;
    }
}

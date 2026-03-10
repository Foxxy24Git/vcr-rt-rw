<?php

namespace App\Services\User;

use App\Enums\UserRole;
use App\Models\User;
use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Str;

class ResellerManagementService
{
    public function __construct(
        private readonly UserRepositoryInterface $userRepository
    ) {}

    public function getAdminPaginatedResellers(?string $search = null, ?string $status = null, int $perPage = 10): LengthAwarePaginator
    {
        return $this->userRepository->paginateResellersForAdmin(
            perPage: $perPage,
            search: $search,
            status: $status
        );
    }

    public function createReseller(array $payload): User
    {
        $normalizedPayload = $this->normalizePayloadForStore($payload);

        return $this->userRepository->createReseller($normalizedPayload);
    }

    public function getResellerOrFail(int $resellerId): User
    {
        return $this->userRepository->findResellerByIdOrFail($resellerId);
    }

    public function updateReseller(int $resellerId, array $payload): User
    {
        $reseller = $this->getResellerOrFail($resellerId);

        return $this->userRepository->updateReseller($reseller, $this->normalizePayloadForUpdate($payload));
    }

    public function toggleStatus(int $resellerId): User
    {
        $reseller = $this->getResellerOrFail($resellerId);
        $nextStatus = $reseller->status === 'active' ? 'inactive' : 'active';

        return $this->userRepository->updateReseller($reseller, [
            'status' => $nextStatus,
        ]);
    }

    public function resetPassword(int $resellerId, string $password): User
    {
        $reseller = $this->getResellerOrFail($resellerId);

        return $this->userRepository->updateReseller($reseller, [
            'password' => $password,
        ]);
    }

    /**
     * @param  array<string, mixed>  $payload
     * @return array<string, mixed>
     */
    private function normalizePayloadForStore(array $payload): array
    {
        return [
            'name' => trim((string) $payload['name']),
            'email' => Str::lower(trim((string) $payload['email'])),
            'phone' => $this->normalizePhone($payload['phone'] ?? null),
            'password' => (string) $payload['password'],
            'role' => UserRole::RESELLER->value,
            'status' => 'active',
        ];
    }

    /**
     * @param  array<string, mixed>  $payload
     * @return array<string, mixed>
     */
    private function normalizePayloadForUpdate(array $payload): array
    {
        $data = [
            'name' => trim((string) $payload['name']),
            'email' => Str::lower(trim((string) $payload['email'])),
            'phone' => $this->normalizePhone($payload['phone'] ?? null),
            'status' => (string) $payload['status'],
        ];

        if (array_key_exists('discount_percent', $payload)) {
            $data['discount_percent'] = (int) ($payload['discount_percent'] ?? 0);
        }

        return $data;
    }

    private function normalizePhone(mixed $phone): ?string
    {
        if (! is_string($phone)) {
            return null;
        }

        $normalized = trim($phone);

        return $normalized === '' ? null : $normalized;
    }
}

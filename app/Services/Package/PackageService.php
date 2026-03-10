<?php

namespace App\Services\Package;

use App\Models\InternetPackage;
use App\Repositories\Contracts\PackageRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Str;

class PackageService
{
    public function __construct(
        private readonly PackageRepositoryInterface $packageRepository
    ) {}

    public function getAdminPaginated(?string $search = null, ?string $status = null, int $perPage = 10): LengthAwarePaginator
    {
        return $this->packageRepository->paginateForAdmin(
            perPage: $perPage,
            search: $search,
            status: $status
        );
    }

    public function getActiveForResellerPaginated(?string $search = null, int $perPage = 10): LengthAwarePaginator
    {
        return $this->packageRepository->paginateActiveForReseller(
            perPage: $perPage,
            search: $search
        );
    }

    public function create(array $payload): InternetPackage
    {
        return $this->packageRepository->create($this->normalizePayload($payload, setDefaultActive: true));
    }

    public function update(InternetPackage $internetPackage, array $payload): InternetPackage
    {
        return $this->packageRepository->update($internetPackage, $this->normalizePayload($payload));
    }

    public function toggleActive(InternetPackage $internetPackage): InternetPackage
    {
        return $this->packageRepository->toggleActive($internetPackage);
    }

    /**
     * Normalize package payload before persisting.
     *
     * @param  array<string, mixed>  $payload
     * @return array<string, mixed>
     */
    private function normalizePayload(array $payload, bool $setDefaultActive = false): array
    {
        $payload['code'] = Str::upper(trim((string) $payload['code']));
        $payload['name'] = trim((string) $payload['name']);
        $payload['mikrotik_profile'] = isset($payload['mikrotik_profile'])
            ? trim((string) $payload['mikrotik_profile'])
            : null;
        $payload['description'] = isset($payload['description'])
            ? trim((string) $payload['description'])
            : null;
        if (array_key_exists('is_active', $payload)) {
            $payload['is_active'] = (bool) $payload['is_active'];
        } elseif ($setDefaultActive) {
            $payload['is_active'] = true;
        }

        return $payload;
    }
}

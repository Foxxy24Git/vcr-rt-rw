<?php

namespace App\Repositories\Eloquent;

use App\Models\InternetPackage;
use App\Repositories\Contracts\PackageRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class PackageRepository implements PackageRepositoryInterface
{
    public function paginateForAdmin(int $perPage = 10, ?string $search = null, ?string $status = null): LengthAwarePaginator
    {
        return InternetPackage::query()
            ->when($search, function ($query, string $search): void {
                $query->where(function ($subQuery) use ($search): void {
                    $subQuery
                        ->where('code', 'like', "%{$search}%")
                        ->orWhere('name', 'like', "%{$search}%");
                });
            })
            ->when($status === 'active', fn ($query) => $query->where('is_active', true))
            ->when($status === 'inactive', fn ($query) => $query->where('is_active', false))
            ->orderBy('name')
            ->paginate($perPage)
            ->withQueryString();
    }

    public function paginateActiveForReseller(int $perPage = 10, ?string $search = null): LengthAwarePaginator
    {
        return InternetPackage::query()
            ->where('is_active', true)
            ->when($search, function ($query, string $search): void {
                $query->where(function ($subQuery) use ($search): void {
                    $subQuery
                        ->where('code', 'like', "%{$search}%")
                        ->orWhere('name', 'like', "%{$search}%");
                });
            })
            ->orderBy('price')
            ->paginate($perPage)
            ->withQueryString();
    }

    public function create(array $payload): InternetPackage
    {
        return InternetPackage::query()->create($payload);
    }

    public function update(InternetPackage $internetPackage, array $payload): InternetPackage
    {
        $internetPackage->update($payload);

        return $internetPackage->refresh();
    }

    public function toggleActive(InternetPackage $internetPackage): InternetPackage
    {
        $internetPackage->update([
            'is_active' => ! $internetPackage->is_active,
        ]);

        return $internetPackage->refresh();
    }
}

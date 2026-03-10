<?php

namespace App\Repositories\Eloquent;

use App\Models\VoucherBatch;
use App\Repositories\Contracts\VoucherBatchRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class VoucherBatchRepository implements VoucherBatchRepositoryInterface
{
    public function create(array $payload): VoucherBatch
    {
        return VoucherBatch::query()->create($payload);
    }

    public function findByIdOrFail(int $batchId): VoucherBatch
    {
        return VoucherBatch::query()->findOrFail($batchId);
    }

    public function existsByBatchCode(string $batchCode): bool
    {
        return VoucherBatch::query()->where('batch_code', $batchCode)->exists();
    }

    public function loadBatchDetail(VoucherBatch $batch): VoucherBatch
    {
        return $batch->load([
            'reseller:id,name,email',
            'package:id,code,name,price,validity_value,validity_unit',
        ]);
    }

    public function paginateForAdmin(int $perPage = 10, ?string $search = null, ?string $status = null): LengthAwarePaginator
    {
        return VoucherBatch::query()
            ->with([
                'reseller:id,name,email',
                'package:id,code,name',
            ])
            ->when($search, function ($query, string $search): void {
                $query->where(function ($subQuery) use ($search): void {
                    $subQuery
                        ->where('batch_code', 'like', "%{$search}%")
                        ->orWhereHas('reseller', function ($resellerQuery) use ($search): void {
                            $resellerQuery
                                ->where('name', 'like', "%{$search}%")
                                ->orWhere('email', 'like', "%{$search}%");
                        });
                });
            })
            ->when($status, fn ($query) => $query->where('status', $status))
            ->latest()
            ->paginate($perPage)
            ->withQueryString();
    }

    public function paginateForReseller(int $resellerId, int $perPage = 10, ?string $status = null): LengthAwarePaginator
    {
        return VoucherBatch::query()
            ->with([
                'package:id,code,name',
            ])
            ->where('reseller_id', $resellerId)
            ->when($status, fn ($query) => $query->where('status', $status))
            ->latest()
            ->paginate($perPage)
            ->withQueryString();
    }
}

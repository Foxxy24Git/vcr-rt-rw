<?php

namespace App\Repositories\Eloquent;

use App\Models\Voucher;
use App\Models\VoucherBatch;
use App\Repositories\Contracts\VoucherRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class VoucherRepository implements VoucherRepositoryInterface
{
    public function existsByCode(string $code): bool
    {
        return Voucher::query()->where('code', $code)->exists();
    }

    public function insertMany(array $payloads): void
    {
        Voucher::query()->insert($payloads);
    }

    public function paginateByBatch(VoucherBatch $batch, int $perPage = 50): LengthAwarePaginator
    {
        return Voucher::query()
            ->where('batch_id', $batch->id)
            ->orderBy('id')
            ->paginate($perPage)
            ->withQueryString();
    }
}

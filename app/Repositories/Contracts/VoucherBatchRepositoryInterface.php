<?php

namespace App\Repositories\Contracts;

use App\Models\VoucherBatch;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface VoucherBatchRepositoryInterface
{
    public function create(array $payload): VoucherBatch;

    public function findByIdOrFail(int $batchId): VoucherBatch;

    public function existsByBatchCode(string $batchCode): bool;

    public function loadBatchDetail(VoucherBatch $batch): VoucherBatch;

    public function paginateForAdmin(int $perPage = 10, ?string $search = null, ?string $status = null): LengthAwarePaginator;

    public function paginateForReseller(int $resellerId, int $perPage = 10, ?string $status = null): LengthAwarePaginator;
}

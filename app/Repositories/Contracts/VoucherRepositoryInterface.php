<?php

namespace App\Repositories\Contracts;

use App\Models\VoucherBatch;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface VoucherRepositoryInterface
{
    public function existsByCode(string $code): bool;

    public function insertMany(array $payloads): void;

    public function paginateByBatch(VoucherBatch $batch, int $perPage = 50): LengthAwarePaginator;
}

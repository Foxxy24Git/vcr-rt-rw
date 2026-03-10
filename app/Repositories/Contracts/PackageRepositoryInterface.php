<?php

namespace App\Repositories\Contracts;

use App\Models\InternetPackage;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface PackageRepositoryInterface
{
    public function paginateForAdmin(int $perPage = 10, ?string $search = null, ?string $status = null): LengthAwarePaginator;

    public function paginateActiveForReseller(int $perPage = 10, ?string $search = null): LengthAwarePaginator;

    public function create(array $payload): InternetPackage;

    public function update(InternetPackage $internetPackage, array $payload): InternetPackage;

    public function toggleActive(InternetPackage $internetPackage): InternetPackage;
}

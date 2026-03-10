<?php

namespace App\Repositories\Contracts;

use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface UserRepositoryInterface
{
    public function findById(int $id): ?User;

    public function findByEmail(string $email): ?User;

    public function findResellerByIdOrFail(int $id): User;

    public function paginateResellersForAdmin(int $perPage = 10, ?string $search = null, ?string $status = null): LengthAwarePaginator;

    public function createReseller(array $payload): User;

    public function updateReseller(User $reseller, array $payload): User;
}

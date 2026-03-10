<?php

namespace App\Repositories\Eloquent;

use App\Enums\UserRole;
use App\Models\User;
use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class UserRepository implements UserRepositoryInterface
{
    public function findById(int $id): ?User
    {
        return User::query()->find($id);
    }

    public function findByEmail(string $email): ?User
    {
        return User::query()->where('email', $email)->first();
    }

    public function findResellerByIdOrFail(int $id): User
    {
        return User::query()
            ->where('role', UserRole::RESELLER->value)
            ->with('wallet:id,user_id,balance,currency')
            ->findOrFail($id);
    }

    public function paginateResellersForAdmin(int $perPage = 10, ?string $search = null, ?string $status = null): LengthAwarePaginator
    {
        return User::query()
            ->where('role', UserRole::RESELLER->value)
            ->with('wallet:id,user_id,balance,currency')
            ->when($search, function ($query, string $search): void {
                $query->where(function ($subQuery) use ($search): void {
                    $subQuery
                        ->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%");
                });
            })
            ->when($status, fn ($query) => $query->where('status', $status))
            ->orderBy('name')
            ->paginate($perPage)
            ->withQueryString();
    }

    public function createReseller(array $payload): User
    {
        return User::query()->create($payload)->refresh();
    }

    public function updateReseller(User $reseller, array $payload): User
    {
        $reseller->update($payload);

        return $reseller->refresh()->loadMissing('wallet:id,user_id,balance,currency');
    }
}

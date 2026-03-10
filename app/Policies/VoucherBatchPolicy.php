<?php

namespace App\Policies;

use App\Models\User;
use App\Models\VoucherBatch;

class VoucherBatchPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin() || $user->isReseller();
    }

    public function view(User $user, VoucherBatch $voucherBatch): bool
    {
        return $user->isAdmin() || $voucherBatch->reseller_id === $user->id;
    }

    public function create(User $user): bool
    {
        return $user->isReseller();
    }
}

<?php

namespace App\Policies;

use App\Models\InternetPackage;
use App\Models\User;

class InternetPackagePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin() || $user->isReseller();
    }

    public function view(User $user, InternetPackage $internetPackage): bool
    {
        return $user->isAdmin() || ($user->isReseller() && $internetPackage->is_active);
    }

    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    public function update(User $user, InternetPackage $internetPackage): bool
    {
        return $user->isAdmin();
    }

    public function toggleActive(User $user, InternetPackage $internetPackage): bool
    {
        return $user->isAdmin();
    }
}

<?php

namespace App\Services\Auth;

use App\Models\User;

class RoleRedirectService
{
    public function dashboardRouteName(User $user): string
    {
        return $user->isAdmin()
            ? 'admin.dashboard'
            : 'reseller.dashboard';
    }
}

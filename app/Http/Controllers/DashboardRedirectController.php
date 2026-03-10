<?php

namespace App\Http\Controllers;

use App\Services\Auth\RoleRedirectService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class DashboardRedirectController extends Controller
{
    public function __invoke(Request $request, RoleRedirectService $roleRedirectService): RedirectResponse
    {
        $user = $request->user();

        if (! $user) {
            return redirect()->route('login');
        }

        return redirect()->route($roleRedirectService->dashboardRouteName($user));
    }
}

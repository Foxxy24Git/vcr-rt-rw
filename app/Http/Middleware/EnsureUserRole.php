<?php

namespace App\Http\Middleware;

use App\Enums\UserRole;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserRole
{
    /**
     * Ensure the authenticated user has one of the allowed roles.
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if (! $user || empty($roles)) {
            abort(403);
        }

        $allowedRoles = collect($roles)
            ->map(static fn (string $role) => UserRole::tryFrom($role)?->value)
            ->filter()
            ->values()
            ->all();

        if (empty($allowedRoles)) {
            abort(403);
        }

        $currentRole = $user->role instanceof UserRole
            ? $user->role->value
            : (string) $user->role;

        if (! in_array($currentRole, $allowedRoles, true)) {
            abort(403);
        }

        return $next($request);
    }
}

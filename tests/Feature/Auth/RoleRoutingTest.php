<?php

namespace Tests\Feature\Auth;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoleRoutingTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_user_is_redirected_to_admin_dashboard_after_login(): void
    {
        $admin = User::factory()->create([
            'role' => UserRole::ADMIN->value,
        ]);

        $response = $this->post('/login', [
            'email' => $admin->email,
            'password' => 'password',
        ]);

        $response->assertRedirect(route('admin.dashboard', absolute: false));
    }

    public function test_reseller_cannot_access_admin_dashboard(): void
    {
        $reseller = User::factory()->create([
            'role' => UserRole::RESELLER->value,
        ]);

        $response = $this->actingAs($reseller)->get('/admin/dashboard');

        $response->assertForbidden();
    }

    public function test_admin_cannot_access_reseller_dashboard(): void
    {
        $admin = User::factory()->create([
            'role' => UserRole::ADMIN->value,
        ]);

        $response = $this->actingAs($admin)->get('/reseller/dashboard');

        $response->assertForbidden();
    }
}

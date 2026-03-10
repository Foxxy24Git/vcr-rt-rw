<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminResellerManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_reseller(): void
    {
        $admin = User::factory()->create([
            'role' => UserRole::ADMIN->value,
            'status' => 'active',
        ]);

        $response = $this->actingAs($admin)->post(route('admin.resellers.store'), [
            'name' => 'Reseller Satu',
            'email' => 'reseller1@example.com',
            'phone' => '08123456789',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => UserRole::ADMIN->value,
            'status' => 'inactive',
        ]);

        $response
            ->assertRedirect(route('admin.resellers.index'))
            ->assertSessionHas('status');

        $this->assertDatabaseHas('users', [
            'email' => 'reseller1@example.com',
            'name' => 'Reseller Satu',
            'phone' => '08123456789',
            'role' => UserRole::RESELLER->value,
            'status' => 'active',
        ]);

        $reseller = User::query()->where('email', 'reseller1@example.com')->firstOrFail();

        $this->assertDatabaseHas('wallets', [
            'user_id' => $reseller->id,
        ]);
    }

    public function test_reseller_cannot_access_admin_reseller_routes(): void
    {
        $reseller = User::factory()->create([
            'role' => UserRole::RESELLER->value,
            'status' => 'active',
        ]);

        $this->actingAs($reseller)
            ->get(route('admin.resellers.index'))
            ->assertForbidden();

        $this->actingAs($reseller)
            ->post(route('admin.resellers.store'), [
                'name' => 'Hacker',
                'email' => 'hack@example.com',
                'password' => 'password123',
                'password_confirmation' => 'password123',
            ])
            ->assertForbidden();
    }

    public function test_admin_cannot_edit_other_admin_account_from_reseller_module(): void
    {
        $admin = User::factory()->create([
            'role' => UserRole::ADMIN->value,
            'status' => 'active',
        ]);

        $anotherAdmin = User::factory()->create([
            'role' => UserRole::ADMIN->value,
            'status' => 'active',
        ]);

        $this->actingAs($admin)
            ->get(route('admin.resellers.edit', $anotherAdmin->id))
            ->assertNotFound();
    }
}

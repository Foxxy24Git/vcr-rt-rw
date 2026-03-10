<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\InternetPackage;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InternetPackageAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_access_admin_package_index(): void
    {
        $admin = User::factory()->create([
            'role' => UserRole::ADMIN->value,
        ]);

        $response = $this->actingAs($admin)->get(route('admin.packages.index'));

        $response->assertOk();
    }

    public function test_reseller_cannot_access_admin_package_pages(): void
    {
        $reseller = User::factory()->create([
            'role' => UserRole::RESELLER->value,
        ]);

        $package = InternetPackage::factory()->create();

        $this->actingAs($reseller)
            ->get(route('admin.packages.index'))
            ->assertForbidden();

        $this->actingAs($reseller)
            ->get(route('admin.packages.create'))
            ->assertForbidden();

        $this->actingAs($reseller)
            ->get(route('admin.packages.edit', $package))
            ->assertForbidden();
    }

    public function test_reseller_only_sees_active_packages(): void
    {
        $reseller = User::factory()->create([
            'role' => UserRole::RESELLER->value,
        ]);

        $activePackage = InternetPackage::factory()->create([
            'name' => 'Paket Aktif',
            'is_active' => true,
        ]);

        $inactivePackage = InternetPackage::factory()->create([
            'name' => 'Paket Nonaktif',
            'is_active' => false,
        ]);

        $response = $this->actingAs($reseller)->get(route('reseller.packages.index'));

        $response->assertOk();
        $response->assertSee($activePackage->name);
        $response->assertDontSee($inactivePackage->name);
    }
}

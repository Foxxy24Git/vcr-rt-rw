<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Seed the default admin user.
     */
    public function run(): void
    {
        $email = env('DEFAULT_ADMIN_EMAIL', 'admin@vcr.local');

        User::query()->updateOrCreate(
            ['email' => $email],
            [
                'name' => env('DEFAULT_ADMIN_NAME', 'Super Admin'),
                'password' => Hash::make(env('DEFAULT_ADMIN_PASSWORD', 'password')),
                'email_verified_at' => now(),
                'role' => UserRole::ADMIN->value,
                'status' => 'active',
            ]
        );
    }
}

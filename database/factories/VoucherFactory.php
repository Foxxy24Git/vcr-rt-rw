<?php

namespace Database\Factories;

use App\Models\InternetPackage;
use App\Models\User;
use App\Models\Voucher;
use App\Models\VoucherBatch;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Voucher>
 */
class VoucherFactory extends Factory
{
    protected $model = Voucher::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'batch_id' => VoucherBatch::factory(),
            'reseller_id' => User::factory()->state([
                'role' => 'reseller',
                'status' => 'active',
            ]),
            'package_id' => InternetPackage::factory(),
            'code' => Str::upper(Str::random(10)),
            'username' => Str::lower(Str::random(8)),
            'password' => Str::upper(Str::random(8)),
            'status' => 'ready',
            'cost_price' => fake()->randomFloat(2, 10000, 100000),
            'sold_price' => null,
            'generated_at' => now(),
            'sold_at' => null,
            'used_at' => null,
            'expires_at' => null,
        ];
    }
}

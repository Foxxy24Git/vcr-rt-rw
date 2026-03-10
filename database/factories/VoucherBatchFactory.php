<?php

namespace Database\Factories;

use App\Models\InternetPackage;
use App\Models\User;
use App\Models\VoucherBatch;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\VoucherBatch>
 */
class VoucherBatchFactory extends Factory
{
    protected $model = VoucherBatch::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $quantity = fake()->numberBetween(1, 10);
        $unitPrice = fake()->randomFloat(2, 10000, 100000);

        return [
            'reseller_id' => User::factory()->state([
                'role' => 'reseller',
                'status' => 'active',
            ]),
            'package_id' => InternetPackage::factory(),
            'batch_code' => 'BTCH-'.Str::upper(Str::random(10)),
            'qty_requested' => $quantity,
            'qty_generated' => $quantity,
            'unit_price' => $unitPrice,
            'total_cost' => $unitPrice * $quantity,
            'status' => 'generated',
            'paid_at' => now(),
            'generated_at' => now(),
        ];
    }
}

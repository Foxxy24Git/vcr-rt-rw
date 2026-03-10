<?php

namespace Database\Factories;

use App\Models\InternetPackage;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\InternetPackage>
 */
class InternetPackageFactory extends Factory
{
    protected $model = InternetPackage::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'code' => strtoupper(fake()->unique()->bothify('PKG###')),
            'name' => 'Paket '.fake()->randomElement(['Harian', 'Mingguan', 'Bulanan']),
            'description' => fake()->sentence(),
            'price' => fake()->randomFloat(2, 10000, 500000),
            'validity_value' => fake()->randomElement([1, 7, 30]),
            'validity_unit' => fake()->randomElement(['day', 'month']),
            'bandwidth_up_kbps' => fake()->randomElement([512, 1024, 2048]),
            'bandwidth_down_kbps' => fake()->randomElement([1024, 2048, 4096]),
            'quota_mb' => fake()->randomElement([1024, 2048, 5120, 10240]),
            'mikrotik_profile' => fake()->randomElement(['HS-1H', 'HS-1D', 'HS-1M']),
            'is_active' => true,
        ];
    }
}

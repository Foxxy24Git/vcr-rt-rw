<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingsSeeder extends Seeder
{
    /**
     * Default system settings.
     *
     * @var array<string, string|int>
     */
    private const DEFAULTS = [
        'mikrotik_host' => '192.168.88.1',
        'mikrotik_port' => '8728',
        'mikrotik_timeout' => '10',
        'voucher_prefix' => 'VCR',
        'voucher_price' => '2000',
        'hotspot_name' => 'RT-RW HOTSPOT',
    ];

    /**
     * Seed the settings table dengan nilai default.
     */
    public function run(): void
    {
        foreach (self::DEFAULTS as $key => $value) {
            Setting::set($key, (string) $value);
        }
    }
}

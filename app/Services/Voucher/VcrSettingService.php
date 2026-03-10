<?php

namespace App\Services\Voucher;

use App\Models\VcrSetting;

class VcrSettingService
{
    public function getActiveSetting(): VcrSetting
    {
        $setting = VcrSetting::query()->orderBy('id')->first();

        if ($setting) {
            return $setting;
        }

        return VcrSetting::query()->create($this->defaultAttributes());
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    public function saveActiveSetting(array $attributes): VcrSetting
    {
        $setting = VcrSetting::query()->orderBy('id')->first();

        if ($setting) {
            $setting->fill($attributes);
            $setting->save();

            return $setting->refresh();
        }

        return VcrSetting::query()->create(array_merge(
            $this->defaultAttributes(),
            $attributes
        ));
    }

    /**
     * @return array<string, mixed>
     */
    public function defaultAttributes(): array
    {
        return [
            'username_format' => '{CODE}',
            'password_format' => '{RANDOM}',
            'length' => 10,
            'allow_numbers' => true,
            'allow_uppercase' => true,
            'allow_lowercase' => false,
            'user_equals_password' => false,
        ];
    }
}

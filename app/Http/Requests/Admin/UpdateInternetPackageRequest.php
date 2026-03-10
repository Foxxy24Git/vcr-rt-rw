<?php

namespace App\Http\Requests\Admin;

use App\Models\InternetPackage;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateInternetPackageRequest extends FormRequest
{
    protected function prepareForValidation(): void
    {
        if ($this->has('code')) {
            $this->merge([
                'code' => strtoupper((string) $this->input('code')),
            ]);
        }
    }

    public function authorize(): bool
    {
        $internetPackage = $this->route('internetPackage');

        return $internetPackage instanceof InternetPackage
            ? $this->user()?->can('update', $internetPackage) ?? false
            : false;
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        /** @var \App\Models\InternetPackage $internetPackage */
        $internetPackage = $this->route('internetPackage');

        return [
            'code' => [
                'required',
                'string',
                'max:50',
                'alpha_dash',
                Rule::unique('internet_packages', 'code')->ignore($internetPackage->id),
            ],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'price' => ['required', 'numeric', 'min:0'],
            'validity_value' => ['required', 'integer', 'min:1'],
            'validity_unit' => ['required', Rule::in(['hour', 'day', 'month'])],
            'bandwidth_up_kbps' => ['nullable', 'integer', 'min:1'],
            'bandwidth_down_kbps' => ['nullable', 'integer', 'min:1'],
            'quota_mb' => ['nullable', 'integer', 'min:1'],
            'mikrotik_profile' => ['nullable', 'string', 'max:100'],
        ];
    }
}

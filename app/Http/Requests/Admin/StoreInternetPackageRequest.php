<?php

namespace App\Http\Requests\Admin;

use App\Models\InternetPackage;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreInternetPackageRequest extends FormRequest
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
        return $this->user()?->can('create', InternetPackage::class) ?? false;
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'code' => ['required', 'string', 'max:50', 'alpha_dash', Rule::unique('internet_packages', 'code')],
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

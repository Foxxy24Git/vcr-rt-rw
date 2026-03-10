<?php

namespace App\Http\Requests\Reseller;

use App\Models\VoucherBatch;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreVoucherBatchRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('create', VoucherBatch::class) ?? false;
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'internet_package_id' => [
                'required',
                'integer',
                Rule::exists('internet_packages', 'id')->where(fn ($query) => $query->where('is_active', true)),
            ],
            'quantity' => ['required', 'integer', 'min:1', 'max:500'],
        ];
    }
}

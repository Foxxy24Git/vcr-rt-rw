<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateResellerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() ?? false;
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        $resellerId = (int) $this->route('reseller');

        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore($resellerId)],
            'phone' => ['nullable', 'string', 'max:30'],
            'status' => ['required', Rule::in(['active', 'inactive'])],
            'discount_percent' => ['nullable', 'integer', 'min:0', 'max:100'],
        ];
    }
}

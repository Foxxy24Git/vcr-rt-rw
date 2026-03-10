<?php

namespace App\Http\Requests\Admin;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class UpdateVcrSettingRequest extends FormRequest
{
    protected function prepareForValidation(): void
    {
        $this->merge([
            'allow_numbers' => $this->boolean('allow_numbers'),
            'allow_uppercase' => $this->boolean('allow_uppercase'),
            'allow_lowercase' => $this->boolean('allow_lowercase'),
            'user_equals_password' => $this->boolean('user_equals_password'),
        ]);
    }

    public function authorize(): bool
    {
        return $this->user()?->isAdmin() ?? false;
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'length' => ['required', 'integer', 'min:4', 'max:32'],
            'allow_numbers' => ['required', 'boolean'],
            'allow_uppercase' => ['required', 'boolean'],
            'allow_lowercase' => ['required', 'boolean'],
            'user_equals_password' => ['required', 'boolean'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            $allowNumbers = $this->boolean('allow_numbers');
            $allowUppercase = $this->boolean('allow_uppercase');
            $allowLowercase = $this->boolean('allow_lowercase');

            if (! $allowNumbers && ! $allowUppercase && ! $allowLowercase) {
                $validator->errors()->add('allow_numbers', 'Minimal satu jenis karakter harus diaktifkan.');
            }
        });
    }
}

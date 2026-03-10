<?php

namespace App\Http\Requests\Admin;

use App\Models\Wallet;
use Illuminate\Foundation\Http\FormRequest;

class WalletTopUpRequest extends FormRequest
{
    public function authorize(): bool
    {
        $wallet = $this->route('wallet');

        return $wallet instanceof Wallet
            ? $this->user()?->can('topUp', $wallet) ?? false
            : false;
    }

    /**
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'amount' => ['required', 'numeric', 'gt:0'],
            'description' => ['nullable', 'string', 'max:255'],
        ];
    }
}

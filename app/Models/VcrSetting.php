<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VcrSetting extends Model
{
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'username_format',
        'password_format',
        'length',
        'allow_numbers',
        'allow_uppercase',
        'allow_lowercase',
        'user_equals_password',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'length' => 'integer',
            'allow_numbers' => 'boolean',
            'allow_uppercase' => 'boolean',
            'allow_lowercase' => 'boolean',
            'user_equals_password' => 'boolean',
        ];
    }
}

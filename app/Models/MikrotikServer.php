<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MikrotikServer extends Model
{
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'host',
        'port',
        'username',
        'password',
        'is_active',
        'notes',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'password' => 'encrypted',
            'is_active' => 'boolean',
        ];
    }

    public function logs(): HasMany
    {
        return $this->hasMany(MikrotikLog::class, 'server_id');
    }
}

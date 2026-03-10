<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Voucher extends Model
{
    use HasFactory;

    public const STATUS_READY = 'READY';

    public const STATUS_USED = 'USED';

    public const STATUS_ACTIVE = 'ACTIVE';

    public const STATUS_EXPIRED = 'EXPIRED';

    public const STATUS_DISABLED = 'DISABLED';

    /**
     * @var list<string>
     */
    protected $fillable = [
        'batch_id',
        'reseller_id',
        'package_id',
        'code',
        'username',
        'password',
        'status',
        'uptime',
        'last_sync_at',
        'cost_price',
        'sold_price',
        'generated_at',
        'sold_at',
        'used_at',
        'expires_at',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'password' => 'encrypted',
            'cost_price' => 'decimal:2',
            'sold_price' => 'decimal:2',
            'generated_at' => 'datetime',
            'sold_at' => 'datetime',
            'used_at' => 'datetime',
            'expires_at' => 'datetime',
            'last_sync_at' => 'datetime',
        ];
    }

    public function batch(): BelongsTo
    {
        return $this->belongsTo(VoucherBatch::class, 'batch_id');
    }

    public function reseller(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reseller_id');
    }

    public function package(): BelongsTo
    {
        return $this->belongsTo(InternetPackage::class, 'package_id');
    }
}

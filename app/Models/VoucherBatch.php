<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class VoucherBatch extends Model
{
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'reseller_id',
        'package_id',
        'batch_code',
        'qty_requested',
        'qty_generated',
        'unit_price',
        'total_cost',
        'status',
        'paid_at',
        'generated_at',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'unit_price' => 'decimal:2',
            'total_cost' => 'decimal:2',
            'paid_at' => 'datetime',
            'generated_at' => 'datetime',
        ];
    }

    public function reseller(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reseller_id');
    }

    public function package(): BelongsTo
    {
        return $this->belongsTo(InternetPackage::class, 'package_id');
    }

    public function vouchers(): HasMany
    {
        return $this->hasMany(Voucher::class, 'batch_id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WalletTransaction extends Model
{
    use HasFactory;

    public const TYPE_CREDIT = 'credit';

    public const TYPE_DEBIT = 'debit';

    public const SOURCE_TOPUP = 'topup';

    public const SOURCE_VOUCHER_PURCHASE = 'voucher_purchase';

    public const SOURCE_MANUAL_ADJUSTMENT = 'manual_adjustment';

    public const SOURCE_REFUND = 'refund';

    /**
     * @var list<string>
     */
    protected $fillable = [
        'wallet_id',
        'type',
        'source',
        'amount',
        'balance_before',
        'balance_after',
        'reference_type',
        'reference_id',
        'description',
        'created_by',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'balance_before' => 'decimal:2',
            'balance_after' => 'decimal:2',
        ];
    }

    public function wallet(): BelongsTo
    {
        return $this->belongsTo(Wallet::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}

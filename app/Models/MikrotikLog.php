<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MikrotikLog extends Model
{
    use HasFactory;

    public const STATUS_SUCCESS = 'success';

    public const STATUS_FAILED = 'failed';

    public const STATUS_SIMULATED = 'simulated';

    public const ACTION_VOUCHER_BATCH_GENERATION = 'voucher_batch_generation';

    /**
     * @var list<string>
     */
    protected $fillable = [
        'server_id',
        'action',
        'request_payload',
        'response_payload',
        'status',
        'message',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'request_payload' => 'array',
            'response_payload' => 'array',
        ];
    }

    public function server(): BelongsTo
    {
        return $this->belongsTo(MikrotikServer::class, 'server_id');
    }
}

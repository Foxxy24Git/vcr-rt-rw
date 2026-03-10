<?php

namespace App\Services\Audit;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class AuditLogService
{
    public function logAction(
        User $actor,
        string $action,
        Model $model,
        ?array $oldValues = null,
        ?array $newValues = null,
        ?string $ipAddress = null
    ): AuditLog {
        return AuditLog::query()->create([
            'actor_id' => $actor->id,
            'action' => $action,
            'model_type' => $model::class,
            'model_id' => (int) $model->getKey(),
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'ip_address' => $ipAddress ?? request()?->ip(),
        ]);
    }
}

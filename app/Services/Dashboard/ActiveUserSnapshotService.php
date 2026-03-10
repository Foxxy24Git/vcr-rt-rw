<?php

namespace App\Services\Dashboard;

use App\Models\ActiveUserSnapshot;
use App\Models\Voucher;

class ActiveUserSnapshotService
{
    public function capture(): ActiveUserSnapshot
    {
        $totalActive = Voucher::query()
            ->where('status', Voucher::STATUS_ACTIVE)
            ->count();

        return ActiveUserSnapshot::query()->create([
            'total_active' => $totalActive,
            'created_at' => now(),
        ]);
    }
}

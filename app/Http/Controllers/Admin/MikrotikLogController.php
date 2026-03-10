<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MikrotikLog;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class MikrotikLogController extends Controller
{
    public function index(Request $request): View
    {
        $validated = $request->validate([
            'status' => ['nullable', Rule::in([MikrotikLog::STATUS_SUCCESS, MikrotikLog::STATUS_FAILED])],
            'date_from' => ['nullable', 'date'],
            'date_to' => ['nullable', 'date', 'after_or_equal:date_from'],
        ]);

        $status = $validated['status'] ?? null;
        $dateFrom = $validated['date_from'] ?? null;
        $dateTo = $validated['date_to'] ?? null;

        $logs = MikrotikLog::query()
            ->select(['id', 'status', 'message', 'created_at'])
            ->selectRaw("CAST(JSON_UNQUOTE(JSON_EXTRACT(request_payload, '$.batch_id')) AS UNSIGNED) AS voucher_batch_id")
            ->when(
                $status,
                fn ($query) => $query->where('status', $status)
            )
            ->when(
                $dateFrom,
                fn ($query) => $query->whereDate('created_at', '>=', $dateFrom)
            )
            ->when(
                $dateTo,
                fn ($query) => $query->whereDate('created_at', '<=', $dateTo)
            )
            ->orderByDesc('created_at')
            ->paginate(15)
            ->withQueryString();

        return view('admin.monitoring.mikrotik-logs.index', [
            'logs' => $logs,
            'status' => $status,
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
        ]);
    }
}

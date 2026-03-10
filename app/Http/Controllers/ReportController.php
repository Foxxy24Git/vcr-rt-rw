<?php

namespace App\Http\Controllers;

use App\Enums\UserRole;
use App\Exports\VoucherReportExport;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ReportController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();
        $resellers = collect();

        if ($user !== null && $user->isAdmin()) {
            $resellers = User::query()
                ->where('role', UserRole::RESELLER->value)
                ->orderBy('name')
                ->get(['id', 'name']);
        }

        return view('reports.vouchers.index', [
            'resellers' => $resellers,
        ]);
    }

    public function exportVoucherReport(Request $request): BinaryFileResponse
    {
        $validated = $request->validate([
            'from_date' => ['required', 'date'],
            'to_date' => ['required', 'date', 'after_or_equal:from_date'],
            'reseller_id' => ['nullable', 'integer', 'exists:users,id'],
        ]);

        $user = $request->user();
        $resellerId = $validated['reseller_id'] ?? null;

        if ($user !== null && $user->isReseller()) {
            $resellerId = $user->id;
        }

        $filename = 'voucher-report-'.$validated['from_date'].'_'.$validated['to_date'].'.xlsx';

        return Excel::download(
            new VoucherReportExport(
                fromDate: (string) $validated['from_date'],
                toDate: (string) $validated['to_date'],
                resellerId: $resellerId !== null ? (int) $resellerId : null
            ),
            $filename
        );
    }
}

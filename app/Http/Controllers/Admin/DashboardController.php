<?php

namespace App\Http\Controllers\Admin;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\ActiveUserSnapshot;
use App\Models\AuditLog;
use App\Models\InternetPackage;
use App\Models\MikrotikLog;
use App\Models\User;
use App\Models\Voucher;
use App\Models\VoucherBatch;
use App\Models\Wallet;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $today = now()->toDateString();

        $voucherStats = Voucher::query()
            ->selectRaw('COUNT(*) as total_vouchers')
            ->selectRaw(
                'SUM(CASE WHEN DATE(generated_at) = ? THEN 1 ELSE 0 END) as vouchers_today',
                [$today]
            )
            ->selectRaw(
                'COALESCE(SUM(CASE WHEN DATE(generated_at) = ? THEN cost_price ELSE 0 END), 0) as revenue_today',
                [$today]
            )
            ->first();

        $totalReseller = User::query()
            ->where('role', UserRole::RESELLER->value)
            ->count();

        $totalWalletBalance = Wallet::query()
            ->join('users', 'wallets.user_id', '=', 'users.id')
            ->where('users.role', UserRole::RESELLER->value)
            ->sum('wallets.balance');

        $activePackages = InternetPackage::query()
            ->where('is_active', true)
            ->count();

        $activeUsersCount = Voucher::query()
            ->where('status', Voucher::STATUS_ACTIVE)
            ->count();

        $activePerReseller = Voucher::query()
            ->where('status', Voucher::STATUS_ACTIVE)
            ->select('reseller_id', DB::raw('COUNT(*) as total_active'))
            ->groupBy('reseller_id')
            ->with('reseller:id,name')
            ->orderByDesc('total_active')
            ->get();

        $threshold = (int) config('app.reseller_active_threshold', 20);

        $overloadedResellers = $activePerReseller
            ->filter(fn ($reseller) => (int) $reseller->total_active > $threshold)
            ->values();

        $failedMikrotikJobs = MikrotikLog::query()
            ->where('status', MikrotikLog::STATUS_FAILED)
            ->count();

        $auditLogsToday = AuditLog::query()
            ->whereDate('created_at', $today)
            ->count();

        $activeUsersTrendRows = ActiveUserSnapshot::query()
            ->where('created_at', '>=', now()->subDay())
            ->orderBy('created_at')
            ->get(['created_at', 'total_active']);

        $activeUsersTrendJson = json_encode([
            'labels' => $activeUsersTrendRows
                ->map(fn (ActiveUserSnapshot $snapshot): string => $snapshot->created_at?->format('H:i') ?? '-')
                ->all(),
            'values' => $activeUsersTrendRows
                ->map(fn (ActiveUserSnapshot $snapshot): int => (int) $snapshot->total_active)
                ->all(),
        ]) ?: '{"labels":[],"values":[]}';

        $topResellersToday = VoucherBatch::query()
            ->whereDate('created_at', $today)
            ->whereRaw('UPPER(status) = ?', ['GENERATED'])
            ->select('reseller_id', DB::raw('SUM(total_cost) as total_revenue'))
            ->groupBy('reseller_id')
            ->with('reseller:id,name')
            ->orderByDesc('total_revenue')
            ->take(5)
            ->get();

        $activeVouchers = Voucher::query()
            ->where('status', Voucher::STATUS_ACTIVE)
            ->with('package:id,bandwidth_down_kbps')
            ->get();

        $estimatedKbps = $activeVouchers->sum(fn (Voucher $voucher): int => (int) ($voucher->package->bandwidth_down_kbps ?? 0));
        $estimatedMbps = round($estimatedKbps / 1000, 2);
        $capacity = (float) config('app.network_total_capacity_mbps', 75);
        $utilizationPercent = $capacity > 0
            ? round(($estimatedMbps / $capacity) * 100, 1)
            : 0.0;

        return view('admin.dashboard', [
            'dashboardTitle' => 'Dashboard Admin ROOT.NET',
            'activeUsersCount' => $activeUsersCount,
            'activePerReseller' => $activePerReseller,
            'overloadedResellers' => $overloadedResellers,
            'resellerActiveThreshold' => $threshold,
            'activeUsersTrendJson' => $activeUsersTrendJson,
            'topResellersToday' => $topResellersToday,
            'estimatedMbps' => $estimatedMbps,
            'networkCapacityMbps' => $capacity,
            'utilizationPercent' => $utilizationPercent,
            'kpis' => [
                [
                    'label' => 'Total Reseller',
                    'value' => number_format($totalReseller),
                    'accent' => 'bg-rootPrimary',
                ],
                [
                    'label' => 'Total Wallet Balance',
                    'value' => 'Rp '.number_format((float) $totalWalletBalance, 2, ',', '.'),
                    'accent' => 'bg-rootTeal',
                ],
                [
                    'label' => 'Total Voucher Generated',
                    'value' => number_format((int) ($voucherStats?->total_vouchers ?? 0)),
                    'accent' => 'bg-rootPrimary',
                ],
                [
                    'label' => 'Active Packages',
                    'value' => number_format($activePackages),
                    'accent' => 'bg-rootTeal',
                ],
                [
                    'label' => 'Voucher Generated Today',
                    'value' => number_format((int) ($voucherStats?->vouchers_today ?? 0)),
                    'accent' => 'bg-rootPrimary',
                ],
                [
                    'label' => 'Revenue Today',
                    'value' => 'Rp '.number_format((float) ($voucherStats?->revenue_today ?? 0), 2, ',', '.'),
                    'accent' => 'bg-rootTeal',
                ],
                [
                    'label' => 'Failed MikroTik Jobs',
                    'value' => number_format($failedMikrotikJobs),
                    'accent' => 'bg-rootPrimary',
                ],
                [
                    'label' => 'Audit Logs Today',
                    'value' => number_format($auditLogsToday),
                    'accent' => 'bg-rootTeal',
                ],
            ],
        ]);
    }
}

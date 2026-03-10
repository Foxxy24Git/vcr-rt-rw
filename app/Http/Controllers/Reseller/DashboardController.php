<?php

namespace App\Http\Controllers\Reseller;

use App\Http\Controllers\Controller;
use App\Models\InternetPackage;
use App\Models\Voucher;
use App\Models\Wallet;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $reseller = auth()->user();

        $packages = InternetPackage::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $wallet = Wallet::query()->where('user_id', $reseller->id)->first();

        $todayVoucherCount = Voucher::query()
            ->where('reseller_id', $reseller->id)
            ->whereDate('created_at', Carbon::today())
            ->count();

        $recentVouchers = Voucher::query()
            ->where('reseller_id', $reseller->id)
            ->with('package')
            ->latest()
            ->limit(5)
            ->get();

        return view('reseller.dashboard', [
            'dashboardTitle' => 'Dashboard Reseller',
            'roleLabel' => 'Reseller',
            'packages' => $packages,
            'wallet' => $wallet,
            'todayVoucherCount' => $todayVoucherCount,
            'recentVouchers' => $recentVouchers,
        ]);
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\VoucherBatch;
use App\Services\Voucher\VoucherGenerationService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class VoucherBatchController extends Controller
{
    public function __construct(
        private readonly VoucherGenerationService $voucherGenerationService
    ) {}

    public function index(Request $request): View
    {
        $this->authorize('viewAny', VoucherBatch::class);

        $search = $request->string('search')->toString();
        $status = $request->string('status')->toString();

        $batches = $this->voucherGenerationService->paginateForAdmin(
            search: $search ?: null,
            status: $status ?: null
        );

        return view('admin.vouchers.batches.index', [
            'batches' => $batches,
            'search' => $search,
            'status' => $status,
        ]);
    }

    public function show(VoucherBatch $voucherBatch): View
    {
        $this->authorize('view', $voucherBatch);

        $voucherBatch = $this->voucherGenerationService->loadBatchDetail($voucherBatch);
        $vouchers = $this->voucherGenerationService->paginateBatchVouchers($voucherBatch);

        return view('admin.vouchers.batches.show', [
            'voucherBatch' => $voucherBatch,
            'vouchers' => $vouchers,
        ]);
    }
}

<?php

namespace App\Http\Controllers\Reseller;

use App\Exceptions\InsufficientBalanceException;
use App\Exceptions\ResellerOverloadException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Reseller\StoreVoucherBatchRequest;
use App\Models\InternetPackage;
use App\Models\VoucherBatch;
use App\Services\Voucher\VoucherGenerationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\MessageBag;
use Illuminate\View\View;

class VoucherBatchController extends Controller
{
    public function __construct(
        private readonly VoucherGenerationService $voucherGenerationService
    ) {}

    public function index(Request $request): View
    {
        $this->authorize('viewAny', VoucherBatch::class);

        $status = $request->string('status')->toString();

        $batches = $this->voucherGenerationService->paginateForReseller(
            reseller: $request->user(),
            status: $status ?: null
        );

        return view('reseller.vouchers.batches.index', [
            'batches' => $batches,
            'status' => $status,
        ]);
    }

    public function create(): View
    {
        $this->authorize('create', VoucherBatch::class);

        $packages = $this->voucherGenerationService->getActivePackagesForGeneration();

        return view('reseller.vouchers.batches.create', [
            'packages' => $packages,
        ]);
    }

    public function store(StoreVoucherBatchRequest $request): RedirectResponse
    {
        /** @var \App\Models\InternetPackage $package */
        $package = InternetPackage::query()->findOrFail((int) $request->validated('internet_package_id'));
        $quantity = (int) $request->validated('quantity');

        try {
            $batch = $this->voucherGenerationService->generateBatch(
                reseller: $request->user(),
                package: $package,
                quantity: $quantity
            );
        } catch (InsufficientBalanceException $exception) {
            $errors = new MessageBag([
                'quantity' => 'Saldo wallet Anda tidak mencukupi untuk generate voucher.',
            ]);

            return back()->withErrors($errors)->withInput();
        } catch (ResellerOverloadException $exception) {
            $errors = new MessageBag([
                'quantity' => $exception->getMessage(),
            ]);

            return back()->withErrors($errors)->withInput();
        }

        return redirect()
            ->route('reseller.voucher-batches.show', $batch)
            ->with('status', 'Voucher batch berhasil digenerate.');
    }

    public function show(VoucherBatch $voucherBatch): View
    {
        $this->authorize('view', $voucherBatch);

        $voucherBatch = $this->voucherGenerationService->loadBatchDetail($voucherBatch);
        $vouchers = $this->voucherGenerationService->paginateBatchVouchers($voucherBatch);

        return view('reseller.vouchers.batches.show', [
            'voucherBatch' => $voucherBatch,
            'vouchers' => $vouchers,
        ]);
    }

    public function printView(VoucherBatch $batch, Request $request): View
    {
        $this->authorize('view', $batch);

        if ((int) $batch->reseller_id !== (int) $request->user()->id) {
            abort(403);
        }

        $voucherBatch = $this->voucherGenerationService->loadBatchDetail($batch);
        $vouchers = $batch->vouchers()
            ->select(['id', 'batch_id', 'code', 'username', 'password', 'generated_at'])
            ->orderBy('id')
            ->get();

        return view('reseller.vouchers.batches.print', [
            'voucherBatch' => $voucherBatch,
            'vouchers' => $vouchers,
        ]);
    }

    public function thermalView(VoucherBatch $batch, Request $request): View
    {
        $this->authorize('view', $batch);

        if ((int) $batch->reseller_id !== (int) $request->user()->id) {
            abort(403);
        }

        $voucherBatch = $this->voucherGenerationService->loadBatchDetail($batch);
        $vouchers = $batch->vouchers()
            ->select(['id', 'batch_id', 'code', 'username', 'password', 'generated_at'])
            ->orderBy('id')
            ->get();

        return view('reseller.vouchers.batches.thermal', [
            'voucherBatch' => $voucherBatch,
            'vouchers' => $vouchers,
        ]);
    }

    public function cardView(VoucherBatch $batch, Request $request): View
    {
        $this->authorize('view', $batch);

        if ((int) $batch->reseller_id !== (int) $request->user()->id) {
            abort(403);
        }

        $voucherBatch = $this->voucherGenerationService->loadBatchDetail($batch);
        $vouchers = $batch->vouchers()
            ->select(['id', 'batch_id', 'username', 'password'])
            ->orderBy('id')
            ->get();

        return view('reseller.vouchers.batches.card', [
            'voucherBatch' => $voucherBatch,
            'vouchers' => $vouchers,
        ]);
    }
}

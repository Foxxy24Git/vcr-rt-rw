<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateVcrSettingRequest;
use App\Services\Voucher\VcrSettingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class VcrSettingController extends Controller
{
    public function __construct(
        private readonly VcrSettingService $vcrSettingService
    ) {}

    public function edit(): View
    {
        return view('admin.vcr-settings.edit', [
            'setting' => $this->vcrSettingService->getActiveSetting(),
        ]);
    }

    public function update(UpdateVcrSettingRequest $request): RedirectResponse
    {
        $this->vcrSettingService->saveActiveSetting($request->validated());

        return redirect()
            ->route('admin.vcr-settings.edit')
            ->with('status', 'Pengaturan VCR berhasil diperbarui.');
    }
}

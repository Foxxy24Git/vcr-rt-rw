<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreInternetPackageRequest;
use App\Http\Requests\Admin\UpdateInternetPackageRequest;
use App\Models\InternetPackage;
use App\Services\Audit\AuditLogService;
use App\Services\Package\PackageService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class InternetPackageController extends Controller
{
    public function __construct(
        private readonly PackageService $packageService,
        private readonly AuditLogService $auditLogService
    ) {}

    public function index(Request $request): View
    {
        $this->authorize('viewAny', InternetPackage::class);

        $search = $request->string('search')->toString();
        $status = $request->string('status')->toString();

        $packages = $this->packageService->getAdminPaginated(
            search: $search ?: null,
            status: $status ?: null,
        );

        return view('admin.packages.index', [
            'packages' => $packages,
            'search' => $search,
            'status' => $status,
        ]);
    }

    public function create(): View
    {
        $this->authorize('create', InternetPackage::class);

        return view('admin.packages.create');
    }

    public function store(StoreInternetPackageRequest $request): RedirectResponse
    {
        $package = $this->packageService->create($request->validated());
        $this->auditLogService->logAction(
            actor: $request->user(),
            action: 'package.created',
            model: $package,
            oldValues: null,
            newValues: $this->packageSnapshot($package),
            ipAddress: $request->ip()
        );

        return redirect()
            ->route('admin.packages.index')
            ->with('status', 'Paket internet berhasil dibuat.');
    }

    public function edit(InternetPackage $internetPackage): View
    {
        $this->authorize('update', $internetPackage);

        return view('admin.packages.edit', [
            'internetPackage' => $internetPackage,
        ]);
    }

    public function update(UpdateInternetPackageRequest $request, InternetPackage $internetPackage): RedirectResponse
    {
        $oldValues = $this->packageSnapshot($internetPackage);
        $package = $this->packageService->update($internetPackage, $request->validated());
        $this->auditLogService->logAction(
            actor: $request->user(),
            action: 'package.updated',
            model: $package,
            oldValues: $oldValues,
            newValues: $this->packageSnapshot($package),
            ipAddress: $request->ip()
        );

        return redirect()
            ->route('admin.packages.index')
            ->with('status', 'Paket internet berhasil diperbarui.');
    }

    public function toggleActive(Request $request, InternetPackage $internetPackage): RedirectResponse
    {
        $this->authorize('toggleActive', $internetPackage);

        $oldValues = [
            'is_active' => $internetPackage->is_active,
        ];
        $internetPackage = $this->packageService->toggleActive($internetPackage);
        $this->auditLogService->logAction(
            actor: $request->user(),
            action: 'package.active_toggled',
            model: $internetPackage,
            oldValues: $oldValues,
            newValues: [
                'is_active' => $internetPackage->is_active,
            ],
            ipAddress: $request->ip()
        );

        $message = $internetPackage->is_active
            ? 'Paket berhasil diaktifkan.'
            : 'Paket berhasil dinonaktifkan.';

        return redirect()
            ->route('admin.packages.index')
            ->with('status', $message);
    }

    /**
     * @return array<string, mixed>
     */
    private function packageSnapshot(InternetPackage $internetPackage): array
    {
        return [
            'code' => $internetPackage->code,
            'name' => $internetPackage->name,
            'description' => $internetPackage->description,
            'price' => (string) $internetPackage->price,
            'validity_value' => $internetPackage->validity_value,
            'validity_unit' => $internetPackage->validity_unit,
            'bandwidth_up_kbps' => $internetPackage->bandwidth_up_kbps,
            'bandwidth_down_kbps' => $internetPackage->bandwidth_down_kbps,
            'quota_mb' => $internetPackage->quota_mb,
            'mikrotik_profile' => $internetPackage->mikrotik_profile,
            'is_active' => $internetPackage->is_active,
        ];
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ResetResellerPasswordRequest;
use App\Http\Requests\Admin\StoreResellerRequest;
use App\Http\Requests\Admin\UpdateResellerRequest;
use App\Models\User;
use App\Services\Audit\AuditLogService;
use App\Services\User\ResellerManagementService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ResellerController extends Controller
{
    public function __construct(
        private readonly ResellerManagementService $resellerManagementService,
        private readonly AuditLogService $auditLogService
    ) {}

    public function index(Request $request): View
    {
        $search = $request->string('search')->toString();
        $status = $request->string('status')->toString();

        $resellers = $this->resellerManagementService->getAdminPaginatedResellers(
            search: $search ?: null,
            status: $status ?: null
        );

        return view('admin.resellers.index', [
            'resellers' => $resellers,
            'search' => $search,
            'status' => $status,
        ]);
    }

    public function create(): View
    {
        return view('admin.resellers.create');
    }

    public function store(StoreResellerRequest $request): RedirectResponse
    {
        $reseller = $this->resellerManagementService->createReseller($request->validated());
        $this->auditLogService->logAction(
            actor: $request->user(),
            action: 'reseller.created',
            model: $reseller,
            oldValues: null,
            newValues: $this->resellerSnapshot($reseller),
            ipAddress: $request->ip()
        );

        return redirect()
            ->route('admin.resellers.index')
            ->with('status', 'Reseller berhasil dibuat.');
    }

    public function edit(int $reseller): View
    {
        $reseller = $this->resellerManagementService->getResellerOrFail($reseller);

        return view('admin.resellers.edit', [
            'reseller' => $reseller,
        ]);
    }

    public function update(UpdateResellerRequest $request, int $reseller): RedirectResponse
    {
        $before = $this->resellerManagementService->getResellerOrFail($reseller);
        $oldValues = $this->resellerSnapshot($before);
        $updated = $this->resellerManagementService->updateReseller(
            resellerId: $reseller,
            payload: $request->validated()
        );
        $this->auditLogService->logAction(
            actor: $request->user(),
            action: 'reseller.updated',
            model: $updated,
            oldValues: $oldValues,
            newValues: $this->resellerSnapshot($updated),
            ipAddress: $request->ip()
        );

        return redirect()
            ->route('admin.resellers.index')
            ->with('status', 'Profil reseller berhasil diperbarui.');
    }

    public function toggleStatus(Request $request, int $reseller): RedirectResponse
    {
        $before = $this->resellerManagementService->getResellerOrFail($reseller);
        $reseller = $this->resellerManagementService->toggleStatus($reseller);
        $this->auditLogService->logAction(
            actor: $request->user(),
            action: 'reseller.status_toggled',
            model: $reseller,
            oldValues: [
                'status' => $before->status,
            ],
            newValues: [
                'status' => $reseller->status,
            ],
            ipAddress: $request->ip()
        );

        $message = $reseller->status === 'active'
            ? 'Reseller berhasil diaktifkan.'
            : 'Reseller berhasil dinonaktifkan.';

        return redirect()
            ->route('admin.resellers.index')
            ->with('status', $message);
    }

    public function resetPassword(ResetResellerPasswordRequest $request, int $reseller): RedirectResponse
    {
        $reseller = $this->resellerManagementService->resetPassword(
            resellerId: $reseller,
            password: (string) $request->validated('password')
        );
        $this->auditLogService->logAction(
            actor: $request->user(),
            action: 'reseller.password_reset',
            model: $reseller,
            oldValues: null,
            newValues: [
                'password_reset' => true,
            ],
            ipAddress: $request->ip()
        );

        return redirect()
            ->route('admin.resellers.edit', $reseller)
            ->with('status', 'Password reseller berhasil direset.');
    }

    /**
     * @return array<string, mixed>
     */
    private function resellerSnapshot(User $reseller): array
    {
        return [
            'name' => $reseller->name,
            'email' => $reseller->email,
            'phone' => $reseller->phone,
            'status' => $reseller->status,
            'role' => $reseller->role instanceof \App\Enums\UserRole
                ? $reseller->role->value
                : (string) $reseller->role,
        ];
    }
}

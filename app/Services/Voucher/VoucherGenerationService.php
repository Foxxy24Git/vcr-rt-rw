<?php

namespace App\Services\Voucher;

use App\Exceptions\ResellerOverloadException;
use App\Jobs\PushVoucherToMikrotikJob;
use App\Models\InternetPackage;
use App\Models\User;
use App\Models\VcrSetting;
use App\Models\Voucher;
use App\Models\VoucherBatch;
use App\Models\WalletTransaction;
use App\Repositories\Contracts\VoucherBatchRepositoryInterface;
use App\Repositories\Contracts\VoucherRepositoryInterface;
use App\Services\Wallet\WalletDebitService;
use App\Services\Wallet\WalletService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use InvalidArgumentException;

class VoucherGenerationService
{
    public function __construct(
        private readonly VoucherBatchRepositoryInterface $voucherBatchRepository,
        private readonly VoucherRepositoryInterface $voucherRepository,
        private readonly WalletService $walletService,
        private readonly WalletDebitService $walletDebitService,
        private readonly VoucherPricingService $voucherPricingService,
        private readonly VcrSettingService $vcrSettingService
    ) {}

    public function paginateForAdmin(?string $search = null, ?string $status = null, int $perPage = 10): LengthAwarePaginator
    {
        return $this->voucherBatchRepository->paginateForAdmin(
            perPage: $perPage,
            search: $search,
            status: $status
        );
    }

    public function paginateForReseller(User $reseller, ?string $status = null, int $perPage = 10): LengthAwarePaginator
    {
        if (! $reseller->isReseller()) {
            throw new InvalidArgumentException('Hanya reseller yang memiliki batch voucher.');
        }

        return $this->voucherBatchRepository->paginateForReseller(
            resellerId: $reseller->id,
            perPage: $perPage,
            status: $status
        );
    }

    public function loadBatchDetail(VoucherBatch $batch): VoucherBatch
    {
        return $this->voucherBatchRepository->loadBatchDetail($batch);
    }

    public function paginateBatchVouchers(VoucherBatch $batch, int $perPage = 50): LengthAwarePaginator
    {
        return $this->voucherRepository->paginateByBatch($batch, $perPage);
    }

    public function generateBatch(User $reseller, InternetPackage $package, int $quantity): VoucherBatch
    {
        if (! $reseller->isReseller()) {
            throw new InvalidArgumentException('Hanya reseller yang dapat generate voucher.');
        }

        if (! $package->is_active) {
            throw new InvalidArgumentException('Paket internet tidak aktif.');
        }

        if ($quantity <= 0) {
            throw new InvalidArgumentException('Jumlah voucher harus lebih dari nol.');
        }

        return DB::transaction(function () use ($reseller, $package, $quantity): VoucherBatch {
            $this->ensureResellerNotOverloaded($reseller);

            $unitPrice = $this->voucherPricingService->calculateUnitPrice($package, $reseller);
            $totalCost = $this->voucherPricingService->calculateTotalCost($package, $quantity, $reseller);
            $batchCode = $this->generateUniqueBatchCode();
            $generatedAt = now();

            $wallet = $this->walletService->getWalletByUser($reseller);

            // Debit wallet reseller sebelum batch dibuat.
            $this->walletDebitService->debitWithinTransaction(
                wallet: $wallet,
                amount: $totalCost,
                actorUserId: $reseller->id,
                source: WalletTransaction::SOURCE_VOUCHER_PURCHASE,
                description: "Pembelian voucher batch {$batchCode}"
            );

            $batch = $this->voucherBatchRepository->create([
                'reseller_id' => $reseller->id,
                'package_id' => $package->id,
                'batch_code' => $batchCode,
                'qty_requested' => $quantity,
                'qty_generated' => $quantity,
                'unit_price' => $unitPrice,
                'total_cost' => $totalCost,
                'status' => 'generated',
                'paid_at' => $generatedAt,
                'generated_at' => $generatedAt,
            ]);

            $vouchersPayload = $this->buildVouchersPayload(
                batch: $batch,
                package: $package,
                quantity: $quantity,
                unitPrice: $unitPrice,
                generatedAt: $generatedAt
            );

            $this->voucherRepository->insertMany($vouchersPayload);
            DB::afterCommit(fn () => PushVoucherToMikrotikJob::dispatch($batch->id));

            return $this->voucherBatchRepository->loadBatchDetail($batch);
        });
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function buildVouchersPayload(
        VoucherBatch $batch,
        InternetPackage $package,
        int $quantity,
        string $unitPrice,
        \Illuminate\Support\Carbon $generatedAt
    ): array {
        $payloads = [];
        $usedCodes = [];
        $setting = $this->vcrSettingService->getActiveSetting();

        for ($i = 0; $i < $quantity; $i++) {
            $code = $this->generateUniqueVoucherCode($usedCodes, $setting);
            $usedCodes[$code] = true;
            $username = $this->generateUsername($code, $setting);
            $plainPassword = $setting->user_equals_password
                ? $username
                : $this->generatePassword($code, $username, $setting);

            $payloads[] = [
                'batch_id' => $batch->id,
                'reseller_id' => $batch->reseller_id,
                'package_id' => $package->id,
                'code' => $code,
                'username' => $username,
                'password' => Crypt::encryptString($plainPassword),
                'status' => 'ready',
                'cost_price' => $unitPrice,
                'sold_price' => null,
                'generated_at' => $generatedAt,
                'sold_at' => null,
                'used_at' => null,
                'expires_at' => null,
                'created_at' => $generatedAt,
                'updated_at' => $generatedAt,
            ];
        }

        return $payloads;
    }

    private function generateUniqueBatchCode(int $maxRetry = 25): string
    {
        for ($attempt = 0; $attempt < $maxRetry; $attempt++) {
            $candidate = 'BTCH-'.Str::upper(Str::random(10));

            if (! $this->voucherBatchRepository->existsByBatchCode($candidate)) {
                return $candidate;
            }
        }

        throw new InvalidArgumentException('Gagal membuat batch code unik.');
    }

    /**
     * @param  array<string, bool>  $usedCodes
     */
    private function generateUniqueVoucherCode(array $usedCodes, VcrSetting $setting, int $maxRetry = 50): string
    {
        for ($attempt = 0; $attempt < $maxRetry; $attempt++) {
            $candidate = $this->generateRandomBySetting($setting);

            if (isset($usedCodes[$candidate])) {
                continue;
            }

            if (! $this->voucherRepository->existsByCode($candidate)) {
                return $candidate;
            }
        }

        throw new InvalidArgumentException('Gagal membuat voucher code unik.');
    }

    private function generateUsername(string $code, VcrSetting $setting): string
    {
        $username = $this->applyFormatTemplate(
            format: $setting->username_format,
            code: $code,
            username: $code,
            setting: $setting
        );

        return $username !== '' ? $username : $code;
    }

    private function generatePassword(string $code, string $username, VcrSetting $setting): string
    {
        $password = $this->applyFormatTemplate(
            format: $setting->password_format,
            code: $code,
            username: $username,
            setting: $setting
        );

        return $password !== '' ? $password : $this->generateRandomBySetting($setting);
    }

    private function applyFormatTemplate(string $format, string $code, string $username, VcrSetting $setting): string
    {
        $template = trim($format);

        if ($template === '') {
            return '';
        }

        return str_replace(
            ['{CODE}', '{USERNAME}', '{RANDOM}'],
            [$code, $username, $this->generateRandomBySetting($setting)],
            $template
        );
    }

    private function generateRandomBySetting(VcrSetting $setting): string
    {
        $length = max(4, (int) $setting->length);
        $pool = $this->resolveCharacterPool($setting);
        $maxIndex = strlen($pool) - 1;
        $random = '';

        for ($i = 0; $i < $length; $i++) {
            $random .= $pool[random_int(0, $maxIndex)];
        }

        return $random;
    }

    private function resolveCharacterPool(VcrSetting $setting): string
    {
        $pool = '';

        if ($setting->allow_numbers) {
            $pool .= '0123456789';
        }

        if ($setting->allow_uppercase) {
            $pool .= 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        }

        if ($setting->allow_lowercase) {
            $pool .= 'abcdefghijklmnopqrstuvwxyz';
        }

        if ($pool === '') {
            return '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        }

        return $pool;
    }

    /**
     * @return Collection<int, InternetPackage>
     */
    public function getActivePackagesForGeneration(): Collection
    {
        return InternetPackage::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get();
    }

    private function ensureResellerNotOverloaded(User $reseller): void
    {
        if (! $reseller->isReseller()) {
            return;
        }

        $threshold = (int) config('app.reseller_active_threshold', 20);
        $mode = (string) config('app.reseller_overload_mode', 'soft');
        $authenticatedResellerId = auth()->id();
        $resellerId = $authenticatedResellerId !== null ? (int) $authenticatedResellerId : $reseller->id;

        $activeCount = Voucher::query()
            ->where('reseller_id', $resellerId)
            ->where('status', Voucher::STATUS_ACTIVE)
            ->count();

        if ($mode === 'hard' && $activeCount > $threshold) {
            throw new ResellerOverloadException("Active user limit exceeded ({$activeCount}/{$threshold}).");
        }
    }
}

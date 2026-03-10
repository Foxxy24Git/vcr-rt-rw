<?php

namespace App\Services\Voucher;

use App\Models\InternetPackage;
use App\Models\User;
use InvalidArgumentException;

class VoucherPricingService
{
    public function calculateUnitPrice(InternetPackage $package, ?User $reseller = null): string
    {
        $packagePrice = (float) $package->price;
        $discount = $reseller ? (int) ($reseller->discount_percent ?? 0) : 0;
        $discount = max(0, min(100, $discount));
        $finalPrice = $packagePrice - ($packagePrice * $discount / 100);

        return number_format($finalPrice, 2, '.', '');
    }

    public function calculateTotalCost(InternetPackage $package, int $quantity, ?User $reseller = null): string
    {
        if ($quantity <= 0) {
            throw new InvalidArgumentException('Quantity harus lebih dari nol.');
        }

        $unitPrice = $this->calculateUnitPrice($package, $reseller);
        $unitPriceCents = $this->toCents($unitPrice);
        $totalCostCents = $unitPriceCents * $quantity;

        return $this->fromCents($totalCostCents);
    }

    private function toCents(float|string $amount): int
    {
        return (int) round(((float) $amount) * 100);
    }

    private function fromCents(int $amount): string
    {
        return number_format($amount / 100, 2, '.', '');
    }
}

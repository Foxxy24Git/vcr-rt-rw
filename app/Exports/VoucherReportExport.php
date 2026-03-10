<?php

namespace App\Exports;

use App\Models\Voucher;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class VoucherReportExport implements FromCollection, WithHeadings
{
    public function __construct(
        private readonly string $fromDate,
        private readonly string $toDate,
        private readonly ?int $resellerId = null
    ) {}

    public function collection(): Collection
    {
        $from = Carbon::parse($this->fromDate)->startOfDay();
        $to = Carbon::parse($this->toDate)->endOfDay();

        $query = Voucher::query()
            ->with([
                'reseller:id,name',
                'package:id,name',
                'batch:id,batch_code',
            ])
            ->whereBetween('created_at', [$from, $to])
            ->orderBy('created_at');

        if ($this->resellerId !== null) {
            $query->where('reseller_id', $this->resellerId);
        }

        return $query->get()->map(function (Voucher $voucher): array {
            return [
                'date' => $voucher->created_at?->format('Y-m-d') ?? '-',
                'time' => $voucher->created_at?->format('H:i:s') ?? '-',
                'batch_code' => $voucher->batch?->batch_code ?? '-',
                'voucher_code' => $voucher->code,
                'username' => $voucher->username ?? '-',
                'package_name' => $voucher->package?->name ?? '-',
                'cost_price' => (float) $voucher->cost_price,
                'status' => strtoupper((string) $voucher->status),
            ];
        });
    }

    /**
     * @return list<string>
     */
    public function headings(): array
    {
        return [
            'Date',
            'Time',
            'Batch Code',
            'Voucher Code',
            'Username',
            'Package Name',
            'Cost Price',
            'Status',
        ];
    }
}

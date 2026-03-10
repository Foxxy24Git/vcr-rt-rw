<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="robots" content="noindex">
        <title>Print Voucher Batch {{ $voucherBatch->batch_code }}</title>
        @vite(['resources/css/app.css'])
        <style>
            @media print {
                nav,
                .no-print {
                    display: none !important;
                }

                *,
                *::before,
                *::after {
                    box-shadow: none !important;
                    background: transparent !important;
                }

                body {
                    background: #fff !important;
                    color: #111827 !important;
                }

                .print-container {
                    max-width: 100% !important;
                    margin: 0 !important;
                    padding: 0 !important;
                }

                .print-table th,
                .print-table td {
                    padding-top: 0.35rem !important;
                    padding-bottom: 0.35rem !important;
                }
            }
        </style>
    </head>
    <body class="bg-white text-gray-900">
        <div class="print-container mx-auto max-w-6xl px-4 py-6 sm:px-6 lg:px-8">
            <div class="no-print mb-4 flex items-center justify-end gap-2">
                <a href="{{ route('reseller.voucher-batches.show', $voucherBatch) }}" class="rounded-xl border border-rootPrimary px-3 py-2 text-xs font-medium text-rootPrimary hover:bg-rootPink/20">
                    Kembali
                </a>
                <button type="button" onclick="window.print()" class="rounded-xl bg-rootPrimary px-3 py-2 text-xs font-medium text-white hover:bg-rootIndigo">
                    Print
                </button>
            </div>

            <section class="print-card relative overflow-hidden rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
                <span class="absolute inset-y-0 left-0 w-1 bg-rootPrimary"></span>
                <div class="flex flex-wrap items-start justify-between gap-3">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wider text-rootPrimary">ROOT.NET</p>
                        <h1 class="mt-1 text-lg font-semibold text-gray-900">Voucher Batch {{ $voucherBatch->batch_code }}</h1>
                        <p class="mt-1 text-xs text-gray-500">Generated {{ $voucherBatch->generated_at?->format('d-m-Y H:i') ?? '-' }}</p>
                    </div>
                    <div class="text-right">
                        <p class="text-xs text-gray-500">Paket</p>
                        <p class="text-sm font-semibold text-gray-900">{{ $voucherBatch->package->name }}</p>
                        <p class="text-xs text-gray-500">
                            {{ $voucherBatch->package->validity_value }} {{ $voucherBatch->package->validity_unit }}
                        </p>
                    </div>
                </div>
            </section>

            <section class="print-table-wrapper mt-4 overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
                <div class="overflow-x-auto">
                    <table class="print-table min-w-full divide-y divide-gray-200 text-xs">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-3 py-2 text-left font-semibold text-gray-700">Voucher Code</th>
                                <th class="px-3 py-2 text-left font-semibold text-gray-700">Username</th>
                                <th class="px-3 py-2 text-left font-semibold text-gray-700">Password</th>
                                <th class="px-3 py-2 text-left font-semibold text-gray-700">Package</th>
                                <th class="px-3 py-2 text-left font-semibold text-gray-700">Validity</th>
                                <th class="px-3 py-2 text-left font-semibold text-gray-700">Generated At</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 bg-white">
                            @forelse ($vouchers as $voucher)
                                <tr>
                                    <td class="px-3 py-2 font-mono text-gray-900">{{ $voucher->code }}</td>
                                    <td class="px-3 py-2 font-mono text-gray-900">{{ $voucher->username ?? '-' }}</td>
                                    <td class="px-3 py-2 font-mono text-gray-900">{{ $voucher->password ?? '-' }}</td>
                                    <td class="px-3 py-2 text-gray-700">{{ $voucherBatch->package->name }}</td>
                                    <td class="px-3 py-2 text-gray-700">{{ $voucherBatch->package->validity_value }} {{ $voucherBatch->package->validity_unit }}</td>
                                    <td class="px-3 py-2 text-gray-700">{{ $voucher->generated_at?->format('d-m-Y H:i') ?? '-' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-3 py-6 text-center text-gray-500">
                                        Tidak ada voucher untuk dicetak.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>
        </div>
    </body>
</html>

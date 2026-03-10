<x-dashboard-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            Detail Voucher Batch: {{ $voucherBatch->batch_code }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-7xl space-y-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                <div class="rounded-lg bg-white p-4 shadow-sm">
                    <p class="text-sm text-gray-500">Reseller</p>
                    <p class="font-medium text-gray-900">{{ $voucherBatch->reseller->name }}</p>
                    <p class="text-xs text-gray-500">{{ $voucherBatch->reseller->email }}</p>
                </div>

                <div class="rounded-lg bg-white p-4 shadow-sm">
                    <p class="text-sm text-gray-500">Paket</p>
                    <p class="font-medium text-gray-900">{{ $voucherBatch->package->name }}</p>
                    <p class="text-xs text-gray-500">{{ $voucherBatch->package->code }}</p>
                </div>

                <div class="rounded-lg bg-white p-4 shadow-sm">
                    <p class="text-sm text-gray-500">Status</p>
                    <x-status-badge :status="$voucherBatch->status" class="mt-1" />
                    <p class="text-xs text-gray-500">{{ $voucherBatch->generated_at?->format('d-m-Y H:i') }}</p>
                </div>
            </div>

            <div class="rounded-lg bg-white p-4 shadow-sm">
                <dl class="grid grid-cols-1 gap-3 text-sm md:grid-cols-4">
                    <div>
                        <dt class="text-gray-500">Qty</dt>
                        <dd class="font-medium text-gray-900">{{ $voucherBatch->qty_generated }}/{{ $voucherBatch->qty_requested }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500">Unit Price</dt>
                        <dd class="font-medium text-gray-900">Rp {{ number_format((float) $voucherBatch->unit_price, 2, ',', '.') }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500">Total Cost</dt>
                        <dd class="font-medium text-gray-900">Rp {{ number_format((float) $voucherBatch->total_cost, 2, ',', '.') }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500">Paid At</dt>
                        <dd class="font-medium text-gray-900">{{ $voucherBatch->paid_at?->format('d-m-Y H:i') ?? '-' }}</dd>
                    </div>
                </dl>
            </div>

            <div class="overflow-hidden rounded-xl bg-white shadow-sm">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left font-semibold text-gray-700">Code</th>
                                <th class="px-4 py-3 text-left font-semibold text-gray-700">Username</th>
                                <th class="px-4 py-3 text-left font-semibold text-gray-700">Password</th>
                                <th class="px-4 py-3 text-left font-semibold text-gray-700">Status</th>
                                <th class="px-4 py-3 text-left font-semibold text-gray-700">Cost Price</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 bg-white">
                            @forelse ($vouchers as $voucher)
                                <tr>
                                    <td class="px-4 py-3 font-mono text-xs text-gray-900">{{ $voucher->code }}</td>
                                    <td class="px-4 py-3 text-gray-700">{{ $voucher->username ?? '-' }}</td>
                                    <td class="px-4 py-3 text-gray-700">{{ $voucher->password ?? '-' }}</td>
                                    <td class="px-4 py-3">
                                        <x-status-badge :status="$voucher->status" />
                                    </td>
                                    <td class="px-4 py-3 text-gray-700">Rp {{ number_format((float) $voucher->cost_price, 2, ',', '.') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-4 py-8 text-center text-gray-500">Tidak ada voucher pada batch ini.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="border-t border-gray-100 px-4 py-3">
                    {{ $vouchers->links() }}
                </div>
            </div>
        </div>
    </div>
</x-dashboard-layout>

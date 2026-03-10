<x-dashboard-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Voucher Batch Saya
            </h2>

            <a href="{{ route('reseller.voucher-batches.create') }}" class="rounded-xl bg-rootPrimary px-4 py-2 text-sm font-medium text-white hover:bg-rootIndigo">
                + Generate Batch
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-7xl space-y-4 sm:px-6 lg:px-8">
            @if (session('status'))
                <div class="rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
                    {{ session('status') }}
                </div>
            @endif

            <div class="rounded-lg bg-white p-4 shadow-sm">
                <form method="GET" action="{{ route('reseller.voucher-batches.index') }}" class="flex flex-col gap-3 md:flex-row md:items-end">
                    <div class="w-full md:max-w-xs">
                        <x-input-label for="status" value="Status" />
                        <select id="status" name="status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-rootPrimary focus:ring-rootTeal">
                            <option value="">Semua</option>
                            @foreach (['draft', 'paid', 'generated', 'failed', 'cancelled'] as $statusOption)
                                <option value="{{ $statusOption }}" @selected($status === $statusOption)>{{ strtoupper($statusOption) }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="flex gap-2">
                        <button type="submit" class="rounded-xl bg-rootPrimary px-4 py-2 text-sm text-white hover:bg-rootIndigo">
                            Filter
                        </button>
                        <a href="{{ route('reseller.voucher-batches.index') }}" class="rounded-xl border border-rootPrimary px-4 py-2 text-sm text-rootPrimary hover:bg-rootPink/20">
                            Reset
                        </a>
                    </div>
                </form>
            </div>

            <div class="overflow-hidden rounded-xl bg-white shadow-sm">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left font-semibold text-gray-700">Batch Code</th>
                                <th class="px-4 py-3 text-left font-semibold text-gray-700">Paket</th>
                                <th class="px-4 py-3 text-left font-semibold text-gray-700">Qty</th>
                                <th class="px-4 py-3 text-left font-semibold text-gray-700">Total Cost</th>
                                <th class="px-4 py-3 text-left font-semibold text-gray-700">Status</th>
                                <th class="px-4 py-3 text-right font-semibold text-gray-700">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 bg-white">
                            @forelse ($batches as $batch)
                                <tr>
                                    <td class="px-4 py-3 font-medium text-gray-900">{{ $batch->batch_code }}</td>
                                    <td class="px-4 py-3 text-gray-700">{{ $batch->package->name }}</td>
                                    <td class="px-4 py-3 text-gray-700">{{ $batch->qty_generated }}/{{ $batch->qty_requested }}</td>
                                    <td class="px-4 py-3 text-gray-700">Rp {{ number_format((float) $batch->total_cost, 2, ',', '.') }}</td>
                                    <td class="px-4 py-3">
                                        <x-status-badge :status="$batch->status" />
                                    </td>
                                    <td class="px-4 py-3 text-right">
                                        <a href="{{ route('reseller.voucher-batches.show', $batch) }}" class="rounded-xl border border-rootPrimary px-3 py-2 text-xs font-medium text-rootPrimary hover:bg-rootPink/20">
                                            Detail
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-4 py-8 text-center text-gray-500">Belum ada voucher batch.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="border-t border-gray-100 px-4 py-3">
                    {{ $batches->links() }}
                </div>
            </div>
        </div>
    </div>
</x-dashboard-layout>

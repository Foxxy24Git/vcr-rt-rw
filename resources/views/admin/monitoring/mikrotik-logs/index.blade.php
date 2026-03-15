<x-dashboard-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            Monitoring MikroTik Logs
        </h2>
    </x-slot>

    <div class="py-4 sm:py-6 md:py-8">
        <div class="mx-auto max-w-7xl space-y-4 sm:px-6 lg:px-8">
            @if (session('status'))
                <div class="rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
                    {{ session('status') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="rounded-lg border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700">
                    {{ $errors->first() }}
                </div>
            @endif

            <div class="rounded-xl bg-white p-4 shadow-sm">
                <form method="GET" action="{{ route('admin.mikrotik-logs.index') }}" class="grid gap-3 md:grid-cols-4">
                    <div>
                        <x-input-label for="status" value="Status" />
                        <select id="status" name="status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-rootPrimary focus:ring-rootTeal">
                            <option value="">Semua</option>
                            <option value="success" @selected($status === 'success')>SUCCESS</option>
                            <option value="failed" @selected($status === 'failed')>FAILED</option>
                        </select>
                    </div>

                    <div>
                        <x-input-label for="date_from" value="Tanggal Dari" />
                        <x-text-input id="date_from" name="date_from" type="date" class="mt-1 block w-full" :value="$dateFrom" />
                    </div>

                    <div>
                        <x-input-label for="date_to" value="Tanggal Sampai" />
                        <x-text-input id="date_to" name="date_to" type="date" class="mt-1 block w-full" :value="$dateTo" />
                    </div>

                    <div class="flex items-end gap-2">
                        <button type="submit" class="rounded-xl bg-rootPrimary px-4 py-2 text-sm text-white hover:bg-rootIndigo">
                            Filter
                        </button>
                        <a href="{{ route('admin.mikrotik-logs.index') }}" class="rounded-xl border border-rootPrimary px-4 py-2 text-sm text-rootPrimary hover:bg-rootPink/20">
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
                                <th class="px-4 py-3 text-left font-semibold text-gray-700">Voucher Batch ID</th>
                                <th class="px-4 py-3 text-left font-semibold text-gray-700">Status</th>
                                <th class="px-4 py-3 text-left font-semibold text-gray-700">Message</th>
                                <th class="px-4 py-3 text-left font-semibold text-gray-700">Created At</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 bg-white">
                            @forelse ($logs as $log)
                                <tr>
                                    <td class="px-4 py-3 font-medium text-gray-900">{{ $log->voucher_batch_id ?: '-' }}</td>
                                    <td class="px-4 py-3">
                                        <x-status-badge :status="$log->status" />
                                    </td>
                                    <td class="px-4 py-3 text-gray-700">{{ $log->message ?: '-' }}</td>
                                    <td class="px-4 py-3 text-gray-700">{{ \Illuminate\Support\Carbon::parse($log->created_at)->format('d-m-Y H:i:s') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-4 py-8 text-center text-gray-500">
                                        Belum ada data log MikroTik.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="border-t border-gray-100 px-4 py-3">
                    {{ $logs->links() }}
                </div>
            </div>
        </div>
    </div>
</x-dashboard-layout>

<x-dashboard-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            Wallet Saya
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-7xl space-y-4 sm:px-6 lg:px-8">
            <div class="rounded-lg bg-white p-6 shadow-sm">
                <p class="text-sm text-gray-500">Saldo Saat Ini</p>
                <p class="mt-1 text-3xl font-bold text-rootPrimary">
                    {{ $wallet->currency }} {{ number_format((float) $wallet->balance, 2, ',', '.') }}
                </p>
            </div>

            <div class="overflow-hidden rounded-xl bg-white shadow-sm">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left font-semibold text-gray-700">Waktu</th>
                                <th class="px-4 py-3 text-left font-semibold text-gray-700">Tipe</th>
                                <th class="px-4 py-3 text-left font-semibold text-gray-700">Sumber</th>
                                <th class="px-4 py-3 text-left font-semibold text-gray-700">Nominal</th>
                                <th class="px-4 py-3 text-left font-semibold text-gray-700">Sebelum</th>
                                <th class="px-4 py-3 text-left font-semibold text-gray-700">Sesudah</th>
                                <th class="px-4 py-3 text-left font-semibold text-gray-700">Keterangan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 bg-white">
                            @forelse ($transactions as $transaction)
                                <tr>
                                    <td class="px-4 py-3 text-gray-700">{{ $transaction->created_at?->format('d-m-Y H:i') }}</td>
                                    <td class="px-4 py-3">
                                        <span class="inline-flex rounded-full px-2 py-1 text-xs font-semibold {{ $transaction->type === 'credit' ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700' }}">
                                            {{ strtoupper($transaction->type) }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-gray-700">{{ $transaction->source }}</td>
                                    <td class="px-4 py-3 text-gray-700">{{ number_format((float) $transaction->amount, 2, ',', '.') }}</td>
                                    <td class="px-4 py-3 text-gray-700">{{ number_format((float) $transaction->balance_before, 2, ',', '.') }}</td>
                                    <td class="px-4 py-3 text-gray-700">{{ number_format((float) $transaction->balance_after, 2, ',', '.') }}</td>
                                    <td class="px-4 py-3 text-gray-700">{{ $transaction->description ?? '-' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-4 py-8 text-center text-gray-500">
                                        Belum ada transaksi wallet.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="border-t border-gray-100 px-4 py-3">
                    {{ $transactions->links() }}
                </div>
            </div>
        </div>
    </div>
</x-dashboard-layout>

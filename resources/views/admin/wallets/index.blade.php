<x-dashboard-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            Wallet Reseller
        </h2>
    </x-slot>

    <div class="py-4 sm:py-6 md:py-8">
        <div class="mx-auto max-w-7xl space-y-4 px-3 sm:px-6 lg:px-8">
            @if (session('status'))
                <div class="rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
                    {{ session('status') }}
                </div>
            @endif
            @if (session('success'))
                <div class="rounded-lg border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-700">
                    {{ session('success') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="rounded-lg border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700">
                    <p class="font-semibold">Terdapat kesalahan input:</p>
                    <ul class="mt-1 list-disc ps-5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="rounded-lg bg-white p-4 shadow-sm">
                <form method="GET" action="{{ route('admin.wallets.index') }}" class="flex flex-col gap-3 md:flex-row md:items-end">
                    <div class="w-full md:max-w-md">
                        <x-input-label for="search" value="Cari Reseller (nama/email)" />
                        <x-text-input id="search" name="search" type="text" class="mt-1 block w-full" :value="$search" />
                    </div>

                    <div class="flex gap-2">
                        <button type="submit" class="rounded-xl bg-rootPrimary px-4 py-2 text-sm text-white hover:bg-rootIndigo">
                            Filter
                        </button>
                        <a href="{{ route('admin.wallets.index') }}" class="rounded-xl border border-rootPrimary px-4 py-2 text-sm text-rootPrimary hover:bg-rootPink/20">
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
                                <th class="px-4 py-3 text-left font-semibold text-gray-700">Reseller</th>
                                <th class="px-4 py-3 text-left font-semibold text-gray-700">Saldo</th>
                                <th class="px-4 py-3 text-left font-semibold text-gray-700">Top Up</th>
                                <th class="px-4 py-3 text-right font-semibold text-gray-700">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 bg-white">
                            @forelse ($wallets as $wallet)
                                <tr>
                                    <td class="px-4 py-3">
                                        <p class="font-medium text-gray-900">{{ $wallet->user->name }}</p>
                                        <p class="text-xs text-gray-500">{{ $wallet->user->email }}</p>
                                    </td>
                                    <td class="px-4 py-3 font-medium text-gray-800">
                                        {{ $wallet->currency }} {{ number_format((float) $wallet->balance, 2, ',', '.') }}
                                    </td>
                                    <td class="px-4 py-3">
                                        <form method="POST" action="{{ route('admin.wallets.topup', $wallet) }}" class="flex flex-wrap items-center gap-2">
                                            @csrf
                                            <input
                                                type="number"
                                                name="amount"
                                                min="1"
                                                step="0.01"
                                                placeholder="Nominal"
                                                class="w-32 rounded-md border-gray-300 text-sm shadow-sm focus:border-rootPrimary focus:ring-rootTeal"
                                                required
                                            >
                                            <input
                                                type="text"
                                                name="description"
                                                placeholder="Keterangan (opsional)"
                                                class="w-48 rounded-md border-gray-300 text-sm shadow-sm focus:border-rootPrimary focus:ring-rootTeal"
                                            >
                                            <button type="submit" class="rounded-xl bg-rootPrimary px-3 py-2 text-xs font-medium text-white hover:bg-rootIndigo">
                                                Top Up
                                            </button>
                                        </form>
                                    </td>
                                    <td class="px-4 py-3 text-right">
                                        <div class="flex items-center justify-end gap-2">
                                            <button
                                                type="button"
                                                onclick="openAdjustModal({{ $wallet->id }})"
                                                class="rounded-lg bg-amber-600 px-4 py-2 font-semibold text-white shadow-md transition hover:bg-amber-700"
                                            >
                                                Adjust
                                            </button>
                                            <a
                                                href="{{ route('admin.wallets.ledger', $wallet->id) }}"
                                                style="background:#4f46e5;color:white;padding:8px 16px;border-radius:8px;font-weight:600;display:inline-block;box-shadow:0 4px 10px rgba(0,0,0,0.15);"
                                            >
                                                Lihat Ledger
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-4 py-8 text-center text-gray-500">
                                        Wallet reseller belum tersedia.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="border-t border-gray-100 px-4 py-3">
                    {{ $wallets->links() }}
                </div>
            </div>
        </div>
    </div>

    <div id="adjustModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/40">
        <div class="w-[420px] rounded-lg bg-white p-6 shadow-lg">
            <h2 class="mb-4 text-lg font-semibold text-gray-800">
                Adjust Wallet Balance
            </h2>

            <form id="adjustForm" method="POST">
                @csrf

                <div class="mb-3">
                    <label class="text-sm text-gray-600">Amount</label>
                    <input
                        type="number"
                        name="amount"
                        step="0.01"
                        placeholder="+10000 or -5000"
                        required
                        class="mt-1 w-full rounded border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-rootPrimary focus:ring-rootTeal"
                    >
                </div>

                <div class="mb-4">
                    <label class="text-sm text-gray-600">Reason</label>
                    <input
                        type="text"
                        name="description"
                        required
                        maxlength="255"
                        placeholder="Adjustment reason"
                        class="mt-1 w-full rounded border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-rootPrimary focus:ring-rootTeal"
                    >
                </div>

                <div class="flex justify-end gap-2">
                    <button
                        type="button"
                        onclick="closeAdjustModal()"
                        class="rounded-lg bg-gray-200 px-4 py-2 text-gray-800 hover:bg-gray-300"
                    >
                        Cancel
                    </button>

                    <button
                        type="submit"
                        class="rounded-lg bg-amber-600 px-4 py-2 font-semibold text-white shadow-sm transition hover:bg-amber-700"
                    >
                        Save Adjustment
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openAdjustModal(walletId) {
            const modal = document.getElementById('adjustModal');
            const form = document.getElementById('adjustForm');

            form.action = `/admin/wallets/${walletId}/adjust`;

            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }

        function closeAdjustModal() {
            const modal = document.getElementById('adjustModal');

            modal.classList.remove('flex');
            modal.classList.add('hidden');
        }
    </script>
</x-dashboard-layout>

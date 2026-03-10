<x-dashboard-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            Reports
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-4xl space-y-4 px-4 sm:px-6 lg:px-8">
            @if ($errors->any())
                <div class="rounded-lg border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700">
                    <p class="font-semibold">Export gagal:</p>
                    <ul class="mt-1 list-disc ps-5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
                <h3 class="text-base font-semibold text-gray-900">Voucher Report Export (.xlsx)</h3>
                <p class="mt-1 text-sm text-gray-500">Pilih rentang tanggal, lalu unduh laporan voucher.</p>

                <form method="GET" action="{{ route('reports.vouchers.export') }}" class="mt-5 grid grid-cols-1 gap-4 md:grid-cols-2">
                    <div>
                        <x-input-label for="from_date" value="From Date" />
                        <x-text-input
                            id="from_date"
                            name="from_date"
                            type="date"
                            class="mt-1 block w-full"
                            :value="old('from_date', now()->toDateString())"
                            required
                        />
                    </div>

                    <div>
                        <x-input-label for="to_date" value="To Date" />
                        <x-text-input
                            id="to_date"
                            name="to_date"
                            type="date"
                            class="mt-1 block w-full"
                            :value="old('to_date', now()->toDateString())"
                            required
                        />
                    </div>

                    @if (auth()->user()?->isAdmin())
                        <div class="md:col-span-2">
                            <x-input-label for="reseller_id" value="Reseller (Opsional)" />
                            <select
                                id="reseller_id"
                                name="reseller_id"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-rootPrimary focus:ring-rootTeal"
                            >
                                <option value="">Semua Reseller</option>
                                @foreach ($resellers as $reseller)
                                    <option value="{{ $reseller->id }}" @selected((int) old('reseller_id') === $reseller->id)>
                                        {{ $reseller->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    @endif

                    <div class="md:col-span-2 flex justify-end">
                        <button
                            type="submit"
                            class="rounded-xl bg-rootPrimary px-4 py-2 text-sm font-medium text-white hover:bg-rootIndigo"
                        >
                            Download
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-dashboard-layout>

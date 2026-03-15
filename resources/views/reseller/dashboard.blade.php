<x-dashboard-layout>
    <x-slot name="header">
        <h2 class="text-lg font-semibold leading-tight text-gray-800 sm:text-xl">
            {{ $dashboardTitle ?? __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-4 sm:py-6 md:py-8 lg:py-12">
        <div class="mx-auto max-w-7xl px-3 sm:px-6 lg:px-8">
            <div class="overflow-hidden rounded-lg bg-white shadow-sm transition-shadow duration-200 hover:shadow-md sm:rounded-lg">
                <div class="p-4 text-gray-900 sm:p-5 md:p-6">
                    {{ __('Anda login sebagai :role.', ['role' => $roleLabel ?? 'User']) }}
                </div>
            </div>

            <div class="mb-6 grid grid-cols-1 gap-4 sm:mb-8 sm:gap-6 md:grid-cols-2">
                <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm transition duration-200 hover:shadow-lg md:p-6">
                    <p class="text-xs text-gray-500 sm:text-sm">
                        Saldo Wallet
                    </p>
                    <p class="mt-2 text-2xl font-bold text-green-600 sm:text-3xl">
                        {{ $wallet?->currency ?? 'IDR' }} {{ number_format((float) ($wallet?->balance ?? 0), 0, ',', '.') }}
                    </p>
                    <p class="mt-2 text-xs text-gray-400">
                        Saldo yang tersedia untuk generate voucher
                    </p>
                </div>
                <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm transition duration-200 hover:shadow-lg md:p-6">
                    <p class="text-xs text-gray-500 sm:text-sm">
                        Voucher Generated Hari Ini
                    </p>
                    <p class="mt-2 text-2xl font-bold text-blue-600 sm:text-3xl">
                        {{ $todayVoucherCount }}
                    </p>
                    <p class="mt-2 text-xs text-gray-400">
                        Jumlah voucher yang dibuat hari ini
                    </p>
                </div>
            </div>

            <div class="max-w-xl rounded-xl border border-gray-200 bg-white p-4 shadow-sm transition duration-200 hover:shadow-lg md:p-6">
                <h3 class="mb-4 text-lg font-semibold text-gray-800 sm:mb-6 sm:text-xl">
                    Generate Voucher
                </h3>

                @if ($errors->any())
                    <div class="mb-4 rounded-lg border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700">
                        <p class="font-semibold">Generate gagal:</p>
                        <ul class="mt-1 list-disc ps-5">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('reseller.voucher-batches.store') }}">
                    @csrf

                    <div>
                        <label class="mb-1 block text-sm font-medium text-gray-600">Pilih Paket</label>
                        <select
                            name="internet_package_id"
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-rootPrimary focus:ring-rootTeal"
                            required
                        >
                            @foreach ($packages as $package)
                                <option value="{{ $package->id }}" @selected((int) old('internet_package_id') === $package->id)>
                                    {{ $package->name }} - Rp {{ number_format((float) $package->price, 0, ',', '.') }}
                                </option>
                            @endforeach
                        </select>
                        @error('internet_package_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mt-4">
                        <label class="mb-1 block text-sm font-medium text-gray-600">Jumlah Voucher</label>
                        <input
                            type="number"
                            name="quantity"
                            min="1"
                            max="50"
                            value="{{ old('quantity', 1) }}"
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-rootPrimary focus:ring-rootTeal"
                            required
                        >
                        @error('quantity')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <button
                        type="submit"
                        class="mt-4 w-full rounded-lg bg-green-600 px-6 py-3 font-semibold text-white shadow-md transition duration-200 hover:bg-green-700 hover:shadow-lg"
                    >
                        Generate Voucher
                    </button>
                </form>
            </div>

            <div class="mt-8 rounded-xl border border-gray-200 bg-white p-6 shadow-sm transition duration-200 hover:shadow-lg">
                <h3 class="mb-4 text-lg font-semibold text-gray-800">
                    Voucher Terakhir
                </h3>
                @if(isset($recentVouchers) && $recentVouchers->count())
                    <div class="space-y-3">
                        @foreach($recentVouchers as $voucher)
                            <div class="flex items-center justify-between rounded-lg p-3 transition hover:bg-gray-50">
                                <div>
                                    <p class="font-medium text-gray-800">
                                        {{ $voucher->username ?? $voucher->code }}
                                    </p>
                                    <p class="text-sm text-gray-400">
                                        {{ $voucher->package->name ?? '-' }}
                                    </p>
                                </div>
                                <span class="text-sm text-gray-500">
                                    {{ $voucher->created_at?->format('H:i') }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-sm text-gray-400">
                        Belum ada voucher yang dibuat.
                    </p>
                @endif
            </div>
        </div>
    </div>
</x-dashboard-layout>

<x-dashboard-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            Generate Voucher Batch
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-4xl space-y-4 sm:px-6 lg:px-8">
            @if ($errors->any())
                <div class="rounded-lg border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700">
                    <p class="font-semibold">Generate gagal:</p>
                    <ul class="mt-1 list-disc ps-5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="rounded-lg bg-white p-6 shadow-sm">
                <form method="POST" action="{{ route('reseller.voucher-batches.store') }}" class="space-y-4">
                    @csrf

                    <div>
                        <x-input-label for="internet_package_id" value="Pilih Paket Internet" />
                        <select id="internet_package_id" name="internet_package_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-rootPrimary focus:ring-rootTeal" required>
                            <option value="">-- Pilih Paket --</option>
                            @foreach ($packages as $package)
                                <option value="{{ $package->id }}" @selected((int) old('internet_package_id') === $package->id)>
                                    {{ $package->code }} - {{ $package->name }} (Rp {{ number_format((float) $package->price, 2, ',', '.') }})
                                </option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('internet_package_id')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="quantity" value="Jumlah Voucher" />
                        <x-text-input id="quantity" name="quantity" type="number" min="1" max="500" class="mt-1 block w-full" :value="old('quantity', 1)" required />
                        <x-input-error :messages="$errors->get('quantity')" class="mt-2" />
                        <p class="mt-1 text-xs text-gray-500">
                            Total biaya akan dihitung otomatis berdasarkan harga paket dan jumlah voucher.
                        </p>
                    </div>

                    <div class="flex items-center justify-end gap-2 pt-2">
                        <a href="{{ route('reseller.voucher-batches.index') }}" class="rounded-xl border border-rootPrimary px-4 py-2 text-sm text-rootPrimary hover:bg-rootPink/20">
                            Batal
                        </a>
                        <button type="submit" class="rounded-xl bg-rootPrimary px-4 py-2 text-sm font-medium text-white hover:bg-rootIndigo">
                            Generate Voucher
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-dashboard-layout>

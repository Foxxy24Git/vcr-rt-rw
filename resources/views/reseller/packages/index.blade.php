<x-dashboard-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            Daftar Paket Internet Aktif
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-7xl space-y-4 sm:px-6 lg:px-8">
            <div class="rounded-lg bg-white p-4 shadow-sm">
                <form method="GET" action="{{ route('reseller.packages.index') }}" class="flex flex-col gap-3 md:flex-row md:items-end">
                    <div class="w-full md:max-w-md">
                        <x-input-label for="search" value="Cari Kode/Nama Paket" />
                        <x-text-input id="search" name="search" type="text" class="mt-1 block w-full" :value="$search" />
                    </div>

                    <div class="flex gap-2">
                        <button type="submit" class="rounded-xl bg-rootPrimary px-4 py-2 text-sm text-white hover:bg-rootIndigo">
                            Cari
                        </button>
                        <a href="{{ route('reseller.packages.index') }}" class="rounded-xl border border-rootPrimary px-4 py-2 text-sm text-rootPrimary hover:bg-rootPink/20">
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
                                <th class="px-4 py-3 text-left font-semibold text-gray-700">Kode</th>
                                <th class="px-4 py-3 text-left font-semibold text-gray-700">Nama Paket</th>
                                <th class="px-4 py-3 text-left font-semibold text-gray-700">Harga</th>
                                <th class="px-4 py-3 text-left font-semibold text-gray-700">Masa Aktif</th>
                                <th class="px-4 py-3 text-left font-semibold text-gray-700">Profil MikroTik</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 bg-white">
                            @forelse ($packages as $package)
                                <tr>
                                    <td class="px-4 py-3 font-medium text-gray-900">{{ $package->code }}</td>
                                    <td class="px-4 py-3">
                                        <p class="font-medium text-gray-900">{{ $package->name }}</p>
                                        <p class="text-xs text-gray-500">
                                            DL {{ $package->bandwidth_down_kbps ?? '-' }} Kbps ·
                                            UL {{ $package->bandwidth_up_kbps ?? '-' }} Kbps ·
                                            Kuota {{ $package->quota_mb ?? 'Unlimited' }} MB
                                        </p>
                                    </td>
                                    <td class="px-4 py-3 text-gray-700">Rp {{ number_format((float) $package->price, 2, ',', '.') }}</td>
                                    <td class="px-4 py-3 text-gray-700">{{ $package->validity_value }} {{ $package->validity_unit }}</td>
                                    <td class="px-4 py-3 text-gray-700">{{ $package->mikrotik_profile ?? '-' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-4 py-8 text-center text-gray-500">
                                        Paket aktif belum tersedia.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="border-t border-gray-100 px-4 py-3">
                    {{ $packages->links() }}
                </div>
            </div>
        </div>
    </div>
</x-dashboard-layout>

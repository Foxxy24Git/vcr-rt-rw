<x-dashboard-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <h2 class="text-lg font-semibold leading-tight text-gray-800 sm:text-xl">
                Master Paket Internet
            </h2>

            <a
                href="{{ route('admin.packages.create') }}"
                class="inline-block w-full rounded-xl bg-rootPrimary px-4 py-2 text-center text-sm font-medium text-white shadow-sm transition duration-200 hover:bg-rootIndigo hover:shadow-md md:w-auto"
            >
                + Tambah Paket
            </a>
        </div>
    </x-slot>

    <div class="py-4 sm:py-6 md:py-8">
        <div class="mx-auto max-w-7xl space-y-4 px-4 sm:px-6 lg:px-8">
            @if (session('status'))
                <div class="rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
                    {{ session('status') }}
                </div>
            @endif

            <div class="rounded-lg bg-white p-4 shadow-sm transition duration-200 hover:shadow-md md:p-6">
                <form method="GET" action="{{ route('admin.packages.index') }}" class="grid grid-cols-1 gap-3 md:grid-cols-3">
                    <div>
                        <x-input-label for="search" value="Cari Kode/Nama" />
                        <x-text-input id="search" name="search" type="text" class="mt-1 block w-full" :value="$search" />
                    </div>

                    <div>
                        <x-input-label for="status" value="Status" />
                        <select id="status" name="status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-rootPrimary focus:ring-rootTeal">
                            <option value="">Semua</option>
                            <option value="active" @selected($status === 'active')>Aktif</option>
                            <option value="inactive" @selected($status === 'inactive')>Nonaktif</option>
                        </select>
                    </div>

                    <div class="flex flex-wrap items-end gap-2">
                        <button type="submit" class="w-full rounded-xl bg-rootPrimary px-4 py-2 text-sm text-white shadow-sm transition duration-200 hover:bg-rootIndigo hover:shadow-md md:w-auto">
                            Filter
                        </button>
                        <a href="{{ route('admin.packages.index') }}" class="w-full rounded-xl border border-rootPrimary px-4 py-2 text-center text-sm text-rootPrimary transition duration-200 hover:bg-rootPink/20 md:w-auto">
                            Reset
                        </a>
                    </div>
                </form>
            </div>

            <div class="overflow-hidden rounded-lg bg-white shadow-sm transition duration-200 hover:shadow-md">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-xs sm:text-sm">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-3 py-2 text-left font-semibold text-gray-700 sm:px-4 sm:py-3">Kode</th>
                                <th class="px-3 py-2 text-left font-semibold text-gray-700 sm:px-4 sm:py-3">Nama</th>
                                <th class="px-3 py-2 text-left font-semibold text-gray-700 sm:px-4 sm:py-3">Harga</th>
                                <th class="px-3 py-2 text-left font-semibold text-gray-700 sm:px-4 sm:py-3">Masa Aktif</th>
                                <th class="px-3 py-2 text-left font-semibold text-gray-700 sm:px-4 sm:py-3">Status</th>
                                <th class="px-3 py-2 text-right font-semibold text-gray-700 sm:px-4 sm:py-3">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 bg-white">
                            @forelse ($packages as $package)
                                <tr>
                                    <td class="px-3 py-2 font-medium text-gray-900 sm:px-4 sm:py-3">{{ $package->code }}</td>
                                    <td class="px-3 py-2 sm:px-4 sm:py-3">
                                        <p class="font-medium text-gray-900">{{ $package->name }}</p>
                                        <p class="text-xs text-gray-500">
                                            DL {{ $package->bandwidth_down_kbps ?? '-' }} Kbps ·
                                            UL {{ $package->bandwidth_up_kbps ?? '-' }} Kbps
                                        </p>
                                    </td>
                                    <td class="px-3 py-2 text-gray-700 sm:px-4 sm:py-3">
                                        Rp {{ number_format((float) $package->price, 2, ',', '.') }}
                                    </td>
                                    <td class="px-3 py-2 text-gray-700 sm:px-4 sm:py-3">
                                        {{ $package->validity_value }} {{ $package->validity_unit }}
                                    </td>
                                    <td class="px-3 py-2 sm:px-4 sm:py-3">
                                        <x-status-badge :status="$package->is_active ? 'Aktif' : 'Nonaktif'" />
                                    </td>
                                    <td class="px-3 py-2 sm:px-4 sm:py-3">
                                        <div class="flex flex-wrap justify-end gap-1 sm:gap-2">
                                            <a
                                                href="{{ route('admin.packages.edit', $package) }}"
                                                class="rounded-xl border border-rootPrimary px-2 py-1.5 text-xs font-medium text-rootPrimary transition duration-200 hover:bg-rootPink/20 sm:px-3"
                                            >
                                                Edit
                                            </a>

                                            <form method="POST" action="{{ route('admin.packages.toggle-active', $package) }}">
                                                @csrf
                                                @method('PATCH')
                                                <button
                                                    type="submit"
                                                    class="rounded-xl px-2 py-1.5 text-xs font-medium transition duration-200 sm:px-3 {{ $package->is_active ? 'border border-red-200 bg-red-50 text-red-700 hover:bg-red-100' : 'bg-rootPrimary text-white hover:bg-rootIndigo' }}"
                                                >
                                                    {{ $package->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-3 py-6 text-center text-sm text-gray-500 sm:px-4 sm:py-8">
                                        Belum ada paket internet.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="border-t border-gray-100 px-3 py-3 sm:px-4">
                    {{ $packages->links() }}
                </div>
            </div>
        </div>
    </div>
</x-dashboard-layout>

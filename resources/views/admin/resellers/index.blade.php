<x-dashboard-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Manajemen Reseller
            </h2>

            <a href="{{ route('admin.resellers.create') }}" class="rounded-xl bg-rootPrimary px-4 py-2 text-sm font-medium text-white hover:bg-rootIndigo">
                + Tambah Reseller
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
                <form method="GET" action="{{ route('admin.resellers.index') }}" class="grid gap-3 md:grid-cols-3">
                    <div>
                        <x-input-label for="search" value="Cari nama/email/telepon" />
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

                    <div class="flex items-end gap-2">
                        <button type="submit" class="rounded-xl bg-rootPrimary px-4 py-2 text-sm text-white hover:bg-rootIndigo">
                            Filter
                        </button>
                        <a href="{{ route('admin.resellers.index') }}" class="rounded-xl border border-rootPrimary px-4 py-2 text-sm text-rootPrimary hover:bg-rootPink/20">
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
                                <th class="px-4 py-3 text-left font-semibold text-gray-700">Kontak</th>
                                <th class="px-4 py-3 text-left font-semibold text-gray-700">Saldo Wallet</th>
                                <th class="px-4 py-3 text-left font-semibold text-gray-700">Status</th>
                                <th class="px-4 py-3 text-right font-semibold text-gray-700">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 bg-white">
                            @forelse ($resellers as $reseller)
                                <tr>
                                    <td class="px-4 py-3">
                                        <p class="font-medium text-gray-900">{{ $reseller->name }}</p>
                                        <p class="text-xs text-gray-500">{{ $reseller->email }}</p>
                                    </td>
                                    <td class="px-4 py-3 text-gray-700">
                                        {{ $reseller->phone ?: '-' }}
                                    </td>
                                    <td class="px-4 py-3 font-medium text-gray-800">
                                        @if ($reseller->wallet)
                                            {{ $reseller->wallet->currency }} {{ number_format((float) $reseller->wallet->balance, 2, ',', '.') }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td class="px-4 py-3">
                                        <x-status-badge :status="$reseller->status === 'active' ? 'Aktif' : 'Nonaktif'" />
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="flex justify-end gap-2">
                                            <a href="{{ route('admin.resellers.edit', $reseller->id) }}" class="rounded-xl border border-rootPrimary px-3 py-1.5 text-xs font-medium text-rootPrimary hover:bg-rootPink/20">
                                                Edit
                                            </a>

                                            <form method="POST" action="{{ route('admin.resellers.toggle-status', $reseller->id) }}">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="rounded-xl px-3 py-1.5 text-xs font-medium {{ $reseller->status === 'active' ? 'bg-red-50 text-red-700 border border-red-200 hover:bg-red-100' : 'bg-rootPrimary text-white hover:bg-rootIndigo' }}">
                                                    {{ $reseller->status === 'active' ? 'Nonaktifkan' : 'Aktifkan' }}
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-4 py-8 text-center text-gray-500">
                                        Belum ada data reseller.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="border-t border-gray-100 px-4 py-3">
                    {{ $resellers->links() }}
                </div>
            </div>
        </div>
    </div>
</x-dashboard-layout>

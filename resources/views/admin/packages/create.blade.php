<x-dashboard-layout>
    <x-slot name="header">
        <h2 class="text-lg font-semibold leading-tight text-gray-800 sm:text-xl">
            Tambah Paket Internet
        </h2>
    </x-slot>

    <div class="py-4 sm:py-6 md:py-8">
        <div class="mx-auto max-w-4xl px-3 sm:px-6 lg:px-8">
            <div class="rounded-lg bg-white p-4 shadow-sm transition-shadow duration-200 hover:shadow-md md:p-6">
                <form method="POST" action="{{ route('admin.packages.store') }}">
                    @csrf

                    @include('admin.packages.partials.form-fields')

                    <div class="mt-6 flex flex-wrap justify-end gap-2">
                        <a href="{{ route('admin.packages.index') }}" class="w-full rounded-xl border border-rootPrimary px-4 py-2 text-center text-sm text-rootPrimary transition duration-200 hover:bg-rootPink/20 md:w-auto">
                            Batal
                        </a>
                        <button type="submit" class="w-full rounded-xl bg-rootPrimary px-4 py-2 text-sm font-medium text-white shadow-sm transition duration-200 hover:bg-rootIndigo hover:shadow-md md:w-auto">
                            Simpan Paket
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-dashboard-layout>

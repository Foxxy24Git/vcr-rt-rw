<x-dashboard-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            Edit Paket Internet
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-4xl sm:px-6 lg:px-8">
            <div class="rounded-lg bg-white p-6 shadow-sm">
                <form method="POST" action="{{ route('admin.packages.update', $internetPackage) }}">
                    @csrf
                    @method('PUT')

                    @include('admin.packages.partials.form-fields', ['internetPackage' => $internetPackage])

                    <div class="mt-6 flex items-center justify-end gap-2">
                        <a href="{{ route('admin.packages.index') }}" class="rounded-xl border border-rootPrimary px-4 py-2 text-sm text-rootPrimary hover:bg-rootPink/20">
                            Batal
                        </a>
                        <button type="submit" class="rounded-xl bg-rootPrimary px-4 py-2 text-sm font-medium text-white hover:bg-rootIndigo">
                            Update Paket
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-dashboard-layout>

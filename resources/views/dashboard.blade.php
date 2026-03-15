<x-dashboard-layout>
    <x-slot name="header">
        <h2 class="text-lg font-semibold leading-tight text-gray-800 md:text-xl">
            {{ $dashboardTitle ?? __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-4 sm:py-6 md:py-8">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="overflow-hidden rounded-lg bg-white shadow-sm transition duration-200 hover:shadow-md">
                <div class="p-4 text-gray-900 md:p-6">
                    <p class="text-sm md:text-base">
                        {{ __('Anda login sebagai :role.', ['role' => $roleLabel ?? 'User']) }}
                    </p>
                </div>
            </div>
        </div>
    </div>
</x-dashboard-layout>

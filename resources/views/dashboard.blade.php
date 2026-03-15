<x-dashboard-layout>
    <x-slot name="header">
        <h2 class="text-base font-semibold leading-tight text-gray-800 sm:text-lg md:text-xl">
            {{ $dashboardTitle ?? __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-4 sm:py-6 md:py-8 lg:py-12">
        <div class="mx-auto w-full max-w-7xl px-3 sm:px-6 lg:px-8">
            <div class="overflow-hidden rounded-lg bg-white shadow-sm transition-shadow duration-200 hover:shadow-md sm:rounded-lg">
                <div class="p-4 text-gray-900 sm:p-5 md:p-6">
                    <p class="text-sm sm:text-base">
                        {{ __('Anda login sebagai :role.', ['role' => $roleLabel ?? 'User']) }}
                    </p>
                </div>
            </div>
        </div>
    </div>
</x-dashboard-layout>

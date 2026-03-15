<x-dashboard-layout>
    <x-slot name="header">
        <h2 class="text-base font-semibold leading-tight text-gray-800 sm:text-lg md:text-xl">
            {{ __('Profile') }}
        </h2>
    </x-slot>

    <div class="py-4 sm:py-6 md:py-8 lg:py-12">
        <div class="mx-auto max-w-7xl space-y-4 px-3 sm:space-y-6 sm:px-6 lg:px-8">
            <div class="rounded-lg bg-white p-4 shadow-sm transition-shadow duration-200 hover:shadow-md sm:p-6 md:p-8">
                <div class="max-w-xl">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>

            <div class="rounded-lg bg-white p-4 shadow-sm transition-shadow duration-200 hover:shadow-md sm:p-6 md:p-8">
                <div class="max-w-xl">
                    @include('profile.partials.update-password-form')
                </div>
            </div>

            <div class="rounded-lg bg-white p-4 shadow-sm transition-shadow duration-200 hover:shadow-md sm:p-6 md:p-8">
                <div class="max-w-xl">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>
        </div>
    </div>
</x-dashboard-layout>

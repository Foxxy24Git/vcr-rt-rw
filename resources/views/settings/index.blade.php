<x-dashboard-layout>
    <x-slot name="header">
        <h2 class="text-lg font-semibold leading-tight text-gray-800 sm:text-xl">
            {{ __('System Settings') }}
        </h2>
    </x-slot>

    <div class="py-4 sm:py-6 md:py-8">
        <div class="mx-auto max-w-2xl space-y-4 px-3 sm:space-y-6 sm:px-6 lg:px-8">
            @if (session('status'))
                <div class="rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
                    {{ session('status') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="rounded-lg border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700">
                    <p class="font-semibold">{{ __('Validation errors:') }}</p>
                    <ul class="mt-1 list-disc ps-5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm transition-shadow duration-200 hover:shadow-md md:p-6">
                <form method="POST" action="{{ route('settings.update') }}" class="space-y-6">
                    @csrf

                    <div>
                        <label for="mikrotik_host" class="mb-1 block text-sm font-medium text-gray-700">Mikrotik Host</label>
                        <input type="text" name="mikrotik_host" id="mikrotik_host" value="{{ old('mikrotik_host', $settings['mikrotik_host'] ?? '') }}"
                            class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        <x-input-error :messages="$errors->get('mikrotik_host')" class="mt-1" />
                    </div>

                    <div>
                        <label for="mikrotik_port" class="mb-1 block text-sm font-medium text-gray-700">Mikrotik Port</label>
                        <input type="number" name="mikrotik_port" id="mikrotik_port" value="{{ old('mikrotik_port', $settings['mikrotik_port'] ?? '') }}"
                            class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        <x-input-error :messages="$errors->get('mikrotik_port')" class="mt-1" />
                    </div>

                    <div>
                        <label for="mikrotik_timeout" class="mb-1 block text-sm font-medium text-gray-700">Mikrotik Timeout</label>
                        <input type="number" name="mikrotik_timeout" id="mikrotik_timeout" value="{{ old('mikrotik_timeout', $settings['mikrotik_timeout'] ?? '') }}"
                            class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        <x-input-error :messages="$errors->get('mikrotik_timeout')" class="mt-1" />
                    </div>

                    <div>
                        <label for="hotspot_name" class="mb-1 block text-sm font-medium text-gray-700">Hotspot Name</label>
                        <input type="text" name="hotspot_name" id="hotspot_name" value="{{ old('hotspot_name', $settings['hotspot_name'] ?? '') }}"
                            class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        <x-input-error :messages="$errors->get('hotspot_name')" class="mt-1" />
                    </div>

                    <div class="flex justify-end border-t border-gray-200 pt-4">
                        <button type="submit" class="w-full rounded-lg bg-indigo-600 px-4 py-2.5 text-white shadow-sm transition duration-200 hover:bg-indigo-700 hover:shadow-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 md:w-auto md:px-5">
                            Save Settings
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-dashboard-layout>

<header class="sticky top-0 z-30 flex h-16 flex-shrink-0 border-b border-gray-200 bg-white">
    <button type="button"
            @click="sidebarOpen = true"
            class="border-r border-gray-200 px-4 text-gray-500 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-indigo-500 lg:hidden">
        <span class="sr-only">{{ __('Open sidebar') }}</span>
        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
        </svg>
    </button>
    <div class="flex flex-1 items-center justify-between px-4 sm:px-6 lg:px-8">
        <div class="flex flex-1 items-center gap-4">
            <a href="{{ route('dashboard') }}" class="hidden lg:block">
                <x-application-logo class="block h-8 w-auto fill-current text-gray-800" />
            </a>
        </div>
        <div class="flex items-center gap-2">
            <button type="button" class="rounded-full p-2 text-gray-500 hover:bg-gray-100 hover:text-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                <span class="sr-only">{{ __('Notifications') }}</span>
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6 6 0 00-6-6 6 6 0 00-6 6v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                </svg>
            </button>
            <x-dropdown align="right" width="48">
                <x-slot name="trigger">
                    <button type="button" class="flex items-center gap-2 rounded-full px-2 py-1.5 text-sm font-medium text-gray-700 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                        <span class="hidden sm:inline">{{ Auth::user()?->name }}</span>
                        <svg class="h-5 w-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                    </button>
                </x-slot>
                <x-slot name="content">
                    <x-dropdown-link :href="route('profile.edit')">
                        {{ __('Profile') }}
                    </x-dropdown-link>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <x-dropdown-link :href="route('logout')"
                                onclick="event.preventDefault(); this.closest('form').submit();">
                            {{ __('Log Out') }}
                        </x-dropdown-link>
                    </form>
                </x-slot>
            </x-dropdown>
        </div>
    </div>
</header>

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="bg-gray-100 font-sans antialiased" x-data="{ sidebarOpen: false }">
        <div class="flex min-h-screen">
            <!-- Mobile sidebar backdrop -->
            <div x-show="sidebarOpen"
                 x-transition:enter="transition-opacity ease-out duration-200"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="transition-opacity ease-in duration-150"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 @click="sidebarOpen = false"
                 class="fixed inset-0 z-30 bg-gray-900/50 md:hidden"
                 style="display: none;"></div>

            <!-- Sidebar: hidden on mobile, visible on md+ -->
            <aside class="fixed inset-y-0 left-0 z-40 flex w-64 flex-col border-r border-gray-200 bg-white shadow-sm transition-transform duration-200 ease-out md:relative md:translate-x-0"
                   :class="{ 'translate-x-0': sidebarOpen, '-translate-x-full': !sidebarOpen }">
                @php
                    $user = auth()->user();
                @endphp
                <div class="flex h-14 items-center justify-between border-b border-gray-200 px-4 md:justify-start">
                    <span class="text-lg font-semibold text-gray-800">{{ $user->role->value === 'admin' ? 'Admin Panel' : 'Panel Reseller' }}</span>
                    <button type="button" @click="sidebarOpen = false" class="rounded-lg p-2 text-gray-500 hover:bg-gray-100 md:hidden">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>
                </div>

                <nav class="mt-4 flex-1 space-y-1 overflow-y-auto px-3 pb-4">
                    {{-- Admin menu --}}
                    @if($user->role->value === 'admin')
                        <a href="{{ route('dashboard') }}" @click="sidebarOpen = false"
                            class="flex items-center rounded-lg px-3 py-2 text-sm transition-colors duration-200 {{ request()->routeIs('dashboard') || request()->routeIs('admin.dashboard') ? 'bg-gray-100 text-gray-900 font-medium' : 'text-gray-600 hover:bg-gray-50' }}">
                            <svg xmlns="http://www.w3.org/2000/svg" class="mr-3 h-5 w-5 shrink-0 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a2 2 0 002-2v-4a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 002 2m-6 0h6a2 2 0 002-2v-4a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 002 2h-6" />
                            </svg>
                            Dashboard
                        </a>
                        <a href="{{ route('admin.packages.index') }}" @click="sidebarOpen = false"
                            class="flex items-center rounded-lg px-3 py-2 text-sm transition-colors duration-200 {{ request()->routeIs('admin.packages.*') ? 'bg-gray-100 text-gray-900 font-medium' : 'text-gray-600 hover:bg-gray-50' }}">
                            <svg xmlns="http://www.w3.org/2000/svg" class="mr-3 h-5 w-5 shrink-0 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                            </svg>
                            Paket Internet
                        </a>
                        <a href="{{ route('admin.voucher-batches.index') }}" @click="sidebarOpen = false"
                            class="flex items-center rounded-lg px-3 py-2 text-sm transition-colors duration-200 {{ request()->routeIs('admin.voucher-batches.*') ? 'bg-gray-100 text-gray-900 font-medium' : 'text-gray-600 hover:bg-gray-50' }}">
                            <svg xmlns="http://www.w3.org/2000/svg" class="mr-3 h-5 w-5 shrink-0 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z" />
                            </svg>
                            Voucher Batch
                        </a>
                        <a href="{{ route('admin.resellers.index') }}" @click="sidebarOpen = false"
                            class="flex items-center rounded-lg px-3 py-2 text-sm transition-colors duration-200 {{ request()->routeIs('admin.resellers.*') ? 'bg-gray-100 text-gray-900 font-medium' : 'text-gray-600 hover:bg-gray-50' }}">
                            <svg xmlns="http://www.w3.org/2000/svg" class="mr-3 h-5 w-5 shrink-0 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                            Reseller
                        </a>
                        <a href="{{ route('admin.wallets.index') }}" @click="sidebarOpen = false"
                            class="flex items-center rounded-lg px-3 py-2 text-sm transition-colors duration-200 {{ request()->routeIs('admin.wallets.*') ? 'bg-gray-100 text-gray-900 font-medium' : 'text-gray-600 hover:bg-gray-50' }}">
                            <svg xmlns="http://www.w3.org/2000/svg" class="mr-3 h-5 w-5 shrink-0 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                            </svg>
                            Wallet
                        </a>

                        <div class="pt-2" x-data="{ open: {{ request()->routeIs('admin.mikrotik-logs.*', 'admin.failed-jobs.*') ? 'true' : 'false' }} }">
                            <button type="button"
                                @click="open = !open"
                                class="flex w-full items-center justify-between rounded-lg px-3 py-2 text-left text-sm text-gray-600 transition-colors duration-200 hover:bg-gray-50">
                                <span class="flex items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="mr-3 h-5 w-5 shrink-0 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                    </svg>
                                    Monitoring
                                </span>
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 shrink-0 transition-transform" :class="{ 'rotate-180': open }" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>
                            <div x-show="open" x-transition class="mt-2 space-y-1 pl-8">
                                <a href="{{ route('admin.mikrotik-logs.index') }}" @click="sidebarOpen = false"
                                    class="block rounded-lg px-3 py-2 text-sm {{ request()->routeIs('admin.mikrotik-logs.*') ? 'font-medium text-gray-900 bg-gray-100' : 'text-gray-600 hover:bg-gray-50' }}">
                                    MikroTik Logs
                                </a>
                                <a href="{{ route('admin.failed-jobs.index') }}" @click="sidebarOpen = false"
                                    class="block rounded-lg px-3 py-2 text-sm {{ request()->routeIs('admin.failed-jobs.*') ? 'font-medium text-gray-900 bg-gray-100' : 'text-gray-600 hover:bg-gray-50' }}">
                                    Failed Jobs
                                </a>
                            </div>
                        </div>

                        <a href="{{ route('settings.index') }}" @click="sidebarOpen = false"
                            class="flex items-center rounded-lg px-3 py-2 text-sm transition-colors duration-200 {{ request()->routeIs('settings.*') ? 'bg-gray-100 text-gray-900 font-medium' : 'text-gray-600 hover:bg-gray-50' }}">
                            <svg xmlns="http://www.w3.org/2000/svg" class="mr-3 h-5 w-5 shrink-0 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            System Settings
                        </a>
                        <a href="{{ route('admin.vcr-settings.edit') }}" @click="sidebarOpen = false"
                            class="flex items-center rounded-lg px-3 py-2 text-sm transition-colors duration-200 {{ request()->routeIs('admin.vcr-settings.*') ? 'bg-gray-100 text-gray-900 font-medium' : 'text-gray-600 hover:bg-gray-50' }}">
                            <svg xmlns="http://www.w3.org/2000/svg" class="mr-3 h-5 w-5 shrink-0 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M7 4v16M17 4v16M3 8h4m10 0h4M3 12h18M3 16h4m10 0h4M4 20h16a1 1 0 001-1V5a1 1 0 00-1-1H4a1 1 0 00-1 1v14a1 1 0 001 1z" />
                            </svg>
                            VCR Settings
                        </a>
                    @endif

                    {{-- Reseller menu --}}
                    @if($user->role->value === 'reseller')
                        <a href="{{ route('dashboard') }}" @click="sidebarOpen = false"
                            class="flex items-center rounded-lg px-3 py-2 text-sm transition-colors duration-200 {{ request()->routeIs('dashboard') || request()->routeIs('reseller.dashboard') ? 'bg-gray-100 text-gray-900 font-medium' : 'text-gray-600 hover:bg-gray-50' }}">
                            <svg xmlns="http://www.w3.org/2000/svg" class="mr-3 h-5 w-5 shrink-0 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a2 2 0 002-2v-4a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 002 2m-6 0h6a2 2 0 002-2v-4a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 002 2h-6" />
                            </svg>
                            Dashboard
                        </a>
                        <a href="{{ route('reseller.packages.index') }}" @click="sidebarOpen = false"
                            class="flex items-center rounded-lg px-3 py-2 text-sm transition-colors duration-200 {{ request()->routeIs('reseller.packages.*') ? 'bg-gray-100 text-gray-900 font-medium' : 'text-gray-600 hover:bg-gray-50' }}">
                            <svg xmlns="http://www.w3.org/2000/svg" class="mr-3 h-5 w-5 shrink-0 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                            </svg>
                            Paket Internet
                        </a>
                        <a href="{{ route('reseller.voucher-batches.index') }}" @click="sidebarOpen = false"
                            class="flex items-center rounded-lg px-3 py-2 text-sm transition-colors duration-200 {{ request()->routeIs('reseller.voucher-batches.*') ? 'bg-gray-100 text-gray-900 font-medium' : 'text-gray-600 hover:bg-gray-50' }}">
                            <svg xmlns="http://www.w3.org/2000/svg" class="mr-3 h-5 w-5 shrink-0 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z" />
                            </svg>
                            Voucher Batch
                        </a>
                        <a href="{{ route('reseller.wallet.show') }}" @click="sidebarOpen = false"
                            class="flex items-center rounded-lg px-3 py-2 text-sm transition-colors duration-200 {{ request()->routeIs('reseller.wallet.*') ? 'bg-gray-100 text-gray-900 font-medium' : 'text-gray-600 hover:bg-gray-50' }}">
                            <svg xmlns="http://www.w3.org/2000/svg" class="mr-3 h-5 w-5 shrink-0 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                            </svg>
                            Wallet Saya
                        </a>
                        <a href="{{ route('reports.vouchers.index') }}" @click="sidebarOpen = false"
                            class="flex items-center rounded-lg px-3 py-2 text-sm transition-colors duration-200 {{ request()->routeIs('reports.*') ? 'bg-gray-100 text-gray-900 font-medium' : 'text-gray-600 hover:bg-gray-50' }}">
                            <svg xmlns="http://www.w3.org/2000/svg" class="mr-3 h-5 w-5 shrink-0 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            Reports
                        </a>
                    @endif
                </nav>
            </aside>

            <!-- Main content -->
            <div class="flex min-w-0 flex-1 flex-col">
                <!-- Mobile topbar: [ hamburger ] [ title ] [ user dropdown ] -->
                <header class="flex items-center justify-between border-b border-gray-200 bg-white px-4 py-3 md:hidden">
                    <button type="button" @click="sidebarOpen = true" class="shrink-0 rounded-lg p-2 text-gray-500 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-200">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                    <span class="min-w-0 flex-1 truncate text-center text-lg font-semibold text-gray-800">{{ auth()->user()->role->value === 'admin' ? 'Dashboard Admin' : 'Dashboard' }}</span>
                    <div class="flex shrink-0 items-center gap-2">
                        <div x-data="{ userMenuOpen: false }" class="relative">
                            <button type="button"
                                @click="userMenuOpen = !userMenuOpen"
                                class="flex items-center gap-1 rounded-lg px-2 py-1.5 text-sm font-medium text-gray-700 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-200">
                                <span class="max-w-[120px] truncate">{{ auth()->user()->name }}</span>
                                <svg class="h-4 w-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>
                            <div x-show="userMenuOpen"
                                @click.outside="userMenuOpen = false"
                                x-transition
                                class="absolute right-0 z-20 mt-2 w-48 rounded-lg border border-gray-200 bg-white py-1 shadow-lg">
                                <a href="{{ route('profile.edit') }}"
                                    class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    Profile
                                </a>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit"
                                        class="block w-full px-4 py-2 text-left text-sm text-gray-700 hover:bg-gray-100">
                                        Log Out
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </header>

                <!-- Desktop topbar -->
                <header class="hidden items-center justify-between border-b border-gray-200 bg-white px-4 py-3 md:flex sm:px-6">
                    <div class="flex-1"></div>
                    <div class="flex items-center gap-2">
                        <div x-data="{ userMenuOpen: false }" class="relative">
                            <button type="button"
                                @click="userMenuOpen = !userMenuOpen"
                                class="flex items-center gap-2 rounded-lg px-2 py-1.5 text-sm font-medium text-gray-700 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-200">
                                <span>{{ auth()->user()->name }}</span>
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>
                            <div x-show="userMenuOpen"
                                @click.outside="userMenuOpen = false"
                                x-transition
                                class="absolute right-0 z-10 mt-2 w-48 rounded-lg border border-gray-200 bg-white py-1 shadow-lg">
                                <a href="{{ route('profile.edit') }}"
                                    class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    Profile
                                </a>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit"
                                        class="block w-full px-4 py-2 text-left text-sm text-gray-700 hover:bg-gray-100">
                                        Log Out
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </header>

                <!-- Page Content -->
                <main class="flex-1 p-4 sm:p-6">
                    @isset($header)
                        <div class="mb-4 md:mb-6">
                            {{ $header }}
                        </div>
                    @endisset
                    @hasSection('content')
                        @yield('content')
                    @else
                        {{ $slot }}
                    @endif
                </main>
            </div>
        </div>
    </body>
</html>

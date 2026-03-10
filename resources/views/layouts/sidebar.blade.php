@php
    $voucherActive = request()->routeIs('admin.packages.*', 'admin.voucher-batches.*');
    $resellerActive = request()->routeIs('admin.resellers.*', 'admin.wallets.*');
    $monitoringActive = request()->routeIs('reports.*', 'admin.mikrotik-logs.*', 'admin.failed-jobs.*');
    $systemActive = request()->routeIs('settings.*', 'admin.vcr-settings.*');
    $adminActive = request()->routeIs('admin.dashboard');
    $resellerVoucherActive = request()->routeIs('reseller.packages.*', 'reseller.wallet.*', 'reseller.voucher-batches.*');
@endphp
    <!-- Mobile sidebar backdrop -->
    <div x-show="sidebarOpen"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-40 bg-gray-900/60 backdrop-blur-sm lg:hidden"
         @click="sidebarOpen = false"
         style="display: none;">
    </div>

    <!-- Sidebar -->
    <aside x-data="{
        voucherOpen: {{ $voucherActive ? 'true' : 'false' }},
        resellerOpen: {{ $resellerActive ? 'true' : 'false' }},
        monitoringOpen: {{ $monitoringActive ? 'true' : 'false' }},
        systemOpen: {{ $systemActive ? 'true' : 'false' }},
        adminOpen: {{ $adminActive ? 'true' : 'false' }},
        resellerVoucherOpen: {{ $resellerVoucherActive ? 'true' : 'false' }}
    }"
           :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'"
           class="fixed inset-y-0 left-0 z-50 flex w-64 flex-col border-r border-gray-200 bg-white shadow-xl transition-transform duration-300 ease-out lg:static lg:inset-auto lg:flex-shrink-0 lg:shadow-none">
        <!-- Logo -->
        <div class="flex h-16 flex-shrink-0 items-center border-b border-gray-100 px-5">
            <a href="{{ route('dashboard') }}" class="flex items-center gap-2 transition-opacity hover:opacity-90">
                <x-application-logo class="block h-8 w-auto fill-current text-gray-800" />
            </a>
        </div>

        <nav class="flex-1 space-y-0.5 overflow-y-auto px-3 py-4" @click="sidebarOpen = false">
            {{-- Dashboard --}}
            <div class="py-1">
                <a href="{{ route('dashboard') }}"
                   class="@if(request()->routeIs('dashboard', 'admin.dashboard', 'reseller.dashboard')) border-l-indigo-600 bg-indigo-50 text-indigo-700 @else text-gray-600 hover:bg-gray-100 hover:text-gray-900 @endif group flex items-center gap-3 rounded-r-lg border-l-2 border-transparent px-3 py-2.5 text-sm font-medium transition-all duration-200">
                    <svg class="h-5 w-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                    </svg>
                    {{ __('Dashboard') }}
                </a>
            </div>

            @if (Auth::check() && Auth::user()->isAdmin())
                {{-- Voucher (collapsible) --}}
                <div class="py-1">
                    <button type="button"
                            @click="voucherOpen = !voucherOpen"
                            class="flex w-full items-center justify-between rounded-lg px-3 py-2.5 text-xs font-semibold uppercase tracking-wider text-gray-500 transition-colors duration-200 hover:bg-gray-100 hover:text-gray-700">
                        <span class="flex items-center gap-3">
                            <svg class="h-5 w-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/>
                            </svg>
                            Voucher
                        </span>
                        <svg class="h-4 w-4 transition-transform duration-200" :class="voucherOpen && 'rotate-180'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>
                    <div x-show="voucherOpen"
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 -translate-y-1"
                         x-transition:enter-end="opacity-100 translate-y-0"
                         x-transition:leave="transition ease-in duration-150"
                         x-transition:leave-start="opacity-100 translate-y-0"
                         x-transition:leave-end="opacity-0 -translate-y-1"
                         class="mt-0.5 space-y-0.5 pl-4">
                        <a href="{{ route('admin.packages.index') }}"
                           class="@if(request()->routeIs('admin.packages.*')) border-l-indigo-600 bg-indigo-50 text-indigo-700 @else text-gray-600 hover:bg-gray-100 hover:text-gray-900 @endif group flex items-center gap-3 rounded-r-lg border-l-2 border-transparent px-3 py-2 text-sm font-medium transition-all duration-200">
                            <svg class="h-5 w-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                            </svg>
                            {{ __('Paket Internet') }}
                        </a>
                        <a href="{{ route('admin.voucher-batches.index') }}"
                           class="@if(request()->routeIs('admin.voucher-batches.*')) border-l-indigo-600 bg-indigo-50 text-indigo-700 @else text-gray-600 hover:bg-gray-100 hover:text-gray-900 @endif group flex items-center gap-3 rounded-r-lg border-l-2 border-transparent px-3 py-2 text-sm font-medium transition-all duration-200">
                            <svg class="h-5 w-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                            </svg>
                            {{ __('Voucher Batch') }}
                        </a>
                    </div>
                </div>

                {{-- Reseller (collapsible) --}}
                <div class="py-1">
                    <button type="button"
                            @click="resellerOpen = !resellerOpen"
                            class="flex w-full items-center justify-between rounded-lg px-3 py-2.5 text-xs font-semibold uppercase tracking-wider text-gray-500 transition-colors duration-200 hover:bg-gray-100 hover:text-gray-700">
                        <span class="flex items-center gap-3">
                            <svg class="h-5 w-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                            </svg>
                            Reseller
                        </span>
                        <svg class="h-4 w-4 transition-transform duration-200" :class="resellerOpen && 'rotate-180'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>
                    <div x-show="resellerOpen"
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 -translate-y-1"
                         x-transition:enter-end="opacity-100 translate-y-0"
                         x-transition:leave="transition ease-in duration-150"
                         x-transition:leave-start="opacity-100 translate-y-0"
                         x-transition:leave-end="opacity-0 -translate-y-1"
                         class="mt-0.5 space-y-0.5 pl-4">
                        <a href="{{ route('admin.resellers.index') }}"
                           class="@if(request()->routeIs('admin.resellers.*')) border-l-indigo-600 bg-indigo-50 text-indigo-700 @else text-gray-600 hover:bg-gray-100 hover:text-gray-900 @endif group flex items-center gap-3 rounded-r-lg border-l-2 border-transparent px-3 py-2 text-sm font-medium transition-all duration-200">
                            <svg class="h-5 w-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                            {{ __('Reseller') }}
                        </a>
                        <a href="{{ route('admin.wallets.index') }}"
                           class="@if(request()->routeIs('admin.wallets.*')) border-l-indigo-600 bg-indigo-50 text-indigo-700 @else text-gray-600 hover:bg-gray-100 hover:text-gray-900 @endif group flex items-center gap-3 rounded-r-lg border-l-2 border-transparent px-3 py-2 text-sm font-medium transition-all duration-200">
                            <svg class="h-5 w-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                            </svg>
                            {{ __('Wallet') }}
                        </a>
                    </div>
                </div>

                {{-- Monitoring (collapsible) --}}
                <div class="py-1">
                    <button type="button"
                            @click="monitoringOpen = !monitoringOpen"
                            class="flex w-full items-center justify-between rounded-lg px-3 py-2.5 text-xs font-semibold uppercase tracking-wider text-gray-500 transition-colors duration-200 hover:bg-gray-100 hover:text-gray-700">
                        <span class="flex items-center gap-3">
                            <svg class="h-5 w-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3"/>
                            </svg>
                            Monitoring
                        </span>
                        <svg class="h-4 w-4 transition-transform duration-200" :class="monitoringOpen && 'rotate-180'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>
                    <div x-show="monitoringOpen"
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 -translate-y-1"
                         x-transition:enter-end="opacity-100 translate-y-0"
                         x-transition:leave="transition ease-in duration-150"
                         x-transition:leave-start="opacity-100 translate-y-0"
                         x-transition:leave-end="opacity-0 -translate-y-1"
                         class="mt-0.5 space-y-0.5 pl-4">
                        <a href="{{ route('reports.vouchers.index') }}"
                           class="@if(request()->routeIs('reports.*')) border-l-indigo-600 bg-indigo-50 text-indigo-700 @else text-gray-600 hover:bg-gray-100 hover:text-gray-900 @endif group flex items-center gap-3 rounded-r-lg border-l-2 border-transparent px-3 py-2 text-sm font-medium transition-all duration-200">
                            <svg class="h-5 w-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                            </svg>
                            {{ __('Reports') }}
                        </a>
                        <a href="{{ route('admin.mikrotik-logs.index') }}"
                           class="@if(request()->routeIs('admin.mikrotik-logs.*')) border-l-indigo-600 bg-indigo-50 text-indigo-700 @else text-gray-600 hover:bg-gray-100 hover:text-gray-900 @endif group flex items-center gap-3 rounded-r-lg border-l-2 border-transparent px-3 py-2 text-sm font-medium transition-all duration-200">
                            <svg class="h-5 w-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                            </svg>
                            {{ __('MikroTik Logs') }}
                        </a>
                        <a href="{{ route('admin.failed-jobs.index') }}"
                           class="@if(request()->routeIs('admin.failed-jobs.*')) border-l-indigo-600 bg-indigo-50 text-indigo-700 @else text-gray-600 hover:bg-gray-100 hover:text-gray-900 @endif group flex items-center gap-3 rounded-r-lg border-l-2 border-transparent px-3 py-2 text-sm font-medium transition-all duration-200">
                            <svg class="h-5 w-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            {{ __('Failed Jobs') }}
                        </a>
                    </div>
                </div>

                {{-- System (collapsible) --}}
                <div class="py-1">
                    <button type="button"
                            @click="systemOpen = !systemOpen"
                            class="flex w-full items-center justify-between rounded-lg px-3 py-2.5 text-xs font-semibold uppercase tracking-wider text-gray-500 transition-colors duration-200 hover:bg-gray-100 hover:text-gray-700">
                        <span class="flex items-center gap-3">
                            <svg class="h-5 w-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            System
                        </span>
                        <svg class="h-4 w-4 transition-transform duration-200" :class="systemOpen && 'rotate-180'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>
                    <div x-show="systemOpen"
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 -translate-y-1"
                         x-transition:enter-end="opacity-100 translate-y-0"
                         x-transition:leave="transition ease-in duration-150"
                         x-transition:leave-start="opacity-100 translate-y-0"
                         x-transition:leave-end="opacity-0 -translate-y-1"
                         class="mt-0.5 space-y-0.5 pl-4">
                        <a href="{{ route('settings.index') }}"
                           class="@if(request()->routeIs('settings.*')) border-l-indigo-600 bg-indigo-50 text-indigo-700 @else text-gray-600 hover:bg-gray-100 hover:text-gray-900 @endif group flex items-center gap-3 rounded-r-lg border-l-2 border-transparent px-3 py-2 text-sm font-medium transition-all duration-200">
                            <svg class="h-5 w-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            {{ __('System Settings') }}
                        </a>
                        <a href="{{ route('admin.vcr-settings.edit') }}"
                           class="@if(request()->routeIs('admin.vcr-settings.*')) border-l-indigo-600 bg-indigo-50 text-indigo-700 @else text-gray-600 hover:bg-gray-100 hover:text-gray-900 @endif group flex items-center gap-3 rounded-r-lg border-l-2 border-transparent px-3 py-2 text-sm font-medium transition-all duration-200">
                            <svg class="h-5 w-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                            {{ __('VCR Settings') }}
                        </a>
                    </div>
                </div>

                {{-- Admin (collapsible) --}}
                <div class="py-1">
                    <button type="button"
                            @click="adminOpen = !adminOpen"
                            class="flex w-full items-center justify-between rounded-lg px-3 py-2.5 text-xs font-semibold uppercase tracking-wider text-gray-500 transition-colors duration-200 hover:bg-gray-100 hover:text-gray-700">
                        <span class="flex items-center gap-3">
                            <svg class="h-5 w-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                            </svg>
                            Admin
                        </span>
                        <svg class="h-4 w-4 transition-transform duration-200" :class="adminOpen && 'rotate-180'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>
                    <div x-show="adminOpen"
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 -translate-y-1"
                         x-transition:enter-end="opacity-100 translate-y-0"
                         x-transition:leave="transition ease-in duration-150"
                         x-transition:leave-start="opacity-100 translate-y-0"
                         x-transition:leave-end="opacity-0 -translate-y-1"
                         class="mt-0.5 space-y-0.5 pl-4">
                        <a href="{{ route('admin.dashboard') }}"
                           class="@if(request()->routeIs('admin.dashboard')) border-l-indigo-600 bg-indigo-50 text-indigo-700 @else text-gray-600 hover:bg-gray-100 hover:text-gray-900 @endif group flex items-center gap-3 rounded-r-lg border-l-2 border-transparent px-3 py-2 text-sm font-medium transition-all duration-200">
                            <svg class="h-5 w-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                            </svg>
                            {{ __('Super Admin') }}
                        </a>
                    </div>
                </div>
            @endif

            @if (Auth::check() && Auth::user()->isReseller())
                {{-- Reseller: Voucher (collapsible) --}}
                <div class="py-1">
                    <button type="button"
                            @click="resellerVoucherOpen = !resellerVoucherOpen"
                            class="flex w-full items-center justify-between rounded-lg px-3 py-2.5 text-xs font-semibold uppercase tracking-wider text-gray-500 transition-colors duration-200 hover:bg-gray-100 hover:text-gray-700">
                        <span class="flex items-center gap-3">
                            <svg class="h-5 w-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/>
                            </svg>
                            Voucher
                        </span>
                        <svg class="h-4 w-4 transition-transform duration-200" :class="resellerVoucherOpen && 'rotate-180'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>
                    <div x-show="resellerVoucherOpen"
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 -translate-y-1"
                         x-transition:enter-end="opacity-100 translate-y-0"
                         x-transition:leave="transition ease-in duration-150"
                         x-transition:leave-start="opacity-100 translate-y-0"
                         x-transition:leave-end="opacity-0 -translate-y-1"
                         class="mt-0.5 space-y-0.5 pl-4">
                        <a href="{{ route('reseller.packages.index') }}"
                           class="@if(request()->routeIs('reseller.packages.*')) border-l-indigo-600 bg-indigo-50 text-indigo-700 @else text-gray-600 hover:bg-gray-100 hover:text-gray-900 @endif group flex items-center gap-3 rounded-r-lg border-l-2 border-transparent px-3 py-2 text-sm font-medium transition-all duration-200">
                            <svg class="h-5 w-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                            </svg>
                            {{ __('Paket Internet') }}
                        </a>
                        <a href="{{ route('reseller.wallet.show') }}"
                           class="@if(request()->routeIs('reseller.wallet.*')) border-l-indigo-600 bg-indigo-50 text-indigo-700 @else text-gray-600 hover:bg-gray-100 hover:text-gray-900 @endif group flex items-center gap-3 rounded-r-lg border-l-2 border-transparent px-3 py-2 text-sm font-medium transition-all duration-200">
                            <svg class="h-5 w-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                            </svg>
                            {{ __('Wallet Saya') }}
                        </a>
                        <a href="{{ route('reseller.voucher-batches.index') }}"
                           class="@if(request()->routeIs('reseller.voucher-batches.*')) border-l-indigo-600 bg-indigo-50 text-indigo-700 @else text-gray-600 hover:bg-gray-100 hover:text-gray-900 @endif group flex items-center gap-3 rounded-r-lg border-l-2 border-transparent px-3 py-2 text-sm font-medium transition-all duration-200">
                            <svg class="h-5 w-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                            </svg>
                            {{ __('Voucher Batch') }}
                        </a>
                    </div>
                </div>
            @endif
        </nav>
    </aside>

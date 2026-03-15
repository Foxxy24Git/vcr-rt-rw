<x-dashboard-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <h2 class="text-lg font-semibold leading-tight text-gray-800 sm:text-xl">
                {{ $dashboardTitle ?? 'Dashboard Admin' }}
            </h2>
            <span class="rounded-xl border border-rootPrimary/30 bg-rootPink/20 px-3 py-1 text-xs font-medium text-rootPrimary">
                KPI Overview
            </span>
        </div>
    </x-slot>

    <div class="py-4 sm:py-6 md:py-8">
        <div class="mx-auto max-w-7xl space-y-4 px-3 sm:space-y-6 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 gap-3 sm:gap-4 md:grid-cols-2 lg:grid-cols-4">
                <article class="relative overflow-hidden rounded-xl border border-gray-200 bg-white p-4 shadow-sm transition-shadow duration-200 hover:shadow-md sm:rounded-2xl md:p-5">
                    <span class="absolute inset-y-0 left-0 w-1 bg-rootTeal"></span>

                    <p class="text-xs font-medium text-gray-500 sm:text-sm">Active Users</p>
                    <p class="mt-2 text-2xl font-bold tracking-tight text-gray-900 sm:mt-3 sm:text-3xl">{{ number_format((int) ($activeUsersCount ?? 0)) }}</p>
                </article>

                @foreach ($kpis as $kpi)
                    <article class="relative overflow-hidden rounded-xl border border-gray-200 bg-white p-4 shadow-sm transition-shadow duration-200 hover:shadow-md sm:rounded-2xl md:p-5">
                        <span class="absolute inset-y-0 left-0 w-1 {{ $kpi['accent'] }}"></span>

                        <p class="text-xs font-medium text-gray-500 sm:text-sm">{{ $kpi['label'] }}</p>
                        <p class="mt-2 text-xl font-semibold tracking-tight text-gray-900 sm:mt-3 sm:text-2xl">{{ $kpi['value'] }}</p>
                    </article>
                @endforeach
            </div>

            @if (($overloadedResellers ?? collect())->isNotEmpty())
                <section class="rounded-xl border border-red-300 bg-red-50 p-4 sm:rounded-2xl md:p-6">
                    <h3 class="text-sm font-semibold text-red-800 sm:text-base">&#9888; Reseller Overload Alert</h3>

                    <div class="mt-4 grid grid-cols-1 gap-3 md:grid-cols-2 xl:grid-cols-3">
                        @foreach ($overloadedResellers as $item)
                            <article class="rounded-lg border border-red-200 bg-white/80 p-3 transition-shadow duration-200 hover:shadow-sm sm:rounded-xl md:p-4">
                                <p class="truncate text-sm font-semibold text-gray-900">
                                    {{ $item->reseller?->name ?? 'Unknown Reseller' }}
                                </p>
                                <p class="mt-2 text-sm text-red-700">
                                    Active: <span class="font-bold">{{ number_format((int) $item->total_active) }}</span>
                                    / Threshold: <span class="font-bold">{{ number_format((int) ($resellerActiveThreshold ?? 0)) }}</span>
                                </p>
                                <p class="mt-1 text-xs font-medium text-red-600">Exceeds safe limit</p>
                            </article>
                        @endforeach
                    </div>
                </section>
            @endif

            <section class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm transition-shadow duration-200 hover:shadow-md sm:rounded-2xl md:p-5">
                <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                    <h3 class="text-sm font-semibold text-gray-900 sm:text-base">Active Users per Reseller</h3>
                    <span class="text-xs text-gray-400">Realtime</span>
                </div>

                @if (($activePerReseller ?? collect())->isEmpty())
                    <p class="mt-4 text-sm text-gray-500">No active users currently</p>
                @else
                    <div class="mt-4 grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-3">
                        @foreach ($activePerReseller as $item)
                            <article class="rounded-lg border border-gray-100 bg-white p-3 transition-shadow duration-200 hover:shadow-sm sm:rounded-xl md:p-4">
                                <p class="truncate text-sm font-medium text-gray-700">
                                    {{ $item->reseller?->name ?? 'Unknown Reseller' }}
                                </p>
                                <div class="mt-2 flex items-center gap-2">
                                    <span class="h-2 w-2 rounded-full bg-rootTeal"></span>
                                    <span class="text-lg font-bold text-rootTeal">{{ number_format((int) $item->total_active) }}</span>
                                </div>
                            </article>
                        @endforeach
                    </div>
                @endif
            </section>

            @php
                $utilization = (float) ($utilizationPercent ?? 0);
                $utilizationBarWidth = max(0, min(100, $utilization));
                $utilizationBarClass = $utilization < 60
                    ? 'bg-emerald-500'
                    : ($utilization <= 80 ? 'bg-amber-500' : 'bg-rose-500');
            @endphp

            <section class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm transition-shadow duration-200 hover:shadow-md sm:rounded-2xl md:p-5">
                <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                    <h3 class="text-sm font-semibold text-gray-900 sm:text-base">Network Utilization</h3>
                    <span class="text-xs text-gray-400">Estimated realtime</span>
                </div>

                <div class="mt-4 grid grid-cols-1 gap-3 md:grid-cols-3">
                    <article class="rounded-lg border border-gray-100 bg-gray-50 p-3 transition-shadow duration-200 hover:shadow-sm sm:rounded-xl md:p-4">
                        <p class="text-xs uppercase tracking-wide text-gray-500">Estimated Load</p>
                        <p class="mt-1 text-lg font-bold text-gray-900 sm:text-2xl">{{ number_format((float) ($estimatedMbps ?? 0), 2, ',', '.') }} Mbps</p>
                    </article>
                    <article class="rounded-lg border border-gray-100 bg-gray-50 p-3 transition-shadow duration-200 hover:shadow-sm sm:rounded-xl md:p-4">
                        <p class="text-xs uppercase tracking-wide text-gray-500">Total Capacity</p>
                        <p class="mt-1 text-lg font-bold text-gray-900 sm:text-2xl">{{ number_format((float) ($networkCapacityMbps ?? 0), 2, ',', '.') }} Mbps</p>
                    </article>
                    <article class="rounded-lg border border-gray-100 bg-gray-50 p-3 transition-shadow duration-200 hover:shadow-sm sm:rounded-xl md:p-4">
                        <p class="text-xs uppercase tracking-wide text-gray-500">Utilization</p>
                        <p class="mt-1 text-lg font-bold text-gray-900 sm:text-2xl">{{ number_format($utilization, 1, ',', '.') }}%</p>
                    </article>
                </div>

                <div class="mt-4">
                    <div class="h-3 w-full overflow-hidden rounded-full bg-gray-100">
                        <div class="h-3 rounded-full {{ $utilizationBarClass }}" style="width: {{ $utilizationBarWidth }}%"></div>
                    </div>
                </div>
            </section>

            <section class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm transition-shadow duration-200 hover:shadow-md sm:rounded-2xl md:p-5">
                <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                    <h3 class="text-sm font-semibold text-gray-900 sm:text-base">Active Users Trend (Last 24h)</h3>
                    <span class="text-xs text-gray-400">Snapshot setiap 5 menit</span>
                </div>

                <div class="mt-4 h-72">
                    <canvas id="activeUsersTrendChart" class="h-full w-full"></canvas>
                </div>
            </section>

            <section class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm">
                <div class="flex items-center justify-between">
                    <h3 class="text-base font-semibold text-gray-900">Top Resellers - Revenue Today</h3>
                    <span class="text-xs text-gray-400">Top 5</span>
                </div>

                @if (($topResellersToday ?? collect())->isEmpty())
                    <p class="mt-4 text-sm text-gray-500">No revenue recorded today</p>
                @else
                    <div class="mt-4 grid grid-cols-1 gap-3 md:grid-cols-2 xl:grid-cols-3">
                        @foreach ($topResellersToday as $index => $item)
                            @php
                                $isTopOne = $index === 0;
                            @endphp
                            <article class="rounded-xl border p-4 {{ $isTopOne ? 'border-rootPrimary bg-rootPrimary/5' : 'border-gray-100 bg-white' }}">
                                <div class="flex items-start justify-between gap-3">
                                    <p class="text-sm font-semibold {{ $isTopOne ? 'text-rootPrimary' : 'text-gray-500' }}">
                                        #{{ $index + 1 }}
                                    </p>
                                    <p class="text-right text-sm font-bold {{ $isTopOne ? 'text-rootPrimary' : 'text-gray-800' }}">
                                        Rp {{ number_format((float) $item->total_revenue, 2, ',', '.') }}
                                    </p>
                                </div>

                                <p class="mt-2 truncate text-sm font-medium text-gray-800">
                                    {{ $item->reseller?->name ?? 'Unknown Reseller' }}
                                </p>
                            </article>
                        @endforeach
                    </div>
                @endif
            </section>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.6/dist/chart.umd.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const canvas = document.getElementById('activeUsersTrendChart');

            if (!canvas || typeof window.Chart === 'undefined') {
                return;
            }

            const trend = JSON.parse(@json($activeUsersTrendJson ?? '{"labels":[],"values":[]}'));
            const labels = Array.isArray(trend.labels) ? trend.labels : [];
            const values = Array.isArray(trend.values) ? trend.values : [];

            new window.Chart(canvas, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Active Users',
                        data: values,
                        borderColor: '#46c8cd',
                        backgroundColor: 'rgba(70, 200, 205, 0.16)',
                        borderWidth: 2,
                        pointRadius: 2,
                        pointHoverRadius: 4,
                        tension: 0.25,
                        fill: true
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        x: {
                            grid: {
                                color: 'rgba(17, 24, 39, 0.06)'
                            },
                            ticks: {
                                color: '#6b7280',
                                maxTicksLimit: 12
                            }
                        },
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: 'rgba(17, 24, 39, 0.06)'
                            },
                            ticks: {
                                color: '#6b7280',
                                precision: 0
                            }
                        }
                    }
                }
            });
        });
    </script>
</x-dashboard-layout>

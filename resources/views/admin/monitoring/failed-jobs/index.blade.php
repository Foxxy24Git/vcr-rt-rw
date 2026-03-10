<x-dashboard-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            Monitoring Failed Jobs
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-7xl space-y-4 sm:px-6 lg:px-8">
            @if (session('status'))
                <div class="rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
                    {{ session('status') }}
                </div>
            @endif

            @if (session('error'))
                <div class="rounded-lg border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700">
                    {{ session('error') }}
                </div>
            @endif

            <div class="overflow-hidden rounded-xl bg-white shadow-sm">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left font-semibold text-gray-700">ID</th>
                                <th class="px-4 py-3 text-left font-semibold text-gray-700">Connection</th>
                                <th class="px-4 py-3 text-left font-semibold text-gray-700">Queue</th>
                                <th class="px-4 py-3 text-left font-semibold text-gray-700">Exception</th>
                                <th class="px-4 py-3 text-left font-semibold text-gray-700">Failed At</th>
                                <th class="px-4 py-3 text-right font-semibold text-gray-700">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 bg-white">
                            @forelse ($failedJobs as $job)
                                <tr>
                                    <td class="px-4 py-3 font-medium text-gray-900">{{ $job->id }}</td>
                                    <td class="px-4 py-3 text-gray-700">{{ $job->connection }}</td>
                                    <td class="px-4 py-3 text-gray-700">{{ $job->queue }}</td>
                                    <td class="px-4 py-3 text-gray-700" title="{{ $job->exception }}">
                                        {{ \Illuminate\Support\Str::limit($job->exception, 120) }}
                                    </td>
                                    <td class="px-4 py-3 text-gray-700">{{ \Illuminate\Support\Carbon::parse($job->failed_at)->format('d-m-Y H:i:s') }}</td>
                                    <td class="px-4 py-3 text-right">
                                        <form method="POST" action="{{ route('admin.failed-jobs.retry', $job->id) }}">
                                            @csrf
                                            <button type="submit" class="rounded-xl bg-rootPrimary px-3 py-1.5 text-xs font-medium text-white hover:bg-rootIndigo">
                                                Retry
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-4 py-8 text-center text-gray-500">
                                        Tidak ada failed job.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="border-t border-gray-100 px-4 py-3">
                    {{ $failedJobs->links() }}
                </div>
            </div>
        </div>
    </div>
</x-dashboard-layout>

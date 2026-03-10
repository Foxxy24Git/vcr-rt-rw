<x-dashboard-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-3">
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Detail Batch: {{ $voucherBatch->batch_code }}
            </h2>

            <div class="flex items-center gap-2">
                <a
                    href="{{ route('reseller.voucher-batches.card', $voucherBatch) }}"
                    target="_blank"
                    rel="noopener"
                    class="rounded-xl border border-rootPrimary px-4 py-2 text-sm font-medium text-rootPrimary hover:bg-rootPink/20"
                >
                    Card Mode
                </a>

                <a
                    href="{{ route('reseller.voucher-batches.print', $voucherBatch) }}"
                    target="_blank"
                    rel="noopener"
                    class="rounded-xl border border-rootPrimary px-4 py-2 text-sm font-medium text-rootPrimary hover:bg-rootPink/20"
                >
                    Print
                </a>

                <a
                    href="{{ route('reseller.voucher-batches.thermal', $voucherBatch) }}"
                    target="_blank"
                    rel="noopener"
                    class="rounded-xl bg-rootPrimary px-4 py-2 text-sm font-medium text-white hover:bg-rootIndigo"
                >
                    Thermal Print
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-7xl space-y-4 sm:px-6 lg:px-8">
            @if (session('status'))
                <div class="rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
                    {{ session('status') }}
                </div>
            @endif

            <div class="relative overflow-hidden rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
                <span class="absolute inset-y-0 left-0 w-1 bg-rootPrimary"></span>
                <div class="grid grid-cols-1 gap-4 md:grid-cols-4">
                    <div>
                        <p class="text-xs uppercase tracking-wide text-gray-500">Total Vouchers</p>
                        <p class="mt-1 text-xl font-semibold text-gray-900">{{ number_format($voucherBatch->qty_generated) }}</p>
                    </div>

                    <div>
                        <p class="text-xs uppercase tracking-wide text-gray-500">Total Amount</p>
                        <p class="mt-1 text-xl font-semibold text-rootPrimary">
                            Rp {{ number_format((float) $voucherBatch->total_cost, 2, ',', '.') }}
                        </p>
                    </div>

                    <div>
                        <p class="text-xs uppercase tracking-wide text-gray-500">Status</p>
                        <x-status-badge :status="$voucherBatch->status" class="mt-1" />
                    </div>

                    <div>
                        <p class="text-xs uppercase tracking-wide text-gray-500">Generated At</p>
                        <p class="mt-1 text-sm font-medium text-gray-900">
                            {{ $voucherBatch->generated_at?->format('d-m-Y H:i') ?? '-' }}
                        </p>
                    </div>
                </div>
            </div>

            <div class="overflow-hidden rounded-xl bg-white shadow-sm">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left font-semibold text-gray-700">Code</th>
                                <th class="px-4 py-3 text-left font-semibold text-gray-700">Username</th>
                                <th class="px-4 py-3 text-left font-semibold text-gray-700">Password</th>
                                <th class="px-4 py-3 text-left font-semibold text-gray-700">Status</th>
                                <th class="px-4 py-3 text-left font-semibold text-gray-700">Cost Price</th>
                                <th class="px-4 py-3 text-left font-semibold text-gray-700">Copy</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 bg-white">
                            @forelse ($vouchers as $voucher)
                                @php
                                    $username = (string) ($voucher->username ?? '');
                                    $password = (string) ($voucher->password ?? '');
                                    $credential = $username !== '' || $password !== '' ? "{$username}:{$password}" : '';
                                @endphp
                                <tr>
                                    <td class="px-4 py-3 font-mono text-xs text-gray-900">{{ $voucher->code }}</td>
                                    <td class="px-4 py-3">
                                        <p class="font-mono text-lg tracking-wider text-gray-900">{{ $username !== '' ? $username : '-' }}</p>
                                    </td>
                                    <td class="px-4 py-3 font-mono text-sm tracking-wide text-gray-700">{{ $password !== '' ? $password : '-' }}</td>
                                    <td class="px-4 py-3">
                                        <x-status-badge :status="$voucher->status" />
                                    </td>
                                    <td class="px-4 py-3 text-gray-700">Rp {{ number_format((float) $voucher->cost_price, 2, ',', '.') }}</td>
                                    <td class="px-4 py-3">
                                        <div class="flex flex-wrap gap-1.5">
                                            <button
                                                type="button"
                                                class="rounded-lg border border-rootPrimary/30 px-2 py-1 text-[11px] font-medium text-rootPrimary hover:bg-rootPink/20"
                                                data-copy="{{ $username }}"
                                            >
                                                Username
                                            </button>
                                            <button
                                                type="button"
                                                class="rounded-lg border border-rootPrimary/30 px-2 py-1 text-[11px] font-medium text-rootPrimary hover:bg-rootPink/20"
                                                data-copy="{{ $password }}"
                                            >
                                                Password
                                            </button>
                                            <button
                                                type="button"
                                                class="rounded-lg border border-rootPrimary/30 px-2 py-1 text-[11px] font-medium text-rootPrimary hover:bg-rootPink/20"
                                                data-copy="{{ $credential }}"
                                            >
                                                User:Pass
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-4 py-8 text-center text-gray-500">Tidak ada voucher.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="border-t border-gray-100 px-4 py-3">
                    {{ $vouchers->links() }}
                </div>
            </div>
        </div>
    </div>

    <div id="copy-toast" class="pointer-events-none fixed right-6 top-6 z-50 hidden rounded-xl bg-gray-900 px-3 py-2 text-xs font-semibold text-white shadow-lg">
        Copied!
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const toast = document.getElementById('copy-toast');
            let toastTimer = null;

            const showToast = () => {
                if (!toast) {
                    return;
                }

                toast.classList.remove('hidden');

                if (toastTimer) {
                    clearTimeout(toastTimer);
                }

                toastTimer = setTimeout(() => {
                    toast.classList.add('hidden');
                }, 1200);
            };

            const fallbackCopy = (text) => {
                const textarea = document.createElement('textarea');
                textarea.value = text;
                textarea.setAttribute('readonly', '');
                textarea.style.position = 'absolute';
                textarea.style.left = '-9999px';
                document.body.appendChild(textarea);
                textarea.select();
                document.execCommand('copy');
                document.body.removeChild(textarea);
            };

            document.querySelectorAll('[data-copy]').forEach((button) => {
                button.addEventListener('click', async () => {
                    const value = button.getAttribute('data-copy') || '';

                    if (value === '') {
                        return;
                    }

                    try {
                        await navigator.clipboard.writeText(value);
                    } catch (error) {
                        fallbackCopy(value);
                    }

                    showToast();
                });
            });
        });
    </script>
</x-dashboard-layout>

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="robots" content="noindex">
        <title>Voucher Card {{ $voucherBatch->batch_code }}</title>
        @vite(['resources/css/app.css'])
    </head>
    <body class="min-h-screen bg-white">
        @php
            $validityUnitMap = [
                'hour' => 'Jam',
                'day' => 'Hari',
                'month' => 'Bulan',
            ];
            $validityUnit = $validityUnitMap[$voucherBatch->package->validity_unit] ?? ucfirst((string) $voucherBatch->package->validity_unit);
            $validityLabel = $voucherBatch->package->validity_value.' '.$validityUnit;
        @endphp

        <main class="mx-auto flex max-w-5xl flex-col items-center gap-4 px-4 py-6">
            <div class="w-full max-w-[380px]">
                <a href="{{ route('reseller.voucher-batches.show', $voucherBatch) }}" class="inline-flex rounded-xl border border-rootPrimary px-3 py-1.5 text-xs font-medium text-rootPrimary hover:bg-rootPink/20">
                    Kembali
                </a>
            </div>

            @forelse ($vouchers as $voucher)
                @php
                    $shareMessage = "Voucher ROOT.NET\n".
                        "Paket: {$voucherBatch->package->name}\n".
                        "User: ".($voucher->username ?? '-')."\n".
                        "Pass: ".($voucher->password ?? '-')."\n".
                        "Berlaku: {$validityLabel}";
                    $whatsappShareUrl = 'https://wa.me/?text='.rawurlencode($shareMessage);
                @endphp

                <section class="w-full max-w-[380px] rounded-2xl border border-rootPrimary/25 bg-white p-5 shadow-sm">
                    <div class="flex items-start justify-between">
                        <span class="inline-flex rounded-full border border-rootPrimary/30 px-2.5 py-1 text-[11px] font-semibold uppercase tracking-wide text-rootPrimary">
                            Voucher
                        </span>
                        <span class="text-xs font-medium text-gray-600">{{ $validityLabel }}</span>
                    </div>

                    <div class="mt-4 space-y-1 text-center">
                        <p class="text-xs font-semibold uppercase tracking-[0.25em] text-rootPrimary">ROOT.NET</p>
                        <p class="text-sm font-semibold text-gray-900">{{ $voucherBatch->package->name }}</p>
                    </div>

                    <div class="my-4 space-y-3">
                        <div class="border-t border-gray-200 pt-3">
                            <p class="text-[10px] font-semibold uppercase tracking-widest text-gray-500">Username</p>
                            <p class="mt-1 font-mono text-2xl font-bold leading-tight tracking-wider text-gray-900">{{ $voucher->username ?? '-' }}</p>
                        </div>

                        <div class="border-t border-gray-200 pt-3">
                            <p class="text-[10px] font-semibold uppercase tracking-widest text-gray-500">Password</p>
                            <p class="mt-1 font-mono text-2xl font-bold leading-tight tracking-wider text-gray-900">{{ $voucher->password ?? '-' }}</p>
                        </div>
                    </div>

                    <div class="border-t border-gray-200 pt-3">
                        <p class="text-xs text-gray-600">Berlaku: {{ $validityLabel }}</p>
                        <p class="mt-0.5 text-xs text-gray-500">Akses Wifi Root Net</p>

                        <a
                            href="{{ $whatsappShareUrl }}"
                            target="_blank"
                            rel="noopener"
                            class="mt-3 inline-flex w-full items-center justify-center rounded-xl bg-rootPrimary px-3 py-2 text-sm font-semibold text-white hover:bg-rootIndigo"
                        >
                            Bagikan Voucher
                        </a>
                    </div>
                </section>
            @empty
                <section class="w-full max-w-[380px] rounded-2xl border border-rootPrimary/25 bg-white p-6 text-center shadow-sm">
                    <p class="text-sm text-gray-600">Tidak ada voucher untuk ditampilkan.</p>
                </section>
            @endforelse
        </main>
    </body>
</html>

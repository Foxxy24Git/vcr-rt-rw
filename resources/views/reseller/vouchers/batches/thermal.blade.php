<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="robots" content="noindex">
        <title>Thermal Voucher Batch {{ $voucherBatch->batch_code }}</title>
        @vite(['resources/css/app.css'])
        <style>
            body {
                margin: 0;
                background: #fff;
                color: #000;
                font-family: ui-sans-serif, system-ui, -apple-system, Segoe UI, sans-serif;
            }

            .thermal-page {
                width: 58mm;
                margin: 0 auto;
                padding: 2.5mm 1.5mm;
            }

            .voucher-section {
                width: 100%;
                text-align: center;
                padding: 1.5mm 0;
                box-shadow: none;
                border: 0;
            }

            .ticket-divider {
                border-top: 1px solid #000;
                margin: 2mm 0;
            }

            .voucher-separator {
                border-top: 1px dashed #000;
                margin: 3mm 0;
            }

            @media print {
                body {
                    width: 58mm;
                    margin: 0;
                    background: #fff !important;
                }

                .thermal-page {
                    width: 58mm;
                    margin: 0;
                    padding: 0;
                }

                .no-print {
                    display: none !important;
                }

                .voucher-section,
                .voucher-separator,
                .ticket-divider {
                    box-shadow: none !important;
                    background: transparent !important;
                }
            }
        </style>
    </head>
    <body>
        <div class="thermal-page">
            @forelse ($vouchers as $voucher)
                @php
                    $validity = $voucherBatch->package->validity_value.' '.$voucherBatch->package->validity_unit;
                    $shareMessage = "Voucher ROOT.NET\n".
                        "Paket: {$voucherBatch->package->name}\n".
                        "User: ".($voucher->username ?? '-')."\n".
                        "Pass: ".($voucher->password ?? '-')."\n".
                        "Valid: {$validity}";
                    $encodedShareMessage = rawurlencode($shareMessage);
                @endphp
                <section class="voucher-section" style="page-break-after: always;">
                    <div class="no-print mb-2 flex justify-center gap-1.5">
                        <button type="button" onclick="window.print()" class="rounded-md border border-black px-2 py-1 text-[10px] font-semibold text-black">
                            Print
                        </button>
                        <a
                            href="https://wa.me/?text={{ $encodedShareMessage }}"
                            target="_blank"
                            rel="noopener"
                            class="rounded-md border border-black px-2 py-1 text-[10px] font-semibold text-black"
                        >
                            WhatsApp
                        </a>
                        <a
                            href="https://t.me/share/url?text={{ $encodedShareMessage }}"
                            target="_blank"
                            rel="noopener"
                            class="rounded-md border border-black px-2 py-1 text-[10px] font-semibold text-black"
                        >
                            Telegram
                        </a>
                    </div>

                    <p class="text-[10px] font-semibold tracking-[0.2em]">ROOT.NET</p>
                    <div class="ticket-divider"></div>

                    <div>
                        <p class="text-[10px] uppercase">Package</p>
                        <p class="text-[12px] font-semibold">{{ $voucherBatch->package->name }}</p>
                    </div>
                    <div class="ticket-divider"></div>

                    <div>
                        <p class="text-[10px] uppercase">Username</p>
                        <p class="font-mono text-[24px] font-bold leading-tight tracking-wider">{{ $voucher->username ?? '-' }}</p>
                    </div>
                    <div class="ticket-divider"></div>

                    <div>
                        <p class="text-[10px] uppercase">Password</p>
                        <p class="font-mono text-[24px] font-bold leading-tight tracking-wider">{{ $voucher->password ?? '-' }}</p>
                    </div>
                    <div class="ticket-divider"></div>

                    <div class="space-y-0.5 text-[10px]">
                        <p>Validity: {{ $validity }}</p>
                        <p>Generated: {{ $voucher->generated_at?->format('d-m-Y H:i') ?? '-' }}</p>
                    </div>

                    @if (! $loop->last)
                        <div class="voucher-separator"></div>
                    @endif
                </section>
            @empty
                <section class="voucher-section">
                    <p class="text-[11px]">Tidak ada voucher untuk dicetak.</p>
                </section>
            @endforelse
        </div>
    </body>
</html>

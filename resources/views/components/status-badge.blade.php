@props(['status'])

@php
    $rawStatus = trim((string) ($status ?? ''));
    $normalizedStatus = strtolower($rawStatus);

    $statusMap = [
        'ready' => ['label' => 'READY', 'class' => 'bg-emerald-100 text-emerald-700'],
        'generated' => ['label' => 'GENERATED', 'class' => 'bg-rootTeal/20 text-rootTeal'],
        'failed' => ['label' => 'FAILED', 'class' => 'bg-rose-100 text-rose-700'],
        'pending' => ['label' => 'PENDING', 'class' => 'bg-amber-100 text-amber-700'],
        'used' => ['label' => 'USED', 'class' => 'bg-rootTeal/20 text-rootTeal'],
        'active' => ['label' => 'ACTIVE', 'class' => 'bg-rootTeal text-white font-semibold shadow-sm tracking-wide'],
        'expired' => ['label' => 'EXPIRED', 'class' => 'bg-rose-100 text-rose-700'],
        'disabled' => ['label' => 'DISABLED', 'class' => 'bg-rose-100 text-rose-700'],
        'aktif' => ['label' => 'Aktif', 'class' => 'bg-emerald-100 text-emerald-700'],
        'nonaktif' => ['label' => 'Nonaktif', 'class' => 'bg-rose-100 text-rose-700'],
        'inactive' => ['label' => 'Nonaktif', 'class' => 'bg-rose-100 text-rose-700'],

        // Additional statuses for compatibility.
        'success' => ['label' => 'SUCCESS', 'class' => 'bg-emerald-100 text-emerald-700'],
        'simulated' => ['label' => 'SIMULATED', 'class' => 'bg-rootPink/30 text-rootPrimary'],
        'paid' => ['label' => 'PAID', 'class' => 'bg-rootTeal/20 text-rootTeal'],
        'draft' => ['label' => 'DRAFT', 'class' => 'bg-amber-100 text-amber-700'],
        'cancelled' => ['label' => 'CANCELLED', 'class' => 'bg-gray-100 text-gray-700'],
        'sold' => ['label' => 'SOLD', 'class' => 'bg-rootTeal/20 text-rootTeal'],
        'revoked' => ['label' => 'REVOKED', 'class' => 'bg-rose-100 text-rose-700'],
    ];

    $resolvedStatus = $statusMap[$normalizedStatus] ?? [
        'label' => $rawStatus !== '' ? strtoupper($rawStatus) : '-',
        'class' => 'bg-gray-100 text-gray-700',
    ];

    $baseClasses = 'inline-flex rounded-full px-3 py-1 text-xs font-semibold';
@endphp

<span {{ $attributes->merge(['class' => $baseClasses.' '.$resolvedStatus['class']]) }}>
    {{ $resolvedStatus['label'] }}
</span>

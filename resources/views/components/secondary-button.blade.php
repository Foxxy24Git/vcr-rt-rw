<button {{ $attributes->merge(['type' => 'button', 'class' => 'inline-flex w-full items-center justify-center px-4 py-2 rounded-xl border border-rootPrimary bg-white font-semibold text-xs uppercase tracking-widest text-rootPrimary shadow-sm transition duration-200 hover:bg-rootPink/20 hover:shadow-sm focus:outline-none focus:ring-2 focus:ring-rootTeal focus:ring-offset-2 disabled:opacity-25 md:w-auto']) }}>
    {{ $slot }}
</button>

<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center px-4 py-2 bg-rootPrimary border border-transparent rounded-xl font-semibold text-xs text-white uppercase tracking-widest hover:bg-rootIndigo focus:bg-rootIndigo active:bg-rootPrimary/90 focus:outline-none focus:ring-2 focus:ring-rootTeal focus:ring-offset-2 transition ease-in-out duration-150']) }}>
    {{ $slot }}
</button>

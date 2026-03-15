<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex w-full items-center justify-center px-4 py-2 bg-rootPrimary border border-transparent rounded-xl font-semibold text-xs text-white uppercase tracking-widest shadow-sm transition duration-200 hover:bg-rootIndigo hover:shadow-md focus:bg-rootIndigo focus:outline-none focus:ring-2 focus:ring-rootTeal focus:ring-offset-2 active:bg-rootPrimary/90 md:w-auto']) }}>
    {{ $slot }}
</button>

<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex w-full items-center justify-center px-4 py-2 rounded-xl border border-red-200 bg-red-50 font-semibold text-xs uppercase tracking-widest text-red-700 shadow-sm transition duration-200 hover:bg-red-100 hover:shadow-sm focus:outline-none focus:ring-2 focus:ring-red-200 focus:ring-offset-2 active:bg-red-200 md:w-auto']) }}>
    {{ $slot }}
</button>

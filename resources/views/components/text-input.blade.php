@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'w-full rounded-xl border-gray-300 shadow-sm transition duration-200 focus:border-rootPrimary focus:ring-rootTeal sm:text-sm']) }}>

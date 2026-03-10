@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'border-gray-300 focus:border-rootPrimary focus:ring-rootTeal rounded-xl shadow-sm']) }}>

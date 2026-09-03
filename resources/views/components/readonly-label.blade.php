@props(['value'])

<label {{ $attributes->merge(['class' => 'block text-xs font-semibold uppercase text-gray-500 dark:text-gray-400 mb-1']) }}>
    {{ $value ?? $slot }}
</label>

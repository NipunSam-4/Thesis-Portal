<button
    {{ $attributes->merge([
        'type' => 'submit',
        'class' => '
            button
            inline-flex items-center justify-center
            px-6 py-3
            text-sm font-semibold
            focus:outline-none'
    ]) }}
>
    {{ $slot }}
</button>

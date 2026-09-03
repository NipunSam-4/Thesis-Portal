@props(['type' => 'text', 'value' => ''])

<input type="{{ $type }}" 
       value="{{ $value }}" 
       {{ $attributes->merge(['class' => 'w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:[color-scheme:dark] focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 text-sm shadow-xs transition']) }}>

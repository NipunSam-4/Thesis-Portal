@props(['value' => ''])

<input type="text" 
       value="{{ $value }}" 
       readonly 
       {{ $attributes->merge(['class' => 'w-full bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200 rounded-lg border-gray-300 dark:border-gray-600 cursor-not-allowed text-sm']) }}>

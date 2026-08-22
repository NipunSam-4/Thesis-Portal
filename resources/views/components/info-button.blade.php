@props(['text' => 'Course credits earned including coursework, seminars, and research credits.'])

<span x-data="{ open: false }" class="relative flex items-center shrink-0 ml-1 align-middle">
    <button type="button" 
            @mouseenter="open = true" 
            @mouseleave="open = false" 
            @click.prevent="open = !open" 
            class="text-indigo-500 hover:text-indigo-600 dark:text-indigo-400 dark:hover:text-indigo-300 rounded-full hover:bg-indigo-50 dark:hover:bg-indigo-950/50 transition focus:outline-none shrink-0"
            aria-label="More Information">
        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
        </svg>
    </button>
    <span x-show="open" 
          x-cloak 
          x-transition:enter="transition ease-out duration-150"
          x-transition:enter-start="opacity-0 transform scale-95"
          x-transition:enter-end="opacity-100 transform scale-100"
          x-transition:leave="transition ease-in duration-100"
          x-transition:leave-start="opacity-100 transform scale-100"
          x-transition:leave-end="opacity-0 transform scale-95"
          class="absolute left-1/2 -translate-x-1/2 bottom-full mb-2 w-64 p-2.5 bg-gray-900 dark:bg-gray-700 text-white text-xs font-normal normal-case rounded-xl shadow-xl z-50 leading-relaxed text-left pointer-events-none border border-gray-700 dark:border-gray-600">
        {{ $text }}
        <span class="absolute top-full left-1/2 -translate-x-1/2 -mt-1 border-4 border-transparent border-t-gray-900 dark:border-t-gray-700 block"></span>
    </span>
</span>

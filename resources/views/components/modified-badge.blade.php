@props([
    'oldValue' => null,
    'newValue' => null,
    'text' => null
])

<span x-data="{ open: false }" class="relative inline-flex items-center align-middle shrink-0 self-center">
    <!-- Modified Badge (Circular Amber/Yellow in both themes) -->
    <span @mouseenter="open = true" 
          @mouseleave="open = false" 
          @click.prevent="open = !open" 
          class="cursor-pointer select-none w-4 h-4 inline-flex items-center justify-center rounded-full text-[10px] font-bold leading-none bg-amber-100 text-amber-900 border border-amber-300 hover:bg-amber-200 dark:bg-amber-950/80 dark:text-yellow-300 dark:border-yellow-600/50 dark:hover:bg-amber-900 transition shadow-xs shrink-0 self-center">
        M
    </span>
    
    <!-- Hover Popover Tooltip (Light in Light Mode, Dark Gray as in Form in Dark Mode) -->
    <span x-show="open" 
          x-cloak 
          x-transition:enter="transition ease-out duration-150"
          x-transition:enter-start="opacity-0 transform scale-95"
          x-transition:enter-end="opacity-100 transform scale-100"
          x-transition:leave="transition ease-in duration-100"
          x-transition:leave-start="opacity-100 transform scale-100"
          x-transition:leave-end="opacity-0 transform scale-95"
          class="absolute left-1/2 -translate-x-1/2 bottom-full mb-2 w-max max-w-xs sm:max-w-sm p-3 bg-white text-gray-800 border border-gray-200 shadow-xl dark:bg-gray-900 dark:text-gray-100 dark:border-gray-700 dark:shadow-2xl text-xs font-normal normal-case rounded-xl z-50 leading-relaxed text-left pointer-events-none">
        
        <div class="space-y-1.5">
            <div class="text-[10px] uppercase font-bold text-amber-700 dark:text-yellow-400 tracking-wider flex items-center gap-1.5">
                <svg class="w-3.5 h-3.5 shrink-0 text-amber-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                Modified by Main Supervisor
            </div>
            @if($text)
                <div class="text-gray-700 dark:text-gray-200">{{ $text }}</div>
            @elseif($oldValue !== null && $newValue !== null)
                <div class="text-gray-700 dark:text-gray-200">
                    Value has been modified from <span class="font-semibold text-rose-600 dark:text-rose-400">"{{ $oldValue }}"</span> to <span class="font-bold text-emerald-600 dark:text-emerald-400">"{{ $newValue }}"</span>.
                </div>
            @elseif($newValue !== null)
                <div class="text-gray-700 dark:text-gray-200">
                    Value changed to <span class="font-bold text-emerald-600 dark:text-emerald-400">"{{ $newValue }}"</span>.
                </div>
            @endif
        </div>
        
        <!-- Tooltip Downward Arrow -->
        <span class="absolute top-full left-1/2 -translate-x-1/2 -mt-1 border-4 border-transparent border-t-white dark:border-t-gray-900 block"></span>
    </span>
</span>

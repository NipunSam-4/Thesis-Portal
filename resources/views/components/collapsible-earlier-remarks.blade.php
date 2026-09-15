@props([
    'defaultOpen' => false,
    'title' => 'Earlier Authority Recommendations & Remarks',
])

<div x-data="{ isExpanded: {{ $defaultOpen ? 'true' : 'false' }} }" class="space-y-4">
    <!-- Collapsible Toggle Bar -->
    <button type="button" 
            @click="isExpanded = !isExpanded" 
            class="w-full flex items-center justify-between p-3 rounded-lg border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700/50 hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-200 text-sm font-semibold transition select-none">
        <span class="flex items-center gap-2">
            <span>{{ $title }}</span>
        </span>
        <div class="flex items-center gap-1.5 text-xs text-gray-500 dark:text-gray-400 font-medium">
            <span x-text="isExpanded ? 'Collapse' : 'Expand'">{{ $defaultOpen ? 'Collapse' : 'Expand' }}</span>
            <svg class="w-4 h-4 transform transition-transform duration-200" 
                 :class="{ 'rotate-180': isExpanded }" 
                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
            </svg>
        </div>
    </button>

    <!-- Collapsible Content -->
    <div x-show="isExpanded" 
         x-collapse 
         x-cloak 
         class="space-y-4">
        {{ $slot }}
    </div>
</div>

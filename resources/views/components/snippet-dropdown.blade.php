@props([
    'target' => 'confidentialRemark',
    'formType' => 'all',
    'role' => 'all',
    'snippets' => null,
])

@php
    $snippetList = $snippets ?? \App\Models\CommentSnippet::forFormAndRole($formType, $role)->get();
@endphp

@if($snippetList->isNotEmpty())
    <div class="relative inline-block text-left" x-data="{ open: false }" @click.outside="open = false" @keydown.escape.window="open = false">
        <button type="button" 
                @click="open = !open" 
                class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700/80 shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-all duration-150"
                aria-expanded="false"
                :aria-expanded="open.toString()">
            <svg class="w-3.5 h-3.5 text-gray-500 dark:text-gray-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path>
            </svg>
            <span>Insert snippet</span>
            <svg class="w-3 h-3 text-gray-400 transition-transform duration-150 shrink-0" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
            </svg>
        </button>

        <div x-show="open" 
             x-transition:enter="transition ease-out duration-100" 
             x-transition:enter-start="transform opacity-0 scale-95" 
             x-transition:enter-end="transform opacity-100 scale-100" 
             x-transition:leave="transition ease-in duration-75" 
             x-transition:leave-start="transform opacity-100 scale-100" 
             x-transition:leave-end="transform opacity-0 scale-95" 
             class="absolute left-0 mt-1.5 w-72 sm:w-80 max-h-64 overflow-y-auto z-50 bg-white dark:bg-gray-800 rounded-xl shadow-xl border border-gray-200 dark:border-gray-700 py-1.5 divide-y divide-gray-100 dark:divide-gray-700/60"
             style="display: none;">
            @foreach($snippetList as $snippet)
                <button type="button" 
                        @click="{{ $target }} = ({{ $target }} && {{ $target }}.trim() ? ({{ $target }}.trim() + '\n\n' + @js($snippet->content)) : @js($snippet->content)); open = false;"
                        class="w-full text-left px-3.5 py-2.5 hover:bg-indigo-50/70 dark:hover:bg-indigo-950/40 text-xs text-gray-700 dark:text-gray-300 transition-colors flex flex-col group">
                    <span class="font-medium text-gray-900 dark:text-gray-100 group-hover:text-indigo-600 dark:group-hover:text-indigo-300 leading-relaxed whitespace-pre-wrap">{{ $snippet->content }}</span>
                </button>
            @endforeach
        </div>
    </div>
@endif

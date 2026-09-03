@props([
    'text' => null,
    'fallback' => 'Comment not provided',
    'role' => null,
    'borderClass' => null,
])

@php
    $roleBorders = [
        'main_supervisor'    => 'border-indigo-200 dark:border-indigo-800/70',
        'co_supervisor'      => 'border-blue-200 dark:border-blue-800/70',
        'pspc'               => 'border-cyan-200 dark:border-cyan-800/70',
        'dpgc'               => 'border-teal-200 dark:border-teal-800/70',
        'hod'                => 'border-sky-200 dark:border-sky-800/70',
        'academic_office'    => 'border-violet-200 dark:border-violet-800/70',
        'doaa'               => 'border-purple-200 dark:border-purple-800/70',
        'dr'                 => 'border-fuchsia-200 dark:border-fuchsia-800/70',
        'senate'             => 'border-pink-200 dark:border-pink-800/70',
        'senate_chairperson' => 'border-slate-200 dark:border-slate-800/70',
    ];

    $finalBorder = $borderClass ?? ($role ? ($roleBorders[$role] ?? 'border-gray-200 dark:border-gray-700') : 'border-gray-200 dark:border-gray-700');
    $trimmed = trim($text ?? '');
@endphp

@if($trimmed !== '')
    <div class="min-w-0 max-w-full">
        <p class="m-0 text-xs text-gray-800 dark:text-gray-100 bg-white/95 dark:bg-gray-900/70 py-1.5 px-3 rounded-md border {{ $finalBorder }} whitespace-pre-wrap break-words [overflow-wrap:anywhere] leading-snug shadow-xs">{{ $trimmed }}</p>
    </div>
@else
    <div class="min-w-0 max-w-full">
        <div class="w-full flex items-center gap-2 text-xs text-gray-500 dark:text-gray-400 bg-gray-50/80 dark:bg-gray-900/40 py-1.5 px-3 rounded-md border border-dashed border-gray-300 dark:border-gray-700/60 shadow-xs">
            <svg class="w-3.5 h-3.5 text-gray-400 dark:text-gray-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 4.418 9 8z"></path>
            </svg>
            <span class="italic font-normal">{{ $fallback }}</span>
        </div>
    </div>
@endif
@props([
    'role' => 'main_supervisor',
    'title' => null,
])

@php
    $themes = [
        'main_supervisor'    => 'p-4 bg-indigo-50/70 dark:bg-indigo-950/40 border-l-4 border-indigo-500 rounded-xl space-y-2',
        'co_supervisor'      => 'p-4 bg-blue-50/70 dark:bg-blue-950/40 border-l-4 border-blue-500 rounded-xl space-y-2',
        'pspc'               => 'p-4 bg-cyan-50/70 dark:bg-cyan-950/40 border-l-4 border-cyan-500 rounded-xl space-y-2',
        'dpgc'               => 'p-4 bg-teal-50/70 dark:bg-teal-950/40 border-l-4 border-teal-500 rounded-xl space-y-2',
        'hod'                => 'p-4 bg-sky-50/70 dark:bg-sky-950/40 border-l-4 border-sky-500 rounded-xl space-y-2',
        'academic_office'    => 'p-4 bg-violet-50/70 dark:bg-violet-950/40 border-l-4 border-violet-500 rounded-xl space-y-2',
        'doaa'               => 'p-4 bg-purple-50/70 dark:bg-purple-950/40 border-l-4 border-purple-500 rounded-xl space-y-2',
        'dr'                 => 'p-4 bg-fuchsia-50/70 dark:bg-fuchsia-950/40 border-l-4 border-fuchsia-500 rounded-xl space-y-2',
        'senate'             => 'p-4 bg-pink-50/70 dark:bg-pink-950/40 border-l-4 border-pink-500 rounded-xl space-y-2',
        'senate_chairperson' => 'p-4 bg-slate-50/70 dark:bg-slate-950/40 border-l-4 border-slate-500 rounded-xl space-y-2',
    ];

    $titleColors = [
        'main_supervisor'    => 'text-indigo-900 dark:text-indigo-200',
        'co_supervisor'      => 'text-blue-900 dark:text-blue-200',
        'pspc'               => 'text-cyan-900 dark:text-cyan-200',
        'dpgc'               => 'text-teal-900 dark:text-teal-200',
        'hod'                => 'text-sky-900 dark:text-sky-200',
        'academic_office'    => 'text-violet-900 dark:text-violet-200',
        'doaa'               => 'text-purple-900 dark:text-purple-200',
        'dr'                 => 'text-fuchsia-900 dark:text-fuchsia-200',
        'senate'             => 'text-pink-900 dark:text-pink-200',
        'senate_chairperson' => 'text-slate-900 dark:text-slate-200',
    ];

    $titleColor = $titleColors[$role] ?? $titleColors['main_supervisor'];
@endphp

<div {{ $attributes->merge(['class' => $themes[$role] ?? $themes['main_supervisor']]) }}>
    @if($title)
        <div class="flex items-center justify-between font-bold gap-2 sm:gap-4">
            <h5 class="text-base font-bold {{ $titleColor }}">
                {{ $title }}
            </h5>
            {{ $badge ?? '' }}
        </div>
    @endif
    {{ $slot }}
</div>

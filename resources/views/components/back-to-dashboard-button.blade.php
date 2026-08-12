@props([
    'href' => route('dashboard'),
    'label' => 'Back to Dashboard'
])

<a href="{{ $href }}" {{ $attributes->merge(['class' => 'inline-flex items-center gap-1.5 px-3.5 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 dark:bg-slate-800 dark:hover:bg-slate-700 dark:text-slate-200 font-semibold text-xs rounded-lg border border-slate-200 dark:border-slate-700 transition-all duration-200 shadow-sm hover:shadow group']) }}>
    <svg class="w-4 h-4 text-slate-500 dark:text-slate-400 group-hover:-translate-x-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
    </svg>
    <span>{{ $label }}</span>
</a>

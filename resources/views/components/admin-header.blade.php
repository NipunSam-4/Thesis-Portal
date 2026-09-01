@props(['title', 'description' => null])

<div class="space-y-3">
    <!-- Top Back to Dashboard Button -->
    <div class="flex justify-end">
        <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center justify-center px-3.5 py-1.5 sm:px-4 sm:py-2 bg-white dark:bg-slate-900 hover:bg-slate-100 dark:hover:bg-slate-800 text-slate-700 dark:text-slate-200 text-xs font-semibold rounded-xl border border-slate-200 dark:border-slate-800 transition shadow-sm">
            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Back to Dashboard
        </a>
    </div>

    <!-- Header Title Card -->
    <div class="bg-white dark:bg-slate-900 p-4 sm:p-6 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-800">
        <h2 class="text-lg sm:text-2xl font-bold text-slate-900 dark:text-white">{{ $title }}</h2>
        @if($description)
            <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-0.5 sm:mt-1">{{ $description }}</p>
        @endif
    </div>
</div>

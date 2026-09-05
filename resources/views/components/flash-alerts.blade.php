@php
    $hasAlert = session('success') || session('error') || session('warning') || session('info') || session('status');
@endphp

@if($hasAlert)
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-4 pb-2 space-y-3"
         x-data="{ show: true }" 
         x-show="show" 
         x-init="setTimeout(() => show = false, 5000)" 
         x-transition:leave="transition ease-in duration-500" 
         x-transition:leave-start="opacity-100 scale-100" 
         x-transition:leave-end="opacity-0 scale-95">
        
        @if(session('success'))
            <div class="p-4 bg-emerald-50 dark:bg-emerald-950/60 border-l-4 border-emerald-500 rounded-xl text-emerald-900 dark:text-emerald-200 text-sm flex items-center justify-between gap-3 shadow-sm">
                <div class="flex items-center gap-3">
                    <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    <span class="font-semibold">{{ session('success') }}</span>
                </div>
                <button type="button" @click="show = false" class="text-emerald-700 dark:text-emerald-300 hover:text-emerald-900 dark:hover:text-white text-lg font-bold leading-none p-1 cursor-pointer" aria-label="Dismiss alert">&times;</button>
            </div>
        @endif

        @if(session('error'))
            <div class="p-4 bg-rose-50 dark:bg-rose-950/60 border-l-4 border-rose-500 rounded-xl text-rose-900 dark:text-rose-200 text-sm flex items-center justify-between gap-3 shadow-sm">
                <div class="flex items-center gap-3">
                    <svg class="w-5 h-5 text-rose-600 dark:text-rose-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    <span class="font-semibold">{{ session('error') }}</span>
                </div>
                <button type="button" @click="show = false" class="text-rose-700 dark:text-rose-300 hover:text-rose-900 dark:hover:text-white text-lg font-bold leading-none p-1 cursor-pointer" aria-label="Dismiss alert">&times;</button>
            </div>
        @endif

        @if(session('warning'))
            <div class="p-4 bg-amber-50 dark:bg-amber-950/60 border-l-4 border-amber-500 rounded-xl text-amber-900 dark:text-amber-200 text-sm flex items-center justify-between gap-3 shadow-sm">
                <div class="flex items-center gap-3">
                    <svg class="w-5 h-5 text-amber-600 dark:text-amber-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    <span class="font-semibold">{{ session('warning') }}</span>
                </div>
                <button type="button" @click="show = false" class="text-amber-700 dark:text-amber-300 hover:text-amber-900 dark:hover:text-white text-lg font-bold leading-none p-1 cursor-pointer" aria-label="Dismiss alert">&times;</button>
            </div>
        @endif

        @if(session('info'))
            <div class="p-4 bg-blue-50 dark:bg-blue-950/60 border-l-4 border-blue-500 rounded-xl text-blue-900 dark:text-blue-200 text-sm flex items-center justify-between gap-3 shadow-sm">
                <div class="flex items-center gap-3">
                    <svg class="w-5 h-5 text-blue-600 dark:text-blue-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span class="font-semibold">{{ session('info') }}</span>
                </div>
                <button type="button" @click="show = false" class="text-blue-700 dark:text-blue-300 hover:text-blue-900 dark:hover:text-white text-lg font-bold leading-none p-1 cursor-pointer" aria-label="Dismiss alert">&times;</button>
            </div>
        @endif

        @if(session('status'))
            <div class="p-4 bg-indigo-50 dark:bg-indigo-950/60 border-l-4 border-indigo-500 rounded-xl text-indigo-900 dark:text-indigo-200 text-sm flex items-center justify-between gap-3 shadow-sm">
                <div class="flex items-center gap-3">
                    <svg class="w-5 h-5 text-indigo-600 dark:text-indigo-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span class="font-semibold">{{ session('status') }}</span>
                </div>
                <button type="button" @click="show = false" class="text-indigo-700 dark:text-indigo-300 hover:text-indigo-900 dark:hover:text-white text-lg font-bold leading-none p-1 cursor-pointer" aria-label="Dismiss alert">&times;</button>
            </div>
        @endif

    </div>
@endif

<div x-show="confirmModalOpen" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div x-show="confirmModalOpen" class="fixed inset-0 bg-slate-900/75 dark:bg-slate-900/80 transition-opacity" @click="confirmModalOpen = false"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
        <div x-show="confirmModalOpen" class="inline-block align-bottom bg-white dark:bg-slate-900 rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full border border-slate-200 dark:border-slate-800 p-6">
            
            <div class="flex items-start space-x-4">
                <div class="p-3 bg-amber-100 dark:bg-amber-900/40 text-amber-600 dark:text-amber-400 rounded-full flex-shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-slate-900 dark:text-white" x-text="confirmTitle">Confirm Action</h3>
                    <p class="text-sm text-slate-500 dark:text-slate-400 mt-1" x-text="confirmMessage"></p>
                </div>
            </div>

            <form :action="confirmFormAction" method="POST" class="mt-6 flex flex-row-reverse gap-3">
                @csrf
                <button type="submit" class="px-5 py-2.5 rounded-xl text-sm font-semibold transition" :class="confirmBtnClass" x-text="confirmBtnText">
                    Confirm
                </button>
                <button type="button" @click="confirmModalOpen = false" class="px-5 py-2.5 bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 rounded-xl text-sm font-medium hover:bg-slate-200 dark:hover:bg-slate-700 transition">
                    Cancel
                </button>
            </form>

        </div>
    </div>
</div>

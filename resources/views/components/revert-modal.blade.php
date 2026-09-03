@props([
    'show' => 'showRevertModal',
    'action' => '',
    'title' => 'Revert Form to Student',
    'subtitle' => 'Send form back to student for required changes',
    'onSubmit' => '',
])

<div x-show="{{ $show }}" 
     x-cloak 
     class="fixed inset-0 z-50 overflow-y-auto bg-gray-900/60 backdrop-blur-sm flex items-center justify-center p-4"
     x-transition:enter="transition ease-out duration-200"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-in duration-150"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0">

    <div @click.away="{{ $show }} = false" 
         class="bg-white dark:bg-gray-800 rounded-2xl max-w-lg w-full p-6 shadow-2xl border border-gray-100 dark:border-gray-700 space-y-5"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100">

        <div class="flex items-center justify-between border-b border-gray-100 dark:border-gray-700 pb-3">
            <div class="flex items-center space-x-3">
                <div class="p-2.5 bg-amber-100 dark:bg-amber-900/40 rounded-xl text-amber-600 dark:text-amber-400 shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">{{ $title }}</h3>
                    @if($subtitle)
                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ $subtitle }}</p>
                    @endif
                </div>
            </div>
            <button type="button" @click="{{ $show }} = false" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 text-xl font-bold p-1 rounded-lg leading-none">&times;</button>
        </div>

        <!-- Independent Revert Form -->
        <form action="{{ $action }}" method="POST" class="space-y-4" @if($onSubmit) @submit="{{ $onSubmit }}" @endif>
            @csrf

            <div>
                <label for="modal_reversion_comment" class="block text-sm font-bold text-gray-800 dark:text-gray-200 mb-1.5">
                    Reason for Reversion <span class="text-red-500">*</span>
                </label>
                <textarea name="reversion_comment" 
                          id="modal_reversion_comment"
                          required 
                          rows="4" 
                          placeholder="Provide clear reasons/instructions for the student regarding required modifications" 
                          class="w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm focus:ring-2 focus:ring-amber-500 whitespace-pre-wrap"></textarea>
            </div>

            <div class="flex justify-end space-x-3 pt-2">
                <button type="button" 
                        @click="{{ $show }} = false" 
                        class="px-5 py-2.5 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200 font-bold text-sm rounded-xl transition">
                    Cancel
                </button>
                <button type="submit" 
                        class="px-5 py-2.5 bg-red-600 hover:bg-red-700 text-white font-bold text-sm rounded-xl shadow-md transition flex items-center">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"></path>
                    </svg>
                    Confirm Revert
                </button>
            </div>
        </form>
    </div>
</div>

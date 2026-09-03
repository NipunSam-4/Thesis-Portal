<div 
    x-data="{
        showModal: false,
        fileName: '',
        fileSize: '',
        limitMB: '',
        open(detail) {
            this.fileName = detail.fileName || 'Selected file';
            this.fileSize = detail.fileSize || 'Unknown';
            this.limitMB = detail.limitMB || 'Allowed limit';
            this.showModal = true;
        },
        close() {
            this.showModal = false;
        }
    }"
    x-on:file-size-exceeded.window="open($event.detail)"
    x-on:keydown.escape.window="if (showModal) close()"
    x-cloak
>
    <!-- Modal Backdrop and Overlay -->
    <div 
        x-show="showModal" 
        x-transition:enter="ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-[100] overflow-y-auto bg-gray-950/70 backdrop-blur-xs flex items-center justify-center p-4 sm:p-6"
    >
        <div 
            x-show="showModal"
            x-transition:enter="ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
            x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
            @click.away="close()"
            class="bg-white dark:bg-gray-800 rounded-2xl max-w-md w-full p-6 space-y-5 shadow-2xl border border-red-100 dark:border-red-900/40 relative overflow-hidden"
        >
            <div class="flex items-start gap-4">
                <div class="w-12 h-12 rounded-2xl bg-red-100 dark:bg-red-950/60 text-red-600 dark:text-red-400 flex items-center justify-center shrink-0 shadow-xs border border-red-200 dark:border-red-800/60">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>

                <div class="space-y-1 flex-1 pr-2">
                    <h3 class="text-base sm:text-lg font-bold text-gray-900 dark:text-white leading-snug">
                        File Size Limit Exceeded
                    </h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400">
                        The file you selected exceeds the permissible upload limit.
                    </p>
                </div>

                <button 
                    type="button" 
                    @click="close()" 
                    class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 p-1 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition"
                    aria-label="Close"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <!-- File Details Breakdown Box -->
            <div class="bg-gray-50 dark:bg-gray-900/60 rounded-xl p-3.5 border border-gray-200 dark:border-gray-700/60 space-y-2.5 text-xs">
                <div class="flex items-start justify-between gap-3">
                    <span class="text-gray-500 dark:text-gray-400 font-medium shrink-0">Selected File:</span>
                    <span class="font-semibold text-gray-900 dark:text-gray-200 break-all text-right cursor-default" :title="fileName" x-text="fileName"></span>
                </div>
                <div class="flex items-center justify-between gap-2 border-t border-gray-200 dark:border-gray-800 pt-2">
                    <span class="text-gray-500 dark:text-gray-400 font-medium shrink-0">Actual File Size:</span>
                    <span class="font-bold text-red-600 dark:text-red-400 bg-red-50 dark:bg-red-950/50 px-2 py-0.5 rounded border border-red-200 dark:border-red-900/40" :title="fileSize" x-text="fileSize"></span>
                </div>
                <div class="flex items-center justify-between gap-2 border-t border-gray-200 dark:border-gray-800 pt-2">
                    <span class="text-gray-500 dark:text-gray-400 font-medium shrink-0">Permissible Limit:</span>
                    <span class="font-bold text-emerald-700 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-950/50 px-2 py-0.5 rounded border border-emerald-200 dark:border-emerald-900/40" :title="limitMB" x-text="limitMB"></span>
                </div>
            </div>

            <p class="text-[11px] text-gray-500 dark:text-gray-400 leading-relaxed">
                Please compress your file or select another document within the permissible limit to proceed.
            </p>

            <!-- Action Button -->
            <div class="flex justify-end pt-1">
                <button 
                    type="button" 
                    @click="close()" 
                    class="w-full sm:w-auto px-5 py-2.5 bg-red-600 hover:bg-red-700 active:bg-red-800 text-white font-bold text-xs rounded-xl shadow-md transition flex items-center justify-center gap-1.5"
                >
                    <span>Understood</span>
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    /**
     * Global helper to validate file size.
     * If exceeded, clears input value and dispatches 'file-size-exceeded' event to display the modal.
     * 
     * @param {Event|HTMLInputElement} target - The change event or input element
     * @param {number} maxMB - Maximum permissible limit in Megabytes
     * @returns {boolean} - true if valid, false if exceeded
     */
    window.checkFileSize = function(target, maxMB) {
        const input = (target && target.target) ? target.target : target;
        if (!input || !input.files || !input.files[0]) return true;

        const file = input.files[0];
        const sizeInMB = file.size / (1024 * 1024);
        const limit = Number(maxMB) || 10;

        if (sizeInMB > limit) {
            const formattedSize = sizeInMB >= 1 ? `${sizeInMB.toFixed(2)} MB` : `${(file.size / 1024).toFixed(1)} KB`;
            const formattedLimit = `${limit} MB`;

            window.dispatchEvent(new CustomEvent('file-size-exceeded', {
                detail: {
                    fileName: file.name,
                    fileSize: formattedSize,
                    limitMB: formattedLimit,
                    inputId: input.id || null
                }
            }));

            // Clear the file input
            input.value = '';
            return false;
        }

        return true;
    };

    // Auto-bind to any file input with data-max-size attribute
    document.addEventListener('change', function(event) {
        if (event.target && event.target.matches && event.target.matches('input[type="file"][data-max-size]')) {
            const limit = parseFloat(event.target.getAttribute('data-max-size'));
            if (limit > 0) {
                window.checkFileSize(event.target, limit);
            }
        }
    });
</script>

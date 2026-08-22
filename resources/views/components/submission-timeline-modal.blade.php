@props(['form', 'title' => 'Form Submission Timeline'])

@if($form && method_exists($form, 'getSubmittedTimeline'))
    @php
        $timeline = $form->getSubmittedTimeline();
    @endphp

    @if(count($timeline) > 0)
        <div x-data="{ openModal: false }" class="inline-flex items-center">
            @if(isset($trigger))
                <div @click="openModal = true" class="inline-flex items-center cursor-pointer">
                    {{ $trigger }}
                </div>
            @else
                <!-- Clock Trigger Button -->
                <button type="button" 
                        @click="openModal = true" 
                        title="View Submission Timeline" 
                        class="mr-1.5 p-1 text-gray-500 hover:text-indigo-600 dark:text-gray-400 dark:hover:text-indigo-400 rounded-full hover:bg-gray-100 dark:hover:bg-gray-700/60 transition inline-flex items-center shrink-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </button>
            @endif

            <!-- Popup Modal (Styled similar to student info modal) -->
            <div x-show="openModal" 
                 x-cloak 
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 class="fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-6 overflow-hidden overscroll-contain" 
                 aria-labelledby="modal-title" 
                 role="dialog" 
                 aria-modal="true">
                
                <!-- Backdrop -->
                <div class="fixed inset-0 bg-gray-900/60 dark:bg-gray-950/80 backdrop-blur-sm transition-opacity" 
                     @click="openModal = false"></div>

                <!-- Modal Panel Container -->
                <div class="relative w-full max-w-5xl max-h-[90vh] flex flex-col bg-white dark:bg-gray-800 rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all border border-gray-100 dark:border-gray-700 z-10">
                    
                    <!-- Header -->
                    <div class="bg-gradient-to-r from-indigo-900 via-indigo-800 to-indigo-900 px-6 py-3.5 flex items-center justify-between shrink-0">
                        <div class="flex items-center space-x-3">
                            <div class="p-2 bg-indigo-800/80 rounded-lg text-indigo-200">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-base font-bold text-white leading-snug">{{ $title }}</h3>
                                <p class="text-xs text-indigo-200">Submission timeline and current evaluation progress</p>
                            </div>
                        </div>
                        <button @click="openModal = false" class="text-indigo-200 hover:text-white p-1 rounded-lg hover:bg-indigo-800/60 transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                    </div>

                    <!-- Body (3 in a Row Grid) -->
                    <div class="p-4 sm:p-5 space-y-4 overflow-y-auto overscroll-contain flex-1">
                        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3.5">
                            @foreach($timeline as $item)
                                @php
                                    $statusType = $item['status_type'] ?? 'submitted';
                                    $statusLabel = $item['status_label'] ?? '✓ Submitted';
                                    
                                    $badgeClass = match($statusType) {
                                        'pending' => 'bg-amber-100 text-amber-800 dark:bg-amber-900/60 dark:text-amber-300 border border-amber-200 dark:border-amber-700',
                                        'approved' => 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/60 dark:text-emerald-300',
                                        'rejected' => 'bg-red-100 text-red-800 dark:bg-red-900/60 dark:text-red-300',
                                        'reverted' => 'bg-amber-100 text-amber-800 dark:bg-amber-900/60 dark:text-amber-300',
                                        default => 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/60 dark:text-emerald-300',
                                    };
                                    
                                    $cardBg = match($statusType) {
                                        'pending' => 'bg-amber-50/60 dark:bg-amber-950/30 border-amber-200 dark:border-amber-800/60',
                                        'rejected' => 'bg-red-50/50 dark:bg-red-950/20 border-red-200 dark:border-red-800/50',
                                        'reverted' => 'bg-amber-50/50 dark:bg-amber-950/20 border-amber-200 dark:border-amber-800/50',
                                        default => 'bg-gray-50 dark:bg-gray-700/40 border-gray-200 dark:border-gray-600',
                                    };
                                @endphp
                                <div class="{{ $cardBg }} p-3 sm:p-3.5 rounded-xl border space-y-1 shadow-sm">
                                    <div class="flex items-center justify-between gap-2">
                                        <span class="text-[10px] font-bold uppercase tracking-wider text-indigo-600 dark:text-indigo-400 truncate">
                                            {{ $item['role'] }}
                                        </span>
                                        <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-semibold {{ $badgeClass }} shrink-0">
                                            {{ $statusLabel }}
                                        </span>
                                    </div>
                                    <div class="text-xs sm:text-sm font-bold text-gray-900 dark:text-white truncate">
                                        {{ $item['name'] }}
                                    </div>
                                    @if(!empty($item['submitted_at']))
                                        <div class="text-[11px] font-medium text-gray-500 dark:text-gray-400 flex items-center space-x-1 pt-0.5">
                                            <svg class="w-3.5 h-3.5 text-gray-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                            <span>{{ \Carbon\Carbon::parse($item['submitted_at'])->format('d-M-Y H:i') }}</span>
                                        </div>
                                    @else
                                        <div class="text-[11px] font-medium text-amber-700 dark:text-amber-400 flex items-center space-x-1 pt-0.5">
                                            <svg class="w-3.5 h-3.5 text-amber-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                            <span>Evaluation Pending</span>
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Footer -->
                    <div class="bg-gray-50 dark:bg-gray-800/80 px-6 py-2.5 border-t border-gray-100 dark:border-gray-700 flex justify-end shrink-0">
                        <button @click="openModal = false" class="px-4 py-1.5 bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 text-gray-800 dark:text-gray-200 font-bold text-xs rounded-lg transition">
                            Close
                        </button>
                    </div>

                </div>
            </div>
        </div>
    @endif
@endif

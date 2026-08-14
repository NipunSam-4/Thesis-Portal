<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-bold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Circulate Draft Synopsis Report') }}
            </h2>
            <x-back-to-dashboard-button />
        </div>
    </x-slot>

    <div class="py-8" x-data="draftSynopsisForm()">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Success/Error Alerts -->
            @if(session('success'))
                <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)" class="p-4 bg-emerald-100 border-l-4 border-emerald-500 text-emerald-800 rounded-lg shadow-sm font-semibold text-sm">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('warning'))
                <div class="p-4 bg-amber-100 border-l-4 border-amber-500 text-amber-800 rounded-lg shadow-sm font-semibold text-sm">
                    {{ session('warning') }}
                </div>
            @endif

            @if($errors->any())
                <div class="p-4 bg-red-100 border-l-4 border-red-500 text-red-800 rounded-lg shadow-sm font-semibold text-sm space-y-1">
                    @foreach($errors->all() as $error)
                        <div>• {{ $error }}</div>
                    @endforeach
                </div>
            @endif

            @if($circulation)
                <!-- READ-ONLY CARD (After Submission) -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 space-y-6">
                    <div class="border-b border-gray-100 dark:border-gray-700 pb-3 flex justify-between items-center">
                        <div>
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white">
                                Circulated Draft Synopsis Details
                            </h3>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                                Your draft synopsis report has been submitted and shared with all academic authorities for comments.
                            </p>
                        </div>
                        <span class="px-3 py-1 bg-indigo-100 text-indigo-800 dark:bg-indigo-900/60 dark:text-indigo-300 text-xs font-bold rounded-full">
                            Circulated
                        </span>
                    </div>

                    <!-- Thesis Title (Read-Only) -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">
                            Name of Thesis
                        </label>
                        <input type="text" 
                               value="{{ $thesis->title }}" 
                               readonly 
                               class="w-full rounded-lg border-gray-300 dark:border-gray-600 bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200 text-sm font-medium cursor-not-allowed">
                    </div>

                    <!-- Draft Synopsis Upload (View-Only Document Badge) -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">
                            Uploaded Draft Synopsis Report
                        </label>
                        <div class="flex items-center justify-between p-3.5 bg-indigo-50/70 dark:bg-indigo-950/40 border border-indigo-200 dark:border-indigo-800 rounded-xl">
                            <div class="flex items-center space-x-2 text-xs font-semibold text-indigo-900 dark:text-indigo-200">
                                <svg class="w-5 h-5 text-indigo-600 dark:text-indigo-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                <span>Circulated Draft Synopsis Document</span>
                            </div>
                            <a href="{{ route('draft_synopsis.document.serve', $circulation->id) }}" target="_blank" class="inline-flex items-center px-3.5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs rounded-lg shadow transition">
                                View Document &rarr;
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Comments Trail Section (Visible After Submission) -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 space-y-4">
                    <div class="border-b border-gray-100 dark:border-gray-700 pb-2 flex items-center justify-between">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white">
                            Authority Comments
                        </h3>
                        <span class="px-2.5 py-0.5 bg-indigo-100 text-indigo-800 dark:bg-indigo-900/60 dark:text-indigo-300 text-xs font-bold rounded-full">
                            {{ $comments->count() }} {{ Str::plural('Comment', $comments->count()) }}
                        </span>
                    </div>

                    @if($comments->isEmpty())
                        <div class="flex items-center space-x-2 text-xs text-gray-600 dark:text-gray-500 bg-gray-50 dark:bg-gray-700/50 p-4 rounded-xl border border-dashed border-gray-200 dark:border-gray-700/60">
                            <svg class="w-5 h-5 text-gray-400 dark:text-gray-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 4.418 9 8z"></path>
                            </svg>
                            <span class="italic font-normal text-sm">No authority comments provided yet. Comments will appear here as authorities review your draft synopsis.</span>
                        </div>
                    @else
                        <div class="space-y-3">
                            @foreach($comments as $comment)
                                <div class="p-4 bg-gray-50/80 dark:bg-gray-700/40 border-l-4 border-indigo-500 rounded-xl space-y-2">
                                    <div class="flex items-center justify-between text-xs">
                                        <span class="font-bold text-sm text-indigo-900 dark:text-indigo-200">
                                            {{ $comment->authority_label }}
                                        </span>
                                        <span class="text-xs text-gray-500 dark:text-gray-400 font-semibold">
                                            {{ $comment->created_at->format('d M Y, h:i A') }}
                                        </span>
                                    </div>
                                    <div class="prose dark:prose-invert max-w-none text-sm text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 p-4 rounded-lg border border-gray-100 dark:border-gray-700 leading-relaxed">
                                        {!! \Stevebauman\Purify\Facades\Purify::clean($comment->comment) !!}
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

            @else
                <!-- EDITABLE SUBMISSION FORM (First Time Before Submission) -->
                <form action="{{ route('student.draft_synopsis.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                    @csrf

                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 space-y-6">
                        <div class="border-b border-gray-100 dark:border-gray-700 pb-3">
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white">
                                Draft Synopsis Circulation Details
                            </h3>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                Circulate your draft synopsis report to all academic authorities simultaneously for feedback before submitting your PTS-1 form.
                            </p>
                        </div>

                        <!-- Thesis Title Field (Editable) -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">
                                Name of Thesis <span class="text-red-500">*</span>
                            </label>
                            <input type="text" 
                                   name="thesis_title" 
                                   value="{{ old('thesis_title', $thesis->title) }}" 
                                   required 
                                   placeholder="Enter your thesis title..." 
                                   class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm focus:ring-indigo-500 focus:border-indigo-500">
                            <p class="text-[11px] text-gray-500 dark:text-gray-400 mt-1">
                                Updating the thesis title here will also update your registered thesis record.
                            </p>
                        </div>

                        <!-- Draft Synopsis Upload Field -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">
                                Draft Synopsis Upload <span class="text-red-500">*</span>
                                <span class="text-xs text-gray-500 font-normal">(PDF or DOCX, max 10MB)</span>
                            </label>
                            
                            <div class="border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-xl p-4 bg-gray-50/50 dark:bg-gray-900/40 text-center space-y-3">
                                <input type="file" 
                                       id="synopsisInput" 
                                       name="draft_synopsis_report" 
                                       accept=".pdf,.docx" 
                                       required
                                       @change="handleFileSelect($event)" 
                                       class="hidden">

                                <!-- Upload UI when no file selected -->
                                <div x-show="!selectedFile.name">
                                    <svg class="w-8 h-8 text-gray-400 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                                    </svg>
                                    <label for="synopsisInput" class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs rounded-lg shadow cursor-pointer transition">
                                        Browse & Select Draft Synopsis Document
                                    </label>
                                </div>

                                <!-- Selected File Badge -->
                                <div x-show="selectedFile.name" x-cloak class="flex items-center justify-between p-3 bg-white dark:bg-gray-800 rounded-lg border border-indigo-200 dark:border-indigo-800">
                                    <div class="flex items-center space-x-2 text-xs text-gray-800 dark:text-gray-200 font-medium truncate">
                                        <svg class="w-4 h-4 text-indigo-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                        </svg>
                                        <span x-text="selectedFile.name" class="truncate"></span>
                                        <span x-text="selectedFile.size" class="text-gray-400"></span>
                                    </div>
                                    <button type="button" @click="clearSelectedFile()" class="text-red-500 hover:text-red-700 font-bold text-xs px-2 py-1">
                                        Remove
                                    </button>
                                </div>
                            </div>
                            
                            <p x-show="fileError" x-text="fileError" class="text-xs text-red-600 font-semibold mt-1" x-cloak></p>
                        </div>

                        <!-- Action Button -->
                        <div class="pt-4 border-t border-gray-100 dark:border-gray-700 flex justify-end">
                            <button type="submit" class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-sm rounded-xl shadow transition flex items-center space-x-2">
                                <span>Circulate Draft Synopsis Report</span>
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                </form>
            @endif

        </div>
    </div>

    <script>
        function draftSynopsisForm() {
            return {
                selectedFile: { name: '', size: '' },
                fileError: '',

                handleFileSelect(event) {
                    const file = event.target.files[0];
                    if (!file) return;

                    const maxMB = 10;
                    if (file.size > maxMB * 1024 * 1024) {
                        this.fileError = `File size exceeds the maximum limit of ${maxMB} MB.`;
                        event.target.value = '';
                        this.selectedFile = { name: '', size: '' };
                        return;
                    }

                    this.fileError = '';
                    this.selectedFile = {
                        name: file.name,
                        size: `(${(file.size / (1024 * 1024)).toFixed(2)} MB)`
                    };
                },

                clearSelectedFile() {
                    const input = document.getElementById('synopsisInput');
                    if (input) input.value = '';
                    this.selectedFile = { name: '', size: '' };
                    this.fileError = '';
                }
            };
        }
    </script>
</x-app-layout>

<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-bold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Circulate Draft Synopsis Report') }}
            </h2>
            <x-back-to-dashboard-button />
        </div>
    </x-slot>

    <div class="py-6" x-data="draftSynopsisForm()">
        <div class="max-w-4xl mx-auto px-2 sm:px-6 lg:px-8 space-y-6">

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
                               value="{{ $circulation->thesis_title ?? $thesis->title }}" 
                               readonly 
                               class="w-full rounded-lg border-gray-300 dark:border-gray-600 bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200 text-sm font-medium cursor-not-allowed">
                    </div>

                    <!-- Draft Synopsis Upload (View-Only Document Badge) -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">
                            Uploaded Draft Synopsis Report
                        </label>
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between p-3.5 bg-indigo-50/70 dark:bg-indigo-950/40 border border-indigo-200 dark:border-indigo-800 rounded-xl gap-3">
                            <div class="flex items-center space-x-2 text-xs font-semibold text-indigo-900 dark:text-indigo-200 min-w-0 truncate">
                                <svg class="w-5 h-5 text-indigo-600 dark:text-indigo-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                <span class="truncate">Circulated Draft Synopsis Document</span>
                            </div>
                            <a href="{{ route('draft_synopsis.document.serve', $circulation->id) }}" target="_blank" class="inline-flex items-center justify-center space-x-1.5 px-3.5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs rounded-lg shadow transition shrink-0">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                </svg>
                                <span>View Document &rarr;</span>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Comments Trail Section (Visible After Submission) -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 space-y-4">
                    <div class="border-b border-gray-100 dark:border-gray-700 pb-2 flex items-center justify-between gap-3">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white">
                            Authority Comments
                        </h3>
                        <span class="px-2.5 py-0.5 bg-indigo-100 text-indigo-800 dark:bg-indigo-900/60 dark:text-indigo-300 text-xs font-bold rounded-full whitespace-nowrap shrink-0">
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
                                    <div class="flex items-center justify-between text-xs gap-2 sm:gap-4">
                                        <span class="font-bold text-sm text-indigo-900 dark:text-indigo-200">
                                            @php
                                                $label = $comment->authority_label;
                                                $formattedLabel = preg_replace('/(\s*\([^)]+\))/', '<span class="block sm:inline text-xs font-normal text-indigo-700 dark:text-indigo-300 mt-0.5 sm:mt-0">$1</span>', e($label));
                                            @endphp
                                            {!! $formattedLabel !!}
                                        </span>
                                        <span class="text-xs text-gray-500 dark:text-gray-400 font-semibold whitespace-nowrap shrink-0">
                                            {{ $comment->created_at->format('d M Y, h:i A') }}
                                        </span>
                                    </div>
                                    <div class="prose dark:prose-invert max-w-none text-sm text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 p-4 rounded-lg border border-gray-100 dark:border-gray-700 leading-relaxed">
                                        {!! class_exists(\Stevebauman\Purify\Facades\Purify::class) ? \Stevebauman\Purify\Facades\Purify::clean($comment->comment) : nl2br(e($comment->comment)) !!}
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

            @else
                <!-- EDITABLE SUBMISSION FORM (First Time Before Submission) -->
                <form action="{{ route('student.draft_synopsis.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6" @submit="clearDraft()">
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
                                   x-model="thesisTitle"
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
                                <div x-show="selectedFile.name" x-cloak class="flex flex-col sm:flex-row sm:items-center justify-between p-3 bg-white dark:bg-gray-800 rounded-lg border border-indigo-200 dark:border-indigo-800 gap-3">
                                    <div class="flex items-center space-x-2 text-xs text-gray-800 dark:text-gray-200 font-medium min-w-0 truncate">
                                        <svg class="w-4 h-4 text-indigo-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                        </svg>
                                        <span x-text="selectedFile.name" class="truncate"></span>
                                        <span x-text="selectedFile.size" class="text-gray-400 shrink-0"></span>
                                    </div>
                                    <div class="flex items-center space-x-2 shrink-0 justify-end sm:justify-start">
                                        <a :href="selectedFile.url" target="_blank" x-show="selectedFile.url" class="inline-flex items-center space-x-1 px-3 py-1.5 bg-indigo-50 dark:bg-indigo-900/50 text-indigo-700 dark:text-indigo-300 border border-indigo-200 dark:border-indigo-700 rounded-lg text-xs font-bold hover:bg-indigo-100 dark:hover:bg-indigo-800/60 transition">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                            </svg>
                                            <span>View Document</span>
                                        </a>
                                        <button type="button" @click="clearSelectedFile()" class="text-red-500 hover:text-red-700 font-bold text-xs px-2 py-1">
                                            Remove
                                        </button>
                                    </div>
                                </div>
                            </div>
                            
                            <p x-show="fileError" x-text="fileError" class="text-xs text-red-600 font-semibold mt-1" x-cloak></p>
                        </div>

                        <!-- Action Button -->
                        <div class="pt-4 border-t border-gray-100 dark:border-gray-700 flex justify-center sm:justify-end">
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
            const draftKey = 'draft_synopsis_student_draft_thesis_' + @js($thesis->id);
            let savedDraft = {};
            try {
                savedDraft = JSON.parse(sessionStorage.getItem(draftKey) || '{}');
            } catch (e) {}

            return {
                thesisTitle: @js(old('thesis_title')) || savedDraft.thesisTitle || @js($thesis->title),
                selectedFile: { name: '', size: '', url: '' },
                fileError: '',

                init() {
                    this.$watch('thesisTitle', () => this.saveDraft());
                },

                handleFileSelect(event) {
                    const file = event.target.files[0];
                    if (!file) return;

                    const maxMB = 10;
                    if (file.size > maxMB * 1024 * 1024) {
                        this.fileError = `File size exceeds the maximum limit of ${maxMB} MB.`;
                        event.target.value = '';
                        this.selectedFile = { name: '', size: '', url: '' };
                        return;
                    }

                    this.fileError = '';
                    if (this.selectedFile.url) {
                        URL.revokeObjectURL(this.selectedFile.url);
                    }
                    this.selectedFile = {
                        name: file.name,
                        size: `(${(file.size / (1024 * 1024)).toFixed(2)} MB)`,
                        url: URL.createObjectURL(file)
                    };
                },

                clearSelectedFile() {
                    const input = document.getElementById('synopsisInput');
                    if (input) input.value = '';
                    if (this.selectedFile.url) {
                        URL.revokeObjectURL(this.selectedFile.url);
                    }
                    this.selectedFile = { name: '', size: '', url: '' };
                    this.fileError = '';
                },

                saveDraft() {
                    try {
                        sessionStorage.setItem(draftKey, JSON.stringify({
                            thesisTitle: this.thesisTitle,
                        }));
                    } catch (e) {}
                },

                clearDraft() {
                    try {
                        sessionStorage.removeItem(draftKey);
                    } catch (e) {}
                }
            };
        }
    </script>
</x-app-layout>

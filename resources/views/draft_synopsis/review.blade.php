<x-app-layout>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/tinymce/7.6.0/tinymce.min.js" referrerpolicy="origin"></script>

    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-bold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Draft Synopsis Review Portal') }}
            </h2>
            <x-back-to-dashboard-button />
        </div>
    </x-slot>

    <div class="py-6" x-data="draftSynopsisReviewForm()">
        <div class="max-w-4xl mx-auto px-2 sm:px-6 lg:px-8 space-y-6">

            <!-- Success/Error Alerts -->
            @if(session('success'))
                <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)" class="p-4 bg-emerald-100 border-l-4 border-emerald-500 text-emerald-800 rounded-lg shadow-sm font-semibold text-sm">
                    {{ session('success') }}
                </div>
            @endif

            @if($errors->any())
                <div class="p-4 bg-red-100 border-l-4 border-red-500 text-red-800 rounded-lg shadow-sm font-semibold text-sm space-y-1">
                    @foreach($errors->all() as $error)
                        <div>• {{ $error }}</div>
                    @endforeach
                </div>
            @endif

            <!-- Section 1: Student & Circulated Synopsis Info Card -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 space-y-4">
                <div class="border-b border-gray-100 dark:border-gray-700 pb-3 flex justify-between items-start">
                    <div>
                        <span class="text-xs uppercase font-bold text-indigo-600 dark:text-indigo-400 tracking-wider">
                            Circulated Draft Synopsis Report
                        </span>
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white">
                            {{ $student->user->name }}
                            <span class="text-sm font-semibold text-gray-500 dark:text-gray-400">({{ $student->roll_number }})</span>
                        </h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400">
                            Department of {{ $student->department->name ?? 'N/A' }} | {{ strtoupper($student->program_name) }} Program
                        </p>
                    </div>

                    <span class="px-3 py-1 bg-indigo-100 dark:bg-indigo-900/60 text-indigo-800 dark:text-indigo-300 text-xs font-bold rounded-full">
                        Circulated
                    </span>
                </div>

                <!-- Thesis Title Field -->
                <div>
                    <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">Thesis Title</label>
                    <div class="p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg text-sm font-bold text-gray-900 dark:text-white border border-gray-200 dark:border-gray-600">
                        {{ $circulation->thesis_title ?? $thesis->title }}
                    </div>
                </div>

                <!-- Circulated Document Badge -->
                <div>
                    <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">Circulated Synopsis Document</label>
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between p-3 bg-indigo-50/70 dark:bg-indigo-950/40 border border-indigo-200 dark:border-indigo-800 rounded-lg gap-3">
                        <div class="flex items-center space-x-2 text-xs font-semibold text-indigo-900 dark:text-indigo-200 min-w-0 truncate">
                            <svg class="w-5 h-5 text-indigo-600 dark:text-indigo-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            <span class="truncate">Draft Synopsis Report File</span>
                        </div>
                        <a href="{{ route('draft_synopsis.document.serve', $circulation->id) }}" target="_blank" class="inline-flex items-center justify-center space-x-1.5 px-3.5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs rounded-lg shadow transition shrink-0">
                            View / Download Document &rarr;
                        </a>
                    </div>
                </div>
            </div>

            <!-- Section 2: Authority Feedback Submission Form -->
            <form action="{{ route('draft_synopsis.comment', $circulation->id) }}" method="POST" class="space-y-6" @submit="clearDraft()">
                @csrf
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 space-y-4">
                    <div class="border-b border-gray-100 dark:border-gray-700 pb-3 flex justify-between items-center">
                        <div>
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white">
                                Feedback for Draft Synopsis Report
                            </h3>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                                Provide your comment on the student's draft synopsis report below.
                            </p>
                        </div>
                    </div>

                    <div @input="comment = $event.target.value">
                        <x-tinymce name="comment" 
                                   id="draft_synopsis_comment"
                                   :value="$userComment?->comment" 
                                   placeholder="Type your feedback, observations, or suggestions on the draft synopsis report..."
                                   :height="350" />
                    </div>

                    <div class="flex justify-center sm:justify-end">
                        <button type="submit" class="w-full sm:w-auto px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-sm rounded-xl shadow transition">
                            {{ $userComment ? 'Update Feedback Comment' : 'Submit Feedback Comment' }}
                        </button>
                    </div>
                </div>
            </form>

            <!-- Section 3: Ordered Authority Comments Trail -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 space-y-4">
                <div class="border-b border-gray-100 dark:border-gray-700 pb-2 flex items-center justify-between gap-3">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">
                        Authority Feedback & Comments
                    </h3>
                    <span class="px-2.5 py-0.5 bg-indigo-100 text-indigo-800 dark:bg-indigo-900/60 dark:text-indigo-300 text-xs font-bold rounded-full whitespace-nowrap shrink-0">
                        {{ $comments->count() }} {{ Str::plural('Comment', $comments->count()) }}
                    </span>
                </div>

                @if($comments->isEmpty())
                    <div class="flex items-center space-x-2 text-xs text-gray-600 dark:text-gray-500 bg-gray-50 dark:bg-gray-700/50 p-4 rounded-xl border border-dashed border-gray-200 dark:border-gray-700/60">
                        <svg class="w-5 h-5 text-gray-400 dark:text-gray-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M16 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 4.418 9 8z"></path>
                        </svg>
                        <span class="italic font-normal text-sm">No authority comments submitted yet.</span>
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

        </div>
    </div>

    <script>
        function draftSynopsisReviewForm() {
            const draftKey = 'draft_synopsis_review_draft_thesis_' + @js($circulation->thesis_id);
            let savedDraft = {};
            try {
                savedDraft = JSON.parse(sessionStorage.getItem(draftKey) || '{}');
            } catch (e) {}

            return {
                comment: @js(old('comment')) || savedDraft.comment || @js($userComment?->comment ?? ''),

                init() {
                    this.$watch('comment', () => this.saveDraft());

                    // Restore draft into TinyMCE editor once initialized
                    if (savedDraft.comment && savedDraft.comment !== @js($userComment?->comment ?? '')) {
                        const checkTinyMCE = setInterval(() => {
                            if (typeof tinymce !== 'undefined' && tinymce.get('draft_synopsis_comment')) {
                                tinymce.get('draft_synopsis_comment').setContent(savedDraft.comment);
                                clearInterval(checkTinyMCE);
                            }
                        }, 100);
                    }
                },

                saveDraft() {
                    try {
                        sessionStorage.setItem(draftKey, JSON.stringify({
                            comment: this.comment,
                        }));
                    } catch (e) {}
                },

                clearDraft() {
                    try {
                        sessionStorage.removeItem(draftKey);
                    } catch (e) {}
                }
            }
        }
    </script>
</x-app-layout>

@props(['rejectedTheses', 'student' => null, 'showPill' => true, 'openByDefault' => false])

@php
    $student = $student ?? ($rejectedTheses->first()?->student ?? null);
    $isPhd = $student ? $student->isPhd() : true;
    $ptsPrefix = $isPhd ? 'PTS' : 'MSRTS';
@endphp

@if($rejectedTheses && $rejectedTheses->isNotEmpty())
    <div x-data="{ openRejectedTheses: {{ $openByDefault ? 'true' : 'false' }} }" class="space-y-3">
        @if($showPill)
            <!-- Top-Right Standalone Past Rejected Theses Collapsible Pill -->
            <div class="flex justify-end">
                <button 
                    type="button" 
                    @click="openRejectedTheses = !openRejectedTheses" 
                    class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-rose-50 hover:bg-rose-100 dark:bg-rose-950/70 dark:hover:bg-rose-900/80 border border-rose-200 dark:border-rose-800 text-rose-700 hover:text-rose-900 dark:text-rose-300 dark:hover:text-rose-100 text-xs font-semibold shadow-xs transition focus:outline-none cursor-pointer"
                >
                    <span>Past rejected theses</span>
                    <span class="w-4 h-4 rounded-full bg-rose-200 dark:bg-rose-900 text-rose-800 dark:text-rose-200 flex items-center justify-center text-[10px] font-bold">
                        {{ $rejectedTheses->count() }}
                    </span>
                    <svg class="w-3.5 h-3.5 text-rose-500 dark:text-rose-400 transform transition-transform duration-200" :class="{ 'rotate-180': openRejectedTheses }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>
            </div>
        @endif

        <!-- Content Body -->
        <div @if($showPill) x-show="openRejectedTheses" x-transition x-cloak @endif class="p-4 sm:p-6 bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-rose-200 dark:border-rose-900/40 space-y-4">
            <div class="text-xs font-extrabold uppercase tracking-wider text-rose-800 dark:text-rose-300 flex items-center gap-2 pb-3 border-b border-rose-100 dark:border-gray-700">
                <span> Past Rejected Theses History ({{ $rejectedTheses->count() }})</span>
            </div>
            @foreach($rejectedTheses as $rejThesis)
                <div class="p-4 sm:p-5 bg-slate-50/70 dark:bg-gray-900/50 rounded-xl border border-slate-200 dark:border-gray-700/80 space-y-4">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-3 border-b border-slate-200 dark:border-gray-700 gap-2">
                        <div>
                            <div class="flex items-center gap-2">
                                <span class="px-2.5 py-0.5 bg-rose-100 text-rose-800 dark:bg-rose-950/60 dark:text-rose-300 text-[10px] font-extrabold rounded-md border border-rose-200 dark:border-rose-800">
                                    ❌ Rejected Thesis
                                </span>
                                <span class="text-xs text-slate-500 dark:text-slate-400 font-medium">
                                    Registered: {{ $rejThesis->created_at?->format('d-M-Y') }}
                                </span>
                            </div>
                            <h4 class="font-bold text-sm sm:text-base text-slate-900 dark:text-white mt-1.5 leading-snug">
                                {{ $rejThesis->title ?? 'No Title Registered' }}
                            </h4>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-1">
                        <!-- Draft Synopsis Card -->
                        @if($rejThesis->draftSynopsisCirculation)
                            <div class="p-4 rounded-xl border border-slate-200 dark:border-gray-700 bg-white dark:bg-gray-800 shadow-xs space-y-3 flex flex-col justify-between">
                                <div class="space-y-2">
                                    <div class="flex justify-between items-start gap-2">
                                        <span class="font-bold text-sm text-slate-900 dark:text-white leading-snug">Draft Synopsis Circulation</span>
                                        <div class="flex items-center gap-1.5 shrink-0">
                                            <x-submission-timeline-modal :form="$rejThesis->draftSynopsisCirculation" title="Draft Synopsis Timeline" />
                                            <span class="shrink-0 bg-indigo-100 text-indigo-800 dark:bg-indigo-950/60 dark:text-indigo-300 text-xs font-bold px-2.5 py-0.5 rounded-full border border-indigo-200 dark:border-indigo-800">Circulated</span>
                                        </div>
                                    </div>
                                    <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed">
                                        Circulate draft synopsis report to Supervisors and PSPC Members for early comments before Open Seminar.
                                    </p>

                                    @if($rejThesis->draftSynopsisCirculation->comments->isNotEmpty())
                                        <div class="p-2.5 bg-indigo-50 dark:bg-indigo-950/40 border-l-4 border-indigo-500 rounded-lg text-xs space-y-1 my-2">
                                            <div class="font-bold text-indigo-900 dark:text-indigo-200">
                                                {{ $rejThesis->draftSynopsisCirculation->comments->count() }} {{ Str::plural('Comment', $rejThesis->draftSynopsisCirculation->comments->count()) }} Received
                                            </div>
                                        </div>
                                    @endif
                                </div>

                                <div class="pt-2 border-t border-slate-100 dark:border-gray-700">
                                    <a href="{{ route('student.draft_synopsis.show', $rejThesis->draftSynopsisCirculation->id) }}" target="_blank" class="block w-full text-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs rounded-lg shadow-sm transition">
                                        View Circulated Synopsis &amp; Comments &rarr;
                                    </a>
                                </div>
                            </div>
                        @endif

                        <!-- PTS-1 Milestone Card -->
                        @if($rejThesis->pts1Form)
                            <div class="p-4 rounded-xl border border-slate-200 dark:border-gray-700 bg-white dark:bg-gray-800 shadow-xs space-y-3 flex flex-col justify-between">
                                <div class="space-y-2">
                                    <div class="flex justify-between items-start gap-2">
                                        <span class="font-bold text-sm text-slate-900 dark:text-white leading-snug">{{ $ptsPrefix }}-1: Open Seminar Report</span>
                                        <div class="flex items-center gap-1.5 shrink-0">
                                            <x-submission-timeline-modal :form="$rejThesis->pts1Form" :title="$ptsPrefix . '-1 Submission Timeline'" />
                                            @if($rejThesis->pts1Form->status === 'approved')
                                                <span class="shrink-0 bg-emerald-100 text-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800 text-xs font-bold px-2.5 py-0.5 rounded-full">Approved</span>
                                            @elseif($rejThesis->pts1Form->status === 'reverted')
                                                <span class="shrink-0 bg-amber-100 text-amber-800 dark:bg-amber-950/60 dark:text-amber-300 border border-amber-200 dark:border-amber-800 text-xs font-bold px-2.5 py-0.5 rounded-full">Reverted</span>
                                            @elseif($rejThesis->pts1Form->status === 'rejected')
                                                <span class="shrink-0 bg-rose-100 text-rose-800 dark:bg-rose-950/60 dark:text-rose-300 border border-rose-200 dark:border-rose-800 text-xs font-bold px-2.5 py-0.5 rounded-full">Rejected</span>
                                            @else
                                                <span class="shrink-0 bg-blue-100 text-blue-800 dark:bg-blue-950/60 dark:text-blue-300 border border-blue-200 dark:border-blue-800 text-xs font-bold px-2.5 py-0.5 rounded-full">{{ ucfirst($rejThesis->pts1Form->status) }}</span>
                                            @endif
                                        </div>
                                    </div>
                                    <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed">
                                        Submission of open seminar details, institute norms, draft synopsis report, publication and other recognitions list.
                                    </p>
                                </div>

                                <div class="pt-2 border-t border-slate-100 dark:border-gray-700">
                                    <div class="flex items-center justify-between gap-2 pt-1">
                                        @if($rejThesis->pts1Form->status === 'approved')
                                            <span class="text-[11px] text-emerald-700 dark:text-emerald-300 font-bold">✓ {{ $ptsPrefix }}-1 Form Fully Approved</span>
                                            <a href="{{ route('pts1.show', $rejThesis->pts1Form->id) }}" target="_blank" class="inline-flex items-center px-3.5 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-lg shadow-sm transition shrink-0 whitespace-nowrap">
                                                View Submission &rarr;
                                            </a>
                                        @else
                                            <span class="text-[11px] text-rose-700 dark:text-rose-300 font-bold">❌ {{ $ptsPrefix }}-1 Form Rejected</span>
                                            <a href="{{ route('pts1.show', $rejThesis->pts1Form->id) }}" target="_blank" class="inline-flex items-center px-3.5 py-1.5 bg-rose-600 hover:bg-rose-700 text-white font-bold text-xs rounded-lg shadow-sm transition shrink-0 whitespace-nowrap">
                                                View Submission &rarr;
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endif

                        <!-- PTS-2 Milestone Card -->
                        @if($rejThesis->pts2Form)
                            <div class="p-4 rounded-xl border border-slate-200 dark:border-gray-700 bg-white dark:bg-gray-800 shadow-xs space-y-3 flex flex-col justify-between">
                                <div class="space-y-2">
                                    <div class="flex justify-between items-start gap-2">
                                        <span class="font-bold text-sm text-slate-900 dark:text-white leading-snug">{{ $ptsPrefix }}-2: Synopsis Report Submission</span>
                                        <div class="flex items-center gap-1.5 shrink-0">
                                            <x-submission-timeline-modal :form="$rejThesis->pts2Form" :title="$ptsPrefix . '-2 Submission Timeline'" />
                                            @if($rejThesis->pts2Form->status === 'approved')
                                                <span class="shrink-0 bg-emerald-100 text-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800 text-xs font-bold px-2.5 py-0.5 rounded-full">Approved</span>
                                            @elseif($rejThesis->pts2Form->status === 'reverted')
                                                <span class="shrink-0 bg-amber-100 text-amber-800 dark:bg-amber-950/60 dark:text-amber-300 border border-amber-200 dark:border-amber-800 text-xs font-bold px-2.5 py-0.5 rounded-full">Reverted</span>
                                            @elseif($rejThesis->pts2Form->status === 'rejected')
                                                <span class="shrink-0 bg-rose-100 text-rose-800 dark:bg-rose-950/60 dark:text-rose-300 border border-rose-200 dark:border-rose-800 text-xs font-bold px-2.5 py-0.5 rounded-full">Rejected</span>
                                            @else
                                                <span class="shrink-0 bg-purple-100 text-purple-800 dark:bg-purple-950/60 dark:text-purple-300 border border-purple-200 dark:border-purple-800 text-xs font-bold px-2.5 py-0.5 rounded-full">{{ ucfirst($rejThesis->pts2Form->status) }}</span>
                                            @endif
                                        </div>
                                    </div>
                                    <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed">
                                        Submission and sequential endorsement of the {{ $isPhd ? 'PhD' : 'MS(R)' }} Synopsis Report.
                                    </p>

                                    @if($rejThesis->pts2Extension)
                                        <div class="p-3 rounded-xl border text-xs my-2 space-y-2 {{ $rejThesis->pts2Extension->status === 'approved' ? 'bg-emerald-50 dark:bg-emerald-950/40 border-emerald-300 dark:border-emerald-800 text-emerald-900 dark:text-emerald-200' : ($rejThesis->pts2Extension->status === 'rejected' ? 'bg-rose-50 dark:bg-rose-950/40 border-rose-300 dark:border-rose-800 text-rose-900 dark:text-rose-200' : 'bg-purple-50 dark:bg-purple-950/40 border-purple-300 dark:border-purple-800 text-purple-900 dark:text-purple-200') }}">
                                            <div class="flex items-center justify-between font-bold">
                                                <span>📅 {{ $ptsPrefix }}-2 Extension</span>
                                                <div class="flex items-center gap-1">
                                                    <x-submission-timeline-modal :form="$rejThesis->pts2Extension" :title="$ptsPrefix . '-2 Extension Timeline'" />
                                                    <span class="text-[10px] px-2.5 py-0.5 rounded-full uppercase font-extrabold tracking-wider {{ $rejThesis->pts2Extension->status === 'approved' ? 'bg-emerald-100 dark:bg-emerald-900/60 text-emerald-800 dark:text-emerald-300' : 'bg-rose-100 dark:bg-rose-900/60 text-rose-800 dark:text-rose-300' }}">
                                                        {{ $rejThesis->pts2Extension->status === 'approved' ? '✓ Approved' : '❌ Rejected' }}
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="text-[11px] flex justify-between items-center gap-2 pt-1">
                                                <span>Extended Until: {{ ($rejThesis->pts2Extension->approved_extended_until_date)?->format('d-M-Y') ?? 'N/A' }}</span>
                                                <a href="{{ route('pts2_extension.show', $rejThesis->pts2Extension->id) }}" target="_blank" class="underline font-bold hover:text-emerald-700 shrink-0 whitespace-nowrap">View Form &rarr;</a>
                                            </div>
                                        </div>
                                    @endif
                                </div>

                                <div class="pt-2 border-t border-slate-100 dark:border-gray-700">
                                    <div class="flex items-center justify-between gap-2 pt-1">
                                        @if($rejThesis->pts2Form->status === 'approved')
                                            <span class="text-[11px] text-emerald-700 dark:text-emerald-300 font-bold">✓ {{ $ptsPrefix }}-2 Form Approved</span>
                                            <a href="{{ route('pts2.show', $rejThesis->pts2Form->id) }}" target="_blank" class="inline-flex items-center px-3.5 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-lg shadow-sm transition shrink-0 whitespace-nowrap">
                                                View Submission &rarr;
                                            </a>
                                        @else
                                            <span class="text-[11px] text-rose-700 dark:text-rose-300 font-bold">❌ {{ $ptsPrefix }}-2 Form Rejected</span>
                                            <a href="{{ route('pts2.show', $rejThesis->pts2Form->id) }}" target="_blank" class="inline-flex items-center px-3.5 py-1.5 bg-rose-600 hover:bg-rose-700 text-white font-bold text-xs rounded-lg shadow-sm transition shrink-0 whitespace-nowrap">
                                                View Submission &rarr;
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endif

                        <!-- PTS-3 Milestone Card -->
                        @if($rejThesis->pts3Form)
                            <div class="p-4 rounded-xl border border-slate-200 dark:border-gray-700 bg-white dark:bg-gray-800 shadow-xs space-y-3 flex flex-col justify-between">
                                <div class="space-y-2">
                                    <div class="flex justify-between items-start gap-2">
                                        <span class="font-bold text-sm text-slate-900 dark:text-white leading-snug">
                                            {{ $ptsPrefix }}-3: Panels of Examiners
                                        </span>
                                        <div class="flex items-center gap-1.5 shrink-0">
                                            <x-submission-timeline-modal :form="$rejThesis->pts3Form" :title="$ptsPrefix . '-3 Submission Timeline'" />
                                            @if($rejThesis->pts3Form->status === 'approved')
                                                <span class="shrink-0 bg-emerald-100 text-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800 text-xs font-bold px-2.5 py-0.5 rounded-full">Approved</span>
                                            @elseif($rejThesis->pts3Form->status === 'rejected')
                                                <span class="shrink-0 bg-rose-100 text-rose-800 dark:bg-rose-950/60 dark:text-rose-300 border border-rose-200 dark:border-rose-800 text-xs font-bold px-2.5 py-0.5 rounded-full">Rejected</span>
                                            @else
                                                <span class="shrink-0 bg-purple-100 text-purple-800 dark:bg-purple-950/60 dark:text-purple-300 border border-purple-200 dark:border-purple-800 text-xs font-bold px-2.5 py-0.5 rounded-full">{{ ucfirst($rejThesis->pts3Form->status) }}</span>
                                            @endif
                                        </div>
                                    </div>
                                    <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed">
                                        Panel of Indian &amp; International Examiners and Oral Examination Board (OEB) recommendations.
                                    </p>
                                </div>

                                <div class="pt-2 border-t border-slate-100 dark:border-gray-700">
                                    <div class="flex items-center justify-between gap-2 pt-1">
                                        @if($rejThesis->pts3Form->status === 'approved')
                                            <span class="text-[11px] text-emerald-700 dark:text-emerald-300 font-bold">✓ {{ $ptsPrefix }}-3 Form Approved</span>
                                            <a href="{{ route('pts3.show', $rejThesis->pts3Form->id) }}" target="_blank" class="inline-flex items-center px-3.5 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-lg shadow-sm transition shrink-0 whitespace-nowrap">
                                                View Status &rarr;
                                            </a>
                                        @else
                                            <span class="text-[11px] text-rose-700 dark:text-rose-300 font-bold">❌ Form Rejected</span>
                                            <a href="{{ route('pts3.show', $rejThesis->pts3Form->id) }}" target="_blank" class="inline-flex items-center px-3.5 py-1.5 bg-rose-600 hover:bg-rose-700 text-white font-bold text-xs rounded-lg shadow-sm transition shrink-0 whitespace-nowrap">
                                                View Status &rarr;
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endif

                        <!-- PTS-4 Milestone Card -->
                        @if($rejThesis->pts4Form)
                            <div class="p-4 rounded-xl border border-slate-200 dark:border-gray-700 bg-white dark:bg-gray-800 shadow-xs space-y-3 flex flex-col justify-between">
                                <div class="space-y-2">
                                    <div class="flex justify-between items-start gap-2">
                                        <span class="font-bold text-sm text-slate-900 dark:text-white leading-snug">
                                            {{ $ptsPrefix }}-4: Thesis Submission
                                        </span>
                                        <div class="flex items-center gap-1.5 shrink-0">
                                            <x-submission-timeline-modal :form="$rejThesis->pts4Form" :title="$ptsPrefix . '-4 Submission Timeline'" />
                                            @if($rejThesis->pts4Form->status === 'approved')
                                                <span class="shrink-0 bg-emerald-100 text-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800 text-xs font-bold px-2.5 py-0.5 rounded-full">Approved</span>
                                            @elseif($rejThesis->pts4Form->status === 'rejected')
                                                <span class="shrink-0 bg-rose-100 text-rose-800 dark:bg-rose-950/60 dark:text-rose-300 border border-rose-200 dark:border-rose-800 text-xs font-bold px-2.5 py-0.5 rounded-full">Rejected</span>
                                            @else
                                                <span class="shrink-0 bg-purple-100 text-purple-800 dark:bg-purple-950/60 dark:text-purple-300 border border-purple-200 dark:border-purple-800 text-xs font-bold px-2.5 py-0.5 rounded-full">{{ ucfirst($rejThesis->pts4Form->status) }}</span>
                                            @endif
                                        </div>
                                    </div>
                                    <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed">
                                        Submission and sequential endorsement of the {{ $isPhd ? 'PhD' : 'MS(R)' }} Thesis.
                                    </p>

                                    @if($rejThesis->pts4Extension)
                                        <div class="p-3 rounded-xl border text-xs my-2 space-y-2 {{ $rejThesis->pts4Extension->status === 'approved' ? 'bg-emerald-50 dark:bg-emerald-950/40 border-emerald-300 dark:border-emerald-800 text-emerald-900 dark:text-emerald-200' : ($rejThesis->pts4Extension->status === 'rejected' ? 'bg-rose-50 dark:bg-rose-950/40 border-rose-300 dark:border-rose-800 text-rose-900 dark:text-rose-200' : 'bg-purple-50 dark:bg-purple-950/40 border-purple-300 dark:border-purple-800 text-purple-900 dark:text-purple-200') }}">
                                            <div class="flex items-center justify-between font-bold">
                                                <span>📅 {{ $ptsPrefix }}-4 Extension</span>
                                                <div class="flex items-center gap-1">
                                                    <x-submission-timeline-modal :form="$rejThesis->pts4Extension" :title="$ptsPrefix . '-4 Extension Timeline'" />
                                                    <span class="text-[10px] px-2.5 py-0.5 rounded-full uppercase font-extrabold tracking-wider {{ $rejThesis->pts4Extension->status === 'approved' ? 'bg-emerald-100 dark:bg-emerald-900/60 text-emerald-800 dark:text-emerald-300' : 'bg-rose-100 dark:bg-rose-900/60 text-rose-800 dark:text-rose-300' }}">
                                                        {{ $rejThesis->pts4Extension->status === 'approved' ? '✓ Approved' : '❌ Rejected' }}
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="text-[11px] flex justify-between items-center gap-2 pt-1">
                                                <span>Extended Until: {{ ($rejThesis->pts4Extension->approved_extended_until_date)?->format('d-M-Y') ?? 'N/A' }}</span>
                                                <a href="{{ route('pts4_extension.show', $rejThesis->pts4Extension->id) }}" target="_blank" class="underline font-bold hover:text-emerald-700 shrink-0 whitespace-nowrap">View Form &rarr;</a>
                                            </div>
                                        </div>
                                    @endif
                                </div>

                                <div class="pt-2 border-t border-slate-100 dark:border-gray-700">
                                    <div class="flex items-center justify-between gap-2 pt-1">
                                        @if($rejThesis->pts4Form->status === 'approved')
                                            <span class="text-[11px] text-emerald-700 dark:text-emerald-300 font-bold">✓ {{ $ptsPrefix }}-4 Form Approved</span>
                                            <a href="{{ route('pts4.show', $rejThesis->pts4Form->id) }}" target="_blank" class="inline-flex items-center px-3.5 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-lg shadow-sm transition shrink-0 whitespace-nowrap">
                                                View Submission &rarr;
                                            </a>
                                        @else
                                            <span class="text-[11px] text-rose-700 dark:text-rose-300 font-bold">❌ {{ $ptsPrefix }}-4 Form Rejected</span>
                                            <a href="{{ route('pts4.show', $rejThesis->pts4Form->id) }}" target="_blank" class="inline-flex items-center px-3.5 py-1.5 bg-rose-600 hover:bg-rose-700 text-white font-bold text-xs rounded-lg shadow-sm transition shrink-0 whitespace-nowrap">
                                                View Submission &rarr;
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endif

                        <!-- PTS-5 Milestone Card -->
                        @if($rejThesis->pts5Form)
                            <div class="p-4 rounded-xl border border-slate-200 dark:border-gray-700 bg-white dark:bg-gray-800 shadow-xs space-y-3 flex flex-col justify-between">
                                <div class="space-y-2">
                                    <div class="flex justify-between items-start gap-2">
                                        <span class="font-bold text-sm text-slate-900 dark:text-white leading-snug">
                                            {{ $ptsPrefix }}-5: Examiners Report &amp; Defense
                                        </span>
                                        <div class="flex items-center gap-1.5 shrink-0">
                                            <x-submission-timeline-modal :form="$rejThesis->pts5Form" :title="$ptsPrefix . '-5 Submission Timeline'" />
                                            @if($rejThesis->pts5Form->status === 'approved')
                                                <span class="shrink-0 bg-emerald-100 text-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800 text-xs font-bold px-2.5 py-0.5 rounded-full">Approved</span>
                                            @elseif($rejThesis->pts5Form->status === 'rejected')
                                                <span class="shrink-0 bg-rose-100 text-rose-800 dark:bg-rose-950/60 dark:text-rose-300 border border-rose-200 dark:border-rose-800 text-xs font-bold px-2.5 py-0.5 rounded-full">Rejected</span>
                                            @else
                                                <span class="shrink-0 bg-purple-100 text-purple-800 dark:bg-purple-950/60 dark:text-purple-300 border border-purple-200 dark:border-purple-800 text-xs font-bold px-2.5 py-0.5 rounded-full">{{ ucfirst($rejThesis->pts5Form->status) }}</span>
                                            @endif
                                        </div>
                                    </div>
                                    <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed">
                                        Evaluation of {{ $isPhd ? 'PhD' : 'MS(R)' }} thesis by external examiners and oral defense recommendations.
                                    </p>
                                </div>

                                <div class="pt-2 border-t border-slate-100 dark:border-gray-700">
                                    <div class="flex items-center justify-between gap-2 pt-1">
                                        @if($rejThesis->pts5Form->status === 'approved')
                                            <span class="text-[11px] text-emerald-700 dark:text-emerald-300 font-bold">✓ {{ $ptsPrefix }}-5 Form Approved</span>
                                        @else
                                            <span class="text-[11px] text-rose-700 dark:text-rose-300 font-bold">❌ {{ $ptsPrefix }}-5 Form Rejected</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endif

                        <!-- PTS-6 Milestone Card -->
                        @if($rejThesis->pts6Form)
                            <div class="p-4 rounded-xl border border-slate-200 dark:border-gray-700 bg-white dark:bg-gray-800 shadow-xs space-y-3 flex flex-col justify-between">
                                <div class="space-y-2">
                                    <div class="flex justify-between items-start gap-2">
                                        <span class="font-bold text-sm text-slate-900 dark:text-white leading-snug">
                                            {{ $ptsPrefix }}-6: Report of {{ $isPhd ? 'PhD' : 'MS(R)' }} Thesis Oral Examination
                                        </span>
                                        <div class="flex items-center gap-1.5 shrink-0">
                                            <x-submission-timeline-modal :form="$rejThesis->pts6Form" :title="$ptsPrefix . '-6 Submission Timeline'" />
                                            @if($rejThesis->pts6Form->status === 'approved')
                                                <span class="shrink-0 bg-emerald-100 text-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800 text-xs font-bold px-2.5 py-0.5 rounded-full">Approved</span>
                                            @elseif($rejThesis->pts6Form->status === 'rejected')
                                                <span class="shrink-0 bg-rose-100 text-rose-800 dark:bg-rose-950/60 dark:text-rose-300 border border-rose-200 dark:border-rose-800 text-xs font-bold px-2.5 py-0.5 rounded-full">Rejected</span>
                                            @else
                                                <span class="shrink-0 bg-purple-100 text-purple-800 dark:bg-purple-950/60 dark:text-purple-300 border border-purple-200 dark:border-purple-800 text-xs font-bold px-2.5 py-0.5 rounded-full">{{ ucfirst($rejThesis->pts6Form->status) }}</span>
                                            @endif
                                        </div>
                                    </div>
                                    <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed">
                                        Report of {{ $isPhd ? 'PhD' : 'MS(R)' }} Thesis Oral Examination and Final Degree Recommendation.
                                    </p>
                                </div>

                                <div class="pt-2 border-t border-slate-100 dark:border-gray-700">
                                    <div class="flex items-center justify-between gap-2 pt-1">
                                        @if($rejThesis->pts6Form->status === 'approved')
                                            <span class="text-[11px] text-emerald-700 dark:text-emerald-300 font-bold">✓ {{ $ptsPrefix }}-6 Form Approved</span>
                                        @else
                                            <span class="text-[11px] text-rose-700 dark:text-rose-300 font-bold">❌ {{ $ptsPrefix }}-6 Form Rejected</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endif

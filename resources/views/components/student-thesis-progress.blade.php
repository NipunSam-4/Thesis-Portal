@props(['student'])

@php
    $activeThesis = $student->theses->where('status', '!=', 'cancelled')->last() ?? $student->theses->last();
    $draftSynopsis = $activeThesis?->draftSynopsisCirculation;
    $pts1Form = $activeThesis?->pts1Form;
    $pts1Approved = $pts1Form && $pts1Form->status === 'approved';
    $pts2Form = $activeThesis?->pts2Form;
    $pts2Extension = $activeThesis?->pts2Extension;
    $isMsr = !$student->isPhd();
    $pts1Prefix = $isMsr ? 'MSRTS-1' : 'PTS-1';
    $pts2Prefix = $isMsr ? 'MSRTS-2' : 'PTS-2';
@endphp

<div class="space-y-6 text-left">
    @if(!$activeThesis)
        <!-- NO THESIS REGISTERED NOTICE -->
        <div class="bg-white dark:bg-slate-900 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-800 p-6">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 rounded-full bg-amber-100 dark:bg-amber-900/50 text-amber-700 dark:text-amber-300 flex items-center justify-center font-bold text-lg shrink-0">
                    ⚠️
                </div>
                <div>
                    <h3 class="text-base font-bold text-slate-900 dark:text-white">No {{ $student->isPhd() ? 'PhD' : 'MS(R)' }} Thesis Registered Yet</h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">The student has not registered a thesis title yet.</p>
                </div>
            </div>
        </div>
    @else
        <!-- THESIS REGISTERED CONTAINER -->
        <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-5 sm:p-6 space-y-5">
            
            <!-- Registered Thesis Title Header -->
            <div class="border-b border-slate-100 dark:border-slate-800 pb-4">
                <span class="text-[10px] font-bold text-indigo-600 dark:text-indigo-400 uppercase tracking-wider block">Registered Thesis Title</span>
                <h2 class="text-lg font-bold text-slate-900 dark:text-white mt-1 leading-snug">
                    {{ $activeThesis->title }}
                </h2>
            </div>
            
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 text-xs">
                <h3 class="font-bold text-slate-900 dark:text-white text-base">
                    {{ $student->isPhd() ? 'PhD Thesis Submission (PTS) Forms Progress' : 'MS(R) Thesis Submission (MSRTS) Forms Progress' }}
                </h3>
                <span class="bg-blue-100 text-blue-800 dark:bg-blue-900/60 dark:text-blue-300 text-[11px] font-bold px-3 py-1 rounded-full w-fit">
                    Current Status: {{ $activeThesis->current_status }}
                </span>
            </div>

            <!-- Form Cards Grid (Read Only - Action Buttons Hidden) -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">

                <!-- 1. Draft Synopsis Circulation Card -->
                <div class="p-4 rounded-xl border border-slate-200 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-800/40 space-y-3 flex flex-col justify-between">
                    <div class="space-y-2">
                        <div class="flex justify-between items-start gap-2">
                            <span class="font-bold text-sm text-slate-900 dark:text-white leading-snug">Draft Synopsis Circulation</span>
                            <div class="flex items-center gap-1 shrink-0">
                                <x-submission-timeline-modal :form="$draftSynopsis" title="Draft Synopsis Timeline" />
                                @if($draftSynopsis)
                                    <span class="shrink-0 bg-indigo-100 text-indigo-800 dark:bg-indigo-900/60 dark:text-indigo-300 text-xs font-bold px-2 py-0.5 rounded whitespace-nowrap">Circulated</span>
                                @elseif($pts1Approved)
                                    <span class="shrink-0 bg-slate-200 dark:bg-slate-700 text-slate-600 dark:text-slate-300 text-xs font-bold px-2 py-0.5 rounded whitespace-nowrap">🔒 Disabled</span>
                                @else
                                    <span class="shrink-0 bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-400 text-xs font-medium px-2 py-0.5 rounded whitespace-nowrap">Not Circulated</span>
                                @endif
                            </div>
                        </div>
                        <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed">
                            Draft synopsis report circulated to Supervisors and PSPC Members for early comments before Open Seminar.
                        </p>

                        @if($draftSynopsis)
                            <div class="p-2.5 bg-indigo-50 dark:bg-indigo-950/40 border-l-4 border-indigo-500 rounded-lg text-xs space-y-1 my-2">
                                <div class="font-bold text-indigo-900 dark:text-indigo-200">
                                    {{ $draftSynopsis->comments->count() }} {{ Str::plural('Comment', $draftSynopsis->comments->count()) }} Received
                                </div>
                                <div class="text-[11px] text-slate-500 dark:text-slate-400">
                                    Circulated on: {{ $draftSynopsis->created_at->format('d-M-Y') }}
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- 2. PTS-1 / MSRTS-1 Card -->
                <div class="p-4 rounded-xl border border-slate-200 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-800/40 space-y-3 flex flex-col justify-between">
                    <div class="space-y-2">
                        <div class="flex justify-between items-start gap-2">
                            <span class="font-bold text-sm text-slate-900 dark:text-white leading-snug">{{ $pts1Prefix }}: Open Seminar Report</span>
                            <div class="flex items-center gap-1 shrink-0">
                                <x-submission-timeline-modal :form="$pts1Form" title="{{ $pts1Prefix }} Submission Timeline" />
                                @if(!$pts1Form)
                                    <span class="shrink-0 bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-400 text-xs font-medium px-2 py-0.5 rounded whitespace-nowrap">Not Initiated</span>
                                @elseif($pts1Form->status === 'approved')
                                    <span class="shrink-0 bg-emerald-100 text-emerald-800 dark:bg-emerald-900/60 dark:text-emerald-300 text-xs font-bold px-2 py-0.5 rounded whitespace-nowrap">Approved</span>
                                @elseif($pts1Form->status === 'reverted')
                                    <span class="shrink-0 bg-amber-100 text-amber-800 dark:bg-amber-900/60 dark:text-amber-300 text-xs font-bold px-2 py-0.5 rounded whitespace-nowrap">Reverted</span>
                                @elseif($pts1Form->status === 'rejected')
                                    <span class="shrink-0 bg-rose-100 text-rose-800 dark:bg-rose-900/60 dark:text-rose-300 text-xs font-bold px-2 py-0.5 rounded whitespace-nowrap">Rejected</span>
                                @else
                                    <span class="shrink-0 bg-blue-100 text-blue-800 dark:bg-blue-900/60 dark:text-blue-300 text-xs font-bold px-2 py-0.5 rounded whitespace-nowrap">In Progress</span>
                                @endif
                            </div>
                        </div>
                        <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed">
                            Open seminar details, institute norms check, and publication recognitions list.
                        </p>

                        @if($pts1Form)
                            <div class="p-2.5 bg-slate-100 dark:bg-slate-800/80 rounded-lg text-xs space-y-1 my-2 border border-slate-200 dark:border-slate-700">
                                @if($pts1Form->seminar_date)
                                    <div class="text-slate-700 dark:text-slate-300 font-medium">
                                        📅 Open Seminar: <strong>{{ $pts1Form->seminar_date->format('d-M-Y') }}</strong> {{ $pts1Form->seminar_time ? 'at ' . $pts1Form->seminar_time : '' }}
                                    </div>
                                @endif
                                <div class="text-indigo-600 dark:text-indigo-400 font-semibold">
                                    Current Stage: {{ $pts1Form->stage_label }}
                                </div>
                                @if($pts1Form->vested_doaa_email)
                                    <div class="text-[11px] text-amber-600 dark:text-amber-400 font-semibold">
                                        ⭐ Vested DOAA: {{ $pts1Form->vested_doaa_email }}
                                    </div>
                                @endif
                                <div class="text-[11px] text-slate-500 dark:text-slate-400">
                                    Submitted: {{ $pts1Form->created_at->format('d-M-Y') }}
                                </div>
                            </div>
                            @if($pts1Form->status === 'reverted')
                                <div class="p-2.5 bg-amber-50 dark:bg-amber-950/40 border-l-4 border-amber-500 rounded-lg text-xs font-semibold text-amber-900 dark:text-amber-200">
                                    ⚠️ Reverted by {{ $pts1Form->getRevertedByRoleLabel() }}
                                </div>
                            @endif
                        @endif
                    </div>
                </div>

                <!-- 3. PTS-2 / MSRTS-2 Card -->
                <div class="p-4 rounded-xl border border-slate-200 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-800/40 space-y-3 flex flex-col justify-between">
                    <div class="space-y-2">
                        <div class="flex justify-between items-start gap-2">
                            <span class="font-bold text-sm text-slate-900 dark:text-white leading-snug">{{ $pts2Prefix }}: Synopsis Report</span>
                            <div class="flex items-center gap-1 shrink-0">
                                <x-submission-timeline-modal :form="$pts2Form" title="{{ $pts2Prefix }} Submission Timeline" />
                                @if(!$pts1Approved)
                                    <span class="shrink-0 bg-slate-200 dark:bg-slate-700 text-slate-600 dark:text-slate-300 text-xs font-bold px-2 py-0.5 rounded whitespace-nowrap">🔒 Locked</span>
                                @elseif(!$pts2Form)
                                    <span class="shrink-0 bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-400 text-xs font-medium px-2 py-0.5 rounded whitespace-nowrap">Not Initiated</span>
                                @elseif($pts2Form->status === 'approved')
                                    <span class="shrink-0 bg-emerald-100 text-emerald-800 dark:bg-emerald-900/60 dark:text-emerald-300 text-xs font-bold px-2 py-0.5 rounded whitespace-nowrap">Approved</span>
                                @elseif($pts2Form->status === 'reverted')
                                    <span class="shrink-0 bg-amber-100 text-amber-800 dark:bg-amber-900/60 dark:text-amber-300 text-xs font-bold px-2 py-0.5 rounded whitespace-nowrap">Reverted</span>
                                @else
                                    <span class="shrink-0 bg-purple-100 text-purple-800 dark:bg-purple-900/60 dark:text-purple-300 text-xs font-bold px-2 py-0.5 rounded whitespace-nowrap">In Progress</span>
                                @endif
                            </div>
                        </div>
                        <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed">
                            Synopsis Report submission and examiner panel approval.
                        </p>

                        @if($pts2Form)
                            <div class="p-2.5 bg-slate-100 dark:bg-slate-800/80 rounded-lg text-xs space-y-1 my-2 border border-slate-200 dark:border-slate-700">
                                <div class="text-purple-600 dark:text-purple-400 font-semibold">
                                    Current Stage: {{ $pts2Form->stage_label }}
                                </div>
                                <div class="text-[11px] text-slate-500 dark:text-slate-400">
                                    Submitted: {{ $pts2Form->created_at->format('d-M-Y') }}
                                </div>
                            </div>
                            @if($pts2Form->status === 'reverted')
                                <div class="p-2.5 bg-amber-50 dark:bg-amber-950/40 border-l-4 border-amber-500 rounded-lg text-xs font-semibold text-amber-900 dark:text-amber-200">
                                    ⚠️ Reverted by {{ $pts2Form->getRevertedByRoleLabel() }}
                                </div>
                            @endif
                        @endif

                        @if($pts2Extension)
                            <div class="p-2.5 rounded-lg border text-xs my-2 space-y-1.5 {{ $pts2Extension->status === 'approved' ? 'bg-emerald-50 dark:bg-emerald-950/40 border-emerald-300 dark:border-emerald-800 text-emerald-900 dark:text-emerald-200' : ($pts2Extension->status === 'reverted' ? 'bg-amber-50 dark:bg-amber-950/40 border-amber-300 dark:border-amber-800 text-amber-900 dark:text-amber-200' : ($pts2Extension->status === 'rejected' ? 'bg-rose-50 dark:bg-rose-950/40 border-rose-300 dark:border-rose-800 text-rose-900 dark:text-rose-200' : 'bg-purple-50 dark:bg-purple-950/40 border-purple-300 dark:border-purple-800 text-purple-900 dark:text-purple-200')) }}">
                                <div class="flex items-center justify-between font-bold">
                                    <span>📅 {{ $pts2Prefix }} Extension</span>
                                    <div class="flex items-center gap-1">
                                        <x-submission-timeline-modal :form="$pts2Extension" title="{{ $pts2Prefix }} Extension Timeline" />
                                        <span class="text-[10px] px-2 py-0.5 rounded-full uppercase font-bold {{ $pts2Extension->status === 'approved' ? 'bg-emerald-100 text-emerald-800' : ($pts2Extension->status === 'reverted' ? 'bg-amber-100 text-amber-800' : 'bg-purple-100 text-purple-800') }}">
                                            {{ $pts2Extension->status }}
                                        </span>
                                    </div>
                                </div>
                                @if($pts2Extension->approved_extended_until_date)
                                    <div class="text-[11px] text-slate-600 dark:text-slate-300">
                                        Extended Until: <strong>{{ $pts2Extension->approved_extended_until_date->format('d-M-Y') }}</strong>
                                    </div>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>

            </div>
        </div>
    @endif
</div>

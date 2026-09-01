@props([
    'student',
    'user',
    'role' => null,      // 'main', 'co', 'pspc' (in faculty context)
    'subTab' => null,    // 'acting', 'vested' (in acting authority context)
    'isMsr' => false,
])

@php
    $degreeType = $student->isPhd() ? 'PhD' : 'MS(R)';
    $ptsPrefix = $student->isPhd() ? 'PTS' : 'MSRTS';
    $cardKey = $role ? "{$role}-{$student->id}" : "{$student->id}";
    $stageLabel = $student->getThesisStageLabel();
    $actionBadge = $student->getActionRequiredBadgeLabel($user, $role);
    $thesis = $student->current_thesis;
    $rejectedTheses = $student->rejected_theses;

    $pts1Approved = $thesis && $thesis->pts1Form && $thesis->pts1Form->status === 'approved';
    $pts2Approved = $thesis && $thesis->pts2Form && $thesis->pts2Form->status === 'approved';
    $pts3Approved = $thesis && $thesis->pts3Form && $thesis->pts3Form->status === 'approved';
    $pts4Approved = $thesis && $thesis->pts4Form && $thesis->pts4Form->status === 'approved';
    $pts5Approved = $thesis && $thesis->pts5Form && $thesis->pts5Form->status === 'approved';
@endphp

<div x-show="matchesFilter(@js($student->searchable_text), {{ $student->department_id ?? 'null' }})" class="mb-2.5 border border-gray-200 dark:border-gray-700 rounded-xl overflow-hidden shadow-xs bg-gray-50/50 dark:bg-gray-800/50 transition">
    <!-- Student Header Card -->
    <div @click="toggleStudent('{{ $cardKey }}')" class="py-2.5 px-3.5 sm:px-4 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700/70 cursor-pointer flex flex-col md:flex-row md:items-center justify-between gap-2 sm:gap-3 transition">
        <div class="flex items-center space-x-2.5 min-w-0">
            <div class="w-8 h-8 rounded-full bg-blue-100 dark:bg-blue-900/60 text-blue-700 dark:text-blue-300 flex items-center justify-center font-bold text-xs border border-blue-200 dark:border-blue-800 shrink-0">
                {{ substr($student->user->name ?? 'S', 0, 1) }}
            </div>
            <div class="min-w-0">
                <h4 class="font-bold text-gray-900 dark:text-white text-sm flex flex-wrap items-center gap-1.5 leading-tight">
                    <span class="truncate">{{ $student->user->name }}</span>
                    <x-student-info-modal :student="$student" />
                    <span class="text-[11px] font-semibold text-gray-500 bg-gray-100 dark:bg-gray-700 px-1.5 py-0.5 rounded">
                        {{ $student->roll_number }}
                    </span>
                    <span class="text-[11px] font-semibold bg-blue-50 dark:bg-blue-900/60 text-blue-700 dark:text-blue-300 border border-blue-100 dark:border-blue-800 px-1.5 py-0.5 rounded">
                        Dept: {{ $student->department->code ?? 'N/A' }}
                    </span>
                </h4>
            </div>
        </div>

        <div class="flex items-center space-x-2 text-xs font-bold shrink-0 self-end md:self-auto">
            @if($actionBadge)
                <span class="inline-flex items-center gap-1 bg-amber-100 dark:bg-amber-900/60 text-amber-800 dark:text-amber-300 text-[11px] font-extrabold px-2 py-0.5 rounded-full border border-amber-300 dark:border-amber-700 shadow-xs whitespace-nowrap">
                    {{ $actionBadge }}
                </span>
            @endif

            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-bold shadow-xs border
                @if($stageLabel === 'Unregistered')
                    bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300 border-gray-200 dark:border-gray-600
                @else
                    bg-blue-100 text-blue-800 dark:bg-blue-900/60 dark:text-blue-300 border-blue-200 dark:border-blue-800
                @endif
            ">
                {{ $stageLabel }}
            </span>

            <svg class="w-4 h-4 text-gray-400 transform transition-transform duration-200" :class="expandedStudent === '{{ $cardKey }}' ? 'rotate-180 text-blue-500' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
            </svg>
        </div>
    </div>

    <!-- Expandable Student Breakdown -->
    <div x-show="expandedStudent === '{{ $cardKey }}'" x-cloak class="p-2 sm:p-5 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 space-y-4 sm:space-y-5">
        
        <!-- Standalone Past Rejected Theses History Pill -->
        @if($rejectedTheses && $rejectedTheses->isNotEmpty())
            <x-past-rejected-theses :rejectedTheses="$rejectedTheses" :student="$student" />
        @endif

        @if($thesis)
            <div class="p-2.5 sm:p-4 bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 space-y-3 sm:space-y-4">
                <div class="border-b border-gray-100 dark:border-gray-700 pb-2">
                    <div class="text-[10px] uppercase font-bold text-blue-600 dark:text-blue-400">Current Thesis Title</div>
                    <h5 class="font-bold text-base text-gray-900 dark:text-white">{{ $thesis->title }}</h5>
                </div>

                <!-- Milestone Forms Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    
                    <!-- Draft Synopsis Circulation Card (Visible only to authorized supervisors & PSPC) -->
                    @if($thesis->canUserViewDraftSynopsis($user))
                        <div class="p-2.5 sm:p-4 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700/30 space-y-3 flex flex-col justify-between">
                            <div class="space-y-2">
                                <div class="flex justify-between items-start gap-2">
                                    <span class="font-bold text-sm text-gray-900 dark:text-white leading-snug">Draft Synopsis Circulation</span>
                                    <div class="flex items-center gap-1 shrink-0">
                                        <x-submission-timeline-modal :form="$thesis->draftSynopsisCirculation" title="Draft Synopsis Timeline" />
                                        @if($thesis->draftSynopsisCirculation)
                                            <span class="shrink-0 bg-blue-100 text-blue-800 text-xs font-bold px-2 py-0.5 rounded whitespace-nowrap">Circulated</span>
                                        @else
                                            <span class="shrink-0 bg-gray-200 text-gray-700 text-xs font-bold px-2 py-0.5 rounded whitespace-nowrap">Not Circulated</span>
                                        @endif
                                    </div>
                                </div>

                                <p class="text-xs text-gray-500 dark:text-gray-400 leading-relaxed">
                                    Circulation of the Draft Synopsis to the PSPC and Supervisor(s) before Open Seminar.
                                </p>

                                @if($thesis->draftSynopsisCirculation)
                                    <div class="text-xs text-gray-600 dark:text-gray-300 space-y-1">
                                        <div>Circulated On: <strong>{{ $thesis->draftSynopsisCirculation->created_at->format('d-M-Y') }}</strong></div>
                                        <div>Total Comments: <strong>{{ $thesis->draftSynopsisCirculation->comments->count() }}</strong></div>
                                    </div>
                                @endif
                            </div>

                            <div class="pt-2 border-t border-gray-200 dark:border-gray-600">
                                @if(!$thesis->draftSynopsisCirculation)
                                    @if($role === 'main')
                                        <a href="{{ route('student.draft_synopsis.show', $thesis->id) }}" class="block w-full text-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs rounded-lg shadow transition">
                                            Circulate Draft Synopsis &rarr;
                                        </a>
                                    @else
                                        <button disabled class="w-full text-center px-4 py-2 bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 font-semibold text-xs rounded-lg border border-blue-200 dark:border-blue-800 cursor-not-allowed">
                                            Waiting for Draft Synopsis Circulation
                                        </button>
                                    @endif
                                @else
                                    <a href="{{ route('draft_synopsis.review', $thesis->draftSynopsisCirculation->id) }}" class="block w-full text-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs rounded-lg shadow transition">
                                        View Circulation Status &rarr;
                                    </a>
                                @endif
                            </div>
                        </div>
                    @endif

                    <!-- PTS-1 Milestone Card -->
                    <div class="p-2.5 sm:p-4 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700/30 space-y-3 flex flex-col justify-between">
                        <div class="space-y-2">
                            <div class="flex justify-between items-start gap-2">
                                <span class="font-bold text-sm text-gray-900 dark:text-white leading-snug">{{ $ptsPrefix }}-1: Open Seminar</span>
                                <div class="flex items-center gap-1 shrink-0">
                                    <x-submission-timeline-modal :form="$thesis->pts1Form" :title="$ptsPrefix . '-1 Submission Timeline'" />
                                    @if($thesis->pts1Form)
                                        @if($thesis->pts1Form->status === 'in_progress')
                                            <span class="shrink-0 bg-blue-100 text-blue-800 text-xs font-bold px-2.5 py-0.5 rounded whitespace-nowrap">In Progress</span>
                                        @elseif($thesis->pts1Form->status === 'reverted')
                                            <span class="shrink-0 bg-amber-100 text-amber-800 text-xs font-bold px-2.5 py-0.5 rounded whitespace-nowrap">Reverted</span>
                                        @elseif($thesis->pts1Form->status === 'approved')
                                            <span class="shrink-0 bg-emerald-100 text-emerald-800 text-xs font-bold px-2.5 py-0.5 rounded whitespace-nowrap">Approved</span>
                                        @elseif($thesis->pts1Form->status === 'rejected')
                                            <span class="shrink-0 bg-red-100 text-red-800 text-xs font-bold px-2.5 py-0.5 rounded whitespace-nowrap">Rejected</span>
                                        @endif
                                    @else
                                        <span class="shrink-0 bg-gray-200 text-gray-700 text-xs font-bold px-2.5 py-0.5 rounded whitespace-nowrap">Not Initiated</span>
                                    @endif
                                </div>
                            </div>

                            <p class="text-xs text-gray-500 dark:text-gray-400 leading-relaxed">
                                Recommendations of the {{ $degreeType }} Open Seminar.
                            </p>

                            @if($thesis->pts1Form)
                                @if($thesis->pts1Form->status === 'in_progress')
                                    <div class="text-xs text-gray-600 dark:text-gray-300 space-y-1">
                                        <div>Open Seminar: <strong>{{ $thesis->pts1Form->seminar_date?->format('d-m-Y') }}</strong> at {{ $thesis->pts1Form->seminar_time }}</div>
                                        <div class="text-blue-700 dark:text-blue-300 font-semibold">⏳ Under Review Stage: {{ $thesis->pts1Form->stage_label }}</div>
                                    </div>
                                @endif

                                @if($thesis->pts1Form->status === 'reverted')
                                    @can('viewReverted', $thesis->pts1Form)
                                        <div class="p-3 bg-amber-50 dark:bg-amber-950/40 border-l-4 border-amber-500 rounded-lg text-xs my-2">
                                            <div class="font-bold text-amber-900 dark:text-amber-200">
                                                ⚠️ Reverted by {{ $thesis->pts1Form->getRevertedByRoleLabel() }}
                                            </div>
                                        </div>
                                    @endcan
                                @endif
                            @endif
                        </div>

                        <div class="pt-2 border-t border-gray-200 dark:border-gray-600">
                            @if(!$thesis->pts1Form)
                                <button disabled class="w-full text-center px-4 py-2 bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 font-semibold text-xs rounded-lg border border-blue-200 dark:border-blue-800 cursor-not-allowed">
                                    Waiting for the Student to Initiate
                                </button>
                            @elseif(Gate::check('evaluate', $thesis->pts1Form))
                                @php
                                    $pts1EvalRoute = ($role === 'main' && $thesis->pts1Form->current_stage === 'main_supervisor')
                                        ? route('faculty.pts1.review', $thesis->pts1Form->id)
                                        : route('pts1.review', $thesis->pts1Form->id);
                                @endphp
                                <a href="{{ $pts1EvalRoute }}" class="block w-full text-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs rounded-lg shadow transition">
                                    Review & Endorse {{ $ptsPrefix }}-1 Form &rarr;
                                </a>
                            @elseif($thesis->pts1Form->status === 'in_progress' && Gate::check('view', $thesis->pts1Form))
                                <a href="{{ route('pts1.submitted', $thesis->pts1Form->id) }}" class="block w-full text-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs rounded-lg shadow transition">
                                    View Submitted Form &rarr;
                                </a>
                            @elseif(in_array($thesis->pts1Form->status, ['approved', 'rejected', 'reverted']))
                                @if($thesis->pts1Form->status !== 'reverted' || Gate::check('viewReverted', $thesis->pts1Form))
                                    <a href="{{ route($thesis->pts1Form->status === 'reverted' ? 'pts1.reverted' : 'pts1.show', $thesis->pts1Form->id) }}" 
                                       class="block w-full text-center px-4 py-2 {{ $thesis->pts1Form->status === 'reverted' ? 'bg-amber-600 hover:bg-amber-700' : ($thesis->pts1Form->status === 'rejected' ? 'bg-red-600 hover:bg-red-700' : 'bg-emerald-600 hover:bg-emerald-700') }} text-white font-bold text-xs rounded-lg shadow transition">
                                        {{ $thesis->pts1Form->status === 'reverted' ? 'View Reverted Form' : ($thesis->pts1Form->status === 'rejected' ? 'View Rejected Form' : 'View Approved Form') }} &rarr;
                                    </a>
                                @endif
                            @endif
                        </div>
                    </div>

                    <!-- PTS-2 Milestone Card -->
                    <div class="p-2.5 sm:p-4 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700/30 space-y-3 flex flex-col justify-between">
                        <div class="space-y-2">
                            <div class="flex justify-between items-start gap-2">
                                <span class="font-bold text-sm text-gray-900 dark:text-white leading-snug">{{ $ptsPrefix }}-2: Synopsis Submission</span>
                                <div class="flex items-center gap-1 shrink-0">
                                    @if(!$pts1Approved)
                                        <span class="shrink-0 bg-gray-200 text-gray-700 text-xs font-bold px-2 py-0.5 rounded whitespace-nowrap">🔒 Locked</span>
                                    @elseif($thesis->pts2Form)
                                        <x-submission-timeline-modal :form="$thesis->pts2Form" :title="$ptsPrefix . '-2 Submission Timeline'" />
                                        @if($thesis->pts2Form->status === 'in_progress')
                                            <span class="shrink-0 bg-blue-100 text-blue-800 text-xs font-bold px-2.5 py-0.5 rounded whitespace-nowrap">In Progress</span>
                                        @elseif($thesis->pts2Form->status === 'reverted')
                                            <span class="shrink-0 bg-amber-100 text-amber-800 text-xs font-bold px-2.5 py-0.5 rounded whitespace-nowrap">Reverted</span>
                                        @elseif($thesis->pts2Form->status === 'approved')
                                            <span class="shrink-0 bg-emerald-100 text-emerald-800 text-xs font-bold px-2.5 py-0.5 rounded whitespace-nowrap">Approved</span>
                                        @elseif($thesis->pts2Form->status === 'rejected')
                                            <span class="shrink-0 bg-red-100 text-red-800 text-xs font-bold px-2.5 py-0.5 rounded whitespace-nowrap">Rejected</span>
                                        @endif
                                    @else
                                        <span class="shrink-0 bg-gray-200 text-gray-700 text-xs font-bold px-2.5 py-0.5 rounded whitespace-nowrap">Not Initiated</span>
                                    @endif
                                </div>
                            </div>

                            <p class="text-xs text-gray-500 dark:text-gray-400 leading-relaxed">
                                Submission of {{ $degreeType }} Synopsis Report.
                            </p>

                            @if($thesis->pts2Form)
                                @if($thesis->pts2Form->status === 'in_progress')
                                    <div class="text-xs text-gray-600 dark:text-gray-300 space-y-1">
                                        <div class="text-blue-700 dark:text-blue-300 font-semibold">⏳ Under Review Stage: {{ $thesis->pts2Form->stage_label }}</div>
                                    </div>
                                @endif

                                @if($thesis->pts2Form->status === 'reverted')
                                    @can('viewReverted', $thesis->pts2Form)
                                        <div class="p-3 bg-amber-50 dark:bg-amber-950/40 border-l-4 border-amber-500 rounded-lg text-xs my-2">
                                            <div class="font-bold text-amber-900 dark:text-amber-200">
                                                ⚠️ Reverted by {{ $thesis->pts2Form->getRevertedByRoleLabel() }}
                                            </div>
                                        </div>
                                    @endcan
                                @endif
                            @endif
                        </div>

                        <div class="pt-2 border-t border-gray-200 dark:border-gray-600">
                            @if(!$pts1Approved)
                                <button disabled class="w-full text-center px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-400 dark:text-gray-500 font-bold text-xs rounded-lg cursor-not-allowed">
                                    Requires {{ $ptsPrefix }}-1 Approval
                                </button>
                            @elseif(!$thesis->pts2Form)
                                <button disabled class="w-full text-center px-4 py-2 bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 font-semibold text-xs rounded-lg border border-blue-200 dark:border-blue-800 cursor-not-allowed">
                                    Waiting for the Student to Initiate
                                </button>
                            @elseif(Gate::check('evaluate', $thesis->pts2Form))
                                @php
                                    $pts2EvalRoute = ($role === 'main' && $thesis->pts2Form->current_stage === 'main_supervisor')
                                        ? route('faculty.pts2.review', $thesis->pts2Form->id)
                                        : route('pts2.review', $thesis->pts2Form->id);
                                @endphp
                                <a href="{{ $pts2EvalRoute }}" class="block w-full text-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs rounded-lg shadow transition">
                                    Review & Endorse {{ $ptsPrefix }}-2 Form &rarr;
                                </a>
                            @elseif($thesis->pts2Form->status === 'in_progress' && Gate::check('view', $thesis->pts2Form))
                                <a href="{{ route('pts2.submitted', $thesis->pts2Form->id) }}" class="block w-full text-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs rounded-lg shadow transition">
                                    View Submitted Form &rarr;
                                </a>
                            @elseif(in_array($thesis->pts2Form->status, ['approved', 'rejected', 'reverted']))
                                @if($thesis->pts2Form->status !== 'reverted' || Gate::check('viewReverted', $thesis->pts2Form))
                                    <a href="{{ route($thesis->pts2Form->status === 'reverted' ? 'pts2.reverted' : 'pts2.show', $thesis->pts2Form->id) }}" 
                                       class="block w-full text-center px-4 py-2 {{ $thesis->pts2Form->status === 'reverted' ? 'bg-amber-600 hover:bg-amber-700' : ($thesis->pts2Form->status === 'rejected' ? 'bg-red-600 hover:bg-red-700' : 'bg-emerald-600 hover:bg-emerald-700') }} text-white font-bold text-xs rounded-lg shadow transition">
                                        {{ $thesis->pts2Form->status === 'reverted' ? 'View Reverted Form' : ($thesis->pts2Form->status === 'rejected' ? 'View Rejected Form' : 'View Approved Form') }} &rarr;
                                    </a>
                                @endif
                            @endif
                        </div>

                        {{-- PTS-2 Extension Sub-card --}}
                        @if($thesis->pts2Extension)
                            <div class="mt-3 p-2.5 rounded-lg border text-xs space-y-2 bg-purple-50 dark:bg-purple-950/40 border-purple-200 dark:border-purple-800">
                                <div class="flex items-center justify-between font-bold text-purple-900 dark:text-purple-200">
                                    <span>📅 {{ $ptsPrefix }}-2 Extension Requested</span>
                                    <div class="flex items-center gap-1">
                                        <x-submission-timeline-modal :form="$thesis->pts2Extension" :title="$ptsPrefix . '-2 Extension Timeline'" />
                                        @if($thesis->pts2Extension->status === 'approved')
                                            <span class="text-[10px] px-2 py-0.5 bg-emerald-100 text-emerald-800 rounded font-extrabold uppercase">
                                                ✓ Approved
                                            </span>
                                        @elseif($thesis->pts2Extension->status === 'rejected')
                                            <span class="text-[10px] px-2 py-0.5 bg-red-100 text-red-800 rounded font-extrabold uppercase">
                                                Rejected
                                            </span>
                                        @else
                                            <span class="text-[10px] px-2 py-0.5 bg-purple-200 text-purple-900 rounded font-extrabold uppercase">
                                                {{ $thesis->pts2Extension->status_label }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                <div class="text-[11px] text-purple-800 dark:text-purple-300">
                                    Requested Until: <strong>{{ $thesis->pts2Extension->extended_until_date?->format('d-M-Y') }}</strong>
                                </div>

                                @if($thesis->pts2Extension->status === 'in_progress')
                                    <div class="text-[11px] font-semibold text-purple-800 dark:text-purple-300">
                                        ⏳ Current Stage: {{ $thesis->pts2Extension->stage_label }}
                                    </div>
                                @endif

                                @can('evaluate', $thesis->pts2Extension)
                                    <div class="pt-1">
                                        <a href="{{ route('pts2_extension.review', $thesis->pts2Extension->id) }}" class="block w-full text-center px-3 py-1.5 bg-purple-600 hover:bg-purple-700 text-white font-bold text-xs rounded-lg shadow transition">
                                            Review {{ $ptsPrefix }}-2 Extension &rarr;
                                        </a>
                                    </div>
                                @elseif($thesis->pts2Extension->status === 'in_progress' && Gate::check('view', $thesis->pts2Extension))
                                    <div class="pt-1">
                                        <a href="{{ route('pts2_extension.show', $thesis->pts2Extension->id) }}" class="block w-full text-center px-3 py-1.5 bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-800 dark:text-gray-200 font-bold text-xs rounded-lg shadow transition">
                                            View Submitted Extension &rarr;
                                        </a>
                                    </div>
                                @elseif(in_array($thesis->pts2Extension->status, ['approved', 'rejected', 'reverted']) && Gate::check('view', $thesis->pts2Extension))
                                    <div class="pt-1">
                                        <a href="{{ route('pts2_extension.show', $thesis->pts2Extension->id) }}" 
                                           class="block w-full text-center px-3 py-1.5 {{ $thesis->pts2Extension->status === 'reverted' ? 'bg-amber-600 hover:bg-amber-700 text-white' : 'bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-800 dark:text-gray-200' }} font-bold text-xs rounded-lg shadow transition">
                                            {{ $thesis->pts2Extension->status === 'reverted' ? 'View Reverted Extension' : ($thesis->pts2Extension->status === 'rejected' ? 'View Rejected Extension' : 'View Approved Extension') }} &rarr;
                                        </a>
                                    </div>
                                @endcan
                            </div>
                        @endif
                    </div>

                    <!-- PTS-3 Milestone Card (Initiated by Main Supervisor) -->
                    <div class="p-2.5 sm:p-4 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700/30 space-y-3 flex flex-col justify-between">
                        <div class="space-y-2">
                            <div class="flex justify-between items-start gap-2">
                                <span class="font-bold text-sm text-gray-900 dark:text-white leading-snug">{{ $ptsPrefix }}-3: Panels of Examiners Submission</span>
                                <div class="flex items-center gap-1 shrink-0">
                                    @if(!$pts2Approved)
                                        <span class="shrink-0 bg-gray-200 text-gray-700 text-xs font-bold px-2 py-0.5 rounded whitespace-nowrap">🔒 Locked</span>
                                    @elseif($thesis->pts3Form)
                                        <x-submission-timeline-modal :form="$thesis->pts3Form" :title="$ptsPrefix . '-3 Submission Timeline'" />
                                        @if($thesis->pts3Form->status === 'in_progress')
                                            <span class="shrink-0 bg-blue-100 text-blue-800 text-xs font-bold px-2.5 py-0.5 rounded whitespace-nowrap">In Progress</span>
                                        @elseif($thesis->pts3Form->status === 'reverted')
                                            <span class="shrink-0 bg-amber-100 text-amber-800 text-xs font-bold px-2.5 py-0.5 rounded whitespace-nowrap">Reverted</span>
                                        @elseif($thesis->pts3Form->status === 'approved')
                                            <span class="shrink-0 bg-emerald-100 text-emerald-800 text-xs font-bold px-2.5 py-0.5 rounded whitespace-nowrap">Approved</span>
                                        @elseif($thesis->pts3Form->status === 'rejected')
                                            <span class="shrink-0 bg-red-100 text-red-800 text-xs font-bold px-2.5 py-0.5 rounded whitespace-nowrap">Rejected</span>
                                        @endif
                                    @else
                                        <span class="shrink-0 bg-gray-200 text-gray-700 text-xs font-bold px-2.5 py-0.5 rounded whitespace-nowrap">Not Initiated</span>
                                    @endif
                                </div>
                            </div>

                            <p class="text-xs text-gray-500 dark:text-gray-400 leading-relaxed">
                                Panel of Indian &amp; International Examiners and Oral Examination Board (OEB) recommendations.
                            </p>

                            @if($thesis->pts3Form)
                                @if($thesis->pts3Form->status === 'in_progress')
                                    <div class="text-xs text-gray-600 dark:text-gray-300 space-y-1">
                                        <div class="text-blue-700 dark:text-blue-300 font-semibold">⏳ Under Review Stage: {{ $thesis->pts3Form->stage_label }}</div>
                                    </div>
                                @endif

                                @if($thesis->pts3Form->status === 'reverted')
                                    @can('viewReverted', $thesis->pts3Form)
                                        <div class="p-3 bg-amber-50 dark:bg-amber-950/40 border-l-4 border-amber-500 rounded-lg text-xs my-2">
                                            <div class="font-bold text-amber-900 dark:text-amber-200">
                                                ⚠️ Reverted by {{ $thesis->pts3Form->getRevertedByRoleLabel() }}
                                            </div>
                                        </div>
                                    @endcan
                                @endif
                            @endif
                        </div>

                        <div class="pt-2 border-t border-gray-200 dark:border-gray-600">
                            @if(!$pts2Approved)
                                <button disabled class="w-full text-center px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-400 dark:text-gray-500 font-bold text-xs rounded-lg cursor-not-allowed">
                                    Requires {{ $ptsPrefix }}-2 Approval
                                </button>
                            @elseif(!$thesis->pts3Form)
                                @if($role === 'main')
                                    <a href="{{ route('faculty.pts3.create', $student->id) }}" class="block w-full text-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs rounded-lg shadow transition">
                                        Initiate {{ $ptsPrefix }}-3 Form &rarr;
                                    </a>
                                @else
                                    <button disabled class="w-full text-center px-4 py-2 bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 font-semibold text-xs rounded-lg border border-blue-200 dark:border-blue-800 cursor-not-allowed">
                                        Waiting for the Main Supervisor to Initiate
                                    </button>
                                @endif
                            @elseif($role === 'main' && $thesis->pts3Form->status === 'reverted' && $thesis->pts3Form->current_stage === 'main_supervisor')
                                <a href="{{ route('faculty.pts3.edit', $thesis->pts3Form->id) }}" class="block w-full text-center px-4 py-2 bg-amber-600 hover:bg-amber-700 text-white font-bold text-xs rounded-lg shadow transition">
                                    Edit Reverted {{ $ptsPrefix }}-3 Form &rarr;
                                </a>
                            @elseif(Gate::check('evaluate', $thesis->pts3Form))
                                <a href="{{ route('pts3.show', $thesis->pts3Form->id) }}" class="block w-full text-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs rounded-lg shadow transition">
                                    Review & Endorse {{ $ptsPrefix }}-3 Form &rarr;
                                </a>
                            @elseif($thesis->pts3Form->status === 'in_progress' && Gate::check('view', $thesis->pts3Form))
                                <a href="{{ route('pts3.show', $thesis->pts3Form->id) }}" class="block w-full text-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs rounded-lg shadow transition">
                                    View Submitted Form &rarr;
                                </a>
                            @elseif(in_array($thesis->pts3Form->status, ['approved', 'rejected', 'reverted']))
                                @if($thesis->pts3Form->status !== 'reverted' || Gate::check('viewReverted', $thesis->pts3Form))
                                    <a href="{{ route('pts3.show', $thesis->pts3Form->id) }}" class="block w-full text-center px-4 py-2 {{ $thesis->pts3Form->status === 'reverted' ? 'bg-amber-600 hover:bg-amber-700' : ($thesis->pts3Form->status === 'rejected' ? 'bg-red-600 hover:bg-red-700' : 'bg-emerald-600 hover:bg-emerald-700') }} text-white font-bold text-xs rounded-lg shadow transition">
                                        {{ $thesis->pts3Form->status === 'reverted' ? 'View Reverted Form' : ($thesis->pts3Form->status === 'rejected' ? 'View Rejected Form' : 'View Approved Form') }} &rarr;
                                    </a>
                                @endif
                            @endif
                        </div>
                    </div>

                    <!-- PTS-4 Milestone Card -->
                    <div class="p-2.5 sm:p-4 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700/30 space-y-3 flex flex-col justify-between">
                        <div class="space-y-2">
                            <div class="flex justify-between items-start gap-2">
                                <span class="font-bold text-sm text-gray-900 dark:text-white leading-snug">{{ $ptsPrefix }}-4: Thesis Submission</span>
                                <div class="flex items-center gap-1 shrink-0">
                                    @if(!$pts2Approved)
                                        <span class="shrink-0 bg-gray-200 text-gray-700 text-xs font-bold px-2 py-0.5 rounded whitespace-nowrap">🔒 Locked</span>
                                    @elseif($thesis->pts4Form)
                                        <x-submission-timeline-modal :form="$thesis->pts4Form" :title="$ptsPrefix . '-4 Submission Timeline'" />
                                        @if($thesis->pts4Form->status === 'in_progress')
                                            <span class="shrink-0 bg-blue-100 text-blue-800 text-xs font-bold px-2.5 py-0.5 rounded whitespace-nowrap">In Progress</span>
                                        @elseif($thesis->pts4Form->status === 'reverted')
                                            <span class="shrink-0 bg-amber-100 text-amber-800 text-xs font-bold px-2.5 py-0.5 rounded whitespace-nowrap">Reverted</span>
                                        @elseif($thesis->pts4Form->status === 'approved')
                                            <span class="shrink-0 bg-emerald-100 text-emerald-800 text-xs font-bold px-2.5 py-0.5 rounded whitespace-nowrap">Approved</span>
                                        @elseif($thesis->pts4Form->status === 'rejected')
                                            <span class="shrink-0 bg-red-100 text-red-800 text-xs font-bold px-2.5 py-0.5 rounded whitespace-nowrap">Rejected</span>
                                        @endif
                                    @else
                                        <span class="shrink-0 bg-gray-200 text-gray-700 text-xs font-bold px-2.5 py-0.5 rounded whitespace-nowrap">Not Initiated</span>
                                    @endif
                                </div>
                            </div>

                            <p class="text-xs text-gray-500 dark:text-gray-400 leading-relaxed">
                                Submission and sequential endorsement of the {{ $degreeType }} Thesis.
                            </p>

                            @if($thesis->pts4Form)
                                @if($thesis->pts4Form->status === 'in_progress')
                                    <div class="text-xs text-gray-600 dark:text-gray-300 space-y-1">
                                        <div class="text-blue-700 dark:text-blue-300 font-semibold">⏳ Under Review Stage: {{ $thesis->pts4Form->stage_label }}</div>
                                    </div>
                                @endif

                                @if($thesis->pts4Form->status === 'reverted' && $thesis->pts4Form->canUserViewRevertedForm($user))
                                    <div class="p-3 bg-amber-50 dark:bg-amber-950/40 border-l-4 border-amber-500 rounded-lg text-xs my-2">
                                        <div class="font-bold text-amber-900 dark:text-amber-200">
                                            ⚠️ Reverted by {{ $thesis->pts4Form->getRevertedByRoleLabel() }}
                                        </div>
                                    </div>
                                @endif
                            @endif
                        </div>

                        <div class="pt-2 border-t border-gray-200 dark:border-gray-600">
                            @if(!$pts2Approved)
                                <button disabled class="w-full text-center px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-400 dark:text-gray-500 font-bold text-xs rounded-lg cursor-not-allowed">
                                    Requires {{ $ptsPrefix }}-2 Approval
                                </button>
                            @elseif(!$thesis->pts4Form)
                                <button disabled class="w-full text-center px-4 py-2 bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 font-semibold text-xs rounded-lg border border-blue-200 dark:border-blue-800 cursor-not-allowed">
                                    Waiting for the Student to Initiate
                                </button>
                            @elseif($thesis->pts4Form->canUserEvaluate($user))
                                <a href="{{ route('pts4.review', $thesis->pts4Form->id) }}" class="block w-full text-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs rounded-lg shadow transition">
                                    Review & Endorse {{ $ptsPrefix }}-4 Form &rarr;
                                </a>
                            @elseif($thesis->pts4Form->status === 'in_progress' && $thesis->pts4Form->canUserView($user))
                                <a href="{{ route('pts4.submitted', $thesis->pts4Form->id) }}" class="block w-full text-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs rounded-lg shadow transition">
                                    View Submitted Form &rarr;
                                </a>
                            @elseif(in_array($thesis->pts4Form->status, ['approved', 'rejected', 'reverted']))
                                @if($thesis->pts4Form->status !== 'reverted' || $thesis->pts4Form->canUserViewRevertedForm($user))
                                    <a href="{{ route($thesis->pts4Form->status === 'reverted' ? 'pts4.reverted' : 'pts4.show', $thesis->pts4Form->id) }}" 
                                       class="block w-full text-center px-4 py-2 {{ $thesis->pts4Form->status === 'reverted' ? 'bg-amber-600 hover:bg-amber-700' : ($thesis->pts4Form->status === 'rejected' ? 'bg-red-600 hover:bg-red-700' : 'bg-emerald-600 hover:bg-emerald-700') }} text-white font-bold text-xs rounded-lg shadow transition">
                                        {{ $thesis->pts4Form->status === 'reverted' ? 'View Reverted Form' : ($thesis->pts4Form->status === 'rejected' ? 'View Rejected Form' : 'View Approved Form') }} &rarr;
                                    </a>
                                @endif
                            @endif
                        </div>

                        {{-- PTS-4 Extension Sub-card --}}
                        @if($thesis->pts4Extension)
                            <div class="mt-3 p-2.5 rounded-lg border text-xs space-y-2 bg-purple-50 dark:bg-purple-950/40 border-purple-200 dark:border-purple-800">
                                <div class="flex items-center justify-between font-bold text-purple-900 dark:text-purple-200">
                                    <span>📅 {{ $ptsPrefix }}-4 Extension Requested</span>
                                    <div class="flex items-center gap-1">
                                        <x-submission-timeline-modal :form="$thesis->pts4Extension" :title="$ptsPrefix . '-4 Extension Timeline'" />
                                        @if($thesis->pts4Extension->status === 'approved')
                                            <span class="text-[10px] px-2 py-0.5 bg-emerald-100 text-emerald-800 rounded font-extrabold uppercase">
                                                ✓ Approved
                                            </span>
                                        @elseif($thesis->pts4Extension->status === 'rejected')
                                            <span class="text-[10px] px-2 py-0.5 bg-red-100 text-red-800 rounded font-extrabold uppercase">
                                                Rejected
                                            </span>
                                        @else
                                            <span class="text-[10px] px-2 py-0.5 bg-purple-200 text-purple-900 rounded font-extrabold uppercase">
                                                {{ $thesis->pts4Extension->status_label }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                <div class="text-[11px] text-purple-800 dark:text-purple-300">
                                    Requested Until: <strong>{{ $thesis->pts4Extension->extended_until_date?->format('d-M-Y') }}</strong>
                                </div>

                                @if($thesis->pts4Extension->status === 'in_progress')
                                    <div class="text-[11px] font-semibold text-purple-800 dark:text-purple-300">
                                        ⏳ Current Stage: {{ $thesis->pts4Extension->stage_label }}
                                    </div>
                                @endif

                                @can('evaluate', $thesis->pts4Extension)
                                    <div class="pt-1">
                                        <a href="{{ route('pts4_extension.review', $thesis->pts4Extension->id) }}" class="block w-full text-center px-3 py-1.5 bg-purple-600 hover:bg-purple-700 text-white font-bold text-xs rounded-lg shadow transition">
                                            Review {{ $ptsPrefix }}-4 Extension &rarr;
                                        </a>
                                    </div>
                                @elseif($thesis->pts4Extension->status === 'in_progress' && Gate::check('view', $thesis->pts4Extension))
                                    <div class="pt-1">
                                        <a href="{{ route('pts4_extension.show', $thesis->pts4Extension->id) }}" class="block w-full text-center px-3 py-1.5 bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-800 dark:text-gray-200 font-bold text-xs rounded-lg shadow transition">
                                            View Submitted Extension &rarr;
                                        </a>
                                    </div>
                                @elseif(in_array($thesis->pts4Extension->status, ['approved', 'rejected', 'reverted']) && Gate::check('view', $thesis->pts4Extension))
                                    <div class="pt-1">
                                        <a href="{{ route('pts4_extension.show', $thesis->pts4Extension->id) }}" 
                                           class="block w-full text-center px-3 py-1.5 {{ $thesis->pts4Extension->status === 'reverted' ? 'bg-amber-600 hover:bg-amber-700 text-white' : 'bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-800 dark:text-gray-200' }} font-bold text-xs rounded-lg shadow transition">
                                            {{ $thesis->pts4Extension->status === 'reverted' ? 'View Reverted Extension' : ($thesis->pts4Extension->status === 'rejected' ? 'View Rejected Extension' : 'View Approved Extension') }} &rarr;
                                        </a>
                                    </div>
                                @endcan
                            </div>
                        @endif
                    </div>

                    <!-- PTS-5 Milestone Card -->
                    <div class="p-2.5 sm:p-4 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700/30 space-y-3 flex flex-col justify-between">
                        <div class="space-y-2">
                            <div class="flex justify-between items-start gap-2">
                                <span class="font-bold text-sm text-gray-900 dark:text-white leading-snug">
                                    {{ $ptsPrefix }}-5: Evaluation by External Examiners &amp; Oral Defense
                                </span>
                                <div class="flex items-center gap-1 shrink-0">
                                    @if($thesis->pts5Form)
                                        <x-submission-timeline-modal :form="$thesis->pts5Form" :title="$ptsPrefix . '-5 Submission Timeline'" />
                                    @endif
                                    @if(!$pts3Approved || !$pts4Approved)
                                        <span class="shrink-0 bg-gray-200 text-gray-700 text-xs font-bold px-2 py-0.5 rounded whitespace-nowrap">🔒 Locked</span>
                                    @elseif($thesis->pts5Form && $thesis->pts5Form->status === 'approved')
                                        <span class="shrink-0 bg-emerald-100 text-emerald-800 text-xs font-bold px-2.5 py-0.5 rounded whitespace-nowrap">Approved</span>
                                    @elseif($thesis->pts5Form && $thesis->pts5Form->status === 'in_progress')
                                        <span class="shrink-0 bg-blue-100 text-blue-800 text-xs font-bold px-2.5 py-0.5 rounded whitespace-nowrap">In Progress</span>
                                    @elseif($thesis->pts5Form && $thesis->pts5Form->status === 'rejected')
                                        <span class="shrink-0 bg-red-100 text-red-800 text-xs font-bold px-2.5 py-0.5 rounded whitespace-nowrap">Rejected</span>
                                    @else
                                        <span class="shrink-0 bg-blue-100 text-blue-800 text-xs font-bold px-2.5 py-0.5 rounded whitespace-nowrap">Available</span>
                                    @endif
                                </div>
                            </div>
                            <p class="text-xs text-gray-500 dark:text-gray-400 leading-relaxed">
                                Evaluation of {{ $degreeType }} thesis by external examiners and oral defense recommendations.
                            </p>
                        </div>

                        <div class="pt-2 border-t border-gray-200 dark:border-gray-600 space-y-2">
                            @if(!$pts3Approved || !$pts4Approved)
                                <button disabled class="w-full text-center px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-400 dark:text-gray-500 font-bold text-xs rounded-lg cursor-not-allowed">
                                    Requires {{ $ptsPrefix }}-3 &amp; {{ $ptsPrefix }}-4 Approval
                                </button>
                            @elseif($thesis->pts5Form && $thesis->pts5Form->status === 'approved')
                                <div class="flex items-center justify-between gap-2 pt-1">
                                    <span class="text-[11px] text-emerald-700 dark:text-emerald-300 font-bold">✓ {{ $ptsPrefix }}-5 Form Approved</span>
                                </div>
                            @elseif($thesis->pts5Form && $thesis->pts5Form->status === 'in_progress')
                                <div class="flex items-center justify-between gap-2 pt-1">
                                    <div class="text-[11px] text-blue-700 dark:text-blue-300 font-semibold py-1 leading-tight break-words">
                                        ⏳ Under Review Stage: {{ $thesis->pts5Form->stage_label }}
                                    </div>
                                </div>
                            @else
                                <button disabled class="w-full text-center px-4 py-2 bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 font-semibold text-xs rounded-lg border border-blue-200 dark:border-blue-800 cursor-not-allowed">
                                    Examiner Evaluation &amp; Defense Stage
                                </button>
                            @endif
                        </div>
                    </div>

                    <!-- PTS-6 Milestone Card -->
                    <div class="p-2.5 sm:p-4 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700/30 space-y-3 flex flex-col justify-between">
                        <div class="space-y-2">
                            <div class="flex justify-between items-start gap-2">
                                <span class="font-bold text-sm text-gray-900 dark:text-white leading-snug">
                                    {{ $ptsPrefix }}-6: Report of {{ $degreeType }} Thesis Oral Examination
                                </span>
                                <div class="flex items-center gap-1 shrink-0">
                                    @if($thesis->pts6Form)
                                        <x-submission-timeline-modal :form="$thesis->pts6Form" :title="$ptsPrefix . '-6 Submission Timeline'" />
                                    @endif
                                    @if(!$pts5Approved)
                                        <span class="shrink-0 bg-gray-200 text-gray-700 text-xs font-bold px-2 py-0.5 rounded whitespace-nowrap">🔒 Locked</span>
                                    @elseif($thesis->pts6Form && $thesis->pts6Form->status === 'approved')
                                        <span class="shrink-0 bg-emerald-100 text-emerald-800 text-xs font-bold px-2.5 py-0.5 rounded whitespace-nowrap">Approved</span>
                                    @elseif($thesis->pts6Form && $thesis->pts6Form->status === 'in_progress')
                                        <span class="shrink-0 bg-blue-100 text-blue-800 text-xs font-bold px-2.5 py-0.5 rounded whitespace-nowrap">In Progress</span>
                                    @else
                                        <span class="shrink-0 bg-blue-100 text-blue-800 text-xs font-bold px-2.5 py-0.5 rounded whitespace-nowrap">Final Stage</span>
                                    @endif
                                </div>
                            </div>
                            <p class="text-xs text-gray-500 dark:text-gray-400 leading-relaxed">
                                Report of {{ $degreeType }} Thesis Oral Examination and Final Degree Recommendation.
                            </p>
                        </div>
                        <div class="pt-2 border-t border-gray-200 dark:border-gray-600">
                            @if(!$pts5Approved)
                                <button disabled class="w-full text-center px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-400 dark:text-gray-500 font-bold text-xs rounded-lg cursor-not-allowed">
                                    Requires {{ $ptsPrefix }}-5 Approval
                                </button>
                            @elseif($thesis->pts6Form && $thesis->pts6Form->status === 'approved')
                                <div class="flex items-center justify-between gap-2 pt-1">
                                    <span class="text-[11px] text-emerald-700 dark:text-emerald-300 font-bold">✓ {{ $ptsPrefix }}-6 Form Approved</span>
                                </div>
                            @else
                                <button disabled class="w-full text-center px-4 py-2 bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 font-semibold text-xs rounded-lg border border-blue-200 dark:border-blue-800 cursor-not-allowed">
                                    Final Degree Award Stage
                                </button>
                            @endif
                        </div>
                    </div>

                </div>
            </div>
        @else
            <div class="p-6 bg-white dark:bg-gray-800 rounded-xl border border-dashed border-gray-200 dark:border-gray-700 text-center">
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">No active thesis registered for this student.</p>
            </div>
        @endif
    </div>
</div>

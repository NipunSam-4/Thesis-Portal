@props([
    'student',
    'user',
    'subTab' => 'standard', // 'standard', 'acting', 'vested'
    'isMsr' => false,
])

@php
    $pts1Prefix = $isMsr ? 'MSRTS-1' : 'PTS-1';
    $pts2Prefix = $isMsr ? 'MSRTS-2' : 'PTS-2';
    $accentColor = $isMsr ? 'blue' : 'indigo';
    $stageLabel = $student->getThesisStageLabel();
    $needsAction = $student->requiresActionFromUser($user);
@endphp

<div x-show="matchesFilter(@js($student->searchable_text), {{ $student->department_id ?? 'null' }})" class="mb-4 border border-gray-200 dark:border-gray-700 rounded-xl overflow-hidden shadow-sm bg-gray-50/50 dark:bg-gray-800/50">
    <!-- Student Header Card -->
    <div @click="toggleStudent({{ $student->id }})" class="p-4 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer flex flex-col md:flex-row md:items-center justify-between gap-4 transition">
        <div class="flex items-center space-x-3">
            <div class="w-10 h-10 rounded-full bg-{{ $accentColor }}-100 dark:bg-{{ $accentColor }}-900 text-{{ $accentColor }}-700 dark:text-{{ $accentColor }}-300 flex items-center justify-center font-bold text-base border border-{{ $accentColor }}-200 dark:border-{{ $accentColor }}-800">
                {{ substr($student->user->name ?? 'S', 0, 1) }}
            </div>
            <div>
                <h4 class="font-bold text-gray-900 dark:text-white text-base flex items-center flex-wrap gap-1">
                    <span>{{ $student->user->name }}</span>
                    <x-student-info-modal :student="$student" />
                    <span class="text-xs font-semibold text-gray-500 bg-gray-100 dark:bg-gray-700 px-2 py-0.5 rounded">
                        {{ $student->roll_number }}
                    </span>
                    <span class="text-xs font-semibold bg-{{ $accentColor }}-100 dark:bg-{{ $accentColor }}-900 text-{{ $accentColor }}-800 dark:text-{{ $accentColor }}-300 px-2 py-0.5 rounded">
                        Dept: {{ $student->department->code ?? 'N/A' }}
                    </span>
                </h4>
            </div>
        </div>

        <div class="flex items-center space-x-2 text-xs font-bold">
            @if($needsAction)
                <span class="inline-flex items-center gap-1 bg-amber-100 dark:bg-amber-900/60 text-amber-800 dark:text-amber-300 text-xs font-extrabold px-2.5 py-0.5 rounded-full border border-amber-300 dark:border-amber-700 shadow-sm animate-pulse">
                    Action Required
                </span>
            @endif

            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold shadow-sm border
                @if($stageLabel === 'Unregistered')
                    bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300 border-gray-200 dark:border-gray-600
                @else
                    bg-blue-100 text-blue-800 dark:bg-blue-900/60 dark:text-blue-300 border-blue-200 dark:border-blue-800
                @endif
            ">
                {{ $stageLabel }}
            </span>

            <svg class="w-5 h-5 text-gray-400 transform transition-transform" :class="expandedStudent === {{ $student->id }} ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
            </svg>
        </div>
    </div>

    <!-- Expandable Student Theses & PTS Form Breakdown -->
    <div x-show="expandedStudent === {{ $student->id }}" x-cloak class="p-2 sm:p-5 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 space-y-4 sm:space-y-5">
        @forelse($student->theses as $thesis)
            <div class="p-2.5 sm:p-4 bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 space-y-3 sm:space-y-4">
                <div class="border-b border-gray-100 dark:border-gray-700 pb-2">
                    <div class="text-[10px] uppercase font-bold text-{{ $accentColor }}-600 dark:text-{{ $accentColor }}-400">Thesis Title</div>
                    <h5 class="font-bold text-base text-gray-900 dark:text-white">{{ $thesis->title }}</h5>
                </div>

                <!-- Forms Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- PTS-1 Card -->
                    <div class="p-2.5 sm:p-4 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700/30 space-y-3">
                        <div class="flex justify-between items-center">
                            <span class="font-bold text-sm text-gray-900 dark:text-white">{{ $pts1Prefix }} (Open Seminar)</span>
                            <div class="flex items-center gap-1">
                                @if($subTab === 'acting')
                                    {{-- Acting DOAA View Rules --}}
                                    @if($thesis->pts1Form)
                                        @if($thesis->pts1Form->acting_doaa_email === $user->email)
                                            @if($thesis->pts1Form->status === 'in_progress')
                                                <span class="bg-blue-100 text-blue-800 text-xs font-bold px-2.5 py-0.5 rounded">In Progress</span>
                                            @elseif($thesis->pts1Form->status === 'reverted')
                                                <span class="bg-amber-100 text-amber-800 text-xs font-bold px-2.5 py-0.5 rounded">Reverted</span>
                                            @elseif($thesis->pts1Form->status === 'approved')
                                                <span class="bg-emerald-100 text-emerald-800 text-xs font-bold px-2.5 py-0.5 rounded">Approved</span>
                                            @elseif($thesis->pts1Form->status === 'rejected')
                                                <span class="bg-red-100 text-red-800 text-xs font-bold px-2.5 py-0.5 rounded">Rejected</span>
                                            @endif
                                        @elseif($thesis->pts1Form->status === 'approved')
                                            <span class="bg-emerald-100 text-emerald-800 text-xs font-bold px-2.5 py-0.5 rounded">✓ Approved</span>
                                        @else
                                            <span class="bg-gray-200 text-gray-700 text-xs font-bold px-2.5 py-0.5 rounded">Completed / N/A</span>
                                        @endif
                                    @else
                                        <span class="bg-gray-200 text-gray-700 text-xs font-bold px-2.5 py-0.5 rounded">Not Submitted</span>
                                    @endif
                                @elseif($subTab === 'vested')
                                    {{-- Vested DOAA View Rules (Full Submission History) --}}
                                    @if($thesis->pts1Form)
                                        <x-submission-timeline-modal :form="$thesis->pts1Form" :title="$pts1Prefix . ' Submission Timeline'" />
                                        @if($thesis->pts1Form->status === 'in_progress')
                                            <span class="bg-blue-100 text-blue-800 text-xs font-bold px-2.5 py-0.5 rounded">In Progress</span>
                                        @elseif($thesis->pts1Form->status === 'reverted')
                                            <span class="bg-amber-100 text-amber-800 text-xs font-bold px-2.5 py-0.5 rounded">Reverted</span>
                                        @elseif($thesis->pts1Form->status === 'approved')
                                            <span class="bg-emerald-100 text-emerald-800 text-xs font-bold px-2.5 py-0.5 rounded">Approved</span>
                                        @elseif($thesis->pts1Form->status === 'rejected')
                                            <span class="bg-red-100 text-red-800 text-xs font-bold px-2.5 py-0.5 rounded">Rejected</span>
                                        @endif
                                    @else
                                        <span class="bg-gray-200 text-gray-700 text-xs font-bold px-2.5 py-0.5 rounded">Not Submitted</span>
                                    @endif
                                @else
                                    {{-- Standard Global Authority View --}}
                                    @if($thesis->pts1Form)
                                        <x-submission-timeline-modal :form="$thesis->pts1Form" :title="$pts1Prefix . ' Submission Timeline'" />
                                        @if($thesis->pts1Form->status === 'in_progress')
                                            <span class="bg-blue-100 text-blue-800 text-xs font-bold px-2.5 py-0.5 rounded">In Progress</span>
                                        @elseif($thesis->pts1Form->status === 'reverted')
                                            <span class="bg-amber-100 text-amber-800 text-xs font-bold px-2.5 py-0.5 rounded">Reverted</span>
                                        @elseif($thesis->pts1Form->status === 'approved')
                                            <span class="bg-emerald-100 text-emerald-800 text-xs font-bold px-2.5 py-0.5 rounded">Approved</span>
                                        @elseif($thesis->pts1Form->status === 'rejected')
                                            <span class="bg-red-100 text-red-800 text-xs font-bold px-2.5 py-0.5 rounded">Rejected</span>
                                        @endif
                                    @else
                                        <span class="bg-gray-200 text-gray-700 text-xs font-bold px-2.5 py-0.5 rounded">Not Submitted</span>
                                    @endif
                                @endif
                            </div>
                        </div>

                        @if($thesis->pts1Form)
                            @if($subTab === 'acting' && $thesis->pts1Form->acting_doaa_email !== $user->email)
                                {{-- For Previous / Other PTS Forms in Acting DOAA subtab: only show approved state --}}
                                <div class="text-xs text-gray-500 dark:text-gray-400 py-1">
                                    <span class="font-medium">Previous Milestone Status:</span> <span class="font-bold text-emerald-600 dark:text-emerald-400">Approved</span>
                                </div>
                            @else
                                <div class="text-xs text-gray-600 dark:text-gray-300 space-y-1">
                                    <div>Open Seminar: <strong>{{ $thesis->pts1Form->seminar_date?->format('d-m-Y') }}</strong> at {{ $thesis->pts1Form->seminar_time }}</div>
                                    <div class="text-{{ $accentColor }}-600 dark:text-{{ $accentColor }}-400 font-semibold">Current Stage: {{ $thesis->pts1Form->stage_label }}</div>
                                </div>

                                @if($thesis->pts1Form->status === 'reverted' && $thesis->pts1Form->canUserViewRevertedForm($user))
                                    <div class="p-3 bg-amber-50 dark:bg-amber-950/40 border-l-4 border-amber-500 rounded-lg text-xs my-2">
                                        <div class="font-bold text-amber-900 dark:text-amber-200">
                                            ⚠️ Reverted by {{ $thesis->pts1Form->getRevertedByRoleLabel() }}
                                        </div>
                                    </div>
                                @endif

                                {{-- Action Button for Acting / Vested DOAA when appointed --}}
                                @if(($user->isActingApprovalAuthority() && ($thesis->pts1Form->acting_doaa_email === $user->email || $thesis->pts1Form->vested_doaa_email === $user->email) && $thesis->pts1Form->status === 'in_progress' && $thesis->pts1Form->current_stage === 'doaa') || ($user->isDoaa() && $thesis->pts1Form->status === 'in_progress' && $thesis->pts1Form->current_stage === 'doaa'))
                                    <div class="pt-2">
                                        <a href="{{ route('pts1.review_endorse', $thesis->pts1Form->id) }}" class="block w-full text-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs rounded-lg shadow transition">
                                            Review & Endorse {{ $pts1Prefix }} Form &rarr;
                                        </a>
                                    </div>
                                @elseif($user->isSectionOfficer() && $thesis->pts1Form->status === 'in_progress' && $thesis->pts1Form->current_stage === 'section_officer')
                                    <div class="pt-2">
                                        <a href="{{ route('pts1.review_endorse', $thesis->pts1Form->id) }}" class="block w-full text-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs rounded-lg shadow transition">
                                            Review & Endorse {{ $pts1Prefix }} Form &rarr;
                                        </a>
                                    </div>
                                @elseif(in_array($thesis->pts1Form->status, ['approved', 'rejected', 'reverted']) || $subTab === 'vested')
                                    @if($thesis->pts1Form->status !== 'reverted' || $thesis->pts1Form->canUserViewRevertedForm($user))
                                        <div class="pt-2">
                                            <a href="{{ route('pts1.show', $thesis->pts1Form->id) }}" 
                                               class="block w-full text-center px-4 py-2 {{ $thesis->pts1Form->status === 'reverted' ? 'bg-amber-600 hover:bg-amber-700' : ($thesis->pts1Form->status === 'rejected' ? 'bg-red-600 hover:bg-red-700' : ($thesis->pts1Form->status === 'approved' ? 'bg-emerald-600 hover:bg-emerald-700' : 'bg-indigo-600 hover:bg-indigo-700')) }} text-white font-bold text-xs rounded-lg shadow transition">
                                                {{ $thesis->pts1Form->status === 'reverted' ? 'View Reverted ' . $pts1Prefix . ' Form' : ($thesis->pts1Form->status === 'rejected' ? 'View Rejected ' . $pts1Prefix . ' Form' : ($thesis->pts1Form->status === 'approved' ? 'View Submitted ' . $pts1Prefix . ' Form' : 'View ' . $pts1Prefix . ' Form Details')) }} &rarr;
                                            </a>
                                        </div>
                                    @endif
                                @endif
                            @endif
                        @endif
                    </div>

                    <!-- PTS-2 Card -->
                    <div class="p-2.5 sm:p-4 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700/30 space-y-3">
                        <div class="flex justify-between items-center">
                            <span class="font-bold text-sm text-gray-900 dark:text-white">{{ $pts2Prefix }} (Synopsis Report)</span>
                            <div class="flex items-center gap-1">
                                @if($subTab === 'acting')
                                    @if(!$thesis->pts1Form || $thesis->pts1Form->status !== 'approved')
                                        <span class="bg-gray-200 text-gray-700 text-xs font-bold px-2 py-0.5 rounded">🔒 Locked</span>
                                    @elseif($thesis->pts2Form)
                                        @if($thesis->pts2Form->acting_doaa_email === $user->email)
                                            <x-submission-timeline-modal :form="$thesis->pts2Form" :title="$pts2Prefix . ' Submission Timeline'" />
                                            @if($thesis->pts2Form->status === 'in_progress')
                                                <span class="bg-purple-100 text-purple-800 text-xs font-bold px-2.5 py-0.5 rounded">In Progress</span>
                                            @elseif($thesis->pts2Form->status === 'reverted')
                                                <span class="bg-amber-100 text-amber-800 text-xs font-bold px-2.5 py-0.5 rounded">Reverted</span>
                                            @elseif($thesis->pts2Form->status === 'approved')
                                                <span class="bg-emerald-100 text-emerald-800 text-xs font-bold px-2.5 py-0.5 rounded">Approved</span>
                                            @elseif($thesis->pts2Form->status === 'rejected')
                                                <span class="bg-red-100 text-red-800 text-xs font-bold px-2.5 py-0.5 rounded">Rejected</span>
                                            @endif
                                        @elseif($thesis->pts2Form->status === 'approved')
                                            <span class="bg-emerald-100 text-emerald-800 text-xs font-bold px-2.5 py-0.5 rounded">✓ Approved</span>
                                        @else
                                            <span class="bg-gray-200 text-gray-700 text-xs font-bold px-2.5 py-0.5 rounded">Completed / N/A</span>
                                        @endif
                                    @else
                                        <span class="bg-gray-200 text-gray-700 text-xs font-bold px-2.5 py-0.5 rounded">Not Submitted</span>
                                    @endif
                                @elseif($subTab === 'vested')
                                    @if($thesis->pts2Form)
                                        <x-submission-timeline-modal :form="$thesis->pts2Form" :title="$pts2Prefix . ' Submission Timeline'" />
                                        @if($thesis->pts2Form->status === 'in_progress')
                                            <span class="bg-purple-100 text-purple-800 text-xs font-bold px-2.5 py-0.5 rounded">In Progress</span>
                                        @elseif($thesis->pts2Form->status === 'reverted')
                                            <span class="bg-amber-100 text-amber-800 text-xs font-bold px-2.5 py-0.5 rounded">Reverted</span>
                                        @elseif($thesis->pts2Form->status === 'approved')
                                            <span class="bg-emerald-100 text-emerald-800 text-xs font-bold px-2.5 py-0.5 rounded">Approved</span>
                                        @elseif($thesis->pts2Form->status === 'rejected')
                                            <span class="bg-red-100 text-red-800 text-xs font-bold px-2.5 py-0.5 rounded">Rejected</span>
                                        @endif
                                    @else
                                        <span class="bg-gray-200 text-gray-700 text-xs font-bold px-2.5 py-0.5 rounded">Not Submitted</span>
                                    @endif
                                @else
                                    <x-submission-timeline-modal :form="$thesis->pts2Form" :title="$pts2Prefix . ' Submission Timeline'" />
                                    @if(!$thesis->pts1Form || $thesis->pts1Form->status !== 'approved')
                                        <span class="bg-gray-200 text-gray-700 text-xs font-bold px-2 py-0.5 rounded">🔒 Locked</span>
                                    @elseif($thesis->pts2Form)
                                        @if($thesis->pts2Form->status === 'in_progress')
                                            <span class="bg-purple-100 text-purple-800 text-xs font-bold px-2.5 py-0.5 rounded">In Progress</span>
                                        @elseif($thesis->pts2Form->status === 'reverted')
                                            <span class="bg-amber-100 text-amber-800 text-xs font-bold px-2.5 py-0.5 rounded">Reverted</span>
                                        @elseif($thesis->pts2Form->status === 'approved')
                                            <span class="bg-emerald-100 text-emerald-800 text-xs font-bold px-2.5 py-0.5 rounded">Approved</span>
                                        @elseif($thesis->pts2Form->status === 'rejected')
                                            <span class="bg-red-100 text-red-800 text-xs font-bold px-2.5 py-0.5 rounded">Rejected</span>
                                        @endif
                                    @else
                                        <span class="bg-gray-200 text-gray-700 text-xs font-bold px-2.5 py-0.5 rounded">Not Submitted</span>
                                    @endif
                                @endif
                            </div>
                        </div>

                        @if($thesis->pts2Form)
                            @if($subTab === 'acting' && $thesis->pts2Form->acting_doaa_email !== $user->email)
                                <div class="text-xs text-gray-500 dark:text-gray-400 py-1">
                                    <span class="font-medium">Previous Milestone Status:</span> <span class="font-bold text-emerald-600 dark:text-emerald-400">Approved</span>
                                </div>
                            @else
                                <div class="text-xs text-gray-600 dark:text-gray-300 space-y-1">
                                    <div class="text-purple-600 dark:text-purple-400 font-semibold">Current Stage: {{ $thesis->pts2Form->stage_label }}</div>
                                </div>

                                @if($thesis->pts2Form->status === 'reverted' && $thesis->pts2Form->canUserViewRevertedForm($user))
                                    <div class="p-3 bg-amber-50 dark:bg-amber-950/40 border-l-4 border-amber-500 rounded-lg text-xs my-2">
                                        <div class="font-bold text-amber-900 dark:text-amber-200">
                                            ⚠️ Reverted by {{ $thesis->pts2Form->getRevertedByRoleLabel() }}
                                        </div>
                                    </div>
                                @endif

                                {{-- Action Button for Acting / Vested DOAA when appointed --}}
                                @if(($user->isActingApprovalAuthority() && ($thesis->pts2Form->acting_doaa_email === $user->email || $thesis->pts2Form->vested_doaa_email === $user->email) && $thesis->pts2Form->status === 'in_progress' && $thesis->pts2Form->current_stage === 'doaa') || ($user->isDoaa() && $thesis->pts2Form->status === 'in_progress' && $thesis->pts2Form->current_stage === 'doaa'))
                                    <div class="pt-2">
                                        <a href="{{ route('pts2.review_endorse', $thesis->pts2Form->id) }}" class="block w-full text-center px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white font-bold text-xs rounded-lg shadow transition">
                                            Review & Endorse {{ $pts2Prefix }} Form &rarr;
                                        </a>
                                    </div>
                                @elseif(($user->isAcademicOffice() || ($user->isGlobalAuthority() && !$user->isActingApprovalAuthority())) && $thesis->pts2Form->status === 'in_progress' && $thesis->pts2Form->current_stage === 'academic_office')
                                    <div class="pt-2">
                                        <a href="{{ route('pts2.review_endorse', $thesis->pts2Form->id) }}" class="block w-full text-center px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white font-bold text-xs rounded-lg shadow transition">
                                            Review & Endorse {{ $pts2Prefix }} Form &rarr;
                                        </a>
                                    </div>
                                @elseif(in_array($thesis->pts2Form->status, ['approved', 'rejected', 'reverted']) || $subTab === 'vested')
                                    @if($thesis->pts2Form->status !== 'reverted' || $thesis->pts2Form->canUserViewRevertedForm($user))
                                        <div class="pt-2">
                                            <a href="{{ route('pts2.show', $thesis->pts2Form->id) }}" 
                                               class="block w-full text-center px-4 py-2 {{ $thesis->pts2Form->status === 'reverted' ? 'bg-amber-600 hover:bg-amber-700' : ($thesis->pts2Form->status === 'rejected' ? 'bg-red-600 hover:bg-red-700' : ($thesis->pts2Form->status === 'approved' ? 'bg-emerald-600 hover:bg-emerald-700' : 'bg-purple-600 hover:bg-purple-700')) }} text-white font-bold text-xs rounded-lg shadow transition">
                                                {{ $thesis->pts2Form->status === 'reverted' ? 'View Reverted ' . $pts2Prefix . ' Form' : ($thesis->pts2Form->status === 'rejected' ? 'View Rejected ' . $pts2Prefix . ' Form' : ($thesis->pts2Form->status === 'approved' ? 'View Submitted ' . $pts2Prefix . ' Form' : 'View ' . $pts2Prefix . ' Form Details')) }} &rarr;
                                            </a>
                                        </div>
                                    @endif
                                @endif
                            @endif
                        @endif

                        {{-- PTS-2 Extension Block --}}
                        @if($thesis->pts2Extension)
                            <div class="mt-3 p-2.5 rounded-lg border text-xs space-y-2 bg-purple-50 dark:bg-purple-950/40 border-purple-200 dark:border-purple-800">
                                <div class="flex items-center justify-between font-bold text-purple-900 dark:text-purple-200">
                                    <span>📅 {{ $pts2Prefix }} Extension Requested</span>
                                    <div class="flex items-center gap-1">
                                        @if($subTab === 'acting')
                                            @if($thesis->pts2Extension->acting_doaa_email === $user->email)
                                                <x-submission-timeline-modal :form="$thesis->pts2Extension" :title="$pts2Prefix . ' Extension Timeline'" />
                                                <span class="text-[10px] px-2 py-0.5 bg-purple-200 text-purple-900 rounded font-extrabold uppercase">
                                                    {{ str_replace('_', ' ', $thesis->pts2Extension->status) }}
                                                </span>
                                            @elseif($thesis->pts2Extension->status === 'approved')
                                                <span class="text-[10px] px-2 py-0.5 bg-emerald-100 text-emerald-800 rounded font-extrabold uppercase">
                                                    ✓ Approved
                                                </span>
                                            @else
                                                <span class="text-[10px] px-2 py-0.5 bg-gray-200 text-gray-800 rounded font-extrabold uppercase">
                                                    {{ str_replace('_', ' ', $thesis->pts2Extension->status) }}
                                                </span>
                                            @endif
                                        @else
                                            <x-submission-timeline-modal :form="$thesis->pts2Extension" :title="$pts2Prefix . ' Extension Timeline'" />
                                            <span class="text-[10px] px-2 py-0.5 bg-purple-200 text-purple-900 rounded font-extrabold uppercase">
                                                {{ str_replace('_', ' ', $thesis->pts2Extension->status) }}
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

                                @if(($user->isActingApprovalAuthority() && ($thesis->pts2Extension->acting_doaa_email === $user->email || $thesis->pts2Extension->vested_doaa_email === $user->email) && $thesis->pts2Extension->current_stage === 'doaa' && $thesis->pts2Extension->status === 'in_progress') || ($user->isDoaa() && $thesis->pts2Extension->current_stage === 'doaa' && $thesis->pts2Extension->status === 'in_progress'))
                                    <div class="pt-1">
                                        <a href="{{ route('pts2_extension.review', $thesis->pts2Extension->id) }}" class="block w-full text-center px-3 py-1.5 bg-purple-600 hover:bg-purple-700 text-white font-bold text-xs rounded-lg shadow transition">
                                            Review & Evaluate {{ $pts2Prefix }} Extension &rarr;
                                        </a>
                                    </div>
                                @elseif(in_array($thesis->pts2Extension->status, ['approved', 'rejected', 'reverted']) || $subTab === 'vested')
                                    @if($thesis->pts2Extension->status !== 'reverted' || $thesis->pts2Extension->canUserViewRevertedForm($user))
                                        <div class="pt-1">
                                            <a href="{{ route('pts2_extension.show', $thesis->pts2Extension->id) }}" 
                                               class="block w-full text-center px-3 py-1.5 {{ $thesis->pts2Extension->status === 'reverted' ? 'bg-amber-600 hover:bg-amber-700 text-white' : 'bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-800 dark:text-gray-200' }} font-bold text-xs rounded-lg shadow transition">
                                                {{ $thesis->pts2Extension->status === 'reverted' ? 'View Reverted Extension' : ($thesis->pts2Extension->status === 'in_progress' ? 'View Extension Details' : 'View Extension Details') }} &rarr;
                                            </a>
                                        </div>
                                    @endif
                                @endif
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <p class="text-xs text-gray-500 dark:text-gray-400">No theses registered for this student.</p>
        @endforelse
    </div>
</div>

<x-app-layout>
    @php
        $formPrefix = isset($student) && $student->isPhd() ? 'PTS' : 'MSRTS';
    @endphp
    <div class="py-6" x-data="pts2EndorseForm()">
        <div class="max-w-5xl mx-auto px-2 sm:px-6 lg:px-8 space-y-4">

            <!-- Top Back to Dashboard Button -->
            <div>
                <x-back-to-dashboard-button />
            </div>

            <!-- Page Header Card -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-5 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div class="space-y-1">
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white leading-tight">
                        {{ __("Review & Endorse {$formPrefix}-2 Synopsis Form") }}
                    </h2>
                </div>
            </div>

            <!-- Flash Session Alerts -->
            @if(session('success'))
                <div x-data="{ show: true }" x-show="show" x-transition class="p-4 bg-emerald-100 dark:bg-emerald-900/40 border-l-4 border-emerald-500 text-emerald-800 dark:text-emerald-200 rounded-xl shadow-sm flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <span class="font-bold text-sm">{{ session('success') }}</span>
                    </div>
                </div>
            @endif

            @if(session('warning'))
                <div x-data="{ show: true }" x-show="show" x-transition class="p-4 bg-amber-100 dark:bg-amber-900/40 border-l-4 border-amber-500 text-amber-800 dark:text-amber-200 rounded-xl shadow-sm flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <svg class="w-5 h-5 text-amber-600 dark:text-amber-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                        <span class="font-bold text-sm">{{ session('warning') }}</span>
                    </div>
                </div>
            @endif

            @if(session('error'))
                <div x-data="{ show: true }" x-show="show" x-transition class="p-4 bg-red-100 dark:bg-red-900/40 border-l-4 border-red-500 text-red-800 dark:text-red-200 rounded-xl shadow-sm flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <svg class="w-5 h-5 text-red-600 dark:text-red-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <span class="font-bold text-sm">{{ session('error') }}</span>
                    </div>
                </div>
            @endif

            @if(session('info'))
                <div x-data="{ show: true }" x-show="show" x-transition class="p-4 bg-blue-100 dark:bg-blue-900/40 border-l-4 border-blue-500 text-blue-800 dark:text-blue-200 rounded-xl shadow-sm flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <svg class="w-5 h-5 text-blue-600 dark:text-blue-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <span class="font-bold text-sm">{{ session('info') }}</span>
                    </div>
                </div>
            @endif

            <!-- Error Alerts -->
            @if($errors->any())
                <div class="p-4 bg-red-100 border-l-4 border-red-500 text-red-700 rounded-lg shadow-sm">
                    <div class="font-bold">Please correct the validation errors below:</div>
                    <ul class="mt-1 list-disc list-inside text-sm">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Common Form Information Sections (1 to 5) -->
            @include('pts2.form_info', ['pts2' => $pts2, 'viewPerspective' => 'authority'])

            @php
                $stage = $pts2->current_stage;
                $stageWeights = [
                    'main_supervisor' => 1,
                    'co_supervisors'  => 2,
                    'academic_office' => 3,
                    'doaa'            => 4,
                    'completed'       => 5,
                ];

                $currentWeight = $stageWeights[$stage] ?? 1;

                $passedMainSupervisor = ($currentWeight > 1) || !is_null($pts2->main_supervisor_submitted_at) || !is_null($pts2->main_supervisor_recommendation);
                $passedCoSupervisors  = ($currentWeight > 2) || !is_null($pts2->co_supervisors_submitted_at);
                $passedAcademicOffice = ($currentWeight > 3) || !is_null($pts2->academic_office_submitted_at) || !is_null($pts2->academic_office_is_verified);
                $passedDoaa           = ($currentWeight > 4) || !is_null($pts2->doaa_approval) || !is_null($pts2->doaa_submitted_at);
            @endphp

            <!-- Section 6: Authority Recommendations & Remarks -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 space-y-6">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white border-b border-gray-100 dark:border-gray-700 pb-2">
                    6. Authority Recommendations &amp; Remarks
                </h3>

                <div class="space-y-4">
                    <!-- Main Supervisor Evaluation -->
                    <div class="p-4 bg-indigo-50/70 dark:bg-indigo-950/40 border-l-4 border-indigo-500 rounded-xl space-y-2">
                        <div class="flex items-center justify-between font-bold text-indigo-900 dark:text-indigo-200 gap-2 sm:gap-4">
                            <span class="text-s">Main Supervisor @if(isset($mainSupervisor))<span class="block sm:inline text-xs font-normal text-indigo-700 dark:text-indigo-300 mt-0.5 sm:mt-0">({{ $mainSupervisor->name }})</span>@endif</span>
                            @if(!is_null($pts2->main_supervisor_recommendation))
                                <span class="font-bold text-xs whitespace-nowrap shrink-0 {{ $pts2->main_supervisor_recommendation ? 'text-emerald-600' : 'text-red-600' }}">
                                    {{ $pts2->main_supervisor_recommendation ? '✓ Recommended' : '❌ Not Recommended' }}
                                </span>
                            @endif
                        </div>
                        <div class="text-xs text-gray-700 dark:text-gray-300">
                            <strong>Student Comment:</strong>
                            @if($pts2->main_supervisor_student_comment)
                                <p class="italic bg-white dark:bg-gray-800 p-2.5 rounded-lg border border-indigo-100 dark:border-indigo-900 mt-0.5 whitespace-pre-wrap break-words [overflow-wrap:anywhere]">{{ trim($pts2->main_supervisor_student_comment) }}</p>
                            @else
                                <div class="flex items-center space-x-2 text-xs text-gray-600 dark:text-gray-500 bg-gray-200/60 dark:bg-gray-900/40 p-2.5 rounded-lg border border-dashed border-gray-200 dark:border-gray-700/60 mt-0.5">
                                    <svg class="w-4 h-4 text-gray-400 dark:text-gray-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 4.418 9 8z"></path>
                                    </svg>
                                    <span class="italic font-normal">Comment not provided</span>
                                </div>
                            @endif
                        </div>
                        <div class="text-xs text-gray-700 dark:text-gray-300 pt-0.5">
                            <strong>Recommendation Remark:</strong>
                            @if($pts2->main_supervisor_confidential_remark)
                                <p class="italic bg-white dark:bg-gray-800 p-2.5 rounded-lg border border-indigo-100 dark:border-indigo-900 mt-0.5 whitespace-pre-wrap break-words [overflow-wrap:anywhere]">{{ trim($pts2->main_supervisor_confidential_remark) }}</p>
                            @else
                                <div class="flex items-center space-x-2 text-xs text-gray-600 dark:text-gray-500 bg-gray-200/60 dark:bg-gray-900/40 p-2.5 rounded-lg border border-dashed border-gray-200 dark:border-gray-700/60 mt-0.5">
                                    <svg class="w-4 h-4 text-gray-400 dark:text-gray-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 4.418 9 8z"></path>
                                    </svg>
                                    <span class="italic font-normal">Remark not provided</span>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Co-Supervisors Recommendations (Slots 1..10) -->
                    @php
                        $hasAnyCoRecommended = false;
                        for ($i = 1; $i <= 10; $i++) {
                            $idCol = "co_supervisor_{$i}_id";
                            $remCol = "co_supervisor_{$i}_confidential_remark";
                            $recCol = "co_supervisor_{$i}_recommendation";
                            $comCol = "co_supervisor_{$i}_student_comment";
                            if ($pts2->$idCol && (!is_null($pts2->$remCol) || !is_null($pts2->$recCol) || !is_null($pts2->$comCol) || $passedCoSupervisors)) {
                                $hasAnyCoRecommended = true;
                                break;
                            }
                        }
                    @endphp

                    @if($hasAnyCoRecommended)
                        <div class="p-4 bg-blue-50/70 dark:bg-blue-950/40 border-l-4 border-blue-500 rounded-xl space-y-3">
                            <h5 class="text-s font-bold text-blue-900 dark:text-blue-200">Co-Supervisor</h5>
                            
                            @for($i = 1; $i <= 10; $i++)
                                @php
                                    $idCol = "co_supervisor_{$i}_id";
                                    $recCol = "co_supervisor_{$i}_recommendation";
                                    $remCol = "co_supervisor_{$i}_confidential_remark";
                                    $comCol = "co_supervisor_{$i}_student_comment";
                                    $coUser = $coSupervisors[$i] ?? ($pts2->$idCol ? \App\Models\User::find($pts2->$idCol) : null);
                                @endphp

                                @if($coUser && (!is_null($pts2->$remCol) || !is_null($pts2->$recCol) || !is_null($pts2->$comCol) || $passedCoSupervisors))
                                    @php
                                        $isExt = $coUser->isExternalSupervisor();
                                        $roleTitle = $student ? $student->getSupervisorRoleTitle($coUser) : ($isExt ? 'External Supervisor' : "Co-Supervisor {$i}");
                                        $inst = ($isExt && $coUser->externalSupervisorProfile?->affiliated_institute) ? ' - ' . $coUser->externalSupervisorProfile->affiliated_institute : '';
                                    @endphp
                                    <div class="text-xs space-y-1 pt-1 {{ $i > 1 ? 'border-t border-blue-100 dark:border-blue-900' : '' }}">
                                        <div class="flex items-center justify-between font-semibold gap-2 sm:gap-4">
                                            <span>{{ $roleTitle }} <span class="block sm:inline text-xs font-normal text-gray-500 dark:text-gray-400 mt-0.5 sm:mt-0">({{ $coUser->name }}{{ $inst }})</span>:</span>
                                            <span class="font-bold whitespace-nowrap shrink-0 {{ $pts2->$recCol ? 'text-emerald-600' : 'text-red-600' }}">
                                                {{ $pts2->$recCol ? '✓ Recommended' : '❌ Not Recommended' }}
                                            </span>
                                        </div>
                                        <div class="pt-1">
                                            <strong>Student Comment:</strong>
                                            @if($pts2->$comCol)
                                                <p class="italic text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 p-2.5 rounded-lg border border-blue-100 dark:border-blue-900 mt-0.5 whitespace-pre-wrap break-words [overflow-wrap:anywhere]">{{ trim($pts2->$comCol) }}</p>
                                            @else
                                                <div class="flex items-center space-x-2 text-xs text-gray-600 dark:text-gray-500 bg-gray-200/60 dark:bg-gray-900/40 p-2.5 rounded-lg border border-dashed border-gray-200 dark:border-gray-700/60 mt-0.5">
                                                    <svg class="w-4 h-4 text-gray-400 dark:text-gray-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 4.418 9 8z"></path>
                                                    </svg>
                                                    <span class="italic font-normal">Comment not provided</span>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="pt-0.5">
                                            <strong>Recommendation Remark:</strong>
                                            @if($pts2->$remCol)
                                                <p class="italic text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 p-2.5 rounded-lg border border-blue-100 dark:border-blue-900 mt-0.5 whitespace-pre-wrap break-words [overflow-wrap:anywhere]">{{ trim($pts2->$remCol) }}</p>
                                            @else
                                                <div class="flex items-center space-x-2 text-xs text-gray-600 dark:text-gray-500 bg-gray-200/60 dark:bg-gray-900/40 p-2.5 rounded-lg border border-dashed border-gray-200 dark:border-gray-700/60 mt-0.5">
                                                    <svg class="w-4 h-4 text-gray-400 dark:text-gray-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 4.418 9 8z"></path>
                                                    </svg>
                                                    <span class="italic font-normal">Remark not provided</span>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                @endif
                            @endfor
                        </div>
                    @endif

                    <!-- Academic Office Verification -->
                    @if($passedAcademicOffice)
                        @php
                            $academicOfficeCredits = $pts2->academic_office_course_credits;
                            $academicOfficeRemark = $pts2->academic_office_verification_remark;
                        @endphp
                        <div class="p-4 bg-rose-50/70 dark:bg-rose-950/40 border-l-4 border-rose-500 rounded-xl space-y-2">
                            <div class="flex items-center justify-between font-bold text-rose-900 dark:text-rose-200 gap-2 sm:gap-4">
                                <h5 class="text-s">Academic Office</h5>
                                <span class="font-bold text-xs text-emerald-600 whitespace-nowrap shrink-0">
                                    ✓ Verified &amp; Forwarded
                                </span>
                            </div>
                            @if($academicOfficeCredits !== null)
                                <div class="flex items-center gap-1 text-xs text-gray-700 dark:text-gray-300">
                                    <strong>Verified Course Credits:</strong> <span class="font-bold text-rose-700 dark:text-rose-300">{{ $academicOfficeCredits }}</span>
                                    <x-info-button text="Course credits earned including coursework, seminars, and research credits." />
                                </div>
                            @endif
                            <div class="text-xs text-gray-700 dark:text-gray-300 pt-0.5">
                                <strong>Verification Remark:</strong>
                                @if($academicOfficeRemark)
                                    <p class="italic bg-white dark:bg-gray-800 p-2.5 rounded-lg border border-rose-100 dark:border-rose-900 mt-0.5 whitespace-pre-wrap break-words [overflow-wrap:anywhere]">{{ trim($academicOfficeRemark) }}</p>
                                @else
                                    <div class="flex items-center space-x-2 text-xs text-gray-600 dark:text-gray-500 bg-gray-200/60 dark:bg-gray-900/40 p-2.5 rounded-lg border border-dashed border-gray-200 dark:border-gray-700/60 mt-0.5">
                                        <svg class="w-4 h-4 text-gray-400 dark:text-gray-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 4.418 9 8z"></path>
                                        </svg>
                                        <span class="italic font-normal">Remark not provided</span>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif

                    <!-- DOAA Approval -->
                    @if($passedDoaa)
                        <div class="p-4 bg-emerald-50/70 dark:bg-emerald-950/40 border-l-4 border-emerald-500 rounded-xl space-y-2">
                            <div class="flex items-center justify-between font-bold text-emerald-900 dark:text-emerald-200 gap-2 sm:gap-4">
                                <h5 class="text-s">Dean of Academic Affairs (DOAA)</h5>
                                <span class="font-bold text-xs whitespace-nowrap shrink-0 {{ $pts2->doaa_approval ? 'text-emerald-600' : 'text-red-600' }}">
                                    {{ $pts2->doaa_approval ? '✓ Approved' : '❌ Not Approved' }}
                                </span>
                            </div>
                            <div class="text-xs text-gray-700 dark:text-gray-300">
                                <strong>Student Comment:</strong>
                                @if($pts2->doaa_student_comment)
                                    <p class="italic bg-white dark:bg-gray-800 p-2.5 rounded-lg border border-emerald-100 dark:border-emerald-900 mt-0.5 whitespace-pre-wrap break-words [overflow-wrap:anywhere]">{{ trim($pts2->doaa_student_comment) }}</p>
                                @else
                                    <div class="flex items-center space-x-2 text-xs text-gray-600 dark:text-gray-500 bg-gray-200/60 dark:bg-gray-900/40 p-2.5 rounded-lg border border-dashed border-gray-200 dark:border-gray-700/60 mt-0.5">
                                        <svg class="w-4 h-4 text-gray-400 dark:text-gray-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 4.418 9 8z"></path>
                                        </svg>
                                        <span class="italic font-normal">Comment not provided</span>
                                    </div>
                                @endif
                            </div>
                            <div class="text-xs text-gray-700 dark:text-gray-300 pt-0.5">
                                <strong>DOAA Remark:</strong>
                                @if($pts2->doaa_confidential_remark)
                                    <p class="italic bg-white dark:bg-gray-800 p-2.5 rounded-lg border border-emerald-100 dark:border-emerald-900 mt-0.5 whitespace-pre-wrap break-words [overflow-wrap:anywhere]">{{ trim($pts2->doaa_confidential_remark) }}</p>
                                @else
                                    <div class="flex items-center space-x-2 text-xs text-gray-600 dark:text-gray-500 bg-gray-200/60 dark:bg-gray-900/40 p-2.5 rounded-lg border border-dashed border-gray-200 dark:border-gray-700/60 mt-0.5">
                                        <svg class="w-4 h-4 text-gray-400 dark:text-gray-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 4.418 9 8z"></path>
                                        </svg>
                                        <span class="italic font-normal">Remark not provided</span>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            @php
                $showActionForm = false;
                $user = auth()->user();
                if ($pts2->current_stage === 'co_supervisors') {
                    for ($i = 1; $i <= 10; $i++) {
                        $idCol = "co_supervisor_{$i}_id";
                        $recCol = "co_supervisor_{$i}_recommendation";
                        if ($pts2->$idCol === $user->id && is_null($pts2->$recCol)) {
                            $showActionForm = true;
                            break;
                        }
                    }
                } elseif ($pts2->current_stage === 'academic_office') {
                    if (($user->isAcademicOffice() || $user->isGlobalAuthority()) && is_null($pts2->academic_office_submitted_at)) $showActionForm = true;
                } elseif ($pts2->current_stage === 'doaa') {
                    if (($user->isDoaa() || ($user->isActingApprovalAuthority() && ($pts2->acting_doaa_email === $user->email || $pts2->vested_doaa_email === $user->email))) && is_null($pts2->doaa_approval)) $showActionForm = true;
                }
            @endphp

            @if($showActionForm)
                <!-- Section 7: Action Required (Evaluation & Decision) -->
                <form action="{{ route('pts2.endorse', $pts2->id) }}" method="POST" class="space-y-8" @submit="clearDraft()">
                    @csrf

                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-indigo-100 dark:border-indigo-900/50 p-6 space-y-6">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white border-b border-indigo-100 dark:border-indigo-900/50 pb-2 flex items-center">
                            7. Endorsement Evaluation &amp; Recommendation
                        </h3>

                        <!-- Stage-Specific Evaluation Inputs -->
                        @if($pts2->current_stage === 'main_supervisor')
                            <!-- Declarations Review & Confirmation by Main Supervisor -->
                            <div class="space-y-4 p-4 rounded-xl border border-indigo-100 dark:border-indigo-900/50 bg-indigo-50/40 dark:bg-indigo-950/20">
                                <label class="block font-bold text-indigo-900 dark:text-indigo-200 text-sm">
                                    Declarations &amp; Certifications Review (Pre-filled from Student Submission): <span class="text-red-500">*</span>
                                </label>

                                <!-- 1. Prima Facie Case -->
                                <label class="p-3.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 flex items-start space-x-3 cursor-pointer">
                                    <input type="checkbox" name="cert_prima_facie_case" value="1" required x-model="certPrimaFacieCase" class="mt-1 text-emerald-600 focus:ring-emerald-500 rounded w-4 h-4">
                                    <div>
                                        <span class="block font-bold text-xs text-gray-900 dark:text-white uppercase tracking-wide">
                                            1. Prima Facie Case
                                        </span>
                                        <p class="text-xs text-gray-600 dark:text-gray-300 mt-0.5 leading-relaxed">
                                            There is a prima facie case for consideration of the thesis.
                                        </p>
                                    </div>
                                </label>

                                <!-- 2. No Prior Degree Submission -->
                                <label class="p-3.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 flex items-start space-x-3 cursor-pointer">
                                    <input type="checkbox" name="cert_no_prior_degree_submission" value="1" required x-model="certNoPriorDegreeSubmission" class="mt-1 text-emerald-600 focus:ring-emerald-500 rounded w-4 h-4">
                                    <div>
                                        <span class="block font-bold text-xs text-gray-900 dark:text-white uppercase tracking-wide">
                                            2. No Prior Degree Submission
                                        </span>
                                        <p class="text-xs text-gray-600 dark:text-gray-300 mt-0.5 leading-relaxed">
                                            To the best of our knowledge the thesis does not include any work which has, at any time, previously, been submitted for the award of a degree except to the extent of point 3 below.
                                        </p>
                                    </div>
                                </label>

                                <!-- 3. Collaborative Work -->
                                <div class="p-3.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 space-y-3">
                                    <label class="flex items-start space-x-3 cursor-pointer">
                                        <input type="checkbox" name="collaborative_work_status" value="1" x-model="collaborativeWorkStatus" class="mt-1 text-indigo-600 focus:ring-indigo-500 rounded w-4 h-4">
                                        <div>
                                            <span class="block font-bold text-xs text-gray-900 dark:text-white uppercase tracking-wide">
                                                3. Collaborative Work
                                            </span>
                                            <p class="text-xs text-gray-600 dark:text-gray-300 mt-0.5 leading-relaxed">
                                                Does any section of the Thesis relate to collaborative work? (Check if Yes)
                                            </p>
                                        </div>
                                    </label>

                                    <div x-show="collaborativeWorkStatus" class="pt-2 border-t border-gray-100 dark:border-gray-700 space-y-1">
                                        <label class="block text-xs font-bold text-gray-700 dark:text-gray-300">
                                            Collaborative Work Details <span class="text-red-500">*</span>
                                        </label>
                                        <textarea name="collaborative_work_details" rows="3" :required="collaborativeWorkStatus" x-model="collaborativeWorkDetails" placeholder="Specify parts of the thesis that relate to collaborative work..." class="w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-xs"></textarea>
                                    </div>
                                </div>
                            </div>

                            <div class="space-y-4">
                                <label class="block font-bold text-gray-900 dark:text-white text-sm">
                                    Recommendation Status for Candidate Synopsis Submission: <span class="text-red-500">*</span>
                                </label>

                                <div class="grid grid-cols-1 gap-4">
                                    <!-- Option (a) RECOMMENDED -->
                                    <label class="p-4 rounded-xl border-2 transition cursor-pointer flex items-start space-x-3" :class="recommendation === '1' ? 'border-emerald-500 bg-emerald-50/50 dark:bg-emerald-950/30' : 'border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800'">
                                        <input type="radio" name="recommendation" value="1" required x-model="recommendation" class="mt-1 text-emerald-600 focus:ring-emerald-500">
                                        <div>
                                            <span class="block font-bold text-sm text-emerald-900 dark:text-emerald-300 uppercase tracking-wide">
                                                (a) RECOMMENDED
                                            </span>
                                            <p class="text-xs text-gray-600 dark:text-gray-300 mt-1 leading-relaxed">
                                                Recommend the candidate's PTS-2 synopsis submission for forwarding to the next stage in the academic pipeline.
                                            </p>
                                        </div>
                                    </label>

                                    <!-- Option (b) NOT RECOMMENDED -->
                                    <label class="p-4 rounded-xl border-2 transition cursor-pointer flex items-start space-x-3" :class="recommendation === '0' ? 'border-red-500 bg-red-50/50 dark:bg-red-950/30' : 'border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800'">
                                        <input type="radio" name="recommendation" value="0" required x-model="recommendation" class="mt-1 text-red-600 focus:ring-red-500">
                                        <div>
                                            <span class="block font-bold text-sm text-red-900 dark:text-red-300 uppercase tracking-wide">
                                                (b) NOT RECOMMENDED
                                            </span>
                                            <p class="text-xs text-gray-600 dark:text-gray-300 mt-1 leading-relaxed">
                                                Do not recommend the submission in its present form without further improvements.
                                            </p>
                                        </div>
                                    </label>
                                </div>
                        @elseif($pts2->current_stage === 'academic_office')
                            <div class="space-y-6">
                                <!-- Course Credits Side-by-Side Comparison -->
                                <div class="p-4 rounded-xl bg-gray-50 dark:bg-gray-700/50 border border-gray-200 dark:border-gray-600 space-y-3">
                                    <label class="block font-bold text-gray-900 dark:text-white text-xs uppercase tracking-wide">
                                        Course Credits Comparison
                                    </label>
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                        <div class="p-3.5 bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 shadow-sm">
                                            <span class="flex items-center text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase mb-1">
                                                <span>Credits Earned (From System)</span>
                                                <x-info-button text="Course credits earned including coursework, seminars, and research credits." />
                                            </span>
                                            <span class="text-base font-extrabold text-gray-900 dark:text-white block">
                                                {{ $student->course_credits_earned ?? 'N/A' }}
                                            </span>
                                        </div>
                                        <div class="p-3.5 bg-white dark:bg-gray-800 rounded-lg border border-indigo-200 dark:border-indigo-800/60 shadow-sm">
                                            <span class="flex items-center text-xs font-semibold text-indigo-600 dark:text-indigo-400 uppercase mb-1">
                                                <span>Credits Submitted by Student</span>
                                                <x-info-button text="Course credits earned including coursework, seminars, and research credits." />
                                            </span>
                                            <span class="text-base font-extrabold text-indigo-900 dark:text-indigo-200 block">
                                                {{ $pts2->course_credits_student }}
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Course Credits Verification Decision -->
                                <div class="space-y-3">
                                    <label class="block font-bold text-gray-900 dark:text-white text-sm">
                                        Is the course credits earned filled by student correct? <span class="text-red-500">*</span>
                                    </label>

                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                        <!-- Option: Yes -->
                                        <label class="p-4 rounded-xl border-2 transition cursor-pointer flex items-start space-x-3" :class="isCreditsCorrect === '1' ? 'border-emerald-500 bg-emerald-50/50 dark:bg-emerald-950/30' : 'border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800'">
                                            <input type="radio" name="is_credits_correct" value="1" required x-model="isCreditsCorrect" class="mt-1 text-emerald-600 focus:ring-emerald-500">
                                            <div>
                                                <span class="block font-bold text-sm text-emerald-900 dark:text-emerald-300 uppercase tracking-wide">
                                                    Yes, Correct
                                                </span>
                                                <p class="text-xs text-gray-600 dark:text-gray-300 mt-1 leading-relaxed">
                                                    Student filled credits ({{ $pts2->course_credits_student }}) are verified and accurate.
                                                </p>
                                            </div>
                                        </label>

                                        <!-- Option: No -->
                                        <label class="p-4 rounded-xl border-2 transition cursor-pointer flex items-start space-x-3" :class="isCreditsCorrect === '0' ? 'border-amber-500 bg-amber-50/50 dark:bg-amber-950/30' : 'border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800'">
                                            <input type="radio" name="is_credits_correct" value="0" required x-model="isCreditsCorrect" class="mt-1 text-amber-600 focus:ring-amber-500">
                                            <div>
                                                <span class="block font-bold text-sm text-amber-900 dark:text-amber-300 uppercase tracking-wide">
                                                    No, Enter Corrected Credits
                                                </span>
                                                <p class="text-xs text-gray-600 dark:text-gray-300 mt-1 leading-relaxed">
                                                    Credits submitted by the student require correction.
                                                </p>
                                            </div>
                                        </label>
                                    </div>

                                    <!-- If Yes: Pass student credits directly -->
                                    <input type="hidden" name="academic_office_course_credits" value="{{ $pts2->course_credits_student }}" :disabled="isCreditsCorrect !== '1'">

                                    <!-- If No: Option to enter verified/corrected credits -->
                                    <div x-show="isCreditsCorrect === '0'" x-cloak class="p-4 rounded-xl bg-amber-50/60 dark:bg-amber-950/20 border border-amber-200 dark:border-amber-900/50 space-y-2">
                                        <label class="flex items-center font-bold text-amber-900 dark:text-amber-200 text-xs uppercase tracking-wide">
                                            Enter Corrected Course Credits <span class="text-red-500">*</span>
                                            <x-info-button text="Course credits earned including coursework, seminars, and research credits." />
                                        </label>
                                        <input type="number" 
                                               step="0.5" 
                                               min="0" 
                                               name="academic_office_course_credits" 
                                               x-model="academicOfficeCourseCredits" 
                                               :required="isCreditsCorrect === '0'" 
                                               :disabled="isCreditsCorrect !== '0'" 
                                               @wheel="$event.target.blur()" 
                                               onwheel="this.blur()" 
                                               placeholder="Enter verified course credits (e.g. 16, 18.5)" 
                                               class="w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm focus:ring-2 focus:ring-amber-500">
                                    </div>
                                </div>

                                <!-- Verification Confirmation (Above Verification Remark) -->
                                <div class="space-y-2 pt-2">
                                    <label class="block font-bold text-gray-900 dark:text-white text-sm">
                                        Verification: <span class="text-red-500">*</span>
                                    </label>

                                    <label class="p-4 rounded-xl border-2 border-emerald-500 bg-emerald-50/50 dark:bg-emerald-950/30 flex items-start space-x-3 cursor-pointer">
                                        <input type="checkbox" name="verified_details" value="1" required x-model="isVerified" class="mt-1 text-emerald-600 focus:ring-emerald-500 rounded w-4 h-4">
                                        <div>
                                            <span class="block font-bold text-sm text-emerald-900 dark:text-emerald-300 uppercase tracking-wide">
                                                ✓ Verification Confirmation
                                            </span>
                                            <p class="text-xs text-gray-700 dark:text-gray-300 mt-1 leading-relaxed">
                                                I have verified all student details, academic records, open seminar date, and attached synopsis documentation for this PTS-2 submission.
                                            </p>
                                        </div>
                                    </label>
                                </div>
                            </div>
                        @elseif ($pts2->current_stage === 'doaa')
                            <div class="space-y-4">
                                <label class="block font-bold text-gray-900 dark:text-white text-sm">
                                    Approval Status for Candidate PTS-2 Synopsis Submission: <span class="text-red-500">*</span>
                                </label>

                                <div class="grid grid-cols-1 gap-4">
                                    <!-- Option (a) APPROVE -->
                                    <label class="p-4 rounded-xl border-2 transition cursor-pointer flex items-start space-x-3" :class="recommendation === '1' ? 'border-emerald-500 bg-emerald-50/50 dark:bg-emerald-950/30' : 'border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800'">
                                        <input type="radio" name="recommendation" value="1" required x-model="recommendation" class="mt-1 text-emerald-600 focus:ring-emerald-500">
                                        <div>
                                            <span class="block font-bold text-sm text-emerald-900 dark:text-emerald-300 uppercase tracking-wide">
                                                (a) APPROVE
                                            </span>
                                            <p class="text-xs text-gray-600 dark:text-gray-300 mt-1 leading-relaxed">
                                                Approve the candidate's PTS-2 synopsis submission.
                                            </p>
                                        </div>
                                    </label>

                                    <!-- Option (b) DO NOT APPROVE -->
                                    <label class="p-4 rounded-xl border-2 transition cursor-pointer flex items-start space-x-3" :class="recommendation === '0' ? 'border-red-500 bg-red-50/50 dark:bg-red-950/30' : 'border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800'">
                                        <input type="radio" name="recommendation" value="0" required x-model="recommendation" class="mt-1 text-red-600 focus:ring-red-500">
                                        <div>
                                            <span class="block font-bold text-sm text-red-900 dark:text-red-300 uppercase tracking-wide">
                                                (b) DO NOT APPROVE
                                            </span>
                                            <p class="text-xs text-gray-600 dark:text-gray-300 mt-1 leading-relaxed">
                                                Do not approve the candidate's PTS-2 synopsis submission.
                                            </p>
                                        </div>
                                    </label>
                                </div>
                            </div>
                        @else
                            <div class="space-y-4">
                                <label class="block font-bold text-gray-900 dark:text-white text-sm">
                                    Recommendation Status for Candidate Synopsis Submission: <span class="text-red-500">*</span>
                                </label>

                                <div class="grid grid-cols-1 gap-4">
                                    <!-- Option (a) RECOMMENDED -->
                                    <label class="p-4 rounded-xl border-2 transition cursor-pointer flex items-start space-x-3" :class="recommendation === '1' ? 'border-emerald-500 bg-emerald-50/50 dark:bg-emerald-950/30' : 'border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800'">
                                        <input type="radio" name="recommendation" value="1" required x-model="recommendation" class="mt-1 text-emerald-600 focus:ring-emerald-500">
                                        <div>
                                            <span class="block font-bold text-sm text-emerald-900 dark:text-emerald-300 uppercase tracking-wide">
                                                (a) RECOMMENDED
                                            </span>
                                            <p class="text-xs text-gray-600 dark:text-gray-300 mt-1 leading-relaxed">
                                                Recommend the candidate's PTS-2 synopsis submission for forwarding to the next stage in the academic pipeline.
                                            </p>
                                        </div>
                                    </label>

                                    <!-- Option (b) NOT RECOMMENDED -->
                                    <label class="p-4 rounded-xl border-2 transition cursor-pointer flex items-start space-x-3" :class="recommendation === '0' ? 'border-red-500 bg-red-50/50 dark:bg-red-950/30' : 'border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800'">
                                        <input type="radio" name="recommendation" value="0" required x-model="recommendation" class="mt-1 text-red-600 focus:ring-red-500">
                                        <div>
                                            <span class="block font-bold text-sm text-red-900 dark:text-red-300 uppercase tracking-wide">
                                                (b) NOT RECOMMENDED
                                            </span>
                                            <p class="text-xs text-gray-600 dark:text-gray-300 mt-1 leading-relaxed">
                                                Do not recommend the submission in its present form without further improvements.
                                            </p>
                                        </div>
                                    </label>
                                </div>
                            </div>
                        @endif
                        
                        <!-- Remarks and Comments -->
                        @if($pts2->current_stage === 'main_supervisor')
                            <div class="space-y-2 pt-2">
                                <label class="block font-bold text-gray-900 dark:text-white text-sm">
                                    <span x-show="recommendation === '1'">Recommendation Remark (Optional)</span>
                                    <span x-show="recommendation === '0'">Non-Recommendation Remark <span class="text-red-500">*</span></span>
                                </label>
                                <div class="pt-0.5">
                                    <x-snippet-dropdown target="confidentialRemark" form-type="pts2" role="main_supervisor" />
                                </div>
                                <textarea name="confidential_remark" 
                                           rows="3" 
                                           :required="recommendation === '0'" 
                                           x-model="confidentialRemark" 
                                           :placeholder="recommendation === '1' ? 'Optional recommendation remarks' : 'Provide mandatory non-recommendation remarks'" 
                                           class="w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm whitespace-pre-wrap">{{ trim(old('confidential_remark')) }}</textarea>
                            </div>

                            <div class="space-y-2 pt-2">
                                <label class="block font-bold text-gray-900 dark:text-white text-sm">
                                    Student Comment (Optional)
                                </label>
                                <textarea name="student_comment" rows="3" x-model="studentComment" placeholder="Provide optional comments or observations for the student" class="w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm whitespace-pre-wrap">{{ trim(old('student_comment')) }}</textarea>
                            </div>
                        @elseif($pts2->current_stage === 'academic_office')
                            <div class="space-y-2 pt-2">
                                <label class="block font-bold text-gray-900 dark:text-white text-sm">
                                    Verification Remark <span class="text-red-500">*</span>
                                </label>
                                <div class="pt-0.5">
                                    <x-snippet-dropdown target="verificationRemark" form-type="pts2" role="academic_office" comment-type="verification_remark" />
                                </div>
                                <textarea name="verification_remark" 
                                           rows="5" 
                                           required 
                                           x-model="verificationRemark" 
                                           placeholder="Provide mandatory verification remarks..." 
                                           class="w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm whitespace-pre-wrap">{{ trim(old('verification_remark')) }}</textarea>
                            </div>

                            <!-- Academic Office: Optional Acting DOAA Dropdown -->
                            <div class="space-y-2 pt-2 border-t border-gray-100 dark:border-gray-700">
                                <label class="block font-bold text-gray-900 dark:text-white text-sm">
                                    Assign Acting DOAA (Optional)
                                </label>
                                <select name="acting_doaa_email" class="w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm focus:ring-2 focus:ring-indigo-500">
                                    <option value="">None (Forward to Default DOAA only)</option>
                                    @foreach($actingDoaaUsers as $actingUser)
                                        <option value="{{ $actingUser->email }}" {{ (old('acting_doaa_email', $pts2?->acting_doaa_email) === $actingUser->email) ? 'selected' : '' }}>
                                            {{ $actingUser->name }} ({{ $actingUser->email }})
                                        </option>
                                    @endforeach
                                </select>
                                <p class="text-xs text-gray-500 dark:text-gray-400">
                                    If selected, this form will be visible and actionable for the chosen Acting DOAA alongside the DOAA.
                                </p>
                            </div>
                        @elseif($pts2->current_stage === 'doaa')
                            <!-- DOAA Remark -->
                            <div class="space-y-2 pt-2">
                                <label class="block font-bold text-gray-900 dark:text-white text-sm">
                                    <span x-show="recommendation === '1'">Approval Remark (Optional)</span>
                                    <span x-show="recommendation === '0'">Non-Approval Remark <span class="text-red-500">*</span></span>
                                </label>
                                <div class="pt-0.5">
                                    <x-snippet-dropdown target="confidentialRemark" form-type="pts2" role="doaa" />
                                </div>
                                <textarea name="confidential_remark" 
                                           rows="5" 
                                           :required="recommendation === '0'" 
                                           x-model="confidentialRemark" 
                                           :placeholder="recommendation === '1' ? 'Optional approval remarks' : 'Provide mandatory non-approval remarks'" 
                                           class="w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm whitespace-pre-wrap">{{ trim(old('confidential_remark')) }}</textarea>
                            </div>

                            <!-- DOAA Student Comment -->
                            <div class="space-y-2 pt-2">
                                <label class="block font-bold text-gray-900 dark:text-white text-sm">
                                    Student Comment (Optional)
                                </label>
                                <textarea name="student_comment" rows="3" x-model="studentComment" placeholder="Provide optional comments or observations for the student" class="w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm whitespace-pre-wrap">{{ trim(old('student_comment')) }}</textarea>
                            </div>
                        @else
                            <!-- Co-Supervisors -->
                            <div class="space-y-2 pt-2">
                                <label class="block font-bold text-gray-900 dark:text-white text-sm">
                                    <span x-show="recommendation === '1'">Recommendation Remark (Optional)</span>
                                    <span x-show="recommendation === '0'">Non-Recommendation Remark <span class="text-red-500">*</span></span>
                                </label>
                                <div class="pt-0.5">
                                    <x-snippet-dropdown target="confidentialRemark" form-type="pts2" role="co_supervisor" />
                                </div>
                                <textarea name="confidential_remark" 
                                           rows="3" 
                                           :required="recommendation === '0'" 
                                           x-model="confidentialRemark" 
                                           :placeholder="recommendation === '1' ? 'Optional recommendation remarks' : 'Provide mandatory non-recommendation remarks'" 
                                           class="w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm whitespace-pre-wrap">{{ trim(old('confidential_remark')) }}</textarea>
                            </div>

                            <div class="space-y-2 pt-2">
                                <label class="block font-bold text-gray-900 dark:text-white text-sm">
                                    Student Comment (Optional)
                                </label>
                                <textarea name="student_comment" rows="3" x-model="studentComment" placeholder="Provide optional comments or observations for the student..." class="w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm whitespace-pre-wrap">{{ trim(old('student_comment')) }}</textarea>
                            </div>
                        @endif 
                    </div>
                    
                    <!-- Submit & Revert Action Buttons Bar -->
                    <div class="flex flex-col-reverse sm:flex-row items-center sm:justify-end gap-3 pt-4">
                        @if(!$user->isAcademicOffice())
                            <!-- Revert Button (Triggers Independent Pop-Up Modal) -->
                            <button type="button" 
                                    @click="showRevertModal = true" 
                                    class="w-full sm:w-auto justify-center bg-red-600 hover:bg-red-700 text-white text-base font-bold px-6 py-3 rounded-xl shadow-lg transition flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"></path>
                                </svg>
                                Revert Form
                            </button>
                        @endif

                        <!-- Main Submit Button -->
                        @if($pts2->current_stage === 'academic_office')
                            <button type="submit" 
                                    :disabled="!isVerified" 
                                    :class="!isVerified ? 'bg-gray-400 opacity-50 cursor-not-allowed shadow-none' : 'bg-emerald-600 hover:bg-emerald-700 shadow-lg'"
                                    class="w-full sm:w-auto justify-center text-white text-base font-bold px-8 py-3 rounded-xl transition flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                                </svg>
                                Submit &amp; Forward
                            </button>
                        @else
                            <button type="submit" 
                                    class="w-full sm:w-auto justify-center bg-emerald-600 hover:bg-emerald-700 text-white text-base font-bold px-8 py-3 rounded-xl shadow-lg transition flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                                </svg>
                                Submit @if($pts2->current_stage !== 'doaa') &amp; Forward @endif
                            </button>
                        @endif
                    </div>
                </form>

                <!-- Revert Confirmation Pop-Up Modal -->
                <div x-show="showRevertModal" 
                     x-cloak 
                     class="fixed inset-0 z-50 overflow-y-auto bg-gray-900/60 backdrop-blur-sm flex items-center justify-center p-4"
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0"
                     x-transition:enter-end="opacity-100"
                     x-transition:leave="transition ease-in duration-150"
                     x-transition:leave-start="opacity-100"
                     x-transition:leave-end="opacity-0">

                    <div @click.away="showRevertModal = false" 
                         class="bg-white dark:bg-gray-800 rounded-2xl max-w-lg w-full p-6 shadow-2xl border border-gray-100 dark:border-gray-700 space-y-5"
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 scale-95"
                         x-transition:enter-end="opacity-100 scale-100">

                        <div class="flex items-center space-x-3 border-b border-gray-100 dark:border-gray-700 pb-3">
                            <div class="p-2.5 bg-amber-100 dark:bg-amber-900/40 rounded-xl text-amber-600 dark:text-amber-400">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-lg font-bold text-gray-900 dark:text-white">Revert PTS-2 Form to Student</h3>
                                <p class="text-xs text-gray-500 dark:text-gray-400">Send form back to candidate for required changes</p>
                            </div>
                        </div>

                        <!-- Independent Revert Form -->
                        <form action="{{ route('pts2.revert', $pts2->id) }}" method="POST" class="space-y-4" @submit="clearDraft()">
                            @csrf

                            <div>
                                <label class="block text-sm font-bold text-gray-800 dark:text-gray-200 mb-1.5">
                                    Reversion Comment <span class="text-red-500">*</span>
                                </label>
                                <textarea name="reversion_comment" 
                                          required 
                                          rows="4" 
                                          placeholder="Provide clear reasons/instructions for the student regarding required modifications" 
                                          class="w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm focus:ring-2 focus:ring-amber-500 whitespace-pre-wrap "></textarea>
                            </div>

                            <div class="flex justify-end space-x-3 pt-2">
                                <button type="button" 
                                        @click="showRevertModal = false" 
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
            @endif

        </div>
    </div>

    <script>
        function pts2EndorseForm() {
            const userId = @js(auth()->id());
            const thesisId = @js($thesis->id);
            const formId = @js($pts2->id);
            const draftKey = 'pts2_review_draft_user_' + userId + '_thesis_' + thesisId + '_form_' + formId;

            let savedDraft = {};
            try {
                savedDraft = JSON.parse(sessionStorage.getItem(draftKey) || '{}');
            } catch (e) {}

            return {
                showRevertModal: false,
                recommendation: @js(old('approval') ?? old('recommendation')) || savedDraft.recommendation || '1',
                isVerified: savedDraft.isVerified !== undefined ? savedDraft.isVerified : false,
                certPrimaFacieCase: savedDraft.certPrimaFacieCase !== undefined ? savedDraft.certPrimaFacieCase : @js((bool)$pts2->getEffectiveCertPrimaFacieCase()),
                certNoPriorDegreeSubmission: savedDraft.certNoPriorDegreeSubmission !== undefined ? savedDraft.certNoPriorDegreeSubmission : @js((bool)$pts2->getEffectiveCertNoPriorDegreeSubmission()),
                collaborativeWorkStatus: savedDraft.collaborativeWorkStatus !== undefined ? savedDraft.collaborativeWorkStatus : @js((bool)$pts2->getEffectiveCollaborativeWorkStatus()),
                collaborativeWorkDetails: @js(old('collaborative_work_details')) || savedDraft.collaborativeWorkDetails || @js($pts2->getEffectiveCollaborativeWorkDetails() ?? ''),
                confidentialRemark: @js(old('doaa_confidential_remark') ?? old('confidential_remark')) || savedDraft.confidentialRemark || '',
                studentComment: @js(old('doaa_student_comment') ?? old('student_comment')) || savedDraft.studentComment || '',
                verificationRemark: @js(old('verification_remark')) || savedDraft.verificationRemark || '',
                isCreditsCorrect: @js(old('is_credits_correct')) || (savedDraft.isCreditsCorrect !== undefined ? savedDraft.isCreditsCorrect : '1'),
                academicOfficeCourseCredits: @js(old('academic_office_course_credits')) || savedDraft.academicOfficeCourseCredits || '',

                init() {
                    const watchFields = ['recommendation', 'isVerified', 'certPrimaFacieCase', 'certNoPriorDegreeSubmission', 'collaborativeWorkStatus', 'collaborativeWorkDetails', 'confidentialRemark', 'studentComment', 'verificationRemark', 'isCreditsCorrect', 'academicOfficeCourseCredits'];
                    watchFields.forEach(field => {
                        this.$watch(field, () => this.saveDraft());
                    });
                },

                saveDraft() {
                    try {
                        sessionStorage.setItem(draftKey, JSON.stringify({
                            recommendation: this.recommendation,
                            isVerified: this.isVerified,
                            certPrimaFacieCase: this.certPrimaFacieCase,
                            certNoPriorDegreeSubmission: this.certNoPriorDegreeSubmission,
                            collaborativeWorkStatus: this.collaborativeWorkStatus,
                            collaborativeWorkDetails: this.collaborativeWorkDetails,
                            confidentialRemark: this.confidentialRemark,
                            studentComment: this.studentComment,
                            verificationRemark: this.verificationRemark,
                            isCreditsCorrect: this.isCreditsCorrect,
                            academicOfficeCourseCredits: this.academicOfficeCourseCredits,
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

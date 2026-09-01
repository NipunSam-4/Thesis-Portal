<x-app-layout>
    @php
        $pts2 = $pts2 ?? $form ?? null;
        $form = $form ?? $pts2 ?? null;
        $thesis = $thesis ?? $form?->thesis;
        $student = $student ?? $thesis?->student;
        $studentUser = $studentUser ?? $student?->user;
        $formPrefix = isset($student) && $student->isPhd() ? 'PTS' : 'MSRTS';
    @endphp
    <div class="py-6">
        <div class="max-w-5xl mx-auto px-2 sm:px-6 lg:px-8 space-y-4">

            <!-- Top Back to Dashboard Button -->
            <div>
                <x-back-to-dashboard-button />
            </div>

            <!-- Page Header Card -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-5 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div class="space-y-1">
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white leading-tight">
                        @if($pts2->status === 'approved')
                            {{ __("Approved {$formPrefix}-2 Form Details & Remarks") }}
                        @else
                            {{ __("Rejected {$formPrefix}-2 Form Details & Remarks") }}
                        @endif
                    </h2>
                </div>
                <div class="flex items-center space-x-3 shrink-0">
                    @if($pts2->status === 'approved')
                        <span class="px-3.5 py-1.5 bg-emerald-100 dark:bg-emerald-900/60 text-emerald-800 dark:text-emerald-300 text-xs font-extrabold rounded-full uppercase tracking-wider flex items-center shadow-xs border border-emerald-200 dark:border-emerald-800">
                            ✓ Approved
                        </span>
                    @else
                        <span class="px-3.5 py-1.5 bg-red-100 dark:bg-red-900/60 text-red-800 dark:text-red-300 text-xs font-extrabold rounded-full uppercase tracking-wider flex items-center shadow-xs border border-red-200 dark:border-red-800">
                            ❌ Rejected
                        </span>
                    @endif
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

            <!-- Common Form Information Sections (1 to 5) -->
            @include('pts2.form_info', ['pts2' => $pts2, 'viewPerspective' => 'authority'])


            @php
                $isStudent = auth()->user()->isStudent();
                $hasAnyCo = false;
                for ($i = 1; $i <= 10; $i++) {
                    $idCol = "co_supervisor_{$i}_id";
                    if ($pts2->$idCol) {
                        $hasAnyCo = true;
                        break;
                    }
                }
                $hasAnyComment = ($pts2->main_supervisor_submitted_at || $pts2->main_supervisor_recommendation !== null || !is_null($pts2->main_supervisor_student_comment))
                    || $hasAnyCo
                    || (!$isStudent && ($pts2->academic_office_submitted_at || $pts2->academic_office_is_verified !== null || !is_null($pts2->academic_office_verification_remark)))
                    || ($pts2->doaa_submitted_at || $pts2->doaa_approval !== null || !is_null($pts2->doaa_student_comment));
            @endphp

            <!-- Section 6: Authority Comments (Student) or Authority Recommendations & Remarks (Authorities) -->
            @if(in_array($pts2->status, ['approved', 'rejected']))
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 space-y-6">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white border-b border-gray-100 dark:border-gray-700 pb-2">
                        {{ $isStudent ? '6. Authority Comments' : '6. Authority Recommendations & Remarks' }}
                    </h3>

                    @if($hasAnyComment)
                        <div class="space-y-4">
                            <!-- Main Supervisor -->
                            @if($pts2->main_supervisor_submitted_at || $pts2->main_supervisor_recommendation !== null)
                                <div class="p-4 bg-indigo-50/70 dark:bg-indigo-950/40 border-l-4 border-indigo-500 rounded-xl space-y-2">
                                    <div class="flex items-center justify-between text-s gap-2 sm:gap-4">
                                        <span class="font-bold text-indigo-900 dark:text-indigo-200">
                                            Main Supervisor @if(isset($mainSupervisor))<span class="block sm:inline text-xs font-normal text-indigo-700 dark:text-indigo-300 mt-0.5 sm:mt-0">({{ $mainSupervisor->name }})</span>@endif
                                        </span>
                                        @if(!$isStudent && $pts2->main_supervisor_recommendation !== null)
                                            <span class="px-2.5 py-1 font-bold rounded-lg uppercase text-[10px] text-center leading-tight whitespace-normal max-w-[140px] sm:max-w-none {{ $pts2->main_supervisor_recommendation ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/60 dark:text-emerald-300' : 'bg-red-100 text-red-800 dark:bg-red-900/60 dark:text-red-300' }}">
                                                {{ $pts2->main_supervisor_recommendation ? '✓ Recommended' : '❌ Not Recommended' }}
                                            </span>
                                        @endif
                                    </div>
                                    <div class="text-xs text-gray-700 dark:text-gray-300">
                                        <strong>Student Comment:</strong>
                                        @if($pts2->main_supervisor_student_comment)
                                            <p class="italic bg-white dark:bg-gray-800 p-2.5 rounded-lg border border-indigo-100 dark:border-indigo-900 mt-1 whitespace-pre-wrap break-words [overflow-wrap:anywhere]">{{ trim($pts2->main_supervisor_student_comment) }}</p>
                                        @else
                                            <div class="flex items-center space-x-2 text-xs text-gray-600 dark:text-gray-500 bg-gray-200/60 dark:bg-gray-900/40 p-2.5 rounded-lg border border-dashed border-gray-200 dark:border-gray-700/60 mt-1">
                                                <svg class="w-4 h-4 text-gray-400 dark:text-gray-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 4.418 9 8z"></path>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3l18 18"></path>
                                                </svg>
                                                <span class="italic font-normal">Comment not provided</span>
                                            </div>
                                        @endif
                                    </div>
                            
                                    @if(!$isStudent)
                                        <div class="text-xs text-gray-700 dark:text-gray-300 pt-1">
                                            <strong>Main Supervisor Remark:</strong>
                                            @if($pts2->main_supervisor_confidential_remark)
                                                <p class="italic bg-white dark:bg-gray-800 p-2.5 rounded-lg border border-indigo-100 dark:border-indigo-900 mt-1 whitespace-pre-wrap break-words [overflow-wrap:anywhere]">{{ trim($pts2->main_supervisor_confidential_remark) }}</p>
                                            @else
                                                <div class="flex items-center space-x-2 text-xs text-gray-600 dark:text-gray-500 bg-gray-200/60 dark:bg-gray-900/40 p-2.5 rounded-lg border border-dashed border-gray-200 dark:border-gray-700/60 mt-1">
                                                    <svg class="w-4 h-4 text-gray-400 dark:text-gray-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 4.418 9 8z"></path>
                                                    </svg>
                                                    <span class="italic font-normal">Remark not provided</span>
                                                </div>
                                            @endif
                                        </div>
                                    @endif
                                </div>
                            @endif

                            <!-- Co-Supervisors -->
                            @if($hasAnyCo)
                                <div class="p-4 bg-blue-50/70 dark:bg-blue-950/40 border-l-4 border-blue-500 rounded-xl space-y-2">
                                    <h5 class="text-s font-bold text-blue-900 dark:text-blue-200">Co-Supervisor</h5>
                                    
                                    @for($i = 1; $i <= 10; $i++)
                                        @php
                                            $idCol = "co_supervisor_{$i}_id";
                                            $recCol = "co_supervisor_{$i}_recommendation";
                                            $remCol = "co_supervisor_{$i}_confidential_remark";
                                            $comCol = "co_supervisor_{$i}_student_comment";
                                            $coUser = $coSupervisors[$i] ?? ($pts2->$idCol ? \App\Models\User::find($pts2->$idCol) : null);
                                        @endphp

                                        @if($coUser)
                                            @php
                                                $isExt = $coUser->isExternalSupervisor();
                                                $roleTitle = $student ? $student->getSupervisorRoleTitle($coUser) : ($isExt ? 'External Supervisor' : "Co-Supervisor {$i}");
                                                $inst = ($isExt && $coUser->externalSupervisorProfile?->affiliated_institute) ? ' - ' . $coUser->externalSupervisorProfile->affiliated_institute : '';
                                            @endphp
                                            <div class="text-xs space-y-1 pt-1 {{ $i > 1 ? 'border-t border-blue-100 dark:border-blue-900' : '' }}">
                                                <div class="flex items-center justify-between font-semibold gap-2 sm:gap-4">
                                                    <span>{{ $roleTitle }} <span class="block sm:inline text-xs font-normal text-gray-500 dark:text-gray-400 mt-0.5 sm:mt-0">({{ $coUser->name }}{{ $inst }})</span>:</span>
                                                    @if(!$isStudent && !is_null($pts2->$recCol))
                                                        <span class="font-bold whitespace-nowrap shrink-0 {{ $pts2->$recCol ? 'text-emerald-600' : 'text-red-600' }}">
                                                            {{ $pts2->$recCol ? '✓ Recommended' : '❌ Not Recommended' }}
                                                        </span>
                                                    @endif
                                                </div>
                                                <div class="pt-1">
                                                    <strong>{{ 'Student Comment:' }}</strong>
                                                    @if($pts2->$comCol)
                                                        <p class="italic text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 p-2.5 rounded-lg border border-blue-100 dark:border-blue-900 mt-0.5 whitespace-pre-wrap break-words [overflow-wrap:anywhere]">{{ trim($pts2->$comCol) }}</p>
                                                    @else
                                                        <div class="flex items-center space-x-2 text-xs text-gray-600 dark:text-gray-500 bg-gray-200/60 dark:bg-gray-900/40 p-2.5 rounded-lg border border-dashed border-gray-200 dark:border-gray-700/60 mt-0.5">
                                                            <svg class="w-4 h-4 text-gray-400 dark:text-gray-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 4.418 9 8z"></path>
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3l18 18"></path>
                                                            </svg>
                                                            <span class="italic font-normal">Comment not provided</span>
                                                        </div>
                                                    @endif
                                                </div>
                                                @if(!$isStudent)
                                                    <div class="text-xs text-gray-700 dark:text-gray-300 pt-1">
                                                        <strong>Co-Supervisor Remark:</strong>
                                                        @if($pts2->$remCol)
                                                            <div class="pt-1">
                                                                <p class="italic text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 p-2.5 rounded-lg border border-blue-100 dark:border-blue-900 mt-0.5 whitespace-pre-wrap break-words [overflow-wrap:anywhere]">{{ trim($pts2->$remCol) }}</p>
                                                            </div>
                                                        @else
                                                            <div class="flex items-center space-x-2 text-xs text-gray-600 dark:text-gray-500 bg-gray-200/60 dark:bg-gray-900/40 p-2.5 rounded-lg border border-dashed border-gray-200 dark:border-gray-700/60 mt-1">
                                                                <svg class="w-4 h-4 text-gray-400 dark:text-gray-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 4.418 9 8z"></path>
                                                                </svg>
                                                                <span class="italic font-normal">Remark not provided</span>
                                                            </div>
                                                        @endif
                                                    </div>
                                                @endif
                                            </div>
                                        @endif
                                    @endfor
                                </div>
                            @endif

                            <!-- Academic Office Endorsement (Only visible to non-students) -->
                            @if(!$isStudent && ($pts2->academic_office_submitted_at || $pts2->academic_office_is_verified !== null))
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
                            @if($pts2->doaa_submitted_at || $pts2->doaa_approval !== null)
                                <div class="p-4 bg-emerald-50/70 dark:bg-emerald-950/40 border-l-4 border-emerald-500 rounded-xl space-y-2">
                                    <div class="flex items-center justify-between font-bold text-emerald-900 dark:text-emerald-200 gap-2 sm:gap-4">
                                        <div class="flex items-center gap-2">
                                            <h5 class="text-s">Dean of Academic Affairs (DOAA)</h5>
                                        </div>
                                        @if(!$isStudent && $pts2->doaa_approval !== null)
                                            <span class="font-bold text-xs whitespace-nowrap shrink-0 {{ $pts2->doaa_approval ? 'text-emerald-600' : 'text-red-600' }}">
                                                {{ $pts2->doaa_approval ? '✓ Approved' : '❌ Not Approved' }}
                                                @if($pts2->approved_by_authority)
                                                    by: {{ $pts2->approved_by_authority }}
                                                @endif
                                            </span>
                                        @endif
                                    </div>
                                    <div class="text-xs text-gray-700 dark:text-gray-300">
                                        <strong>{{ 'Student Comment:' }}</strong>
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
                                    @if(!$isStudent)
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
                                    @endif
                                </div>
                            @endif
                        </div>
                    @else
                        <div class="flex items-center space-x-2 text-xs text-gray-600 dark:text-gray-500 bg-gray-50 dark:bg-gray-700/50 p-4 rounded-xl border border-dashed border-gray-200 dark:border-gray-700/60">
                            <svg class="w-4 h-4 text-gray-400 dark:text-gray-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 4.418 9 8z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3l18 18"></path>
                            </svg>
                            <span class="italic font-normal">No comments</span>
                        </div>
                    @endif
                </div>
            @endif
        </div>
    </div>
</x-app-layout>

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

            <!-- Common Form Information Sections (1 to 5) -->
            @include('pts2.form_info', ['pts2' => $pts2])

            @php
                $currentUser = $user ?? auth()->user();
                $isStudent = $currentUser?->isStudent() ?? false;
                $hasAnyCo = !empty($coSupervisors);
            @endphp

            <!-- Section 6: Authority Comments (Student) or Authority Recommendations & Remarks (Authorities) -->
            @if(in_array($pts2->status, ['approved', 'rejected']))
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 space-y-6">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white border-b border-gray-100 dark:border-gray-700 pb-2">
                        {{ $isStudent ? '5. Authority Comments' : '5. Authority Recommendations & Remarks' }}
                    </h3>

                    <div class="space-y-4">
                        <!-- Main Supervisor -->
                        <x-role-card role="main_supervisor">
                            <div class="flex items-center justify-between text-base gap-2 sm:gap-4">
                                <span class="font-bold text-indigo-900 dark:text-indigo-200 text-base">
                                    Main Supervisor @if(isset($mainSupervisor))<span class="block sm:inline text-xs font-normal text-gray-500 dark:text-gray-400 mt-0.5 sm:mt-0">({{ $mainSupervisor->name }})</span>@endif
                                </span>
                                @if(!$isStudent && $pts2->main_supervisor_recommendation !== null)
                                    <span class="font-bold text-xs whitespace-nowrap shrink-0 {{ $pts2->main_supervisor_recommendation ? 'text-emerald-600 dark:text-emerald-400' : 'text-red-600 dark:text-red-400' }}">
                                        {{ $pts2->main_supervisor_recommendation ? '✓ Recommended' : '❌ Not Recommended' }}
                                    </span>
                                @endif
                            </div>
                            <div class="text-xs text-gray-700 dark:text-gray-300 space-y-1">
                                <strong>Student Comment:</strong>
                                <x-feedback-box :text="$pts2->main_supervisor_student_comment" fallback="Comment not provided" role="main_supervisor" />
                            </div>
                            @if(!$isStudent)
                                <div class="text-xs text-gray-700 dark:text-gray-300 space-y-1">
                                    <strong>Confidential Remark:</strong>
                                    <x-feedback-box :text="$pts2->main_supervisor_confidential_remark" fallback="Remark not provided" role="main_supervisor" />
                                </div>
                            @endif
                        </x-role-card>

                        <!-- Co-Supervisors -->
                        @if($hasAnyCo)
                            <x-role-card role="co_supervisor">
                                <h5 class="text-base font-bold text-blue-900 dark:text-blue-200">Co-Supervisors</h5>
                                
                                @foreach($coSupervisors as $i => $coUser)
                                    @php
                                        $recCol = "co_supervisor_{$i}_recommendation";
                                        $remCol = "co_supervisor_{$i}_confidential_remark";
                                        $comCol = "co_supervisor_{$i}_student_comment";
                                        $isExt = $coUser->isExternalSupervisor();
                                        $roleTitle = $student ? $student->getSupervisorRoleTitle($coUser) : ($isExt ? 'External Supervisor' : "Co-Supervisor {$i}");
                                        $inst = ($isExt && $coUser->externalSupervisorProfile?->affiliated_institute) ? ' - ' . $coUser->externalSupervisorProfile->affiliated_institute : '';
                                    @endphp
                                    <div class="text-xs space-y-1.5 pt-1.5 {{ !$loop->first ? 'border-t border-blue-100 dark:border-blue-900' : '' }}">
                                        <div class="flex items-center justify-between font-semibold text-sm gap-2 sm:gap-4">
                                            <span>{{ $roleTitle }} <span class="block sm:inline text-xs font-normal text-gray-500 dark:text-gray-400 mt-0.5 sm:mt-0">({{ $coUser->name }}{{ $inst }})</span>:</span>
                                            @if(!$isStudent && !is_null($pts2->$recCol))
                                                <span class="font-bold text-xs whitespace-nowrap shrink-0 {{ $pts2->$recCol ? 'text-emerald-600 dark:text-emerald-400' : 'text-red-600 dark:text-red-400' }}">
                                                    {{ $pts2->$recCol ? '✓ Recommended' : '❌ Not Recommended' }}
                                                </span>
                                            @endif
                                        </div>
                                        <div class="space-y-1">
                                            <strong>Student Comment:</strong>
                                            <x-feedback-box :text="$pts2->$comCol" fallback="Comment not provided" role="co_supervisor" />
                                        </div>
                                        @if(!$isStudent)
                                            <div class="space-y-1">
                                                <strong>Confidential Remark:</strong>
                                                <x-feedback-box :text="$pts2->$remCol" fallback="Remark not provided" role="co_supervisor" />
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            </x-role-card>
                        @endif

                        <!-- Academic Office -->
                        @if(!$isStudent && ($pts2->academic_office_submitted_at || $pts2->academic_office_is_verified !== null))
                            @php
                                $academicOfficeCredits = $pts2->academic_office_course_credits;
                                $academicOfficeRemark = $pts2->academic_office_verification_remark;
                            @endphp
                            <x-role-card role="academic_office" title="Academic Office">
                                <x-slot:badge>
                                    <span class="font-bold text-xs text-emerald-600 dark:text-emerald-400 whitespace-nowrap shrink-0">
                                        ✓ Verified &amp; Forwarded
                                    </span>
                                </x-slot:badge>
                                @if($academicOfficeCredits !== null)
                                    <div class="flex items-center gap-1 text-xs text-gray-700 dark:text-gray-300">
                                        <strong>Verified Course Credits:</strong> <span class="font-bold text-violet-700 dark:text-violet-300">{{ $academicOfficeCredits }}</span>
                                        <x-info-button text="Course credits earned including coursework, seminars, and research credits." />
                                    </div>
                                @endif
                                <div class="text-xs text-gray-700 dark:text-gray-300 space-y-1">
                                    <strong>Verification Remark:</strong>
                                    <x-feedback-box :text="$academicOfficeRemark" fallback="Remark not provided" role="academic_office" />
                                </div>
                                @if(Auth::user()?->isAcademicOffice())
                                    <div class="text-xs text-gray-700 dark:text-gray-300 space-y-1 pt-1">
                                        <strong>Assigned Acting DOAA:</strong>
                                        @if(!empty($pts2->acting_doaa_email))
                                            @php
                                                $actingDoaaUser = \App\Models\User::where('email', $pts2->acting_doaa_email)->first();
                                            @endphp
                                            <div class="p-2 bg-indigo-50/70 dark:bg-indigo-950/40 border border-indigo-200 dark:border-indigo-800 rounded-lg font-semibold text-indigo-900 dark:text-indigo-200">
                                                {{ $actingDoaaUser->name ?? $pts2->acting_doaa_email }} ({{ $pts2->acting_doaa_email }})
                                            </div>
                                        @else
                                            <div class="p-2 bg-gray-100 dark:bg-gray-700/60 border border-gray-200 dark:border-gray-600 rounded-lg text-gray-600 dark:text-gray-400 font-medium">
                                                None
                                            </div>
                                        @endif
                                    </div>
                                @endif
                            </x-role-card>
                        @endif

                        <!-- DOAA Approval -->
                        @if($pts2->doaa_submitted_at || $pts2->doaa_approval !== null)
                            <x-role-card role="doaa" title="Dean of Academic Affairs (DOAA)">
                                @if(!$isStudent && $pts2->doaa_approval !== null)
                                    <x-slot:badge>
                                        <span class="font-bold text-xs whitespace-nowrap shrink-0 {{ $pts2->doaa_approval ? 'text-emerald-600 dark:text-emerald-400' : 'text-red-600 dark:text-red-400' }}">
                                            {{ $pts2->doaa_approval ? '✓ Approved' : '❌ Rejected' }}
                                            @if($pts2->approvedBy)
                                                by: {{ $pts2->approvedBy->email }}
                                            @endif
                                        </span>
                                    </x-slot:badge>
                                @endif
                                <div class="text-xs text-gray-700 dark:text-gray-300 space-y-1">
                                    <strong>Student Comment:</strong>
                                    <x-feedback-box :text="$pts2->doaa_student_comment" fallback="Comment not provided" role="doaa" />
                                </div>
                                @if(!$isStudent)
                                    <div class="text-xs text-gray-700 dark:text-gray-300 space-y-1">
                                        <strong>Confidential Remark:</strong>
                                        <x-feedback-box :text="$pts2->doaa_confidential_remark" fallback="Remark not provided" role="doaa" />
                                    </div>
                                @endif
                            </x-role-card>
                        @endif
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>

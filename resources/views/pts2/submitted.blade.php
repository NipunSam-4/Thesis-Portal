<x-app-layout>
    @php
        $pts2 = $pts2 ?? $form ?? null;
        $form = $form ?? $pts2 ?? null;
        $thesis = $thesis ?? $form?->thesis;
        $student = $student ?? $thesis?->student;
        $studentUser = $studentUser ?? $student?->user;
        $currentUser = $user ?? auth()->user();
        $viewPerspective = $viewPerspective ?? ($currentUser?->isStudent() ? 'student' : ($currentUser?->isAcademicOffice() ? 'academic_office' : ($currentUser?->isDoaa() ? 'doaa' : 'authority')));
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
                        {{ __("Submitted {$formPrefix}-2 Form") }}
                    </h2>
                </div>
                <div class="flex items-center space-x-3 shrink-0">
                    <span class="px-3.5 py-1.5 bg-blue-100 dark:bg-blue-900/60 text-blue-800 dark:text-blue-300 text-xs font-extrabold rounded-full uppercase tracking-wider flex items-center shadow-xs border border-blue-200 dark:border-blue-800">
                        ⏳ In Progress
                    </span>
                </div>
            </div>

            <!-- Common Form Information Sections (1 to 5) -->
            @include('pts2.form_info', ['pts2' => $pts2])

            <!-- Section 6: Role-Scoped Review & Comments -->
            @if($viewPerspective !== 'student')
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 space-y-6">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white border-b border-gray-100 dark:border-gray-700 pb-2">
                        6. Evaluation &amp; Comments
                    </h3>

                    <div class="space-y-4">
                        <!-- 1. Main Supervisor Evaluation -->
                        @if($pts2->main_supervisor_submitted_at || !is_null($pts2->main_supervisor_recommendation))
                            <x-role-card role="main_supervisor">
                                <div class="flex items-center justify-between text-base gap-2 sm:gap-4">
                                    <span class="font-bold text-indigo-900 dark:text-indigo-200 text-base">
                                        Main Supervisor @if(isset($mainSupervisor))<span class="block sm:inline text-xs font-normal text-gray-500 dark:text-gray-400 mt-0.5 sm:mt-0">({{ $mainSupervisor->name }})</span>@endif
                                    </span>
                                    @if(!is_null($pts2->main_supervisor_recommendation))
                                        <span class="font-bold text-xs whitespace-nowrap shrink-0 {{ $pts2->main_supervisor_recommendation ? 'text-emerald-600 dark:text-emerald-400' : 'text-red-600 dark:text-red-400' }}">
                                            {{ $pts2->main_supervisor_recommendation ? '✓ Recommended' : '❌ Not Recommended' }}
                                        </span>
                                    @endif
                                </div>
                                <div class="text-xs text-gray-700 dark:text-gray-300 space-y-1">
                                    <strong>Student Comment:</strong>
                                    <x-feedback-box :text="$pts2->main_supervisor_student_comment" fallback="Comment not provided" role="main_supervisor" />
                                </div>

                                @if($viewPerspective === 'main_supervisor' || in_array($viewPerspective, ['dpgc', 'hod', 'academic_office', 'doaa']))
                                    <div class="text-xs text-gray-700 dark:text-gray-300 space-y-1">
                                        <strong>Confidential Remark:</strong>
                                        <x-feedback-box :text="$pts2->main_supervisor_confidential_remark" fallback="Remark not provided" role="main_supervisor" />
                                    </div>
                                @endif
                            </x-role-card>
                        @endif

                        <!-- 2. Co-Supervisors Endorsements -->
                        <!-- If viewPerspective == 'co_supervisor', show ONLY $myCoSupSlot -->
                        <!-- If viewPerspective in ['dpgc', 'hod', 'academic_office', 'doaa'], show ALL submitted co-supervisors -->
                        @if($viewPerspective === 'co_supervisor' && $myCoSupSlot)
                            @php
                                $recCol = "co_supervisor_{$myCoSupSlot}_recommendation";
                                $commCol = "co_supervisor_{$myCoSupSlot}_student_comment";
                                $remCol = "co_supervisor_{$myCoSupSlot}_confidential_remark";
                                $coUser = auth()->user();
                                $isExt = $coUser ? $coUser->isExternalSupervisor() : false;
                                $roleTitle = ($student && $coUser) ? $student->getSupervisorRoleTitle($coUser) : ($isExt ? 'External Supervisor' : "Co-Supervisor {$myCoSupSlot}");
                                $inst = ($isExt && $coUser?->externalSupervisorProfile?->affiliated_institute) ? ' - ' . $coUser->externalSupervisorProfile->affiliated_institute : '';
                            @endphp
                            @if(!is_null($pts2->$recCol))
                                <x-role-card role="co_supervisor">
                                    <div class="flex items-center justify-between font-semibold gap-2 sm:gap-4">
                                        <h5 class="text-base font-bold text-blue-900 dark:text-blue-200">
                                            {{ $roleTitle }} <span class="block sm:inline text-xs font-normal text-gray-500 dark:text-gray-400 mt-0.5 sm:mt-0">({{ $coUser?->name }}{{ $inst }})</span>
                                        </h5>
                                        <span class="font-bold text-xs whitespace-nowrap shrink-0 {{ $pts2->$recCol ? 'text-emerald-600 dark:text-emerald-400' : 'text-red-600 dark:text-red-400' }}">
                                            {{ $pts2->$recCol ? '✓ Recommended' : '❌ Not Recommended' }}
                                        </span>
                                    </div>
                                    <div class="text-xs text-gray-700 dark:text-gray-300 space-y-1">
                                        <strong>Student Comment:</strong>
                                        <x-feedback-box :text="$pts2->$commCol" fallback="Comment not provided" role="co_supervisor" />
                                    </div>
                                    <div class="text-xs text-gray-700 dark:text-gray-300 space-y-1">
                                        <strong>Confidential Remark:</strong>
                                        <x-feedback-box :text="$pts2->$remCol" fallback="Remark not provided" role="co_supervisor" />
                                    </div>
                                </x-role-card>
                            @endif
                        @elseif(in_array($viewPerspective, ['dpgc', 'hod', 'academic_office', 'doaa']))
                            @php
                                $submittedCoSupervisors = [];
                                foreach ($coSupervisors as $i => $coUser) {
                                    $recCol = "co_supervisor_{$i}_recommendation";
                                    $commCol = "co_supervisor_{$i}_student_comment";
                                    $remCol = "co_supervisor_{$i}_confidential_remark";
                                    if (!is_null($pts2->$recCol)) {
                                        $isExt = $coUser->isExternalSupervisor();
                                        $submittedCoSupervisors[] = [
                                            'slot' => $i,
                                            'user' => $coUser,
                                            'roleTitle' => $student ? $student->getSupervisorRoleTitle($coUser) : ($isExt ? 'External Supervisor' : "Co-Supervisor {$i}"),
                                            'inst' => ($isExt && $coUser->externalSupervisorProfile?->affiliated_institute) ? ' - ' . $coUser->externalSupervisorProfile->affiliated_institute : '',
                                            'recommended' => (bool)$pts2->$recCol,
                                            'studentComment' => $pts2->$commCol,
                                            'remark' => $pts2->$remCol,
                                        ];
                                    }
                                }
                            @endphp
                            @if(!empty($submittedCoSupervisors))
                                <x-role-card role="co_supervisor">
                                    <h5 class="text-base font-bold text-blue-900 dark:text-blue-200">Co-Supervisors</h5>
                                    @foreach($submittedCoSupervisors as $index => $co)
                                        <div class="text-xs space-y-1.5 pt-1.5 {{ $index > 0 ? 'border-t border-blue-100 dark:border-blue-900' : '' }}">
                                            <div class="flex items-center justify-between font-semibold text-sm gap-2 sm:gap-4">
                                                <span>{{ $co['roleTitle'] }} <span class="block sm:inline text-xs font-normal text-gray-500 dark:text-gray-400 mt-0.5 sm:mt-0">({{ $co['user']->name }}{{ $co['inst'] }})</span>:</span>
                                                <span class="font-bold whitespace-nowrap shrink-0 {{ $co['recommended'] ? 'text-emerald-600 dark:text-emerald-400' : 'text-red-600 dark:text-red-400' }}">
                                                    {{ $co['recommended'] ? '✓ Recommended' : '❌ Not Recommended' }}
                                                </span>
                                            </div>
                                            <div class="space-y-1">
                                                <strong>Student Comment:</strong>
                                                <x-feedback-box :text="$co['studentComment']" fallback="Comment not provided" role="co_supervisor" />
                                            </div>
                                            <div class="space-y-1">
                                                <strong>Confidential Remark:</strong>
                                                <x-feedback-box :text="$co['remark']" fallback="Remark not provided" role="co_supervisor" />
                                            </div>
                                        </div>
                                    @endforeach
                                </x-role-card>
                            @endif
                        @endif

                        <!-- 3. Academic Office Endorsement -->
                        @if(in_array($viewPerspective, ['academic_office', 'doaa']) && ($pts2->academic_office_submitted_at || $pts2->academic_office_is_verified !== null))
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

                        <!-- 4. DOAA Approval -->
                        @if($viewPerspective === 'doaa' && ($pts2->doaa_submitted_at || !is_null($pts2->doaa_approval)))
                            <x-role-card role="doaa" title="Dean of Academic Affairs (DOAA)">
                                @if(!is_null($pts2->doaa_approval))
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
                                <div class="text-xs text-gray-700 dark:text-gray-300 space-y-1">
                                    <strong>Confidential Remark:</strong>
                                    <x-feedback-box :text="$pts2->doaa_confidential_remark" fallback="Remark not provided" role="doaa" />
                                </div>
                            </x-role-card>
                        @endif

                    </div>
                </div>
            @endif

        </div>
    </div>
</x-app-layout>

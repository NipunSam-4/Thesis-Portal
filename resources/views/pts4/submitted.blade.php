<x-app-layout>
    @php
        $pts4 = $pts4 ?? $form ?? null;
        $form = $form ?? $pts4 ?? null;
        $thesis = $thesis ?? $form?->thesis;
        $student = $student ?? $thesis?->student;
        $studentUser = $studentUser ?? $student?->user;
        $currentUser = $user ?? auth()->user();
        $viewPerspective = $viewPerspective ?? ($currentUser?->isStudent() ? 'student' : ($currentUser?->isAcademicOffice() ? 'academic_office' : ($currentUser?->isDr() ? 'dr' : 'authority')));
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
                        {{ __("Submitted {$formPrefix}-4 Form") }}
                    </h2>
                </div>
                <div class="flex items-center space-x-3 shrink-0">
                    <span class="px-3.5 py-1.5 bg-blue-100 dark:bg-blue-900/60 text-blue-800 dark:text-blue-300 text-xs font-extrabold rounded-full uppercase tracking-wider flex items-center shadow-xs border border-blue-200 dark:border-blue-800">
                        ⏳ In Progress
                    </span>
                </div>
            </div>

            <!-- Common Form Information Sections (1 to 3) -->
            @include('pts4.form_info', ['pts4' => $pts4])

            <!-- Section 4: Role-Scoped Review & Comments -->
            @if($viewPerspective !== 'student')
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 space-y-6">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white border-b border-gray-100 dark:border-gray-700 pb-2">
                        4. Evaluation &amp; Comments
                    </h3>

                    <div class="space-y-4">
                        <!-- 1. Main Supervisor Evaluation -->
                        @if($pts4->main_supervisor_submitted_at || !is_null($pts4->main_supervisor_recommendation))
                            <x-role-card role="main_supervisor">
                                <div class="flex items-center justify-between text-base gap-2 sm:gap-4">
                                    <span class="font-bold text-indigo-900 dark:text-indigo-200 text-base">
                                        Main Supervisor @if(isset($mainSupervisor))<span class="block sm:inline text-xs font-normal text-gray-500 dark:text-gray-400 mt-0.5 sm:mt-0">({{ $mainSupervisor->name }})</span>@endif
                                    </span>
                                    @if(!is_null($pts4->main_supervisor_recommendation))
                                        <span class="font-bold text-xs whitespace-nowrap shrink-0 {{ $pts4->main_supervisor_recommendation ? 'text-emerald-600 dark:text-emerald-400' : 'text-red-600 dark:text-red-400' }}">
                                            {{ $pts4->main_supervisor_recommendation ? '✓ Recommended' : '❌ Not Recommended' }}
                                        </span>
                                    @endif
                                </div>
                                <div class="text-xs text-gray-700 dark:text-gray-300 space-y-1">
                                    <strong>Student Comment:</strong>
                                    <x-feedback-box :text="$pts4->main_supervisor_student_comment" fallback="Comment not provided" role="main_supervisor" />
                                </div>

                                @if($viewPerspective === 'main_supervisor' || in_array($viewPerspective, ['academic_office', 'dr']))
                                    <div class="text-xs text-gray-700 dark:text-gray-300 space-y-1">
                                        <strong>Confidential Remark:</strong>
                                        <x-feedback-box :text="$pts4->main_supervisor_confidential_remark" fallback="Remark not provided" role="main_supervisor" />
                                    </div>
                                @endif
                            </x-role-card>
                        @endif

                        <!-- 2. Co-Supervisors Endorsements -->
                        @if($viewPerspective === 'co_supervisor' && isset($myCoSupSlot) && $myCoSupSlot)
                            @php
                                $recCol = "co_supervisor_{$myCoSupSlot}_recommendation";
                                $commCol = "co_supervisor_{$myCoSupSlot}_student_comment";
                                $remCol = "co_supervisor_{$myCoSupSlot}_confidential_remark";
                                $coUser = auth()->user();
                                $isExt = $coUser ? $coUser->isExternalSupervisor() : false;
                                $roleTitle = ($student && $coUser) ? $student->getSupervisorRoleTitle($coUser) : ($isExt ? 'External Supervisor' : "Co-Supervisor {$myCoSupSlot}");
                                $inst = ($isExt && $coUser?->externalSupervisorProfile?->affiliated_institute) ? ' - ' . $coUser->externalSupervisorProfile->affiliated_institute : '';
                            @endphp
                            @if(!is_null($pts4->$recCol))
                                <x-role-card role="co_supervisor">
                                    <div class="flex items-center justify-between font-semibold gap-2 sm:gap-4">
                                        <h5 class="text-base font-bold text-blue-900 dark:text-blue-200">
                                            {{ $roleTitle }} <span class="block sm:inline text-xs font-normal text-gray-500 dark:text-gray-400 mt-0.5 sm:mt-0">({{ $coUser?->name }}{{ $inst }})</span>
                                        </h5>
                                        <span class="font-bold text-xs whitespace-nowrap shrink-0 {{ $pts4->$recCol ? 'text-emerald-600 dark:text-emerald-400' : 'text-red-600 dark:text-red-400' }}">
                                            {{ $pts4->$recCol ? '✓ Recommended' : '❌ Not Recommended' }}
                                        </span>
                                    </div>
                                    <div class="text-xs text-gray-700 dark:text-gray-300 space-y-1">
                                        <strong>Student Comment:</strong>
                                        <x-feedback-box :text="$pts4->$commCol" fallback="Comment not provided" role="co_supervisor" />
                                    </div>
                                    <div class="text-xs text-gray-700 dark:text-gray-300 space-y-1">
                                        <strong>Confidential Remark:</strong>
                                        <x-feedback-box :text="$pts4->$remCol" fallback="Remark not provided" role="co_supervisor" />
                                    </div>
                                </x-role-card>
                            @endif
                        @elseif(in_array($viewPerspective, ['academic_office', 'dr', 'authority']))
                            @php
                                $submittedCoSupervisors = [];
                                foreach ($coSupervisors as $i => $coUser) {
                                    $recCol = "co_supervisor_{$i}_recommendation";
                                    $commCol = "co_supervisor_{$i}_student_comment";
                                    $remCol = "co_supervisor_{$i}_confidential_remark";
                                    if (!is_null($pts4->$recCol)) {
                                        $isExt = $coUser->isExternalSupervisor();
                                        $submittedCoSupervisors[] = [
                                            'slot' => $i,
                                            'user' => $coUser,
                                            'roleTitle' => $student ? $student->getSupervisorRoleTitle($coUser) : ($isExt ? 'External Supervisor' : "Co-Supervisor {$i}"),
                                            'inst' => ($isExt && $coUser->externalSupervisorProfile?->affiliated_institute) ? ' - ' . $coUser->externalSupervisorProfile->affiliated_institute : '',
                                            'recommended' => (bool)$pts4->$recCol,
                                            'studentComment' => $pts4->$commCol,
                                            'remark' => $pts4->$remCol,
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
                        @if(in_array($viewPerspective, ['academic_office', 'dr']) && ($pts4->academic_office_submitted_at || $pts4->academic_office_is_verified !== null))
                            @php
                                $academicOfficeRemark = $pts4->academic_office_verification_remark;
                            @endphp
                            <x-role-card role="academic_office" title="Academic Office">
                                <x-slot:badge>
                                    <span class="font-bold text-xs text-emerald-600 dark:text-emerald-400 whitespace-nowrap shrink-0">
                                        ✓ Verified &amp; Forwarded
                                    </span>
                                </x-slot:badge>
                                <div class="text-xs text-gray-700 dark:text-gray-300 space-y-1">
                                    <strong>Verification Remark:</strong>
                                    <x-feedback-box :text="$academicOfficeRemark" fallback="Remark not provided" role="academic_office" />
                                </div>
                            </x-role-card>
                        @endif

                        <!-- 4. DR Acceptance -->
                        @if($viewPerspective === 'dr' && ($pts4->dr_submitted_at || !is_null($pts4->dr_acceptance)))
                            <x-role-card role="dr" title="Deputy Registrar (DR)">
                                @if(!is_null($pts4->dr_acceptance))
                                    <x-slot:badge>
                                        <span class="font-bold text-xs whitespace-nowrap shrink-0 {{ $pts4->dr_acceptance ? 'text-emerald-600 dark:text-emerald-400' : 'text-red-600 dark:text-red-400' }}">
                                            {{ $pts4->dr_acceptance ? '✓ Accepted' : '❌ Not Accepted' }}
                                            @if($pts4->acceptedBy)
                                                by: {{ $pts4->acceptedBy->email }}
                                            @endif
                                        </span>
                                    </x-slot:badge>
                                @endif
                                <div class="text-xs text-gray-700 dark:text-gray-300 space-y-1">
                                    <strong>Student Comment:</strong>
                                    <x-feedback-box :text="$pts4->dr_student_comment" fallback="Comment not provided" role="dr" />
                                </div>
                                <div class="text-xs text-gray-700 dark:text-gray-300 space-y-1">
                                    <strong>Confidential Remark:</strong>
                                    <x-feedback-box :text="$pts4->dr_confidential_remark" fallback="Remark not provided" role="dr" />
                                </div>
                            </x-role-card>
                        @endif

                    </div>
                </div>
            @endif

        </div>
    </div>
</x-app-layout>

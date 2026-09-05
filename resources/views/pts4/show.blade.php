<x-app-layout>
    @php
        $pts4 = $pts4 ?? $form ?? null;
        $form = $form ?? $pts4 ?? null;
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
                        @if($pts4->status === 'approved')
                            {{ __("Approved {$formPrefix}-4 Form Details & Remarks") }}
                        @else
                            {{ __("Rejected {$formPrefix}-4 Form Details & Remarks") }}
                        @endif
                    </h2>
                </div>
                <div class="flex items-center space-x-3 shrink-0">
                    @if($pts4->status === 'approved')
                        <a href="{{ route('student.pts4.certificate', $pts4->id) }}" class="px-3.5 py-1.5 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold rounded-xl shadow-xs transition inline-flex items-center gap-1.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                            <span>Download Thesis Certificate</span>
                        </a>
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

            <!-- Common Form Information Sections (1 to 3) -->
            @include('pts4.form-info', ['pts4' => $pts4])

            @php
                $currentUser = $user ?? auth()->user();
                $isStudent = $currentUser?->isStudent() ?? false;
                $hasAnyCo = !empty($coSupervisors);
            @endphp

            <!-- Section 4: Authority Comments (Student) or Authority Recommendations & Remarks (Authorities) -->
            @if(in_array($pts4->status, ['approved', 'rejected']))
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 space-y-6">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white border-b border-gray-100 dark:border-gray-700 pb-2">
                        {{ $isStudent ? '4. Authority Comments' : '4. Authority Recommendations & Remarks' }}
                    </h3>

                    <div class="space-y-4">
                        <!-- Main Supervisor -->
                        @if($pts4->main_supervisor_submitted_at || $pts4->main_supervisor_recommendation !== null)
                            <x-role-card role="main_supervisor">
                                <div class="flex items-center justify-between text-base gap-2 sm:gap-4">
                                    <span class="font-bold text-indigo-900 dark:text-indigo-200 text-base">
                                        Main Supervisor @if(isset($mainSupervisor))<span class="block sm:inline text-xs font-normal text-gray-500 dark:text-gray-400 mt-0.5 sm:mt-0">({{ $mainSupervisor->name }})</span>@endif
                                    </span>
                                    @if(!$isStudent && $pts4->main_supervisor_recommendation !== null)
                                        <span class="font-bold text-xs whitespace-nowrap shrink-0 {{ $pts4->main_supervisor_recommendation ? 'text-emerald-600 dark:text-emerald-400' : 'text-red-600 dark:text-red-400' }}">
                                            {{ $pts4->main_supervisor_recommendation ? '✓ Recommended' : '❌ Not Recommended' }}
                                        </span>
                                    @endif
                                </div>
                                <div class="text-xs text-gray-700 dark:text-gray-300 space-y-1">
                                    <strong>Student Comment:</strong>
                                    <x-feedback-box :text="$pts4->main_supervisor_student_comment" fallback="Comment not provided" role="main_supervisor" />
                                </div>
                        
                                @if(!$isStudent)
                                    <div class="text-xs text-gray-700 dark:text-gray-300 space-y-1">
                                        <strong>Confidential Remark:</strong>
                                        <x-feedback-box :text="$pts4->main_supervisor_confidential_remark" fallback="Remark not provided" role="main_supervisor" />
                                    </div>
                                @endif
                            </x-role-card>
                        @endif

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
                                            @if(!$isStudent && !is_null($pts4->$recCol))
                                                <span class="font-bold whitespace-nowrap shrink-0 {{ $pts4->$recCol ? 'text-emerald-600 dark:text-emerald-400' : 'text-red-600 dark:text-red-400' }}">
                                                    {{ $pts4->$recCol ? '✓ Recommended' : '❌ Not Recommended' }}
                                                </span>
                                            @endif
                                        </div>
                                        <div class="space-y-1">
                                            <strong>Student Comment:</strong>
                                            <x-feedback-box :text="$pts4->$comCol" fallback="Comment not provided" role="co_supervisor" />
                                        </div>
                                        @if(!$isStudent)
                                            <div class="space-y-1">
                                                <strong>Confidential Remark:</strong>
                                                <x-feedback-box :text="$pts4->$remCol" fallback="Remark not provided" role="co_supervisor" />
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            </x-role-card>
                        @endif

                        <!-- Academic Office Endorsement (Only visible to non-students) -->
                        @if(!$isStudent && ($pts4->academic_office_submitted_at || $pts4->academic_office_is_verified !== null))
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

                        <!-- DR Approval -->
                        @if($pts4->dr_submitted_at || $pts4->dr_approval !== null)
                            <x-role-card role="dr" title="Deputy Registrar (DR)">
                                @if(!$isStudent && $pts4->dr_approval !== null)
                                    <x-slot:badge>
                                        <span class="font-bold text-xs whitespace-nowrap shrink-0 {{ $pts4->dr_approval ? 'text-emerald-600 dark:text-emerald-400' : 'text-red-600 dark:text-red-400' }}">
                                            {{ $pts4->dr_approval ? '✓ Approved' : '❌ Rejected' }}
                                            @if($pts4->approvedBy)
                                                by: {{ $pts4->approvedBy->email }}
                                            @endif
                                        </span>
                                    </x-slot:badge>
                                @endif
                                <div class="text-xs text-gray-700 dark:text-gray-300 space-y-1">
                                    <strong>Student Comment:</strong>
                                    <x-feedback-box :text="$pts4->dr_student_comment" fallback="Comment not provided" role="dr" />
                                </div>
                                @if(!$isStudent)
                                    <div class="text-xs text-gray-700 dark:text-gray-300 space-y-1">
                                        <strong>Confidential Remark:</strong>
                                        <x-feedback-box :text="$pts4->dr_confidential_remark" fallback="Remark not provided" role="dr" />
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

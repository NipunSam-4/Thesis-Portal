<x-app-layout>
    @php
        $pts1 = $pts1 ?? $form ?? null;
        $form = $form ?? $pts1 ?? null;
        $thesis = $thesis ?? $form?->thesis;
        $student = $student ?? $thesis?->student;
        $studentUser = $studentUser ?? $student?->user;
        $currentUser = $user ?? auth()->user();
        $viewPerspective = $viewPerspective ?? ($currentUser?->isStudent() ? 'student' : ($currentUser?->isDpgc() ? 'dpgc' : ($currentUser?->isHod() ? 'hod' : ($currentUser?->isAcademicOffice() ? 'academic_office' : ($currentUser?->isDoaa() ? 'doaa' : 'authority')))));
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
                        {{ __("Submitted {$formPrefix}-1 Form") }}
                    </h2>
                </div>
                <div class="flex items-center space-x-3 shrink-0">
                    <span class="px-3.5 py-1.5 bg-blue-100 dark:bg-blue-900/60 text-blue-800 dark:text-blue-300 text-xs font-extrabold rounded-full uppercase tracking-wider flex items-center shadow-xs border border-blue-200 dark:border-blue-800">
                        ⏳ In Progress
                    </span>
                </div>
            </div>

            <!-- Common Form Information Sections (1 to 5) -->
            @include('pts1.form_info', ['pts1' => $pts1])

            <!-- Section 6: Role-Scoped Review & Comments -->
            @if($viewPerspective !== 'student')
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 space-y-6">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white border-b border-gray-100 dark:border-gray-700 pb-2">
                        6. Evaluation &amp; Comments
                    </h3>

                    <div class="space-y-4">
                        <!-- 1. Main Supervisor Evaluation (Visible to Main Supervisor, Co-Supervisors, PSPC, DPGC, HOD, DOAA) -->
                        @if(!is_null($pts1->main_supervisor_submitted_at) || !is_null($pts1->main_supervisor_recommendation))
                            <x-role-card role="main_supervisor">
                                <div class="flex items-center justify-between text-base gap-2 sm:gap-4">
                                    <span class="font-bold text-indigo-900 dark:text-indigo-200 text-base">
                                        Main Supervisor @if(isset($mainSupervisor))<span class="block sm:inline text-xs font-normal text-gray-500 dark:text-gray-400 mt-0.5 sm:mt-0">({{ $mainSupervisor->name }})</span>@endif
                                    </span>
                                    @if($pts1->work_status)
                                        <span class="px-2.5 py-1 bg-emerald-600 text-white font-bold rounded-lg uppercase text-[10px] text-center leading-tight whitespace-normal max-w-[120px] sm:max-w-none">
                                            Open Seminar Status: {{ strtoupper($pts1->work_status) }}
                                        </span>
                                    @elseif(!is_null($pts1->main_supervisor_recommendation))
                                        <span class="font-bold whitespace-nowrap shrink-0 {{ $pts1->main_supervisor_recommendation ? 'text-emerald-600 dark:text-emerald-400' : 'text-red-600 dark:text-red-400' }}">
                                            {{ $pts1->main_supervisor_recommendation ? '✓ Recommended' : '❌ Not Recommended' }}
                                        </span>
                                    @endif
                                </div>
                                <div class="text-xs text-gray-700 dark:text-gray-300">
                                    <strong>Additional Comment:</strong>
                                    <x-feedback-box :text="$pts1->main_supervisor_student_comment" fallback="Comment not provided" role="main_supervisor" />
                                </div>

                                @if($viewPerspective === 'main_supervisor' || in_array($viewPerspective, ['dpgc', 'hod', 'academic_office', 'doaa']))
                                    <div class="text-xs text-gray-700 dark:text-gray-300 pt-1">
                                        <strong>Confidential Remark:</strong>
                                        <x-feedback-box :text="$pts1->main_supervisor_confidential_remark" fallback="Remark not provided" role="main_supervisor" />
                                    </div>
                                @endif
                            </x-role-card>
                        @endif

                        <!-- 2. Co-Supervisors Endorsements -->
                        <!-- If user is assigned to a Co-Supervisor slot ($myCoSupSlot), show their individual card -->
                        <!-- If user is a higher authority or PSPC member without co-sup slot, show ALL submitted co-supervisors -->
                        @if($myCoSupSlot)
                            @php
                                $recCol = "co_supervisor_{$myCoSupSlot}_recommendation";
                                $commCol = "co_supervisor_{$myCoSupSlot}_student_comment";
                                $remCol = "co_supervisor_{$myCoSupSlot}_confidential_remark";
                                $coUser = auth()->user();
                                $isExt = $coUser ? $coUser->isExternalSupervisor() : false;
                                $roleTitle = ($student && $coUser) ? $student->getSupervisorRoleTitle($coUser) : ($isExt ? 'External Supervisor' : "Co-Supervisor {$myCoSupSlot}");
                                $inst = ($isExt && $coUser?->externalSupervisorProfile?->affiliated_institute) ? ' - ' . $coUser->externalSupervisorProfile->affiliated_institute : '';
                            @endphp
                            @if(!is_null($pts1->$recCol))
                                <x-role-card role="co_supervisor">
                                    <div class="flex items-center justify-between font-semibold gap-2 sm:gap-4">
                                        <h5 class="text-base font-bold text-blue-900 dark:text-blue-200">
                                            {{ $roleTitle }} <span class="block sm:inline text-xs font-normal text-gray-500 dark:text-gray-400 mt-0.5 sm:mt-0">({{ $coUser?->name }}{{ $inst }})</span>
                                        </h5>
                                        <span class="font-bold text-xs whitespace-nowrap shrink-0 {{ $pts1->$recCol ? 'text-emerald-600 dark:text-emerald-400' : 'text-red-600 dark:text-red-400' }}">
                                            {{ $pts1->$recCol ? '✓ Recommended' : '❌ Not Recommended' }}
                                        </span>
                                    </div>
                                    <div class="text-xs text-gray-700 dark:text-gray-300">
                                        <strong>Confidential Remark:</strong>
                                        <x-feedback-box :text="$pts1->$remCol" fallback="Remark not provided" role="co_supervisor" />
                                    </div>
                                </x-role-card>
                            @endif
                        @elseif(in_array($viewPerspective, ['pspc_member', 'dpgc', 'hod', 'academic_office', 'doaa']))
                            @php
                                $submittedCoSupervisors = [];
                                foreach ($coSupervisors as $i => $coUser) {
                                    $recCol = "co_supervisor_{$i}_recommendation";
                                    $remCol = "co_supervisor_{$i}_confidential_remark";
                                    if (!is_null($pts1->$recCol)) {
                                        $isExt = $coUser->isExternalSupervisor();
                                        $submittedCoSupervisors[] = [
                                            'slot' => $i,
                                            'user' => $coUser,
                                            'roleTitle' => $student ? $student->getSupervisorRoleTitle($coUser) : ($isExt ? 'External Supervisor' : "Co-Supervisor {$i}"),
                                            'inst' => ($isExt && $coUser->externalSupervisorProfile?->affiliated_institute) ? ' - ' . $coUser->externalSupervisorProfile->affiliated_institute : '',
                                            'recommended' => (bool)$pts1->$recCol,
                                            'remark' => $pts1->$remCol,
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
                                                <strong>Confidential Remark:</strong>
                                                <x-feedback-box :text="$co['remark']" fallback="Remark not provided" role="co_supervisor" />
                                            </div>
                                        </div>
                                    @endforeach
                                </x-role-card>
                            @endif
                        @endif

                        <!-- 3. PSPC Committee Endorsements -->
                        <!-- If user is assigned to a PSPC slot ($myPspcSlot), show their individual card -->
                        <!-- If user is a higher authority, show ALL submitted PSPC members -->
                        @if($myPspcSlot)
                            @php
                                $recCol = "pspc_member_{$myPspcSlot}_recommendation";
                                $remCol = "pspc_member_{$myPspcSlot}_confidential_remark";
                                $pspcUser = auth()->user();
                            @endphp
                            @if(!is_null($pts1->$recCol))
                                <x-role-card role="pspc">
                                    <div class="flex items-center justify-between font-semibold gap-2 sm:gap-4">
                                        <h5 class="text-base font-bold text-cyan-900 dark:text-cyan-200">
                                            PSPC Member {{ $myPspcSlot }} <span class="block sm:inline text-xs font-normal text-gray-500 dark:text-gray-400 mt-0.5 sm:mt-0">({{ $pspcUser?->name }})</span>
                                        </h5>
                                        <span class="font-bold text-xs whitespace-nowrap shrink-0 {{ $pts1->$recCol ? 'text-emerald-600 dark:text-emerald-400' : 'text-red-600 dark:text-red-400' }}">
                                            {{ $pts1->$recCol ? '✓ Recommended' : '❌ Not Recommended' }}
                                        </span>
                                    </div>
                                    <div class="text-xs text-gray-700 dark:text-gray-300 space-y-1">
                                        <strong>Confidential Remark:</strong>
                                        <x-feedback-box :text="$pts1->$remCol" fallback="Remark not provided" role="pspc" />
                                    </div>
                                </x-role-card>
                            @endif
                        @elseif(in_array($viewPerspective, ['dpgc', 'hod', 'academic_office', 'doaa']))
                            @php
                                $submittedPspcMembers = [];
                                foreach ($pspcMembers as $i => $pspcUser) {
                                    $recCol = "pspc_member_{$i}_recommendation";
                                    $remCol = "pspc_member_{$i}_confidential_remark";
                                    if (!is_null($pts1->$recCol)) {
                                        $submittedPspcMembers[] = [
                                            'slot' => $i,
                                            'user' => $pspcUser,
                                            'recommended' => (bool)$pts1->$recCol,
                                            'remark' => $pts1->$remCol,
                                        ];
                                    }
                                }
                            @endphp
                            @if(!empty($submittedPspcMembers))
                                <x-role-card role="pspc">
                                    <h5 class="text-base font-bold text-cyan-900 dark:text-cyan-200">PSPC Committee</h5>
                                    @foreach($submittedPspcMembers as $index => $pspc)
                                        <div class="text-xs space-y-1.5 pt-1.5 {{ $index > 0 ? 'border-t border-cyan-100 dark:border-cyan-900' : '' }}">
                                            <div class="flex items-center justify-between font-semibold text-sm gap-2 sm:gap-4">
                                                <span>PSPC Member {{ $pspc['slot'] }} <span class="block sm:inline text-xs font-normal text-gray-500 dark:text-gray-400 mt-0.5 sm:mt-0">({{ $pspc['user']->name }})</span>:</span>
                                                <span class="font-bold whitespace-nowrap shrink-0 {{ $pspc['recommended'] ? 'text-emerald-600 dark:text-emerald-400' : 'text-red-600 dark:text-red-400' }}">
                                                    {{ $pspc['recommended'] ? '✓ Recommended' : '❌ Not Recommended' }}
                                                </span>
                                            </div>
                                            <div class="space-y-1">
                                                <strong>Confidential Remark:</strong>
                                                <x-feedback-box :text="$pspc['remark']" fallback="Remark not provided" role="pspc" />
                                            </div>
                                        </div>
                                    @endforeach
                                </x-role-card>
                            @endif
                        @endif

                        <!-- 4. DPGC Endorsement -->
                        @if(in_array($viewPerspective, ['dpgc', 'hod', 'academic_office', 'doaa']) && !is_null($pts1->dpgc_recommendation))
                            <x-role-card role="dpgc" title="Department Postgraduate Committee (DPGC)">
                                <x-slot:badge>
                                    <span class="font-bold text-xs whitespace-nowrap shrink-0 {{ $pts1->dpgc_recommendation ? 'text-emerald-600 dark:text-emerald-400' : 'text-red-600 dark:text-red-400' }}">
                                        {{ $pts1->dpgc_recommendation ? '✓ Recommended' : '❌ Not Recommended' }}
                                    </span>
                                </x-slot:badge>
                                <div class="text-xs text-gray-700 dark:text-gray-300 space-y-1">
                                    <strong>Student Comment:</strong>
                                    <x-feedback-box :text="$pts1->dpgc_student_comment" fallback="Comment not provided" role="dpgc" />
                                </div>
                                <div class="text-xs text-gray-700 dark:text-gray-300 space-y-1">
                                    <strong>Confidential Remark:</strong>
                                    <x-feedback-box :text="$pts1->dpgc_confidential_remark" fallback="Remark not provided" role="dpgc" />
                                </div>
                            </x-role-card>
                        @endif

                        <!-- 5. HOD Endorsement -->
                        @if(in_array($viewPerspective, ['hod', 'academic_office', 'doaa']) && !is_null($pts1->hod_recommendation))
                            <x-role-card role="hod" title="Head of Department (HOD)">
                                <x-slot:badge>
                                    <span class="font-bold text-xs whitespace-nowrap shrink-0 {{ $pts1->hod_recommendation ? 'text-emerald-600 dark:text-emerald-400' : 'text-red-600 dark:text-red-400' }}">
                                        {{ $pts1->hod_recommendation ? '✓ Recommended' : '❌ Not Recommended' }}
                                    </span>
                                </x-slot:badge>
                                <div class="text-xs text-gray-700 dark:text-gray-300 space-y-1">
                                    <strong>Student Comment:</strong>
                                    <x-feedback-box :text="$pts1->hod_student_comment" fallback="Comment not provided" role="hod" />
                                </div>
                                <div class="text-xs text-gray-700 dark:text-gray-300 space-y-1">
                                    <strong>Confidential Remark:</strong>
                                    <x-feedback-box :text="$pts1->hod_confidential_remark" fallback="Remark not provided" role="hod" />
                                </div>
                            </x-role-card>
                        @endif

                        <!-- 6. Academic Office Verification -->
                        @if(in_array($viewPerspective, ['academic_office', 'doaa']) && (!is_null($pts1->academic_office_is_verified) || !is_null($pts1->academic_office_submitted_at)))
                            <x-role-card role="academic_office" title="Academic Office">
                                <x-slot:badge>
                                    <span class="font-bold text-xs whitespace-nowrap shrink-0 {{ $pts1->academic_office_is_verified ? 'text-emerald-600 dark:text-emerald-400' : 'text-red-600 dark:text-red-400' }}">
                                        {{ $pts1->academic_office_is_verified ? '✓ Verified & Forwarded' : '❌ Verification Rejected' }}
                                    </span>
                                </x-slot:badge>
                                <div class="text-xs text-gray-700 dark:text-gray-300 space-y-1">
                                    <strong>Verification Remark:</strong>
                                    <x-feedback-box :text="$pts1->academic_office_confidential_remark" fallback="Remark not provided" role="academic_office" />
                                </div>
                            </x-role-card>
                        @endif

                        <!-- 7. DOAA Approval -->
                        @if($viewPerspective === 'doaa' && !is_null($pts1->doaa_approval))
                            <x-role-card role="doaa" title="Dean of Academic Affairs (DOAA)">
                                <x-slot:badge>
                                    <span class="font-bold text-xs whitespace-nowrap shrink-0 {{ $pts1->doaa_approval ? 'text-emerald-600 dark:text-emerald-400' : 'text-red-600 dark:text-red-400' }}">
                                        {{ $pts1->doaa_approval ? '✓ Approved' : '❌ Not Approved' }}
                                    </span>
                                </x-slot:badge>
                                <div class="text-xs text-gray-700 dark:text-gray-300 space-y-1">
                                    <strong>Student Comment:</strong>
                                    <x-feedback-box :text="$pts1->doaa_student_comment" fallback="Comment not provided" role="doaa" />
                                </div>
                                <div class="text-xs text-gray-700 dark:text-gray-300 space-y-1">
                                    <strong>Confidential Remark:</strong>
                                    <x-feedback-box :text="$pts1->doaa_confidential_remark" fallback="Remark not provided" role="doaa" />
                                </div>
                            </x-role-card>
                        @endif

                    </div>
                </div>
            @endif

        </div>
    </div>

    @php
        $existingPubListUrl = $pts1->getEffectivePublicationListPath()
            ? route('pts.document.serve', ['pts1', $pts1->id, $pts1->getEffectivePublicationListField()])
            : null;
    @endphp

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const pubListUrl = @json($existingPubListUrl);
            if (pubListUrl) {
                window.previewExcelUrl(pubListUrl, {
                    titlePrefix: 'Publication and Other Recognition Preview'
                });
            }
        });
    </script>
</x-app-layout>

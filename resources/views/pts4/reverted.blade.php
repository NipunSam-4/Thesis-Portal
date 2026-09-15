<x-app-layout>
    @php
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
                        {{ __("Reverted {$formPrefix}-4 Form Details & Remarks") }}
                    </h2>
                </div>
                <div class="flex items-center space-x-3 shrink-0">
                    <span class="px-3.5 py-1.5 bg-amber-100 dark:bg-amber-900/60 text-amber-800 dark:text-amber-300 text-xs font-extrabold rounded-full uppercase tracking-wider flex items-center shadow-xs border border-amber-200 dark:border-amber-800">
                        ⚠️ Reverted
                    </span>
                </div>
            </div>
            
            <!-- Common Form Information Sections (1 to 3) -->
            @include('pts4.form_info', ['pts4' => $pts4])

            <!-- Section 4: Authority Recommendations & Remarks -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 space-y-6">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white border-b border-gray-100 dark:border-gray-700 pb-2">
                    4. Authority Recommendations &amp; Remarks
                </h3>

                @php
                    $revertedCoSupervisors = [];
                    foreach ($coSupervisors as $i => $coUser) {
                        $recCol = "co_supervisor_{$i}_recommendation";
                        $studComCol = "co_supervisor_{$i}_student_comment";
                        $remCol = "co_supervisor_{$i}_confidential_remark";
                        if (!is_null($pts4->$recCol)) {
                            $isExt = $coUser->isExternalSupervisor();
                            $revertedCoSupervisors[] = [
                                'slot' => $i,
                                'user' => $coUser,
                                'roleTitle' => $student ? $student->getSupervisorRoleTitle($coUser) : ($isExt ? 'External Supervisor' : "Co-Supervisor {$i}"),
                                'inst' => ($isExt && $coUser->externalSupervisorProfile?->affiliated_institute) ? ' - ' . $coUser->externalSupervisorProfile->affiliated_institute : '',
                                'recommended' => (bool)$pts4->$recCol,
                                'studentComment' => $pts4->$studComCol,
                                'remark' => $pts4->$remCol,
                            ];
                        }
                    }

                    $hasAnyComment = !is_null($pts4->main_supervisor_recommendation)
                        || !empty($revertedCoSupervisors)
                        || !is_null($pts4->academic_office_is_verified)
                        || !is_null($pts4->dr_acceptance);
                @endphp

                @if($hasAnyComment)
                    <div class="space-y-4">
                        <!-- Main Supervisor Evaluation -->
                        @if(!is_null($pts4->main_supervisor_recommendation))
                            <x-role-card role="main_supervisor">
                                <div class="flex items-center justify-between text-base gap-2 sm:gap-4">
                                    <span class="font-bold text-indigo-900 dark:text-indigo-200 text-base">
                                        Main Supervisor @if(isset($mainSupervisor))<span class="block sm:inline text-xs font-normal text-gray-500 dark:text-gray-400 mt-0.5 sm:mt-0">({{ $mainSupervisor->name }})</span>@endif
                                    </span>
                                    <span class="font-bold text-xs whitespace-nowrap shrink-0 {{ $pts4->main_supervisor_recommendation ? 'text-emerald-600 dark:text-emerald-400' : 'text-red-600 dark:text-red-400' }}">
                                        {{ $pts4->main_supervisor_recommendation ? '✓ Recommended' : '❌ Not Recommended' }}
                                    </span>
                                </div>
                                <div class="text-xs text-gray-700 dark:text-gray-300 space-y-1">
                                    <strong>Student Comment:</strong>
                                    <x-feedback-box :text="$pts4->main_supervisor_student_comment" fallback="Comment not provided" role="main_supervisor" />
                                </div>

                                <div class="text-xs text-gray-700 dark:text-gray-300 space-y-1">
                                    <strong>Confidential Remark:</strong>
                                    <x-feedback-box :text="$pts4->main_supervisor_confidential_remark" fallback="Remark not provided" role="main_supervisor" />
                                </div>
                            </x-role-card>
                        @endif

                        <!-- Co-Supervisors Endorsements -->
                        @if(!empty($revertedCoSupervisors))
                            <x-role-card role="co_supervisor">
                                <h5 class="text-base font-bold text-blue-900 dark:text-blue-200">Co-Supervisors</h5>
                                @foreach($revertedCoSupervisors as $index => $co)
                                    <div class="text-xs space-y-1.5 pt-1.5 {{ $index > 0 ? 'border-t border-blue-100 dark:border-blue-900' : '' }}">
                                        <div class="flex items-center justify-between font-semibold text-sm gap-2 sm:gap-4">
                                            <span>
                                                {{ $co['roleTitle'] }} <span class="block sm:inline text-xs font-normal text-gray-500 dark:text-gray-400 mt-0.5 sm:mt-0">({{ $co['user']->name }}{{ $co['inst'] }})</span>:
                                            </span>
                                            <span class="font-bold text-xs whitespace-nowrap shrink-0 {{ $co['recommended'] ? 'text-emerald-600 dark:text-emerald-400' : 'text-red-600 dark:text-red-400' }}">
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

                        <!-- Academic Office Endorsement -->
                        @if(!is_null($pts4->academic_office_is_verified))
                            <x-role-card role="academic_office" title="Academic Office">
                                <x-slot:badge>
                                    <span class="font-bold text-xs whitespace-nowrap shrink-0 {{ $pts4->academic_office_is_verified ? 'text-emerald-600 dark:text-emerald-400' : 'text-red-600 dark:text-red-400' }}">
                                        {{ $pts4->academic_office_is_verified ? '✓ Verified & Forwarded' : '❌ Verification Failed' }}
                                    </span>
                                </x-slot:badge>

                                <div class="text-xs text-gray-700 dark:text-gray-300 space-y-1">
                                    <strong>Verification Remark:</strong>
                                    <x-feedback-box :text="$pts4->academic_office_verification_remark" fallback="Remark not provided" role="academic_office" />
                                </div>
                            </x-role-card>
                        @endif

                        <!-- DR Acceptance -->
                        @if(!is_null($pts4->dr_acceptance))
                            <x-role-card role="dr" title="Deputy Registrar (DR)">
                                <x-slot:badge>
                                    <span class="font-bold text-xs whitespace-nowrap shrink-0 {{ $pts4->dr_acceptance ? 'text-emerald-600 dark:text-emerald-400' : 'text-red-600 dark:text-red-400' }}">
                                        {{ $pts4->dr_acceptance ? '✓ Accepted' : '❌ Not Accepted' }}
                                        @if($pts4->acceptedBy)
                                            by: {{ $pts4->acceptedBy->email }}
                                        @endif
                                    </span>
                                </x-slot:badge>
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

            <!-- Section 5: Reversion Log -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 space-y-6">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white border-b border-gray-100 dark:border-gray-700 pb-2">
                    5. Reversion
                </h3>
                <div class="p-4 bg-amber-50 dark:bg-amber-950/40 border-l-4 border-amber-500 rounded-xl">
                    <div class="font-bold text-amber-900 dark:text-amber-200 text-sm">
                        ⚠️ Reverted by {{ $pts4->getRevertedByRoleLabel() }}
                    </div>
                    @if($pts4->reversion_comment)
                        <div class="mt-2 text-xs text-gray-700 dark:text-gray-300">
                            <strong>Reversion Comment:</strong>
                            <p class="italic bg-white dark:bg-gray-800 p-3 rounded border border-amber-200 dark:border-amber-900 mt-1 whitespace-pre-wrap break-words [overflow-wrap:anywhere]">{{ trim($pts4->reversion_comment) }}</p>
                        </div>
                    @else
                        <p class="text-xs italic text-gray-500 mt-1">No written reversion comment provided.</p>
                    @endif
                </div>
            </div>

        </div>
    </div>
</x-app-layout>

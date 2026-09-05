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
                        {{ __("Reverted {$formPrefix}-1 Form Details & Remarks") }}
                    </h2>
                </div>
                <div class="flex items-center space-x-3 shrink-0">
                    <span class="px-3.5 py-1.5 bg-amber-100 dark:bg-amber-900/60 text-amber-800 dark:text-amber-300 text-xs font-extrabold rounded-full uppercase tracking-wider flex items-center shadow-xs border border-amber-200 dark:border-amber-800">
                        ⚠️ Reverted
                    </span>
                </div>
            </div>

            <!-- Common Form Information Sections (1 to 5) -->
            @include('pts1.form_info', ['pts1' => $pts1])

            <!-- Section 6: Authority Recommendations & Remarks -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 space-y-6">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white border-b border-gray-100 dark:border-gray-700 pb-2">
                    6. Authority Recommendations & Remarks
                </h3>

                @php
                    $revertedCoSupervisors = [];
                    foreach ($coSupervisors as $i => $coUser) {
                        $recCol = "co_supervisor_{$i}_recommendation";
                        $remCol = "co_supervisor_{$i}_confidential_remark";
                        if (!is_null($pts1->$recCol)) {
                            $isExt = $coUser->isExternalSupervisor();
                            $revertedCoSupervisors[] = [
                                'slot' => $i,
                                'user' => $coUser,
                                'roleTitle' => $student ? $student->getSupervisorRoleTitle($coUser) : ($isExt ? 'External Supervisor' : "Co-Supervisor {$i}"),
                                'inst' => ($isExt && $coUser->externalSupervisorProfile?->affiliated_institute) ? ' - ' . $coUser->externalSupervisorProfile->affiliated_institute : '',
                                'recommended' => (bool)$pts1->$recCol,
                                'remark' => $pts1->$remCol,
                            ];
                        }
                    }

                    $revertedPspcMembers = [];
                    foreach ($pspcMembers as $i => $pspcUser) {
                        $recCol = "pspc_member_{$i}_recommendation";
                        $remCol = "pspc_member_{$i}_confidential_remark";
                        if (!is_null($pts1->$recCol)) {
                            $revertedPspcMembers[] = [
                                'slot' => $i,
                                'user' => $pspcUser,
                                'recommended' => (bool)$pts1->$recCol,
                                'remark' => $pts1->$remCol,
                            ];
                        }
                    }

                    $hasAnyComment = !is_null($pts1->main_supervisor_recommendation) 
                        || !empty($revertedCoSupervisors) 
                        || !empty($revertedPspcMembers) 
                        || !is_null($pts1->dpgc_recommendation) 
                        || !is_null($pts1->hod_recommendation) 
                        || !is_null($pts1->academic_office_is_verified) 
                        || !is_null($pts1->doaa_approval);
                @endphp

                @if($hasAnyComment)
                    <div class="space-y-4">
                        <!-- Main Supervisor Evaluation -->
                        @if(!is_null($pts1->main_supervisor_recommendation))
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
                                <div class="text-xs text-gray-700 dark:text-gray-300 space-y-1">
                                    <strong>Additional Comment:</strong>
                                    <x-feedback-box :text="$pts1->main_supervisor_student_comment" fallback="Comment not provided" role="main_supervisor" />
                                </div>

                                <div class="text-xs text-gray-700 dark:text-gray-300 space-y-1">
                                    <strong>Confidential Remark:</strong>
                                    <x-feedback-box :text="$pts1->main_supervisor_confidential_remark" fallback="Remark not provided" role="main_supervisor" />
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

                        <!-- PSPC Committee Endorsements -->
                        @if(!empty($revertedPspcMembers))
                            <x-role-card role="pspc">
                                <h5 class="text-base font-bold text-cyan-900 dark:text-cyan-200">PSPC Committee</h5>
                                @foreach($revertedPspcMembers as $index => $pspc)
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

                        <!-- DPGC Endorsement -->
                        @if(!is_null($pts1->dpgc_recommendation))
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
                                    <x-feedback-box :text="$pts1->dpgc_remarks ?? $pts1->dpgc_confidential_remark" fallback="Remark not provided" role="dpgc" />
                                </div>
                            </x-role-card>
                        @endif

                        <!-- HOD Endorsement -->
                        @if(!is_null($pts1->hod_recommendation))
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
                                    <x-feedback-box :text="$pts1->hod_remarks ?? $pts1->hod_confidential_remark" fallback="Remark not provided" role="hod" />
                                </div>
                            </x-role-card>
                        @endif

                        <!-- Academic Office Remarks -->
                        @if(!is_null($pts1->academic_office_is_verified))
                            <x-role-card role="academic_office" title="Academic Office">
                                <x-slot:badge>
                                    <span class="font-bold text-xs whitespace-nowrap shrink-0 text-emerald-600 dark:text-emerald-400">
                                        ✓ Verified & Forwarded
                                    </span>
                                </x-slot:badge>
                                <div class="text-xs text-gray-700 dark:text-gray-300 space-y-1">
                                    <strong>Verification Remark:</strong>
                                    <x-feedback-box :text="$pts1->academic_office_confidential_remark" fallback="Verification Remark not provided" role="academic_office" />
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

            <!-- Section 7: Reversion Log -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 space-y-6">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white border-b border-gray-100 dark:border-gray-700 pb-2">
                    7. Reversion
                </h3>
                <div class="p-4 bg-amber-50 dark:bg-amber-950/40 border-l-4 border-amber-500 rounded-xl">
                    <div class="font-bold text-amber-900 dark:text-amber-200 text-sm">
                        ⚠️ Reverted by {{ $pts1->getRevertedByRoleLabel() }}
                    </div>
                    @if($pts1->getReversionComment())
                        <div class="mt-2 text-xs text-gray-700 dark:text-gray-300">
                            <strong>Reversion Comment:</strong>
                            <p class="italic bg-white dark:bg-gray-800 p-3 rounded border border-amber-200 dark:border-amber-900 mt-1 whitespace-pre-wrap break-words [overflow-wrap:anywhere]">{{ trim($pts1->getReversionComment()) }}</p>
                        </div>
                    @else
                        <p class="text-xs italic text-gray-500 mt-1">No written reversion comment provided.</p>
                    @endif
                </div>
            </div>
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
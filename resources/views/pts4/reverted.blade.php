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

            <!-- Common Form Information Sections (1 to 3) -->
            @include('pts4.form-info', ['pts4' => $pts4])

            <!-- Section 4: Authority Recommendations & Remarks -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 space-y-6">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white border-b border-gray-100 dark:border-gray-700 pb-2">
                    4. Authority Recommendations &amp; Remarks
                </h3>

                @php
                    $hasCoSigs = false;
                    for ($i = 1; $i <= 10; $i++) {
                        $recCol = "co_supervisor_{$i}_recommendation";
                        if (!is_null($pts4->$recCol)) {
                            $hasCoSigs = true;
                            break;
                        }
                    }
                    $hasAnyComment = !is_null($pts4->main_supervisor_recommendation)
                        || $hasCoSigs
                        || !is_null($pts4->academic_office_is_verified)
                        || !is_null($pts4->dr_approval);
                @endphp

                @if($hasAnyComment)
                    <div class="space-y-4">
                        <!-- Main Supervisor Evaluation -->
                        @if(!is_null($pts4->main_supervisor_recommendation))
                            <x-role-card role="main_supervisor">
                                <div class="flex items-center justify-between text-s gap-2 sm:gap-4">
                                    <span class="font-bold text-indigo-900 dark:text-indigo-200">
                                        Main Supervisor @if(isset($mainSupervisor))<span class="block sm:inline text-xs font-normal text-gray-500 dark:text-gray-400 mt-0.5 sm:mt-0">({{ $mainSupervisor->name }})</span>@endif
                                    </span>
                                    @if(!is_null($pts4->main_supervisor_recommendation))
                                        <span class="px-2.5 py-1 font-bold rounded-lg uppercase text-[10px] text-center leading-tight whitespace-normal max-w-[140px] sm:max-w-none {{ $pts4->main_supervisor_recommendation ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/60 dark:text-emerald-300' : 'bg-red-100 text-red-800 dark:bg-red-900/60 dark:text-red-300' }}">
                                            {{ $pts4->main_supervisor_recommendation ? '✓ Recommended' : '❌ Not Recommended' }}
                                        </span>
                                    @endif
                                </div>
                                <div class="text-xs text-gray-700 dark:text-gray-300 space-y-1">
                                    <strong>Student Comment:</strong>
                                    <x-feedback-box :text="$pts4->main_supervisor_student_comment" fallback="Comment not provided" role="main_supervisor" />
                                </div>

                                <div class="text-xs text-gray-700 dark:text-gray-300 space-y-1">
                                    <strong>Main Supervisor Remark:</strong>
                                    <x-feedback-box :text="$pts4->main_supervisor_confidential_remark" fallback="Remark not provided" role="main_supervisor" />
                                </div>
                            </x-role-card>
                        @endif

                        <!-- Co-Supervisors Endorsements -->
                        @if($hasCoSigs)
                            <x-role-card role="co_supervisor">
                                <h5 class="text-sm font-bold text-blue-900 dark:text-blue-200">Co-Supervisors</h5>
                                @for($i = 1; $i <= 10; $i++)
                                    @php
                                        $idCol = "co_supervisor_{$i}_id";
                                        $recCol = "co_supervisor_{$i}_recommendation";
                                        $studComCol = "co_supervisor_{$i}_student_comment";
                                        $remCol = "co_supervisor_{$i}_confidential_remark";
                                        $coUser = $coSupervisors[$i] ?? null;
                                    @endphp
                                    @if($coUser && !is_null($pts4->$recCol))
                                        @php
                                            $isExt = $coUser->isExternalSupervisor();
                                            $roleTitle = $student ? $student->getSupervisorRoleTitle($coUser) : ($isExt ? 'External Supervisor' : "Co-Supervisor {$i}");
                                            $inst = ($isExt && $coUser->externalSupervisorProfile?->affiliated_institute) ? ' - ' . $coUser->externalSupervisorProfile->affiliated_institute : '';
                                        @endphp
                                        <div class="text-xs space-y-1.5 pt-1.5 {{ $i > 1 ? 'border-t border-blue-100 dark:border-blue-900' : '' }}">
                                            <div class="flex items-center justify-between text-sm font-bold gap-2 sm:gap-4">
                                                <span class="text-gray-800 dark:text-gray-200">
                                                    {{ $roleTitle }} <span class="block sm:inline text-xs font-normal text-gray-500 dark:text-gray-400 mt-0.5 sm:mt-0">({{ $coUser->name }}{{ $inst }})</span>:
                                                </span>
                                                <span class="px-2.5 py-1 rounded-lg uppercase text-[10px] text-center leading-tight whitespace-normal max-w-[140px] sm:max-w-none {{ $pts4->$recCol ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/60 dark:text-emerald-300' : 'bg-red-100 text-red-800 dark:bg-red-900/60 dark:text-red-300' }}">
                                                    {{ $pts4->$recCol ? '✓ Recommended' : '❌ Not Recommended' }}
                                                </span>
                                            </div>

                                            <div class="space-y-1">
                                                <strong>Student Comment:</strong>
                                                <x-feedback-box :text="$pts4->$studComCol" fallback="Comment not provided" role="co_supervisor" />
                                            </div>

                                            <div class="space-y-1">
                                                <strong>{{ $roleTitle }} Remark:</strong>
                                                <x-feedback-box :text="$pts4->$remCol" fallback="Remark not provided" role="co_supervisor" />
                                            </div>
                                        </div>
                                    @endif
                                @endfor
                            </x-role-card>
                        @endif

                        <!-- Academic Office Endorsement -->
                        @if(!is_null($pts4->academic_office_is_verified))
                            <x-role-card role="academic_office">
                                <div class="flex items-center justify-between font-bold text-violet-900 dark:text-violet-200 gap-2 sm:gap-4">
                                    <h5 class="text-s">Academic Office</h5>
                                    <span class="px-2.5 py-1 font-bold rounded-lg uppercase text-[10px] text-center leading-tight whitespace-normal max-w-[140px] sm:max-w-none {{ $pts4->academic_office_is_verified ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/60 dark:text-emerald-300' : 'bg-red-100 text-red-800 dark:bg-red-900/60 dark:text-red-300' }}">
                                        {{ $pts4->academic_office_is_verified ? '✓ Verified & Forwarded' : '❌ Verification Failed' }}
                                    </span>
                                </div>

                                <div class="text-xs text-gray-700 dark:text-gray-300 space-y-1">
                                    <strong>Verification Remark:</strong>
                                    <x-feedback-box :text="$pts4->academic_office_verification_remark" fallback="Remark not provided" role="academic_office" />
                                </div>
                            </x-role-card>
                        @endif

                        <!-- DR Approval -->
                        @if(!is_null($pts4->dr_approval))
                            <x-role-card role="dr">
                                <div class="flex items-center justify-between text-s font-bold text-fuchsia-900 dark:text-fuchsia-200 gap-2 sm:gap-4">
                                    <span>Dean of Research (DR)</span>
                                    <span class="px-2.5 py-1 font-bold rounded-lg uppercase text-[10px] text-center leading-tight whitespace-normal max-w-[140px] sm:max-w-none {{ $pts4->dr_approval ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/60 dark:text-emerald-300' : 'bg-red-100 text-red-800 dark:bg-red-900/60 dark:text-red-300' }}">
                                        {{ $pts4->dr_approval ? '✓ Approved' : '❌ Not Approved' }}
                                        @if($pts4->approvedBy)
                                            by: {{ $pts4->approvedBy->email }}
                                        @endif
                                    </span>
                                </div>
                                <div class="text-xs text-gray-700 dark:text-gray-300 space-y-1">
                                    <strong>Student Comment:</strong>
                                    <x-feedback-box :text="$pts4->dr_student_comment" fallback="Comment not provided" role="dr" />
                                </div>
                                <div class="text-xs text-gray-700 dark:text-gray-300 space-y-1">
                                    <strong>DR Remark:</strong>
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

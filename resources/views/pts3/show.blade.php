<x-app-layout>
    @php
        $formPrefix = isset($student) && $student->isPhd() ? 'PTS' : 'MSRTS';
    @endphp
    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-4">

            <!-- Top Back to Dashboard Button -->
            <div>
                <x-back-to-dashboard-button />
            </div>

            <!-- Page Header Card -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-5 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div class="space-y-1">
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white leading-tight">
                        @if($pts3->status === 'approved')
                            {{ __("Approved {$formPrefix}-3 Form") }}
                        @elseif($pts3->status === 'rejected')
                            {{ __("Rejected {$formPrefix}-3 Form") }}
                        @elseif($pts3->status === 'reverted')
                            {{ __("Reverted {$formPrefix}-3 Form") }}
                        @else
                            {{ __("{$formPrefix}-3 Form Details") }}
                        @endif
                    </h2>
                </div>
                <div class="flex items-center space-x-3 shrink-0">
                    @if($pts3->status === 'approved')
                        <span class="px-3.5 py-1.5 bg-emerald-100 dark:bg-emerald-900/60 text-emerald-800 dark:text-emerald-300 text-xs font-extrabold rounded-full uppercase tracking-wider flex items-center shadow-xs border border-emerald-200 dark:border-emerald-800">
                            ✓ Approved
                        </span>
                    @elseif($pts3->status === 'rejected')
                        <span class="px-3.5 py-1.5 bg-red-100 dark:bg-red-900/60 text-red-800 dark:text-red-300 text-xs font-extrabold rounded-full uppercase tracking-wider flex items-center shadow-xs border border-red-200 dark:border-red-800">
                            ❌ Rejected
                        </span>
                    @elseif($pts3->status === 'reverted')
                        <span class="px-3.5 py-1.5 bg-amber-100 dark:bg-amber-900/60 text-amber-800 dark:text-amber-300 text-xs font-extrabold rounded-full uppercase tracking-wider flex items-center shadow-xs border border-amber-200 dark:border-amber-800">
                            ⚠️ Reverted
                        </span>
                    @else
                        <span class="px-3.5 py-1.5 bg-blue-100 dark:bg-blue-900/60 text-blue-800 dark:text-blue-300 text-xs font-extrabold rounded-full uppercase tracking-wider flex items-center shadow-xs border border-blue-200 dark:border-blue-800">
                            ⏳ In Progress
                        </span>
                    @endif
                </div>
            </div>

            <!-- Reversion Feedback Banner if Reverted -->
            @if($pts3->status === 'reverted')
                <div class="p-5 bg-amber-50 dark:bg-amber-950/40 border-l-4 border-amber-500 rounded-xl space-y-2 shadow-sm">
                    <div class="flex items-center justify-between gap-2 sm:gap-4">
                        <span class="font-bold text-amber-900 dark:text-amber-200 text-sm">
                            ⚠️ Form Reverted by {{ $pts3->getRevertedByRoleLabel() }}
                        </span>
                        <span class="text-xs font-bold text-amber-800 dark:text-amber-300 bg-amber-100 dark:bg-amber-900/60 px-3 py-1 rounded-full">
                            Reverted to Main Supervisor
                        </span>
                    </div>
                    @if($pts3->reversion_comment)
                        <div class="text-xs text-amber-900 dark:text-amber-200 mt-2">
                            <p class="font-semibold mb-1">Reversion Reason:</p>
                            <x-feedback-box :text="$pts3->reversion_comment" role="reverted" />
                        </div>
                    @endif
                </div>
            @endif

            <!-- Section 1: Read-Only Student Information -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4 border-b border-gray-100 dark:border-gray-700 pb-2">
                    1. Student Information
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <x-readonly-label value="Student Name" />
                        <x-readonly-input :value="$studentUser->name ?? 'N/A'" />
                    </div>

                    <div>
                        <x-readonly-label value="Roll Number" />
                        <x-readonly-input :value="$student->roll_number ?? 'N/A'" />
                    </div>

                    <div>
                        <x-readonly-label value="Department" />
                        <x-readonly-input :value="$student->department->name ?? 'N/A'" />
                    </div>

                    <div>
                        <x-readonly-label value="Date of Registration" />
                        <x-readonly-input :value="$student->date_registration ? \Carbon\Carbon::parse($student->date_registration)->format('d-m-Y') : 'N/A'" />
                    </div>

                    <div>
                        <x-readonly-label value="Date of Joining" />
                        <x-readonly-input :value="$student->date_joining ? \Carbon\Carbon::parse($student->date_joining)->format('d-m-Y') : 'N/A'" />
                    </div>

                    <div>
                        <x-readonly-label value="Date of Confirmation" />
                        <x-readonly-input :value="$student->date_confirmation ? \Carbon\Carbon::parse($student->date_confirmation)->format('d-m-Y') : 'N/A'" />
                    </div>

                    <div>
                        <x-readonly-label value="Open Seminar Date" />
                        <x-readonly-input :value="$thesis->getOpenSeminarDate()?->format('d-m-Y') ?? 'N/A'" />
                    </div>

                    <div>
                        <x-readonly-label value="Primary Email Address" />
                        <x-readonly-input :value="$studentUser->email ?? 'N/A'" />
                    </div>

                    <div>
                        <x-readonly-label value="Recent Contact No." />
                        <x-readonly-input :value="$student->phone_number ? (($student->phone_country_code ?: '+91') . ' ' . $student->phone_number) : 'N/A'" />
                    </div>

                    <div class="md:col-span-3">
                        <x-readonly-label value="Main Supervisor" />
                        <x-readonly-input :value="$student->mainSupervisors->pluck('name')->join(', ') ?: ($student->supervisors->first()?->name ?? 'Not Assigned')" />
                    </div>

                    <div class="md:col-span-3">
                        <x-readonly-label value="Co-Supervisor(s)" />
                        <x-readonly-input :value="$student->coSupervisors->pluck('name')->join(', ') ?: 'None'" />
                    </div>

                    <div class="md:col-span-3">
                        <x-readonly-label value="External Supervisor(s)" />
                        @php
                            $extSupText = ($student && $student->externalSupervisors->isNotEmpty())
                                ? $student->externalSupervisors->map(fn($s) => $s->name . ($s->externalSupervisorProfile?->affiliated_institute ? ' (' . $s->externalSupervisorProfile->affiliated_institute . ')' : ''))->join(', ')
                                : 'None';
                        @endphp
                        <x-readonly-input :value="$extSupText" />
                    </div>

                    <div>
                        <x-readonly-label value="Date of Submission" />
                        <x-readonly-input :value="$pts3->created_at ? $pts3->created_at->format('d-m-Y') : 'N/A'" />
                    </div>
                </div>
            </div>

            <!-- Section 2: Name of Thesis (Read-Only) -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 space-y-2">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white border-b border-gray-100 dark:border-gray-700 pb-2">
                    2. Name of Thesis
                </h3>
                <div>
                    <x-readonly-label value="Thesis Title" />
                    <x-readonly-input :value="$pts3->thesis_title ?? $thesis->title ?? 'N/A'" />
                </div>
            </div>

            <!-- Section 3: Indian Examiners Panel (Ordered by viewer role) -->
            <x-pts3-examiners-review-table 
                :examiners="$indianExaminers"
                title="3. Proposed Indian Examiners"
                type="indian"
                :pts3="$pts3"
                :userRank="$userRank"
                :canEvaluate="false"
                :collapsible="false"
            />

            <!-- Section 4: International Examiners Panel (Ordered by viewer role) -->
            <x-pts3-examiners-review-table 
                :examiners="$internationalExaminers"
                title="4. Proposed International Examiners"
                type="international"
                :pts3="$pts3"
                :userRank="$userRank"
                :canEvaluate="false"
                :collapsible="false"
            />

            <!-- Section 5: Proposed Oral Examination Board (OEB) Members (Ordered by viewer role) -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-sm border border-gray-100 dark:border-gray-700 space-y-4">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-3 border-b border-gray-100 dark:border-gray-700">
                    <div>
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white flex items-center gap-2">
                            <span>5. Proposed Oral Examination Board (OEB) Members</span>
                        </h3>
                    </div>
                    <span class="text-xs px-2.5 py-1 bg-amber-50 dark:bg-amber-950/70 text-amber-700 dark:text-amber-300 rounded-full font-semibold border border-amber-200 dark:border-amber-800">
                        Count: {{ $oebMembers->count() }} Member(s)
                    </span>
                </div>

                    <div class="overflow-x-auto rounded-xl border border-gray-200 dark:border-gray-700 shadow-2xs">
                        <table class="w-full min-w-[750px] text-left text-sm text-gray-600 dark:text-gray-300 divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700/50 text-xs uppercase text-gray-500 dark:text-gray-400">
                                <tr>
                                    @if($userRank >= 5)
                                        <th class="p-3 w-12 text-center whitespace-nowrap">#</th>
                                    @endif
                                    <th class="p-3 min-w-[160px] max-w-[240px]">Name</th>
                                    <th class="p-3 min-w-[140px] max-w-[200px]">Designation</th>
                                    <th class="p-3 min-w-[150px] max-w-[220px]">Department</th>
                                    <th class="p-3 min-w-[160px]">Email</th>
                                    <th class="p-3 min-w-[130px] whitespace-nowrap">Phone No.</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-gray-700 bg-white dark:bg-gray-800">
                                @forelse($oebMembers as $index => $oeb)
                                    <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-700/50 transition-colors align-top">
                                        @if($userRank >= 5)
                                            <td class="p-3 text-center font-bold text-gray-900 dark:text-white whitespace-nowrap">{{ $index + 1 }}</td>
                                        @endif
                                        <td class="p-3 font-bold text-gray-900 dark:text-white min-w-[160px] max-w-[240px] break-words">{{ $oeb->name }}</td>
                                        <td class="p-3 text-xs text-gray-700 dark:text-gray-300 min-w-[140px] max-w-[200px] break-words">{{ $oeb->designation }}</td>
                                        <td class="p-3 text-xs font-medium text-gray-800 dark:text-gray-200 min-w-[150px] max-w-[220px] break-words">{{ $oeb->department }}</td>
                                        <td class="p-3 text-xs min-w-[160px] break-all">
                                            <a href="mailto:{{ $oeb->email }}" class="text-indigo-600 dark:text-indigo-400 hover:underline font-medium">{{ $oeb->email }}</a>
                                        </td>
                                        <td class="p-3 text-xs text-gray-700 dark:text-gray-300 whitespace-nowrap min-w-[130px]">{{ $oeb->getFormattedPhoneNumber() ?: 'N/A' }}</td>
                                    </tr>

                                    <!-- Side-by-Side Remarks Sub-Row for Evaluating Authorities (userRank >= 5) -->
                                    @if($userRank >= 5 && ($pts3->academic_office_submitted_at || ($userRank >= 6 && $pts3->doaa_submitted_at)))
                                        <tr class="bg-gray-50/40 dark:bg-gray-900/30 border-t border-gray-100 dark:border-gray-800">
                                            <td colspan="{{ $userRank >= 5 ? 6 : 5 }}" class="p-3 px-6">
                                                <div class="grid grid-cols-1 {{ ($userRank >= 6 && ($pts3->doaa_submitted_at || in_array($pts3->status, ['approved', 'rejected']))) ? 'md:grid-cols-2' : '' }} gap-4">
                                                    <!-- Academic Office Comment Card (Left) -->
                                                    <x-role-card role="academic_office" class="p-3 !space-y-1.5">
                                                        <span class="block text-xs font-bold text-violet-900 dark:text-violet-200">
                                                            Academic Office Comment
                                                        </span>
                                                        <x-feedback-box :text="$oeb->academic_office_remark" role="academic_office" fallback="No comment provided" />
                                                    </x-role-card>
                                          
                                                    @if($userRank >= 6 && ($pts3->doaa_submitted_at || in_array($pts3->status, ['approved', 'rejected'])))
                                                    <!-- DOAA Comment Card (Right) -->
                                                    <x-role-card role="doaa" class="p-3 !space-y-1.5">
                                                        <span class="block text-xs font-bold text-purple-900 dark:text-purple-200">
                                                            DOAA Comment
                                                        </span>
                                                        <x-feedback-box :text="$oeb->doaa_remark" role="doaa" fallback="No comment provided" />
                                                    </x-role-card>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @endif
                                @empty
                                    <tr>
                                        <td colspan="{{ $userRank >= 5 ? 6 : 5 }}" class="p-4 text-center text-sm text-gray-500">No OEB members proposed.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
            </div>

            <!-- Section 6: Stage Evaluation Trail -->
            @if($userRank >= 1)
                <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-sm border border-gray-100 dark:border-gray-700 space-y-6">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white pb-2 border-b border-gray-100 dark:border-gray-700">
                        6. Evaluation &amp; Recommendations Trail
                    </h3>

                    <div class="space-y-4">
                        <!-- 1. Main Supervisor -->
                        @if($pts3->main_supervisor_submitted_at)
                            <x-role-card role="main_supervisor">
                                <div class="flex items-center justify-between text-base gap-2 sm:gap-4">
                                    <span class="font-bold text-indigo-900 dark:text-indigo-200 text-base">
                                        Main Supervisor @if($pts3->mainSupervisor)<span class="block sm:inline text-xs font-normal text-gray-500 dark:text-gray-400 mt-0.5 sm:mt-0">({{ $pts3->mainSupervisor->name }})</span>@endif
                                    </span>
                                    <span class="font-bold text-xs whitespace-nowrap shrink-0 text-emerald-600 dark:text-emerald-400">
                                        ✓ Endorsed
                                    </span>
                                </div>
                            </x-role-card>
                        @endif

                        <!-- 2. Co-Supervisors -->
                        @if($userRank >= 2 && !empty($coSupervisors))
                            @php
                                $visibleCoSupervisors = ($userRank === 2 && !auth('admin')->check()) 
                                    ? collect($coSupervisors)->filter(fn($u) => $u->id === auth()->id())->all()
                                    : $coSupervisors;
                                $endorsedCoSupervisors = collect($visibleCoSupervisors)->filter(function($coUser, $i) use ($pts3) {
                                    return $pts3->{"co_supervisor_{$i}_recommendation"} !== null;
                                })->all();
                            @endphp
                            @if(!empty($endorsedCoSupervisors))
                                <x-role-card role="co_supervisor">
                                    <h5 class="text-base font-bold text-blue-900 dark:text-blue-200">Co-Supervisors</h5>
                                    @foreach($visibleCoSupervisors as $i => $coUser)
                                        @php
                                            $isExt = $coUser->isExternalSupervisor();
                                            $roleTitle = $student ? $student->getSupervisorRoleTitle($coUser) : ($isExt ? 'External Supervisor' : "Co-Supervisor {$i}");
                                            $inst = ($isExt && $coUser->externalSupervisorProfile?->affiliated_institute) ? ' - ' . $coUser->externalSupervisorProfile->affiliated_institute : '';
                                            $isEndorsed = $pts3->{"co_supervisor_{$i}_recommendation"} !== null;
                                        @endphp
                                        @if($isEndorsed)
                                            <div class="text-xs space-y-1.5 pt-1.5 {{ !$loop->first ? 'border-t border-blue-100 dark:border-blue-900' : '' }}">
                                                <div class="flex items-center justify-between font-semibold text-sm gap-2 sm:gap-4">
                                                    <span>{{ $roleTitle }} <span class="block sm:inline text-xs font-normal text-gray-500 dark:text-gray-400 mt-0.5 sm:mt-0">({{ $coUser->name }}{{ $inst }})</span>:</span>
                                                    <span class="font-bold text-xs whitespace-nowrap shrink-0 text-emerald-600 dark:text-emerald-400">
                                                        ✓ Endorsed
                                                    </span>
                                                </div>
                                            </div>
                                        @endif
                                    @endforeach
                                </x-role-card>
                            @endif
                        @endif

                        <!-- 3. DPGC -->
                        @if($userRank >= 3 && $pts3->dpgc_submitted_at && $pts3->dpgc_recommendation !== null)
                            <x-role-card role="dpgc" title="Department Postgraduate Committee (DPGC)">
                                <x-slot:badge>
                                    <span class="font-bold text-xs whitespace-nowrap shrink-0 text-emerald-600 dark:text-emerald-400">
                                        ✓ Endorsed
                                    </span>
                                </x-slot:badge>
                            </x-role-card>
                        @endif

                        <!-- 4. HOD -->
                        @if($userRank >= 4 && $pts3->hod_submitted_at && $pts3->hod_recommendation !== null)
                            <x-role-card role="hod" title="Head of Department (HOD)">
                                <x-slot:badge>
                                    <span class="font-bold text-xs whitespace-nowrap shrink-0 text-emerald-600 dark:text-emerald-400">
                                        ✓ Endorsed
                                    </span>
                                </x-slot:badge>
                            </x-role-card>
                        @endif

                        <!-- 5. Academic Office -->
                        @if($userRank >= 5 && $pts3->academic_office_submitted_at && $pts3->academic_office_is_verified !== null)
                            <x-role-card role="academic_office" title="Academic Office">
                                <x-slot:badge>
                                    <span class="font-bold text-xs whitespace-nowrap shrink-0 text-emerald-600 dark:text-emerald-400">
                                        ✓ Verified
                                    </span>
                                </x-slot:badge>
                                <div class="text-xs text-gray-700 dark:text-gray-300 space-y-1">
                                    <strong>Verification Remark:</strong>
                                    <x-feedback-box :text="$pts3->academic_office_verification_remark" fallback="Remark not provided" role="academic_office" />
                                </div>
                            </x-role-card>
                        @endif

                        <!-- 6. DOAA -->
                        @if($userRank >= 6 && $pts3->doaa_submitted_at && $pts3->doaa_is_verified !== null)
                            <x-role-card role="doaa" title="Dean of Academic Affairs (DOAA)">
                                <x-slot:badge>
                                    <span class="font-bold text-xs whitespace-nowrap shrink-0 text-emerald-600 dark:text-emerald-400">
                                        ✓ Verified
                                    </span>
                                </x-slot:badge>
                                <div class="text-xs text-gray-700 dark:text-gray-300 space-y-1">
                                    <strong>Verification Remark:</strong>
                                    <x-feedback-box :text="$pts3->doaa_verification_remark" fallback="Remark not provided" role="doaa" />
                                </div>
                            </x-role-card>
                        @endif

                        <!-- 7. Senate Chairperson Approval -->
                        @if($pts3->senate_chairperson_submitted_at && $pts3->senate_chairperson_approval !== null)
                            <x-role-card role="senate_chairperson" title="Senate Chairperson">
                                <x-slot:badge>
                                    @if($pts3->senate_chairperson_approval === true || $pts3->status === 'approved')
                                        <span class="font-bold text-xs whitespace-nowrap shrink-0 text-emerald-600 dark:text-emerald-400">
                                            ✓ Approved
                                        </span>
                                    @else
                                        <span class="font-bold text-xs whitespace-nowrap shrink-0 text-red-600 dark:text-red-400">
                                            ❌ Rejected
                                        </span>
                                    @endif
                                </x-slot:badge>
                                <div class="text-xs text-gray-700 dark:text-gray-300 space-y-1">
                                    <strong>{{ ($pts3->senate_chairperson_approval === true || $pts3->status === 'approved') ? 'Approval Remark:' : 'Non-Approval Remark:' }}</strong>
                                    <x-feedback-box :text="$pts3->senate_chairperson_approval_remark" fallback="Remark not provided" role="senate_chairperson" />
                                </div>
                                @if($pts3->senate_chairperson_confidential_remark && (auth()->user()?->isDoaa() || auth()->user()?->role === 'senate_chairperson' || auth('admin')->check()))
                                    <div class="text-xs text-gray-700 dark:text-gray-300 space-y-1">
                                        <strong>Confidential Remark (DOAA &amp; Senate Chairperson Only):</strong>
                                        <x-feedback-box :text="$pts3->senate_chairperson_confidential_remark" fallback="Remark not provided" role="senate_chairperson" />
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

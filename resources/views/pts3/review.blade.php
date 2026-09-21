<x-app-layout>
    @php
        $formPrefix = isset($student) && $student->isPhd() ? 'PTS' : 'MSRTS';
        $canEvaluate = true;
    @endphp

    <div class="py-6" x-data="pts3ReviewForm()">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Top Back to Dashboard Button -->
            <div>
                <x-back-to-dashboard-button />
            </div>

            <!-- Page Header Card -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-5 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div class="space-y-1">
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white leading-tight">
                        {{ __("Review & Endorse {$formPrefix}-3 Form (Panels of Examiners)") }}
                    </h2>
                </div>
            </div>

            <!-- Main Form Wrapper for Endorsement -->
            <form action="{{ route('pts3.endorse', $pts3) }}" method="POST" id="pts3EndorseForm" class="space-y-6" @submit="clearDraft()">
                @csrf

                <!-- Section 1: Pre-filled Student Information -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 space-y-4" x-data="{ showStudentInfo: false }">
                    <div class="flex items-center justify-between border-b border-gray-100 dark:border-gray-700 pb-3 cursor-pointer select-none" @click="showStudentInfo = !showStudentInfo">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white flex items-center gap-2">
                            <span>1. Student Information</span>
                        </h3>
                        <button type="button" class="p-1.5 rounded-lg text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 transition" @click.stop="showStudentInfo = !showStudentInfo">
                            <svg class="w-5 h-5 transform transition-transform duration-200" :class="{ 'rotate-180': showStudentInfo }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                    </div>

                    <!-- Always Visible First Row -->
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
                    </div>

                    <!-- Collapsible Remaining Details -->
                    <div x-show="showStudentInfo" x-transition class="grid grid-cols-1 md:grid-cols-3 gap-6 pt-4 border-t border-gray-100 dark:border-gray-700">

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

                        <div class="md:col-span-1">
                            <x-readonly-label value="Main Supervisor" />
                            <x-readonly-input :value="$student->mainSupervisors->pluck('name')->join(', ') ?: ($student->supervisors->first()?->name ?? 'Not Assigned')" />
                        </div>

                        <div class="md:col-span-2">
                            <x-readonly-label value="Co-Supervisor(s)" />
                            <x-readonly-input :value="$student->coSupervisors->pluck('name')->join(', ') ?: 'None'" />
                        </div>

                        <div class="md:col-span-2">
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
                        2. Thesis Title
                    </h3>
                    <div>
                        <x-readonly-input :value="$pts3->thesis_title ?? $thesis->title ?? 'N/A'" />
                    </div>
                </div>

                <!-- Section 3: Indian Examiners Panel -->
                <x-pts3-examiners-review-table 
                    :examiners="$indianExaminers"
                    title="3. Proposed Indian Examiners"
                    type="indian"
                    :pts3="$pts3"
                    :userRank="$userRank"
                    :canEvaluate="true"
                    :collapsible="true"
                    :currentStage="$pts3->current_stage"
                />

                <!-- Section 4: International Examiners Panel -->
                <x-pts3-examiners-review-table 
                    :examiners="$internationalExaminers"
                    title="4. Proposed International Examiners"
                    type="international"
                    :pts3="$pts3"
                    :userRank="$userRank"
                    :canEvaluate="true"
                    :collapsible="true"
                    :currentStage="$pts3->current_stage"
                />

                @php
                    $isAcademicOfficeActive = $pts3->current_stage === 'academic_office';
                    $isDoaaActive = $pts3->current_stage === 'doaa';
                    $isSenateActive = $pts3->current_stage === 'senate_chairperson';
                @endphp

                <!-- Section 5: Proposed Oral Examination Board (OEB) Chairpersons (2-Column Grid 1 -> 2, 3 -> 4) -->
                <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-sm border border-gray-100 dark:border-gray-700 space-y-3">
                    <div class="pb-2 border-b border-gray-100 dark:border-gray-700">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white flex items-center gap-2">
                            <span>5. Proposed Oral Examination Board (OEB) Chairpersons</span>
                        </h3>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                        @forelse($oebMembers as $index => $oeb)
                            <div class="p-3 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800/90 hover:border-gray-300 dark:hover:border-gray-600 transition shadow-2xs space-y-1 flex flex-col justify-between">
                                <div>
                                    <!-- Card Top Header (Order + Member Label) -->
                                    <div class="flex items-center justify-between gap-2 pb-1 border-b border-gray-100 dark:border-gray-700">
                                        <div class="flex items-center gap-2">
                                            <!-- Unified Order Box (Visible only to DOAA and Senate Chairperson) -->
                                            @if($isDoaaActive)
                                                <div class="flex items-center gap-1.5" title="DOAA Priority Order (0 to 4)">
                                                    <span class="text-xs font-bold text-purple-800 dark:text-purple-200">Order:</span>
                                                    <select name="doaa_oeb_priority[{{ $oeb->id }}]" 
                                                            class="w-20 h-8 py-0.5 pl-3.5 pr-8 text-sm font-extrabold rounded-lg border-2 border-purple-400 dark:border-purple-500 bg-white dark:bg-gray-900 text-purple-950 dark:text-purple-100 focus:ring-2 focus:ring-purple-500 focus:border-purple-500 shadow-xs cursor-pointer">
                                                        @php
                                                            $selDoaaOebPriority = (string)old("doaa_oeb_priority.{$oeb->id}", $oeb->doaa_priority !== null ? $oeb->doaa_priority : min($index + 1, 4));
                                                        @endphp
                                                        @foreach([0, 1, 2, 3, 4] as $val)
                                                            <option value="{{ $val }}" class="bg-white dark:bg-gray-900 text-gray-900 dark:text-white font-bold" {{ $selDoaaOebPriority === (string)$val ? 'selected' : '' }}>
                                                                {{ $val }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            @elseif($isSenateActive)
                                                <div class="flex items-center gap-1.5" title="Senate Chairperson Priority Order (0 to 4)">
                                                    <span class="text-xs font-bold text-indigo-800 dark:text-indigo-200">Order:</span>
                                                    <select name="senate_oeb_priority[{{ $oeb->id }}]" 
                                                            class="w-20 h-8 py-0.5 pl-3.5 pr-8 text-sm font-extrabold rounded-lg border-2 border-indigo-400 dark:border-indigo-500 bg-white dark:bg-gray-900 text-indigo-950 dark:text-indigo-100 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 shadow-xs cursor-pointer">
                                                        @php
                                                            $selSpOebPriority = (string)old("senate_oeb_priority.{$oeb->id}", $oeb->senate_chairperson_priority !== null ? $oeb->senate_chairperson_priority : ($oeb->doaa_priority !== null ? $oeb->doaa_priority : min($index + 1, 4)));
                                                        @endphp
                                                        @foreach([0, 1, 2, 3, 4] as $val)
                                                            <option value="{{ $val }}" class="bg-white dark:bg-gray-900 text-gray-900 dark:text-white font-bold" {{ $selSpOebPriority === (string)$val ? 'selected' : '' }}>
                                                                {{ $val }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            @elseif($userRank >= 6)
                                                @php
                                                    $oebPriorityNum = ($userRank === 7) 
                                                        ? ($oeb->senate_chairperson_priority !== null ? $oeb->senate_chairperson_priority : ($oeb->doaa_priority !== null ? $oeb->doaa_priority : min($index + 1, 4))) 
                                                        : ($oeb->doaa_priority !== null ? $oeb->doaa_priority : min($index + 1, 4));
                                                @endphp
                                                <span class="inline-flex items-center justify-center w-7 h-7 rounded-full bg-blue-100 dark:bg-blue-950/80 text-blue-900 dark:text-blue-200 border border-blue-200 dark:border-blue-700 text-xs font-black shrink-0 shadow-2xs">
                                                    #{{ $oebPriorityNum }}
                                                </span>
                                            @else
                                                {{-- For MS, Co-Supervisors, DPGC, HOD, Academic Office: Do not show sequence numbers --}}
                                            @endif
                                        </div>

                                        <span class="text-xs font-bold text-gray-500 dark:text-gray-400">
                                            OEB Chairperson
                                        </span>
                                    </div>

                                    <!-- Discrete Fields (All Shown in Card: Inline Label: Value) -->
                                    <div class="space-y-1 pt-2 text-xs">
                                        <!-- Name -->
                                        <div class="flex items-baseline gap-1.5 flex-wrap">
                                            <span class="font-semibold text-gray-500 dark:text-gray-400 shrink-0">Faculty Name:</span>
                                            <span class="font-bold text-xs text-gray-900 dark:text-white">{{ $oeb->name }}</span>
                                        </div>

                                        <!-- Designation -->
                                        <div class="flex items-baseline gap-1.5 flex-wrap">
                                            <span class="font-semibold text-gray-500 dark:text-gray-400 shrink-0">Designation:</span>
                                            <span class="text-gray-800 dark:text-gray-200 font-medium">{{ $oeb->designation ?: 'N/A' }}</span>
                                        </div>

                                        <!-- Department -->
                                        <div class="flex items-baseline gap-1.5 flex-wrap">
                                            <span class="font-semibold text-gray-500 dark:text-gray-400 shrink-0">Department:</span>
                                            <span class="text-gray-800 dark:text-gray-200 font-medium">{{ $oeb->department ?: 'N/A' }}</span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Evaluation Remarks Section -->
                                @if($isAcademicOfficeActive)
                                    <!-- Academic Office Comment Input -->
                                    <div class="pt-2 border-t border-gray-100 dark:border-gray-700"
                                         x-data="{ oebRemark: @js(old("oeb_remarks.{$oeb->id}", $oeb->academic_office_remark ?? '')) }">
                                        <div class="flex items-center justify-between gap-2 mb-1.5">
                                            <label class="block text-xs font-bold text-violet-900 dark:text-violet-200">
                                                Academic Office Comment
                                            </label>
                                            <x-snippet-dropdown target="oebRemark" form-type="pts3" role="academic_office" />
                                        </div>
                                        <textarea name="oeb_remarks[{{ $oeb->id }}]" 
                                                  x-model="oebRemark"
                                                  rows="2" 
                                                  class="w-full text-xs rounded-lg border-violet-200 dark:border-violet-800/70 bg-white dark:bg-gray-900/80 text-gray-800 dark:text-gray-100 focus:border-violet-500 focus:ring-violet-500 placeholder-gray-400 dark:placeholder-gray-500" 
                                                  placeholder="Enter verification comment for this OEB chairperson..."></textarea>
                                    </div>
                                @elseif($isDoaaActive)
                                    <!-- DOAA Active: View Academic Office Comment + DOAA Comment Input -->
                                    <div class="pt-2 border-t border-gray-100 dark:border-gray-700 space-y-2">
                                        <div class="space-y-1">
                                            <span class="block text-[11px] font-bold text-violet-900 dark:text-violet-200 uppercase tracking-wide">
                                                Academic Office Comment
                                            </span>
                                            <x-feedback-box :text="$oeb->academic_office_remark" role="academic_office" fallback="No comment provided" />
                                        </div>
                                        <div x-data="{ doaaOebRemark: @js(old("doaa_oeb_remarks.{$oeb->id}", $oeb->doaa_remark ?? '')) }" class="space-y-1">
                                            <div class="flex items-center justify-between gap-2 mb-1.5">
                                                <label class="block text-xs font-bold text-purple-900 dark:text-purple-200">
                                                    DOAA Comment
                                                </label>
                                                <x-snippet-dropdown target="doaaOebRemark" form-type="pts3" role="doaa" />
                                            </div>
                                            <textarea name="doaa_oeb_remarks[{{ $oeb->id }}]" 
                                                      x-model="doaaOebRemark"
                                                      rows="2" 
                                                      class="w-full text-xs rounded-lg border-purple-200 dark:border-purple-800/70 bg-white dark:bg-gray-900/80 text-gray-800 dark:text-gray-100 focus:border-purple-500 focus:ring-purple-500 placeholder-gray-400 dark:placeholder-gray-500" 
                                                      placeholder="Enter DOAA observation/remark..."></textarea>
                                        </div>
                                    </div>
                                @elseif($isSenateActive)
                                    <!-- Senate Chairperson Active: View DOAA Comment ONLY (Academic Office comment hidden) -->
                                    <div class="pt-2 border-t border-gray-100 dark:border-gray-700 space-y-1">
                                        <span class="block text-[11px] font-bold text-purple-900 dark:text-purple-200 uppercase tracking-wide">
                                            DOAA Comment
                                        </span>
                                        <x-feedback-box :text="$oeb->doaa_remark" role="doaa" fallback="No comment provided" />
                                    </div>
                                @elseif(!$canEvaluate && $userRank >= 5)
                                    <!-- View Mode Remarks (Historical / Show view) -->
                                    @php
                                        $showAcademic = ($userRank < 7) && (bool)$pts3->academic_office_submitted_at;
                                        $showDoaa = ($userRank >= 6) && (bool)$pts3->doaa_submitted_at;
                                    @endphp
                                    @if($showAcademic || $showDoaa)
                                        <div class="pt-2 border-t border-gray-100 dark:border-gray-700 space-y-2">
                                            @if($showAcademic)
                                                <div class="space-y-1">
                                                    <span class="block text-[10px] font-bold text-violet-900 dark:text-violet-200 uppercase tracking-wide">
                                                        Academic Office Comment
                                                    </span>
                                                    <x-feedback-box :text="$oeb->academic_office_remark" role="academic_office" fallback="No comment provided" />
                                                </div>
                                            @endif
                                            @if($showDoaa)
                                                <div class="space-y-1">
                                                    <span class="block text-[10px] font-bold text-purple-900 dark:text-purple-200 uppercase tracking-wide">
                                                        DOAA Comment
                                                    </span>
                                                    <x-feedback-box :text="$oeb->doaa_remark" role="doaa" fallback="No comment provided" />
                                                </div>
                                            @endif
                                        </div>
                                    @endif
                                @endif
                            </div>
                        @empty
                            <div class="col-span-full p-6 text-center text-sm text-gray-500 bg-gray-50 dark:bg-gray-800/40 rounded-xl border border-dashed border-gray-200 dark:border-gray-700">
                                No OEB members proposed.
                            </div>
                        @endforelse
                    </div>
                </div>

                <!-- Section 6: Prior Authority Recommendations Trail -->
                @if($userRank >= 3)
                    @php
                        $currStage = $pts3->current_stage;
                        $isDept = in_array($currStage, ['dpgc', 'hod']);
                        $isGlobal = in_array($currStage, ['academic_office', 'doaa', 'senate_chairperson']);
                        $isDeptOrGlobal = $isDept || $isGlobal;
                        $defaultOpen = $isDept;

                        $hasCoSupervisors = ($pts3->co_supervisors_submitted_at && !empty($coSupervisors));

                        $immediateStage = match($currStage) {
                            'dpgc' => ($hasCoSupervisors ? 'co_supervisors' : 'main_supervisor'),
                            'hod' => 'dpgc',
                            'academic_office' => 'hod',
                            'doaa' => 'academic_office',
                            'senate_chairperson' => 'doaa',
                            default => null,
                        };

                        $hasEarlierStages = false;
                        if ($isDeptOrGlobal && $immediateStage) {
                            if ($immediateStage === 'co_supervisors') {
                                $hasEarlierStages = (bool)$pts3->main_supervisor_submitted_at;
                            } elseif ($immediateStage === 'dpgc') {
                                $hasEarlierStages = (bool)$pts3->main_supervisor_submitted_at || $hasCoSupervisors;
                            } elseif ($immediateStage === 'hod') {
                                $hasEarlierStages = (bool)$pts3->main_supervisor_submitted_at || $hasCoSupervisors || (bool)$pts3->dpgc_submitted_at;
                            } elseif ($immediateStage === 'academic_office') {
                                $hasEarlierStages = (bool)$pts3->main_supervisor_submitted_at || $hasCoSupervisors || (bool)$pts3->dpgc_submitted_at || (bool)$pts3->hod_submitted_at;
                            } elseif ($immediateStage === 'doaa') {
                                $hasEarlierStages = (bool)$pts3->main_supervisor_submitted_at || $hasCoSupervisors || (bool)$pts3->dpgc_submitted_at || (bool)$pts3->hod_submitted_at || (bool)$pts3->academic_office_submitted_at;
                            }
                        }
                    @endphp

                    <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-sm border border-gray-100 dark:border-gray-700 space-y-6">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white pb-2 border-b border-gray-100 dark:border-gray-700">
                            6. Prior Evaluation &amp; Recommendations Trail
                        </h3>

                        <div class="space-y-4">
                            @if($isDeptOrGlobal && $immediateStage)
                                <!-- Earlier Authority Recommendations (Collapsed by default for Global, Open for Dept) -->
                                @if($hasEarlierStages)
                                    <x-collapsible-earlier-remarks :default-open="$defaultOpen">
                                        <!-- 1. Main Supervisor -->
                                        @if($pts3->main_supervisor_submitted_at && $immediateStage !== 'main_supervisor')
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
                                        @if($hasCoSupervisors && $immediateStage !== 'co_supervisors')
                                            <x-role-card role="co_supervisor">
                                                <h5 class="text-base font-bold text-blue-900 dark:text-blue-200">Co-Supervisors</h5>
                                                @foreach($coSupervisors as $i => $coUser)
                                                    @php
                                                        $isExt = $coUser->isExternalSupervisor();
                                                        $roleTitle = $student ? $student->getSupervisorRoleTitle($coUser) : ($isExt ? 'External Supervisor' : "Co-Supervisor {$i}");
                                                        $inst = ($isExt && $coUser->externalSupervisorProfile?->affiliated_institute) ? ' - ' . $coUser->externalSupervisorProfile->affiliated_institute : '';
                                                    @endphp
                                                    <div class="text-xs space-y-1.5 pt-1.5 {{ !$loop->first ? 'border-t border-blue-100 dark:border-blue-900' : '' }}">
                                                        <div class="flex items-center justify-between font-semibold text-sm gap-2 sm:gap-4">
                                                            <span>{{ $roleTitle }} <span class="block sm:inline text-xs font-normal text-gray-500 dark:text-gray-400 mt-0.5 sm:mt-0">({{ $coUser->name }}{{ $inst }})</span>:</span>
                                                            <span class="font-bold text-xs whitespace-nowrap shrink-0 text-emerald-600 dark:text-emerald-400">
                                                                ✓ Endorsed
                                                            </span>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </x-role-card>
                                        @endif

                                        <!-- 3. DPGC -->
                                        @if($pts3->dpgc_submitted_at && $immediateStage !== 'dpgc')
                                            <x-role-card role="dpgc" title="Department Postgraduate Committee (DPGC)">
                                                <x-slot:badge>
                                                    <span class="font-bold text-xs whitespace-nowrap shrink-0 text-emerald-600 dark:text-emerald-400">
                                                        ✓ Endorsed
                                                    </span>
                                                </x-slot:badge>
                                            </x-role-card>
                                        @endif

                                        <!-- 4. HOD -->
                                        @if($pts3->hod_submitted_at && $immediateStage !== 'hod')
                                            <x-role-card role="hod" title="Head of Department (HOD)">
                                                <x-slot:badge>
                                                    <span class="font-bold text-xs whitespace-nowrap shrink-0 text-emerald-600 dark:text-emerald-400">
                                                        ✓ Endorsed
                                                    </span>
                                                </x-slot:badge>
                                            </x-role-card>
                                        @endif

                                        <!-- 5. Academic Office -->
                                        @if($pts3->academic_office_submitted_at && $immediateStage !== 'academic_office')
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
                                        @if($pts3->doaa_submitted_at && $immediateStage !== 'doaa')
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
                                    </x-collapsible-earlier-remarks>
                                @endif

                                <!-- Immediate Previous Stage (Always Open / Visible directly) -->
                                @if($immediateStage === 'main_supervisor' && $pts3->main_supervisor_submitted_at)
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
                                @elseif($immediateStage === 'co_supervisors' && $hasCoSupervisors)
                                    <x-role-card role="co_supervisor">
                                        <h5 class="text-base font-bold text-blue-900 dark:text-blue-200">Co-Supervisors</h5>
                                        @foreach($coSupervisors as $i => $coUser)
                                            @php
                                                $isExt = $coUser->isExternalSupervisor();
                                                $roleTitle = $student ? $student->getSupervisorRoleTitle($coUser) : ($isExt ? 'External Supervisor' : "Co-Supervisor {$i}");
                                                $inst = ($isExt && $coUser->externalSupervisorProfile?->affiliated_institute) ? ' - ' . $coUser->externalSupervisorProfile->affiliated_institute : '';
                                            @endphp
                                            <div class="text-xs space-y-1.5 pt-1.5 {{ !$loop->first ? 'border-t border-blue-100 dark:border-blue-900' : '' }}">
                                                <div class="flex items-center justify-between font-semibold text-sm gap-2 sm:gap-4">
                                                    <span>{{ $roleTitle }} <span class="block sm:inline text-xs font-normal text-gray-500 dark:text-gray-400 mt-0.5 sm:mt-0">({{ $coUser->name }}{{ $inst }})</span>:</span>
                                                    <span class="font-bold text-xs whitespace-nowrap shrink-0 text-emerald-600 dark:text-emerald-400">
                                                        ✓ Endorsed
                                                    </span>
                                                </div>
                                            </div>
                                        @endforeach
                                    </x-role-card>
                                @elseif($immediateStage === 'dpgc' && $pts3->dpgc_submitted_at)
                                    <x-role-card role="dpgc" title="Department Postgraduate Committee (DPGC)">
                                        <x-slot:badge>
                                            <span class="font-bold text-xs whitespace-nowrap shrink-0 text-emerald-600 dark:text-emerald-400">
                                                ✓ Endorsed
                                            </span>
                                        </x-slot:badge>
                                    </x-role-card>
                                @elseif($immediateStage === 'hod' && $pts3->hod_submitted_at)
                                    <x-role-card role="hod" title="Head of Department (HOD)">
                                        <x-slot:badge>
                                            <span class="font-bold text-xs whitespace-nowrap shrink-0 text-emerald-600 dark:text-emerald-400">
                                                ✓ Endorsed
                                            </span>
                                        </x-slot:badge>
                                    </x-role-card>
                                @elseif($immediateStage === 'academic_office' && $pts3->academic_office_submitted_at)
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
                                        @if(Auth::user()?->isAcademicOffice())
                                            <div class="text-xs text-gray-700 dark:text-gray-300 space-y-1 pt-1">
                                                <strong>Assigned Acting DOAA:</strong>
                                                @if(!empty($pts3->acting_doaa_email))
                                                    @php
                                                        $actingDoaaUser = \App\Models\User::where('email', $pts3->acting_doaa_email)->first();
                                                    @endphp
                                                    <div class="p-2 bg-indigo-50/70 dark:bg-indigo-950/40 border border-indigo-200 dark:border-indigo-800 rounded-lg font-semibold text-indigo-900 dark:text-indigo-200">
                                                        {{ $actingDoaaUser->name ?? $pts3->acting_doaa_email }} ({{ $pts3->acting_doaa_email }})
                                                    </div>
                                                @else
                                                    <div class="p-2 bg-gray-100 dark:bg-gray-700/60 border border-gray-200 dark:border-gray-600 rounded-lg text-gray-600 dark:text-gray-400 font-medium">
                                                        None
                                                    </div>
                                                @endif
                                            </div>
                                        @endif
                                    </x-role-card>
                                @elseif($immediateStage === 'doaa' && $pts3->doaa_submitted_at)
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
                            @else
                                <!-- Stages before Dept/Global - Render all passed stages directly -->
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
                                @if($pts3->co_supervisors_submitted_at && !empty($coSupervisors))
                                    <x-role-card role="co_supervisor">
                                        <h5 class="text-base font-bold text-blue-900 dark:text-blue-200">Co-Supervisors</h5>
                                        @foreach($coSupervisors as $i => $coUser)
                                            @php
                                                $isExt = $coUser->isExternalSupervisor();
                                                $roleTitle = $student ? $student->getSupervisorRoleTitle($coUser) : ($isExt ? 'External Supervisor' : "Co-Supervisor {$i}");
                                                $inst = ($isExt && $coUser->externalSupervisorProfile?->affiliated_institute) ? ' - ' . $coUser->externalSupervisorProfile->affiliated_institute : '';
                                            @endphp
                                            <div class="text-xs space-y-1.5 pt-1.5 {{ !$loop->first ? 'border-t border-blue-100 dark:border-blue-900' : '' }}">
                                                <div class="flex items-center justify-between font-semibold text-sm gap-2 sm:gap-4">
                                                    <span>{{ $roleTitle }} <span class="block sm:inline text-xs font-normal text-gray-500 dark:text-gray-400 mt-0.5 sm:mt-0">({{ $coUser->name }}{{ $inst }})</span>:</span>
                                                    <span class="font-bold text-xs whitespace-nowrap shrink-0 text-emerald-600 dark:text-emerald-400">
                                                        ✓ Endorsed
                                                    </span>
                                                </div>
                                            </div>
                                        @endforeach
                                    </x-role-card>
                                @endif

                                <!-- 3. DPGC -->
                                @if($userRank > 3 && $pts3->dpgc_submitted_at)
                                    <x-role-card role="dpgc" title="Department Postgraduate Committee (DPGC)">
                                        <x-slot:badge>
                                            <span class="font-bold text-xs whitespace-nowrap shrink-0 text-emerald-600 dark:text-emerald-400">
                                                ✓ Endorsed
                                            </span>
                                        </x-slot:badge>
                                    </x-role-card>
                                @endif

                                <!-- 4. HOD -->
                                @if($userRank > 4 && $pts3->hod_submitted_at)
                                    <x-role-card role="hod" title="Head of Department (HOD)">
                                        <x-slot:badge>
                                            <span class="font-bold text-xs whitespace-nowrap shrink-0 text-emerald-600 dark:text-emerald-400">
                                                ✓ Endorsed
                                            </span>
                                        </x-slot:badge>
                                    </x-role-card>
                                @endif

                                <!-- 5. Academic Office -->
                                @if($userRank > 5 && $pts3->academic_office_submitted_at)
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
                                        @if(Auth::user()?->isAcademicOffice())
                                            <div class="text-xs text-gray-700 dark:text-gray-300 space-y-1 pt-1">
                                                <strong>Assigned Acting DOAA:</strong>
                                                @if(!empty($pts3->acting_doaa_email))
                                                    @php
                                                        $actingDoaaUser = \App\Models\User::where('email', $pts3->acting_doaa_email)->first();
                                                    @endphp
                                                    <div class="p-2 bg-indigo-50/70 dark:bg-indigo-950/40 border border-indigo-200 dark:border-indigo-800 rounded-lg font-semibold text-indigo-900 dark:text-indigo-200">
                                                        {{ $actingDoaaUser->name ?? $pts3->acting_doaa_email }} ({{ $pts3->acting_doaa_email }})
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

                                <!-- 6. DOAA -->
                                @if($userRank > 6 && $pts3->doaa_submitted_at)
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
                            @endif
                        </div>
                    </div>
                @endif

                <!-- Section 7: Declaration (Formatted identically to MS Declaration) -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-indigo-100 dark:border-indigo-900/50 p-6 space-y-4">
                    <h3 class="text-lg font-bold text-indigo-900 dark:text-indigo-300 border-b border-indigo-100 dark:border-indigo-900/50 pb-2 flex items-center">
                         7.Declaration
                    </h3>

                    <!-- Stage-Specific Remarks before Declaration Checkbox -->
                    @if($pts3->current_stage === 'academic_office')
                        <div class="space-y-1.5 pb-2">
                            <div class="flex items-center justify-between gap-2">
                                <label class="block text-sm font-bold text-gray-800 dark:text-gray-200">
                                    Verification Remark <span class="text-xs font-normal text-gray-500 dark:text-gray-400">(Confidential to DOAA)</span>
                                </label>
                                <x-snippet-dropdown target="academicOfficeVerificationRemark" form-type="pts3" role="academic_office" comment-type="confidential" />
                            </div>
                            <textarea name="academic_office_verification_remark" 
                                      x-model="academicOfficeVerificationRemark"
                                      rows="2" 
                                      class="w-full text-sm rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white placeholder-gray-400" 
                                      placeholder="Optional general verification notes / observations regarding student eligibility...">{{ old('academic_office_verification_remark', $pts3->academic_office_verification_remark) }}</textarea>
                        </div>
                        <div class="space-y-2 pt-2 border-t border-gray-100 dark:border-gray-700">
                            <label class="block font-bold text-gray-900 dark:text-white text-sm">
                                Assign Acting DOAA (Optional)
                            </label>
                            <select name="acting_doaa_email" class="w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm focus:ring-2 focus:ring-indigo-500">
                                <option value="">None (Forward to Default DOAA only)</option>
                                @foreach($actingDoaaUsers ?? [] as $actingUser)
                                    <option value="{{ $actingUser->email }}" {{ (old('acting_doaa_email', $pts3?->acting_doaa_email) === $actingUser->email) ? 'selected' : '' }}>
                                        {{ $actingUser->name }} ({{ $actingUser->email }})
                                    </option>
                                @endforeach
                            </select>
                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                If selected, this form will be visible and actionable for the chosen Acting DOAA alongside the DOAA.
                            </p>
                        </div>
                    @elseif($pts3->current_stage === 'doaa')
                        <div class="space-y-1.5 pb-2">
                            <label class="block text-sm font-bold text-gray-800 dark:text-gray-200">
                                Verification Remark <span class="text-xs font-normal text-gray-500 dark:text-gray-400">(Confidential to Senate Chairperson)</span>
                            </label>
                            <div class="pt-0.5">
                                <x-snippet-dropdown target="doaaVerificationRemark" form-type="pts3" role="doaa" comment-type="confidential" />
                            </div>
                            <textarea name="doaa_verification_remark" 
                                      x-model="doaaVerificationRemark"
                                      rows="2" 
                                      class="w-full text-sm rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white placeholder-gray-400" 
                                      placeholder="Optional general DOAA recommendation remarks...">{{ old('doaa_verification_remark', $pts3->doaa_verification_remark) }}</textarea>
                        </div>
                    @elseif($pts3->current_stage === 'senate_chairperson')
                        <!-- Senate Chairperson Decision Block (Approve / Do Not Approve) -->
                        <div class="space-y-4 pb-2">
                            <x-recommendation-block 
                                label="Approval Status for Proposed Examiner Panels & Oral Examination Board"
                                positive-label="(a) APPROVE"
                                positive-desc="Approve the proposed Indian & International Examiner panels and Oral Examination Board (OEB) members."
                                negative-label="(b) DO NOT APPROVE"
                                negative-desc="Do not approve the proposed examiner panels and OEB members in their present form."
                                remark-label-positive="Approval Remark (Optional)"
                                remark-label-negative="Non-Approval Remark"
                                remark-placeholder-positive="Optional approval remark / decision summary..."
                                remark-placeholder-negative="Provide mandatory non-approval remarks explaining why the submission is not approved"
                                remark-rows="3"
                                name="recommendation"
                                model="recommendation"
                                remark-name="senate_chairperson_approval_remark"
                                remark-model="confidentialRemark"
                                form-type="pts3"
                                role="senate_chairperson"
                                :remark-value="old('senate_chairperson_approval_remark', $pts3->senate_chairperson_approval_remark)" />

                            <div class="space-y-1.5 pt-2 border-t border-gray-100 dark:border-gray-700">
                                <label class="block text-sm font-bold text-gray-800 dark:text-gray-200">
                                    Confidential Remark <span class="text-xs font-normal text-gray-500 dark:text-gray-400">(Visible to DOAA &amp; Senate Chairperson only)</span>
                                </label>
                                <div class="pt-0.5">
                                    <x-snippet-dropdown target="senateConfidentialRemark" form-type="pts3" role="senate_chairperson" comment-type="confidential" />
                                </div>
                                <textarea name="senate_chairperson_confidential_remark" 
                                          x-model="senateConfidentialRemark"
                                          rows="2" 
                                          class="w-full text-sm rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white placeholder-gray-400" 
                                          placeholder="Optional confidential remark...">{{ old('senate_chairperson_confidential_remark', $pts3->senate_chairperson_confidential_remark) }}</textarea>
                            </div>
                        </div>
                    @endif

                    <!-- Declaration Checkbox Card -->
                    <label class="p-4 rounded-xl border-2 transition-all flex items-start space-x-3 cursor-pointer"
                           :class="hasDeclared ? 'border-emerald-500 bg-emerald-50/60 dark:bg-emerald-950/30' : 'border-indigo-200 dark:border-indigo-800 bg-indigo-50/30 dark:bg-indigo-950/20 hover:border-indigo-400'">
                        <input type="checkbox" 
                               name="declaration" 
                               value="1" 
                               x-model="hasDeclared" 
                               required
                               class="mt-1 text-emerald-600 focus:ring-emerald-500 rounded w-5 h-5 cursor-pointer">
                        <div class="space-y-1">
                            <p class="text-xs text-gray-700 dark:text-gray-300 leading-relaxed font-medium">
                                @if($pts3->current_stage === 'co_supervisors')
                                    I hereby declare that I have reviewed the proposed Panels of Examiners (Indian &amp; International) and Oral Examination Board (OEB) members, and I endorse this submission.
                                @elseif($pts3->current_stage === 'dpgc')
                                    I hereby declare that the proposed Panels of Examiners and OEB members have been reviewed and recommended by the DPGC in accordance with institute guidelines.
                                @elseif($pts3->current_stage === 'hod')
                                    I hereby declare that I have reviewed and recommended the proposed Panels of Examiners and OEB members, and forward this submission to the Academic Office.
                                @elseif($pts3->current_stage === 'academic_office')
                                    I hereby declare and confirm that I have verified all student academic records, open seminar date, proposed examiner panels, and attached consent documents for this PTS-3 submission.
                                @elseif($pts3->current_stage === 'doaa')
                                    I hereby declare that I have evaluated the proposed examiner panels and forward the recommendations to the Senate Chairperson.
                                @elseif($pts3->current_stage === 'senate_chairperson')
                                    I hereby declare that the Senate Chairperson decision for this Panel of Examiners and Oral Examination Board submission has been recorded.
                                @else
                                    I hereby declare that all information in this review is confirmed and endorsed.
                                @endif
                            </p>
                        </div>
                    </label>

                    <div x-show="!hasDeclared" class="text-xs text-amber-600 dark:text-amber-400 flex items-center font-medium pl-1">
                        <svg class="w-4 h-4 mr-1.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <span>Please confirm the declaration checkbox above to enable submission.</span>
                    </div>

                    <!-- Submit & Revert Action Buttons Bar -->
                    <div class="flex flex-col-reverse sm:flex-row items-center sm:justify-end gap-3 pt-4 border-t border-gray-100 dark:border-gray-700">
                        @if($userRank >= 2)
                            <!-- Revert Button (Triggers Pop-Up Modal) -->
                            <button type="button" 
                                    @click="showRevertModal = true" 
                                    class="w-full sm:w-auto justify-center bg-red-600 hover:bg-red-700 text-white text-base font-bold px-6 py-3 rounded-xl shadow-lg transition flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"></path>
                                </svg>
                                Revert Form
                            </button>
                        @endif

                        <!-- Main Submit Button (Disabled until declaration is checked) -->
                        <button type="submit" 
                                :disabled="!hasDeclared" 
                                :class="!hasDeclared ? 'opacity-50 cursor-not-allowed bg-gray-400 shadow-none' : 'bg-emerald-600 hover:bg-emerald-700 shadow-lg'"
                                class="w-full sm:w-auto justify-center text-white text-base font-bold px-8 py-3 rounded-xl transition flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                            </svg>
                            Submit @if($pts3->current_stage !== 'senate_chairperson') &amp; Forward @else Decision @endif
                        </button>
                    </div>
                </div>

            </form>

            <!-- Revert Confirmation Pop-Up Modal (Directs back to Main Supervisor) -->
            <x-revert-modal 
                show="showRevertModal" 
                :action="route('pts3.revert', $pts3->id)" 
                :title="__('Revert :prefix-3 Form to Main Supervisor', ['prefix' => $formPrefix])"
                subtitle="Send form back to Main Supervisor for examiner panel modifications"
                onSubmit="clearDraft()"
            />

        </div>
    </div>

    <script>
        function pts3ReviewForm() {
            const userId = @js(auth()->id());
            const thesisId = @js($thesis->id);
            const formId = @js($pts3->id);
            const stage = @js($pts3->current_stage);
            const draftKey = 'pts3_review_draft_user_' + userId + '_thesis_' + thesisId + '_form_' + formId + '_stage_' + stage;

            let savedDraft = {};
            try {
                savedDraft = JSON.parse(sessionStorage.getItem(draftKey) || '{}');
            } catch (e) {}

            return {
                showRevertModal: false,
                hasDeclared: @js(old('declaration') !== null) ? @js((bool)old('declaration')) : (savedDraft.hasDeclared !== undefined ? savedDraft.hasDeclared : false),
                recommendation: @js(old('recommendation')) || savedDraft.recommendation || '1',
                confidentialRemark: @js(old('senate_chairperson_approval_remark')) || savedDraft.confidentialRemark || @js($pts3->senate_chairperson_approval_remark ?? ''),
                senateConfidentialRemark: @js(old('senate_chairperson_confidential_remark')) || savedDraft.senateConfidentialRemark || @js($pts3->senate_chairperson_confidential_remark ?? ''),
                academicOfficeVerificationRemark: @js(old('academic_office_verification_remark')) || savedDraft.academicOfficeVerificationRemark || @js($pts3->academic_office_verification_remark ?? ''),
                doaaVerificationRemark: @js(old('doaa_verification_remark')) || savedDraft.doaaVerificationRemark || @js($pts3->doaa_verification_remark ?? ''),

                init() {
                    const watchFields = [
                        'hasDeclared',
                        'recommendation',
                        'confidentialRemark',
                        'senateConfidentialRemark',
                        'academicOfficeVerificationRemark',
                        'doaaVerificationRemark'
                    ];
                    watchFields.forEach(field => {
                        this.$watch(field, () => this.saveDraft());
                    });
                },

                saveDraft() {
                    try {
                        sessionStorage.setItem(draftKey, JSON.stringify({
                            hasDeclared: this.hasDeclared,
                            recommendation: this.recommendation,
                            confidentialRemark: this.confidentialRemark,
                            senateConfidentialRemark: this.senateConfidentialRemark,
                            academicOfficeVerificationRemark: this.academicOfficeVerificationRemark,
                            doaaVerificationRemark: this.doaaVerificationRemark,
                        }));
                    } catch (e) {}
                },

                clearDraft() {
                    try {
                        const prefix = 'pts3_review_draft_';
                        const formToken = '_form_' + formId;
                        const keysToRemove = [];
                        for (let i = 0; i < sessionStorage.length; i++) {
                            const key = sessionStorage.key(i);
                            if (key && key.startsWith(prefix) && key.includes(formToken)) {
                                keysToRemove.push(key);
                            }
                        }
                        keysToRemove.forEach(k => sessionStorage.removeItem(k));
                        sessionStorage.removeItem(draftKey);
                    } catch (e) {}
                }
            };
        }
    </script>
</x-app-layout>

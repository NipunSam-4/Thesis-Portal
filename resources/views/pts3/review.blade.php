<x-app-layout>
    @php
        $formPrefix = isset($student) && $student->isPhd() ? 'PTS' : 'MSRTS';
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
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6" x-data="{ showStudentInfo: true }">
                    <div class="flex items-center justify-between border-b border-gray-100 dark:border-gray-700 pb-3 mb-4 cursor-pointer select-none" @click="showStudentInfo = !showStudentInfo">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white flex items-center gap-2">
                            <span>1. Student Information</span>
                        </h3>
                        <button type="button" class="p-1.5 rounded-lg text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 transition" @click.stop="showStudentInfo = !showStudentInfo">
                            <svg class="w-5 h-5 transform transition-transform duration-200" :class="{ 'rotate-180': !showStudentInfo }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                    </div>

                    <div x-show="showStudentInfo" x-transition class="grid grid-cols-1 md:grid-cols-3 gap-6">
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
                    $oebData = $oebMembers->map(function($oeb) {
                        return [
                            'id' => $oeb->id,
                            'name' => $oeb->name,
                            'designation' => $oeb->designation,
                            'department' => $oeb->department,
                            'email' => $oeb->email,
                            'phone_formatted' => $oeb->getFormattedPhoneNumber(),
                            'academic_office_remark' => $oeb->academic_office_remark,
                            'doaa_remark' => $oeb->doaa_remark,
                        ];
                    })->values();
                    $oebDraftKey = ($isDoaaActive || $isSenateActive)
                        ? ('pts3_review_draft_oeb_user_' . auth()->id() . '_form_' . $pts3->id . '_stage_' . $pts3->current_stage)
                        : null;
                @endphp

                @if($isDoaaActive || $isSenateActive)
                    @once
                    <script>
                        function initOebReorder(initialList, draftKey) {
                            let list = initialList || [];
                            if (draftKey) {
                                try {
                                    const saved = JSON.parse(sessionStorage.getItem(draftKey) || 'null');
                                    if (saved && Array.isArray(saved) && saved.length === initialList.length) {
                                        const map = new Map(initialList.map(item => [item.id, item]));
                                        const restored = [];
                                        let valid = true;
                                        for (const s of saved) {
                                            if (map.has(s.id)) {
                                                restored.push({ ...map.get(s.id), ...s });
                                            } else {
                                                valid = false;
                                                break;
                                            }
                                        }
                                        if (valid && restored.length === initialList.length) {
                                            list = restored;
                                        }
                                    }
                                } catch (e) {}
                            }

                            return {
                                oebList: list,
                                init() {
                                    if (draftKey) {
                                        this.$watch('oebList', () => {
                                            try {
                                                sessionStorage.setItem(draftKey, JSON.stringify(this.oebList));
                                            } catch (e) {}
                                        });
                                    }
                                },
                                moveUp(index) {
                                    if (index > 0) {
                                        const item = this.oebList.splice(index, 1)[0];
                                        this.oebList.splice(index - 1, 0, item);
                                        if (draftKey) {
                                            try {
                                                sessionStorage.setItem(draftKey, JSON.stringify(this.oebList));
                                            } catch (e) {}
                                        }
                                    }
                                },
                                moveDown(index) {
                                    if (index < this.oebList.length - 1) {
                                        const item = this.oebList.splice(index, 1)[0];
                                        this.oebList.splice(index + 1, 0, item);
                                        if (draftKey) {
                                            try {
                                                sessionStorage.setItem(draftKey, JSON.stringify(this.oebList));
                                            } catch (e) {}
                                        }
                                    }
                                }
                            };
                        }
                    </script>
                    @endonce
                @endif

                <!-- Section 5: Proposed Oral Examination Board (OEB) Members -->
                <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-sm border border-gray-100 dark:border-gray-700 space-y-4"
                     @if($isDoaaActive || $isSenateActive)
                         x-data="initOebReorder({{ Js::from($oebData) }}, {{ Js::from($oebDraftKey) }})"
                     @endif
                >
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
                        <table class="w-full min-w-[750px] text-left text-sm text-gray-600 dark:text-gray-300">
                            <thead class="bg-gray-50 dark:bg-gray-700/50 text-xs uppercase text-gray-500 dark:text-gray-400">
                                <tr>
                                    @if($userRank >= 5)
                                        <th class="p-3 w-20 text-center whitespace-nowrap">{{ ($isDoaaActive || $isSenateActive) ? '# / Order' : '#' }}</th>
                                    @endif
                                    <th class="p-3 min-w-[160px] max-w-[240px]">Name</th>
                                    <th class="p-3 min-w-[140px] max-w-[200px]">Designation</th>
                                    <th class="p-3 min-w-[150px] max-w-[220px]">Department</th>
                                    <th class="p-3 min-w-[160px]">Email</th>
                                    <th class="p-3 min-w-[130px] whitespace-nowrap">Phone</th>
                                </tr>
                            </thead>
                            @if($isDoaaActive || $isSenateActive)
                                <template x-for="(oeb, index) in oebList" :key="oeb.id">
                                    <tbody class="border-b border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800">
                                        <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-700/50 transition-colors align-top">
                                            <td class="p-3 text-center whitespace-nowrap">
                                                <div class="flex items-center justify-start gap-2">
                                                    <div class="flex flex-col gap-0.5 shrink-0 bg-gray-50 dark:bg-gray-700/80 p-0.5 rounded border border-gray-200 dark:border-gray-600 shadow-2xs">
                                                        <button type="button" 
                                                                @click="moveUp(index)" 
                                                                :disabled="index === 0" 
                                                                :class="index === 0 ? 'opacity-25 cursor-not-allowed text-gray-400' : 'hover:bg-indigo-50 dark:hover:bg-indigo-900/40 text-indigo-600 dark:text-indigo-300'" 
                                                                class="px-1 py-0 rounded text-[7px] leading-none font-bold transition flex items-center justify-center h-2.5" 
                                                                title="Move Up">
                                                            ▲
                                                        </button>
                                                        <button type="button" 
                                                                @click="moveDown(index)" 
                                                                :disabled="index === oebList.length - 1" 
                                                                :class="index === oebList.length - 1 ? 'opacity-25 cursor-not-allowed text-gray-400' : 'hover:bg-indigo-50 dark:hover:bg-indigo-900/40 text-indigo-600 dark:text-indigo-300'" 
                                                                class="px-1 py-0 rounded text-[7px] leading-none font-bold transition flex items-center justify-center h-2.5" 
                                                                title="Move Down">
                                                            ▼
                                                        </button>
                                                    </div>
                                                    <span class="w-4 font-bold text-gray-900 dark:text-white text-sm leading-tight text-center" x-text="index + 1"></span>
                                                    
                                                    @if($isDoaaActive)
                                                        <input type="hidden" :name="'doaa_oeb_priority[' + oeb.id + ']'" :value="index + 1">
                                                    @endif
                                                    @if($isSenateActive)
                                                        <input type="hidden" :name="'senate_oeb_priority[' + oeb.id + ']'" :value="index + 1">
                                                    @endif
                                                </div>
                                            </td>
                                            <td class="p-3 font-bold text-gray-900 dark:text-white min-w-[160px] max-w-[240px] break-words" x-text="oeb.name"></td>
                                            <td class="p-3 text-xs text-gray-700 dark:text-gray-300 min-w-[140px] max-w-[200px] break-words" x-text="oeb.designation || 'N/A'"></td>
                                            <td class="p-3 text-xs font-medium text-gray-800 dark:text-gray-200 min-w-[150px] max-w-[220px] break-words" x-text="oeb.department || 'N/A'"></td>
                                            <td class="p-3 text-xs min-w-[160px] break-all">
                                                <a :href="'mailto:' + oeb.email" class="text-indigo-600 dark:text-indigo-400 hover:underline font-medium" x-text="oeb.email"></a>
                                            </td>
                                            <td class="p-3 text-xs text-gray-700 dark:text-gray-300 whitespace-nowrap min-w-[130px]" x-text="oeb.phone_formatted || 'N/A'"></td>
                                        </tr>
                                        @if($isDoaaActive)
                                            <!-- DOAA Sub-Row: View Academic Office Comment + DOAA Comment Input Side-by-Side -->
                                            <tr class="bg-gray-50/40 dark:bg-gray-900/30 border-t border-gray-100 dark:border-gray-800">
                                                <td colspan="{{ $userRank >= 5 ? 6 : 5 }}" class="p-3 px-6">
                                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                                        <!-- Academic Office Comment Card (Left) -->
                                                        <div class="p-3 bg-violet-50/70 dark:bg-violet-950/40 border-l-4 border-violet-500 rounded-xl space-y-1.5">
                                                            <span class="block text-xs font-bold text-violet-900 dark:text-violet-200">
                                                                Academic Office Comment
                                                            </span>
                                                            <div x-show="oeb.academic_office_remark" class="min-w-0 max-w-full">
                                                                <p class="m-0 text-xs text-gray-800 dark:text-gray-100 bg-white/95 dark:bg-gray-900/70 py-1.5 px-3 rounded-md border border-violet-200 dark:border-violet-800/70 whitespace-pre-wrap break-words leading-snug shadow-xs" x-text="oeb.academic_office_remark"></p>
                                                            </div>
                                                            <div x-show="!oeb.academic_office_remark" class="min-w-0 max-w-full">
                                                                <div class="w-full flex items-center gap-2 text-xs text-gray-500 dark:text-gray-400 bg-gray-50/80 dark:bg-gray-900/40 py-1.5 px-3 rounded-md border border-dashed border-gray-300 dark:border-gray-700/60 shadow-xs">
                                                                    <svg class="w-3.5 h-3.5 text-gray-400 dark:text-gray-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 4.418 9 8z"></path></svg>
                                                                    <span class="italic font-normal">No comment provided</span>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <!-- DOAA Comment Card (Right) -->
                                                        <div class="p-3 bg-purple-50/70 dark:bg-purple-950/40 border-l-4 border-purple-500 rounded-xl space-y-1.5">
                                                            <label class="block text-xs font-bold text-purple-900 dark:text-purple-200">
                                                                DOAA Comment
                                                            </label>
                                                            <textarea :name="'doaa_oeb_remarks[' + oeb.id + ']'" 
                                                                      rows="2" 
                                                                      class="w-full text-xs rounded-lg border-purple-200 dark:border-purple-800/70 bg-white dark:bg-gray-900/80 text-gray-800 dark:text-gray-100 focus:border-purple-500 focus:ring-purple-500 placeholder-gray-400 dark:placeholder-gray-500 flex-1" 
                                                                      placeholder="Enter DOAA notes or observations regarding this OEB member..." 
                                                                      x-model="oeb.doaa_remark"></textarea>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                        @elseif($isSenateActive)
                                            <!-- Senate Chairperson Sub-Row: View Academic Office & DOAA Comments Side-by-Side -->
                                            <tr class="bg-gray-50/40 dark:bg-gray-900/30 border-t border-gray-100 dark:border-gray-800">
                                                <td colspan="{{ $userRank >= 5 ? 6 : 5 }}" class="p-3 px-6">
                                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                                        <!-- Academic Office Comment Card (Left) -->
                                                        <div class="p-3 bg-violet-50/70 dark:bg-violet-950/40 border-l-4 border-violet-500 rounded-xl space-y-1.5">
                                                            <span class="block text-xs font-bold text-violet-900 dark:text-violet-200">
                                                                Academic Office Comment
                                                            </span>
                                                            <div x-show="oeb.academic_office_remark" class="min-w-0 max-w-full">
                                                                <p class="m-0 text-xs text-gray-800 dark:text-gray-100 bg-white/95 dark:bg-gray-900/70 py-1.5 px-3 rounded-md border border-violet-200 dark:border-violet-800/70 whitespace-pre-wrap break-words leading-snug shadow-xs" x-text="oeb.academic_office_remark"></p>
                                                            </div>
                                                            <div x-show="!oeb.academic_office_remark" class="min-w-0 max-w-full">
                                                                <div class="w-full flex items-center gap-2 text-xs text-gray-500 dark:text-gray-400 bg-gray-50/80 dark:bg-gray-900/40 py-1.5 px-3 rounded-md border border-dashed border-gray-300 dark:border-gray-700/60 shadow-xs">
                                                                    <svg class="w-3.5 h-3.5 text-gray-400 dark:text-gray-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 4.418 9 8z"></path></svg>
                                                                    <span class="italic font-normal">No comment provided</span>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <!-- DOAA Comment Card (Right) -->
                                                        <div class="p-3 bg-purple-50/70 dark:bg-purple-950/40 border-l-4 border-purple-500 rounded-xl space-y-1.5">
                                                            <span class="block text-xs font-bold text-purple-900 dark:text-purple-200">
                                                                DOAA Comment
                                                            </span>
                                                            <div x-show="oeb.doaa_remark" class="min-w-0 max-w-full">
                                                                <p class="m-0 text-xs text-gray-800 dark:text-gray-100 bg-white/95 dark:bg-gray-900/70 py-1.5 px-3 rounded-md border border-purple-200 dark:border-purple-800/70 whitespace-pre-wrap break-words leading-snug shadow-xs" x-text="oeb.doaa_remark"></p>
                                                            </div>
                                                            <div x-show="!oeb.doaa_remark" class="min-w-0 max-w-full">
                                                                <div class="w-full flex items-center gap-2 text-xs text-gray-500 dark:text-gray-400 bg-gray-50/80 dark:bg-gray-900/40 py-1.5 px-3 rounded-md border border-dashed border-gray-300 dark:border-gray-700/60 shadow-xs">
                                                                    <svg class="w-3.5 h-3.5 text-gray-400 dark:text-gray-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 4.418 9 8z"></path></svg>
                                                                    <span class="italic font-normal">No comment provided</span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endif
                                    </tbody>
                                </template>
                            @else
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

                                        <!-- Sub-Row for Academic Office Active Input -->
                                        @if($isAcademicOfficeActive)
                                            <tr class="bg-violet-50/20 dark:bg-violet-950/20 border-t border-gray-100 dark:border-gray-800">
                                                <td colspan="{{ $userRank >= 5 ? 6 : 5 }}" class="p-3 px-6">
                                                    <x-role-card role="academic_office" class="p-3 !space-y-1.5 max-w-3xl">
                                                        <label class="block text-xs font-bold text-violet-900 dark:text-violet-200">
                                                            Academic Office Comment
                                                        </label>
                                                        <textarea name="oeb_remarks[{{ $oeb->id }}]" 
                                                                  rows="2" 
                                                                  class="w-full text-xs rounded-lg border-violet-200 dark:border-violet-800/70 bg-white dark:bg-gray-900/80 text-gray-800 dark:text-gray-100 focus:border-violet-500 focus:ring-violet-500 placeholder-gray-400 dark:placeholder-gray-500" 
                                                                  placeholder="Enter verification notes or observations regarding this OEB member...">{{ old("oeb_remarks.{$oeb->id}", $oeb->academic_office_remark) }}</textarea>
                                                    </x-role-card>
                                                </td>
                                            </tr>
                                        @endif
                                    @empty
                                        <tr>
                                            <td colspan="{{ $userRank >= 5 ? 6 : 5 }}" class="p-4 text-center text-sm text-gray-500">No OEB members proposed.</td>
                                        </tr>
                                    @endforelse
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Section 6: Prior Authority Recommendations Trail -->
                @if($userRank >= 3)
                    <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-sm border border-gray-100 dark:border-gray-700 space-y-6">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white pb-2 border-b border-gray-100 dark:border-gray-700">
                            6. Prior Evaluation &amp; Recommendations Trail
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
                            <label class="block text-sm font-bold text-gray-800 dark:text-gray-200">
                                Verification Remark <span class="text-xs font-normal text-gray-500 dark:text-gray-400">(Confidential to DOAA &amp; Senate Chairperson)</span>
                            </label>
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
                                :remark-value="old('senate_chairperson_approval_remark', $pts3->senate_chairperson_approval_remark)"
                                :show-snippet="false" />

                            <div class="space-y-1.5 pt-2 border-t border-gray-100 dark:border-gray-700">
                                <label class="block text-sm font-bold text-gray-800 dark:text-gray-200">
                                    Confidential Remark <span class="text-xs font-normal text-gray-500 dark:text-gray-400">(Visible to DOAA &amp; Senate Chairperson only)</span>
                                </label>
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

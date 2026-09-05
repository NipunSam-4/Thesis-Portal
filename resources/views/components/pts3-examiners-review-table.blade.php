@props([
    'examiners',
    'title',
    'type' => 'indian',
    'pts3',
    'userRank' => 0,
    'canEvaluate' => false,
    'collapsible' => false,
    'currentStage' => null,
])

@php
    $currentStage = $currentStage ?? $pts3->current_stage;
    $isAcademicOfficeActive = $canEvaluate && $currentStage === 'academic_office';
    $isDoaaActive = $canEvaluate && $currentStage === 'doaa';
    $isSenateActive = $canEvaluate && $currentStage === 'senate_chairperson';

    $themeColor = $type === 'indian' ? 'indigo' : 'purple';
    $accentBg = $type === 'indian' ? 'bg-indigo-50 dark:bg-indigo-950/70 text-indigo-700 dark:text-indigo-300' : 'bg-purple-50 dark:bg-purple-950/70 text-purple-700 dark:text-purple-300';
    $accentBorder = $type === 'indian' ? 'border-indigo-200 dark:border-indigo-800' : 'border-purple-200 dark:border-purple-800';

    $examinersData = $examiners->map(function($ex) use ($pts3) {
        return [
            'id' => $ex->id,
            'name' => $ex->name,
            'designation' => $ex->designation,
            'organization' => $ex->organization,
            'postal_address' => $ex->postal_address,
            'email' => $ex->email,
            'phone_formatted' => $ex->getFormattedPhoneNumber(),
            'research_area' => $ex->research_area,
            'website' => $ex->website,
            'has_consent' => (bool)$ex->has_consent,
            'consent_doc_url' => $ex->consent_doc_path ? (route('pts.document.serve', ['formType' => 'pts3', 'id' => $pts3->id, 'field' => 'consent_doc']) . '?examiner_id=' . $ex->id) : null,
            'academic_office_remark' => $ex->academic_office_remark,
            'doaa_remark' => $ex->doaa_remark,
        ];
    });
    $draftKey = ($isDoaaActive || $isSenateActive) 
        ? ('pts3_review_draft_examiners_' . $type . '_user_' . auth()->id() . '_form_' . $pts3->id . '_stage_' . $currentStage)
        : null;
@endphp

@if($isDoaaActive || $isSenateActive)
    @once
    <script>
        function initExaminerReorder(initialList, draftKey) {
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
                examinersList: list,
                init() {
                    if (draftKey) {
                        this.$watch('examinersList', () => {
                            try {
                                sessionStorage.setItem(draftKey, JSON.stringify(this.examinersList));
                            } catch (e) {}
                        });
                    }
                },
                moveUp(index) {
                    if (index > 0) {
                        const item = this.examinersList.splice(index, 1)[0];
                        this.examinersList.splice(index - 1, 0, item);
                        if (draftKey) {
                            try {
                                sessionStorage.setItem(draftKey, JSON.stringify(this.examinersList));
                            } catch (e) {}
                        }
                    }
                },
                moveDown(index) {
                    if (index < this.examinersList.length - 1) {
                        const item = this.examinersList.splice(index, 1)[0];
                        this.examinersList.splice(index + 1, 0, item);
                        if (draftKey) {
                            try {
                                sessionStorage.setItem(draftKey, JSON.stringify(this.examinersList));
                            } catch (e) {}
                        }
                    }
                }
            };
        }
    </script>
    @endonce
@endif

<div class="bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-sm border border-gray-100 dark:border-gray-700 space-y-4"
     @if($isDoaaActive || $isSenateActive)
         x-data="initExaminerReorder({{ Js::from($examinersData) }}, {{ Js::from($draftKey) }})"
     @endif
>
    <!-- Section Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-3 border-b border-gray-100 dark:border-gray-700">
        <div>
            <h3 class="text-lg font-bold text-gray-900 dark:text-white flex items-center gap-2">
                <span>{{ $title }}</span>
            </h3>
        </div>

        <div class="flex items-center gap-2">
            <span class="text-xs px-3 py-1 rounded-full font-semibold {{ $accentBg }} border {{ $accentBorder }}">
                Count: {{ $examiners->count() }} Examiner(s)
            </span>
            <span class="text-xs px-2.5 py-1 rounded-full font-medium bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300">
                {{ $examiners->where('has_consent', true)->count() }} with Consent, {{ $examiners->where('has_consent', false)->count() }} without
            </span>
        </div>
    </div>

    <!-- Table Container (Horizontal Scrolling for Mobile/Small Screens) -->
    <div class="overflow-x-auto rounded-xl border border-gray-200 dark:border-gray-700 shadow-2xs">
        <table class="w-full min-w-[1100px] text-left text-sm text-gray-600 dark:text-gray-300">
            <thead class="bg-gray-50 dark:bg-gray-700/50 text-xs uppercase text-gray-500 dark:text-gray-400">
                <tr>
                    @if($userRank >= 5)
                        <th class="p-3 w-20 text-center whitespace-nowrap">{{ ($isDoaaActive || $isSenateActive) ? '# / Order' : '#' }}</th>
                    @endif
                    <th class="p-3 min-w-[150px] max-w-[220px]">Name</th>
                    <th class="p-3 min-w-[140px] max-w-[200px]">Designation</th>
                    <th class="p-3 min-w-[160px] max-w-[220px]">Organization</th>
                    <th class="p-3 min-w-[200px] max-w-[280px]">Postal Address</th>
                    <th class="p-3 min-w-[160px] max-w-[220px]">Email</th>
                    <th class="p-3 min-w-[130px] whitespace-nowrap">Phone</th>
                    <th class="p-3 min-w-[100px] whitespace-nowrap">Website</th>
                    <th class="p-3 min-w-[180px] max-w-[260px]">Research Area</th>
                    <th class="p-3 min-w-[130px] text-center whitespace-nowrap">Consent Status</th>
                    <th class="p-3 min-w-[120px] text-center whitespace-nowrap">Consent Document</th>
                </tr>
            </thead>
            @if($isDoaaActive || $isSenateActive)
                <!-- Dynamic Reorderable Rows for Active DOAA & Senate Chairperson Stages -->
                <template x-for="(ex, index) in examinersList" :key="ex.id">
                    <tbody class="border-b border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800">
                        <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-700/50 transition-colors align-top">
                            <td class="p-3 text-center whitespace-nowrap">
                                <div class="flex items-center justify-start gap-2">
                                    <!-- Reordering Arrows on Far Left -->
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
                                                :disabled="index === examinersList.length - 1" 
                                                :class="index === examinersList.length - 1 ? 'opacity-25 cursor-not-allowed text-gray-400' : 'hover:bg-indigo-50 dark:hover:bg-indigo-900/40 text-indigo-600 dark:text-indigo-300'" 
                                                class="px-1 py-0 rounded text-[7px] leading-none font-bold transition flex items-center justify-center h-2.5" 
                                                title="Move Down">
                                            ▼
                                        </button>
                                    </div>
                                    <span class="w-4 font-bold text-gray-900 dark:text-white text-sm leading-tight text-center" x-text="index + 1"></span>
                                    
                                    @if($isDoaaActive)
                                        <input type="hidden" :name="'doaa_examiner_priority[' + ex.id + ']'" :value="index + 1">
                                    @endif
                                    @if($isSenateActive)
                                        <input type="hidden" :name="'senate_examiner_priority[' + ex.id + ']'" :value="index + 1">
                                    @endif
                                </div>
                            </td>

                            <td class="p-3 font-bold text-gray-900 dark:text-white min-w-[150px] max-w-[220px] break-words" x-text="ex.name"></td>
                            <td class="p-3 text-xs text-gray-700 dark:text-gray-300 min-w-[140px] max-w-[200px] break-words" x-text="ex.designation || 'N/A'"></td>
                            <td class="p-3 text-xs font-medium text-gray-800 dark:text-gray-200 min-w-[160px] max-w-[220px] break-words" x-text="ex.organization || 'N/A'"></td>
                            <td class="p-3 text-xs text-gray-600 dark:text-gray-400 min-w-[200px] max-w-[280px] break-words leading-relaxed whitespace-pre-wrap" x-text="ex.postal_address || 'N/A'"></td>
                            <td class="p-3 text-xs min-w-[160px] max-w-[220px] break-all">
                                <a :href="'mailto:' + ex.email" class="text-indigo-600 dark:text-indigo-400 hover:underline font-medium" x-text="ex.email"></a>
                            </td>
                            <td class="p-3 text-xs text-gray-700 dark:text-gray-300 whitespace-nowrap min-w-[130px]" x-text="ex.phone_formatted || 'N/A'"></td>
                            <td class="p-3 text-xs min-w-[100px] whitespace-nowrap">
                                <template x-if="ex.website">
                                    <a :href="ex.website" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-1 text-indigo-600 dark:text-indigo-400 hover:underline font-medium">
                                        <span x-text="ex.website"></span>
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                    </a>
                                </template>
                                <template x-if="!ex.website">
                                    <span class="text-gray-400">-</span>
                                </template>
                            </td>
                            <td class="p-3 text-xs text-gray-600 dark:text-gray-400 min-w-[180px] max-w-[260px] break-words" x-text="ex.research_area || 'N/A'"></td>
                            <td class="p-3 text-center whitespace-nowrap min-w-[130px]">
                                <template x-if="ex.has_consent">
                                    <span class="inline-flex items-center px-2.5 py-0.5 bg-emerald-100 text-emerald-800 dark:bg-emerald-950/80 dark:text-emerald-300 rounded-full text-xs font-semibold">
                                        ✓ Consent Obtained
                                    </span>
                                </template>
                                <template x-if="!ex.has_consent">
                                    <span class="inline-flex items-center px-2.5 py-0.5 bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300 rounded-full text-xs">
                                        No Consent
                                    </span>
                                </template>
                            </td>
                            <td class="p-3 text-center whitespace-nowrap min-w-[120px]">
                                <template x-if="ex.consent_doc_url">
                                    <a :href="ex.consent_doc_url" target="_blank" class="inline-flex items-center gap-1 px-2.5 py-1 bg-blue-50 dark:bg-blue-950/60 hover:bg-blue-100 dark:hover:bg-blue-900/60 text-blue-700 dark:text-blue-300 text-xs font-medium rounded-lg border border-blue-200 dark:border-blue-800 transition">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                        <span>View Document</span>
                                    </a>
                                </template>
                                <template x-if="!ex.consent_doc_url">
                                    <span class="text-xs text-gray-400">-</span>
                                </template>
                            </td>
                        </tr>

                        @if($isDoaaActive)
                            <!-- DOAA Sub-Row: View Academic Office Comment + DOAA Comment Input Side-by-Side -->
                            <tr class="bg-gray-50/40 dark:bg-gray-900/30 border-t border-gray-100 dark:border-gray-800">
                                <td colspan="{{ $userRank >= 5 ? 11 : 10 }}" class="p-3 px-6">
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <!-- Academic Office Comment Card (Left) -->
                                        <div class="p-3 bg-violet-50/70 dark:bg-violet-950/40 border-l-4 border-violet-500 rounded-xl space-y-1.5">
                                            <span class="block text-xs font-bold text-violet-900 dark:text-violet-200">
                                                Academic Office Comment
                                            </span>
                                            <div x-show="ex.academic_office_remark" class="min-w-0 max-w-full">
                                                <p class="m-0 text-xs text-gray-800 dark:text-gray-100 bg-white/95 dark:bg-gray-900/70 py-1.5 px-3 rounded-md border border-violet-200 dark:border-violet-800/70 whitespace-pre-wrap break-words leading-snug shadow-xs" x-text="ex.academic_office_remark"></p>
                                            </div>
                                            <div x-show="!ex.academic_office_remark" class="min-w-0 max-w-full">
                                                <div class="w-full flex items-center gap-2 text-xs text-gray-500 dark:text-gray-400 bg-gray-50/80 dark:bg-gray-900/40 py-1.5 px-3 rounded-md border border-dashed border-gray-300 dark:border-gray-700/60 shadow-xs">
                                                    <svg class="w-3.5 h-3.5 text-gray-400 dark:text-gray-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 4.418 9 8z"></path></svg>
                                                    <span class="italic font-normal">No comment provided</span>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <!-- DOAA Comment Card (Right) -->
                                        <div class="p-3 bg-purple-50/70 dark:bg-purple-950/40 border-l-4 border-purple-500 rounded-xl space-y-1.5">
                                            <label class="block text-xs font-bold text-purple-900 dark:text-purple-200">
                                                DOAA Comment for <span class="text-gray-900 dark:text-white" x-text="ex.name"></span>
                                            </label>
                                            <textarea :name="'doaa_examiner_remarks[' + ex.id + ']'" 
                                                      rows="2" 
                                                      class="w-full text-xs rounded-lg border-purple-200 dark:border-purple-800/70 bg-white dark:bg-gray-900/80 text-gray-800 dark:text-gray-100 focus:border-purple-500 focus:ring-purple-500 placeholder-gray-400 dark:placeholder-gray-500" 
                                                      placeholder="Enter DOAA notes or observations regarding this examiner..." 
                                                      x-model="ex.doaa_remark"></textarea>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @elseif($isSenateActive)
                            <!-- Senate Chairperson Sub-Row: View Academic Office & DOAA Comments Side-by-Side -->
                            <tr class="bg-gray-50/40 dark:bg-gray-900/30 border-t border-gray-100 dark:border-gray-800">
                                <td colspan="{{ $userRank >= 5 ? 11 : 10 }}" class="p-3 px-6">
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <!-- Academic Office Comment Card (Left) -->
                                        <div class="p-3 bg-violet-50/70 dark:bg-violet-950/40 border-l-4 border-violet-500 rounded-xl space-y-1.5">
                                            <span class="block text-xs font-bold text-violet-900 dark:text-violet-200">
                                                Academic Office Comment
                                            </span>
                                            <div x-show="ex.academic_office_remark" class="min-w-0 max-w-full">
                                                <p class="m-0 text-xs text-gray-800 dark:text-gray-100 bg-white/95 dark:bg-gray-900/70 py-1.5 px-3 rounded-md border border-violet-200 dark:border-violet-800/70 whitespace-pre-wrap break-words leading-snug shadow-xs" x-text="ex.academic_office_remark"></p>
                                            </div>
                                            <div x-show="!ex.academic_office_remark" class="min-w-0 max-w-full">
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
                                            <div x-show="ex.doaa_remark" class="min-w-0 max-w-full">
                                                <p class="m-0 text-xs text-gray-800 dark:text-gray-100 bg-white/95 dark:bg-gray-900/70 py-1.5 px-3 rounded-md border border-purple-200 dark:border-purple-800/70 whitespace-pre-wrap break-words leading-snug shadow-xs" x-text="ex.doaa_remark"></p>
                                            </div>
                                            <div x-show="!ex.doaa_remark" class="min-w-0 max-w-full">
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
                <!-- Standard Blade Table Rows -->
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700 bg-white dark:bg-gray-800">
                    @forelse($examiners as $index => $ex)
                        <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-700/50 transition-colors align-top">
                            @if($userRank >= 5)
                                <td class="p-3 text-center font-bold text-gray-900 dark:text-white whitespace-nowrap">
                                    {{ $index + 1 }}
                                </td>
                            @endif

                            <td class="p-3 font-bold text-gray-900 dark:text-white min-w-[150px] max-w-[220px] break-words">
                                {{ $ex->name }}
                            </td>
                            <td class="p-3 text-xs text-gray-700 dark:text-gray-300 min-w-[140px] max-w-[200px] break-words">
                                {{ $ex->designation }}
                            </td>
                            <td class="p-3 text-xs font-medium text-gray-800 dark:text-gray-200 min-w-[160px] max-w-[220px] break-words">
                                {{ $ex->organization }}
                            </td>
                            <td class="p-3 text-xs text-gray-600 dark:text-gray-400 min-w-[200px] max-w-[280px] break-words leading-relaxed whitespace-pre-wrap">{{ $ex->postal_address ?: 'N/A' }}</td>
                            <td class="p-3 text-xs min-w-[160px] max-w-[220px] break-all">
                                <a href="mailto:{{ $ex->email }}" class="text-indigo-600 dark:text-indigo-400 hover:underline font-medium">
                                    {{ $ex->email }}
                                </a>
                            </td>
                            <td class="p-3 text-xs text-gray-700 dark:text-gray-300 whitespace-nowrap min-w-[130px]">
                                {{ $ex->getFormattedPhoneNumber() ?: 'N/A' }}
                            </td>
                            <td class="p-3 text-xs min-w-[100px] whitespace-nowrap">
                                @if($ex->website)
                                    <a href="{{ $ex->website }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-1 text-indigo-600 dark:text-indigo-400 hover:underline font-medium">
                                        <span>{{ $ex->website }}</span>
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                    </a>
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>
                            <td class="p-3 text-xs text-gray-600 dark:text-gray-400 min-w-[180px] max-w-[260px] break-words">
                                {{ $ex->research_area ?: 'N/A' }}
                            </td>
                            <td class="p-3 text-center whitespace-nowrap min-w-[130px]">
                                @if($ex->has_consent)
                                    <span class="inline-flex items-center px-2.5 py-0.5 bg-emerald-100 text-emerald-800 dark:bg-emerald-950/80 dark:text-emerald-300 rounded-full text-xs font-semibold whitespace-nowrap">
                                        ✓ Consent Obtained
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300 rounded-full text-xs whitespace-nowrap">
                                        No Consent
                                    </span>
                                @endif
                            </td>
                            <td class="p-3 text-center whitespace-nowrap min-w-[120px]">
                                @if($ex->consent_doc_path)
                                    <a href="{{ route('pts.document.serve', ['formType' => 'pts3', 'id' => $pts3->id, 'field' => 'consent_doc']) }}?examiner_id={{ $ex->id }}" 
                                       target="_blank" 
                                       class="inline-flex items-center gap-1 px-2.5 py-1 bg-blue-50 dark:bg-blue-950/60 hover:bg-blue-100 dark:hover:bg-blue-900/60 text-blue-700 dark:text-blue-300 text-xs font-medium rounded-lg border border-blue-200 dark:border-blue-800 transition">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                        <span>View Doc</span>
                                    </a>
                                @else
                                    <span class="text-xs text-gray-400">-</span>
                                @endif
                            </td>
                        </tr>

                        <!-- Sub-Row: Academic Office Active Input -->
                        @if($isAcademicOfficeActive)
                            <tr class="bg-violet-50/20 dark:bg-violet-950/20 border-t border-gray-100 dark:border-gray-800">
                                <td colspan="{{ $userRank >= 5 ? 11 : 10 }}" class="p-3 px-6">
                                    <x-role-card role="academic_office" class="p-3 !space-y-1.5 max-w-3xl">
                                        <label class="block text-xs font-bold text-violet-900 dark:text-violet-200">
                                            Academic Office Comment for <span class="text-gray-900 dark:text-white">{{ $ex->name }}</span>
                                        </label>
                                        <textarea name="examiner_remarks[{{ $ex->id }}]" 
                                                  rows="2" 
                                                  class="w-full text-xs rounded-lg border-violet-200 dark:border-violet-800/70 bg-white dark:bg-gray-900/80 text-gray-800 dark:text-gray-100 focus:border-violet-500 focus:ring-violet-500 placeholder-gray-400 dark:placeholder-gray-500" 
                                                  placeholder="Enter verification notes or observations regarding this examiner...">{{ old("examiner_remarks.{$ex->id}", $ex->academic_office_remark) }}</textarea>
                                    </x-role-card>
                                </td>
                            </tr>
                        @elseif($userRank >= 5 && ($pts3->academic_office_submitted_at || ($userRank >= 6 && $pts3->doaa_submitted_at)))
                            <!-- Read-only Remarks Display for Academic Office, DOAA, Senate in Show view -->
                            <tr class="bg-gray-50/40 dark:bg-gray-900/30 border-t border-gray-100 dark:border-gray-800">
                                <td colspan="{{ $userRank >= 5 ? 11 : 10 }}" class="p-3 px-6">
                                    <div class="grid grid-cols-1 {{ ($userRank >= 6 && ($pts3->doaa_submitted_at || in_array($pts3->status, ['approved', 'rejected']))) ? 'md:grid-cols-2' : '' }} gap-4">
                                        <!-- Academic Office Comment Card (Left) -->
                                        <x-role-card role="academic_office" class="p-3 !space-y-1.5">
                                            <span class="block text-xs font-bold text-violet-900 dark:text-violet-200">
                                                Academic Office Comment
                                            </span>
                                            <x-feedback-box :text="$ex->academic_office_remark" role="academic_office" fallback="No comment provided" />
                                        </x-role-card>

                                        @if($userRank >= 6 && ($pts3->doaa_submitted_at || in_array($pts3->status, ['approved', 'rejected'])))
                                        <!-- DOAA Comment Card (Right) -->
                                        <x-role-card role="doaa" class="p-3 !space-y-1.5">
                                            <span class="block text-xs font-bold text-purple-900 dark:text-purple-200">
                                                DOAA Comment
                                            </span>
                                            <x-feedback-box :text="$ex->doaa_remark" role="doaa" fallback="No comment provided" />
                                        </x-role-card>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endif
                    @empty
                        <tr>
                            <td colspan="{{ $userRank >= 5 ? 11 : 10 }}" class="p-4 text-center text-sm text-gray-500">
                                No examiners found for this panel.
                            </td>
                        </tr>
                    @endforelse
                @endif
            </tbody>
        </table>
    </div>
</div>

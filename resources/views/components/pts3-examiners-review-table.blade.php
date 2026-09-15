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
@endphp

<div class="bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-sm border border-gray-100 dark:border-gray-700 space-y-5"
     x-data="{ activeModalExaminer: null }">
    <!-- Section Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-3 border-b border-gray-100 dark:border-gray-700">
        <div>
            <h3 class="text-lg font-bold text-gray-900 dark:text-white flex items-center gap-2">
                <span>{{ $title }}</span>
            </h3>
        </div>

        <div class="flex items-center gap-2 flex-wrap">
            <span class="text-xs px-3 py-1 rounded-full font-semibold {{ $accentBg }} border {{ $accentBorder }}">
                Count: {{ $examiners->count() }} Examiner(s)
            </span>
            <span class="text-xs px-2.5 py-1 rounded-full font-medium bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300">
                {{ $examiners->where('has_consent', true)->count() }} with Consent, {{ $examiners->where('has_consent', false)->count() }} without
            </span>
        </div>
    </div>

    <!-- Examiners 2-Column Grid (1 -> 2, 3 -> 4) -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        @forelse($examiners as $index => $ex)
            <div class="p-4 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800/90 hover:border-gray-300 dark:hover:border-gray-600 transition shadow-2xs space-y-3.5 flex flex-col justify-between">
                <div>
                    <!-- Card Top Header (Order + Consent + Plus/Details Button) -->
                    <div class="flex items-center justify-between gap-2 pb-2.5 border-b border-gray-100 dark:border-gray-700">
                        <div class="flex items-center gap-2">
                            <!-- Unified Order Box (Visible only to DOAA and Senate Chairperson) -->
                            @if($isDoaaActive)
                                <div class="flex items-center gap-1.5" title="DOAA Priority Order (0 to 4)">
                                    <span class="text-xs font-bold text-purple-800 dark:text-purple-200">Order:</span>
                                    <select name="doaa_examiner_priority[{{ $ex->id }}]" 
                                            class="w-20 h-8 py-0.5 pl-3.5 pr-8 text-sm font-extrabold rounded-lg border-2 border-purple-400 dark:border-purple-500 bg-white dark:bg-gray-900 text-purple-950 dark:text-purple-100 focus:ring-2 focus:ring-purple-500 focus:border-purple-500 shadow-xs cursor-pointer">
                                        @php
                                            $selDoaaPriority = (string)old("doaa_examiner_priority.{$ex->id}", $ex->doaa_priority ?? ($index + 1));
                                        @endphp
                                        @foreach([0, 1, 2, 3, 4] as $val)
                                            <option value="{{ $val }}" class="bg-white dark:bg-gray-900 text-gray-900 dark:text-white font-bold" {{ $selDoaaPriority === (string)$val ? 'selected' : '' }}>
                                                {{ $val }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            @elseif($isSenateActive)
                                <div class="flex items-center gap-1.5" title="Senate Chairperson Priority Order (0 to 4)">
                                    <span class="text-xs font-bold text-indigo-800 dark:text-indigo-200">Order:</span>
                                    <select name="senate_examiner_priority[{{ $ex->id }}]" 
                                            class="w-20 h-8 py-0.5 pl-3.5 pr-8 text-sm font-extrabold rounded-lg border-2 border-indigo-400 dark:border-indigo-500 bg-white dark:bg-gray-900 text-indigo-950 dark:text-indigo-100 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 shadow-xs cursor-pointer">
                                        @php
                                            $selSpPriority = (string)old("senate_examiner_priority.{$ex->id}", $ex->senate_chairperson_priority ?? $ex->doaa_priority ?? ($index + 1));
                                        @endphp
                                        @foreach([0, 1, 2, 3, 4] as $val)
                                            <option value="{{ $val }}" class="bg-white dark:bg-gray-900 text-gray-900 dark:text-white font-bold" {{ $selSpPriority === (string)$val ? 'selected' : '' }}>
                                                {{ $val }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            @elseif($userRank >= 6)
                                @php
                                    $exPriorityNum = ($userRank === 7) 
                                        ? ($ex->senate_chairperson_priority ?? $ex->doaa_priority ?? ($index + 1)) 
                                        : ($ex->doaa_priority ?? ($index + 1));
                                @endphp
                                <span class="inline-flex items-center justify-center w-7 h-7 rounded-full bg-blue-100 dark:bg-blue-950/80 text-blue-900 dark:text-blue-200 border border-blue-200 dark:border-blue-700 text-xs font-black shrink-0 shadow-2xs">
                                    #{{ $exPriorityNum }}
                                </span>
                            @else
                                {{-- For MS, Co-Supervisors, DPGC, HOD, Academic Office: Do not show sequence numbers --}}
                            @endif
                        </div>

                        <div class="flex items-center gap-2">
                            <!-- Consent Status Pill -->
                            @if($ex->has_consent)
                                <span class="inline-flex items-center px-2 py-0.5 bg-emerald-100 text-emerald-800 dark:bg-emerald-950/80 dark:text-emerald-300 rounded-full text-[11px] font-semibold shrink-0">
                                    ✓ Consent Obtained
                                </span>
                            @else
                                <span class="inline-flex items-center px-2 py-0.5 bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300 rounded-full text-[11px] shrink-0">
                                    No Consent
                                </span>
                            @endif

                            <!-- Plus / View Full Details Button -->
                            @php
                                $modalIndex = ($userRank === 7) 
                                    ? ($ex->senate_chairperson_priority ?? $ex->doaa_priority ?? ($index + 1)) 
                                    : (($userRank === 6) ? ($ex->doaa_priority ?? ($index + 1)) : ($index + 1));
                            @endphp
                            <button type="button"                                    @click="activeModalExaminer = {{ Js::from([
                                        'id' => $ex->id,
                                        'index' => $modalIndex,
                                        'show_index' => ($userRank >= 6 || $isDoaaActive || $isSenateActive),
                                        'name' => $ex->name,
                                        'designation' => $ex->designation,
                                        'organization' => $ex->organization,
                                        'research_area' => $ex->research_area,
                                        'has_consent' => (bool)$ex->has_consent,
                                        'consent_doc_url' => $ex->consent_doc_path ? (route('pts.document.serve', ['formType' => 'pts3', 'id' => $pts3->id, 'field' => 'consent_doc']) . '?examiner_id=' . $ex->id) : null,
                                        'website' => $ex->website,
                                        'email' => $ex->email,
                                        'phone' => !empty($ex->phone_number) ? ((($ex->phone_country_code ?? '+91') . ' ' . $ex->phone_number)) : 'N/A',
                                        'postal_address' => $ex->postal_address,
                                    ]) }}" 
                                    class="inline-flex items-center gap-1 px-2 py-0.5 text-xs font-bold rounded-lg bg-blue-50 hover:bg-blue-100 dark:bg-blue-950/60 dark:hover:bg-blue-900/80 text-blue-700 dark:text-blue-300 border border-blue-200 dark:border-blue-800 shadow-2xs transition shrink-0 cursor-pointer"
                                    title="View Full Details">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                                <span>Details</span>
                            </button>
                        </div>
                    </div>

                    <!-- Discrete Fields (Inline: Label: Value) -->
                    <div class="space-y-2 pt-2 text-xs">
                        <!-- Full Name -->
                        <div class="flex items-baseline gap-1.5 flex-wrap">
                            <span class="font-semibold text-gray-500 dark:text-gray-400 shrink-0">Examiner Name:</span>
                            <span class="font-bold text-gray-900 dark:text-white">{{ $ex->name }}</span>
                        </div>

                        <!-- Designation -->
                        <div class="flex items-baseline gap-1.5 flex-wrap">
                            <span class="font-semibold text-gray-500 dark:text-gray-400 shrink-0">Designation:</span>
                            <span class="text-gray-800 dark:text-gray-200 font-medium">{{ $ex->designation ?: 'N/A' }}</span>
                        </div>

                        <!-- Organization -->
                        <div class="flex items-baseline gap-1.5 flex-wrap">
                            <span class="font-semibold text-gray-500 dark:text-gray-400 shrink-0">Organization / Institute:</span>
                            <span class="text-gray-800 dark:text-gray-200 font-medium">{{ $ex->organization ?: 'N/A' }}</span>
                        </div>

                        <!-- Research Area -->
                        <div class="flex items-baseline gap-1.5 flex-wrap">
                            <span class="font-semibold text-gray-500 dark:text-gray-400 shrink-0">Research Area / Specialization:</span>
                            <span class="text-gray-700 dark:text-gray-300 font-medium">{{ $ex->research_area ?: 'N/A' }}</span>
                        </div>

                        <!-- Official Website -->
                        <div class="flex items-baseline gap-1.5 flex-wrap">
                            <span class="font-semibold text-gray-500 dark:text-gray-400 shrink-0">Official Website:</span>
                            @if($ex->website)
                                <a href="{{ $ex->website }}" 
                                   target="_blank" 
                                   rel="noopener noreferrer" 
                                   class="inline-flex items-center gap-1 text-blue-600 dark:text-blue-400 hover:underline font-medium break-all">
                                    <span>{{ $ex->website }}</span>
                                    <svg class="w-3 h-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                </a>
                            @else
                                <span class="text-gray-400 dark:text-gray-500 italic">N/A</span>
                            @endif
                        </div>

                        <!-- Consent Document (if attached) -->
                        @if($ex->has_consent)
                            <div class="flex items-center gap-1.5 flex-wrap pt-0.5">
                                <span class="font-semibold text-gray-500 dark:text-gray-400 shrink-0">Consent Document:</span>
                                @if($ex->consent_doc_path)
                                    <a href="{{ route('pts.document.serve', ['formType' => 'pts3', 'id' => $pts3->id, 'field' => 'consent_doc']) }}?examiner_id={{ $ex->id }}" 
                                       target="_blank" 
                                       class="inline-flex items-center gap-1 px-2.5 py-1 bg-emerald-50 hover:bg-emerald-100 dark:bg-emerald-950/60 dark:hover:bg-emerald-900/80 text-emerald-800 dark:text-emerald-300 text-xs font-semibold rounded-lg border border-emerald-200 dark:border-emerald-800 transition">
                                        <svg class="w-3.5 h-3.5 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                        <span>View Consent Document</span>
                                    </a>
                                @else
                                    <span class="text-xs text-gray-400 italic">No document uploaded</span>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Evaluation Remarks Section -->
                @if($isAcademicOfficeActive)
                    <!-- Academic Office Comment Input -->
                    <div class="pt-2 border-t border-gray-100 dark:border-gray-700" 
                         x-data="{ remark: @js(old("examiner_remarks.{$ex->id}", $ex->academic_office_remark ?? '')) }">
                        <div class="flex items-center justify-between gap-2 mb-1.5">
                            <label class="block text-xs font-bold text-violet-900 dark:text-violet-200">
                                Academic Office Comment
                            </label>

                        </div>
                        <textarea name="examiner_remarks[{{ $ex->id }}]" 
                                  x-model="remark"
                                  rows="2" 
                                  class="w-full text-xs rounded-lg border-violet-200 dark:border-violet-800/70 bg-white dark:bg-gray-900/80 text-gray-800 dark:text-gray-100 focus:border-violet-500 focus:ring-violet-500 placeholder-gray-400 dark:placeholder-gray-500" 
                                  placeholder="Enter verification comment for this examiner..."></textarea>
                    </div>
                @elseif($isDoaaActive)
                    <!-- DOAA Active: View Academic Office Comment + DOAA Comment Input -->
                    <div class="pt-2 border-t border-gray-100 dark:border-gray-700 space-y-2">
                        <div class="space-y-1">
                            <span class="block text-[11px] font-bold text-violet-900 dark:text-violet-200 uppercase tracking-wide">
                                Academic Office Comment
                            </span>
                            <x-feedback-box :text="$ex->academic_office_remark" role="academic_office" fallback="No comment provided" />
                        </div>
                        <div x-data="{ doaaRemark: @js(old("doaa_examiner_remarks.{$ex->id}", $ex->doaa_remark ?? '')) }" class="space-y-1">
                            <div class="flex items-center justify-between gap-2 mb-1.5">
                                <label class="block text-xs font-bold text-purple-900 dark:text-purple-200">
                                    DOAA Comment
                                </label>
                            </div>
                            <textarea name="doaa_examiner_remarks[{{ $ex->id }}]" 
                                      x-model="doaaRemark"
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
                        <x-feedback-box :text="$ex->doaa_remark" role="doaa" fallback="No comment provided" />
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
                                    <x-feedback-box :text="$ex->academic_office_remark" role="academic_office" fallback="No comment provided" />
                                </div>
                            @endif
                            @if($showDoaa)
                                <div class="space-y-1">
                                    <span class="block text-[10px] font-bold text-purple-900 dark:text-purple-200 uppercase tracking-wide">
                                        DOAA Comment
                                    </span>
                                    <x-feedback-box :text="$ex->doaa_remark" role="doaa" fallback="No comment provided" />
                                </div>
                            @endif
                        </div>
                    @endif
                @endif
            </div>
        @empty
            <div class="col-span-full p-6 text-center text-sm text-gray-500 bg-gray-50 dark:bg-gray-800/40 rounded-xl border border-dashed border-gray-200 dark:border-gray-700">
                No examiners found for this panel.
            </div>
        @endforelse
    </div>

    <!-- Comprehensive All-Fields Examiner Details Modal (Matching Student Info Modal Design) -->
    <div x-show="activeModalExaminer" 
         x-cloak 
         class="fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-6 overflow-hidden overscroll-contain"
         style="display: none;"
         @keydown.escape.window="activeModalExaminer = null"
         role="dialog" 
         aria-modal="true">
        
        <!-- Backdrop -->
        <div x-show="activeModalExaminer"
             class="fixed inset-0 transform transition-all"
             @click="activeModalExaminer = null"
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0">
            <div class="absolute inset-0 bg-gray-500/75 dark:bg-gray-900/80 backdrop-blur-sm"></div>
        </div>

        <!-- Modal Dialog Box -->
        <div x-show="activeModalExaminer"
             class="relative w-full sm:max-w-2xl max-h-[90vh] flex flex-col bg-white dark:bg-gray-800 rounded-2xl shadow-xl transform transition-all border border-gray-100 dark:border-gray-700 z-10 text-left overflow-hidden"
             @click.stop
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">

            <!-- Modal Header (Fixed at top) -->
            <div class="flex items-center justify-between border-b border-gray-200 dark:border-gray-700 p-6 pb-4 shrink-0">
                <div>
                    <h2 class="text-lg font-bold text-gray-900 dark:text-gray-100 flex items-center gap-2">
                        <template x-if="activeModalExaminer && activeModalExaminer.show_index">
                            <span class="inline-flex items-center justify-center w-7 h-7 rounded-full bg-blue-100 dark:bg-blue-900/80 text-blue-800 dark:text-blue-200 text-xs font-bold" 
                                  x-text="'#' + activeModalExaminer.index"></span>
                        </template>
                        <span x-text="activeModalExaminer ? activeModalExaminer.name : 'Examiner Profile'"></span>
                    </h2>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                        Complete profile and academic details for proposed examiner.
                    </p>
                </div>
                <button type="button" 
                        @click="activeModalExaminer = null" 
                        class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 transition-colors p-1 text-lg font-bold cursor-pointer">
                    ✕
                </button>
            </div>

            <!-- Profile Data Grid (Scrollable only within grid) -->
            <div class="p-6 pb-3 pt-3 overflow-y-auto overscroll-contain flex-1 grid grid-cols-1 md:grid-cols-2 gap-5 text-sm" x-show="activeModalExaminer">
                <!-- Full Name -->
                <div>
                    <label class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider block mb-1">Full Name</label>
                    <div class="text-sm font-medium text-gray-900 dark:text-gray-100" x-text="activeModalExaminer ? activeModalExaminer.name : 'N/A'"></div>
                </div>

                <!-- Designation -->
                <div>
                    <label class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider block mb-1">Designation</label>
                    <div class="text-sm font-medium text-gray-900 dark:text-gray-100" x-text="activeModalExaminer?.designation || 'N/A'"></div>
                </div>

                <!-- Organization / Institute -->
                <div>
                    <label class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider block mb-1">Organization / Institute</label>
                    <div class="text-sm font-medium text-gray-900 dark:text-gray-100" x-text="activeModalExaminer?.organization || 'N/A'"></div>
                </div>

                <!-- Specialization / Research Area -->
                <div>
                    <label class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider block mb-1">Specialization / Research Area</label>
                    <div class="text-sm font-medium text-gray-900 dark:text-gray-100" x-text="activeModalExaminer?.research_area || 'N/A'"></div>
                </div>

                <!-- Consent Status -->
                <div>
                    <label class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider block mb-1">Consent Status</label>
                    <div class="pt-0.5">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold border"
                              :class="activeModalExaminer?.has_consent ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950/80 dark:text-emerald-300 border-emerald-200 dark:border-emerald-800' : 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300 border-gray-200 dark:border-gray-600'"
                              x-text="activeModalExaminer?.has_consent ? '✓ Consent Obtained' : 'No Consent'"></span>
                    </div>
                </div>

                <!-- Consent Document -->
                <div>
                    <label class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider block mb-1">Consent Document</label>
                    <div class="pt-0.5">
                        <template x-if="activeModalExaminer?.consent_doc_url">
                            <a :href="activeModalExaminer.consent_doc_url" 
                               target="_blank" 
                               class="inline-flex items-center gap-1.5 px-3 py-1 bg-emerald-50 hover:bg-emerald-100 dark:bg-emerald-950/60 dark:hover:bg-emerald-900/80 text-emerald-800 dark:text-emerald-300 text-xs font-semibold rounded-lg border border-emerald-200 dark:border-emerald-800 transition">
                                <svg class="w-3.5 h-3.5 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                <span>View Document</span>
                            </a>
                        </template>
                        <template x-if="!activeModalExaminer?.consent_doc_url">
                            <span class="text-sm font-medium text-gray-400 dark:text-gray-500 italic">No document attached</span>
                        </template>
                    </div>
                </div>

                <!-- Official Website -->
                <div>
                    <label class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider block mb-1">Official Website</label>
                    <div class="text-sm font-medium text-gray-900 dark:text-gray-100 truncate">
                        <template x-if="activeModalExaminer?.website">
                            <a :href="activeModalExaminer.website" 
                               target="_blank" 
                               rel="noopener noreferrer" 
                               class="inline-flex items-center gap-1 text-blue-600 dark:text-blue-400 hover:underline break-all">
                                <span x-text="activeModalExaminer.website"></span>
                                <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                            </a>
                        </template>
                        <template x-if="!activeModalExaminer?.website">
                            <span class="text-gray-400 dark:text-gray-500 italic text-sm">N/A</span>
                        </template>
                    </div>
                </div>

                <!-- Email Address -->
                <div>
                    <label class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider block mb-1">Email Address</label>
                    <div class="text-sm font-medium text-gray-900 dark:text-gray-100 truncate">
                        <template x-if="activeModalExaminer?.email">
                            <a :href="'mailto:' + activeModalExaminer.email" 
                               class="text-blue-600 dark:text-blue-400 hover:underline break-all" 
                               x-text="activeModalExaminer.email"></a>
                        </template>
                        <template x-if="!activeModalExaminer?.email">
                            <span class="text-gray-400 dark:text-gray-500 italic text-sm">N/A</span>
                        </template>
                    </div>
                </div>

                <!-- Contact Phone -->
                <div>
                    <label class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider block mb-1">Contact Phone</label>
                    <div class="text-sm font-medium text-gray-900 dark:text-gray-100" x-text="activeModalExaminer?.phone || 'N/A'"></div>
                </div>

                <!-- Postal Address -->
                <div class="md:col-span-2">
                    <label class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider block mb-1">Postal Address</label>
                    <div class="text-sm font-medium text-gray-900 dark:text-gray-100 whitespace-pre-wrap leading-relaxed" x-text="activeModalExaminer?.postal_address || 'N/A'"></div>
                </div>
            </div>

            <!-- Modal Footer (Fixed at bottom) -->
            <div class="border-t border-gray-200 dark:border-gray-700 p-6 pt-4 flex justify-end shrink-0">
                <button type="button" 
                        @click="activeModalExaminer = null" 
                        class="px-4 py-2 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-800 dark:text-gray-200 rounded-xl text-sm font-bold transition cursor-pointer">
                    Close
                </button>
            </div>
        </div>
    </div>
</div>

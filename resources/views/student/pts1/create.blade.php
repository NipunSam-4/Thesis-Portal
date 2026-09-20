<x-app-layout>
    @php
        $formPrefix = isset($student) && $student->isPhd() ? 'PTS' : 'MSRTS';
        $isReverted = isset($pts1Form) && $pts1Form->status === 'reverted';
    @endphp
    <div class="py-6" x-data="pts1Form()">
        <div class="max-w-5xl mx-auto px-2 sm:px-6 lg:px-8 space-y-4">

            <!-- Top Back to Dashboard Button -->
            <div>
                <x-back-to-dashboard-button />
            </div>

            <!-- Page Header Card -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-5 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div class="space-y-1">
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white leading-tight">
                        {{ $isReverted ? __("Edit & Resubmit {$formPrefix}-1 Form") : __("Submit {$formPrefix}-1 Form") }}
                    </h2>
                </div>
                @if($isReverted)
                    <div class="flex items-center space-x-3 shrink-0">
                        <span class="px-3.5 py-1.5 bg-amber-100 dark:bg-amber-900/60 text-amber-800 dark:text-amber-300 text-xs font-extrabold rounded-full uppercase tracking-wider flex items-center shadow-xs border border-amber-200 dark:border-amber-800">
                            ⚠️ Reverted
                        </span>
                    </div>
                @endif
            </div>

            <!-- Error Alerts -->
            @if($errors->any())
                <div class="p-4 bg-red-100 border-l-4 border-red-500 text-red-700 rounded-lg shadow-sm">
                    <div class="font-bold">Please correct the errors below:</div>
                    <ul class="mt-1 list-disc list-inside text-sm">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('student.pts1.store') }}" method="POST" enctype="multipart/form-data" class="space-y-2" @submit="clearDraft()">
                @csrf

                @php
                    $showModified = $isReverted && $pts1Form->reverted_by_role !== 'main_supervisor';
                @endphp

                @if($isReverted)
                    <div class="p-4 bg-amber-50 dark:bg-amber-950/40 border-l-4 border-amber-500 rounded-xl space-y-2">
                        <div class="flex items-center space-x-2 text-amber-900 dark:text-amber-200">
                            <svg class="w-5 h-5 text-amber-600 dark:text-amber-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                            </svg>
                            <span class="font-bold text-sm">Form Reverted by {{ $pts1Form->getRevertedByRoleLabel() }}</span>
                        </div>
                        @if($pts1Form->reversion_comment)
                            <div class="text-xs text-gray-700 dark:text-gray-300">
                                <strong>Reversion Comment:</strong>
                                <p class="italic bg-white dark:bg-gray-800 p-2.5 rounded-lg border border-amber-200 dark:border-amber-900 mt-1 whitespace-pre-wrap break-words [overflow-wrap:anywhere]">{{ trim($pts1Form->reversion_comment) }}</p>
                            </div>
                        @endif
                    </div>
                @endif

                <!-- Section 1: Pre-filled Student Details (Read-Only) -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white pb-3 mb-4 border-b border-gray-100 dark:border-gray-700 flex items-center gap-2">
                        <span>1. Student Information</span>
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <x-readonly-label value="Student Name" />
                            <x-readonly-input :value="$user->name" />
                        </div>

                        <div>
                            <x-readonly-label value="Roll Number" />
                            <x-readonly-input :value="$student->roll_number" />
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
                            <x-form-label class="flex items-center gap-2">
                                <span>Date of Confirmation <span class="text-red-500">*</span></span>
                                @if($showModified && $pts1Form->main_supervisor_date_confirmation && ($student->date_confirmation ? \Carbon\Carbon::parse($student->date_confirmation)->format('Y-m-d') !== $pts1Form->main_supervisor_date_confirmation->format('Y-m-d') : true))
                                    <x-modified-badge 
                                        :old-value="$student->date_confirmation ? \Carbon\Carbon::parse($student->date_confirmation)->format('d-m-Y') : null" 
                                        :new-value="$pts1Form->main_supervisor_date_confirmation->format('d-m-Y')" />
                                @endif
                            </x-form-label>
                            <x-form-input type="date" name="date_confirmation" required x-model="dateConfirmation" />
                        </div>
                    </div>
                </div>

                <!-- Section 2: Thesis Title -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 space-y-3">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white border-b border-gray-100 dark:border-gray-700 pb-2">
                        2. Thesis Title <span class="text-red-500">*</span>
                    </h3>
                    <div>
                        <x-form-input type="text" name="thesis_title" required x-model="thesisTitle" placeholder="Enter full title of thesis" />
                    </div>
                </div>

                <!-- Section 3: Open Seminar Details -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4 border-b border-gray-100 dark:border-gray-700 pb-2">
                        3. Open Seminar Details
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                        <div>
                            <x-form-label class="flex items-center gap-2">
                                <span>Date of Open Seminar <span class="text-red-500">*</span></span>
                                @if($showModified && $pts1Form->main_supervisor_seminar_date && ($pts1Form->seminar_date ? $pts1Form->main_supervisor_seminar_date->format('Y-m-d') !== $pts1Form->seminar_date->format('Y-m-d') : true))
                                    <x-modified-badge 
                                        :old-value="$pts1Form->seminar_date ? $pts1Form->seminar_date->format('d-m-Y') : 'N/A'" 
                                        :new-value="$pts1Form->main_supervisor_seminar_date->format('d-m-Y')" />
                                @endif
                            </x-form-label>
                            <x-form-input type="date" name="seminar_date" required x-model="seminarDate" min="{{ isset($pts1Form) && $pts1Form->seminar_date ? (\Carbon\Carbon::parse($pts1Form->seminar_date)->lt(\Carbon\Carbon::today()) ? \Carbon\Carbon::parse($pts1Form->seminar_date)->format('Y-m-d') : \Carbon\Carbon::today()->format('Y-m-d')) : \Carbon\Carbon::today()->format('Y-m-d') }}" />
                        </div>

                        <div>
                            <x-form-label class="flex items-center gap-2">
                                <span>Time of Open Seminar <span class="text-red-500">*</span></span>
                                @if($showModified && $pts1Form->main_supervisor_seminar_time && $pts1Form->main_supervisor_seminar_time !== $pts1Form->seminar_time)
                                    <x-modified-badge 
                                        :old-value="$pts1Form->seminar_time ?? 'N/A'" 
                                        :new-value="$pts1Form->main_supervisor_seminar_time" />
                                @endif
                            </x-form-label>
                            <x-form-input type="time" name="seminar_time" required x-model="seminarTime" />
                        </div>

                        <div>
                            <x-form-label class="flex items-center gap-2">
                                <span>Venue of Open Seminar <span class="text-red-500">*</span></span>
                                @if($showModified && $pts1Form->main_supervisor_seminar_venue && $pts1Form->main_supervisor_seminar_venue !== $pts1Form->seminar_venue)
                                    <x-modified-badge 
                                        :old-value="$pts1Form->seminar_venue ?? 'N/A'" 
                                        :new-value="$pts1Form->main_supervisor_seminar_venue" />
                                @endif
                            </x-form-label>
                            <x-form-input type="text" name="seminar_venue" placeholder="e.g. Seminar Hall 1, CSE Dept" required x-model="seminarVenue" />
                        </div>

                        <div>
                            <x-form-label class="flex items-center gap-2">
                                <span>Online Meeting Link (Optional)</span>
                                @if($showModified && $pts1Form->main_supervisor_meeting_link !== null && $pts1Form->main_supervisor_meeting_link !== $pts1Form->meeting_link)
                                    <x-modified-badge 
                                        :old-value="$pts1Form->meeting_link ?: null" 
                                        :new-value="$pts1Form->main_supervisor_meeting_link ?: 'None'" />
                                @endif
                            </x-form-label>
                            <x-form-input type="url" name="meeting_link" placeholder="https://meet.google.com/abc-defg-hij" x-model="meetingLink" />
                        </div>
                    </div>
                </div>

                <!-- Section 4: Publication & Minimum Time Criteria -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 space-y-6">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white border-b border-gray-100 dark:border-gray-700 pb-2">
                        4. Institute Norms & Requirements
                    </h3>

                    <!-- Minimum Time Requirement -->
                    <div class="p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg space-y-4">
                        <label class="block font-medium text-gray-900 dark:text-white text-sm flex items-center gap-2">
                            <span>Are you fulfilling the minimum time requirement criteria for thesis submission? <span class="text-red-500">*</span></span>
                            @if($showModified && $pts1Form->main_supervisor_min_time_req_fulfilled !== null && (bool)$pts1Form->main_supervisor_min_time_req_fulfilled !== (bool)$pts1Form->min_time_req_fulfilled)
                                <x-modified-badge 
                                    :old-value="$pts1Form->min_time_req_fulfilled ? 'Yes' : 'No'" 
                                    :new-value="$pts1Form->main_supervisor_min_time_req_fulfilled ? 'Yes' : 'No'" />
                            @endif
                        </label>
                        <div class="flex items-center space-x-6">
                            <label class="inline-flex items-center">
                                <input type="radio" name="min_time_req_fulfilled" value="1" required x-model="timeNorm" class="text-blue-600">
                                <span class="ml-2 text-sm text-gray-800 dark:text-gray-200">Yes</span>
                            </label>
                            <label class="inline-flex items-center">
                                <input type="radio" name="min_time_req_fulfilled" value="0" required x-model="timeNorm" class="text-blue-600">
                                <span class="ml-2 text-sm text-gray-800 dark:text-gray-200">No</span>
                            </label>
                        </div>
                        
                        <!-- If No: Special Approval Question -->
                        <div x-show="timeNorm === '0'" x-cloak class="pt-3 border-t border-gray-200 dark:border-gray-600 space-y-3">
                            <label class="block font-medium text-gray-900 dark:text-white text-sm flex items-center gap-2">
                                <span>Have you taken special approval for the same? <span class="text-red-500">*</span></span>
                                @if($showModified && $pts1Form->main_supervisor_special_approval_min_time !== null && (bool)$pts1Form->main_supervisor_special_approval_min_time !== (bool)$pts1Form->special_approval_min_time)
                                    <x-modified-badge 
                                        :old-value="$pts1Form->special_approval_min_time !== null ? ($pts1Form->special_approval_min_time ? 'Yes' : 'No') : null" 
                                        :new-value="$pts1Form->main_supervisor_special_approval_min_time ? 'Yes' : 'No'" />
                                @endif
                            </label>
                            <div class="flex items-center space-x-6">
                                <label class="inline-flex items-center">
                                    <input type="radio" name="special_approval_min_time" value="1" :required="timeNorm === '0'" x-model="timeApproval" class="text-blue-600">
                                    <span class="ml-2 text-sm text-gray-800 dark:text-gray-200">Yes</span>
                                </label>
                                <label class="inline-flex items-center">
                                    <input type="radio" name="special_approval_min_time" value="0" :required="timeNorm === '0'" x-model="timeApproval" class="text-blue-600">
                                    <span class="ml-2 text-sm text-gray-800 dark:text-gray-200">No</span>
                                </label>
                            </div>
                            
                            <div x-show="timeApproval === '1'" class="pt-2">
                                <div class="flex justify-between items-center mb-1 gap-4">
                                    <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 flex items-center gap-2">
                                        <span>Upload the corresponding approval copy (Max 2 MB) <span class="text-red-500">*</span></span>
                                        @if($showModified && $pts1Form->main_supervisor_min_time_approval_doc_path && $pts1Form->main_supervisor_min_time_approval_doc_path !== $pts1Form->min_time_approval_doc_path)
                                            <x-modified-badge />
                                        @endif
                                    </label>
                                    <span class="text-[11px] text-gray-400">PDF, PNG, JPG</span>
                                </div>
                                
                                <div x-show="!fileStates.timeApp.name">
                                    <input type="file" id="timeAppInput" name="min_time_approval_doc" accept=".pdf,.png,.jpg,.jpeg" :required="timeNorm === '0' && timeApproval === '1' && !fileStates.timeApp.name" @change="handleFileSelect($event, 'timeApp')" class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                                </div>
                                
                                <!-- File Size Error Alert -->
                                <div x-show="fileErrors.timeApp" x-cloak class="mt-2 text-xs font-bold text-red-600 dark:text-red-400 bg-red-50 dark:bg-red-900/30 p-2.5 rounded-lg border border-red-200 dark:border-red-800">
                                    <span x-text="fileErrors.timeApp"></span>
                                </div>
                                
                                <div x-show="fileStates.timeApp.name" x-cloak class="p-3 bg-emerald-50 dark:bg-emerald-900/30 border border-emerald-200 dark:border-emerald-800 rounded-xl flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                                    <div class="flex items-center space-x-3 min-w-0 truncate">
                                        <div class="p-2 bg-emerald-100 dark:bg-emerald-800 rounded-lg text-emerald-700 dark:text-emerald-300 shrink-0">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                        </div>
                                        <div class="truncate">
                                            <div class="text-[10px] text-emerald-800 dark:text-emerald-300 font-bold uppercase">Selected Approval File</div>
                                            <div class="text-sm font-semibold text-gray-900 dark:text-white truncate" x-text="fileStates.timeApp.name"></div>
                                            <div class="text-[11px] text-gray-500" x-text="fileStates.timeApp.size"></div>
                                        </div>
                                    </div>
                                    <div class="flex items-center space-x-2 shrink-0 justify-end sm:justify-start">
                                        <a :href="fileStates.timeApp.url" target="_blank" class="px-2.5 py-1 bg-white dark:bg-gray-800 text-emerald-700 dark:text-emerald-300 border border-emerald-300 dark:border-emerald-700 rounded-lg text-xs font-bold shadow-sm hover:bg-emerald-100 flex items-center">
                                            📄 View File
                                        </a>
                                        <button type="button" @click="clearFile('timeApp', 'timeAppInput')" class="px-2.5 py-1 bg-white dark:bg-gray-800 text-red-600 dark:text-red-400 border border-red-200 dark:border-red-800 rounded-lg text-xs font-bold shadow-sm hover:bg-red-50 dark:hover:bg-red-900/30 flex items-center">
                                            <svg class="w-3.5 h-3.5 mr-1 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                            Delete File
                                        </button>
                                    </div>
                                </div>
                            </div>
                            
                            <div x-show="timeApproval === '0'" class="p-3 bg-red-50 dark:bg-red-900/30 text-red-700 dark:text-red-300 rounded-lg text-sm font-semibold flex items-center">
                                <svg class="w-5 h-5 mr-2 text-red-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path></svg>
                                Please take the approval from respective authority then proceed.
                            </div>
                        </div>
                    </div>

                    <!-- Publication Norm -->
                    <div class="p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg space-y-4">
                        <label class="block font-medium text-gray-900 dark:text-white text-sm flex items-center gap-2">
                            <span>Are you fulfilling the Institute publication norm for open seminar? <span class="text-red-500">*</span></span>
                            @if($showModified && $pts1Form->main_supervisor_publication_norm_fulfillment !== null && (bool)$pts1Form->main_supervisor_publication_norm_fulfillment !== (bool)$pts1Form->publication_norm_fulfillment)
                                <x-modified-badge 
                                    :old-value="$pts1Form->publication_norm_fulfillment ? 'Yes' : 'No'" 
                                    :new-value="$pts1Form->main_supervisor_publication_norm_fulfillment ? 'Yes' : 'No'" />
                            @endif
                        </label>
                        <div class="flex items-center space-x-6">
                            <label class="inline-flex items-center">
                                <input type="radio" name="publication_norm_fulfillment" value="1" required x-model="pubNorm" class="text-blue-600">
                                <span class="ml-2 text-sm text-gray-800 dark:text-gray-200">Yes</span>
                            </label>
                            <label class="inline-flex items-center">
                                <input type="radio" name="publication_norm_fulfillment" value="0" required x-model="pubNorm" class="text-blue-600">
                                <span class="ml-2 text-sm text-gray-800 dark:text-gray-200">No</span>
                            </label>
                        </div>

                        <!-- If No: Special Approval Question -->
                        <div x-show="pubNorm === '0'" x-cloak class="pt-3 border-t border-gray-200 dark:border-gray-600 space-y-3">
                            <label class="block font-medium text-gray-900 dark:text-white text-sm flex items-center gap-2">
                                <span>Have you taken special approval for the same? <span class="text-red-500">*</span></span>
                                @if($showModified && $pts1Form->main_supervisor_special_approval_publication !== null && (bool)$pts1Form->main_supervisor_special_approval_publication !== (bool)$pts1Form->special_approval_publication)
                                    <x-modified-badge 
                                        :old-value="$pts1Form->special_approval_publication !== null ? ($pts1Form->special_approval_publication ? 'Yes' : 'No') : null" 
                                        :new-value="$pts1Form->main_supervisor_special_approval_publication ? 'Yes' : 'No'" />
                                @endif
                            </label>
                            <div class="flex items-center space-x-6">
                                <label class="inline-flex items-center">
                                    <input type="radio" name="special_approval_publication" value="1" :required="pubNorm === '0'" x-model="pubApproval" class="text-blue-600">
                                    <span class="ml-2 text-sm text-gray-800 dark:text-gray-200">Yes</span>
                                </label>
                                <label class="inline-flex items-center">
                                    <input type="radio" name="special_approval_publication" value="0" :required="pubNorm === '0'" x-model="pubApproval" class="text-blue-600">
                                    <span class="ml-2 text-sm text-gray-800 dark:text-gray-200">No</span>
                                </label>
                            </div>

                            <div x-show="pubApproval === '1'" class="pt-2">
                                <div class="flex justify-between items-center mb-1 gap-4">
                                    <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 flex items-center gap-2">
                                        <span>Upload the corresponding approval copy (Max 2 MB) <span class="text-red-500">*</span></span>
                                        @if($showModified && $pts1Form->main_supervisor_publication_approval_doc_path && $pts1Form->main_supervisor_publication_approval_doc_path !== $pts1Form->publication_approval_doc_path)
                                            <x-modified-badge />
                                        @endif
                                    </label>
                                    <span class="text-[11px] text-gray-400">PDF, PNG, JPG</span>
                                </div>

                                <div x-show="!fileStates.pubApp.name">
                                    <input type="file" id="pubAppInput" name="publication_approval_doc" accept=".pdf,.png,.jpg,.jpeg" :required="pubNorm === '0' && pubApproval === '1' && !fileStates.pubApp.name" @change="handleFileSelect($event, 'pubApp')" class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                                </div>

                                <!-- File Size Error Alert -->
                                <div x-show="fileErrors.pubApp" x-cloak class="mt-2 text-xs font-bold text-red-600 dark:text-red-400 bg-red-50 dark:bg-red-900/30 p-2.5 rounded-lg border border-red-200 dark:border-red-800">
                                    <span x-text="fileErrors.pubApp"></span>
                                </div>

                                <div x-show="fileStates.pubApp.name" x-cloak class="p-3 bg-emerald-50 dark:bg-emerald-900/30 border border-emerald-200 dark:border-emerald-800 rounded-xl flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                                    <div class="flex items-center space-x-3 min-w-0 truncate">
                                        <div class="p-2 bg-emerald-100 dark:bg-emerald-800 rounded-lg text-emerald-700 dark:text-emerald-300 shrink-0">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                        </div>
                                        <div class="truncate">
                                            <div class="text-[10px] text-emerald-800 dark:text-emerald-300 font-bold uppercase">Selected Approval File</div>
                                            <div class="text-sm font-semibold text-gray-900 dark:text-white truncate" x-text="fileStates.pubApp.name"></div>
                                            <div class="text-[11px] text-gray-500" x-text="fileStates.pubApp.size"></div>
                                        </div>
                                    </div>
                                    <div class="flex items-center space-x-2 shrink-0 justify-end sm:justify-start">
                                        <a :href="fileStates.pubApp.url" target="_blank" class="px-2.5 py-1 bg-white dark:bg-gray-800 text-emerald-700 dark:text-emerald-300 border border-emerald-300 dark:border-emerald-700 rounded-lg text-xs font-bold shadow-sm hover:bg-emerald-100 flex items-center">
                                            📄 View File
                                        </a>
                                        <button type="button" @click="clearFile('pubApp', 'pubAppInput')" class="px-2.5 py-1 bg-white dark:bg-gray-800 text-red-600 dark:text-red-400 border border-red-200 dark:border-red-800 rounded-lg text-xs font-bold shadow-sm hover:bg-red-50 dark:hover:bg-red-900/30 flex items-center">
                                            <svg class="w-3.5 h-3.5 mr-1 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                            Delete File
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div x-show="pubApproval === '0'" class="p-3 bg-red-50 dark:bg-red-900/30 text-red-700 dark:text-red-300 rounded-lg text-sm font-semibold flex items-center">
                                <svg class="w-5 h-5 mr-2 text-red-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path></svg>
                                Please take the approval from respective authority then proceed.
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Section 4: Document Uploads & Live Multi-Sheet XLSX Preview -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 space-y-6">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white border-b border-gray-100 dark:border-gray-700 pb-2">
                        4. Document Uploads & Publication Preview
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 md:gap-2">
                        <!-- Draft Synopsis Upload -->
                        <div>
                            <div class="flex justify-between items-center md:pr-2">
                                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 flex items-center gap-2">
                                    <span>Upload Draft Synopsis Report (.pdf, .docx)<span class="text-red-500">*</span></span>
                                    @if($showModified && $pts1Form->main_supervisor_draft_synopsis_report_doc_path && $pts1Form->main_supervisor_draft_synopsis_report_doc_path !== $pts1Form->draft_synopsis_report_doc_path)
                                        <x-modified-badge />
                                    @endif
                                </label>
                                <div class="flex items-center space-x-2">
                                    <span class="text-[11px] text-gray-400">Max 10 MB</span>
                                </div>
                            </div>

                            <div class="md:h-9"></div>

                            <div x-show="!fileStates.synopsis.name">
                                <input type="file" id="synopsisInput" name="draft_synopsis_report" accept=".pdf,.docx" :required="!fileStates.synopsis.name" @change="handleFileSelect($event, 'synopsis')" class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                            </div>

                            <!-- File Size Error Alert -->
                            <div x-show="fileErrors.synopsis" x-cloak class="mt-2 text-xs font-bold text-red-600 dark:text-red-400 bg-red-50 dark:bg-red-900/30 p-2.5 rounded-lg border border-red-200 dark:border-red-800">
                                <span x-text="fileErrors.synopsis"></span>
                            </div>

                            <div x-show="fileStates.synopsis.name" x-cloak class="p-3 bg-emerald-50 dark:bg-emerald-900/30 border border-emerald-200 dark:border-emerald-800 rounded-xl flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                                <div class="flex items-center space-x-3 min-w-0 truncate">
                                    <div class="p-2 bg-emerald-100 dark:bg-emerald-800 rounded-lg text-emerald-700 dark:text-emerald-300 shrink-0">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                    </div>
                                    <div class="truncate">
                                        <div class="text-[10px] text-emerald-800 dark:text-emerald-300 font-bold uppercase">Selected Draft Synopsis</div>
                                        <div class="text-sm font-semibold text-gray-900 dark:text-white truncate" x-text="fileStates.synopsis.name"></div>
                                        <div class="text-[11px] text-gray-500" x-text="fileStates.synopsis.size"></div>
                                    </div>
                                </div>
                                <div class="flex items-center space-x-2 shrink-0 justify-end sm:justify-start">
                                    <a :href="fileStates.synopsis.url" target="_blank" class="px-2.5 py-1 bg-white dark:bg-gray-800 text-emerald-700 dark:text-emerald-300 border border-emerald-300 dark:border-emerald-700 rounded-lg text-xs font-bold shadow-sm hover:bg-emerald-100 flex items-center">
                                        📄 View File
                                    </a>
                                    <button type="button" @click="clearFile('synopsis', 'synopsisInput')" class="px-2.5 py-1 bg-white dark:bg-gray-800 text-red-600 dark:text-red-400 border border-red-200 dark:border-red-800 rounded-lg text-xs font-bold shadow-sm hover:bg-red-50 dark:hover:bg-red-900/30 flex items-center">
                                        <svg class="w-3.5 h-3.5 mr-1 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                        Delete File
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Publication List Upload -->
                        <div>
                            <div class="flex justify-between items-center">
                                <label class="text-sm font-semibold text-gray-700 dark:text-gray-300 flex items-center gap-2">
                                    <span>Upload Publication and Other Recognition List (.xlsx, .xls)<span class="text-red-500">*</span></span>
                                    @if($showModified && $pts1Form->main_supervisor_publication_list_doc_path && $pts1Form->main_supervisor_publication_list_doc_path !== $pts1Form->publication_list_doc_path)
                                        <x-modified-badge />
                                    @endif
                                </label>
                                <div class="flex items-center space-x-2">
                                    <span class="text-[11px] text-gray-400">Max 2 MB</span>
                                </div>
                            </div>

                            <div class="mb-3">
                                <a href="{{ route('student.pts1.template.download') }}" class="text-xs font-bold text-indigo-600 dark:text-indigo-400 hover:underline">
                                    ↓ Download Template Format
                                </a>
                            </div>

                            <div x-show="!fileStates.pubList.name">
                                <input type="file" id="pubListInput" name="publication_list" accept=".xlsx,.xls" :required="!fileStates.pubList.name" @change="if (handleFileSelect($event, 'pubList')) { handleExcelPreview($event); }" class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                            </div>

                            <!-- File Size Error Alert -->
                            <div x-show="fileErrors.pubList" x-cloak class="mt-2 text-xs font-bold text-red-600 dark:text-red-400 bg-red-50 dark:bg-red-900/30 p-2.5 rounded-lg border border-red-200 dark:border-red-800">
                                <span x-text="fileErrors.pubList"></span>
                            </div>

                            <div x-show="fileStates.pubList.name" x-cloak class="p-3 bg-emerald-50 dark:bg-emerald-900/30 border border-emerald-200 dark:border-emerald-800 rounded-xl flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                                <div class="flex items-center space-x-3 min-w-0 truncate">
                                    <div class="p-2 bg-emerald-100 dark:bg-emerald-800 rounded-lg text-emerald-700 dark:text-emerald-300 shrink-0">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                    </div>
                                    <div class="truncate">
                                        <div class="text-[10px] text-emerald-800 dark:text-emerald-300 font-bold uppercase">Selected Publication List</div>
                                        <div class="text-sm font-semibold text-gray-900 dark:text-white truncate" x-text="fileStates.pubList.name"></div>
                                        <div class="text-[11px] text-gray-500" x-text="fileStates.pubList.size"></div>
                                    </div>
                                </div>
                                <div class="flex items-center space-x-2 shrink-0 justify-end sm:justify-start">
                                    <a :href="fileStates.pubList.url" target="_blank" class="px-2.5 py-1 bg-white dark:bg-gray-800 text-emerald-700 dark:text-emerald-300 border border-emerald-300 dark:border-emerald-700 rounded-lg text-xs font-bold shadow-sm hover:bg-emerald-100 flex items-center">
                                        📄 View File
                                    </a>
                                    <button type="button" @click="clearFile('pubList', 'pubListInput')" class="px-2.5 py-1 bg-white dark:bg-gray-800 text-red-600 dark:text-red-400 border border-red-200 dark:border-red-800 rounded-lg text-xs font-bold shadow-sm hover:bg-red-50 dark:hover:bg-red-900/30 flex items-center">
                                        <svg class="w-3.5 h-3.5 mr-1 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                        Delete File
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Live SheetJS Excel Preview Container (Renders ALL sheets) -->
                    <div id="excelPreviewContainer" class="mt-6 hidden space-y-6 border-t border-gray-200 dark:border-gray-700 pt-4">
                        <div class="flex items-center justify-between">
                            <h4 class="text-md font-bold text-indigo-900 dark:text-indigo-300 flex items-center">
                                <svg class="w-5 h-5 mr-2 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                Publication and Other Recognition Preview
                            </h4>
                            <span class="text-xs font-bold bg-indigo-100 dark:bg-indigo-900 text-indigo-800 dark:text-indigo-300 px-3 py-1 rounded-full" id="excelSheetCount"></span>
                        </div>

                        <!-- Sheet Tabs Navigation Bar -->
                        <div id="sheetTabsBar" class="flex flex-wrap gap-2 border-b border-gray-200 dark:border-gray-700 pb-2"></div>

                        <!-- All Sheet Content Containers -->
                        <div id="sheetsOutput" class="space-y-8"></div>
                    </div>
                </div>

                <!-- Submit Action -->
                <div class="flex justify-center sm:justify-end pt-4">
                    <button type="submit" :disabled="isBlocked" :class="isBlocked ? 'opacity-50 cursor-not-allowed bg-gray-400' : 'bg-blue-600 hover:bg-blue-700'" class="w-full sm:w-auto text-white text-base font-bold px-8 py-3 rounded-xl shadow-lg transition">
                        {{ $isReverted ? "Resubmit {$formPrefix}-1 Form" : "Submit {$formPrefix}-1 Form" }}
                    </button>
                </div>
            </form>

        </div>
    </div>

    <script>
        function pts1Form() {
            const userId = @js(auth()->id());
            const thesisId = @js($thesis->id);
            const formId = @js(isset($pts1Form) && $pts1Form ? $pts1Form->id : null);
            const existingPts1 = @js(isset($pts1Form) ? $pts1Form : null);
            const draftKey = 'pts1_student_draft_user_' + userId + '_thesis_' + thesisId + (formId ? '_form_' + formId : '');

            let savedDraft = {};
            try {
                savedDraft = JSON.parse(sessionStorage.getItem(draftKey) || '{}');
            } catch (e) {}

            return {
                dateConfirmation: @js(old('date_confirmation')) || savedDraft.dateConfirmation || @js($student->date_confirmation ? \Carbon\Carbon::parse($student->date_confirmation)->format('Y-m-d') : ''),
                thesisTitle: @js(old('thesis_title')) || savedDraft.thesisTitle || @js($thesis->title ?? ''),
                seminarDate: @js(old('seminar_date')) || savedDraft.seminarDate || @js(isset($pts1Form) && $pts1Form->seminar_date ? \Carbon\Carbon::parse($pts1Form->seminar_date)->format('Y-m-d') : ''),
                seminarTime: @js(old('seminar_time')) || savedDraft.seminarTime || @js(isset($pts1Form) ? $pts1Form->seminar_time : ''),
                seminarVenue: @js(old('seminar_venue')) || savedDraft.seminarVenue || @js(isset($pts1Form) ? $pts1Form->seminar_venue : ''),
                meetingLink: @js(old('meeting_link')) || savedDraft.meetingLink || @js(isset($pts1Form) ? $pts1Form->meeting_link : ''),

                pubNorm: @js(old('publication_norm_fulfillment')) || savedDraft.pubNorm || @js(isset($pts1Form) ? ($pts1Form->publication_norm_fulfillment ? '1' : '0') : '1'),
                pubApproval: @js(old('special_approval_publication')) || savedDraft.pubApproval || @js(isset($pts1Form) ? ($pts1Form->special_approval_publication ? '1' : '0') : '1'),
                timeNorm: @js(old('min_time_req_fulfilled')) || savedDraft.timeNorm || @js(isset($pts1Form) ? ($pts1Form->min_time_req_fulfilled ? '1' : '0') : '1'),
                timeApproval: @js(old('special_approval_min_time')) || savedDraft.timeApproval || @js(isset($pts1Form) ? ($pts1Form->special_approval_min_time ? '1' : '0') : '1'),

                // Standard default PHP php.ini upload limit (10 MB = 10240 KB)
                maxSizes: {
                    synopsis: 10, // 10 MB limit
                    pubList: 2,  // 2 MB limit
                    pubApp: 2,   // 2 MB limit
                    timeApp: 2   // 2 MB limit
                },

                fileErrors: {
                    synopsis: '',
                    pubList: '',
                    pubApp: '',
                    timeApp: ''
                },

                fileStates: {
                    synopsis: existingPts1 && existingPts1.draft_synopsis_report_doc_path ? {
                        name: existingPts1.draft_synopsis_report_doc_path.split('/').pop(),
                        size: 'Uploaded Document',
                        url: "{{ route('pts.document.serve', ['pts1', $pts1Form->id ?? 0, 'draft_synopsis_report_doc_path']) }}"
                    } : { name: '', size: '', url: null },

                    pubList: existingPts1 && existingPts1.publication_list_doc_path ? {
                        name: existingPts1.publication_list_doc_path.split('/').pop(),
                        size: 'Uploaded Document',
                        url: "{{ route('pts.document.serve', ['pts1', $pts1Form->id ?? 0, 'publication_list_doc_path']) }}"
                    } : { name: '', size: '', url: null },

                    pubApp: existingPts1 && existingPts1.publication_approval_doc_path ? {
                        name: existingPts1.publication_approval_doc_path.split('/').pop(),
                        size: 'Uploaded Document',
                        url: "{{ route('pts.document.serve', ['pts1', $pts1Form->id ?? 0, 'publication_approval_doc_path']) }}"
                    } : { name: '', size: '', url: null },

                    timeApp: existingPts1 && existingPts1.min_time_approval_doc_path ? {
                        name: existingPts1.min_time_approval_doc_path.split('/').pop(),
                        size: 'Uploaded Document',
                        url: "{{ route('pts.document.serve', ['pts1', $pts1Form->id ?? 0, 'min_time_approval_doc_path']) }}"
                    } : { name: '', size: '', url: null }
                },

                init() {
                    const watchFields = [
                        'dateConfirmation',
                        'thesisTitle',
                        'seminarDate',
                        'seminarTime',
                        'seminarVenue',
                        'meetingLink',
                        'pubNorm',
                        'pubApproval',
                        'timeNorm',
                        'timeApproval'
                    ];
                    watchFields.forEach(field => {
                        this.$watch(field, () => this.saveDraft());
                    });
                    if (existingPts1 && existingPts1.publication_list_doc_path) {
                        window.previewExcelUrl("{{ route('pts.document.serve', ['pts1', $pts1Form->id ?? 0, 'publication_list_doc_path']) }}");
                    }
                },
                
                get isBlocked() {
                    if (this.pubNorm === '0' && this.pubApproval === '0') return true;
                    if (this.timeNorm === '0' && this.timeApproval === '0') return true;
                    if (this.fileErrors.synopsis || this.fileErrors.pubList || this.fileErrors.pubApp || this.fileErrors.timeApp) return true;
                    return false;
                },

                handleFileSelect(event, key) {
                    const file = event.target.files[0];
                    if (!file) return false;

                    const sizeInMB = file.size / (1024 * 1024);
                    const limitMB = this.maxSizes[key] || 2;

                    if (sizeInMB > limitMB) {
                        const formattedSize = sizeInMB >= 1 ? `${sizeInMB.toFixed(2)} MB` : `${(file.size / 1024).toFixed(1)} KB`;
                        window.dispatchEvent(new CustomEvent('file-size-exceeded', {
                            detail: {
                                fileName: file.name,
                                fileSize: formattedSize,
                                limitMB: `${limitMB} MB`,
                                inputId: event.target.id
                            }
                        }));
                        this.fileErrors[key] = `File size (${formattedSize}) exceeds permissible limit of ${limitMB} MB. Please select a smaller file.`;
                        event.target.value = '';
                        this.clearFile(key, event.target.id);
                        return false;
                    }

                    this.fileErrors[key] = '';

                    if (this.fileStates[key].url && !this.fileStates[key].url.includes('/pts/document/serve/')) {
                        URL.revokeObjectURL(this.fileStates[key].url);
                    }

                    this.fileStates[key] = {
                        name: file.name,
                        size: `${sizeInMB.toFixed(2)} MB`,
                        url: URL.createObjectURL(file)
                    };

                    return true;
                },

                clearFile(key, inputId) {
                    this.fileErrors[key] = '';
                    if (this.fileStates[key].url && !this.fileStates[key].url.includes('/pts/document/serve/')) {
                        URL.revokeObjectURL(this.fileStates[key].url);
                    }
                    this.fileStates[key] = { name: '', size: '', url: null };
                    const input = document.getElementById(inputId);
                    if (input) input.value = '';
                    
                    if (key === 'pubList') {
                        window.clearExcelPreview();
                    }
                },

                async handleExcelPreview(event) {
                    const file = event.target.files[0];
                    if (!file) return;
                    try {
                        const result = await window.previewExcelFile(file, { validatePublications: true });
                        if (result && !result.isValid) {
                            this.fileErrors.pubList = 'Mandatory publication columns (Title of Paper and Journal/Conference Name) are missing or not filled in one or more rows.';
                        } else {
                            this.fileErrors.pubList = '';
                        }
                    } catch (e) {
                        this.fileErrors.pubList = 'Could not parse Excel file. Please ensure it is a valid spreadsheet.';
                    }
                },

                saveDraft() {
                    try {
                        sessionStorage.setItem(draftKey, JSON.stringify({
                            dateConfirmation: this.dateConfirmation,
                            thesisTitle: this.thesisTitle,
                            seminarDate: this.seminarDate,
                            seminarTime: this.seminarTime,
                            seminarVenue: this.seminarVenue,
                            meetingLink: this.meetingLink,
                            pubNorm: this.pubNorm,
                            pubApproval: this.pubApproval,
                            timeNorm: this.timeNorm,
                            timeApproval: this.timeApproval,
                        }));
                    } catch (e) {}
                },

                clearDraft() {
                    try {
                        sessionStorage.removeItem(draftKey);
                    } catch (e) {}
                }
            }
        }
    </script>
</x-app-layout>

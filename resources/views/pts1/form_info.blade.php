@php
    $pts1 = $pts1 ?? $form ?? null;
    $thesis = $thesis ?? $pts1?->thesis;
    $student = $student ?? $thesis?->student;
    $studentUser = $studentUser ?? $student?->user;
@endphp

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
            <x-readonly-label class="flex items-center gap-2">
                <span>Date of Confirmation</span>
                @if($pts1->main_supervisor_date_confirmation && ($student->date_confirmation ? \Carbon\Carbon::parse($student->date_confirmation)->format('Y-m-d') !== $pts1->main_supervisor_date_confirmation->format('Y-m-d') : true))
                    <x-modified-badge 
                        :old-value="$student->date_confirmation ? \Carbon\Carbon::parse($student->date_confirmation)->format('d-m-Y') : null" 
                        :new-value="$pts1->main_supervisor_date_confirmation->format('d-m-Y')" />
                @endif
            </x-readonly-label>
            @php
                $dispDateConfirmation = $pts1->effective_date_confirmation ? $pts1->effective_date_confirmation->format('d-m-Y') : ($student->date_confirmation ? \Carbon\Carbon::parse($student->date_confirmation)->format('d-m-Y') : 'N/A');
            @endphp
            <x-readonly-input :value="$dispDateConfirmation" />
        </div>

        <div>
            <x-readonly-label value="Date of Submission" />
            <x-readonly-input :value="$pts1->created_at ? $pts1->created_at->format('d-m-Y') : 'N/A'" />
        </div>
    </div>
</div>

<!-- Section 2: Name of Thesis (Read-Only) -->
<div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 space-y-2">
    <h3 class="text-lg font-bold text-gray-900 dark:text-white border-b border-gray-100 dark:border-gray-700 pb-2">
        2. Name of Thesis
    </h3>
    <div>
        <x-form-label class="flex items-center gap-2">
            <span>Thesis Title</span>
            @if($pts1->main_supervisor_thesis_title && $pts1->main_supervisor_thesis_title !== $pts1->thesis_title)
                <x-modified-badge 
                    :old-value="$pts1->thesis_title ?: ($pts1->thesis?->title ?: 'N/A')" 
                    :new-value="$pts1->main_supervisor_thesis_title" />
            @endif
        </x-form-label>
        @php
            $dispThesisTitle = $pts1->effective_thesis_title;
        @endphp
        <x-readonly-input :value="$dispThesisTitle" />
    </div>
</div>

<!-- Section 3: Seminar & Confirmation Details (Read-Only) -->
<div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4 border-b border-gray-100 dark:border-gray-700 pb-2">
        3. Open Seminar Details
    </h3>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
            <x-form-label class="flex items-center gap-2">
                <span>Date of Open Seminar</span>
                @if($pts1->main_supervisor_seminar_date && ($pts1->seminar_date ? $pts1->main_supervisor_seminar_date->format('Y-m-d') !== $pts1->seminar_date->format('Y-m-d') : true))
                    <x-modified-badge 
                        :old-value="$pts1->seminar_date ? $pts1->seminar_date->format('d-m-Y') : 'N/A'" 
                        :new-value="$pts1->main_supervisor_seminar_date->format('d-m-Y')" />
                @endif
            </x-form-label>
            @php
                $dispSeminarDate =($pts1->effective_seminar_date ? $pts1->effective_seminar_date->format('d-m-Y') : 'N/A');
            @endphp
            <x-readonly-input :value="$dispSeminarDate" />
        </div>

        <div>
            <x-form-label class="flex items-center gap-2">
                <span>Time of Open Seminar</span>
                @if($pts1->main_supervisor_seminar_time && $pts1->main_supervisor_seminar_time !== $pts1->seminar_time)
                    <x-modified-badge 
                        :old-value="$pts1->seminar_time ?? 'N/A'" 
                        :new-value="$pts1->main_supervisor_seminar_time" />
                @endif
            </x-form-label>
            @php
                $dispSeminarTime = ($pts1->effective_seminar_time ?? 'N/A');
            @endphp
            <x-readonly-input :value="$dispSeminarTime" />
        </div>

        <div>
            <x-form-label class="flex items-center gap-2">
                <span>Venue of Open Seminar</span>
                @if($pts1->main_supervisor_seminar_venue && $pts1->main_supervisor_seminar_venue !== $pts1->seminar_venue)
                    <x-modified-badge 
                        :old-value="$pts1->seminar_venue ?? 'N/A'" 
                        :new-value="$pts1->main_supervisor_seminar_venue" />
                @endif
            </x-form-label>
            @php
                $dispSeminarVenue = ($pts1->effective_seminar_venue ?? 'N/A');
            @endphp
            <x-readonly-input :value="$dispSeminarVenue" />
        </div>

        <div>
            <x-form-label class="flex items-center gap-2">
                <span>Online Meeting Link</span>
                @if($pts1->main_supervisor_meeting_link !== null && $pts1->main_supervisor_meeting_link !== $pts1->meeting_link)
                    <x-modified-badge 
                        :old-value="$pts1->meeting_link ?: null" 
                        :new-value="$pts1->main_supervisor_meeting_link ?: 'None'" />
                @endif
            </x-form-label>
            @php
                $dispMeetingLink = ($pts1->effective_meeting_link ?? '');
            @endphp
            <x-readonly-input :value="$dispMeetingLink" />
        </div>
    </div>
</div>

<!-- Section 4: Institute Norms & Criteria Verification (Read-Only) -->
<div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 space-y-6">
    <h3 class="text-lg font-bold text-gray-900 dark:text-white border-b border-gray-100 dark:border-gray-700 pb-2">
        4. Institute Norms & Criteria Verification
    </h3>

    <!-- Minimum Time Requirement -->
    <div class="p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg space-y-3">
        <div class="flex items-center justify-between gap-2 sm:gap-4">
            <span class="font-semibold text-gray-900 dark:text-white text-sm flex items-center gap-2">
                <span>Fulfilling minimum time requirement criteria for thesis submission?</span>
                @if($pts1->main_supervisor_min_time_req_fulfilled !== null && (bool)$pts1->main_supervisor_min_time_req_fulfilled !== (bool)$pts1->min_time_req_fulfilled)
                    <x-modified-badge 
                        :old-value="$pts1->min_time_req_fulfilled ? 'Yes' : 'No'" 
                        :new-value="$pts1->main_supervisor_min_time_req_fulfilled ? 'Yes' : 'No'" />
                @endif
            </span>
            @php
                $dispMinTime = $pts1->effective_min_time_req_fulfilled;
            @endphp
            <span class="px-3 py-1 text-xs font-bold rounded-full whitespace-nowrap shrink-0 {{ $dispMinTime ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/60 dark:text-emerald-300' : 'bg-red-100 text-red-800 dark:bg-red-900/60 dark:text-red-300' }}">
                {{ $dispMinTime ? 'Yes' : 'No' }}
            </span>
        </div>
        
        @if(!$dispMinTime)
            <div class="pt-3 border-t border-gray-200 dark:border-gray-600 space-y-2">
                <div class="flex items-center justify-between gap-2 sm:gap-4">
                    <span class="font-semibold text-gray-900 dark:text-white text-sm flex items-center gap-2">
                        <span>Special approval taken for minimum time relaxation?</span>
                        @if($pts1->main_supervisor_special_approval_min_time !== null && (bool)$pts1->main_supervisor_special_approval_min_time !== (bool)$pts1->special_approval_min_time)
                            <x-modified-badge 
                                :old-value="$pts1->special_approval_min_time !== null ? ($pts1->special_approval_min_time ? 'Yes' : 'No') : null" 
                                :new-value="$pts1->main_supervisor_special_approval_min_time ? 'Yes' : 'No'" />
                        @endif
                    </span>
                    @php
                        $dispSpecialMinTime = $pts1->effective_special_approval_min_time;
                    @endphp
                    <span class="px-3 py-1 text-xs font-bold rounded-full whitespace-nowrap shrink-0 {{ $dispSpecialMinTime ? 'bg-emerald-100 text-emerald-800' : 'bg-red-100 text-red-800' }}">
                        {{ $dispSpecialMinTime ? 'Yes' : 'No' }}
                    </span>
                </div>
                @php
                    $minTimeDocPath =$pts1->getEffectiveMinTimeApprovalPath();
                    $minTimeDocField = $pts1->getEffectiveMinTimeApprovalField();
                @endphp
                @if($minTimeDocPath)
                    <div class="pt-1 flex items-center gap-2">
                        <a href="{{ route('pts.document.serve', ['pts1', $pts1->id, $minTimeDocField]) }}" target="_blank" class="inline-flex items-center text-xs font-bold text-blue-600 dark:text-blue-400 hover:underline">
                            📄 View Special Minimum Time Approval Copy
                        </a>
                        @if($pts1->main_supervisor_min_time_approval_doc_path && $pts1->main_supervisor_min_time_approval_doc_path !== $pts1->min_time_approval_doc_path)
                            <x-modified-badge />
                        @endif
                    </div>
                @endif
            </div>
        @endif
    </div>
    
    <!-- Publication Norm -->
    <div class="p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg space-y-3">
        <div class="flex items-center justify-between gap-2 sm:gap-4">
            <span class="font-semibold text-gray-900 dark:text-white text-sm flex items-center gap-2">
                <span>Fulfilling Institute publication norm for open seminar?</span>
                @if($pts1->main_supervisor_publication_norm_fulfillment !== null && (bool)$pts1->main_supervisor_publication_norm_fulfillment !== (bool)$pts1->publication_norm_fulfillment)
                    <x-modified-badge 
                        :old-value="$pts1->publication_norm_fulfillment ? 'Yes' : 'No'" 
                        :new-value="$pts1->main_supervisor_publication_norm_fulfillment ? 'Yes' : 'No'" />
                @endif
            </span>
            @php
                $dispPubNorm =$pts1->effective_publication_norm_fulfillment;
            @endphp
            <span class="px-3 py-1 text-xs font-bold rounded-full whitespace-nowrap shrink-0 {{ $dispPubNorm ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/60 dark:text-emerald-300' : 'bg-red-100 text-red-800 dark:bg-red-900/60 dark:text-red-300' }}">
                {{ $dispPubNorm ? 'Yes' : 'No' }}
            </span>
        </div>

        @if(!$dispPubNorm)
            <div class="pt-3 border-t border-gray-200 dark:border-gray-600 space-y-2">
                <div class="flex items-center justify-between gap-2 sm:gap-4">
                    <span class="font-semibold text-gray-900 dark:text-white text-sm flex items-center gap-2">
                        <span>Special approval taken for publication norm relaxation?</span>
                        @if($pts1->main_supervisor_special_approval_publication !== null && (bool)$pts1->main_supervisor_special_approval_publication !== (bool)$pts1->special_approval_publication)
                            <x-modified-badge 
                                :old-value="$pts1->special_approval_publication !== null ? ($pts1->special_approval_publication ? 'Yes' : 'No') : null" 
                                :new-value="$pts1->main_supervisor_special_approval_publication ? 'Yes' : 'No'" />
                        @endif
                    </span>
                    @php
                        $dispSpecialPub = $pts1->effective_special_approval_publication;
                    @endphp
                    <span class="px-3 py-1 text-xs font-bold rounded-full whitespace-nowrap shrink-0 {{ $dispSpecialPub ? 'bg-emerald-100 text-emerald-800' : 'bg-red-100 text-red-800' }}">
                        {{ $dispSpecialPub ? 'Yes' : 'No' }}
                    </span>
                </div>
                @php
                    $pubDocPath = $pts1->getEffectivePublicationApprovalPath();
                    $pubDocField = $pts1->getEffectivePublicationApprovalField();
                @endphp
                @if($pubDocPath)
                    <div class="pt-1 flex items-center gap-2">
                        <a href="{{ route('pts.document.serve', ['pts1', $pts1->id, $pubDocField]) }}" target="_blank" class="inline-flex items-center text-xs font-bold text-blue-600 dark:text-blue-400 hover:underline">
                            📄 View Special Publication Approval Copy
                        </a>
                        @if($pts1->main_supervisor_publication_approval_doc_path && $pts1->main_supervisor_publication_approval_doc_path !== $pts1->publication_approval_doc_path)
                            <x-modified-badge />
                        @endif
                    </div>
                @endif
            </div>
        @endif
    </div>
</div>

<!-- Section 5: Uploaded Documents Inspection (Read-Only) -->
<div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 space-y-6">
    <h3 class="text-lg font-bold text-gray-900 dark:text-white border-b border-gray-100 dark:border-gray-700 pb-2">
        5. Uploaded Documents
    </h3>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <!-- Draft Synopsis Card -->
        <div class="p-4 bg-gray-50 dark:bg-gray-700/50 rounded-xl border border-gray-200 dark:border-gray-600 space-y-2">
            <div class="text-xs font-bold text-gray-500 uppercase flex items-center gap-2">
                <span>Draft Synopsis Report</span>
                @if($pts1->main_supervisor_draft_synopsis_report_doc_path && $pts1->main_supervisor_draft_synopsis_report_doc_path !== $pts1->draft_synopsis_report_doc_path)
                    <x-modified-badge />
                @endif
            </div>
            @php
                $draftDocPath = $pts1->getEffectiveDraftSynopsisPath();
                $draftDocField = $pts1->getEffectiveDraftSynopsisField();
            @endphp
            @if($draftDocPath)
                <a href="{{ route('pts.document.serve', ['pts1', $pts1->id, $draftDocField]) }}" target="_blank" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-lg shadow transition inline-flex items-center">
                    📄 View Draft Synopsis Report
                </a>
            @else
                <span class="text-xs font-bold text-gray-400 italic">No File Uploaded</span>
            @endif
        </div>

        <!-- Publication List Card -->
        <div class="p-4 bg-gray-50 dark:bg-gray-700/50 rounded-xl border border-gray-200 dark:border-gray-600 space-y-2">
            <div class="text-xs font-bold text-gray-500 uppercase flex items-center gap-2">
                <span>Publication and Other Recognition List</span>
                @if($pts1->main_supervisor_publication_list_doc_path && $pts1->main_supervisor_publication_list_doc_path !== $pts1->publication_list_doc_path)
                    <x-modified-badge />
                @endif
            </div>
            @php
                $pubListPath = $pts1->getEffectivePublicationListPath();
                $pubListField = $pts1->getEffectivePublicationListField();
            @endphp
            @if($pubListPath)
                <a href="{{ route('pts.document.serve', ['pts1', $pts1->id, $pubListField]) }}" target="_blank" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-lg shadow transition inline-flex items-center">
                    📊 Download / View Publication List
                </a>
            @else
                <span class="text-xs font-bold text-gray-400 italic">No File Uploaded</span>
            @endif
        </div>
    </div>

    <!-- Live SheetJS Excel Preview Container (Renders ALL sheets) -->
    <div id="excelPreviewContainer" class="mt-6 hidden space-y-6 border-t border-gray-200 dark:border-gray-700 pt-4">
        <div class="flex items-center justify-between">
            <h4 class="text-md font-bold text-indigo-900 dark:text-indigo-300 flex items-center">
                <svg class="w-5 h-5 mr-2 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                <span id="excelPreviewTitle">Publication and Other Recognition Preview</span>
            </h4>
            <span class="text-xs font-bold bg-indigo-100 dark:bg-indigo-900 text-indigo-800 dark:text-indigo-300 px-3 py-1 rounded-full" id="excelSheetCount"></span>
        </div>

        <!-- Sheet Tabs Navigation Bar -->
        <div id="sheetTabsBar" class="flex flex-wrap gap-2 border-b border-gray-200 dark:border-gray-700 pb-2"></div>

        <!-- All Sheet Content Containers -->
        <div id="sheetsOutput" class="space-y-8"></div>
    </div>
</div>

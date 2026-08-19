<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div class="flex items-center space-x-3">
                <h2 class="font-bold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                    {{ __('Reverted PTS-1 Form Details & Remarks') }}
                </h2>
                <span class="px-3 py-1 bg-amber-100 dark:bg-amber-900/60 text-amber-800 dark:text-amber-300 text-xs font-extrabold rounded-full uppercase tracking-wider flex items-center shadow-sm">
                    ⚠️ Reverted
                </span>
            </div>
            <x-back-to-dashboard-button />
        </div>
    </x-slot>

    <!-- Custom CSS for Live Excel Table Previews -->
    <style>
        .sheet-table-container table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.825rem;
            margin-top: 0.5rem;
        }
        .sheet-table-container th, .sheet-table-container td {
            border: 1px solid #e5e7eb;
            padding: 0.5rem 0.75rem;
            text-align: left;
        }
        .sheet-table-container tr:first-child {
            background-color: #f3f4f6;
            font-weight: 700;
            color: #1f2937;
        }
        .sheet-table-container tr:nth-child(even) {
            background-color: #f9fafb;
        }

        @media (prefers-color-scheme: dark) {
            .sheet-table-container th,
            .sheet-table-container td {
                border-color: #374151;
                color: #d1d5db;
            }

            .sheet-table-container tr:first-child,
            .sheet-table-container tr:first-child td,
            .sheet-table-container tr:first-child th {
                background-color: #374151 !important;
                color: #ffffff !important;
                font-weight: 700;
            }

            .sheet-table-container tr:not(:first-child) {
                background-color: transparent !important;
            }
            .sheet-table-container tr:not(:first-child) td,
            .sheet-table-container tr:not(:first-child) th {
                background-color: transparent !important;
                color: #d1d5db !important;
            }
        }
    </style>

    <div class="py-6">
        <div class="max-w-5xl mx-auto px-2 sm:px-6 lg:px-8 space-y-6">

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

            <!-- Section 1: Read-Only Student Information -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
                <div class="flex items-center justify-between border-b border-gray-100 dark:border-gray-700 pb-3 mb-4">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white flex items-center">
                        1. Student Information
                    </h3>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label class="block text-xs font-semibold uppercase text-gray-500 mb-1">Student Name</label>
                        <input type="text" value="{{ $studentUser->name }}" readonly class="w-full bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200 rounded-lg border-gray-300 dark:border-gray-600 cursor-not-allowed">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold uppercase text-gray-500 mb-1">Roll Number</label>
                        <input type="text" value="{{ $student->roll_number }}" readonly class="w-full bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200 rounded-lg border-gray-300 dark:border-gray-600 cursor-not-allowed">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold uppercase text-gray-500 mb-1">Department</label>
                        <input type="text" value="{{ $student->department->name ?? 'N/A' }}" readonly class="w-full bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200 rounded-lg border-gray-300 dark:border-gray-600 cursor-not-allowed">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold uppercase text-gray-500 mb-1">Date of Registration</label>
                        <input type="text" value="{{ $student->date_registration ? \Carbon\Carbon::parse($student->date_registration)->format('d-m-Y') : 'N/A' }}" readonly class="w-full bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200 rounded-lg border-gray-300 dark:border-gray-600 cursor-not-allowed">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold uppercase text-gray-500 mb-1">Date of Joining</label>
                        <input type="text" value="{{ $student->date_joining ? \Carbon\Carbon::parse($student->date_joining)->format('d-m-Y') : 'N/A' }}" readonly class="w-full bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200 rounded-lg border-gray-300 dark:border-gray-600 cursor-not-allowed">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold uppercase text-gray-500 mb-1">Date of Confirmation</label>
                        <input type="text" value="{{ $student->date_confirmation ? \Carbon\Carbon::parse($student->date_confirmation)->format('d-m-Y') : 'N/A' }}" readonly class="w-full bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200 rounded-lg border-gray-300 dark:border-gray-600 cursor-not-allowed">
                    </div>
                </div>
            </div>

            <!-- Section 2: Name of Thesis (Read-Only) -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 space-y-2">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white border-b border-gray-100 dark:border-gray-700 pb-2">
                    2. Name of Thesis
                </h3>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Thesis Title</label>
                    <input type="text" value="{{ $pts1->thesis_title ?? $thesis->title ?? ($pts1->thesis->title ?? '') }}" readonly class="w-full bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200 rounded-lg border-gray-300 dark:border-gray-600 cursor-not-allowed">
                </div>
            </div>

            <!-- Section 3: Seminar & Confirmation Details (Read-Only) -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4 border-b border-gray-100 dark:border-gray-700 pb-2">
                    3. Open Seminar Details
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Date of Open Seminar</label>
                        <input type="text" value="{{ $pts1->seminar_date ? \Carbon\Carbon::parse($pts1->seminar_date)->format('d-m-Y') : 'N/A' }}" readonly class="w-full bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200 rounded-lg border-gray-300 dark:border-gray-600 cursor-not-allowed">
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Time of Open Seminar</label>
                        <input type="text" value="{{ $pts1->seminar_time ?? 'N/A' }}" readonly class="w-full bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200 rounded-lg border-gray-300 dark:border-gray-600 cursor-not-allowed">
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Venue of Open Seminar</label>
                        <input type="text" value="{{ $pts1->seminar_venue ?? 'N/A' }}" readonly class="w-full bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200 rounded-lg border-gray-300 dark:border-gray-600 cursor-not-allowed">
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Online Meeting Link</label>
                        <input type="text" value="{{ $pts1->meeting_link ?? '' }}" readonly class="w-full bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200 rounded-lg border-gray-300 dark:border-gray-600 cursor-not-allowed">
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
                        <span class="font-semibold text-gray-900 dark:text-white text-sm">
                            Fulfilling minimum time requirement criteria for thesis submission?
                        </span>
                        <span class="px-3 py-1 text-xs font-bold rounded-full whitespace-nowrap shrink-0 {{ $pts1->min_time_req_fulfilled ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/60 dark:text-emerald-300' : 'bg-red-100 text-red-800 dark:bg-red-900/60 dark:text-red-300' }}">
                            {{ $pts1->min_time_req_fulfilled ? 'Yes' : 'No' }}
                        </span>
                    </div>

                    @if(!$pts1->min_time_req_fulfilled)
                        <div class="pt-3 border-t border-gray-200 dark:border-gray-600 space-y-2">
                            <div class="flex items-center justify-between gap-2 sm:gap-4">
                                <span class="font-semibold text-gray-900 dark:text-white text-sm">
                                    Special approval taken for minimum time relaxation?
                                </span>
                                <span class="px-3 py-1 text-xs font-bold rounded-full whitespace-nowrap shrink-0 {{ $pts1->special_approval_min_time ? 'bg-emerald-100 text-emerald-800' : 'bg-red-100 text-red-800' }}">
                                    {{ $pts1->special_approval_min_time ? 'Yes' : 'No' }}
                                </span>
                            </div>
                            @if($pts1->getEffectiveMinTimeApprovalPath())
                                <div class="pt-1">
                                    <a href="{{ route('pts.document.serve', ['pts1', $pts1->id, 'main_supervisor_min_time_approval_doc_path']) }}" target="_blank" class="inline-flex items-center text-xs font-bold text-blue-600 dark:text-blue-400 hover:underline">
                                        📄 View Special Minimum Time Approval Copy
                                    </a>
                                </div>
                            @endif
                        </div>
                    @endif
                </div>

                <!-- Publication Norm -->
                <div class="p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg space-y-3">
                    <div class="flex items-center justify-between gap-2 sm:gap-4">
                        <span class="font-semibold text-gray-900 dark:text-white text-sm">
                            Fulfilling Institute publication norm for open seminar?
                        </span>
                        <span class="px-3 py-1 text-xs font-bold rounded-full whitespace-nowrap shrink-0 {{ $pts1->publication_norm_fulfillment ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/60 dark:text-emerald-300' : 'bg-red-100 text-red-800 dark:bg-red-900/60 dark:text-red-300' }}">
                            {{ $pts1->publication_norm_fulfillment ? 'Yes' : 'No' }}
                        </span>
                    </div>

                    @if(!$pts1->publication_norm_fulfillment)
                        <div class="pt-3 border-t border-gray-200 dark:border-gray-600 space-y-2">
                            <div class="flex items-center justify-between gap-2 sm:gap-4">
                                <span class="font-semibold text-gray-900 dark:text-white text-sm">
                                    Special approval taken for publication norm relaxation?
                                </span>
                                <span class="px-3 py-1 text-xs font-bold rounded-full whitespace-nowrap shrink-0 {{ $pts1->special_approval_publication ? 'bg-emerald-100 text-emerald-800' : 'bg-red-100 text-red-800' }}">
                                    {{ $pts1->special_approval_publication ? 'Yes' : 'No' }}
                                </span>
                            </div>
                            @if($pts1->getEffectivePublicationApprovalPath())
                                <div class="pt-1">
                                    <a href="{{ route('pts.document.serve', ['pts1', $pts1->id, 'main_supervisor_publication_approval_doc_path']) }}" target="_blank" class="inline-flex items-center text-xs font-bold text-blue-600 dark:text-blue-400 hover:underline">
                                        📄 View Special Publication Approval Copy
                                    </a>
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
                        <div class="text-xs font-bold text-gray-500 uppercase">Draft Synopsis Report</div>
                        @if($pts1->getEffectiveDraftSynopsisPath())
                            <a href="{{ route('pts.document.serve', ['pts1', $pts1->id, 'main_supervisor_draft_synopsis_report_doc_path']) }}" target="_blank" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-lg shadow transition inline-flex items-center">
                                📄 View Draft Synopsis Report
                            </a>
                        @else
                            <span class="text-xs font-bold text-gray-400 italic">No File Uploaded</span>
                        @endif
                    </div>

                    <!-- Publication List Card -->
                    <div class="p-4 bg-gray-50 dark:bg-gray-700/50 rounded-xl border border-gray-200 dark:border-gray-600 space-y-2">
                        <div class="text-xs font-bold text-gray-500 uppercase">Publication and Other Recognition List</div>
                        @if($pts1->getEffectivePublicationListPath())
                            <a href="{{ route('pts.document.serve', ['pts1', $pts1->id, 'main_supervisor_publication_list_doc_path']) }}" target="_blank" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-lg shadow transition inline-flex items-center">
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

            <!-- Section 6: Authority Recommendations & Remarks -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 space-y-6">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white border-b border-gray-100 dark:border-gray-700 pb-2">
                    6. Authority Recommendations & Remarks
                </h3>

                @php
                    $hasCoSigs = false;
                    for ($i = 1; $i <= 10; $i++) {
                        $recCol = "co_supervisor_{$i}_recommendation";
                        if (!is_null($pts1->$recCol)) {
                            $hasCoSigs = true;
                            break;
                        }
                    }
                    $hasPspcSigs = false;
                    for ($i = 1; $i <= 10; $i++) {
                        $recCol = "pspc_member_{$i}_recommendation";
                        if (!is_null($pts1->$recCol)) {
                            $hasPspcSigs = true;
                            break;
                        }
                    }
                    $hasAnyComment = !is_null($pts1->main_supervisor_recommendation) 
                        || $hasCoSigs 
                        || $hasPspcSigs 
                        || !is_null($pts1->dpgc_recommendation) 
                        || !is_null($pts1->hod_recommendation) 
                        || !is_null($pts1->section_officer_recommendation) 
                        || !is_null($pts1->doaa_recommendation);
                @endphp

                @if($hasAnyComment)
                    <div class="space-y-4">
                        <!-- Main Supervisor Evaluation -->
                        @if(!is_null($pts1->main_supervisor_recommendation))
                            <div class="p-4 bg-indigo-50/70 dark:bg-indigo-950/40 border-l-4 border-indigo-500 rounded-xl space-y-2">
                                <div class="flex items-center justify-between text-s gap-2 sm:gap-4">
                                    <span class="font-bold text-indigo-900 dark:text-indigo-200">
                                        Main Supervisor @if(isset($mainSupervisor))<span class="block sm:inline text-xs font-normal text-indigo-700 dark:text-indigo-300 mt-0.5 sm:mt-0">({{ $mainSupervisor->name }})</span>@endif
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
                                <div class="text-xs text-gray-700 dark:text-gray-300">
                                    <strong>Additional Comment:</strong>
                                    @if($pts1->main_supervisor_student_comment)
                                        <p class="italic bg-white dark:bg-gray-800 p-2.5 rounded-lg border border-indigo-100 dark:border-indigo-900 mt-1">
                                            {{ $pts1->main_supervisor_student_comment }}
                                        </p>
                                    @else
                                        <div class="flex items-center space-x-2 text-xs text-gray-600 dark:text-gray-500 bg-gray-200/60 dark:bg-gray-900/40 p-2.5 rounded-lg border border-dashed border-gray-200 dark:border-gray-700/60 mt-1">
                                            <svg class="w-4 h-4 text-gray-400 dark:text-gray-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 4.418 9 8z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3l18 18"></path>
                                            </svg>
                                            <span class="italic font-normal">Comment not provided</span>
                                        </div>
                                    @endif
                                </div>

                                <div class="text-xs text-gray-700 dark:text-gray-300 pt-1">
                                    <strong>Main Supervisor Remark:</strong>
                                    @if($pts1->main_supervisor_confidential_remark)
                                        <p class="italic bg-white dark:bg-gray-800 p-2.5 rounded-lg border border-indigo-100 dark:border-indigo-900 mt-1">
                                            {{ $pts1->main_supervisor_confidential_remark }}
                                        </p>
                                    @else
                                        <div class="flex items-center space-x-2 text-xs text-gray-600 dark:text-gray-500 bg-gray-200/60 dark:bg-gray-900/40 p-2.5 rounded-lg border border-dashed border-gray-200 dark:border-gray-700/60 mt-1">
                                            <svg class="w-4 h-4 text-gray-400 dark:text-gray-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 4.418 9 8z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3l18 18"></path>
                                            </svg>
                                            <span class="italic font-normal">Remark not provided</span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endif

                        <!-- Co-Supervisors Endorsements -->
                        @if($hasCoSigs)
                            <div class="p-4 bg-blue-50/70 dark:bg-blue-950/40 border-l-4 border-blue-500 rounded-xl space-y-3">
                                <h5 class="text-s font-bold text-blue-900 dark:text-blue-200">Co-Supervisor</h5>
                                @for($i = 1; $i <= 10; $i++)
                                    @php
                                        $idCol = "co_supervisor_{$i}_id";
                                        $recCol = "co_supervisor_{$i}_recommendation";
                                        $remCol = "co_supervisor_{$i}_confidential_remark";
                                        $coUser = $pts1->$idCol ? \App\Models\User::find($pts1->$idCol) : null;
                                    @endphp
                                    @if($coUser && !is_null($pts1->$recCol))
                                        <div class="text-xs space-y-1 pt-1 {{ $i > 1 ? 'border-t border-blue-100 dark:border-blue-900' : '' }}">
                                            <div class="flex items-center justify-between font-semibold gap-2 sm:gap-4">
                                                <span>{{ $coUser->name }} <span class="block sm:inline text-xs font-normal text-gray-500 dark:text-gray-400 mt-0.5 sm:mt-0">(Co-Supervisor {{ $i }})</span> Remark:</span>
                                                @if(!is_null($pts1->$recCol))
                                                    <span class="font-bold whitespace-nowrap shrink-0 {{ $pts1->$recCol ? 'text-emerald-600' : 'text-red-600' }}">
                                                        {{ $pts1->$recCol ? '✓ Recommended' : '❌ Not Recommended' }}
                                                    </span>
                                                @endif
                                            </div>
                                            @if($pts1->$remCol)
                                                <p class="italic text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 p-2.5 rounded-lg border border-blue-100 dark:border-blue-900 mt-1">
                                                    {{ $pts1->$remCol }}
                                                </p>
                                            @else
                                                <div class="flex items-center space-x-2 text-xs text-gray-600 dark:text-gray-500 bg-gray-200/60 dark:bg-gray-900/40 p-2.5 rounded-lg border border-dashed border-gray-200 dark:border-gray-700/60 mt-1">
                                                    <svg class="w-4 h-4 text-gray-400 dark:text-gray-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 4.418 9 8z"></path>
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3l18 18"></path>
                                                    </svg>
                                                    <span class="italic font-normal">Remark not provided</span>
                                                </div>
                                            @endif
                                        </div>
                                    @endif
                                @endfor
                            </div>
                        @endif

                        <!-- PSPC Committee Endorsements -->
                        @if($hasPspcSigs)
                            <div class="p-4 bg-purple-50/70 dark:bg-purple-950/40 border-l-4 border-purple-500 rounded-xl space-y-3">
                                <h5 class="text-s font-bold text-purple-900 dark:text-purple-200">PSPC Committee</h5>
                                @for($i = 1; $i <= 10; $i++)
                                    @php
                                        $idCol = "pspc_member_{$i}_id";
                                        $recCol = "pspc_member_{$i}_recommendation";
                                        $remCol = "pspc_member_{$i}_confidential_remark";
                                        $pspcUser = $pts1->$idCol ? \App\Models\User::find($pts1->$idCol) : null;
                                    @endphp
                                    @if($pspcUser && (!is_null($pts1->$recCol)))
                                        <div class="text-xs space-y-1 pt-1 {{ $i > 1 ? 'border-t border-purple-100 dark:border-purple-900' : '' }}">
                                            <div class="flex items-center justify-between font-semibold gap-2 sm:gap-4">
                                                <span>{{ $pspcUser->name }} <span class="block sm:inline text-xs font-normal text-gray-500 dark:text-gray-400 mt-0.5 sm:mt-0">(PSPC Member {{ $i }})</span> Remark:</span>
                                                @if(!is_null($pts1->$recCol))
                                                    <span class="font-bold whitespace-nowrap shrink-0 {{ $pts1->$recCol ? 'text-emerald-600' : 'text-red-600' }}">
                                                        {{ $pts1->$recCol ? '✓ Recommended' : '❌ Not Recommended' }}
                                                    </span>
                                                @endif
                                            </div>
                                            @if($pts1->$remCol)
                                                <p class="italic text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 p-2.5 rounded-lg border border-purple-100 dark:border-purple-900 mt-1">
                                                    {{ $pts1->$remCol }}
                                                </p>
                                            @else
                                                <div class="flex items-center space-x-2 text-xs text-gray-600 dark:text-gray-500 bg-gray-200/60 dark:bg-gray-900/40 p-2.5 rounded-lg border border-dashed border-gray-200 dark:border-gray-700/60 mt-1">
                                                    <svg class="w-4 h-4 text-gray-400 dark:text-gray-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 4.418 9 8z"></path>
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3l18 18"></path>
                                                    </svg>
                                                    <span class="italic font-normal">Remark not provided</span>
                                                </div>
                                            @endif
                                        </div>
                                    @endif
                                @endfor
                            </div>
                        @endif

                        <!-- DPGC Endorsement -->
                        @if(!is_null($pts1->dpgc_recommendation))
                            <div class="p-4 bg-teal-50/70 dark:bg-teal-950/40 border-l-4 border-teal-500 rounded-xl space-y-2">
                                <div class="flex items-center justify-between font-bold text-teal-900 dark:text-teal-200 gap-2 sm:gap-4">
                                    <h5 class="text-s">Department Postgraduate Committee (DPGC)</h5>
                                    @if(!is_null($pts1->dpgc_recommendation))
                                        <span class="font-bold text-xs whitespace-nowrap shrink-0 {{ $pts1->dpgc_recommendation ? 'text-emerald-600' : 'text-red-600' }}">
                                            {{ $pts1->dpgc_recommendation ? '✓ Recommended' : '❌ Not Recommended' }}
                                        </span>
                                    @endif
                                </div>
                                <div class="text-xs text-gray-700 dark:text-gray-300">
                                    <strong>Student Comment:</strong>
                                    @if($pts1->dpgc_student_comment)
                                        <p class="italic bg-white dark:bg-gray-800 p-2.5 rounded-lg border border-teal-100 dark:border-teal-900 mt-0.5">
                                            {{ $pts1->dpgc_student_comment }}
                                        </p>
                                    @else
                                        <div class="flex items-center space-x-2 text-xs text-gray-600 dark:text-gray-500 bg-gray-200/60 dark:bg-gray-900/40 p-2.5 rounded-lg border border-dashed border-gray-200 dark:border-gray-700/60 mt-0.5">
                                            <svg class="w-4 h-4 text-gray-400 dark:text-gray-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 4.418 9 8z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3l18 18"></path>
                                            </svg>
                                            <span class="italic font-normal">Comment not provided</span>
                                        </div>
                                    @endif
                                </div>
                                <div class="text-xs text-gray-700 dark:text-gray-300 pt-0.5">
                                    <strong>DPGC Remark:</strong>
                                    @if($pts1->dpgc_remarks || $pts1->dpgc_confidential_remark)
                                        <p class="italic bg-white dark:bg-gray-800 p-2.5 rounded-lg border border-teal-100 dark:border-teal-900 mt-0.5">
                                            {{ $pts1->dpgc_remarks ?? $pts1->dpgc_confidential_remark }}
                                        </p>
                                    @else
                                        <div class="flex items-center space-x-2 text-xs text-gray-600 dark:text-gray-500 bg-gray-200/60 dark:bg-gray-900/40 p-2.5 rounded-lg border border-dashed border-gray-200 dark:border-gray-700/60 mt-0.5">
                                            <svg class="w-4 h-4 text-gray-400 dark:text-gray-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 4.418 9 8z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3l18 18"></path>
                                            </svg>
                                            <span class="italic font-normal">Remark not provided</span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endif

                        <!-- HOD Endorsement -->
                        @if(!is_null($pts1->hod_recommendation))
                            <div class="p-4 bg-amber-50/70 dark:bg-amber-950/40 border-l-4 border-amber-500 rounded-xl space-y-2">
                                <div class="flex justify-between font-bold text-amber-900 dark:text-amber-200">
                                    <h5 class="text-s">Head of Department (HOD)</h5>
                                    @if(!is_null($pts1->hod_recommendation))
                                        <span class="font-bold text-xs {{ $pts1->hod_recommendation ? 'text-emerald-600' : 'text-red-600' }}">
                                            {{ $pts1->hod_recommendation ? '✓ Recommended' : '❌ Not Recommended' }}
                                        </span>
                                    @endif
                                </div>
                                <div class="text-xs text-gray-700 dark:text-gray-300">
                                    <strong>Student Comment:</strong>
                                    @if($pts1->hod_student_comment)
                                        <p class="italic bg-white dark:bg-gray-800 p-2.5 rounded-lg border border-amber-100 dark:border-amber-900 mt-0.5">
                                            {{ $pts1->hod_student_comment }}
                                        </p>
                                    @else
                                        <div class="flex items-center space-x-2 text-xs text-gray-600 dark:text-gray-500 bg-gray-200/60 dark:bg-gray-900/40 p-2.5 rounded-lg border border-dashed border-gray-200 dark:border-gray-700/60 mt-0.5">
                                            <svg class="w-4 h-4 text-gray-400 dark:text-gray-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 4.418 9 8z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3l18 18"></path>
                                            </svg>
                                            <span class="italic font-normal">Comment not provided</span>
                                        </div>
                                    @endif
                                </div>
                                <div class="text-xs text-gray-700 dark:text-gray-300 pt-0.5">
                                    <strong>HOD Remark:</strong>
                                    @if($pts1->hod_remarks || $pts1->hod_confidential_remark)
                                        <p class="italic bg-white dark:bg-gray-800 p-2.5 rounded-lg border border-amber-100 dark:border-amber-900 mt-0.5">
                                            {{ $pts1->hod_remarks ?? $pts1->hod_confidential_remark }}
                                        </p>
                                    @else
                                        <div class="flex items-center space-x-2 text-xs text-gray-600 dark:text-gray-500 bg-gray-200/60 dark:bg-gray-900/40 p-2.5 rounded-lg border border-dashed border-gray-200 dark:border-gray-700/60 mt-0.5">
                                            <svg class="w-4 h-4 text-gray-400 dark:text-gray-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 4.418 9 8z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3l18 18"></path>
                                            </svg>
                                            <span class="italic font-normal">Remark not provided</span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endif

                        <!-- Section Officer Remarks -->
                        @if(!is_null($pts1->section_officer_recommendation))
                            <div class="p-4 bg-blue-50/70 dark:bg-blue-950/40 border-l-4 border-blue-500 rounded-xl space-y-2">
                                <div class="flex justify-between text-s font-bold text-blue-900 dark:text-blue-200">
                                    <h5 class="text-s">Section Officer</h5>
                                    <span class="font-bold text-xs text-emerald-600">
                                        ✓ Verified & Forwarded
                                    </span>
                                </div>
                                <div class="text-xs text-gray-700 dark:text-gray-300">
                                    <strong>Verification Remark:</strong>
                                    @if($pts1->section_officer_remarks)
                                        <p class="italic bg-white dark:bg-gray-800 p-2.5 rounded-lg border border-blue-100 dark:border-blue-900 mt-1">
                                            {{ $pts1->section_officer_remarks }}
                                        </p>
                                    @else
                                        <div class="flex items-center space-x-2 text-xs text-gray-600 dark:text-gray-500 bg-gray-200/60 dark:bg-gray-900/40 p-2.5 rounded-lg border border-dashed border-gray-200 dark:border-gray-700/60 mt-1">
                                            <svg class="w-4 h-4 text-gray-400 dark:text-gray-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 4.418 9 8z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3l18 18"></path>
                                            </svg>
                                            <span class="italic font-normal">Verification Remark not provided</span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endif

                        <!-- DOAA Approval -->
                        @if(!is_null($pts1->doaa_recommendation))
                            <div class="p-4 bg-emerald-50/70 dark:bg-emerald-950/40 border-l-4 border-emerald-500 rounded-xl space-y-2">
                                <div class="flex justify-between text-s font-bold text-emerald-900 dark:text-emerald-200">
                                    <h5 class="text-s">Dean of Academic Affairs (DOAA)</h5>
                                    @if(!is_null($pts1->doaa_recommendation))
                                        <span class="font-bold text-xs {{ $pts1->doaa_recommendation ? 'text-emerald-600' : 'text-red-600' }}">
                                            {{ $pts1->doaa_recommendation ? '✓ Approved' : '❌ Not Approved' }}
                                        </span>
                                    @endif
                                </div>
                                <div class="text-xs text-gray-700 dark:text-gray-300">
                                    <strong>Student Comment:</strong>
                                    @if($pts1->doaa_student_comment)
                                        <p class="italic bg-white dark:bg-gray-800 p-2.5 rounded-lg border border-emerald-100 dark:border-emerald-900 mt-0.5">
                                            {{ $pts1->doaa_student_comment }}
                                        </p>
                                    @else
                                        <div class="flex items-center space-x-2 text-xs text-gray-600 dark:text-gray-500 bg-gray-200/60 dark:bg-gray-900/40 p-2.5 rounded-lg border border-dashed border-gray-200 dark:border-gray-700/60 mt-0.5">
                                            <svg class="w-4 h-4 text-gray-400 dark:text-gray-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 4.418 9 8z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3l18 18"></path>
                                            </svg>
                                            <span class="italic font-normal">Comment not provided</span>
                                        </div>
                                    @endif
                                </div>
                                <div class="text-xs text-gray-700 dark:text-gray-300 pt-0.5">
                                    <strong>DOAA Remark:</strong>
                                    @if($pts1->doaa_remarks)
                                        <p class="italic bg-white dark:bg-gray-800 p-2.5 rounded-lg border border-emerald-100 dark:border-emerald-900 mt-0.5">
                                            {{ $pts1->doaa_remarks }}
                                        </p>
                                    @else
                                        <div class="flex items-center space-x-2 text-xs text-gray-600 dark:text-gray-500 bg-gray-200/60 dark:bg-gray-900/40 p-2.5 rounded-lg border border-dashed border-gray-200 dark:border-gray-700/60 mt-0.5">
                                            <svg class="w-4 h-4 text-gray-400 dark:text-gray-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 4.418 9 8z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3l18 18"></path>
                                            </svg>
                                            <span class="italic font-normal">Remark not provided</span>
                                        </div>
                                    @endif
                                </div>
                            </div>
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
                            <p class="italic bg-white dark:bg-gray-800 p-3 rounded border border-amber-200 dark:border-amber-900 mt-1">
                                {{ $pts1->getReversionComment() }}
                            </p>
                        </div>
                    @else
                        <p class="text-xs italic text-gray-500 mt-1">No written reversion comment provided.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Load SheetJS for Client-Side Multi-Sheet Excel Parsing -->
    <script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>

    @php
        $existingPubListUrl = $pts1->getEffectivePublicationListPath() 
            ? route('pts.document.serve', ['pts1', $pts1->id, 'main_supervisor_publication_list_doc_path']) 
            : ($pts1->publication_list_doc_path ? route('pts.document.serve', ['pts1', $pts1->id, 'publication_list_doc_path']) : null);
    @endphp

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const pubListUrl = @json($existingPubListUrl);
            if (!pubListUrl) return;

            fetch(pubListUrl)
                .then(res => {
                    if (!res.ok) throw new Error('Failed to load publication list');
                    return res.arrayBuffer();
                })
                .then(ab => {
                    const data = new Uint8Array(ab);
                    const workbook = XLSX.read(data, { type: 'array', cellDates: true });
                    renderWorkbook(workbook, 'Publication and Other Recognition Preview');
                })
                .catch(err => {
                    console.error('Error previewing publication list:', err);
                });

            function renderWorkbook(workbook, titlePrefix = 'Publication and Other Recognition Preview') {
                const container = document.getElementById('excelPreviewContainer');
                const sheetsOutput = document.getElementById('sheetsOutput');
                const sheetTabsBar = document.getElementById('sheetTabsBar');
                const sheetCountSpan = document.getElementById('excelSheetCount');
                const titleSpan = document.getElementById('excelPreviewTitle');

                if (!container || !sheetsOutput) return;

                sheetsOutput.innerHTML = '';
                sheetTabsBar.innerHTML = '';
                container.classList.remove('hidden');

                if (titleSpan) titleSpan.innerText = titlePrefix;

                const sheetNames = workbook.SheetNames;
                if (sheetCountSpan) sheetCountSpan.innerText = `${sheetNames.length} Sheet(s) Found`;

                sheetNames.forEach((sheetName, index) => {
                    const worksheet = workbook.Sheets[sheetName];
                    if (!worksheet) return;

                    const htmlString = XLSX.utils.sheet_to_html(worksheet, { id: 'sheet-table-' + index, editable: false });

                    const tabBtn = document.createElement('a');
                    tabBtn.href = `#sheet-block-${index}`;
                    tabBtn.className = 'px-3 py-1.5 text-xs font-bold rounded-lg border border-indigo-200 dark:border-indigo-800 bg-indigo-50 dark:bg-indigo-900/40 text-indigo-700 dark:text-indigo-300 hover:bg-indigo-100 transition flex items-center';
                    tabBtn.innerHTML = `<span class="w-2 h-2 rounded-full bg-emerald-500 mr-1.5"></span> ${sheetName}`;
                    sheetTabsBar.appendChild(tabBtn);

                    const sheetBlock = document.createElement('div');
                    sheetBlock.id = `sheet-block-${index}`;
                    sheetBlock.className = 'bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl p-5 shadow-sm space-y-3';
                    sheetBlock.innerHTML = `
                        <div class="flex items-center justify-between border-b border-gray-100 dark:border-gray-800 pb-2">
                            <h5 class="font-bold text-sm text-indigo-800 dark:text-indigo-300 uppercase tracking-wider flex items-center">
                                <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 mr-2"></span>
                                Sheet (${index + 1}/${sheetNames.length}): ${sheetName}
                            </h5>
                        </div>
                        <div class="overflow-x-auto sheet-table-container">
                            ${htmlString}
                        </div>
                    `;
                    sheetsOutput.appendChild(sheetBlock);
                });
            }
        });
    </script>
</x-app-layout>

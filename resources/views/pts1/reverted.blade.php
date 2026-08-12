<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div class="flex items-center space-x-3">
                <h2 class="font-bold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                    {{ __('PTS-1 Reverted Submission View') }}
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
        .sheet-table-container tr:first-child, .sheet-table-container td[id*="s2id"] {
            background-color: #f3f4f6;
            font-weight: 700;
            color: #1f2937;
        }
        .sheet-table-container tr:nth-child(even) {
            background-color: #f9fafb;
        }
        .dark .sheet-table-container th, .dark .sheet-table-container td {
            border-color: #374151;
        }
        .dark .sheet-table-container tr:first-child {
            background-color: #374151;
            color: #f9fafb;
        }
        .dark .sheet-table-container tr:nth-child(even) {
            background-color: #1f2937;
        }
    </style>

    <div class="py-8">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Reversion Comment Box -->
            <div class="p-4 bg-amber-50 dark:bg-amber-950/40 border-l-4 border-amber-500 rounded-xl">
                <div class="font-bold text-amber-900 dark:text-amber-200 text-sm">
                    ⚠️ Reverted by {{ $pts1->getRevertedByRoleLabel() }}
                </div>
                @if($pts1->getReversionComment())
                    <p class="italic text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 p-3 rounded border border-amber-200 dark:border-amber-900 mt-2 text-xs">
                        "{{ $pts1->getReversionComment() }}"
                    </p>
                @endif
            </div>

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
                    <input type="text" value="{{ $pts1->thesis->title ?? '' }}" readonly class="w-full bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200 rounded-lg border-gray-300 dark:border-gray-600 cursor-not-allowed">
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
                        <input type="text" value="{{ $pts1->seminar_date ? $pts1->seminar_date->format('d-m-Y') : 'N/A' }}" readonly class="w-full bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200 rounded-lg border-gray-300 dark:border-gray-600 cursor-not-allowed">
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

                <!-- Publication Norm -->
                <div class="p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="font-semibold text-gray-900 dark:text-white text-sm">
                            Fulfilling Institute publication norm for open seminar?
                        </span>
                        <span class="px-3 py-1 text-xs font-bold rounded-full {{ $pts1->publication_norm_fulfillment ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/60 dark:text-emerald-300' : 'bg-red-100 text-red-800 dark:bg-red-900/60 dark:text-red-300' }}">
                            {{ $pts1->publication_norm_fulfillment ? 'Yes' : 'No' }}
                        </span>
                    </div>

                    @if(!$pts1->publication_norm_fulfillment)
                        <div class="pt-3 border-t border-gray-200 dark:border-gray-600 space-y-2">
                            <div class="flex items-center justify-between">
                                <span class="font-semibold text-gray-900 dark:text-white text-sm">
                                    Special approval taken for publication norm relaxation?
                                </span>
                                <span class="px-3 py-1 text-xs font-bold rounded-full {{ $pts1->special_approval_publication ? 'bg-emerald-100 text-emerald-800' : 'bg-red-100 text-red-800' }}">
                                    {{ $pts1->special_approval_publication ? 'Yes' : 'No' }}
                                </span>
                            </div>
                            @if($pts1->publication_approval_doc_path)
                                <div class="pt-1">
                                    <a href="{{ route('pts.document.serve', ['pts1', $pts1->id, 'publication_approval_doc_path']) }}" target="_blank" class="inline-flex items-center text-xs font-bold text-blue-600 dark:text-blue-400 hover:underline">
                                        📄 View Special Publication Approval Copy
                                    </a>
                                </div>
                            @endif
                        </div>
                    @endif
                </div>

                <!-- Minimum Time Requirement -->
                <div class="p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="font-semibold text-gray-900 dark:text-white text-sm">
                            Fulfilling minimum time requirement criteria for thesis submission?
                        </span>
                        <span class="px-3 py-1 text-xs font-bold rounded-full {{ $pts1->min_time_req_fulfilled ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/60 dark:text-emerald-300' : 'bg-red-100 text-red-800 dark:bg-red-900/60 dark:text-red-300' }}">
                            {{ $pts1->min_time_req_fulfilled ? 'Yes' : 'No' }}
                        </span>
                    </div>

                    @if(!$pts1->min_time_req_fulfilled)
                        <div class="pt-3 border-t border-gray-200 dark:border-gray-600 space-y-2">
                            <div class="flex items-center justify-between">
                                <span class="font-semibold text-gray-900 dark:text-white text-sm">
                                    Special approval taken for minimum time relaxation?
                                </span>
                                <span class="px-3 py-1 text-xs font-bold rounded-full {{ $pts1->special_approval_min_time ? 'bg-emerald-100 text-emerald-800' : 'bg-red-100 text-red-800' }}">
                                    {{ $pts1->special_approval_min_time ? 'Yes' : 'No' }}
                                </span>
                            </div>
                            @if($pts1->min_time_approval_doc_path)
                                <div class="pt-1">
                                    <a href="{{ route('pts.document.serve', ['pts1', $pts1->id, 'min_time_approval_doc_path']) }}" target="_blank" class="inline-flex items-center text-xs font-bold text-blue-600 dark:text-blue-400 hover:underline">
                                        📄 View Special Minimum Time Approval Copy
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
                        @if($pts1->draft_synopsis_report_doc_path)
                            <a href="{{ route('pts.document.serve', ['pts1', $pts1->id, 'draft_synopsis_report_doc_path']) }}" target="_blank" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-lg shadow transition inline-flex items-center">
                                📄 View Draft Synopsis Report
                            </a>
                        @else
                            <span class="text-xs font-bold text-gray-400 italic">No File Uploaded</span>
                        @endif
                    </div>

                    <!-- Publication List Card -->
                    <div class="p-4 bg-gray-50 dark:bg-gray-700/50 rounded-xl border border-gray-200 dark:border-gray-600 space-y-2">
                        <div class="text-xs font-bold text-gray-500 uppercase">Publication List</div>
                        @if($pts1->publication_list_doc_path)
                            <a href="{{ route('pts.document.serve', ['pts1', $pts1->id, 'publication_list_doc_path']) }}" target="_blank" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-lg shadow transition inline-flex items-center">
                                📊 Download / View Publication List
                            </a>
                        @else
                            <span class="text-xs font-bold text-gray-400 italic">No File Uploaded</span>
                        @endif
                    </div>
                </div>
            </div>

            @if($pts1->publication_list_doc_path)
                <!-- SheetJS Excel Preview Container -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 space-y-6" x-data="excelPreview()">
                    <div class="flex items-center justify-between border-b border-gray-100 dark:border-gray-700 pb-4">
                        <h4 class="text-md font-bold text-gray-900 dark:text-white flex items-center">
                            <svg class="w-5 h-5 mr-2 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                            Publication and Other Recognition Preview
                        </h4>
                        <span class="text-xs font-bold bg-indigo-100 dark:bg-indigo-900 text-indigo-800 dark:text-indigo-300 px-3 py-1 rounded-full" x-show="sheetCount" x-text="sheetCount"></span>
                    </div>

                    <!-- Loading Spinner -->
                    <div x-show="loading" class="flex justify-center items-center py-8 space-x-2">
                        <div class="animate-spin rounded-full h-6 w-6 border-b-2 border-indigo-600"></div>
                        <span class="text-xs text-gray-500 font-semibold">Loading publication preview...</span>
                    </div>

                    <!-- Error Alert -->
                    <div x-show="error" class="p-4 bg-red-50 dark:bg-red-900/30 text-red-700 dark:text-red-300 rounded-lg text-xs" x-text="error"></div>

                    <!-- Sheet Tabs Navigation Bar -->
                    <div id="sheetTabsBar" class="flex flex-wrap gap-2 pb-2" x-show="!loading && !error"></div>

                    <!-- All Sheet Content Containers -->
                    <div id="sheetsOutput" class="space-y-8" x-show="!loading && !error"></div>
                </div>

                <script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>
                <script>
                    function excelPreview() {
                        return {
                            loading: true,
                            error: null,
                            sheetCount: '',
                            init() {
                                fetch("{{ route('pts.document.serve', ['pts1', $pts1->id, 'publication_list_doc_path']) }}")
                                    .then(res => {
                                        if (!res.ok) throw new Error('Failed to load excel document.');
                                        return res.arrayBuffer();
                                    })
                                    .then(data => {
                                        const workbook = XLSX.read(new Uint8Array(data), { type: 'array', cellDates: true });
                                        const sheetsOutput = document.getElementById('sheetsOutput');
                                        const sheetTabsBar = document.getElementById('sheetTabsBar');

                                        sheetsOutput.innerHTML = '';
                                        sheetTabsBar.innerHTML = '';

                                        const sheetNames = workbook.SheetNames;
                                        this.sheetCount = `${sheetNames.length} Sheet(s) Found`;

                                        sheetNames.forEach((sheetName, index) => {
                                            const worksheet = workbook.Sheets[sheetName];
                                            if (!worksheet) return;

                                            // Convert to html
                                            const htmlString = XLSX.utils.sheet_to_html(worksheet, { id: 'sheet-table-' + index, editable: false });

                                            // Create Tab Button
                                            const tabBtn = document.createElement('a');
                                            tabBtn.href = `#sheet-block-${index}`;
                                            tabBtn.className = 'px-3 py-1.5 text-xs font-bold rounded-lg border border-indigo-200 dark:border-indigo-800 bg-indigo-50 dark:bg-indigo-900/40 text-indigo-700 dark:text-indigo-300 hover:bg-indigo-100 transition flex items-center';
                                            tabBtn.innerHTML = `<span class="w-2 h-2 rounded-full bg-emerald-500 mr-1.5"></span> ${sheetName}`;
                                            sheetTabsBar.appendChild(tabBtn);

                                            // Create Sheet block
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

                                        this.loading = false;
                                    })
                                    .catch(err => {
                                        this.error = err.message;
                                        this.loading = false;
                                    });
                            }
                        }
                    }
                </script>
            @endif

            <!-- Section 6: Authority Comments -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 space-y-6">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white border-b border-gray-100 dark:border-gray-700 pb-2">
                    6. Authority Endorsements & Remarks
                </h3>

                <div class="space-y-4">
                    <!-- Main Supervisor Evaluation -->
                    @if(!is_null($pts1->main_supervisor_recommendation))
                        <div class="p-4 bg-indigo-50/70 dark:bg-indigo-950/40 border-l-4 border-indigo-500 rounded-xl space-y-2">
                            <div class="flex items-center justify-between font-bold text-indigo-900 dark:text-indigo-200">
                                <span>Main Supervisor Evaluation</span>
                                <span class="{{ $pts1->main_supervisor_recommendation ? 'text-emerald-600 dark:text-emerald-400' : 'text-red-600 dark:text-red-400' }}">
                                    {{ $pts1->main_supervisor_recommendation ? '✓ Recommended' : '❌ Not Recommended' }}
                                </span>
                            </div>
                            <div class="text-xs text-gray-700 dark:text-gray-300 space-y-1">
                                <div><strong>Open Seminar Status:</strong> <span class="capitalize font-semibold">{{ $pts1->work_status }}</span></div>
                                <div><strong>Confidential Remarks:</strong></div>
                                @if($pts1->main_supervisor_confidential_remark)
                                    <p class="italic bg-white dark:bg-gray-800 p-2.5 rounded-lg border border-indigo-100 dark:border-indigo-900 mt-1">
                                        {{ $pts1->main_supervisor_confidential_remark }}
                                    </p>
                                @else
                                    <p class="italic text-gray-500 dark:text-gray-400 bg-white dark:bg-gray-800 p-2.5 rounded-lg border border-indigo-100 dark:border-indigo-900 mt-1">
                                        Confidential Remark not Provided
                                    </p>
                                @endif
                                @if($pts1->main_supervisor_student_comment)
                                    <div class="mt-2"><strong>Feedback to Student:</strong></div>
                                    <p class="italic bg-white dark:bg-gray-800 p-2.5 rounded-lg border border-indigo-100 dark:border-indigo-900 mt-1">
                                        {{ $pts1->main_supervisor_student_comment }}
                                    </p>
                                @endif
                            </div>
                        </div>
                    @endif

                    <!-- Co-Supervisors Endorsements -->
                    @php
                        $hasCoSigs = false;
                        for ($i = 1; $i <= 10; $i++) {
                            $recCol = "co_supervisor_{$i}_recommendation";
                            if (!is_null($pts1->$recCol)) {
                                $hasCoSigs = true;
                                break;
                            }
                        }
                    @endphp
                    @if($hasCoSigs)
                        <div class="p-4 bg-blue-50/70 dark:bg-blue-950/40 border-l-4 border-blue-500 rounded-xl space-y-3">
                            <h5 class="text-s font-bold text-blue-900 dark:text-blue-200">Co-Supervisor Recommendations</h5>
                            @for($i = 1; $i <= 10; $i++)
                                @php
                                    $idCol = "co_supervisor_{$i}_id";
                                    $recCol = "co_supervisor_{$i}_recommendation";
                                    $remCol = "co_supervisor_{$i}_confidential_remark";
                                    $coUser = $pts1->$idCol ? \App\Models\User::find($pts1->$idCol) : null;
                                @endphp
                                @if($coUser && !is_null($pts1->$recCol))
                                    <div class="text-xs space-y-1 pt-1 {{ $i > 1 ? 'border-t border-blue-100 dark:border-blue-900' : '' }}">
                                        <div class="flex justify-between font-semibold">
                                            <span>{{ $coUser->name }} (Co-Supervisor {{ $i }})</span>
                                            <span class="font-bold {{ $pts1->$recCol ? 'text-emerald-600' : 'text-red-600' }}">
                                                {{ $pts1->$recCol ? '✓ Recommended' : '❌ Not Recommended' }}
                                            </span>
                                        </div>
                                        <div><strong>Confidential Remarks:</strong></div>
                                        @if($pts1->$remCol)
                                            <p class="italic text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 p-2.5 rounded-lg border border-blue-100 dark:border-blue-900 mt-1">
                                                {{ $pts1->$remCol }}
                                            </p>
                                        @else
                                            <p class="italic text-gray-500 dark:text-gray-400 bg-white dark:bg-gray-800 p-2.5 rounded-lg border border-blue-100 dark:border-blue-900 mt-1">
                                                Confidential Remark not Provided
                                            </p>
                                        @endif
                                    </div>
                                @endif
                            @endfor
                        </div>
                    @endif

                    <!-- PSPC Committee Endorsements -->
                    @php
                        $hasPspcSigs = false;
                        for ($i = 1; $i <= 10; $i++) {
                            $recCol = "pspc_member_{$i}_recommendation";
                            if (!is_null($pts1->$recCol)) {
                                $hasPspcSigs = true;
                                break;
                            }
                        }
                    @endphp
                    @if($hasPspcSigs)
                        <div class="p-4 bg-purple-50/70 dark:bg-purple-950/40 border-l-4 border-purple-500 rounded-xl space-y-3">
                            <h5 class="text-s font-bold text-purple-900 dark:text-purple-200">PSPC Committee Recommendations</h5>
                            @for($i = 1; $i <= 10; $i++)
                                @php
                                    $idCol = "pspc_member_{$i}_id";
                                    $recCol = "pspc_member_{$i}_recommendation";
                                    $remCol = "pspc_member_{$i}_confidential_remark";
                                    $pspcUser = $pts1->$idCol ? \App\Models\User::find($pts1->$idCol) : null;
                                @endphp
                                @if($pspcUser && !is_null($pts1->$recCol))
                                    <div class="text-xs space-y-1 pt-1 {{ $i > 1 ? 'border-t border-purple-100 dark:border-purple-900' : '' }}">
                                        <div class="flex justify-between font-semibold">
                                            <span>{{ $pspcUser->name }} (PSPC Member {{ $i }})</span>
                                            <span class="font-bold {{ $pts1->$recCol ? 'text-emerald-600' : 'text-red-600' }}">
                                                {{ $pts1->$recCol ? '✓ Recommended' : '❌ Not Recommended' }}
                                            </span>
                                        </div>
                                        <div><strong>Confidential Remarks:</strong></div>
                                        @if($pts1->$remCol)
                                            <p class="italic text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 p-2.5 rounded-lg border border-purple-100 dark:border-purple-900 mt-1">
                                                {{ $pts1->$remCol }}
                                            </p>
                                        @else
                                            <p class="italic text-gray-500 dark:text-gray-400 bg-white dark:bg-gray-800 p-2.5 rounded-lg border border-purple-100 dark:border-purple-900 mt-1">
                                                Confidential Remark not Provided
                                            </p>
                                        @endif
                                    </div>
                                @endif
                            @endfor
                        </div>
                    @endif

                    <!-- DPGC Endorsement -->
                    @if(!is_null($pts1->dpgc_recommendation))
                        <div class="p-4 bg-teal-50/70 dark:bg-teal-950/40 border-l-4 border-teal-500 rounded-xl space-y-2">
                            <div class="flex justify-between text-s font-bold text-teal-900 dark:text-teal-200">
                                <span>Department Postgraduate Committee (DPGC)</span>
                                <span class="{{ $pts1->dpgc_recommendation ? 'text-emerald-600 dark:text-emerald-400' : 'text-red-600 dark:text-red-400' }}">
                                    {{ $pts1->dpgc_recommendation ? '✓ Recommended' : '❌ Not Recommended' }}
                                </span>
                            </div>
                            @if($pts1->dpgc_remarks)
                                <div class="text-xs text-gray-700 dark:text-gray-300">
                                    <strong>Remarks:</strong>
                                    <p class="italic bg-white dark:bg-gray-800 p-2.5 rounded-lg border border-teal-100 dark:border-teal-900 mt-1">
                                        {{ $pts1->dpgc_remarks }}
                                    </p>
                                </div>
                            @endif
                            @if($pts1->dpgc_student_comment)
                                <div class="text-xs text-gray-700 dark:text-gray-300">
                                    <strong>Feedback to Student:</strong>
                                    <p class="italic bg-white dark:bg-gray-800 p-2.5 rounded-lg border border-teal-100 dark:border-teal-900 mt-0.5">
                                        {{ $pts1->dpgc_student_comment }}
                                    </p>
                                </div>
                            @endif
                        </div>
                    @endif

                    <!-- HOD Endorsement -->
                    @if(!is_null($pts1->hod_recommendation))
                        <div class="p-4 bg-amber-50/70 dark:bg-amber-950/40 border-l-4 border-amber-500 rounded-xl space-y-2">
                            <div class="flex justify-between text-s font-bold text-amber-900 dark:text-amber-200">
                                <span>Head of Department (HOD)</span>
                                <span class="{{ $pts1->hod_recommendation ? 'text-emerald-600 dark:text-emerald-400' : 'text-red-600 dark:text-red-400' }}">
                                    {{ $pts1->hod_recommendation ? '✓ Recommended' : '❌ Not Recommended' }}
                                </span>
                            </div>
                            @if($pts1->hod_remarks)
                                <div class="text-xs text-gray-700 dark:text-gray-300">
                                    <strong>Remarks:</strong>
                                    <p class="italic bg-white dark:bg-gray-800 p-2.5 rounded-lg border border-amber-100 dark:border-amber-900 mt-1">
                                        {{ $pts1->hod_remarks }}
                                    </p>
                                </div>
                            @endif
                            @if($pts1->hod_student_comment)
                                <div class="text-xs text-gray-700 dark:text-gray-300">
                                    <strong>Feedback to Student:</strong>
                                    <p class="italic bg-white dark:bg-gray-800 p-2.5 rounded-lg border border-amber-100 dark:border-amber-900 mt-0.5">
                                        {{ $pts1->hod_student_comment }}
                                    </p>
                                </div>
                            @endif
                        </div>
                    @endif

                    <!-- Section Officer Remarks -->
                    @if(!is_null($pts1->section_officer_recommendation))
                        <div class="p-4 bg-blue-50/70 dark:bg-blue-950/40 border-l-4 border-blue-500 rounded-xl space-y-2">
                            <div class="flex justify-between text-s font-bold text-blue-900 dark:text-blue-200">
                                <span>Section Officer (Academic)</span>
                                <span class="{{ $pts1->section_officer_recommendation ? 'text-emerald-600 dark:text-emerald-400' : 'text-red-600 dark:text-red-400' }}">
                                    {{ $pts1->section_officer_recommendation ? '✓ Verified' : '❌ Not Verified' }}
                                </span>
                            </div>
                            @if($pts1->section_officer_remarks)
                                <div class="text-xs text-gray-700 dark:text-gray-300">
                                    <strong>Remarks:</strong>
                                    <p class="italic bg-white dark:bg-gray-800 p-2.5 rounded-lg border border-blue-100 dark:border-blue-900 mt-1">
                                        {{ $pts1->section_officer_remarks }}
                                    </p>
                                </div>
                            @endif
                        </div>
                    @endif

                    <!-- DOAA Approval -->
                    @if(!is_null($pts1->doaa_recommendation))
                        <div class="p-4 bg-emerald-50/70 dark:bg-emerald-950/40 border-l-4 border-emerald-500 rounded-xl space-y-2">
                            <div class="flex justify-between text-s font-bold text-emerald-900 dark:text-emerald-200">
                                <span>Dean of Academic Affairs (DOAA)</span>
                                <span class="{{ $pts1->doaa_recommendation ? 'text-emerald-600 dark:text-emerald-400' : 'text-red-600 dark:text-red-400' }}">
                                    {{ $pts1->doaa_recommendation ? '✓ Approved' : '❌ Not Approved' }}
                                </span>
                            </div>
                            @if($pts1->doaa_remarks)
                                <div class="text-xs text-gray-700 dark:text-gray-300">
                                    <strong>Remarks:</strong>
                                    <p class="italic bg-white dark:bg-gray-800 p-2.5 rounded-lg border border-emerald-100 dark:border-emerald-900 mt-1">
                                        {{ $pts1->doaa_remarks }}
                                    </p>
                                </div>
                            @endif
                            @if($pts1->doaa_student_comment)
                                <div class="text-xs text-gray-700 dark:text-gray-300">
                                    <strong>Feedback to Student:</strong>
                                    <p class="italic bg-white dark:bg-gray-800 p-2.5 rounded-lg border border-emerald-100 dark:border-emerald-900 mt-0.5">
                                        {{ $pts1->doaa_student_comment }}
                                    </p>
                                </div>
                            @endif
                        </div>
                    @endif
                </div>
            </div>

            <!-- Section 7: Reversion Log -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 space-y-6">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white border-b border-gray-100 dark:border-gray-700 pb-2">
                    7. Reversion
                </h3>
                <div class="p-4 bg-amber-50 dark:bg-amber-950/40 border-l-4 border-amber-500 rounded-xl">
                    <div class="font-bold text-amber-900 dark:text-amber-200 text-sm">
                        ⚠️ Form Reverted by {{ $pts1->getRevertedByRoleLabel() }}
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
</x-app-layout>

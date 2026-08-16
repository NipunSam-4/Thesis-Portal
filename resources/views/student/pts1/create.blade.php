<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-bold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Submit PTS-1 Form') }}
            </h2>
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

    <div class="py-8" x-data="pts1Form()">
        <div class="max-w-5xl mx-auto px-2 sm:px-6 lg:px-8 space-y-6">

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

            <form action="{{ route('student.pts1.store') }}" method="POST" enctype="multipart/form-data" class="space-y-8">
                @csrf

                @if(isset($pts1Form) && $pts1Form->status === 'reverted')
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
                                <p class="italic bg-white dark:bg-gray-800 p-2.5 rounded-lg border border-amber-200 dark:border-amber-900 mt-1">
                                    {{ $pts1Form->reversion_comment }}
                                </p>
                            </div>
                        @endif
                    </div>
                @endif

                <!-- Section 1: Pre-filled Student Details (Read-Only) -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4 border-b border-gray-100 dark:border-gray-700 pb-2">
                        1. Student Information
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <label class="block text-xs font-semibold uppercase text-gray-500 mb-1">Student Name</label>
                            <input type="text" value="{{ $user->name }}" readonly class="w-full bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200 rounded-lg border-gray-300 dark:border-gray-600 cursor-not-allowed">
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
                            <label class="block text-xs font-semibold uppercase text-gray-500 mb-1">Date of Confirmation <span class="text-red-500">*</span></label>
                            <input type="date" name="date_confirmation" required value="{{ old('date_confirmation', $student->date_confirmation ? \Carbon\Carbon::parse($student->date_confirmation)->format('Y-m-d') : '') }}" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                        </div>
                    </div>
                </div>

                <!-- Section 2: Name of Thesis -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 space-y-3">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white border-b border-gray-100 dark:border-gray-700 pb-2">
                        2. Name of Thesis
                    </h3>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Thesis Title <span class="text-red-500">*</span></label>
                        <input type="text" name="thesis_title" required value="{{ old('thesis_title', $thesis->title) }}" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-blue-500 focus:border-blue-500" placeholder="Enter full title of thesis">
                    </div>
                </div>

                <!-- Section 3: Open Seminar Details -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4 border-b border-gray-100 dark:border-gray-700 pb-2">
                        3. Open Seminar Details
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Date of Open Seminar <span class="text-red-500">*</span></label>
                            <input type="date" name="seminar_date" required min="{{ isset($pts1Form) && $pts1Form->seminar_date ? (\Carbon\Carbon::parse($pts1Form->seminar_date)->lt(\Carbon\Carbon::today()) ? \Carbon\Carbon::parse($pts1Form->seminar_date)->format('Y-m-d') : \Carbon\Carbon::today()->format('Y-m-d')) : \Carbon\Carbon::today()->format('Y-m-d') }}" value="{{ old('seminar_date', isset($pts1Form) && $pts1Form->seminar_date ? \Carbon\Carbon::parse($pts1Form->seminar_date)->format('Y-m-d') : '') }}" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Time of Open Seminar <span class="text-red-500">*</span></label>
                            <input type="time" name="seminar_time" required value="{{ old('seminar_time', isset($pts1Form) ? $pts1Form->seminar_time : '') }}" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Venue of Open Seminar <span class="text-red-500">*</span></label>
                            <input type="text" name="seminar_venue" placeholder="e.g. Seminar Hall 1, CSE Dept" required value="{{ old('seminar_venue', isset($pts1Form) ? $pts1Form->seminar_venue : '') }}" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Online Meeting Link (Optional)</label>
                            <input type="url" name="meeting_link" placeholder="https://meet.google.com/abc-defg-hij" value="{{ old('meeting_link', isset($pts1Form) ? $pts1Form->meeting_link : '') }}" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
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
                        <label class="block font-medium text-gray-900 dark:text-white text-sm">
                            Are you fulfilling the minimum time requirement criteria for thesis submission? <span class="text-red-500">*</span>
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
                            <label class="block font-medium text-gray-900 dark:text-white text-sm">
                                Have you taken special approval for minimum time relaxation? <span class="text-red-500">*</span>
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
                                <div class="flex justify-between items-center mb-1">
                                    <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300">
                                        Upload Special Minimum Time Approval Copy (Max 2 MB) <span class="text-red-500">*</span>
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
                                
                                <div x-show="fileStates.timeApp.name" x-cloak class="p-3 bg-emerald-50 dark:bg-emerald-900/30 border border-emerald-200 dark:border-emerald-800 rounded-xl flex items-center justify-between">
                                    <div class="flex items-center space-x-3 truncate">
                                        <div class="p-2 bg-emerald-100 dark:bg-emerald-800 rounded-lg text-emerald-700 dark:text-emerald-300">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                        </div>
                                        <div class="truncate">
                                            <div class="text-[10px] text-emerald-800 dark:text-emerald-300 font-bold uppercase">Selected Approval File</div>
                                            <div class="text-sm font-semibold text-gray-900 dark:text-white truncate" x-text="fileStates.timeApp.name"></div>
                                            <div class="text-[11px] text-gray-500" x-text="fileStates.timeApp.size"></div>
                                        </div>
                                    </div>
                                    <div class="flex items-center space-x-2 shrink-0">
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
                        <label class="block font-medium text-gray-900 dark:text-white text-sm">
                            Are you fulfilling the Institute publication norm for open seminar? <span class="text-red-500">*</span>
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
                            <label class="block font-medium text-gray-900 dark:text-white text-sm">
                                Have you taken special approval for publication norm relaxation? <span class="text-red-500">*</span>
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
                                <div class="flex justify-between items-center mb-1">
                                    <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300">
                                        Upload Special Publication Approval Copy (Max 2 MB) <span class="text-red-500">*</span>
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

                                <div x-show="fileStates.pubApp.name" x-cloak class="p-3 bg-emerald-50 dark:bg-emerald-900/30 border border-emerald-200 dark:border-emerald-800 rounded-xl flex items-center justify-between">
                                    <div class="flex items-center space-x-3 truncate">
                                        <div class="p-2 bg-emerald-100 dark:bg-emerald-800 rounded-lg text-emerald-700 dark:text-emerald-300">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                        </div>
                                        <div class="truncate">
                                            <div class="text-[10px] text-emerald-800 dark:text-emerald-300 font-bold uppercase">Selected Approval File</div>
                                            <div class="text-sm font-semibold text-gray-900 dark:text-white truncate" x-text="fileStates.pubApp.name"></div>
                                            <div class="text-[11px] text-gray-500" x-text="fileStates.pubApp.size"></div>
                                        </div>
                                    </div>
                                    <div class="flex items-center space-x-2 shrink-0">
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

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Draft Synopsis Upload -->
                        <div>
                            <div class="flex justify-between items-center mb-1">
                                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300">
                                    Upload Draft Synopsis Report (.pdf, .docx) <span class="text-red-500">*</span>
                                </label>
                                <span class="text-[11px] text-gray-400">Max 10 MB</span>
                            </div>

                            <div class="h-8"></div>

                            <div x-show="!fileStates.synopsis.name">
                                <input type="file" id="synopsisInput" name="draft_synopsis_report" accept=".pdf,.docx" :required="!fileStates.synopsis.name" @change="handleFileSelect($event, 'synopsis')" class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                            </div>

                            <!-- File Size Error Alert -->
                            <div x-show="fileErrors.synopsis" x-cloak class="mt-2 text-xs font-bold text-red-600 dark:text-red-400 bg-red-50 dark:bg-red-900/30 p-2.5 rounded-lg border border-red-200 dark:border-red-800">
                                <span x-text="fileErrors.synopsis"></span>
                            </div>

                            <div x-show="fileStates.synopsis.name" x-cloak class="p-3 bg-emerald-50 dark:bg-emerald-900/30 border border-emerald-200 dark:border-emerald-800 rounded-xl flex items-center justify-between">
                                <div class="flex items-center space-x-3 truncate">
                                    <div class="p-2 bg-emerald-100 dark:bg-emerald-800 rounded-lg text-emerald-700 dark:text-emerald-300">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                    </div>
                                    <div class="truncate">
                                        <div class="text-[10px] text-emerald-800 dark:text-emerald-300 font-bold uppercase">Selected Draft Synopsis</div>
                                        <div class="text-sm font-semibold text-gray-900 dark:text-white truncate" x-text="fileStates.synopsis.name"></div>
                                        <div class="text-[11px] text-gray-500" x-text="fileStates.synopsis.size"></div>
                                    </div>
                                </div>
                                <div class="flex items-center space-x-2 shrink-0">
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
                                <label class="text-sm font-semibold text-gray-700 dark:text-gray-300">
                                    Upload Publication and Other Recognition List (.xlsx, .xls) <span class="text-red-500">*</span>
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

                            <div x-show="fileStates.pubList.name" x-cloak class="p-3 bg-emerald-50 dark:bg-emerald-900/30 border border-emerald-200 dark:border-emerald-800 rounded-xl flex items-center justify-between">
                                <div class="flex items-center space-x-3 truncate">
                                    <div class="p-2 bg-emerald-100 dark:bg-emerald-800 rounded-lg text-emerald-700 dark:text-emerald-300">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                    </div>
                                    <div class="truncate">
                                        <div class="text-[10px] text-emerald-800 dark:text-emerald-300 font-bold uppercase">Selected Publication List</div>
                                        <div class="text-sm font-semibold text-gray-900 dark:text-white truncate" x-text="fileStates.pubList.name"></div>
                                        <div class="text-[11px] text-gray-500" x-text="fileStates.pubList.size"></div>
                                    </div>
                                </div>
                                <div class="flex items-center space-x-2 shrink-0">
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
                <div class="flex justify-end pt-4">
                    <button type="submit" :disabled="isBlocked" :class="isBlocked ? 'opacity-50 cursor-not-allowed bg-gray-400' : 'bg-blue-600 hover:bg-blue-700'" class="text-white text-base font-bold px-8 py-3 rounded-xl shadow-lg transition">
                        Submit PTS-1 Form
                    </button>
                </div>
            </form>

        </div>
    </div>

    <!-- Load SheetJS for Client-Side Multi-Sheet Excel Parsing -->
    <script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>

    <script>
        function pts1Form() {
            const existingPts1 = @js(isset($pts1Form) ? $pts1Form : null);
            return {
                pubNorm: @js(old('publication_norm_fulfillment', isset($pts1Form) ? ($pts1Form->publication_norm_fulfillment ? '1' : '0') : '1')),
                pubApproval: @js(old('special_approval_publication', isset($pts1Form) ? ($pts1Form->special_approval_publication ? '1' : '0') : '1')),
                timeNorm: @js(old('min_time_req_fulfilled', isset($pts1Form) ? ($pts1Form->min_time_req_fulfilled ? '1' : '0') : '1')),
                timeApproval: @js(old('special_approval_min_time', isset($pts1Form) ? ($pts1Form->special_approval_min_time ? '1' : '0') : '1')),

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
                    if (existingPts1 && existingPts1.publication_list_doc_path) {
                        fetch("{{ route('pts.document.serve', ['pts1', $pts1Form->id ?? 0, 'publication_list_doc_path']) }}")
                            .then(res => res.ok ? res.arrayBuffer() : null)
                            .then(data => {
                                if (!data) return;
                                const workbook = XLSX.read(new Uint8Array(data), { type: 'array', cellDates: true });
                                const container = document.getElementById('excelPreviewContainer');
                                const sheetsOutput = document.getElementById('sheetsOutput');
                                const sheetTabsBar = document.getElementById('sheetTabsBar');
                                const sheetCountSpan = document.getElementById('excelSheetCount');

                                sheetsOutput.innerHTML = '';
                                sheetTabsBar.innerHTML = '';
                                container.classList.remove('hidden');

                                const sheetNames = workbook.SheetNames;
                                sheetCountSpan.innerText = `${sheetNames.length} Sheet(s) Found`;

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
                            }).catch(() => {});
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
                        this.fileErrors[key] = `File size (${sizeInMB.toFixed(2)} MB) exceeds standard default PHP limit of ${limitMB} MB. Please select a smaller file.`;
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
                        document.getElementById('excelPreviewContainer').classList.add('hidden');
                        document.getElementById('sheetsOutput').innerHTML = '';
                        document.getElementById('sheetTabsBar').innerHTML = '';
                    }
                },

                handleExcelPreview(event) {
                    const file = event.target.files[0];
                    if (!file) return;

                    const reader = new FileReader();
                    reader.onload = function(e) {
                        const data = new Uint8Array(e.target.result);
                        
                        // Parse full workbook with all sheets using SheetJS
                        const workbook = XLSX.read(data, { type: 'array', cellDates: true });
                        
                        const container = document.getElementById('excelPreviewContainer');
                        const sheetsOutput = document.getElementById('sheetsOutput');
                        const sheetTabsBar = document.getElementById('sheetTabsBar');
                        const sheetCountSpan = document.getElementById('excelSheetCount');

                        sheetsOutput.innerHTML = '';
                        sheetTabsBar.innerHTML = '';
                        container.classList.remove('hidden');

                        const sheetNames = workbook.SheetNames;
                        sheetCountSpan.innerText = `${sheetNames.length} Sheet(s) Found`;

                        sheetNames.forEach((sheetName, index) => {
                            const worksheet = workbook.Sheets[sheetName];
                            if (!worksheet) return;

                            // Convert sheet data to formatted HTML table
                            const htmlString = XLSX.utils.sheet_to_html(worksheet, { id: 'sheet-table-' + index, editable: false });

                            // Create Sheet Tab Button
                            const tabBtn = document.createElement('a');
                            tabBtn.href = `#sheet-block-${index}`;
                            tabBtn.className = 'px-3 py-1.5 text-xs font-bold rounded-lg border border-indigo-200 dark:border-indigo-800 bg-indigo-50 dark:bg-indigo-900/40 text-indigo-700 dark:text-indigo-300 hover:bg-indigo-100 transition flex items-center';
                            tabBtn.innerHTML = `<span class="w-2 h-2 rounded-full bg-emerald-500 mr-1.5"></span> ${sheetName}`;
                            sheetTabsBar.appendChild(tabBtn);

                            // Create Sheet Block Container
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
                    };
                    reader.readAsArrayBuffer(file);
                }
            }
        }
    </script>
</x-app-layout>

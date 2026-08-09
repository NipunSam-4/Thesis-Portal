<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-bold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Main Supervisor Review: PTS-1 Form') }} &mdash; {{ $studentUser->name }} ({{ $student->roll_number }})
            </h2>
            <a href="{{ route('faculty.dashboard') }}" class="text-sm font-medium text-blue-600 dark:text-blue-400 hover:underline">
                &larr; Back to Dashboard
            </a>
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

    <div class="py-8" x-data="pts1ReviewForm()">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">

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

            <!-- Error Alerts -->
            @if($errors->any())
                <div class="p-4 bg-red-100 border-l-4 border-red-500 text-red-700 rounded-lg shadow-sm">
                    <div class="font-bold">Please correct the validation errors below:</div>
                    <ul class="mt-1 list-disc list-inside text-sm">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('faculty.pts1.update', $pts1->id) }}" method="POST" enctype="multipart/form-data" class="space-y-8">
                @csrf
                @method('PUT')

                <!-- Section 1: Read-Only Student Information -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
                    <div class="flex items-center justify-between border-b border-gray-100 dark:border-gray-700 pb-3 mb-4">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white flex items-center">
                            1. Student Information
                        </h3>
                        <span class="text-xs font-bold bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 px-3 py-1 rounded-full">
                            Fetched from Student Record
                        </span>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <label class="block text-xs font-semibold uppercase text-gray-500 mb-1">Student Name</label>
                            <input type="text" value="{{ $studentUser->name }}" readonly class="w-full bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200 font-bold rounded-lg border-gray-300 dark:border-gray-600 cursor-not-allowed">
                        </div>

                        <div>
                            <label class="block text-xs font-semibold uppercase text-gray-500 mb-1">Roll Number</label>
                            <input type="text" value="{{ $student->roll_number }}" readonly class="w-full bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200 font-bold rounded-lg border-gray-300 dark:border-gray-600 cursor-not-allowed">
                        </div>

                        <div>
                            <label class="block text-xs font-semibold uppercase text-gray-500 mb-1">Department</label>
                            <input type="text" value="{{ $student->department->name ?? 'N/A' }}" readonly class="w-full bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200 font-bold rounded-lg border-gray-300 dark:border-gray-600 cursor-not-allowed">
                        </div>

                        <div>
                            <label class="block text-xs font-semibold uppercase text-gray-500 mb-1">Date of Registration</label>
                            <input type="text" value="{{ $student->date_registration }}" readonly class="w-full bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200 font-bold rounded-lg border-gray-300 dark:border-gray-600 cursor-not-allowed">
                        </div>

                        <div>
                            <label class="block text-xs font-semibold uppercase text-gray-500 mb-1">Date of Joining</label>
                            <input type="text" value="{{ $student->date_joining }}" readonly class="w-full bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200 font-bold rounded-lg border-gray-300 dark:border-gray-600 cursor-not-allowed">
                        </div>
                    </div>
                </div>

                <!-- Section 2: Seminar & Confirmation Details (Editable by Supervisor) -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4 border-b border-gray-100 dark:border-gray-700 pb-2">
                        2. Confirmation & Seminar Details
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Date of Confirmation <span class="text-red-500">*</span></label>
                            <input type="date" name="date_confirmation" required value="{{ old('date_confirmation', $student->date_confirmation) }}" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Date of Open Seminar <span class="text-red-500">*</span></label>
                            <input type="date" name="seminar_date" required value="{{ old('seminar_date', $pts1->seminar_date ? $pts1->seminar_date->format('Y-m-d') : '') }}" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Time of Open Seminar <span class="text-red-500">*</span></label>
                            <input type="time" name="seminar_time" required value="{{ old('seminar_time', $pts1->seminar_time) }}" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Venue of Open Seminar <span class="text-red-500">*</span></label>
                            <input type="text" name="seminar_venue" required value="{{ old('seminar_venue', $pts1->seminar_venue) }}" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Online Meeting Link (Optional)</label>
                            <input type="url" name="meeting_link" placeholder="https://meet.google.com/abc-defg-hij" value="{{ old('meeting_link', $pts1->meeting_link) }}" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                        </div>
                    </div>
                </div>

                <!-- Section 3: Institute Norms & Requirements -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 space-y-6">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white border-b border-gray-100 dark:border-gray-700 pb-2">
                        3. Institute Norms & Criteria Verification
                    </h3>

                    <!-- Publication Norm -->
                    <div class="p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg space-y-4">
                        <label class="block font-semibold text-gray-900 dark:text-white text-sm">
                            Fulfilling Institute publication norm for open seminar? <span class="text-red-500">*</span>
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

                        <div x-show="pubNorm === '0'" x-cloak class="pt-3 border-t border-gray-200 dark:border-gray-600 space-y-3">
                            <label class="block font-semibold text-gray-900 dark:text-white text-sm">
                                Special approval taken for publication norm relaxation? <span class="text-red-500">*</span>
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

                            <div x-show="pubApproval === '1'" class="pt-2 space-y-3">
                                @if($pts1->publication_approval_doc_path)
                                    <div class="p-3 bg-blue-50 dark:bg-blue-900/30 border border-blue-200 dark:border-blue-800 rounded-xl flex items-center justify-between">
                                        <div class="flex items-center space-x-3">
                                            <span class="text-xs font-bold text-blue-800 dark:text-blue-300 uppercase">Existing File:</span>
                                            <a href="{{ route('pts.document.serve', ['pts1', $pts1->id, 'publication_approval_doc_path']) }}" target="_blank" class="text-xs font-bold text-blue-600 dark:text-blue-400 hover:underline flex items-center">
                                                📄 View Publication Approval Copy
                                            </a>
                                        </div>
                                    </div>
                                @endif

                                <div>
                                    <div class="flex justify-between items-center mb-1">
                                        <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300">
                                            Replace Special Publication Approval Copy (Max 2 MB) @if(!$pts1->publication_approval_doc_path)<span class="text-red-500">*</span>@endif
                                        </label>
                                        <span class="text-[11px] text-gray-400">PDF, PNG, JPG</span>
                                    </div>

                                    <div x-show="!fileStates.pubApp.name">
                                        <input type="file" id="pubAppInput" name="publication_approval_doc" accept=".pdf,.png,.jpg,.jpeg" :required="pubNorm === '0' && pubApproval === '1' && !'{{ $pts1->publication_approval_doc_path }}'" @change="handleFileSelect($event, 'pubApp')" class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
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
                                                <div class="text-[10px] text-emerald-800 dark:text-emerald-300 font-bold uppercase">New Approval File Selected</div>
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
                            </div>
                            <div x-show="pubApproval === '0'" class="p-3 bg-red-50 dark:bg-red-900/30 text-red-700 dark:text-red-300 rounded-lg text-sm font-semibold flex items-center">
                                <svg class="w-5 h-5 mr-2 text-red-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path></svg>
                                Please take the approval from respective authority then proceed.
                            </div>
                        </div>
                    </div>

                    <!-- Minimum Time Requirement -->
                    <div class="p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg space-y-4">
                        <label class="block font-semibold text-gray-900 dark:text-white text-sm">
                            Fulfilling minimum time requirement criteria for thesis submission? <span class="text-red-500">*</span>
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

                        <div x-show="timeNorm === '0'" x-cloak class="pt-3 border-t border-gray-200 dark:border-gray-600 space-y-3">
                            <label class="block font-semibold text-gray-900 dark:text-white text-sm">
                                Special approval taken for minimum time relaxation? <span class="text-red-500">*</span>
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

                            <div x-show="timeApproval === '1'" class="pt-2 space-y-3">
                                @if($pts1->min_time_approval_doc_path)
                                    <div class="p-3 bg-blue-50 dark:bg-blue-900/30 border border-blue-200 dark:border-blue-800 rounded-xl flex items-center justify-between">
                                        <div class="flex items-center space-x-3">
                                            <span class="text-xs font-bold text-blue-800 dark:text-blue-300 uppercase">Existing File:</span>
                                            <a href="{{ route('pts.document.serve', ['pts1', $pts1->id, 'min_time_approval_doc_path']) }}" target="_blank" class="text-xs font-bold text-blue-600 dark:text-blue-400 hover:underline flex items-center">
                                                📄 View Minimum Time Approval Copy
                                            </a>
                                        </div>
                                    </div>
                                @endif

                                <div>
                                    <div class="flex justify-between items-center mb-1">
                                        <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300">
                                            Replace Special Minimum Time Approval Copy (Max 2 MB) @if(!$pts1->min_time_approval_doc_path)<span class="text-red-500">*</span>@endif
                                        </label>
                                        <span class="text-[11px] text-gray-400">PDF, PNG, JPG</span>
                                    </div>

                                    <div x-show="!fileStates.timeApp.name">
                                        <input type="file" id="timeAppInput" name="min_time_approval_doc" accept=".pdf,.png,.jpg,.jpeg" :required="timeNorm === '0' && timeApproval === '1' && !'{{ $pts1->min_time_approval_doc_path }}'" @change="handleFileSelect($event, 'timeApp')" class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
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
                                                <div class="text-[10px] text-emerald-800 dark:text-emerald-300 font-bold uppercase">New Approval File Selected</div>
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
                            </div>
                            <div x-show="timeApproval === '0'" class="p-3 bg-red-50 dark:bg-red-900/30 text-red-700 dark:text-red-300 rounded-lg text-sm font-semibold flex items-center">
                                <svg class="w-5 h-5 mr-2 text-red-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path></svg>
                                Please take the approval from respective authority then proceed.
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Section 4: Document Uploads & Live Multi-Sheet XLSX Preview -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 space-y-6">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white border-b border-gray-100 dark:border-gray-700 pb-2">
                        4. Uploaded Documents & Replacement Options
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Draft Synopsis Upload -->
                        <div class="p-4 bg-gray-50 dark:bg-gray-700/50 rounded-xl space-y-3">
                            <div class="flex justify-between items-center">
                                <label class="block text-sm font-bold text-gray-800 dark:text-gray-200">
                                    Draft Synopsis Report (.pdf, .docx)
                                </label>
                                <span class="text-[11px] text-gray-400">Max 2 MB</span>
                            </div>

                            @if($pts1->draft_synopsis_report_doc_path)
                                <div class="p-2.5 bg-indigo-50 dark:bg-indigo-900/30 border border-indigo-200 dark:border-indigo-800 rounded-lg flex items-center justify-between">
                                    <span class="text-xs text-indigo-800 dark:text-indigo-300 font-semibold">Existing File:</span>
                                    <a href="{{ route('pts.document.serve', ['pts1', $pts1->id, 'draft_synopsis_report_doc_path']) }}" target="_blank" class="px-2.5 py-1 bg-white dark:bg-gray-800 text-indigo-700 dark:text-indigo-300 border border-indigo-300 dark:border-indigo-700 rounded-md text-xs font-bold shadow-sm hover:bg-indigo-100 flex items-center">
                                        📄 View Existing Synopsis
                                    </a>
                                </div>
                            @endif

                            <div x-show="!fileStates.synopsis.name">
                                <label class="block text-xs font-semibold text-gray-500 mb-1">Replace File (Optional)</label>
                                <input type="file" id="synopsisInput" name="draft_synopsis_report" accept=".pdf,.docx" @change="handleFileSelect($event, 'synopsis')" class="w-full text-xs text-gray-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
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
                                        <div class="text-[10px] text-emerald-800 dark:text-emerald-300 font-bold uppercase">New File Selected</div>
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
                        <div class="p-4 bg-gray-50 dark:bg-gray-700/50 rounded-xl space-y-3">
                            <div class="flex justify-between items-center">
                                <label class="block text-sm font-bold text-gray-800 dark:text-gray-200">
                                    Publication List (.xlsx, .xls)
                                </label>
                                <span class="text-[11px] text-gray-400">Max 2 MB</span>
                            </div>

                            @if($pts1->publication_list_doc_path)
                                <div class="p-2.5 bg-emerald-50 dark:bg-emerald-900/30 border border-emerald-200 dark:border-emerald-800 rounded-lg flex items-center justify-between">
                                    <span class="text-xs text-emerald-800 dark:text-emerald-300 font-semibold">Existing File:</span>
                                    <a href="{{ route('pts.document.serve', ['pts1', $pts1->id, 'publication_list_doc_path']) }}" target="_blank" class="px-2.5 py-1 bg-white dark:bg-gray-800 text-emerald-700 dark:text-emerald-300 border border-emerald-300 dark:border-emerald-700 rounded-md text-xs font-bold shadow-sm hover:bg-emerald-100 flex items-center">
                                        📄 View Existing Publication List
                                    </a>
                                </div>
                            @endif

                            <div x-show="!fileStates.pubList.name">
                                <label class="block text-xs font-semibold text-gray-500 mb-1">Replace File (Optional)</label>
                                <input type="file" id="pubListInput" name="publication_list" accept=".xlsx,.xls" @change="if (handleFileSelect($event, 'pubList')) { handleExcelPreview($event); }" class="w-full text-xs text-gray-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100">
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
                                        <div class="text-[10px] text-emerald-800 dark:text-emerald-300 font-bold uppercase">New File Selected</div>
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

                <!-- Section 5: Work Status Evaluation & PSPC Discussion Comments -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-indigo-100 dark:border-indigo-900/50 p-6 space-y-6">
                    <h3 class="text-lg font-bold text-indigo-900 dark:text-indigo-300 border-b border-indigo-100 dark:border-indigo-900/50 pb-2 flex items-center">
                        5. Main Supervisor & PSPC Evaluation
                    </h3>

                    <!-- Item 1: Work Status Radio Cards -->
                    <div class="space-y-4">
                        <label class="block font-bold text-gray-900 dark:text-white text-sm">
                            1. The work done by the candidate towards the degree of Doctor of Philosophy (PhD) is, as of date: <span class="text-red-500">*</span>
                        </label>

                        <div class="grid grid-cols-1 gap-4">
                            <!-- Option (a) ADEQUATE -->
                            <label class="p-4 rounded-xl border-2 transition cursor-pointer flex items-start space-x-3" :class="workStatus === 'adequate' ? 'border-emerald-500 bg-emerald-50/50 dark:bg-emerald-950/30' : 'border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800'">
                                <input type="radio" name="work_status" value="adequate" required x-model="workStatus" class="mt-1 text-emerald-600 focus:ring-emerald-500">
                                <div>
                                    <span class="block font-bold text-sm text-emerald-900 dark:text-emerald-300 uppercase tracking-wide">
                                        (a) ADEQUATE for Thesis Submission
                                    </span>
                                    <p class="text-xs text-gray-600 dark:text-gray-300 mt-1 leading-relaxed">
                                        <strong>ADEQUATE</strong> for the submission of the PhD Synopsis and Thesis to be submitted within maximum <strong>ONE month</strong> from the date of <strong>OPEN SEMINAR</strong>, incorporating the suggestions (if any) made in additional comments, in consultation with the PhD Supervisor.
                                    </p>
                                </div>
                            </label>

                            <!-- Option (b) INADEQUATE -->
                            <label class="p-4 rounded-xl border-2 transition cursor-pointer flex items-start space-x-3" :class="workStatus === 'inadequate' ? 'border-red-500 bg-red-50/50 dark:bg-red-950/30' : 'border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800'">
                                <input type="radio" name="work_status" value="inadequate" required x-model="workStatus" class="mt-1 text-red-600 focus:ring-red-500">
                                <div>
                                    <span class="block font-bold text-sm text-red-900 dark:text-red-300 uppercase tracking-wide">
                                        (b) INADEQUATE in Present Form
                                    </span>
                                    <p class="text-xs text-gray-600 dark:text-gray-300 mt-1 leading-relaxed">
                                        <strong>INADEQUATE</strong> for the submission of the PhD Synopsis and Thesis in its present form and major modifications / additions / changes are required. The student must incorporate the improvements / modifications / changes suggested in the additional comments, and give the <strong>OPEN SEMINAR again</strong>.
                                    </p>
                                </div>
                            </label>
                        </div>
                    </div>

                    <!-- Item 2: PSPC Comments Textarea -->
                    <div class="space-y-2 pt-2">
                        <label class="block font-bold text-gray-900 dark:text-white text-sm">
                            2. Additional comments / observations / recommendations of the PSPC <span class="text-red-500">*</span>
                        </label>
                        <textarea name="main_supervisor_student_comment" rows="4" required placeholder="Provide detailed PSPC comments, observations, and recommendations..." class="w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm"></textarea>
                    </div>

                    <!-- Item 3: Dynamic Recommendation / Non-Recommendation Remark -->
                    <div class="space-y-2 pt-2">
                        <label class="block font-bold text-gray-900 dark:text-white text-sm">
                            <span x-show="workStatus === 'adequate'">3. Recommendation Remark (Optional)</span>
                            <span x-show="workStatus === 'inadequate'">3. Non-Recommendation Remark <span class="text-red-500">*</span></span>
                        </label>
                        <textarea name="main_supervisor_confidential_remark" 
                                  rows="3" 
                                  :required="workStatus === 'inadequate'" 
                                  :placeholder="workStatus === 'adequate' ? 'Optional recommendation remarks for next stage...' : 'Provide mandatory non-recommendation remarks...'" 
                                  class="w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm"></textarea>
                    </div>
                </div>

                <!-- Submit & Revert Action Buttons Bar -->
                <div class="flex items-center justify-end space-x-4 pt-4">
                    <!-- Revert Button (Triggers Independent Pop-Up Modal) -->
                    <button type="button" 
                            @click="showRevertModal = true" 
                            class="bg-red-600 hover:bg-red-700 text-white text-base font-bold px-6 py-3 rounded-xl shadow-lg transition flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"></path>
                        </svg>
                        Revert Form
                    </button>

                    <!-- Main Submit Button (Forwards to Next Stage) -->
                    <button type="submit" 
                            :disabled="isBlocked" 
                            :class="isBlocked ? 'opacity-50 cursor-not-allowed bg-gray-400' : 'bg-emerald-600 hover:bg-emerald-700'" 
                            class="text-white text-base font-bold px-8 py-3 rounded-xl shadow-lg transition flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                        </svg>
                        Submit & Forward
                    </button>
                </div>
            </form>

            <!-- Revert Confirmation Pop-Up Modal -->
            <div x-show="showRevertModal" 
                 x-cloak 
                 class="fixed inset-0 z-50 overflow-y-auto bg-gray-900/60 backdrop-blur-sm flex items-center justify-center p-4"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0">

                <div @click.away="showRevertModal = false" 
                     class="bg-white dark:bg-gray-800 rounded-2xl max-w-lg w-full p-6 shadow-2xl border border-gray-100 dark:border-gray-700 space-y-5"
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 scale-95"
                     x-transition:enter-end="opacity-100 scale-100">

                    <div class="flex items-center space-x-3 border-b border-gray-100 dark:border-gray-700 pb-3">
                        <div class="p-2.5 bg-amber-100 dark:bg-amber-900/40 rounded-xl text-amber-600 dark:text-amber-400">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white">Revert PTS-1 Form to Student</h3>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Send form back to candidate for required changes</p>
                        </div>
                    </div>

                    <!-- Independent Revert Form -->
                    <form action="{{ route('pts1.revert', $pts1->id) }}" method="POST" class="space-y-4">
                        @csrf

                        <div>
                            <label class="block text-sm font-bold text-gray-800 dark:text-gray-200 mb-1.5">
                                Reversion Comment <span class="text-red-500">*</span>
                            </label>
                            <textarea name="reversion_comment" 
                                      required 
                                      rows="4" 
                                      placeholder="Provide clear reasons/instructions for the student regarding required modifications..." 
                                      class="w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm focus:ring-2 focus:ring-amber-500"></textarea>
                        </div>

                        <div class="flex justify-end space-x-3 pt-2">
                            <button type="button" 
                                    @click="showRevertModal = false" 
                                    class="px-5 py-2.5 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200 font-bold text-sm rounded-xl transition">
                                Cancel
                            </button>
                            <button type="submit" 
                                    class="px-5 py-2.5 bg-red-600 hover:bg-red-700 text-white font-bold text-sm rounded-xl shadow-md transition flex items-center">
                                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"></path>
                                </svg>
                                Confirm Revert
                            </button>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>

    <!-- Load SheetJS for Client-Side Multi-Sheet Excel Parsing -->
    <script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>

    @php
        $existingPubListUrl = $pts1->publication_list_doc_path ? route('pts.document.serve', ['pts1', $pts1->id, 'publication_list_doc_path']) : null;
    @endphp

    <script>
        function pts1ReviewForm() {
            return {
                showRevertModal: false,
                workStatus: '{{ old('work_status', $pts1->work_status ?? 'adequate') }}',
                pubNorm: '{{ old('publication_norm_fulfillment', $pts1->publication_norm_fulfillment ? '1' : '0') }}',
                pubApproval: '{{ old('special_approval_publication', $pts1->special_approval_publication ? '1' : '0') }}',
                timeNorm: '{{ old('min_time_req_fulfilled', $pts1->min_time_req_fulfilled ? '1' : '0') }}',
                timeApproval: '{{ old('special_approval_min_time', $pts1->special_approval_min_time ? '1' : '0') }}',
                existingPubListUrl: @json($existingPubListUrl),

                maxSizes: {
                    synopsis: 2,
                    pubList: 2,
                    pubApp: 2,
                    timeApp: 2
                },

                fileErrors: {
                    synopsis: '',
                    pubList: '',
                    pubApp: '',
                    timeApp: ''
                },

                fileStates: {
                    synopsis: { name: '', size: '', url: null },
                    pubList: { name: '', size: '', url: null },
                    pubApp: { name: '', size: '', url: null },
                    timeApp: { name: '', size: '', url: null }
                },

                init() {
                    if (this.existingPubListUrl) {
                        this.loadExcelFromUrl(this.existingPubListUrl, 'Existing Publication List Preview');
                    }
                },

                loadExcelFromUrl(url, titlePrefix = 'Existing Publication List Preview') {
                    fetch(url)
                        .then(res => {
                            if (!res.ok) throw new Error('Failed to load existing publication list');
                            return res.arrayBuffer();
                        })
                        .then(ab => {
                            const data = new Uint8Array(ab);
                            const workbook = XLSX.read(data, { type: 'array', cellDates: true });
                            this.renderWorkbook(workbook, titlePrefix);
                        })
                        .catch(err => {
                            console.error('Error previewing existing publication list:', err);
                        });
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
                        this.fileErrors[key] = `File size (${sizeInMB.toFixed(2)} MB) exceeds limit of ${limitMB} MB. Please select a smaller file.`;
                        event.target.value = '';
                        this.clearFile(key, event.target.id);
                        return false;
                    }

                    this.fileErrors[key] = '';

                    if (this.fileStates[key].url) {
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
                    if (this.fileStates[key].url) {
                        URL.revokeObjectURL(this.fileStates[key].url);
                    }
                    this.fileStates[key] = { name: '', size: '', url: null };
                    const input = document.getElementById(inputId);
                    if (input) input.value = '';

                    if (key === 'pubList') {
                        if (this.existingPubListUrl) {
                            this.loadExcelFromUrl(this.existingPubListUrl, 'Existing Publication List Preview');
                        } else {
                            const container = document.getElementById('excelPreviewContainer');
                            if (container) container.classList.add('hidden');
                            const sheetsOutput = document.getElementById('sheetsOutput');
                            if (sheetsOutput) sheetsOutput.innerHTML = '';
                            const sheetTabsBar = document.getElementById('sheetTabsBar');
                            if (sheetTabsBar) sheetTabsBar.innerHTML = '';
                        }
                    }
                },

                handleExcelPreview(event) {
                    const file = event.target.files[0];
                    if (!file) return;

                    const self = this;
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        const data = new Uint8Array(e.target.result);
                        const workbook = XLSX.read(data, { type: 'array', cellDates: true });
                        self.renderWorkbook(workbook, 'New Selected File Preview');
                    };
                    reader.readAsArrayBuffer(file);
                },

                renderWorkbook(workbook, titlePrefix = 'Publication and Other Recognition Preview') {
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
            }
        }
    </script>
</x-app-layout>

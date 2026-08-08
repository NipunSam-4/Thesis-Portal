<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-bold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Main Supervisor Review: PTS-1 Form') }} &mdash; {{ $scholarUser->name }} ({{ $student->roll_number }})
            </h2>
            <a href="{{ route('faculty.dashboard') }}" class="text-sm font-medium text-blue-600 dark:text-blue-400 hover:underline">
                &larr; Back to Dashboard
            </a>
        </div>
    </x-slot>

    <div class="py-8" x-data="{
        workStatus: '{{ old('work_status', $pts1->work_status ?? 'adequate') }}',
        pubNorm: '{{ old('publication_norm_fulfillment', $pts1->publication_norm_fulfillment ? '1' : '0') }}',
        pubApproval: '{{ old('special_approval_publication', $pts1->special_approval_publication ? '1' : '0') }}',
        timeNorm: '{{ old('min_time_req_fulfilled', $pts1->min_time_req_fulfilled ? '1' : '0') }}',
        timeApproval: '{{ old('special_approval_min_time', $pts1->special_approval_min_time ? '1' : '0') }}'
    }">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">

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

                <!-- Section 1: Read-Only Scholar Information -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
                    <div class="flex items-center justify-between border-b border-gray-100 dark:border-gray-700 pb-3 mb-4">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white flex items-center">
                            <span class="w-2.5 h-2.5 rounded-full bg-blue-500 mr-2"></span>
                            1. Scholar Information (Read-Only)
                        </h3>
                        <span class="text-xs font-bold bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 px-3 py-1 rounded-full">
                            Fetched from Scholar Record
                        </span>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <label class="block text-xs font-semibold uppercase text-gray-500 mb-1">Scholar Name</label>
                            <input type="text" value="{{ $scholarUser->name }}" readonly class="w-full bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200 font-bold rounded-lg border-gray-300 dark:border-gray-600 cursor-not-allowed">
                        </div>

                        <div>
                            <label class="block text-xs font-semibold uppercase text-gray-500 mb-1">Roll Number</label>
                            <input type="text" value="{{ $student->roll_number }}" readonly class="w-full bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200 font-mono font-bold rounded-lg border-gray-300 dark:border-gray-600 cursor-not-allowed">
                        </div>

                        <div>
                            <label class="block text-xs font-semibold uppercase text-gray-500 mb-1">Date of Registration</label>
                            <input type="text" value="{{ $student->date_registration }}" readonly class="w-full bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200 font-bold rounded-lg border-gray-300 dark:border-gray-600 cursor-not-allowed">
                        </div>
                    </div>
                </div>

                <!-- Section 2: Seminar & Confirmation Details (Editable by Supervisor) -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4 border-b border-gray-100 dark:border-gray-700 pb-2">
                        2. Confirmation & Seminar Details (Editable by Main Supervisor)
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
                            <input type="url" name="meeting_link" value="{{ old('meeting_link', $pts1->meeting_link) }}" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
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
                                <input type="radio" name="publication_norm_fulfillment" value="1" x-model="pubNorm" class="text-blue-600">
                                <span class="ml-2 text-sm text-gray-800 dark:text-gray-200">Yes</span>
                            </label>
                            <label class="inline-flex items-center">
                                <input type="radio" name="publication_norm_fulfillment" value="0" x-model="pubNorm" class="text-blue-600">
                                <span class="ml-2 text-sm text-gray-800 dark:text-gray-200">No</span>
                            </label>
                        </div>

                        <div x-show="pubNorm === '0'" x-cloak class="pt-3 border-t border-gray-200 dark:border-gray-600 space-y-3">
                            <label class="block font-semibold text-gray-900 dark:text-white text-sm">Special approval taken for publication norm relaxation?</label>
                            <div class="flex items-center space-x-6">
                                <label class="inline-flex items-center">
                                    <input type="radio" name="special_approval_publication" value="1" x-model="pubApproval" class="text-blue-600">
                                    <span class="ml-2 text-sm text-gray-800 dark:text-gray-200">Yes</span>
                                </label>
                                <label class="inline-flex items-center">
                                    <input type="radio" name="special_approval_publication" value="0" x-model="pubApproval" class="text-blue-600">
                                    <span class="ml-2 text-sm text-gray-800 dark:text-gray-200">No</span>
                                </label>
                            </div>

                            <div x-show="pubApproval === '1'" class="pt-2 space-y-2">
                                @if($pts1->publication_approval_doc_path)
                                    <div class="flex items-center space-x-3">
                                        <span class="text-xs text-gray-500">Current File:</span>
                                        <a href="{{ route('pts.document.serve', ['pts1', $pts1->id, 'publication_approval_doc_path']) }}" target="_blank" class="text-xs font-bold text-blue-600 hover:underline flex items-center">
                                            📄 View Publication Approval Copy
                                        </a>
                                    </div>
                                @endif
                                <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300">Replace Special Publication Approval Copy (Optional)</label>
                                <input type="file" name="publication_approval_doc" accept=".pdf,.png,.jpg,.jpeg" class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
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
                                <input type="radio" name="min_time_req_fulfilled" value="1" x-model="timeNorm" class="text-blue-600">
                                <span class="ml-2 text-sm text-gray-800 dark:text-gray-200">Yes</span>
                            </label>
                            <label class="inline-flex items-center">
                                <input type="radio" name="min_time_req_fulfilled" value="0" x-model="timeNorm" class="text-blue-600">
                                <span class="ml-2 text-sm text-gray-800 dark:text-gray-200">No</span>
                            </label>
                        </div>

                        <div x-show="timeNorm === '0'" x-cloak class="pt-3 border-t border-gray-200 dark:border-gray-600 space-y-3">
                            <label class="block font-semibold text-gray-900 dark:text-white text-sm">Special approval taken for minimum time relaxation?</label>
                            <div class="flex items-center space-x-6">
                                <label class="inline-flex items-center">
                                    <input type="radio" name="special_approval_min_time" value="1" x-model="timeApproval" class="text-blue-600">
                                    <span class="ml-2 text-sm text-gray-800 dark:text-gray-200">Yes</span>
                                </label>
                                <label class="inline-flex items-center">
                                    <input type="radio" name="special_approval_min_time" value="0" x-model="timeApproval" class="text-blue-600">
                                    <span class="ml-2 text-sm text-gray-800 dark:text-gray-200">No</span>
                                </label>
                            </div>

                            <div x-show="timeApproval === '1'" class="pt-2 space-y-2">
                                @if($pts1->min_time_approval_doc_path)
                                    <div class="flex items-center space-x-3">
                                        <span class="text-xs text-gray-500">Current File:</span>
                                        <a href="{{ route('pts.document.serve', ['pts1', $pts1->id, 'min_time_approval_doc_path']) }}" target="_blank" class="text-xs font-bold text-blue-600 hover:underline flex items-center">
                                            📄 View Minimum Time Approval Copy
                                        </a>
                                    </div>
                                @endif
                                <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300">Replace Special Minimum Time Approval Copy (Optional)</label>
                                <input type="file" name="min_time_approval_doc" accept=".pdf,.png,.jpg,.jpeg" class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Section 4: Document Inspection & Replacement -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 space-y-6">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white border-b border-gray-100 dark:border-gray-700 pb-2">
                        4. Uploaded Documents (Inspection & Replace Options)
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Draft Synopsis Report -->
                        <div class="p-4 bg-gray-50 dark:bg-gray-700/50 rounded-xl space-y-3">
                            <div class="flex justify-between items-center">
                                <label class="block text-sm font-bold text-gray-800 dark:text-gray-200">Draft Synopsis Report</label>
                                @if($pts1->draft_synopsis_report_doc_path)
                                    <a href="{{ route('pts.document.serve', ['pts1', $pts1->id, 'draft_synopsis_report_doc_path']) }}" target="_blank" class="px-3 py-1 bg-indigo-50 dark:bg-indigo-900/50 text-indigo-700 dark:text-indigo-300 border border-indigo-200 dark:border-indigo-800 rounded-lg text-xs font-bold hover:bg-indigo-100 flex items-center">
                                        📄 View Draft Synopsis
                                    </a>
                                @endif
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-500 mb-1">Replace File (Optional)</label>
                                <input type="file" name="draft_synopsis_report" accept=".pdf,.docx" class="w-full text-xs text-gray-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                            </div>
                        </div>

                        <!-- Publication List -->
                        <div class="p-4 bg-gray-50 dark:bg-gray-700/50 rounded-xl space-y-3">
                            <div class="flex justify-between items-center">
                                <label class="block text-sm font-bold text-gray-800 dark:text-gray-200">Publication List</label>
                                @if($pts1->publication_list_doc_path)
                                    <a href="{{ route('pts.document.serve', ['pts1', $pts1->id, 'publication_list_doc_path']) }}" target="_blank" class="px-3 py-1 bg-emerald-50 dark:bg-emerald-900/50 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800 rounded-lg text-xs font-bold hover:bg-emerald-100 flex items-center">
                                        📄 View Publication List
                                    </a>
                                @endif
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-500 mb-1">Replace File (Optional)</label>
                                <input type="file" name="publication_list" accept=".xlsx,.xls,.csv" class="w-full text-xs text-gray-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Section 5: Work Status Evaluation & PSPC Discussion Comments -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-indigo-100 dark:border-indigo-900/50 p-6 space-y-6">
                    <h3 class="text-lg font-bold text-indigo-900 dark:text-indigo-300 border-b border-indigo-100 dark:border-indigo-900/50 pb-2 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        5. Main Supervisor & PSPC Evaluation
                    </h3>

                    <!-- Item 1: Work Status Radio Cards -->
                    <div class="space-y-4">
                        <label class="block font-bold text-gray-900 dark:text-white text-sm">
                            1. The work done by the candidate towards the degree of Doctor of Philosophy (Ph.D.) is, as of date: <span class="text-red-500">*</span>
                        </label>

                        <div class="grid grid-cols-1 gap-4">
                            <!-- Option (a) ADEQUATE -->
                            <label class="p-4 rounded-xl border-2 transition cursor-pointer flex items-start space-x-3" :class="workStatus === 'adequate' ? 'border-emerald-500 bg-emerald-50/50 dark:bg-emerald-950/30' : 'border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800'">
                                <input type="radio" name="work_status" value="adequate" x-model="workStatus" class="mt-1 text-emerald-600 focus:ring-emerald-500">
                                <div>
                                    <span class="block font-bold text-sm text-emerald-900 dark:text-emerald-300 uppercase tracking-wide">
                                        (a) ADEQUATE for Thesis Submission
                                    </span>
                                    <p class="text-xs text-gray-600 dark:text-gray-300 mt-1 leading-relaxed">
                                        <strong>ADEQUATE</strong> for the submission of the Ph.D. Synopsis and Thesis to be submitted within maximum <strong>ONE month</strong> from the date of <strong>OPEN SEMINAR</strong>, incorporating the suggestions (if any) made in item 2, in consultation with the Ph.D. Supervisor.
                                    </p>
                                </div>
                            </label>

                            <!-- Option (b) INADEQUATE -->
                            <label class="p-4 rounded-xl border-2 transition cursor-pointer flex items-start space-x-3" :class="workStatus === 'inadequate' ? 'border-red-500 bg-red-50/50 dark:bg-red-950/30' : 'border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800'">
                                <input type="radio" name="work_status" value="inadequate" x-model="workStatus" class="mt-1 text-red-600 focus:ring-red-500">
                                <div>
                                    <span class="block font-bold text-sm text-red-900 dark:text-red-300 uppercase tracking-wide">
                                        (b) INADEQUATE in Present Form (Reverts Form to Scholar)
                                    </span>
                                    <p class="text-xs text-gray-600 dark:text-gray-300 mt-1 leading-relaxed">
                                        <strong>INADEQUATE</strong> for the submission of the Ph.D. Synopsis and Thesis in its present form and major modifications / additions / changes are required. The student must incorporate the improvements / modifications / changes suggested in item 2, and give the <strong>OPEN SEMINAR again</strong>.
                                    </p>
                                </div>
                            </label>
                        </div>
                    </div>

                    <!-- Item 2: PSPC Comments Textarea -->
                    <div class="space-y-2 pt-2">
                        <label class="block font-bold text-gray-900 dark:text-white text-sm">
                            2. Additional comments / observations / recommendations of the PSPC with discussion <span class="text-red-500">*</span>
                        </label>
                        <textarea name="main_supervisor_student_comment" rows="4" required placeholder="Provide detailed PSPC comments, observations, and recommendations..." class="w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm">{{ old('main_supervisor_student_comment', $pts1->main_supervisor_student_comment) }}</textarea>
                    </div>
                </div>

                <!-- Submit Action Button -->
                <div class="flex justify-end pt-4">
                    <button type="submit" :class="workStatus === 'inadequate' ? 'bg-red-600 hover:bg-red-700' : 'bg-emerald-600 hover:bg-emerald-700'" class="text-white text-base font-bold px-8 py-3 rounded-xl shadow-lg transition flex items-center">
                        <span x-text="workStatus === 'inadequate' ? '⚠️ Revert PTS-1 Form to Scholar' : '✓ Endorse & Forward PTS-1 Form'"></span>
                    </button>
                </div>
            </form>

        </div>
    </div>
</x-app-layout>

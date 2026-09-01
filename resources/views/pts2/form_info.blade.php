@php
    $pts2 = $pts2 ?? $form ?? null;
    $thesis = $thesis ?? $pts2?->thesis;
    $student = $student ?? $thesis?->student;
    $studentUser = $studentUser ?? $student?->user;
    $viewPerspective = $viewPerspective ?? 'authority';
@endphp

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
            <input type="text" value="{{ $studentUser->name ?? 'N/A' }}" readonly class="w-full bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200 rounded-lg border-gray-300 dark:border-gray-600 cursor-not-allowed">
        </div>

        <div>
            <label class="block text-xs font-semibold uppercase text-gray-500 mb-1">Roll Number</label>
            <input type="text" value="{{ $student->roll_number ?? 'N/A' }}" readonly class="w-full bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200 rounded-lg border-gray-300 dark:border-gray-600 cursor-not-allowed">
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

        <div>
            <label class="block text-xs font-semibold uppercase text-gray-500 mb-1">Open Seminar Date</label>
            <input type="text" value="{{ $thesis->getOpenSeminarDate()?->format('d-m-Y') ?? 'N/A' }}" readonly class="w-full bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200 rounded-lg border-gray-300 dark:border-gray-600 cursor-not-allowed">
        </div>

        <div>
            <label class="flex items-center text-xs font-semibold uppercase text-gray-500 mb-1">
                <span>Course Credits Required</span>
                <x-info-button text="Minimum course credits required for the degree program." />
            </label>
            <input type="text" value="{{ $student->course_credits_required ?? 'N/A' }}" readonly class="w-full bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200 rounded-lg border-gray-300 dark:border-gray-600 cursor-not-allowed">
        </div>

        <div>
            <label class="flex items-center text-xs font-semibold uppercase text-gray-500 mb-1">
                <span>Course Credits Earned (From System)</span>
                <x-info-button text="Course credits earned including coursework, seminars, and research credits." />
            </label>
            <input type="text" value="{{ $student->course_credits_earned ?? 'N/A' }}" readonly class="w-full bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200 rounded-lg border-gray-300 dark:border-gray-600 cursor-not-allowed">
        </div>

        <div>
            <label class="block text-xs font-semibold uppercase text-gray-500 mb-1">Date of Submission</label>
            <input type="text" value="{{ $pts2->date_of_submission ? \Carbon\Carbon::parse($pts2->date_of_submission)->format('d-m-Y') : ($pts2->created_at ? $pts2->created_at->format('d-m-Y') : 'N/A') }}" readonly class="w-full bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200 rounded-lg border-gray-300 dark:border-gray-600 cursor-not-allowed">
        </div>
    </div>
</div>

<!-- Section 2: Name of Thesis (Read-Only) -->
<div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 space-y-2">
    <h3 class="text-lg font-bold text-gray-900 dark:text-white border-b border-gray-100 dark:border-gray-700 pb-2">
        2. Name of Thesis
    </h3>
    <div>
        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1 flex items-center gap-2">
            <span>Thesis Title</span>
            @if($viewPerspective !== 'student' && $pts2->main_supervisor_thesis_title && $pts2->main_supervisor_thesis_title !== $pts2->thesis_title)
                <x-modified-badge 
                    :old-value="$pts2->thesis_title ?: ($thesis->title ?? 'N/A')" 
                    :new-value="$pts2->main_supervisor_thesis_title" />
            @endif
        </label>
        @php
            $dispThesisTitle = ($viewPerspective === 'student')
                ? ($pts2->thesis_title ?: ($thesis->title ?? 'N/A'))
                : $pts2->effective_thesis_title;
        @endphp
        <input type="text" value="{{ $dispThesisTitle }}" readonly class="w-full bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200 rounded-lg border-gray-300 dark:border-gray-600 cursor-not-allowed">
    </div>
</div>

<!-- Section 3: Contact & Course Details (Submitted by Student) -->
<div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 space-y-4">
    <h3 class="text-lg font-bold text-gray-900 dark:text-white border-b border-gray-100 dark:border-gray-700 pb-2">
        3. Contact &amp; Course Details (Submitted by Student)
    </h3>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="md:col-span-2">
            <label class="block text-xs font-semibold uppercase text-gray-500 mb-1">Current Residential / Correspondence Address</label>
            <p class="p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg border border-gray-200 dark:border-gray-600 text-sm text-gray-800 dark:text-gray-200 whitespace-pre-wrap break-words [overflow-wrap:anywhere]">{{ $pts2->current_address }}</p>
        </div>

        <div>
            <label class="block text-xs font-semibold uppercase text-gray-500 mb-1">Recent Phone Number</label>
            <input type="text" value="{{ $pts2->getFormattedRecentPhoneNumber() }}" readonly class="w-full bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200 rounded-lg border-gray-300 dark:border-gray-600 cursor-not-allowed">
        </div>

        <div>
            <label class="block text-xs font-semibold uppercase text-gray-500 mb-1">Alternate Phone Number</label>
            <input type="text" value="{{ $pts2->getFormattedAlternatePhoneNumber() }}" readonly class="w-full bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200 rounded-lg border-gray-300 dark:border-gray-600 cursor-not-allowed">
        </div>

        <div>
            <label class="block text-xs font-semibold uppercase text-gray-500 mb-1">Alternate Email Address</label>
            <input type="text" value="{{ $pts2->alternate_email ?? 'N/A' }}" readonly class="w-full bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200 rounded-lg border-gray-300 dark:border-gray-600 cursor-not-allowed">
        </div>

        <div>
            <label class="flex items-center gap-1 text-xs font-semibold uppercase text-gray-500 mb-1">
                <span>Course Credits Submitted by Student</span>
                <x-info-button text="Course credits earned including coursework, seminars, and research credits." />
            </label>
            <input type="text" value="{{ $pts2->course_credits_student }}" readonly class="w-full bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200 rounded-lg border-gray-300 dark:border-gray-600 cursor-not-allowed">
        </div>
    </div>
</div>

<!-- Section 4: Further Certified That (Declarations) -->
<div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 space-y-6">
    <h3 class="text-lg font-bold text-gray-900 dark:text-white border-b border-gray-100 dark:border-gray-700 pb-2">
        4. Further Certified That (Declarations)
    </h3>

    <div class="space-y-4">
        <!-- Item 1: Prima Facie Case -->
        <div class="p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg space-y-2">
            <div class="flex items-center justify-between gap-2 sm:gap-4">
                <span class="font-semibold text-gray-900 dark:text-white text-sm flex items-center gap-2">
                    <span>1. There is a prima facie case for consideration of the thesis.</span>
                    @if($viewPerspective !== 'student' && $pts2->main_supervisor_cert_prima_facie_case !== null && (bool)$pts2->main_supervisor_cert_prima_facie_case !== (bool)$pts2->cert_prima_facie_case)
                        <x-modified-badge 
                            :old-value="$pts2->cert_prima_facie_case ? 'Certified' : 'Not Certified'" 
                            :new-value="$pts2->main_supervisor_cert_prima_facie_case ? 'Certified' : 'Not Certified'" />
                    @endif
                </span>
                @php
                    $dispPrimaFacie = ($viewPerspective === 'student') ? $pts2->cert_prima_facie_case : $pts2->getEffectiveCertPrimaFacieCase();
                @endphp
                <span class="px-3 py-1 text-xs font-bold rounded-full whitespace-nowrap shrink-0 {{ $dispPrimaFacie ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/60 dark:text-emerald-300' : 'bg-red-100 text-red-800 dark:bg-red-900/60 dark:text-red-300' }}">
                    {{ $dispPrimaFacie ? '✓ Certified' : 'Not Certified' }}
                </span>
            </div>
        </div>

        <!-- Item 2: No Prior Degree Submission -->
        <div class="p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg space-y-2">
            <div class="flex items-center justify-between gap-2 sm:gap-4">
                <span class="font-semibold text-gray-900 dark:text-white text-sm flex items-center gap-2">
                    <span>2. To the best of our knowledge the thesis does not include any work which has, at any time, previously, been submitted for the award of a degree except to the extent of point 3 below.</span>
                    @if($viewPerspective !== 'student' && $pts2->main_supervisor_cert_no_prior_degree_submission !== null && (bool)$pts2->main_supervisor_cert_no_prior_degree_submission !== (bool)$pts2->cert_no_prior_degree_submission)
                        <x-modified-badge 
                            :old-value="$pts2->cert_no_prior_degree_submission ? 'Certified' : 'Not Certified'" 
                            :new-value="$pts2->main_supervisor_cert_no_prior_degree_submission ? 'Certified' : 'Not Certified'" />
                    @endif
                </span>
                @php
                    $dispNoPrior = ($viewPerspective === 'student') ? $pts2->cert_no_prior_degree_submission : $pts2->getEffectiveCertNoPriorDegreeSubmission();
                @endphp
                <span class="px-3 py-1 text-xs font-bold rounded-full whitespace-nowrap shrink-0 {{ $dispNoPrior ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/60 dark:text-emerald-300' : 'bg-red-100 text-red-800 dark:bg-red-900/60 dark:text-red-300' }}">
                    {{ $dispNoPrior ? '✓ Certified' : 'Not Certified' }}
                </span>
            </div>
        </div>

        <!-- Item 3: Collaborative Work -->
        @php
            $hasCollab = ($viewPerspective === 'student') ? $pts2->collaborative_work_status : $pts2->getEffectiveCollaborativeWorkStatus();
            $collabDetails = ($viewPerspective === 'student') ? $pts2->collaborative_work_details : $pts2->getEffectiveCollaborativeWorkDetails();
        @endphp
        <div class="p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg space-y-3">
            <div class="flex items-center justify-between gap-2 sm:gap-4">
                <span class="font-semibold text-gray-900 dark:text-white text-sm flex items-center gap-2">
                    <span>3. Does any section of the Thesis relate to collaborative work?</span>
                    @if($viewPerspective !== 'student' && $pts2->main_supervisor_collaborative_work_status !== null && (bool)$pts2->main_supervisor_collaborative_work_status !== (bool)$pts2->collaborative_work_status)
                        <x-modified-badge 
                            :old-value="$pts2->collaborative_work_status ? 'Yes' : 'No'" 
                            :new-value="$pts2->main_supervisor_collaborative_work_status ? 'Yes' : 'No'" />
                    @endif
                </span>
                <span class="px-3 py-1 text-xs font-bold rounded-full whitespace-nowrap shrink-0 {{ $hasCollab ? 'bg-indigo-100 text-indigo-800 dark:bg-indigo-900/60 dark:text-indigo-300' : 'bg-gray-200 text-gray-800 dark:bg-gray-600 dark:text-gray-200' }}">
                    {{ $hasCollab ? 'Yes (Collaborative work exists)' : 'No' }}
                </span>
            </div>

            @if($hasCollab && $collabDetails)
                <div class="pt-3 border-t border-gray-200 dark:border-gray-600 space-y-1">
                    <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase flex items-center gap-2">
                        <span>Collaborative Sections &amp; Details:</span>
                        @if($viewPerspective !== 'student' && $pts2->main_supervisor_collaborative_work_details !== null && $pts2->main_supervisor_collaborative_work_details !== $pts2->collaborative_work_details)
                            <x-modified-badge 
                                :old-value="$pts2->collaborative_work_details" 
                                :new-value="$pts2->main_supervisor_collaborative_work_details" />
                        @endif
                    </label>
                    <div class="p-3 bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 text-xs text-gray-800 dark:text-gray-200 whitespace-pre-wrap break-words [overflow-wrap:anywhere] leading-relaxed shadow-sm">{{ trim($collabDetails) }}</div>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Section 5: Uploaded Documents Inspection (Read-Only) -->
<div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 space-y-6">
    <h3 class="text-lg font-bold text-gray-900 dark:text-white border-b border-gray-100 dark:border-gray-700 pb-2">
        5. Uploaded Documents
    </h3>

    <div class="p-4 bg-gray-50 dark:bg-gray-700/50 rounded-xl border border-gray-200 dark:border-gray-600 space-y-2">
        <div class="text-xs font-bold text-gray-500 uppercase flex items-center gap-2">
            <span>{{ ($student && $student->isPhd()) ? 'PhD' : 'MS(R)' }} Synopsis Report Document</span>
            @if($viewPerspective !== 'student' && $pts2->main_supervisor_synopsis_report_doc_path && $pts2->main_supervisor_synopsis_report_doc_path !== $pts2->synopsis_report_doc_path)
                <x-modified-badge />
            @endif
        </div>
        @php
            $synopsisDocPath = ($viewPerspective === 'student') ? $pts2->synopsis_report_doc_path : $pts2->getEffectiveSynopsisPath();
            $synopsisDocField = ($viewPerspective === 'student') ? 'synopsis_report_doc_path' : $pts2->getEffectiveSynopsisField();
        @endphp
        @if($synopsisDocPath)
            <a href="{{ route('pts.document.serve', ['pts2', $pts2->id, $synopsisDocField]) }}" target="_blank" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-lg shadow transition inline-flex items-center">
                📄 View Synopsis Report Document
            </a>
        @else
            <span class="text-xs font-bold text-gray-400 italic">No File Uploaded</span>
        @endif
    </div>
</div>

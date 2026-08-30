<x-app-layout>
    @php
        $pts2 = $pts2 ?? $form ?? null;
        $form = $form ?? $pts2 ?? null;
        $thesis = $thesis ?? $form?->thesis;
        $student = $student ?? $thesis?->student;
        $studentUser = $studentUser ?? $student?->user;
        $currentUser = $user ?? auth()->user();
        $viewPerspective = $viewPerspective ?? ($currentUser?->isStudent() ? 'student' : ($currentUser?->isAcademicOffice() ? 'academic_office' : ($currentUser?->isDoaa() ? 'doaa' : 'authority')));
        $formPrefix = isset($student) && $student->isPhd() ? 'PTS' : 'MSRTS';
    @endphp
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div class="flex items-center space-x-3">
                <h2 class="font-bold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                    {{ __("Submitted {$formPrefix}-2 Form") }}
                </h2>
                <span class="px-3 py-1 bg-blue-100 dark:bg-blue-900/60 text-blue-800 dark:text-blue-300 text-xs font-extrabold rounded-full uppercase tracking-wider flex items-center shadow-sm">
                    ⏳ In Progress
                </span>
            </div>
            <x-back-to-dashboard-button />
        </div>
    </x-slot>

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
                    @php
                        $isCertPrimaFacie = ($viewPerspective === 'student')
                            ? (bool)$pts2->cert_prima_facie_case
                            : (bool)$pts2->getEffectiveCertPrimaFacieCase();
                    @endphp
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
                            <span class="px-3 py-1 text-xs font-bold rounded-full whitespace-nowrap shrink-0 {{ $isCertPrimaFacie ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/60 dark:text-emerald-300' : 'bg-red-100 text-red-800 dark:bg-red-900/60 dark:text-red-300' }}">
                                {{ $isCertPrimaFacie ? '✓ Certified' : 'Not Certified' }}
                            </span>
                        </div>
                    </div>

                    <!-- Item 2: No Prior Degree Submission -->
                    @php
                        $isCertNoPriorDegree = ($viewPerspective === 'student')
                            ? (bool)$pts2->cert_no_prior_degree_submission
                            : (bool)$pts2->getEffectiveCertNoPriorDegreeSubmission();
                    @endphp
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
                            <span class="px-3 py-1 text-xs font-bold rounded-full whitespace-nowrap shrink-0 {{ $isCertNoPriorDegree ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/60 dark:text-emerald-300' : 'bg-red-100 text-red-800 dark:bg-red-900/60 dark:text-red-300' }}">
                                {{ $isCertNoPriorDegree ? '✓ Certified' : 'Not Certified' }}
                            </span>
                        </div>
                    </div>

                    <!-- Item 3: Collaborative Work -->
                    @php
                        $hasCollab = ($viewPerspective === 'student')
                            ? (bool)$pts2->collaborative_work_status
                            : (bool)$pts2->getEffectiveCollaborativeWorkStatus();
                        
                        $collabDetails = ($viewPerspective === 'student')
                            ? $pts2->collaborative_work_details
                            : $pts2->getEffectiveCollaborativeWorkDetails();
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
                        <span>PhD Synopsis Report Document</span>
                        @if($viewPerspective !== 'student' && $pts2->main_supervisor_synopsis_report_doc_path && $pts2->main_supervisor_synopsis_report_doc_path !== $pts2->synopsis_report_doc_path)
                            <x-modified-badge />
                        @endif
                    </div>
                    @php
                        $synopsisDocField = ($viewPerspective === 'student')
                            ? ($pts2->synopsis_report_doc_path ? 'synopsis_report_doc_path' : null)
                            : $pts2->getEffectiveSynopsisField();
                    @endphp
                    @if($synopsisDocField)
                        <a href="{{ route('pts.document.serve', ['pts2', $pts2->id, $synopsisDocField]) }}" target="_blank" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-lg shadow transition inline-flex items-center">
                            📄 View Synopsis Report Document
                        </a>
                    @else
                        <span class="text-xs font-bold text-gray-400 italic">No File Uploaded</span>
                    @endif
                </div>
            </div>

            <!-- Section 6: Role-Scoped Review & Comments -->
            @if($viewPerspective !== 'student')
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 space-y-6">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white border-b border-gray-100 dark:border-gray-700 pb-2">
                        6. Evaluation &amp; Comments
                    </h3>

                    <div class="space-y-4">
                        <!-- 1. Main Supervisor Evaluation -->
                        @if($pts2->main_supervisor_submitted_at || !is_null($pts2->main_supervisor_recommendation))
                            <div class="p-4 bg-indigo-50/70 dark:bg-indigo-950/40 border-l-4 border-indigo-500 rounded-xl space-y-2">
                                <div class="flex items-center justify-between text-s gap-2 sm:gap-4">
                                    <span class="font-bold text-indigo-900 dark:text-indigo-200">
                                        Main Supervisor @if(isset($mainSupervisor))<span class="block sm:inline text-xs font-normal text-indigo-700 dark:text-indigo-300 mt-0.5 sm:mt-0">({{ $mainSupervisor->name }})</span>@endif
                                    </span>
                                    @if(!is_null($pts2->main_supervisor_recommendation))
                                        <span class="px-2.5 py-1 font-bold rounded-lg uppercase text-[10px] text-center leading-tight whitespace-normal max-w-[140px] sm:max-w-none {{ $pts2->main_supervisor_recommendation ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/60 dark:text-emerald-300' : 'bg-red-100 text-red-800 dark:bg-red-900/60 dark:text-red-300' }}">
                                            {{ $pts2->main_supervisor_recommendation ? '✓ Recommended' : '❌ Not Recommended' }}
                                        </span>
                                    @endif
                                </div>
                                <div class="text-xs text-gray-700 dark:text-gray-300">
                                    <strong>Student Comment:</strong>
                                    @if($pts2->main_supervisor_student_comment)
                                        <p class="italic bg-white dark:bg-gray-800 p-2.5 rounded-lg border border-indigo-100 dark:border-indigo-900 mt-1 whitespace-pre-wrap break-words [overflow-wrap:anywhere]">{{ trim($pts2->main_supervisor_student_comment) }}</p>
                                    @else
                                        <div class="flex items-center space-x-2 text-xs text-gray-600 dark:text-gray-500 bg-gray-200/60 dark:bg-gray-900/40 p-2.5 rounded-lg border border-dashed border-gray-200 dark:border-gray-700/60 mt-1">
                                            <svg class="w-4 h-4 text-gray-400 dark:text-gray-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 4.418 9 8z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3l18 18"></path></svg>
                                            <span class="italic font-normal">Comment not provided</span>
                                        </div>
                                    @endif
                                </div>

                                @if($viewPerspective === 'main_supervisor' || in_array($viewPerspective, ['dpgc', 'hod', 'academic_office', 'doaa']))
                                    <div class="text-xs text-gray-700 dark:text-gray-300 pt-1">
                                        <strong>Main Supervisor Remark:</strong>
                                        @if($pts2->main_supervisor_confidential_remark)
                                            <p class="italic bg-white dark:bg-gray-800 p-2.5 rounded-lg border border-indigo-100 dark:border-indigo-900 mt-1 whitespace-pre-wrap break-words [overflow-wrap:anywhere]">{{ trim($pts2->main_supervisor_confidential_remark) }}</p>
                                        @else
                                            <div class="flex items-center space-x-2 text-xs text-gray-600 dark:text-gray-500 bg-gray-200/60 dark:bg-gray-900/40 p-2.5 rounded-lg border border-dashed border-gray-200 dark:border-gray-700/60 mt-1">
                                                <svg class="w-4 h-4 text-gray-400 dark:text-gray-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 4.418 9 8z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3l18 18"></path></svg>
                                                <span class="italic font-normal">Remark not provided</span>
                                            </div>
                                        @endif
                                    </div>
                                @endif
                            </div>
                        @endif

                        <!-- 2. Co-Supervisors Endorsements -->
                        <!-- If viewPerspective == 'co_supervisor', show ONLY $myCoSupSlot -->
                        <!-- If viewPerspective in ['dpgc', 'hod', 'academic_office', 'doaa'], show ALL submitted co-supervisors -->
                        @if($viewPerspective === 'co_supervisor' && $myCoSupSlot)
                            @php
                                $recCol = "co_supervisor_{$myCoSupSlot}_recommendation";
                                $commCol = "co_supervisor_{$myCoSupSlot}_student_comment";
                                $remCol = "co_supervisor_{$myCoSupSlot}_confidential_remark";
                                $coUser = auth()->user();
                                $isExt = $coUser ? $coUser->isExternalSupervisor() : false;
                                $roleTitle = ($student && $coUser) ? $student->getSupervisorRoleTitle($coUser) : ($isExt ? 'External Supervisor' : "Co-Supervisor {$myCoSupSlot}");
                                $inst = ($isExt && $coUser?->externalSupervisorProfile?->affiliated_institute) ? ' - ' . $coUser->externalSupervisorProfile->affiliated_institute : '';
                            @endphp
                            @if(!is_null($pts2->$recCol))
                                <div class="p-4 bg-blue-50/70 dark:bg-blue-950/40 border-l-4 border-blue-500 rounded-xl space-y-3">
                                    <div class="flex items-center justify-between font-semibold gap-2 sm:gap-4">
                                        <h5 class="text-s font-bold text-blue-900 dark:text-blue-200">
                                            {{ $roleTitle }} <span class="block sm:inline text-xs font-normal text-gray-500 dark:text-gray-400 mt-0.5 sm:mt-0">({{ $coUser?->name }}{{ $inst }})</span>
                                        </h5>
                                        <span class="font-bold text-xs whitespace-nowrap shrink-0 {{ $pts2->$recCol ? 'text-emerald-600' : 'text-red-600' }}">
                                            {{ $pts2->$recCol ? '✓ Recommended' : '❌ Not Recommended' }}
                                        </span>
                                    </div>
                                    <div class="text-xs text-gray-700 dark:text-gray-300">
                                        <strong>Student Comment:</strong>
                                        @if($pts2->$commCol)
                                            <p class="italic bg-white dark:bg-gray-800 p-2.5 rounded-lg border border-blue-100 dark:border-blue-900 mt-1 whitespace-pre-wrap break-words [overflow-wrap:anywhere]">{{ trim($pts2->$commCol) }}</p>
                                        @else
                                            <div class="flex items-center space-x-2 text-xs text-gray-600 dark:text-gray-500 bg-gray-200/60 dark:bg-gray-900/40 p-2.5 rounded-lg border border-dashed border-gray-200 dark:border-gray-700/60 mt-1">
                                                <svg class="w-4 h-4 text-gray-400 dark:text-gray-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 4.418 9 8z"></path></svg>
                                                <span class="italic font-normal">Comment not provided</span>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="text-xs text-gray-700 dark:text-gray-300">
                                        <strong>Co-Supervisor Remark:</strong>
                                        @if($pts2->$remCol)
                                            <p class="italic bg-white dark:bg-gray-800 p-2.5 rounded-lg border border-blue-100 dark:border-blue-900 mt-1 whitespace-pre-wrap break-words [overflow-wrap:anywhere]">{{ trim($pts2->$remCol) }}</p>
                                        @else
                                            <div class="flex items-center space-x-2 text-xs text-gray-600 dark:text-gray-500 bg-gray-200/60 dark:bg-gray-900/40 p-2.5 rounded-lg border border-dashed border-gray-200 dark:border-gray-700/60 mt-1">
                                                <svg class="w-4 h-4 text-gray-400 dark:text-gray-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 4.418 9 8z"></path></svg>
                                                <span class="italic font-normal">Remark not provided</span>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endif
                        @elseif(in_array($viewPerspective, ['dpgc', 'hod', 'academic_office', 'doaa']))
                            @php
                                $hasAnyCoSubmitted = false;
                                for ($i = 1; $i <= 10; $i++) {
                                    $recCol = "co_supervisor_{$i}_recommendation";
                                    if (!is_null($pts2->$recCol)) { $hasAnyCoSubmitted = true; break; }
                                }
                            @endphp
                            @if($hasAnyCoSubmitted)
                                <div class="p-4 bg-blue-50/70 dark:bg-blue-950/40 border-l-4 border-blue-500 rounded-xl space-y-3">
                                    <h5 class="text-s font-bold text-blue-900 dark:text-blue-200">Co-Supervisors</h5>
                                    @for($i = 1; $i <= 10; $i++)
                                        @php
                                            $idCol = "co_supervisor_{$i}_id";
                                            $recCol = "co_supervisor_{$i}_recommendation";
                                            $commCol = "co_supervisor_{$i}_student_comment";
                                            $remCol = "co_supervisor_{$i}_confidential_remark";
                                            $coUser = $pts2->$idCol ? \App\Models\User::find($pts2->$idCol) : null;
                                        @endphp
                                        @if($coUser && !is_null($pts2->$recCol))
                                            @php
                                                $isExt = $coUser->isExternalSupervisor();
                                                $roleTitle = $student ? $student->getSupervisorRoleTitle($coUser) : ($isExt ? 'External Supervisor' : "Co-Supervisor {$i}");
                                                $inst = ($isExt && $coUser->externalSupervisorProfile?->affiliated_institute) ? ' - ' . $coUser->externalSupervisorProfile->affiliated_institute : '';
                                            @endphp
                                            <div class="text-xs space-y-1 pt-1 {{ $i > 1 ? 'border-t border-blue-100 dark:border-blue-900' : '' }}">
                                                <div class="flex items-center justify-between font-semibold gap-2 sm:gap-4">
                                                    <span>{{ $roleTitle }} <span class="block sm:inline text-xs font-normal text-gray-500 dark:text-gray-400 mt-0.5 sm:mt-0">({{ $coUser->name }}{{ $inst }})</span>:</span>
                                                    <span class="font-bold whitespace-nowrap shrink-0 {{ $pts2->$recCol ? 'text-emerald-600' : 'text-red-600' }}">
                                                        {{ $pts2->$recCol ? '✓ Recommended' : '❌ Not Recommended' }}
                                                    </span>
                                                </div>
                                                @if($pts2->$remCol)
                                                    <p class="italic text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 p-2.5 rounded-lg border border-blue-100 dark:border-blue-900 mt-1 whitespace-pre-wrap break-words [overflow-wrap:anywhere]">{{ trim($pts2->$remCol) }}</p>
                                                @else
                                                    <div class="flex items-center space-x-2 text-xs text-gray-600 dark:text-gray-500 bg-gray-200/60 dark:bg-gray-900/40 p-2.5 rounded-lg border border-dashed border-gray-200 dark:border-gray-700/60 mt-1">
                                                        <svg class="w-4 h-4 text-gray-400 dark:text-gray-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 4.418 9 8z"></path></svg>
                                                        <span class="italic font-normal">Remark not provided</span>
                                                    </div>
                                                @endif
                                            </div>
                                        @endif
                                    @endfor
                                </div>
                            @endif
                        @endif

                        <!-- 3. Academic Office Endorsement -->
                        @if(in_array($viewPerspective, ['academic_office', 'doaa']) && ($pts2->academic_office_submitted_at || $pts2->academic_office_is_verified !== null))
                            @php
                                $academicOfficeCredits = $pts2->academic_office_course_credits;
                                $academicOfficeRemark = $pts2->academic_office_verification_remark;
                            @endphp
                            <div class="p-4 bg-rose-50/70 dark:bg-rose-950/40 border-l-4 border-rose-500 rounded-xl space-y-2">
                                <div class="flex items-center justify-between font-bold text-rose-900 dark:text-rose-200 gap-2 sm:gap-4">
                                    <h5 class="text-s">Academic Office</h5>
                                    <span class="font-bold text-xs text-emerald-600 whitespace-nowrap shrink-0">
                                        ✓ Verified &amp; Forwarded
                                    </span>
                                </div>
                                @if($academicOfficeCredits !== null)
                                    <div class="flex items-center gap-1 text-xs text-gray-700 dark:text-gray-300">
                                        <strong>Verified Course Credits:</strong> <span class="font-bold text-rose-700 dark:text-rose-300">{{ $academicOfficeCredits }}</span>
                                        <x-info-button text="Course credits earned including coursework, seminars, and research credits." />
                                    </div>
                                @endif
                                <div class="text-xs text-gray-700 dark:text-gray-300 pt-0.5">
                                    <strong>Verification Remark:</strong>
                                    @if($academicOfficeRemark)
                                        <p class="italic bg-white dark:bg-gray-800 p-2.5 rounded-lg border border-rose-100 dark:border-rose-900 mt-0.5 whitespace-pre-wrap break-words [overflow-wrap:anywhere]">{{ trim($academicOfficeRemark) }}</p>
                                    @else
                                        <div class="flex items-center space-x-2 text-xs text-gray-600 dark:text-gray-500 bg-gray-200/60 dark:bg-gray-900/40 p-2.5 rounded-lg border border-dashed border-gray-200 dark:border-gray-700/60 mt-0.5">
                                            <svg class="w-4 h-4 text-gray-400 dark:text-gray-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 4.418 9 8z"></path></svg>
                                            <span class="italic font-normal">Remark not provided</span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endif

                        <!-- 4. DOAA Approval -->
                        @if($viewPerspective === 'doaa' && ($pts2->doaa_submitted_at || !is_null($pts2->doaa_approval)))
                            <div class="p-4 bg-emerald-50/70 dark:bg-emerald-950/40 border-l-4 border-emerald-500 rounded-xl space-y-2">
                                <div class="flex items-center justify-between font-bold text-emerald-900 dark:text-emerald-200 gap-2 sm:gap-4">
                                    <h5 class="text-s">Dean of Academic Affairs (DOAA)</h5>
                                    @if(!is_null($pts2->doaa_approval))
                                        <span class="font-bold text-xs whitespace-nowrap shrink-0 {{ $pts2->doaa_approval ? 'text-emerald-600' : 'text-red-600' }}">
                                            {{ $pts2->doaa_approval ? '✓ Approved' : '❌ Not Approved' }}
                                            @if($pts2->approved_by_authority)
                                                by: {{ $pts2->approved_by_authority }}
                                            @endif
                                        </span>
                                    @endif
                                </div>
                                <div class="text-xs text-gray-700 dark:text-gray-300">
                                    <strong>Student Comment:</strong>
                                    @if($pts2->doaa_student_comment)
                                        <p class="italic bg-white dark:bg-gray-800 p-2.5 rounded-lg border border-emerald-100 dark:border-emerald-900 mt-0.5 whitespace-pre-wrap break-words [overflow-wrap:anywhere]">{{ trim($pts2->doaa_student_comment) }}</p>
                                    @else
                                        <div class="flex items-center space-x-2 text-xs text-gray-600 dark:text-gray-500 bg-gray-200/60 dark:bg-gray-900/40 p-2.5 rounded-lg border border-dashed border-gray-200 dark:border-gray-700/60 mt-0.5">
                                            <svg class="w-4 h-4 text-gray-400 dark:text-gray-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 4.418 9 8z"></path></svg>
                                            <span class="italic font-normal">Comment not provided</span>
                                        </div>
                                    @endif
                                </div>
                                <div class="text-xs text-gray-700 dark:text-gray-300 pt-0.5">
                                    <strong>DOAA Remark:</strong>
                                    @if($pts2->doaa_confidential_remark)
                                        <p class="italic bg-white dark:bg-gray-800 p-2.5 rounded-lg border border-emerald-100 dark:border-emerald-900 mt-0.5 whitespace-pre-wrap break-words [overflow-wrap:anywhere]">{{ trim($pts2->doaa_confidential_remark) }}</p>
                                    @else
                                        <div class="flex items-center space-x-2 text-xs text-gray-600 dark:text-gray-500 bg-gray-200/60 dark:bg-gray-900/40 p-2.5 rounded-lg border border-dashed border-gray-200 dark:border-gray-700/60 mt-0.5">
                                            <svg class="w-4 h-4 text-gray-400 dark:text-gray-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 4.418 9 8z"></path></svg>
                                            <span class="italic font-normal">Remark not provided</span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endif

                    </div>
                </div>
            @endif

        </div>
    </div>
</x-app-layout>

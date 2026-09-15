<x-app-layout>
    @php
        $formPrefix = isset($student) && $student->isPhd() ? 'PTS' : 'MSRTS';
    @endphp
    <div class="py-6" x-data="pts2ReviewForm()">
        <div class="max-w-5xl mx-auto px-2 sm:px-6 lg:px-8 space-y-4">

            <!-- Top Back to Dashboard Button -->
            <div>
                <x-back-to-dashboard-button />
            </div>

            <!-- Page Header Card -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-5 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div class="space-y-1">
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white leading-tight">
                        {{ __("Review & Endorse {$formPrefix}-2 Synopsis Form") }}
                    </h2>
                </div>
            </div>

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

            <form action="{{ route('faculty.pts2.update', $pts2->id) }}" method="POST" enctype="multipart/form-data" class="space-y-6" @submit="clearDraft()">
                @csrf
                @method('PUT')

                <!-- Section 1: Read-Only Student Information -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6" x-data="{ showStudentInfo: true }">
                    <div class="flex items-center justify-between border-b border-gray-100 dark:border-gray-700 pb-3 mb-4 cursor-pointer select-none" @click="showStudentInfo = !showStudentInfo">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white flex items-center gap-2">
                            <span>1. Student Information</span>
                        </h3>
                        <button type="button" class="p-1.5 rounded-lg text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 transition" @click.stop="showStudentInfo = !showStudentInfo">
                            <svg class="w-5 h-5 transform transition-transform duration-200" :class="{ 'rotate-180': !showStudentInfo }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                    </div>

                    <div x-show="showStudentInfo" x-transition class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <x-readonly-label value="Student Name" />
                            <x-readonly-input :value="$studentUser->name" />
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
                            <x-readonly-label value="Date of Confirmation" />
                            <x-readonly-input :value="$student->date_confirmation ? \Carbon\Carbon::parse($student->date_confirmation)->format('d-m-Y') : 'N/A'" />
                        </div>

                        <div>
                            <x-readonly-label value="Open Seminar Date" />
                            <x-readonly-input :value="$thesis->getOpenSeminarDate()?->format('d-m-Y') ?? 'N/A'" />
                        </div>

                        <div>
                            <x-readonly-label class="flex items-center">
                                <span>Course Credits Required</span>
                                <x-info-button text="Minimum course credits required for the degree program." />
                            </x-readonly-label>
                            <x-readonly-input :value="$student->course_credits_required ?? 'N/A'" />
                        </div>

                        <div>
                            <x-readonly-label class="flex items-center">
                                <span>Course Credits Earned (From System)</span>
                                <x-info-button text="Course credits earned including coursework, seminars, and research credits." />
                            </x-readonly-label>
                            <x-readonly-input :value="$student->course_credits_earned ?? 'N/A'" />
                        </div>

                        <div>
                            <x-readonly-label class="flex items-center">
                                <span>Course Credits Submitted by Student</span>
                                <x-info-button text="Course credits earned including coursework, seminars, and research credits." />
                            </x-readonly-label>
                            <x-readonly-input :value="$pts2->course_credits_student" />
                        </div>

                        <div>
                            <x-readonly-label value="Primary Email Address" />
                            <x-readonly-input :value="$studentUser->email" />
                        </div>

                        <div>
                            <x-readonly-label value="Recent Contact No." />
                            <x-readonly-input :value="$pts2->getFormattedRecentPhoneNumber()" />
                        </div>

                        <div>
                            <x-readonly-label value="Alternate Contact No." />
                            <x-readonly-input :value="$pts2->getFormattedAlternatePhoneNumber()" />
                        </div>

                        <div>
                            <x-readonly-label value="Alternate Email Address" />
                            <x-readonly-input :value="$pts2->alternate_email ?? 'N/A'" />
                        </div>

                        <div>
                            <x-readonly-label value="Date of Submission" />
                            <x-readonly-input :value="$pts2->created_at ? $pts2->created_at->format('d-m-Y') : 'N/A'" />
                        </div>

                        <div class="md:col-span-3">
                            <x-readonly-label value="Current Residential / Correspondence Address" />
                            <p class="p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg border border-gray-200 dark:border-gray-600 text-sm text-gray-800 dark:text-gray-200 whitespace-pre-wrap">{{ $pts2->current_address }}</p>
                        </div>
                    </div>
                </div>

                <!-- Section 2: Name of Thesis -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 space-y-3">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white border-b border-gray-100 dark:border-gray-700 pb-2">
                        2. Name of Thesis
                    </h3>
                    <div>
                        <x-form-label value="Thesis Title" :required="true" />
                        <x-form-input type="text" name="thesis_title" required :value="old('thesis_title', $pts2->effective_thesis_title)" />
                    </div>
                </div>

                <!-- Section 3: Further Certified That (Supervisor Edit & Certification) -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 space-y-4">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white border-b border-gray-100 dark:border-gray-700 pb-2">
                        3. Further Certified That (Supervisor Verification)
                    </h3>

                    <div class="space-y-4">
                        <!-- Declaration 1 -->
                        <div class="flex items-start gap-3 p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg border border-gray-200 dark:border-gray-600">
                            <input type="checkbox" id="cert_prima_facie_case" name="cert_prima_facie_case" value="1" x-model="certPrimaFacie" class="mt-1 rounded text-blue-600 focus:ring-blue-500">
                            <label for="cert_prima_facie_case" class="text-sm font-medium text-gray-800 dark:text-gray-200">
                                <strong>1.</strong> There is a prima facie case for consideration of the thesis. <span class="text-red-500">*</span>
                            </label>
                        </div>

                        <!-- Declaration 2 -->
                        <div class="flex items-start gap-3 p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg border border-gray-200 dark:border-gray-600">
                            <input type="checkbox" id="cert_no_prior_degree_submission" name="cert_no_prior_degree_submission" value="1" x-model="certNoPriorDegree" class="mt-1 rounded text-blue-600 focus:ring-blue-500">
                            <label for="cert_no_prior_degree_submission" class="text-sm font-medium text-gray-800 dark:text-gray-200">
                                <strong>2.</strong> To the best of our knowledge the thesis does not include any work which has, at any time, previously, been submitted for the award of a degree except to the extent of point 3 below. <span class="text-red-500">*</span>
                            </label>
                        </div>

                        <!-- Declaration 3: Collaborative Work -->
                        <div class="p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg border border-gray-200 dark:border-gray-600 space-y-3">
                            <label class="block text-sm font-medium text-gray-800 dark:text-gray-200">
                                <strong>3.</strong> Does any section of the Thesis relate to collaborative work? <span class="text-red-500">*</span>
                            </label>
                            
                            <div class="flex items-center gap-6">
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="radio" name="collaborative_work_status" value="0" x-model="collaborativeWorkStatus" class="text-blue-600 focus:ring-blue-500">
                                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300">No</span>
                                </label>
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="radio" name="collaborative_work_status" value="1" x-model="collaborativeWorkStatus" class="text-blue-600 focus:ring-blue-500">
                                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Yes (Collaborative work exists)</span>
                                </label>
                            </div>

                            <div x-show="collaborativeWorkStatus == '1'" x-transition class="pt-2">
                                <label class="block text-xs font-semibold uppercase text-gray-600 dark:text-gray-400 mb-1">
                                    Collaborative Work Details <span class="text-red-500">*</span>
                                </label>
                                <textarea name="collaborative_work_details" rows="3" x-model="collaborativeWorkDetails" :required="collaborativeWorkStatus == '1'" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-blue-500 focus:border-blue-500" placeholder="Mention briefly the section(s) and details relating to collaborative work"></textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Section 4: Synopsis Document Inspection & Replacement -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 space-y-6">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white border-b border-gray-100 dark:border-gray-700 pb-2">
                        4. Submitted Document(s)
                    </h3>

                    <div class="p-4 bg-gray-50 dark:bg-gray-700/50 rounded-xl space-y-3">
                        <div class="flex justify-between items-center">
                            <label class="block text-sm font-bold text-gray-800 dark:text-gray-200">
                                Final PhD Synopsis Report (.pdf, .doc, .docx)
                            </label>
                            <span class="text-[11px] text-gray-400">Max 10 MB</span>
                        </div>

                        @if($pts2->synopsis_report_doc_path)
                            <div class="p-2.5 bg-emerald-50 dark:bg-emerald-900/30 border border-emerald-200 dark:border-emerald-800 rounded-lg flex items-center justify-between">
                                <span class="text-xs text-emerald-800 dark:text-emerald-300 font-semibold">Existing Submitted File:</span>
                                <a href="{{ route('pts.document.serve', ['pts2', $pts2->id, 'synopsis_report_doc_path']) }}" target="_blank" class="px-2.5 py-1 bg-white dark:bg-gray-800 text-emerald-700 dark:text-emerald-300 border border-emerald-300 dark:border-emerald-700 rounded-md text-xs font-bold shadow-sm hover:bg-emerald-100 flex items-center">
                                    📄 View Existing File
                                </a>
                            </div>
                        @endif

                        <div x-show="!fileStates.synopsis.name">
                            <label class="block text-xs font-semibold text-gray-500 mb-1">Replace File</label>
                            <input type="file" id="synopsisInput" name="synopsis_report_doc" accept=".pdf,.doc,.docx" @change="handleFileSelect($event, 'synopsis')" class="w-full text-xs text-gray-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100">
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
                                    <div class="text-[10px] text-emerald-800 dark:text-emerald-300 font-bold uppercase">New File Selected</div>
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
                </div>

                <!-- Section 5: Supervisor Evaluation & Action -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 space-y-6">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white border-b border-gray-100 dark:border-gray-700 pb-2">
                        5. Supervisor Evaluation & Action
                    </h3>

                    <!-- Item 1 & 2: Recommendation Status & Dynamic Mandatory Remark -->
                    <x-recommendation-block 
                        label="Recommendation Status for Student Synopsis Submission"
                        name="recommendation"
                        model="recommendation"
                        remark-name="main_supervisor_confidential_remark"
                        remark-model="confidentialRemark"
                        :remark-value="old('main_supervisor_confidential_remark', $pts2->main_supervisor_confidential_remark)"
                        form-type="pts2"
                        role="main_supervisor"
                        positive-desc="Recommend the student's PTS-2 synopsis submission for forwarding to the next stage in the academic pipeline."
                        negative-desc="Do not recommend the submission in its present form without further improvements." />

                    <!-- Item 3: Student Comments Textarea -->
                    <x-student-comment-input 
                        name="main_supervisor_student_comment" 
                        model="studentComment" 
                        form-type="pts2"
                        role="main_supervisor"
                        :value="old('main_supervisor_student_comment', $pts2->main_supervisor_student_comment)" />

                </div>

                <!-- Section 7: Declaration -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-indigo-100 dark:border-indigo-900/50 p-6 space-y-4">
                    <h3 class="text-lg font-bold text-indigo-900 dark:text-indigo-300 border-b border-indigo-100 dark:border-indigo-900/50 pb-2 flex items-center">
                        7. Declaration
                    </h3>

                    <label class="p-4 rounded-xl border-2 transition-all flex items-start space-x-3 cursor-pointer"
                           :class="undertaking ? 'border-emerald-500 bg-emerald-50/60 dark:bg-emerald-950/30' : 'border-indigo-200 dark:border-indigo-800 bg-indigo-50/30 dark:bg-indigo-950/20 hover:border-indigo-400'">
                        <input type="checkbox" 
                               name="undertaking" 
                               value="1" 
                               x-model="undertaking" 
                               required
                               class="mt-1 text-emerald-600 focus:ring-emerald-500 rounded w-5 h-5 cursor-pointer">
                        <div class="space-y-1">
                            <p class="text-xs text-gray-700 dark:text-gray-300 leading-relaxed font-medium">
                                I hereby confirm that I have reviewed the submitted synopsis report, verified the certifications, and provided my evaluation.
                            </p>
                        </div>
                    </label>

                    <div x-show="!undertaking" class="text-xs text-amber-600 dark:text-amber-400 flex items-center font-medium pl-1">
                        <svg class="w-4 h-4 mr-1.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <span>Please confirm the declaration checkbox above to enable submission.</span>
                    </div>
                </div>

                <!-- Submit & Revert Action Buttons Bar -->
                <div class="flex flex-col-reverse sm:flex-row items-center sm:justify-end gap-3 pt-4">
                    <!-- Revert Button -->
                    <button type="button" 
                            @click="showRevertModal = true" 
                            class="w-full sm:w-auto justify-center bg-red-600 hover:bg-red-700 text-white text-base font-bold px-6 py-3 rounded-xl shadow-lg transition flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"></path>
                        </svg>
                        Revert Form
                    </button>

                    <!-- Main Submit Button -->
                    <button type="submit" 
                            :disabled="!undertaking || !certPrimaFacie || !certNoPriorDegree" 
                            :class="(!undertaking || !certPrimaFacie || !certNoPriorDegree) ? 'opacity-50 cursor-not-allowed bg-gray-400 dark:bg-gray-600' : 'bg-emerald-600 hover:bg-emerald-700'" 
                            class="w-full sm:w-auto justify-center text-white text-base font-bold px-8 py-3 rounded-xl shadow-lg transition flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                        </svg>
                        Submit &amp; Forward
                    </button>
                </div>
            </form>

            <!-- Revert Confirmation Pop-Up Modal -->
            <x-revert-modal 
                show="showRevertModal" 
                :action="route('pts2.revert', $pts2->id)" 
                :title="__('Revert :prefix-2 Form to Student', ['prefix' => $formPrefix])"
                subtitle="Send form back to student for required changes"
                onSubmit="clearDraft()"
            />

        </div>
    </div>

    <script>
        function pts2ReviewForm() {
            const userId = @js(auth()->id());
            const thesisId = @js($thesis->id);
            const formId = @js($pts2->id);
            const draftKey = 'pts2_supervisor_draft_user_' + userId + '_thesis_' + thesisId + '_form_' + formId;

            let savedDraft = {};
            try {
                savedDraft = JSON.parse(sessionStorage.getItem(draftKey) || '{}');
            } catch (e) {}

            return {
                showRevertModal: false,
                undertaking: savedDraft.undertaking !== undefined ? savedDraft.undertaking : false,
                certPrimaFacie: savedDraft.certPrimaFacie !== undefined ? savedDraft.certPrimaFacie : {{ old('cert_prima_facie_case', $pts2->getEffectiveCertPrimaFacieCase()) ? 'true' : 'false' }},
                certNoPriorDegree: savedDraft.certNoPriorDegree !== undefined ? savedDraft.certNoPriorDegree : {{ old('cert_no_prior_degree_submission', $pts2->getEffectiveCertNoPriorDegreeSubmission()) ? 'true' : 'false' }},
                collaborativeWorkStatus: savedDraft.collaborativeWorkStatus !== undefined ? String(savedDraft.collaborativeWorkStatus) : '{{ old('collaborative_work_status', $pts2->getEffectiveCollaborativeWorkStatus() ? '1' : '0') }}',
                collaborativeWorkDetails: @js(old('collaborative_work_details')) || savedDraft.collaborativeWorkDetails || @js($pts2->getEffectiveCollaborativeWorkDetails() ?? ''),
                recommendation: @js(old('recommendation')) || savedDraft.recommendation || '{{ $pts2->main_supervisor_recommendation !== null ? ($pts2->main_supervisor_recommendation ? '1' : '0') : '1' }}',
                confidentialRemark: @js(old('main_supervisor_confidential_remark')) || savedDraft.confidentialRemark || @js($pts2->main_supervisor_confidential_remark ?? ''),
                studentComment: @js(old('main_supervisor_student_comment')) || savedDraft.studentComment || @js($pts2->main_supervisor_student_comment ?? ''),

                init() {
                    const watchFields = ['undertaking', 'certPrimaFacie', 'certNoPriorDegree', 'collaborativeWorkStatus', 'collaborativeWorkDetails', 'recommendation', 'confidentialRemark', 'studentComment'];
                    watchFields.forEach(field => {
                        this.$watch(field, () => this.saveDraft());
                    });
                },

                saveDraft() {
                    try {
                        sessionStorage.setItem(draftKey, JSON.stringify({
                            undertaking: this.undertaking,
                            certPrimaFacie: this.certPrimaFacie,
                            certNoPriorDegree: this.certNoPriorDegree,
                            collaborativeWorkStatus: this.collaborativeWorkStatus,
                            collaborativeWorkDetails: this.collaborativeWorkDetails,
                            recommendation: this.recommendation,
                            confidentialRemark: this.confidentialRemark,
                            studentComment: this.studentComment,
                        }));
                    } catch (e) {}
                },

                maxSizes: {
                    synopsis: 10
                },

                fileErrors: {
                    synopsis: ''
                },

                fileStates: {
                    synopsis: { name: '', size: '', url: null }
                },

                handleFileSelect(event, key) {
                    const file = event.target.files[0];
                    if (!file) return false;

                    const sizeInMB = file.size / (1024 * 1024);
                    const limitMB = this.maxSizes[key] || 10;

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
                },

                clearDraft() {
                    try {
                        sessionStorage.removeItem(draftKey);
                    } catch (e) {}
                }
            };
        }
    </script>
</x-app-layout>

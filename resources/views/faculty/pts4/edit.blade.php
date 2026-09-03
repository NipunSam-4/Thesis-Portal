<x-app-layout>
    @php
        $formPrefix = isset($student) && $student->isPhd() ? 'PTS' : 'MSRTS';
    @endphp
    <div class="py-6" x-data="pts4ReviewForm()">
        <div class="max-w-5xl mx-auto px-2 sm:px-6 lg:px-8 space-y-4">

            <!-- Top Back to Dashboard Button -->
            <div>
                <x-back-to-dashboard-button />
            </div>

            <!-- Page Header Card -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-5 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div class="space-y-1">
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white leading-tight">
                        {{ __("Review & Evaluate {$formPrefix}-4 Thesis Form") }}
                    </h2>
                </div>
            </div>

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

            <!-- Main Supervisor Review & Modification Form -->
            <form action="{{ route('faculty.pts4.update', $pts4->id) }}" method="POST" enctype="multipart/form-data" class="space-y-6" @submit="clearDraft()">
                @csrf
                @method('PUT')

                <!-- Section 1: Read-Only Student Information -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4 border-b border-gray-100 dark:border-gray-700 pb-2">
                        1. Student Information
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <x-readonly-label value="Student Name (English)" />
                            <x-readonly-input :value="$studentUser->name" />
                        </div>

                        <div>
                            <x-readonly-label value="Student Name (Hindi)" />
                            <x-readonly-input :value="$pts4->hindi_name ?? ($student->hindi_name ?? 'N/A')" class="font-hindi" />
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
                            <x-readonly-label value="Program" />
                            <x-readonly-input :value="$student->isPhd() ? 'Ph.D.' : 'M.S. (Research)'" />
                        </div>

                        <div>
                            <x-readonly-label value="Admission Category" />
                            <x-readonly-input :value="$student->admission_category ?? 'N/A'" />
                        </div>

                        <div>
                            <x-readonly-label value="Primary Email Address" />
                            <x-readonly-input :value="$studentUser->email" />
                        </div>

                        <div>
                            <x-readonly-label value="Recent Contact No." />
                            <x-readonly-input :value="$student->phone_number ? (($student->phone_country_code ?: '+91') . ' ' . $student->phone_number) : 'N/A'" />
                        </div>

                        <div>
                            <x-readonly-label value="Alternate Contact No." />
                            <x-readonly-input :value="$pts4->getFormattedAlternatePhoneNumber()" />
                        </div>

                        <div>
                            <x-readonly-label value="Alternate Email Address" />
                            <x-readonly-input :value="$pts4->alternate_email ?: ($student->alternate_email ?: 'N/A')" />
                        </div>

                        <div class="md:col-span-3">
                            <x-readonly-label value="Main Supervisor" />
                            <x-readonly-input :value="$student?->mainSupervisors?->pluck('name')->join(', ') ?: ($student?->supervisors?->first()?->name ?? 'Not Assigned')" />
                        </div>

                        <div class="md:col-span-3">
                            <x-readonly-label value="Co-Supervisor(s)" />
                            <x-readonly-input :value="$student?->coSupervisors?->pluck('name')->join(', ') ?: 'None'" />
                        </div>

                        <div class="md:col-span-3">
                            <x-readonly-label value="External Supervisor(s)" />
                            @php
                                $extSupText = ($student && $student->externalSupervisors->isNotEmpty())
                                    ? $student->externalSupervisors->map(fn($s) => $s->name . ($s->externalSupervisorProfile?->affiliated_institute ? ' (' . $s->externalSupervisorProfile->affiliated_institute . ')' : ''))->join(', ')
                                    : 'None';
                            @endphp
                            <x-readonly-input :value="$extSupText" />
                        </div>

                        <div>
                            <x-readonly-label value="Date of Submission" />
                            <x-readonly-input :value="$pts4->created_at ? $pts4->created_at->format('d-m-Y') : 'N/A'" />
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
                        <x-form-input type="text" name="thesis_title" required :value="old('thesis_title', $pts4->effective_thesis_title)" />
                    </div>
                </div>

                <!-- Section 3: Submitted Document(s) -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 space-y-6">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white border-b border-gray-100 dark:border-gray-700 pb-2">
                        3. Submitted Document(s)
                    </h3>

                    <div class="p-4 bg-gray-50 dark:bg-gray-700/50 rounded-xl space-y-3">
                        <div class="flex justify-between items-center">
                            <label class="block text-sm font-bold text-gray-800 dark:text-gray-200">
                                Final {{ ($student && $student->isPhd()) ? 'PhD' : 'MS(R)' }} Thesis Document (.pdf, .doc, .docx)
                            </label>
                            <span class="text-[11px] text-gray-400">Max 100 MB</span>
                        </div>

                        @php
                            $effectiveDocPath = $pts4->getEffectiveThesisDocPath();
                            $effectiveDocField = $pts4->getEffectiveThesisDocField();
                        @endphp
                        @if($effectiveDocPath)
                            <div class="p-2.5 bg-emerald-50 dark:bg-emerald-900/30 border border-emerald-200 dark:border-emerald-800 rounded-lg flex items-center justify-between">
                                <span class="text-xs text-emerald-800 dark:text-emerald-300 font-semibold">Existing Submitted File:</span>
                                <a href="{{ route('pts.document.serve', ['pts4', $pts4->id, $effectiveDocField]) }}" target="_blank" class="px-2.5 py-1 bg-white dark:bg-gray-800 text-emerald-700 dark:text-emerald-300 border border-emerald-300 dark:border-emerald-700 rounded-md text-xs font-bold shadow-sm hover:bg-emerald-100 flex items-center">
                                    📄 View Existing File
                                </a>
                            </div>
                        @endif

                        <div x-show="!fileStates.thesis.name">
                            <label class="block text-xs font-semibold text-gray-500 mb-1">Replace File</label>
                            <input type="file" id="thesisInput" name="thesis_doc" accept=".pdf,.doc,.docx" @change="handleFileSelect($event, 'thesis')" class="w-full text-xs text-gray-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100">
                        </div>

                        <!-- File Size Error Alert -->
                        <div x-show="fileErrors.thesis" x-cloak class="mt-2 text-xs font-bold text-red-600 dark:text-red-400 bg-red-50 dark:bg-red-900/30 p-2.5 rounded-lg border border-red-200 dark:border-red-800">
                            <span x-text="fileErrors.thesis"></span>
                        </div>

                        <!-- Selected File Preview -->
                        <div x-show="fileStates.thesis.name" x-cloak class="p-3 bg-emerald-50 dark:bg-emerald-900/30 border border-emerald-200 dark:border-emerald-800 rounded-xl flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                            <div class="flex items-center space-x-3 min-w-0 truncate">
                                <div class="p-2 bg-emerald-100 dark:bg-emerald-800 rounded-lg text-emerald-700 dark:text-emerald-300 shrink-0">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                </div>
                                <div class="truncate">
                                    <div class="text-[10px] text-emerald-800 dark:text-emerald-300 font-bold uppercase">New File Selected</div>
                                    <div class="text-sm font-semibold text-gray-900 dark:text-white truncate" x-text="fileStates.thesis.name"></div>
                                    <div class="text-[11px] text-gray-500" x-text="fileStates.thesis.size"></div>
                                </div>
                            </div>
                            <div class="flex items-center space-x-2 shrink-0 justify-end sm:justify-start">
                                <a :href="fileStates.thesis.url" target="_blank" class="px-2.5 py-1 bg-white dark:bg-gray-800 text-emerald-700 dark:text-emerald-300 border border-emerald-300 dark:border-emerald-700 rounded-lg text-xs font-bold shadow-sm hover:bg-emerald-100 flex items-center">
                                    📄 View File
                                </a>
                                <button type="button" @click="clearFile('thesis', 'thesisInput')" class="px-2.5 py-1 bg-white dark:bg-gray-800 text-red-600 dark:text-red-400 border border-red-200 dark:border-red-800 rounded-lg text-xs font-bold shadow-sm hover:bg-red-50 dark:hover:bg-red-900/30 flex items-center">
                                    <svg class="w-3.5 h-3.5 mr-1 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                    Delete File
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Section 4: Supervisor Evaluation & Action -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 space-y-6">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white border-b border-gray-100 dark:border-gray-700 pb-2">
                        4. Supervisor Evaluation & Action
                    </h3>

                    <!-- Item 1 & 2: Recommendation Status & Dynamic Mandatory Remark -->
                    <x-recommendation-block 
                        label="Recommendation Status for Student Thesis Submission"
                        name="recommendation"
                        model="recommendation"
                        remark-name="main_supervisor_confidential_remark"
                        remark-model="confidentialRemark"
                        :remark-value="old('main_supervisor_confidential_remark', $pts4->main_supervisor_confidential_remark)"
                        form-type="pts4"
                        role="main_supervisor"
                        positive-desc="Recommend the student's thesis submission for forwarding to the next stage in the academic pipeline."
                        negative-desc="Do not recommend the submission in its present form without further improvements." />

                    <!-- Item 3: Student Comments Textarea -->
                    <x-student-comment-input 
                        name="main_supervisor_student_comment" 
                        model="studentComment" 
                        :value="old('main_supervisor_student_comment', $pts4->main_supervisor_student_comment)" />
                </div>

                <!-- Section 5: Declaration -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-indigo-100 dark:border-indigo-900/50 p-6 space-y-4">
                    <h3 class="text-lg font-bold text-indigo-900 dark:text-indigo-300 border-b border-indigo-100 dark:border-indigo-900/50 pb-2 flex items-center">
                        5. Declaration
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
                                I hereby confirm that I have reviewed the submitted thesis document and provided my evaluation and recommendation.
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
                            :disabled="!undertaking" 
                            :class="(!undertaking) ? 'opacity-50 cursor-not-allowed bg-gray-400 dark:bg-gray-600' : 'bg-emerald-600 hover:bg-emerald-700'" 
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
                :action="route('pts4.revert', $pts4->id)" 
                :title="__('Revert :prefix-4 Form to Student', ['prefix' => $formPrefix])"
                subtitle="Send form back to student for required changes"
                onSubmit="clearDraft()"
            />
        </div>
    </div>

    <script>
        function pts4ReviewForm() {
            const userId = @js(auth()->id());
            const thesisId = @js($thesis->id);
            const formId = @js($pts4->id);
            const draftKey = 'pts4_supervisor_draft_user_' + userId + '_thesis_' + thesisId + '_form_' + formId;

            let savedDraft = {};
            try {
                savedDraft = JSON.parse(sessionStorage.getItem(draftKey) || '{}');
            } catch (e) {}

            return {
                undertaking: savedDraft.undertaking !== undefined ? savedDraft.undertaking : false,
                recommendation: @js(old('recommendation')) || savedDraft.recommendation || @js(!is_null($pts4->main_supervisor_recommendation) ? ($pts4->main_supervisor_recommendation ? '1' : '0') : '1'),
                confidentialRemark: @js(old('main_supervisor_confidential_remark')) || savedDraft.confidentialRemark || @js($pts4->main_supervisor_confidential_remark ?? ''),
                studentComment: @js(old('main_supervisor_student_comment')) || savedDraft.studentComment || @js($pts4->main_supervisor_student_comment ?? ''),
                showRevertModal: false,

                init() {
                    const watchFields = [
                        'undertaking',
                        'recommendation',
                        'confidentialRemark',
                        'studentComment',
                    ];
                    watchFields.forEach(field => {
                        this.$watch(field, () => this.saveDraft());
                    });
                },

                saveDraft() {
                    try {
                        sessionStorage.setItem(draftKey, JSON.stringify({
                            undertaking: this.undertaking,
                            recommendation: this.recommendation,
                            confidentialRemark: this.confidentialRemark,
                            studentComment: this.studentComment,
                        }));
                    } catch (e) {}
                },

                maxSizes: {
                    thesis: 100 // 100 MB limit
                },

                fileErrors: {
                    thesis: ''
                },

                fileStates: {
                    thesis: { name: '', size: '', url: null }
                },

                handleFileSelect(event, key) {
                    const file = event.target.files[0];
                    if (!file) return false;

                    const sizeInMB = file.size / (1024 * 1024);
                    const limitMB = this.maxSizes[key] || 100;

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

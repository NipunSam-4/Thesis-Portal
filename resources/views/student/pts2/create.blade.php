<x-app-layout>
    @php
        $formPrefix = isset($student) && $student->isPhd() ? 'PTS' : 'MSRTS';
        $isReverted = isset($pts2Form) && $pts2Form->status === 'reverted';
    @endphp
    <div class="py-6" x-data="pts2Form()">
        <div class="max-w-5xl mx-auto px-2 sm:px-6 lg:px-8 space-y-4">

            <!-- Top Back to Dashboard Button -->
            <div>
                <x-back-to-dashboard-button />
            </div>

            <!-- Page Header Card -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-5 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div class="space-y-1">
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white leading-tight">
                        {{ $isReverted ? __("Edit & Resubmit {$formPrefix}-2 Synopsis Form") : __("Submit {$formPrefix}-2 Synopsis Form") }}
                    </h2>
                </div>
                @if($isReverted)
                    <div class="flex items-center space-x-3 shrink-0">
                        <span class="px-3.5 py-1.5 bg-amber-100 dark:bg-amber-900/60 text-amber-800 dark:text-amber-300 text-xs font-extrabold rounded-full uppercase tracking-wider flex items-center shadow-xs border border-amber-200 dark:border-amber-800">
                            ⚠️ Reverted
                        </span>
                    </div>
                @endif
            </div>

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

            <form action="{{ route('student.pts2.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6" @submit="clearDraft()">
                @csrf

                @php
                    $showModified = $isReverted && $pts2Form->reverted_by_role !== 'main_supervisor';
                @endphp

                @if($isReverted)
                    <div class="p-4 bg-amber-50 dark:bg-amber-950/40 border-l-4 border-amber-500 rounded-xl space-y-2">
                        <div class="flex items-center space-x-2 text-amber-900 dark:text-amber-200">
                            <svg class="w-5 h-5 text-amber-600 dark:text-amber-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                            </svg>
                            <span class="font-bold text-sm">Form Reverted by {{ $pts2Form->getRevertedByRoleLabel() }}</span>
                        </div>
                        @if($pts2Form->reversion_comment)
                            <div class="text-xs text-gray-700 dark:text-gray-300">
                                <strong>Reversion Comment:</strong>
                                <p class="italic bg-white dark:bg-gray-800 p-2.5 rounded-lg border border-amber-200 dark:border-amber-900 mt-1 whitespace-pre-wrap break-words [overflow-wrap:anywhere]">{{ trim($pts2Form->reversion_comment) }}</p>
                            </div>
                        @endif
                    </div>
                @endif

                <!-- Section 1: Pre-filled Student Details (Read-Only) -->
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
                            <x-readonly-input :value="$user->name" />
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
                            <x-readonly-input :value="is_numeric($student->course_credits_required) ? ($student->course_credits_required == (int)$student->course_credits_required ? (int)$student->course_credits_required : $student->course_credits_required) : 'N/A'" />
                        </div>
                    </div>
                </div>

                <!-- Section 2: Name of Thesis -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 space-y-3">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white border-b border-gray-100 dark:border-gray-700 pb-2">
                        2. Name of Thesis
                    </h3>
                    <div>
                        <x-form-label class="flex items-center gap-2">
                            <span>Thesis Title <span class="text-red-500">*</span></span>
                            @if($showModified && $pts2Form->main_supervisor_thesis_title && $pts2Form->main_supervisor_thesis_title !== $pts2Form->thesis_title)
                                <x-modified-badge 
                                    :old-value="$pts2Form->thesis_title ?: ($thesis->title ?? 'N/A')" 
                                    :new-value="$pts2Form->main_supervisor_thesis_title" />
                            @endif
                        </x-form-label>
                        <x-form-input type="text" name="thesis_title" required x-model="thesisTitle" placeholder="Enter full title of thesis" />
                    </div>
                </div>

                <!-- Section 3: Contact & Course Details -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 space-y-4">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white border-b border-gray-100 dark:border-gray-700 pb-2">
                        3. Contact &amp; Course Details
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="md:col-span-2">
                            <x-form-label value="Current Residential / Correspondence Address" :required="true" />
                            <textarea name="current_address" required rows="3" x-model="currentAddress" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-blue-500 focus:border-blue-500" placeholder="Enter your complete current address"></textarea>
                        </div>

                        <div>
                            <div class="inline-flex items-center gap-1">
                                <x-input-label value="Recent Contact No." />
                                <span class="text-red-500">*</span>
                            </div>
                            <x-text-input id="recent_phone_number" 
                                          name="recent_phone_number" 
                                          type="tel" 
                                          inputmode="numeric"
                                          oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                                          x-model="recentPhone" 
                                          required 
                                          class="mt-1 w-full dark:bg-gray-900 dark:text-gray-200 dark:border-gray-700" 
                                          placeholder="Enter your current phone no" />
                            <input type="hidden" id="recent_phone_country_code" name="recent_phone_country_code"
                                :value="recentPhoneCountryCode">
                            <input type="hidden" id="recent_phone_iso2" name="recent_phone_iso2"
                                :value="recentPhoneIso2">
                            <x-input-error :messages="$errors->get('recent_phone_number')" class="mt-2" />
                        </div>

                        <div>
                            <div class="inline-flex items-center gap-1">
                                <x-input-label value="Alternate Contact No. (Optional)" />
                            </div>
                            <x-text-input id="alternate_phone_number" 
                                          name="alternate_phone_number" 
                                          type="tel" 
                                          inputmode="numeric"
                                          oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                                          x-model="alternatePhone" 
                                          class="mt-1 w-full dark:bg-gray-900 dark:text-gray-200 dark:border-gray-700" 
                                          placeholder="Optional phone number" />
                            <input type="hidden" id="alternate_phone_country_code" name="alternate_phone_country_code"
                                :value="alternatePhoneCountryCode">
                            <input type="hidden" id="alternate_phone_iso2" name="alternate_phone_iso2"
                                :value="alternatePhoneIso2">
                            <x-input-error :messages="$errors->get('alternate_phone_number')" class="mt-2" />
                        </div>

                        <div>
                            <x-form-label value="Alternate Email Address (Other than Institute ID) (Optional)" />
                            <x-form-input type="email" name="alternate_email" x-model="alternateEmail" placeholder="e.g. personal.email@gmail.com" />
                        </div>

                        <div>
                            <x-form-label class="flex items-center">
                                <span>Total Course Credits Earned <span class="text-red-500">*</span></span>
                                <x-info-button text="Course credits earned including coursework, seminars, and research credits." />
                            </x-form-label>
                            <x-form-input type="number" step="0.5" min="0" name="course_credits_student" required x-model="courseCredits" @wheel="$event.target.blur()" onwheel="this.blur()" placeholder="e.g. 16.0" />
                        </div>
                    </div>
                </div>

                <!-- Section 4: Further Certified That -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 space-y-4">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white border-b border-gray-100 dark:border-gray-700 pb-2">
                        4. Further Certified That
                    </h3>

                    <div class="space-y-4">
                        <!-- Declaration 1 -->
                        <div class="flex items-start gap-3 p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                            <input type="checkbox" id="cert_prima_facie_case" name="cert_prima_facie_case" value="1" required x-model="certPrimaFacie" class="mt-1 rounded text-blue-600 focus:ring-blue-500">
                            <label for="cert_prima_facie_case" class="text-sm font-medium text-gray-800 dark:text-gray-200 flex items-center gap-2">
                                <span><strong>1.</strong> There is a prima facie case for consideration of the thesis. <span class="text-red-500">*</span></span>
                                @if($showModified && $pts2Form->main_supervisor_cert_prima_facie_case !== null && (bool)$pts2Form->main_supervisor_cert_prima_facie_case !== (bool)$pts2Form->cert_prima_facie_case)
                                    <x-modified-badge 
                                        :old-value="$pts2Form->cert_prima_facie_case ? 'Certified' : 'Not Certified'" 
                                        :new-value="$pts2Form->main_supervisor_cert_prima_facie_case ? 'Certified' : 'Not Certified'" />
                                @endif
                            </label>
                        </div>

                        <!-- Declaration 2 -->
                        <div class="flex items-start gap-3 p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                            <input type="checkbox" id="cert_no_prior_degree_submission" name="cert_no_prior_degree_submission" value="1" required x-model="certNoPriorDegree" class="mt-1 rounded text-blue-600 focus:ring-blue-500">
                            <label for="cert_no_prior_degree_submission" class="text-sm font-medium text-gray-800 dark:text-gray-200 flex items-center gap-2">
                                <span><strong>2.</strong> To the best of our knowledge the thesis does not include any work which has, at any time, previously, been submitted for the award of a degree except to the extent of point 3 below. <span class="text-red-500">*</span></span>
                                @if($showModified && $pts2Form->main_supervisor_cert_no_prior_degree_submission !== null && (bool)$pts2Form->main_supervisor_cert_no_prior_degree_submission !== (bool)$pts2Form->cert_no_prior_degree_submission)
                                    <x-modified-badge 
                                        :old-value="$pts2Form->cert_no_prior_degree_submission ? 'Certified' : 'Not Certified'" 
                                        :new-value="$pts2Form->main_supervisor_cert_no_prior_degree_submission ? 'Certified' : 'Not Certified'" />
                                @endif
                            </label>
                        </div>

                        <!-- Declaration 3: Collaborative Work -->
                        <div class="p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg space-y-3">
                            <label class="block text-sm font-medium text-gray-800 dark:text-gray-200 flex items-center gap-2">
                                <span><strong>3.</strong> Does any section of the Thesis relate to collaborative work? <span class="text-red-500">*</span></span>
                                @if($showModified && $pts2Form->main_supervisor_collaborative_work_status !== null && (bool)$pts2Form->main_supervisor_collaborative_work_status !== (bool)$pts2Form->collaborative_work_status)
                                    <x-modified-badge 
                                        :old-value="$pts2Form->collaborative_work_status ? 'Yes' : 'No'" 
                                        :new-value="$pts2Form->main_supervisor_collaborative_work_status ? 'Yes' : 'No'" />
                                @endif
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

                            <div x-show="collaborativeWorkStatus === '1'" x-transition class="pt-2">
                                <label class="block text-xs font-semibold uppercase text-gray-600 dark:text-gray-400 mb-1 flex items-center gap-2">
                                    <span>Collaborative Work Details <span class="text-red-500">*</span></span>
                                    @if($showModified && $pts2Form->main_supervisor_collaborative_work_details !== null && $pts2Form->main_supervisor_collaborative_work_details !== $pts2Form->collaborative_work_details)
                                        <x-modified-badge 
                                            :old-value="$pts2Form->collaborative_work_details" 
                                            :new-value="$pts2Form->main_supervisor_collaborative_work_details" />
                                    @endif
                                </label>
                                <textarea name="collaborative_work_details" rows="3" x-model="collaborativeWorkDetails" :required="collaborativeWorkStatus === '1'" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-blue-500 focus:border-blue-500" placeholder="Mention briefly the section(s) and details relating to collaborative work"></textarea>
                            </div>
                        </div>

                        <div x-show="!certPrimaFacie || !certNoPriorDegree" class="text-xs text-amber-600 dark:text-amber-400 flex items-center font-medium pl-1">
                            <svg class="w-4 h-4 mr-1.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            <span>Please confirm both mandatory certifications above to enable submission.</span>
                        </div>
                    </div>
                </div>

                <!-- Section 5: Document Uploads -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 space-y-6">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white border-b border-gray-100 dark:border-gray-700 pb-2">
                        5. Document Uploads
                    </h3>

                    <div>
                        <div class="flex justify-between items-center mb-1">
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 flex items-center gap-2">
                                <span>Upload Final PhD Synopsis Report (.pdf, .doc, .docx) <span class="text-red-500">*</span></span>
                                @if($showModified && $pts2Form->main_supervisor_synopsis_report_doc_path && $pts2Form->main_supervisor_synopsis_report_doc_path !== $pts2Form->synopsis_report_doc_path)
                                    <x-modified-badge />
                                @endif
                            </label>
                            <span class="text-[11px] text-gray-400">Max 10 MB</span>
                        </div>

                        <div class="h-2"></div>

                        <div x-show="!fileStates.synopsis.name">
                            <input type="file" id="synopsisInput" name="synopsis_report_doc" accept=".pdf,.doc,.docx" :required="!fileStates.synopsis.name" @change="handleFileSelect($event, 'synopsis')" class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
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
                                    <div class="text-[10px] text-emerald-800 dark:text-emerald-300 font-bold uppercase">Selected Synopsis Document</div>
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

                <!-- Section 6: Declaration -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-indigo-100 dark:border-indigo-900/50 p-6 space-y-4">
                    <h3 class="text-lg font-bold text-indigo-900 dark:text-indigo-300 border-b border-indigo-100 dark:border-indigo-900/50 pb-2 flex items-center">
                        6. Declaration
                    </h3>

                    <label class="p-4 rounded-xl border-2 transition-all flex items-start space-x-3 cursor-pointer"
                           :class="studentDeclaration ? 'border-emerald-500 bg-emerald-50/60 dark:bg-emerald-950/30' : 'border-indigo-200 dark:border-indigo-800 bg-indigo-50/30 dark:bg-indigo-950/20 hover:border-indigo-400'">
                        <input type="checkbox" 
                               name="student_declaration" 
                               value="1" 
                               x-model="studentDeclaration" 
                               required
                               class="mt-1 text-emerald-600 focus:ring-emerald-500 rounded w-5 h-5 cursor-pointer">
                        <div class="space-y-1">
                            <p class="text-xs text-gray-700 dark:text-gray-300 leading-relaxed font-medium">
                                I hereby declare that all the information, course credits, declarations, and synopsis report provided in this PTS-2 submission are complete, accurate, and true to the best of my knowledge.
                            </p>
                        </div>
                    </label>

                    <div x-show="!studentDeclaration" class="text-xs text-amber-600 dark:text-amber-400 flex items-center font-medium pl-1">
                        <svg class="w-4 h-4 mr-1.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <span>Please confirm the declaration checkbox above to enable submission.</span>
                    </div>
                </div>

                <!-- Submit Action -->
                <div class="flex justify-center sm:justify-end pt-4">
                    <button type="submit" :disabled="isBlocked" :class="isBlocked ? 'opacity-50 cursor-not-allowed bg-gray-400' : 'bg-blue-600 hover:bg-blue-700'" class="w-full sm:w-auto text-white text-base font-bold px-8 py-3 rounded-xl shadow-lg transition">
                        {{ $isReverted ? "Resubmit {$formPrefix}-2 Synopsis Form" : "Submit {$formPrefix}-2 Synopsis Form" }}
                    </button>
                </div>
            </form>

        </div>
    </div>

    <!-- intl-tel-input Assets -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/intl-tel-input@18.2.1/build/css/intlTelInput.css">
    <script src="https://cdn.jsdelivr.net/npm/intl-tel-input@18.2.1/build/js/intlTelInput.min.js"></script>

    <script>
        function pts2Form() {
            const userId = @js(auth()->id());
            const thesisId = @js($thesis->id);
            const formId = @js(isset($pts2Form) && $pts2Form ? $pts2Form->id : null);
            const existingPts2 = @js(isset($pts2Form) ? $pts2Form : null);
            const draftKey = 'pts2_student_draft_user_' + userId + '_thesis_' + thesisId + (formId ? '_form_' + formId : '');

            let savedDraft = {};
            try {
                savedDraft = JSON.parse(sessionStorage.getItem(draftKey) || '{}');
            } catch (e) {}

            return {
                thesisTitle: @js(old('thesis_title')) || savedDraft.thesisTitle || @js(isset($pts2Form) ? $pts2Form->thesis_title : ($thesis->title ?? '')),
                currentAddress: @js(old('current_address')) || savedDraft.currentAddress || @js(isset($pts2Form) ? $pts2Form->current_address : ($student->current_address ?? '')),
                recentPhone: @js(old('recent_phone_number')) || savedDraft.recentPhone || @js(isset($pts2Form) ? $pts2Form->recent_phone_number : ($student->phone_number ?? '')),
                recentPhoneCountryCode: @js(old('recent_phone_country_code')) || savedDraft.recentPhoneCountryCode || @js(isset($pts2Form) ? ($pts2Form->recent_phone_country_code ?? ($student->phone_country_code ?? '+91')) : ($student->phone_country_code ?? '+91')),
                recentPhoneIso2: @js(old('recent_phone_iso2')) || savedDraft.recentPhoneIso2 || @js(isset($pts2Form) ? ($pts2Form->recent_phone_iso2 ?? ($student->phone_iso2 ?? 'in')) : ($student->phone_iso2 ?? 'in')),
                
                alternatePhone: @js(old('alternate_phone_number')) || savedDraft.alternatePhone || @js(isset($pts2Form) ? $pts2Form->alternate_phone_number : ($student->alternate_phone_number ?? '')),
                alternatePhoneCountryCode: @js(old('alternate_phone_country_code')) || savedDraft.alternatePhoneCountryCode || @js(isset($pts2Form) ? ($pts2Form->alternate_phone_country_code ?? ($student->alternate_phone_country_code ?? '+91')) : ($student->alternate_phone_country_code ?? '+91')),
                alternatePhoneIso2: @js(old('alternate_phone_iso2')) || savedDraft.alternatePhoneIso2 || @js(isset($pts2Form) ? ($pts2Form->alternate_phone_iso2 ?? ($student->alternate_phone_iso2 ?? 'in')) : ($student->alternate_phone_iso2 ?? 'in')),

                alternateEmail: @js(old('alternate_email')) || savedDraft.alternateEmail || @js(isset($pts2Form) ? $pts2Form->alternate_email : ($student->alternate_email ?? '')),
                courseCredits: @js(old('course_credits_student')) || savedDraft.courseCredits || @js(isset($pts2Form) ? $pts2Form->course_credits_student : ($student->course_credits_earned ?? '')),
                
                certPrimaFacie: @js(old('cert_prima_facie_case')) !== null && @js(old('cert_prima_facie_case')) !== '' ? Boolean(Number(@js(old('cert_prima_facie_case')))) : (savedDraft.certPrimaFacie !== undefined ? savedDraft.certPrimaFacie : {{ (isset($pts2Form) && $pts2Form->cert_prima_facie_case) ? 'true' : 'false' }}),
                
                certNoPriorDegree: @js(old('cert_no_prior_degree_submission')) !== null && @js(old('cert_no_prior_degree_submission')) !== '' ? Boolean(Number(@js(old('cert_no_prior_degree_submission')))) : (savedDraft.certNoPriorDegree !== undefined ? savedDraft.certNoPriorDegree : {{ (isset($pts2Form) && $pts2Form->cert_no_prior_degree_submission) ? 'true' : 'false' }}),
                
                collaborativeWorkStatus: @js(old('collaborative_work_status')) !== null && @js(old('collaborative_work_status')) !== '' ? String(@js(old('collaborative_work_status'))) : (savedDraft.collaborativeWorkStatus !== undefined ? String(savedDraft.collaborativeWorkStatus) : '{{ (isset($pts2Form) && $pts2Form->collaborative_work_status !== null) ? ($pts2Form->collaborative_work_status ? '1' : '0') : '0' }}'),
                
                collaborativeWorkDetails: @js(old('collaborative_work_details')) || savedDraft.collaborativeWorkDetails || @js(isset($pts2Form) ? $pts2Form->collaborative_work_details : ''),

                studentDeclaration: @js(old('student_declaration')) !== null && @js(old('student_declaration')) !== '' ? Boolean(Number(@js(old('student_declaration')))) : (savedDraft.studentDeclaration !== undefined ? savedDraft.studentDeclaration : false),

                maxSizes: {
                    synopsis: 10 // 10 MB limit
                },

                fileErrors: {
                    synopsis: ''
                },

                fileStates: {
                    synopsis: existingPts2 && existingPts2.synopsis_report_doc_path ? {
                        name: existingPts2.synopsis_report_doc_path.split('/').pop(),
                        size: 'Uploaded Document',
                        url: "{{ route('pts.document.serve', ['pts2', $pts2Form->id ?? 0, 'synopsis_report_doc_path']) }}"
                    } : { name: '', size: '', url: null }
                },

                init() {
                    const watchFields = [
                        'thesisTitle',
                        'currentAddress',
                        'recentPhone',
                        'recentPhoneCountryCode',
                        'recentPhoneIso2',
                        'alternatePhone',
                        'alternatePhoneCountryCode',
                        'alternatePhoneIso2',
                        'alternateEmail',
                        'courseCredits',
                        'certPrimaFacie',
                        'certNoPriorDegree',
                        'collaborativeWorkStatus',
                        'collaborativeWorkDetails',
                        'studentDeclaration'
                    ];
                    watchFields.forEach(field => {
                        this.$watch(field, () => this.saveDraft());
                    });

                    this.$nextTick(() => {
                        const recentInput = document.querySelector("#recent_phone_number");
                        if (recentInput && window.intlTelInput) {
                            const itiRecent = window.intlTelInput(recentInput, {
                                initialCountry: this.recentPhoneIso2 || "in",
                                preferredCountries: ["in", "us", "gb", "de", "sg", "au", "ca"],
                                utilsScript: "https://cdn.jsdelivr.net/npm/intl-tel-input@18.2.1/build/js/utils.js",
                            });
                            const updateRecent = () => {
                                const data = itiRecent.getSelectedCountryData();
                                this.recentPhoneCountryCode = '+' + (data.dialCode || '91');
                                this.recentPhoneIso2 = data.iso2 || 'in';
                                this.saveDraft();
                            };
                            recentInput.addEventListener("countrychange", updateRecent);
                            recentInput.addEventListener("input", updateRecent);
                        }

                        const altInput = document.querySelector("#alternate_phone_number");
                        if (altInput && window.intlTelInput) {
                            const itiAlt = window.intlTelInput(altInput, {
                                initialCountry: this.alternatePhoneIso2 || "in",
                                preferredCountries: ["in", "us", "gb", "de", "sg", "au", "ca"],
                                utilsScript: "https://cdn.jsdelivr.net/npm/intl-tel-input@18.2.1/build/js/utils.js",
                            });
                            const updateAlt = () => {
                                const data = itiAlt.getSelectedCountryData();
                                this.alternatePhoneCountryCode = '+' + (data.dialCode || '91');
                                this.alternatePhoneIso2 = data.iso2 || 'in';
                                this.saveDraft();
                            };
                            altInput.addEventListener("countrychange", updateAlt);
                            altInput.addEventListener("input", updateAlt);
                        }
                    });
                },

                get isBlocked() {
                    if (!this.studentDeclaration) return true;
                    if (!this.certPrimaFacie || !this.certNoPriorDegree) return true;
                    if (!this.fileStates.synopsis.name && !this.fileStates.synopsis.url) return true;
                    if (this.fileErrors.synopsis) return true;
                    return false;
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

                saveDraft() {
                    try {
                        sessionStorage.setItem(draftKey, JSON.stringify({
                            thesisTitle: this.thesisTitle,
                            currentAddress: this.currentAddress,
                            recentPhone: this.recentPhone,
                            recentPhoneCountryCode: this.recentPhoneCountryCode,
                            recentPhoneIso2: this.recentPhoneIso2,
                            alternatePhone: this.alternatePhone,
                            alternatePhoneCountryCode: this.alternatePhoneCountryCode,
                            alternatePhoneIso2: this.alternatePhoneIso2,
                            alternateEmail: this.alternateEmail,
                            courseCredits: this.courseCredits,
                            certPrimaFacie: this.certPrimaFacie,
                            certNoPriorDegree: this.certNoPriorDegree,
                            collaborativeWorkStatus: this.collaborativeWorkStatus,
                            collaborativeWorkDetails: this.collaborativeWorkDetails,
                            studentDeclaration: this.studentDeclaration,
                        }));
                    } catch (e) {}
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

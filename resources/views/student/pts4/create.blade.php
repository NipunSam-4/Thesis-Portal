<x-app-layout>
    @php
        $formPrefix = isset($student) && $student->isPhd() ? 'PTS' : 'MSRTS';
        $isReverted = isset($pts4Form) && $pts4Form->status === 'reverted';
    @endphp
    <div class="py-6" x-data="pts4Form()">
        <div class="max-w-5xl mx-auto px-2 sm:px-6 lg:px-8 space-y-4">

            <!-- Top Back to Dashboard Button -->
            <div>
                <x-back-to-dashboard-button />
            </div>

            <!-- Page Header Card -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-5 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div class="space-y-1">
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white leading-tight">
                        {{ $isReverted ? __("Edit & Resubmit {$formPrefix}-4 Thesis Form") : __("Submit {$formPrefix}-4 Thesis Form") }}
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

            <form action="{{ route('student.pts4.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6" @submit="clearDraft()">
                @csrf

                @php
                    $showModified = $isReverted && $pts4Form->reverted_by_role !== 'main_supervisor';
                @endphp

                @if($isReverted)
                    <div class="p-4 bg-amber-50 dark:bg-amber-950/40 border-l-4 border-amber-500 rounded-xl space-y-2">
                        <div class="flex items-center space-x-2 text-amber-900 dark:text-amber-200">
                            <svg class="w-5 h-5 text-amber-600 dark:text-amber-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                            </svg>
                            <span class="font-bold text-sm">Form Reverted by {{ $pts4Form->getRevertedByRoleLabel() }}</span>
                        </div>
                        @if($pts4Form->reversion_comment)
                            <div class="text-xs text-gray-700 dark:text-gray-300">
                                <strong>Reversion Comment:</strong>
                                <p class="italic bg-white dark:bg-gray-800 p-2.5 rounded-lg border border-amber-200 dark:border-amber-900 mt-1 whitespace-pre-wrap break-words [overflow-wrap:anywhere]">{{ trim($pts4Form->reversion_comment) }}</p>
                            </div>
                        @endif
                    </div>
                @endif

                <!-- Section 1: Read-Only Student Information -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white pb-3 mb-4 border-b border-gray-100 dark:border-gray-700 flex items-center gap-2">
                        <span>1. Student Information</span>
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
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
                            <x-readonly-label value="Primary Email Address" />
                            <x-readonly-input :value="$user->email" />
                        </div>

                        <div>
                            <x-readonly-label value="Recent Contact No." />
                            <x-readonly-input :value="$student->phone_number ? (($student->phone_country_code ?? '+91') . ' ' . $student->phone_number) : 'N/A'" />
                        </div>

                        <div class="md:col-span-3">
                            <x-readonly-label value="Main Supervisor" />
                            <x-readonly-input :value="$student->mainSupervisors->pluck('name')->join(', ') ?: ($student->supervisors->first()?->name ?? 'Not Assigned')" />
                        </div>

                        <div class="md:col-span-3">
                            <x-readonly-label value="Co-Supervisor(s)" />
                            <x-readonly-input :value="$student->coSupervisors->pluck('name')->join(', ') ?: 'None'" />
                        </div>

                        <div class="md:col-span-3">
                            <x-readonly-label value="External Supervisor(s)" />
                            @php
                                $extSupText = $student->externalSupervisors->isNotEmpty()
                                    ? $student->externalSupervisors->map(fn($s) => $s->name . ($s->externalSupervisorProfile?->affiliated_institute ? ' (' . $s->externalSupervisorProfile->affiliated_institute . ')' : ''))->join(', ')
                                    : 'None';
                            @endphp
                            <x-readonly-input :value="$extSupText" />
                        </div>
                    </div>
                </div>

                <!-- Section 2: Thesis Title -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 space-y-3">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white border-b border-gray-100 dark:border-gray-700 pb-2">
                        2. Thesis Title <span class="text-red-500">*</span>
                    </h3>
                    <div>
                        <x-form-input type="text" name="thesis_title" required x-model="thesisTitle" placeholder="Enter full title of thesis" />
                    </div>
                </div>

                <!-- Section 3: Personal & Contact Details -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 space-y-4">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white border-b border-gray-100 dark:border-gray-700 pb-2">
                        3. Personal &amp; Contact Details
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="md:col-span-2">
                            <x-form-label for="hindi_name" value="Student Name in Hindi" />
                            <x-form-input 
                                type="text" 
                                name="hindi_name" 
                                id="hindi_name" 
                                x-model="hindiName"
                                placeholder="उदा. निपुण शर्मा" 
                                class="font-hindi"
                            />
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

                    </div>
                </div>

                <!-- Section 4: Document Uploads -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 space-y-6">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white border-b border-gray-100 dark:border-gray-700 pb-2">
                        4. Document Uploads
                    </h3>

                    <div>
                        <div class="flex justify-between items-center mb-1">
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 flex items-center gap-2">
                                <span>Upload Final {{ ($student && $student->isPhd()) ? 'PhD' : 'MS(R)' }} Thesis Document (.pdf, .doc, .docx) <span class="text-red-500">*</span></span>
                                @if($showModified && $pts4Form->main_supervisor_thesis_doc_path && $pts4Form->main_supervisor_thesis_doc_path !== $pts4Form->thesis_doc_path)
                                    <x-modified-badge />
                                @endif
                            </label>
                            <span class="text-[11px] text-gray-400">Max 100 MB</span>
                        </div>

                        <div class="h-2"></div>

                        <div x-show="!fileStates.thesis.name">
                            <input type="file" id="thesisInput" name="thesis_doc" accept=".pdf,.doc,.docx" :required="!fileStates.thesis.name" @change="handleFileSelect($event, 'thesis')" class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                        </div>

                        <!-- File Size Error Alert -->
                        <div x-show="fileErrors.thesis" x-cloak class="mt-2 text-xs font-bold text-red-600 dark:text-red-400 bg-red-50 dark:bg-red-900/30 p-2.5 rounded-lg border border-red-200 dark:border-red-800">
                            <span x-text="fileErrors.thesis"></span>
                        </div>

                        <!-- File Selected Preview -->
                        <div x-show="fileStates.thesis.name" x-cloak class="p-3 bg-emerald-50 dark:bg-emerald-900/30 border border-emerald-200 dark:border-emerald-800 rounded-xl flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                            <div class="flex items-center space-x-3 min-w-0 truncate">
                                <div class="p-2 bg-emerald-100 dark:bg-emerald-800 rounded-lg text-emerald-700 dark:text-emerald-300 shrink-0">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                </div>
                                <div class="truncate">
                                    <div class="text-[10px] text-emerald-800 dark:text-emerald-300 font-bold uppercase">Selected Thesis Document</div>
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

                <!-- Section 5: Declaration -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-indigo-100 dark:border-indigo-900/50 p-6 space-y-4">
                    <h3 class="text-lg font-bold text-indigo-900 dark:text-indigo-300 border-b border-indigo-100 dark:border-indigo-900/50 pb-2 flex items-center">
                        5. Declaration
                    </h3>

                    <label class="p-4 rounded-xl border-2 transition-all flex items-start space-x-3 cursor-pointer"
                           :class="declarationNorms ? 'border-emerald-500 bg-emerald-50/60 dark:bg-emerald-950/30' : 'border-indigo-200 dark:border-indigo-800 bg-indigo-50/30 dark:bg-indigo-950/20 hover:border-indigo-400'">
                        <input type="checkbox" 
                               name="declaration_norms" 
                               value="1" 
                               x-model="declarationNorms" 
                               required
                               class="mt-1 text-emerald-600 focus:ring-emerald-500 rounded w-5 h-5 cursor-pointer">
                        <div class="space-y-1">
                            <p class="text-xs text-gray-700 dark:text-gray-300 leading-relaxed font-medium">
                                I hereby certify that all the copies of the thesis submitted have been prepared strictly in accordance with the norms for {{ ($student && $student->isPhd()) ? 'Ph.D.' : 'M.S. (Research)' }} Thesis from IIT Indore.
                            </p>
                        </div>
                    </label>

                    <div x-show="!declarationNorms" class="text-xs text-amber-600 dark:text-amber-400 flex items-center font-medium pl-1">
                        <svg class="w-4 h-4 mr-1.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <span>Please confirm the declaration checkbox above to enable submission.</span>
                    </div>
                </div>

                <!-- Submit Action -->
                <div class="flex justify-center sm:justify-end pt-4">
                    <button type="submit" :disabled="isBlocked" :class="isBlocked ? 'opacity-50 cursor-not-allowed bg-gray-400' : 'bg-blue-600 hover:bg-blue-700'" class="w-full sm:w-auto text-white text-base font-bold px-8 py-3 rounded-xl shadow-lg transition">
                        {{ $isReverted ? "Resubmit {$formPrefix}-4 Thesis Form" : "Submit {$formPrefix}-4 Thesis Form" }}
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- intl-tel-input Assets -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/intl-tel-input@18.2.1/build/css/intlTelInput.css">
    <script src="https://cdn.jsdelivr.net/npm/intl-tel-input@18.2.1/build/js/intlTelInput.min.js"></script>

    <script>
        function pts4Form() {
            const userId = @js(auth()->id());
            const thesisId = @js($thesis->id);
            const formId = @js(isset($pts4Form) && $pts4Form ? $pts4Form->id : null);
            const existingPts4 = @js(isset($pts4Form) ? $pts4Form : null);
            const draftKey = 'pts4_student_draft_user_' + userId + '_thesis_' + thesisId + (formId ? '_form_' + formId : '');

            let savedDraft = {};
            try {
                savedDraft = JSON.parse(sessionStorage.getItem(draftKey) || '{}');
            } catch (e) {}

            return {
                thesisTitle: @js(old('thesis_title')) || savedDraft.thesisTitle || @js(isset($pts4Form) ? $pts4Form->thesis_title : ($thesis->title ?? '')),
                hindiName: @js(old('hindi_name')) || savedDraft.hindiName || @js(isset($pts4Form) ? $pts4Form->hindi_name : ($student->hindi_name ?? '')),
                alternatePhone: @js(old('alternate_phone_number')) || savedDraft.alternatePhone || @js(isset($pts4Form) ? $pts4Form->alternate_phone_number : ($student->alternate_phone_number ?? '')),
                alternatePhoneCountryCode: @js(old('alternate_phone_country_code')) || savedDraft.alternatePhoneCountryCode || @js(isset($pts4Form) ? ($pts4Form->alternate_phone_country_code ?? '+91') : ($student->alternate_phone_country_code ?? '+91')),
                alternatePhoneIso2: @js(old('alternate_phone_iso2')) || savedDraft.alternatePhoneIso2 || @js(isset($pts4Form) ? ($pts4Form->alternate_phone_iso2 ?? 'in') : ($student->alternate_phone_iso2 ?? 'in')),
                alternateEmail: @js(old('alternate_email')) || savedDraft.alternateEmail || @js(isset($pts4Form) ? $pts4Form->alternate_email : ($student->alternate_email ?? '')),
                declarationNorms: @js(old('declaration_norms')) !== null && @js(old('declaration_norms')) !== '' ? Boolean(Number(@js(old('declaration_norms')))) : (savedDraft.declarationNorms !== undefined ? savedDraft.declarationNorms : false),

                maxSizes: {
                    thesis: 100 // 100 MB limit
                },

                fileErrors: {
                    thesis: ''
                },

                fileStates: {
                    thesis: existingPts4 && existingPts4.thesis_doc_path ? {
                        name: existingPts4.thesis_doc_path.split('/').pop(),
                        size: 'Uploaded Document',
                        url: "{{ route('pts.document.serve', ['pts4', $pts4Form->id ?? 0, 'thesis_doc_path']) }}"
                    } : { name: '', size: '', url: null }
                },

                init() {
                    const watchFields = [
                        'thesisTitle',
                        'hindiName',
                        'alternatePhone',
                        'alternatePhoneCountryCode',
                        'alternatePhoneIso2',
                        'alternateEmail',
                        'declarationNorms'
                    ];
                    watchFields.forEach(field => {
                        this.$watch(field, () => this.saveDraft());
                    });

                    this.$nextTick(() => {
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
                    if (!this.declarationNorms) return true;
                    if (!this.fileStates.thesis.name && !this.fileStates.thesis.url) return true;
                    if (this.fileErrors.thesis) return true;
                    return false;
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

                saveDraft() {
                    try {
                        sessionStorage.setItem(draftKey, JSON.stringify({
                            thesisTitle: this.thesisTitle,
                            hindiName: this.hindiName,
                            alternatePhone: this.alternatePhone,
                            alternatePhoneCountryCode: this.alternatePhoneCountryCode,
                            alternatePhoneIso2: this.alternatePhoneIso2,
                            alternateEmail: this.alternateEmail,
                            declarationNorms: this.declarationNorms,
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

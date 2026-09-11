<x-app-layout>
    @php
        $formPrefix = isset($student) && $student->isPhd() ? 'PTS' : 'MSRTS';
        $isReverted = isset($pts3) && $pts3->status === 'reverted';

        $defaultExaminer = ['name'=>'', 'designation'=>'', 'organization'=>'', 'postal_address'=>'', 'email'=>'', 'phone_number'=>'', 'phone_country_code'=>'+91', 'phone_iso2'=>'in', 'website'=>'', 'research_area'=>'', 'has_consent'=>'1', 'consent_doc_path'=>null, 'consent_doc_name'=>null, 'consent_doc_url'=>null];
        $defaultOeb = ['name'=>'', 'designation'=>'', 'department'=>'', 'email'=>'', 'phone_number'=>'', 'phone_country_code'=>'+91', 'phone_iso2'=>'in'];

        $indianData = isset($pts3) ? $pts3->indianExaminers->sortBy('id')->map(function($e) use ($pts3) {
            return [
                'id' => $e->id,
                'name' => $e->name, 'designation' => $e->designation, 'organization' => $e->organization,
                'postal_address' => $e->postal_address, 'email' => $e->email, 'phone_number' => $e->phone_number,
                'phone_country_code' => $e->phone_country_code ?? '+91', 'phone_iso2' => $e->phone_iso2 ?? 'in',
                'website' => $e->website, 'research_area' => $e->research_area, 'has_consent' => $e->has_consent ? '1' : '0',
                'consent_doc_path' => $e->consent_doc_path,
                'consent_doc_name' => $e->consent_doc_path ? basename($e->consent_doc_path) : null,
                'consent_doc_url' => $e->consent_doc_path ? route('pts.document.serve', ['pts3', $pts3->id, 'consent_doc', 'examiner_id' => $e->id]) : null,
            ];
        })->values()->toArray() : [$defaultExaminer, $defaultExaminer];

        $intlData = isset($pts3) ? $pts3->internationalExaminers->sortBy('id')->map(function($e) use ($pts3) {
            return [
                'id' => $e->id,
                'name' => $e->name, 'designation' => $e->designation, 'organization' => $e->organization,
                'postal_address' => $e->postal_address, 'email' => $e->email, 'phone_number' => $e->phone_number,
                'phone_country_code' => $e->phone_country_code ?? '+91', 'phone_iso2' => $e->phone_iso2 ?? 'in',
                'website' => $e->website, 'research_area' => $e->research_area, 'has_consent' => $e->has_consent ? '1' : '0',
                'consent_doc_path' => $e->consent_doc_path,
                'consent_doc_name' => $e->consent_doc_path ? basename($e->consent_doc_path) : null,
                'consent_doc_url' => $e->consent_doc_path ? route('pts.document.serve', ['pts3', $pts3->id, 'consent_doc', 'examiner_id' => $e->id]) : null,
            ];
        })->values()->toArray() : [$defaultExaminer, $defaultExaminer];

        $oebData = isset($pts3) ? $pts3->oebMembers->sortBy('id')->map(function($o) {
            return [
                'name' => $o->name, 'designation' => $o->designation, 'department' => $o->department,
                'email' => $o->email, 'phone_number' => $o->phone_number,
                'phone_country_code' => $o->phone_country_code ?? '+91', 'phone_iso2' => $o->phone_iso2 ?? 'in'
            ];
        })->values()->toArray() : array_fill(0, 4, $defaultOeb);
    @endphp
    <div class="py-6" x-data="pts3Form()">
        <div class="max-w-7xl mx-auto px-2 sm:px-6 lg:px-8 space-y-4">

            <!-- Top Back to Dashboard Button -->
            <div>
                <x-back-to-dashboard-button />
            </div>

            <!-- Page Header Card -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-5 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div class="space-y-1">
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white leading-tight">
                        {{ $isReverted ? __("Edit & Resubmit {$formPrefix}-3 Form") : __("Submit {$formPrefix}-3 Form") }}
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

            <form action="{{ $isReverted ? route('faculty.pts3.update', $pts3->id) : route('faculty.pts3.store', $student->id) }}" method="POST" enctype="multipart/form-data" class="space-y-6" @submit="if (!validateForm($event)) { return false; } clearDraft();">
                @csrf
                @if($isReverted)
                    @method('PUT')
                @endif

                @if($isReverted)
                    <div class="p-4 bg-amber-50 dark:bg-amber-950/40 border-l-4 border-amber-500 rounded-xl space-y-2">
                        <div class="flex items-center space-x-2 text-amber-900 dark:text-amber-200">
                            <svg class="w-5 h-5 text-amber-600 dark:text-amber-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                            </svg>
                            <span class="font-bold text-sm">Form Reverted by {{ $pts3->getRevertedByRoleLabel() ?? 'Authority' }}</span>
                        </div>
                        @if($pts3->reversion_comment)
                            <div class="text-xs text-gray-700 dark:text-gray-300">
                                <strong>Reversion Comment:</strong>
                                <p class="italic bg-white dark:bg-gray-800 p-2.5 rounded-lg border border-amber-200 dark:border-amber-900 mt-1 whitespace-pre-wrap break-words">{{ trim($pts3->reversion_comment) }}</p>
                            </div>
                        @endif
                    </div>
                @endif

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

                <!-- Section 2: Name of Thesis -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 space-y-3">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white border-b border-gray-100 dark:border-gray-700 pb-2">
                        2. Name of Thesis
                    </h3>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Thesis Title <span class="text-red-500">*</span></label>
                        <input type="text" name="thesis_title" required x-model="thesisTitle" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-blue-500 focus:border-blue-500" placeholder="Enter full title of thesis">
                    </div>
                </div>

                <!-- Examiners Components via Alpine -->
                <x-pts3-examiners-section type="indian" title="3. Indian Examiners Panel" />
                
                <x-pts3-examiners-section type="international" title="4. International Examiners Panel" />

                <!-- OEB Members -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 space-y-4">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white border-b border-gray-100 dark:border-gray-700 pb-2">
                        5. Oral Examination Board (OEB) Faculty Members
                    </h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Please provide exactly 4 faculty members for the OEB.</p>
                    
                    <div class="space-y-6">
                        <template x-for="(oeb, index) in oebMembers" :key="index">
                            <div class="p-4 border border-gray-200 dark:border-gray-700 rounded-lg relative bg-gray-50 dark:bg-gray-900/50">
                                <h4 class="font-bold text-gray-700 dark:text-gray-300 mb-3" x-text="`OEB Member #${index + 1}`"></h4>
                                
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <x-form-label value="Name" required />
                                        <x-form-input type="text" x-model="oeb.name" x-bind:name="`oeb_members[${index}][name]`" required placeholder="Faculty Name" />
                                    </div>
                                    <div>
                                        <x-form-label value="Designation" required />
                                        <x-form-input type="text" x-model="oeb.designation" x-bind:name="`oeb_members[${index}][designation]`" required placeholder="e.g. Professor / Assoc. Prof" />
                                    </div>
                                    <div>
                                        <x-form-label value="Department" required />
                                        <x-form-input type="text" x-model="oeb.department" x-bind:name="`oeb_members[${index}][department]`" required placeholder="e.g. Mechanical Engineering" />
                                    </div>
                                    <div>
                                        <x-form-label value="Email Address" required />
                                        <x-form-input type="email" x-model="oeb.email" x-bind:name="`oeb_members[${index}][email]`" required placeholder="faculty@institute.ac.in" />
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
                
                <!-- Section 6: Declaration -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-indigo-100 dark:border-indigo-900/50 p-6 space-y-4">
                    <h3 class="text-lg font-bold text-indigo-900 dark:text-indigo-300 border-b border-indigo-100 dark:border-indigo-900/50 pb-2 flex items-center">
                        6. Supervisor Declaration
                    </h3>

                    <label class="p-4 rounded-xl border-2 transition-all flex items-start space-x-3 cursor-pointer"
                           :class="supervisorDeclaration ? 'border-emerald-500 bg-emerald-50/60 dark:bg-emerald-950/30' : 'border-indigo-200 dark:border-indigo-800 bg-indigo-50/30 dark:bg-indigo-950/20 hover:border-indigo-400'">
                        <input type="checkbox" 
                               name="supervisor_declaration" 
                               value="1" 
                               x-model="supervisorDeclaration" 
                               required
                               class="mt-1 text-emerald-600 focus:ring-emerald-500 rounded w-5 h-5 cursor-pointer">
                        <div class="space-y-1">
                            <p class="text-xs text-gray-700 dark:text-gray-300 leading-relaxed font-medium">
                                I hereby declare that the proposed Panel of Examiners (Indian &amp; International) and Oral Examination Board (OEB) members comply with all institute guidelines and that written consent has been obtained for all indicated consented examiners.
                            </p>
                        </div>
                    </label>

                    <div x-show="!supervisorDeclaration" class="text-xs text-amber-600 dark:text-amber-400 flex items-center font-medium pl-1">
                        <svg class="w-4 h-4 mr-1.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <span>Please confirm the declaration checkbox above to enable submission.</span>
                    </div>
                </div>
                
                <div class="flex justify-end pt-4 pb-12">
                    <button type="submit" 
                            :disabled="!isPanelValid('indianExaminers') || !isPanelValid('internationalExaminers') || !supervisorDeclaration"
                            :class="(!isPanelValid('indianExaminers') || !isPanelValid('internationalExaminers') || !supervisorDeclaration) ? 'opacity-60 cursor-not-allowed bg-gray-400' : 'bg-blue-600 hover:bg-blue-700'"
                            class="px-6 py-3 text-white font-bold rounded-xl shadow-md transition-all flex items-center gap-2">
                        <span>{{ $isReverted ? 'Resubmit' : 'Submit' }}</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- intl-tel-input Assets -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/intl-tel-input@18.2.1/build/css/intlTelInput.css">
    <style>
        .iti { width: 100%; display: block; }
        .iti__country-list { color: #111827; }
        .dark .iti__country-list { background-color: #1f2937; color: #f9fafb; border-color: #374151; }
        .dark .iti__country.iti__highlight { background-color: #374151; }
    </style>
    <script src="https://cdn.jsdelivr.net/npm/intl-tel-input@18.2.1/build/js/intlTelInput.min.js"></script>

    <!-- Define alpine data logic in script -->
    <script>
        function pts3Form() {
            const userId = @js(auth()->id());
            const thesisId = @js($thesis->id);
            const formId = @js(isset($pts3) && $pts3 ? $pts3->id : null);
            const draftKey = 'pts3_supervisor_draft_user_' + userId + '_thesis_' + thesisId + (formId ? '_form_' + formId : '');

            const serverIndian = @json($indianData);
            const serverIntl = @json($intlData);

            function sanitizeDraftExaminers(draftList, serverList) {
                if (!draftList || !Array.isArray(draftList) || !draftList.length) return serverList;
                return draftList.map((e, index) => {
                    const serverMatch = serverList && serverList[index] ? serverList[index] : null;
                    return {
                        ...e,
                        consent_doc_path: serverMatch ? serverMatch.consent_doc_path : null,
                        consent_doc_name: serverMatch ? serverMatch.consent_doc_name : null,
                        consent_doc_url: serverMatch ? serverMatch.consent_doc_url : null,
                    };
                });
            }

            let savedDraft = {};
            try {
                savedDraft = JSON.parse(sessionStorage.getItem(draftKey) || '{}');
            } catch (e) {}

            return {
                thesisTitle: @js(old('thesis_title')) || savedDraft.thesisTitle || @js($pts3->thesis_title ?? $thesis->title ?? ''),
                supervisorDeclaration: savedDraft.supervisorDeclaration !== undefined ? savedDraft.supervisorDeclaration : @js($isReverted ? true : false),
                
                indianExaminers: sanitizeDraftExaminers(savedDraft.indianExaminers, serverIndian),
                internationalExaminers: sanitizeDraftExaminers(savedDraft.internationalExaminers, serverIntl),
                oebMembers: (savedDraft.oebMembers && savedDraft.oebMembers.length) ? savedDraft.oebMembers : @json($oebData),

                init() {
                    this.$watch('thesisTitle', () => this.saveDraft());
                    this.$watch('supervisorDeclaration', () => this.saveDraft());
                    this.$watch('indianExaminers', () => this.saveDraft(), { deep: true });
                    this.$watch('internationalExaminers', () => this.saveDraft(), { deep: true });
                    this.$watch('oebMembers', () => this.saveDraft(), { deep: true });
                },

                saveDraft() {
                    try {
                        const cleanExaminers = (list) => (list || []).map(e => ({
                            ...e,
                            consent_doc_path: null,
                            consent_doc_name: null,
                            consent_doc_url: null,
                        }));

                        sessionStorage.setItem(draftKey, JSON.stringify({
                            thesisTitle: this.thesisTitle,
                            supervisorDeclaration: this.supervisorDeclaration,
                            indianExaminers: cleanExaminers(this.indianExaminers),
                            internationalExaminers: cleanExaminers(this.internationalExaminers),
                            oebMembers: this.oebMembers,
                        }));
                    } catch (e) {}
                },

                clearDraft() {
                    try {
                        sessionStorage.removeItem(draftKey);
                    } catch (e) {}
                },

                createDefaultExaminer(type = 'indian', consent = '1') {
                    const isIntl = type === 'international' || type === 'internationalExaminers';
                    return {
                        id: null,
                        name: '',
                        designation: '',
                        organization: '',
                        postal_address: '',
                        email: '',
                        phone_number: '',
                        phone_country_code: isIntl ? '+1' : '+91',
                        phone_iso2: isIntl ? 'us' : 'in',
                        website: '',
                        research_area: '',
                        has_consent: consent,
                        consent_doc_path: null,
                        consent_doc_name: null,
                        consent_doc_url: null
                    };
                },

                clearConsentDoc(type, index, inputId) {
                    if (this[type] && this[type][index]) {
                        this[type][index].consent_doc_path = null;
                        this[type][index].consent_doc_name = null;
                        this[type][index].consent_doc_url = null;
                    }
                    if (inputId) {
                        const el = document.getElementById(inputId);
                        if (el) el.value = '';
                    }
                },

                handleExaminerFileSelect(event, type, index) {
                    const file = event.target.files[0];
                    if (!file) return;

                    const sizeInMB = file.size / (1024 * 1024);
                    const limitMB = 2;

                    if (sizeInMB > limitMB) {
                        const formattedSize = sizeInMB >= 1 ? `${sizeInMB.toFixed(2)} MB` : `${(file.size / 1024).toFixed(1)} KB`;
                        window.dispatchEvent(new CustomEvent('file-size-exceeded', {
                            detail: {
                                fileName: file.name,
                                fileSize: formattedSize,
                                limitMB: `${limitMB} MB`,
                                inputId: event.target.id || null
                            }
                        }));
                        event.target.value = '';
                        this.clearConsentDoc(type, index, event.target.id);
                        return;
                    }

                    if (this[type] && this[type][index]) {
                        this[type][index].consent_doc_name = file.name;
                        this[type][index].consent_doc_url = URL.createObjectURL(file);
                    }
                },

                // Auto-balancing consent logic
                handleConsentChange(type, index, value) {
                    if (!this[type] || !this[type][index]) return;
                    this[type][index].has_consent = String(value);

                    const total = this[type].length;
                    const consentCount = this[type].filter(e => e.has_consent === '1').length;
                    const nonConsentCount = this[type].filter(e => e.has_consent === '0').length;

                    // Case A: 2 examiners, user makes one '0' -> needs 1 more '0' slot (total 3)
                    if (total === 2 && consentCount === 1 && nonConsentCount === 1) {
                        this[type].push(this.createDefaultExaminer(type, '0'));
                    }
                    // Case B: 2 examiners, user makes both '0' -> needs 2 more '0' slots (total 4)
                    else if (total === 2 && consentCount === 0 && nonConsentCount === 2) {
                        this[type].push(this.createDefaultExaminer(type, '0'));
                        this[type].push(this.createDefaultExaminer(type, '0'));
                    }
                    // Case C: 3 examiners (1 consent + 2 non-consent), user turns the 1 consent to '0' -> needs 1 more '0' slot (total 4)
                    else if (total === 3 && consentCount === 0 && nonConsentCount === 3) {
                        this[type].push(this.createDefaultExaminer(type, '0'));
                    }
                    // Case D: User changes a non-consent to consent:
                    // If we have 3 examiners and now 2 are consented, collapse the 3rd non-consent
                    else if (total === 3 && consentCount === 2 && nonConsentCount === 1) {
                        const lastNonConsentIdx = this[type].map(e => e.has_consent).lastIndexOf('0');
                        if (lastNonConsentIdx !== -1) {
                            this[type].splice(lastNonConsentIdx, 1);
                        }
                    }
                    // If we have 4 examiners and now 1 is consented, collapse the 4th non-consent (becomes 1 consent + 2 non-consent = 3 total)
                    else if (total === 4 && consentCount === 1 && nonConsentCount === 3) {
                        const lastNonConsentIdx = this[type].map(e => e.has_consent).lastIndexOf('0');
                        if (lastNonConsentIdx !== -1) {
                            this[type].splice(lastNonConsentIdx, 1);
                        }
                    }
                    // If we have 4 examiners and now 2 are consented, collapse 2 non-consents (becomes 2 consent = 2 total)
                    else if (total === 4 && consentCount === 2 && nonConsentCount === 2) {
                        this[type] = this[type].filter(e => e.has_consent === '1');
                    }
                },

                getPanelUnits(type) {
                    if (!this[type]) return '0.0';
                    const consentCount = this[type].filter(e => e.has_consent === '1').length;
                    const nonConsentCount = this[type].filter(e => e.has_consent === '0').length;
                    const units = (consentCount * 1.0) + (nonConsentCount * 0.5);
                    return Number.isInteger(units) ? units.toFixed(1) : units.toString();
                },

                isPanelValid(type) {
                    if (!this[type]) return false;
                    const total = this[type].length;
                    const consentCount = this[type].filter(e => e.has_consent === '1').length;
                    const nonConsentCount = this[type].filter(e => e.has_consent === '0').length;

                    return (total === 2 && consentCount === 2 && nonConsentCount === 0)
                        || (total === 3 && consentCount === 1 && nonConsentCount === 2)
                        || (total === 4 && consentCount === 0 && nonConsentCount === 4);
                },

                addExaminer(type) {
                    if (this[type].length < 4) {
                        this[type].push(this.createDefaultExaminer(type, '0'));
                    }
                },

                removeExaminer(type, index) {
                    if (this[type].length > 2) {
                        this[type].splice(index, 1);
                    }
                },

                validateForm(event) {
                    if (!this.isPanelValid('indianExaminers')) {
                        alert("Indian Examiners Panel is incomplete:\nYou must provide either:\n• 2 examiners with consent, OR\n• 1 with consent + 2 without consent (3 total), OR\n• 4 without consent.");
                        event.preventDefault();
                        return false;
                    }
                    if (!this.isPanelValid('internationalExaminers')) {
                        alert("International Examiners Panel is incomplete:\nYou must provide either:\n• 2 examiners with consent, OR\n• 1 with consent + 2 without consent (3 total), OR\n• 4 without consent.");
                        event.preventDefault();
                        return false;
                    }
                    if (!this.supervisorDeclaration) {
                        alert("Please confirm the supervisor declaration checkbox before submitting.");
                        event.preventDefault();
                        return false;
                    }
                    return true;
                }
            }
        }
    </script>
</x-app-layout>

<x-app-layout>
    @php
        $draft = $draft ?? null;
        $pts3 = $pts3 ?? null;
        $formPrefix = isset($student) && $student->isPhd() ? 'PTS' : 'MSRTS';
        $isReverted = isset($pts3) && $pts3->status === 'reverted';

        $defaultExaminer = ['name'=>'', 'designation'=>'', 'organization'=>'', 'postal_address'=>'', 'email'=>'', 'phone_number'=>'', 'phone_country_code'=>'+91', 'phone_iso2'=>'in', 'website'=>'', 'research_area'=>'', 'has_consent'=>'1', 'consent_doc_path'=>null, 'consent_doc_name'=>null, 'consent_doc_url'=>null];
        $defaultOeb = ['name'=>'', 'designation'=>'', 'department'=>'', 'email'=>''];

        if ($isReverted) {
            $indianData = $pts3->getIndianExaminers()->map(function($e) use ($pts3) {
                return [
                    'id' => $e->id,
                    'name' => $e->name, 'designation' => $e->designation, 'organization' => $e->organization,
                    'postal_address' => $e->postal_address, 'email' => $e->email, 'phone_number' => $e->phone_number,
                    'phone_country_code' => $e->phone_country_code ?? '+91', 'phone_iso2' => $e->phone_iso2 ?? 'in',
                    'website' => $e->website, 'research_area' => $e->research_area, 'has_consent' => $e->has_consent ? '1' : '0',
                    'consent_doc_path' => $e->consent_doc_path,
                    'consent_doc_name' => $e->consent_doc_path ? basename($e->consent_doc_path) : null,
                    'consent_doc_url' => $e->consent_doc_path ? route('pts.document.serve', ['formType' => 'pts3', 'id' => $pts3->id, 'field' => 'consent_doc', 'type' => 'indian', 'slot' => $e->slot]) : null,
                ];
            })->values()->toArray();
        } elseif ($draft && $draft->getIndianExaminers()->isNotEmpty()) {
            $indianData = $draft->getIndianExaminers()->map(function($e) use ($draft) {
                return [
                    'id' => null,
                    'name' => $e->name, 'designation' => $e->designation, 'organization' => $e->organization,
                    'postal_address' => $e->postal_address, 'email' => $e->email, 'phone_number' => $e->phone_number,
                    'phone_country_code' => $e->phone_country_code ?? '+91', 'phone_iso2' => $e->phone_iso2 ?? 'in',
                    'website' => $e->website, 'research_area' => $e->research_area, 'has_consent' => $e->has_consent ? '1' : '0',
                    'consent_doc_path' => $e->consent_doc_path,
                    'consent_doc_name' => $e->consent_doc_path ? basename($e->consent_doc_path) : null,
                    'consent_doc_url' => $e->consent_doc_path ? route('pts.document.serve', ['formType' => 'pts3_draft', 'id' => $draft->id, 'field' => 'consent_doc', 'type' => 'indian', 'slot' => $e->slot]) : null,
                ];
            })->values()->toArray();
        } else {
            $indianData = [$defaultExaminer, $defaultExaminer];
        }

        if ($isReverted) {
            $intlData = $pts3->getInternationalExaminers()->map(function($e) use ($pts3) {
                return [
                    'id' => $e->id,
                    'name' => $e->name, 'designation' => $e->designation, 'organization' => $e->organization,
                    'postal_address' => $e->postal_address, 'email' => $e->email, 'phone_number' => $e->phone_number,
                    'phone_country_code' => $e->phone_country_code ?? '+1', 'phone_iso2' => $e->phone_iso2 ?? 'us',
                    'website' => $e->website, 'research_area' => $e->research_area, 'has_consent' => $e->has_consent ? '1' : '0',
                    'consent_doc_path' => $e->consent_doc_path,
                    'consent_doc_name' => $e->consent_doc_path ? basename($e->consent_doc_path) : null,
                    'consent_doc_url' => $e->consent_doc_path ? route('pts.document.serve', ['formType' => 'pts3', 'id' => $pts3->id, 'field' => 'consent_doc', 'type' => 'international', 'slot' => $e->slot]) : null,
                ];
            })->values()->toArray();
        } elseif ($draft && $draft->getInternationalExaminers()->isNotEmpty()) {
            $intlData = $draft->getInternationalExaminers()->map(function($e) use ($draft) {
                return [
                    'id' => null,
                    'name' => $e->name, 'designation' => $e->designation, 'organization' => $e->organization,
                    'postal_address' => $e->postal_address, 'email' => $e->email, 'phone_number' => $e->phone_number,
                    'phone_country_code' => $e->phone_country_code ?? '+1', 'phone_iso2' => $e->phone_iso2 ?? 'us',
                    'website' => $e->website, 'research_area' => $e->research_area, 'has_consent' => $e->has_consent ? '1' : '0',
                    'consent_doc_path' => $e->consent_doc_path,
                    'consent_doc_name' => $e->consent_doc_path ? basename($e->consent_doc_path) : null,
                    'consent_doc_url' => $e->consent_doc_path ? route('pts.document.serve', ['formType' => 'pts3_draft', 'id' => $draft->id, 'field' => 'consent_doc', 'type' => 'international', 'slot' => $e->slot]) : null,
                ];
            })->values()->toArray();
        } else {
            $intlData = [$defaultExaminer, $defaultExaminer];
        }

        if ($isReverted) {
            $oebData = $pts3->getOebChairpersons()->map(function($o) {
                return [
                    'name' => $o->name, 'designation' => $o->designation, 'department' => $o->department,
                    'email' => $o->email
                ];
            })->values()->toArray();
        } elseif ($draft && $draft->getOebChairpersons()->isNotEmpty()) {
            $oebData = $draft->getOebChairpersons()->map(function($o) {
                return [
                    'name' => $o->name, 'designation' => $o->designation, 'department' => $o->department,
                    'email' => $o->email
                ];
            })->values()->toArray();
            while (count($oebData) < 4) {
                $oebData[] = $defaultOeb;
            }
        } else {
            $oebData = array_fill(0, 4, $defaultOeb);
        }

        $thesisTitle = old('thesis_title', $draft->thesis_title ?? ($pts3->thesis_title ?? ($thesis->title ?? '')));
        $supervisorDeclaration = old('supervisor_declaration', $isReverted ? true : false);
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

            <!-- Saved Draft Notification Banner -->
            @if(!$isReverted && $draft)
                <div class="p-4 bg-blue-50/90 dark:bg-blue-950/40 border border-blue-200 dark:border-blue-800/80 rounded-xl flex flex-col sm:flex-row sm:items-center justify-between gap-3 shadow-xs">
                    <div class="flex items-center gap-2.5 text-blue-900 dark:text-blue-200 text-sm">
                        <svg class="w-5 h-5 text-blue-600 dark:text-blue-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path>
                        </svg>
                        <div>
                            <span class="font-bold">Loaded from saved draft</span>
                            <span class="text-xs text-blue-700 dark:text-blue-300 block sm:inline sm:ml-1">
                                (Last saved: {{ $draft->updated_at ? $draft->updated_at->format('d M Y, h:i A') : 'Recently' }})
                            </span>
                        </div>
                    </div>
                    <form action="{{ route('faculty.pts3.draft.discard', $student->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to discard this saved draft and start fresh?');" class="shrink-0">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="px-3.5 py-1.5 text-xs font-bold text-red-600 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300 hover:bg-red-50 dark:hover:bg-red-950/50 rounded-lg border border-red-200 dark:border-red-800 transition cursor-pointer">
                            Discard Draft
                        </button>
                    </form>
                </div>
            @endif

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

            <form action="{{ $isReverted ? route('faculty.pts3.update', $pts3->id) : route('faculty.pts3.store', $student->id) }}" method="POST" enctype="multipart/form-data" class="space-y-6" @submit="if ($event.submitter && $event.submitter.hasAttribute('formaction')) { return true; } if (!validateForm($event)) { return false; }">
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
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 space-y-4" x-data="{ showStudentInfo: false }">
                    <div class="flex items-center justify-between border-b border-gray-100 dark:border-gray-700 pb-3 cursor-pointer select-none" @click="showStudentInfo = !showStudentInfo">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white flex items-center gap-2">
                            <span>1. Student Information</span>
                        </h3>
                        <button type="button" class="p-1.5 rounded-lg text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 transition" @click.stop="showStudentInfo = !showStudentInfo">
                            <svg class="w-5 h-5 transform transition-transform duration-200" :class="{ 'rotate-180': showStudentInfo }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                    </div>

                    <!-- Always Visible First Row -->
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
                    </div>

                    <!-- Collapsible Remaining Details -->
                    <div x-show="showStudentInfo" x-transition class="grid grid-cols-1 md:grid-cols-3 gap-6 pt-4 border-t border-gray-100 dark:border-gray-700">

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

                        <div class="md:col-span-1">
                            <x-readonly-label value="Main Supervisor" />
                            <x-readonly-input :value="$student->mainSupervisors->pluck('name')->join(', ') ?: ($student->supervisors->first()?->name ?? 'Not Assigned')" />
                        </div>

                        <div class="md:col-span-2">
                            <x-readonly-label value="Co-Supervisor(s)" />
                            <x-readonly-input :value="$student->coSupervisors->pluck('name')->join(', ') ?: 'None'" />
                        </div>

                        <div class="md:col-span-2">
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
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 space-y-2">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white border-b border-gray-100 dark:border-gray-700 pb-2">
                        2. Thesis Title <span class="text-red-500">*</span>
                    </h3>
                    <div>
                        <input type="text" name="thesis_title" required x-model="thesisTitle" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-blue-500 focus:border-blue-500" placeholder="Enter full title of thesis">
                    </div>
                </div>

                <!-- Examiners Components via Alpine -->
                <x-pts3-examiners-section type="indian" title="3. Indian Examiners Panel" />
                
                <x-pts3-examiners-section type="international" title="4. International Examiners Panel" />

                <!-- OEB Chairpersons -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 space-y-4">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white border-b border-gray-100 dark:border-gray-700 pb-2">
                        5. Oral Examination Board (OEB) Chairpersons
                    </h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Please provide exactly 4 Institute faculty members as proposed OEB Chairpersons.</p>
                    
                    <div class="space-y-6">
                        <template x-for="(oeb, index) in oebMembers" :key="index">
                            <div class="p-4 border border-gray-200 dark:border-gray-700 rounded-xl relative bg-gray-50 dark:bg-gray-900/50 space-y-3">
                                <div class="flex items-center justify-between pb-2 border-b border-gray-200 dark:border-gray-700">
                                    <div class="flex items-center gap-2.5">
                                        <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-gray-200 text-xs font-bold shrink-0"
                                              x-text="index + 1"></span>
                                        <h4 class="font-bold text-sm text-gray-800 dark:text-gray-200" 
                                            x-text="oeb.name ? oeb.name : `OEB Chairperson #${index + 1}`"></h4>
                                    </div>
                                    <button type="button" 
                                            class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-semibold text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 transition cursor-pointer"
                                            @click="clearOebMember(index)"
                                            title="Clear all fields for this OEB chairperson">
                                        <svg class="w-3.5 h-3.5 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                                        <span>Clear</span>
                                    </button>
                                </div>
                                
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <x-form-label value="Name" required />
                                        <x-form-input type="text" x-model="oeb.name" x-bind:name="`oeb_chairpersons[${index}][name]`" required placeholder="Faculty Name" />
                                    </div>
                                    <div>
                                        <x-form-label value="Designation" required />
                                        <x-form-input type="text" x-model="oeb.designation" x-bind:name="`oeb_chairpersons[${index}][designation]`" required placeholder="e.g. Professor / Assoc. Prof" />
                                    </div>
                                    <div>
                                        <x-form-label value="Department" required />
                                        <x-form-input type="text" x-model="oeb.department" x-bind:name="`oeb_chairpersons[${index}][department]`" required placeholder="e.g. Mechanical Engineering" />
                                    </div>
                                    <div>
                                        <x-form-label value="Email Address" required />
                                        <x-form-input type="email" x-model="oeb.email" x-bind:name="`oeb_chairpersons[${index}][email]`" required placeholder="faculty@institute.ac.in" />
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
                
                <div class="flex flex-col sm:flex-row items-center justify-between gap-4 pt-4 pb-12">
                    <div>
                        @if(isset($draft) && $draft && !$isReverted)
                            <button type="submit"
                                    formaction="{{ route('faculty.pts3.draft.discard', $student->id) }}"
                                    formnovalidate
                                    onclick="return confirm('Are you sure you want to discard this saved draft? All unsaved progress will be permanently removed.');"
                                    class="px-4 py-2.5 text-xs sm:text-sm font-bold text-red-600 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300 hover:bg-red-50 dark:hover:bg-red-950/50 rounded-xl border border-red-200 dark:border-red-800 transition shadow-xs cursor-pointer flex items-center gap-1.5">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                <span>Discard Draft</span>
                            </button>
                        @endif
                    </div>

                    <div class="flex items-center gap-3 w-full sm:w-auto justify-end">
                        @if(!$isReverted)
                            <button type="submit" 
                                    formaction="{{ route('faculty.pts3.draft.save', $student->id) }}" 
                                    formnovalidate
                                    class="px-5 py-3 text-sm font-bold text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-800 hover:bg-gray-100 dark:hover:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-xl shadow-xs transition flex items-center gap-2 cursor-pointer">
                                <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path></svg>
                                <span>Save Draft</span>
                            </button>
                        @endif

                        <button type="submit" 
                                :disabled="!isPanelValid('indianExaminers') || !isPanelValid('internationalExaminers') || !supervisorDeclaration"
                                :class="(!isPanelValid('indianExaminers') || !isPanelValid('internationalExaminers') || !supervisorDeclaration) ? 'opacity-60 cursor-not-allowed bg-gray-400' : 'bg-blue-600 hover:bg-blue-700 cursor-pointer'"
                                class="px-6 py-3 text-white font-bold rounded-xl shadow-md transition-all flex items-center gap-2">
                        <span>{{ $isReverted ? 'Resubmit' : 'Submit' }}</span>
                        </button>
                    </div>
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
        function createDefaultExaminerObject(type = 'indian', consent = '1') {
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
        }

        function pts3Form() {
            const serverIndian = @json($indianData);
            const serverIntl = @json($intlData);
            const serverOeb = @json($oebData);
            const serverTitle = @json($thesisTitle);
            const serverDeclaration = @json($supervisorDeclaration);

            return {
                thesisTitle: serverTitle || '',
                supervisorDeclaration: Boolean(serverDeclaration),
                
                indianExaminers: (serverIndian && serverIndian.length) ? serverIndian : [
                    createDefaultExaminerObject('indian', '1'),
                    createDefaultExaminerObject('indian', '1')
                ],
                internationalExaminers: (serverIntl && serverIntl.length) ? serverIntl : [
                    createDefaultExaminerObject('international', '1'),
                    createDefaultExaminerObject('international', '1')
                ],
                oebMembers: (serverOeb && serverOeb.length) ? serverOeb : [
                    { name: '', designation: '', department: '', email: '' },
                    { name: '', designation: '', department: '', email: '' },
                    { name: '', designation: '', department: '', email: '' },
                    { name: '', designation: '', department: '', email: '' }
                ],

                createDefaultExaminer(type = 'indian', consent = '1') {
                    return createDefaultExaminerObject(type, consent);
                },

                clearExaminer(type, index, inputId) {
                    if (this[type] && this[type][index]) {
                        const isIntl = type === 'international' || type === 'internationalExaminers';
                        const currentConsent = this[type][index].has_consent || '1';
                        this[type][index] = {
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
                            has_consent: currentConsent,
                            consent_doc_path: null,
                            consent_doc_name: null,
                            consent_doc_url: null
                        };
                    }
                    if (inputId) {
                        const el = document.getElementById(inputId);
                        if (el) el.value = '';
                    }
                },

                clearOebMember(index) {
                    if (this.oebMembers && this.oebMembers[index]) {
                        this.oebMembers[index] = {
                            name: '',
                            designation: '',
                            department: '',
                            email: ''
                        };
                    }
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

                handleConsentChange(type, index, value) {
                    if (!this[type] || !this[type][index]) return;
                    this[type][index].has_consent = String(value);
                    if (String(value) === '0') {
                        this[type][index].consent_doc_path = null;
                        this[type][index].consent_doc_name = null;
                        this[type][index].consent_doc_url = null;
                    }
                },

                getPanelUnits(type) {
                    if (!this[type]) return 0;
                    const consentCount = this[type].filter(e => e.has_consent === '1').length;
                    const nonConsentCount = this[type].filter(e => e.has_consent === '0').length;
                    return (consentCount * 1.0) + (nonConsentCount * 0.5);
                },

                getPanelCompositionLabel(type) {
                    if (!this[type]) return 'Requirement Not Met';
                    const total = this[type].length;
                    const consentCount = this[type].filter(e => e.has_consent === '1').length;
                    const nonConsentCount = this[type].filter(e => e.has_consent === '0').length;

                    if (total === 2 && consentCount === 2 && nonConsentCount === 0) {
                        return '2 Consent';
                    }
                    if (total === 3 && consentCount === 1 && nonConsentCount === 2) {
                        return '1 Consent + 2 Non-Consent';
                    }
                    if (total === 4 && consentCount === 0 && nonConsentCount === 4) {
                        return '4 Non-Consent';
                    }
                    return `Requirement Not Met`;
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
                    if (this[type] && this[type].length > 0) {
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

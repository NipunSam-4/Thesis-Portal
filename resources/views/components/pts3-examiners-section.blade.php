@props(['type', 'title'])

@php
    $modelName = $type === 'indian' ? 'indianExaminers' : 'internationalExaminers';
    $namePrefix = $type === 'indian' ? 'indian_examiners' : 'international_examiners';
@endphp

<div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 space-y-5" x-data>
    <!-- Section Header with Dynamic Status Badge -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-gray-100 dark:border-gray-700 pb-3">
        <div>
            <h3 class="text-lg font-bold text-gray-900 dark:text-white flex items-center gap-2">
                {{ $title }}
            </h3>
        </div>

        <div class="flex items-center gap-2">
            <!-- Dynamic Status Badge -->
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold transition-all shadow-xs"
                  :class="isPanelValid('{{ $modelName }}')
                      ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950/70 dark:text-emerald-300 border border-emerald-300 dark:border-emerald-700'
                      : 'bg-amber-100 text-amber-800 dark:bg-amber-950/70 dark:text-amber-300 border border-amber-300 dark:border-amber-700'">
                <template x-if="isPanelValid('{{ $modelName }}')">
                    <span class="flex items-center gap-1">
                        <svg class="w-3.5 h-3.5 text-emerald-600 dark:text-emerald-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                        <span>Requirement Met</span>
                    </span>
                </template>
                <template x-if="!isPanelValid('{{ $modelName }}')">
                    <span class="flex items-center gap-1">
                        <svg class="w-3.5 h-3.5 text-amber-600 dark:text-amber-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>
                        <span x-text="`Incomplete (${getPanelUnits('{{ $modelName }}')} / 2.0 Units)`"></span>
                    </span>
                </template>
            </span>

            <!-- Manual Add Examiner Button (if < 4) -->
            <button type="button" 
                    @click="addExaminer('{{ $modelName }}')" 
                    x-show="{{ $modelName }}.length < 4" 
                    class="text-xs bg-indigo-50 hover:bg-indigo-100 text-indigo-700 dark:bg-indigo-900/40 dark:hover:bg-indigo-900/70 dark:text-indigo-300 px-3 py-1.5 rounded-lg font-semibold transition-all border border-indigo-200 dark:border-indigo-800">
                + Add Examiner
            </button>
        </div>
    </div>
    
    <!-- Combination Guide Banner -->
    <div class="bg-gray-50 dark:bg-gray-900/50 rounded-xl p-3.5 border border-gray-200 dark:border-gray-700/70">
        <div class="text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1.5 flex items-center justify-between">
            <span>Valid Composition Rules:</span>
            <span class="text-[11px] font-normal text-gray-500 dark:text-gray-400">
                Current: <strong class="text-gray-900 dark:text-white" x-text="{{ $modelName }}.length + ' Examiner(s)'"></strong> 
                (<span x-text="{{ $modelName }}.filter(e => e.has_consent === '1').length + ' with consent, ' + {{ $modelName }}.filter(e => e.has_consent === '0').length + ' without consent'"></span>)
            </span>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-2 text-xs">
            <div class="p-2 rounded-lg border transition-all"
                 :class="{{ $modelName }}.length === 2 && {{ $modelName }}.filter(e => e.has_consent === '1').length === 2
                     ? 'bg-emerald-50 dark:bg-emerald-950/40 border-emerald-400 dark:border-emerald-600 text-emerald-900 dark:text-emerald-200 font-semibold shadow-xs'
                     : 'bg-white dark:bg-gray-800 border-gray-200 dark:border-gray-700 text-gray-600 dark:text-gray-400'">
                <span class="block">✓ Option 1: <strong>2 Consented</strong></span>
                <span class="text-[11px] opacity-80">(Total 2 Examiners)</span>
            </div>
            <div class="p-2 rounded-lg border transition-all"
                 :class="{{ $modelName }}.length === 3 && {{ $modelName }}.filter(e => e.has_consent === '1').length === 1 && {{ $modelName }}.filter(e => e.has_consent === '0').length === 2
                     ? 'bg-emerald-50 dark:bg-emerald-950/40 border-emerald-400 dark:border-emerald-600 text-emerald-900 dark:text-emerald-200 font-semibold shadow-xs'
                     : 'bg-white dark:bg-gray-800 border-gray-200 dark:border-gray-700 text-gray-600 dark:text-gray-400'">
                <span class="block">✓ Option 2: <strong>1 Consent + 2 Non-Consent</strong></span>
                <span class="text-[11px] opacity-80">(Total 3 Examiners)</span>
            </div>
            <div class="p-2 rounded-lg border transition-all"
                 :class="{{ $modelName }}.length === 4 && {{ $modelName }}.filter(e => e.has_consent === '1').length === 0 && {{ $modelName }}.filter(e => e.has_consent === '0').length === 4
                     ? 'bg-emerald-50 dark:bg-emerald-950/40 border-emerald-400 dark:border-emerald-600 text-emerald-900 dark:text-emerald-200 font-semibold shadow-xs'
                     : 'bg-white dark:bg-gray-800 border-gray-200 dark:border-gray-700 text-gray-600 dark:text-gray-400'">
                <span class="block">✓ Option 3: <strong>4 Non-Consented</strong></span>
                <span class="text-[11px] opacity-80">(Total 4 Examiners)</span>
            </div>
        </div>
    </div>

    <!-- Examiners Cards List -->
    <div class="space-y-6">
        <template x-for="(examiner, index) in {{ $modelName }}" :key="index">
            <div class="p-5 border rounded-xl relative transition-all bg-white dark:bg-gray-800/90"
                 :class="examiner.has_consent === '1'
                     ? 'border-blue-200 dark:border-blue-900/60 shadow-xs'
                     : 'border-gray-200 dark:border-gray-700'">
                
                <!-- Card Header -->
                <div class="flex justify-between items-center mb-4 border-b border-gray-100 dark:border-gray-700 pb-2.5">
                    <div class="flex items-center gap-2">
                        <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200 text-xs font-bold"
                              x-text="index + 1"></span>
                        <h4 class="font-bold text-sm text-gray-800 dark:text-gray-200" x-text="`Examiner #${index + 1}`"></h4>
                        
                        <!-- Consent Pill Badge -->
                        <span class="text-[11px] font-semibold px-2.5 py-0.5 rounded-full"
                              :class="examiner.has_consent === '1'
                                  ? 'bg-blue-100 text-blue-800 dark:bg-blue-950/80 dark:text-blue-300'
                                  : 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300'"
                              x-text="examiner.has_consent === '1' ? 'Consent Obtained ' : 'No Consent'">
                        </span>
                    </div>

                    <button type="button" 
                            @click="removeExaminer('{{ $modelName }}', index)" 
                            x-show="{{ $modelName }}.length > 2" 
                            class="text-xs text-red-500 hover:text-red-700 dark:hover:text-red-400 font-semibold px-2 py-1 rounded hover:bg-red-50 dark:hover:bg-red-950/40 transition">
                        Remove
                    </button>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Name -->
                    <div>
                        <x-form-label value="Full Name" required />
                        <x-form-input type="text" x-model="examiner.name" x-bind:name="`{{ $namePrefix }}[${index}][name]`" required placeholder="e.g. Prof. John Doe" />
                    </div>

                    <!-- Designation -->
                    <div>
                        <x-form-label value="Designation" required />
                        <x-form-input type="text" x-model="examiner.designation" x-bind:name="`{{ $namePrefix }}[${index}][designation]`" required placeholder="e.g. Professor / Senior Scientist" />
                    </div>

                    <!-- Organization -->
                    <div>
                        <x-form-label value="Organization / Institute" required />
                        <x-form-input type="text" x-model="examiner.organization" x-bind:name="`{{ $namePrefix }}[${index}][organization]`" required placeholder="e.g. University / Research Center" />
                    </div>

                    <!-- Postal Address -->
                    <div>
                        <x-form-label value="Postal Address" required />
                        <textarea x-model="examiner.postal_address" x-bind:name="`{{ $namePrefix }}[${index}][postal_address]`" required rows="2" class="w-full text-sm rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:[color-scheme:dark] focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 shadow-xs transition" placeholder="Department, Institute, City, State, Country, Postal Code"></textarea>
                    </div>

                    <!-- Email -->
                    <div>
                        <x-form-label value="Email Address" required />
                        <x-form-input type="email" x-model="examiner.email" x-bind:name="`{{ $namePrefix }}[${index}][email]`" required placeholder="official@institute.edu" />
                    </div>

                    <!-- Phone with Flag & Country Code Selector -->
                    <div x-data="{ iti: null }" x-init="
                        $nextTick(() => {
                            if ($refs.phoneInput && window.intlTelInput) {
                                iti = window.intlTelInput($refs.phoneInput, {
                                    initialCountry: examiner.phone_iso2 || '{{ $type === 'indian' ? 'in' : 'us' }}',
                                    preferredCountries: ['in', 'us', 'gb', 'de', 'sg', 'au', 'ca'],
                                    utilsScript: 'https://cdn.jsdelivr.net/npm/intl-tel-input@18.2.1/build/js/utils.js',
                                });
                                const updateCountry = () => {
                                    const data = iti.getSelectedCountryData();
                                    examiner.phone_country_code = '+' + (data.dialCode || '{{ $type === 'indian' ? '91' : '1' }}');
                                    examiner.phone_iso2 = data.iso2 || '{{ $type === 'indian' ? 'in' : 'us' }}';
                                };
                                $refs.phoneInput.addEventListener('countrychange', updateCountry);
                                $refs.phoneInput.addEventListener('input', updateCountry);
                                updateCountry();
                            }
                        })
                    ">
                        <x-form-label value="Contact Phone Number" required />
                        <x-form-input x-ref="phoneInput"
                                      type="tel" 
                                      inputmode="numeric" 
                                      oninput="this.value = this.value.replace(/[^0-9]/g, '')" 
                                      x-model="examiner.phone_number" 
                                      x-bind:name="`{{ $namePrefix }}[${index}][phone_number]`" 
                                      required 
                                      placeholder="e.g. 9876543210" />
                        <input type="hidden" x-bind:name="`{{ $namePrefix }}[${index}][phone_country_code]`" x-model="examiner.phone_country_code">
                        <input type="hidden" x-bind:name="`{{ $namePrefix }}[${index}][phone_iso2]`" x-model="examiner.phone_iso2">
                    </div>

                    <!-- Website -->
                    <div>
                        <x-form-label value="Official Profile Web Link (Optional)" />
                        <x-form-input type="url" x-model="examiner.website" x-bind:name="`{{ $namePrefix }}[${index}][website]`" placeholder="https://institute.edu/~faculty" />
                    </div>

                    <!-- Research Area -->
                    <div>
                        <x-form-label value="Specialization / Research Area (Optional)" />
                        <x-form-input type="text" x-model="examiner.research_area" x-bind:name="`{{ $namePrefix }}[${index}][research_area]`" placeholder="e.g. Machine Learning, Structural Mechanics" />
                    </div>
                    
                    <!-- Consent Radio Group with Auto-Balancing Trigger -->
                    <div class="md:col-span-2 pt-3 border-t border-gray-100 dark:border-gray-700 mt-1">
                        <x-form-label value="Has consent been obtained from this examiner?" required />
                        <div class="flex items-center gap-6 mt-1">
                            <label class="flex items-center gap-2 cursor-pointer text-sm text-gray-700 dark:text-gray-300 font-medium">
                                <input type="radio" 
                                       :name="`{{ $namePrefix }}[${index}][has_consent]`" 
                                       value="1" 
                                       :checked="examiner.has_consent === '1'"
                                       @change="handleConsentChange('{{ $modelName }}', index, '1')"
                                       class="text-blue-600 focus:ring-blue-500 w-4 h-4 cursor-pointer">
                                <span>Yes, Consent Obtained</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer text-sm text-gray-700 dark:text-gray-300 font-medium">
                                <input type="radio" 
                                       :name="`{{ $namePrefix }}[${index}][has_consent]`" 
                                       value="0" 
                                       :checked="examiner.has_consent === '0'"
                                       @change="handleConsentChange('{{ $modelName }}', index, '0')"
                                       class="text-blue-600 focus:ring-blue-500 w-4 h-4 cursor-pointer">
                                <span>No Consent</span>
                            </label>
                        </div>
                        
                        <!-- Consent Document Upload -->
                        <div x-show="examiner.has_consent === '1'" class="mt-3 p-3 bg-blue-50/60 dark:bg-blue-950/30 rounded-lg border border-blue-200 dark:border-blue-900/60 space-y-2">
                            <input type="hidden" :name="`{{ $namePrefix }}[${index}][consent_doc_path]`" x-model="examiner.consent_doc_path">
                            
                            <div class="flex justify-between items-center">
                                <label class="block text-xs font-semibold text-blue-950 dark:text-blue-200">
                                    Consent Confirmation Document (.pdf, .doc, .docx) <span class="text-red-500">*</span>
                                </label>
                                <span class="text-[11px] text-gray-400">Max 2 MB</span>
                            </div>

                            <!-- File Input (when no document is attached or user clicked Delete/Replace) -->
                            <div x-show="!examiner.consent_doc_name && !examiner.consent_doc_path">
                                <input type="file" 
                                       :id="`{{ $namePrefix }}_${index}_doc`"
                                       :name="`{{ $namePrefix }}[${index}][consent_doc]`" 
                                       accept=".pdf,.doc,.docx" 
                                       :required="examiner.has_consent === '1' && !examiner.consent_doc_path" 
                                       @change="handleExaminerFileSelect($event, '{{ $modelName }}', index)"
                                       class="w-full text-xs text-gray-500 dark:text-gray-400 file:mr-4 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-blue-600 file:text-white hover:file:bg-blue-700 cursor-pointer">
                            </div>

                            <!-- Attached Document Card (Matching PTS-1 / PTS-2 / PTS-4 exact emerald style) -->
                            <div x-show="examiner.consent_doc_name || examiner.consent_doc_path" x-cloak class="p-3 bg-emerald-50 dark:bg-emerald-900/30 border border-emerald-200 dark:border-emerald-800 rounded-xl flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                                <div class="flex items-center space-x-3 min-w-0 truncate">
                                    <div class="p-2 bg-emerald-100 dark:bg-emerald-800 rounded-lg text-emerald-700 dark:text-emerald-300 shrink-0">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                    </div>
                                    <div class="truncate">
                                        <div class="text-[10px] text-emerald-800 dark:text-emerald-300 font-bold uppercase">Selected Consent Document</div>
                                        <div class="text-sm font-semibold text-gray-900 dark:text-white truncate" x-text="examiner.consent_doc_name || (examiner.consent_doc_path ? examiner.consent_doc_path.split('/').pop() : '')"></div>
                                        <div class="text-[11px] text-gray-500">Uploaded Document</div>
                                    </div>
                                </div>
                                <div class="flex items-center space-x-2 shrink-0 justify-end sm:justify-start">
                                    <template x-if="examiner.consent_doc_url">
                                        <a :href="examiner.consent_doc_url" target="_blank" class="px-2.5 py-1 bg-white dark:bg-gray-800 text-emerald-700 dark:text-emerald-300 border border-emerald-300 dark:border-emerald-700 rounded-lg text-xs font-bold shadow-xs hover:bg-emerald-100 flex items-center gap-1">
                                            📄 View File
                                        </a>
                                    </template>
                                    <button type="button" @click="clearConsentDoc('{{ $modelName }}', index, `{{ $namePrefix }}_${index}_doc`)" class="px-2.5 py-1 bg-white dark:bg-gray-800 text-red-600 dark:text-red-400 border border-red-200 dark:border-red-800 rounded-lg text-xs font-bold shadow-xs hover:bg-red-50 dark:hover:bg-red-900/30 flex items-center gap-1">
                                        <svg class="w-3.5 h-3.5 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                        Delete
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </template>
    </div>
</div>

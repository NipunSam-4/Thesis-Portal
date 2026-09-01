@props(['type', 'title'])

@php
    $modelName = $type === 'indian' ? 'indianExaminers' : 'internationalExaminers';
    $namePrefix = $type === 'indian' ? 'indian_examiners' : 'international_examiners';
@endphp

<div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 space-y-4">
    <div class="flex justify-between items-center border-b border-gray-100 dark:border-gray-700 pb-2">
        <h3 class="text-lg font-bold text-gray-900 dark:text-white">
            {{ $title }}
        </h3>
        <button type="button" @click="addExaminer('{{ $modelName }}')" x-show="{{ $modelName }}.length < 4" class="text-sm bg-blue-50 text-blue-600 px-3 py-1.5 rounded-lg hover:bg-blue-100 font-semibold transition-colors">
            + Add Examiner
        </button>
    </div>
    
    <div class="text-sm text-gray-600 dark:text-gray-400 mb-2">
        <p><strong>Rules for combinations:</strong></p>
        <ul class="list-disc pl-5">
            <li>2 Examiners &rarr; Both must have consent.</li>
            <li>3 Examiners &rarr; Exactly 1 must have consent.</li>
            <li>4 Examiners &rarr; None must have consent.</li>
        </ul>
    </div>

    <div class="space-y-6">
        <template x-for="(examiner, index) in {{ $modelName }}" :key="index">
            <div class="p-4 border border-gray-200 dark:border-gray-700 rounded-lg relative bg-gray-50 dark:bg-gray-900/50">
                <div class="flex justify-between items-center mb-4 border-b border-gray-200 dark:border-gray-700 pb-2">
                    <h4 class="font-bold text-gray-700 dark:text-gray-300" x-text="`Examiner ${index + 1}`"></h4>
                    <button type="button" @click="removeExaminer('{{ $modelName }}', index)" x-show="{{ $modelName }}.length > 2" class="text-red-500 hover:text-red-700 font-medium text-sm">
                        Remove
                    </button>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Name -->
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Name *</label>
                        <input type="text" x-model="examiner.name" :name="`{{ $namePrefix }}[${index}][name]`" required class="w-full text-sm rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                    </div>
                    <!-- Designation -->
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Designation *</label>
                        <input type="text" x-model="examiner.designation" :name="`{{ $namePrefix }}[${index}][designation]`" required class="w-full text-sm rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                    </div>
                    <!-- Organization -->
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Organization *</label>
                        <input type="text" x-model="examiner.organization" :name="`{{ $namePrefix }}[${index}][organization]`" required class="w-full text-sm rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                    </div>
                    <!-- Postal Address -->
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Postal Address *</label>
                        <textarea x-model="examiner.postal_address" :name="`{{ $namePrefix }}[${index}][postal_address]`" required rows="2" class="w-full text-sm rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white"></textarea>
                    </div>
                    <!-- Email -->
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Email *</label>
                        <input type="email" x-model="examiner.email" :name="`{{ $namePrefix }}[${index}][email]`" required class="w-full text-sm rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                    </div>
                    <!-- Phone -->
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Phone Number *</label>
                        <input type="text" x-model="examiner.phone_number" :name="`{{ $namePrefix }}[${index}][phone_number]`" required class="w-full text-sm rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                        <input type="hidden" :name="`{{ $namePrefix }}[${index}][phone_country_code]`" x-model="examiner.phone_country_code">
                        <input type="hidden" :name="`{{ $namePrefix }}[${index}][phone_iso2]`" x-model="examiner.phone_iso2">
                    </div>
                    <!-- Website -->
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Website Profile Link (Optional)</label>
                        <input type="url" x-model="examiner.website" :name="`{{ $namePrefix }}[${index}][website]`" class="w-full text-sm rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white" placeholder="https://...">
                    </div>
                    <!-- Research Area -->
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Research Area (Optional)</label>
                        <input type="text" x-model="examiner.research_area" :name="`{{ $namePrefix }}[${index}][research_area]`" class="w-full text-sm rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                    </div>
                    
                    <!-- Consent -->
                    <div class="md:col-span-2 pt-2 border-t border-gray-200 dark:border-gray-700 mt-2">
                        <label class="block text-sm font-semibold text-gray-800 dark:text-gray-200 mb-2">Have you taken consent from them? *</label>
                        <div class="flex items-center gap-6">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="radio" :name="`{{ $namePrefix }}[${index}][has_consent]`" value="0" x-model="examiner.has_consent" class="text-blue-600 focus:ring-blue-500">
                                <span class="text-sm text-gray-700 dark:text-gray-300">No</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="radio" :name="`{{ $namePrefix }}[${index}][has_consent]`" value="1" x-model="examiner.has_consent" class="text-blue-600 focus:ring-blue-500">
                                <span class="text-sm text-gray-700 dark:text-gray-300">Yes</span>
                            </label>
                        </div>
                        
                        <div x-show="examiner.has_consent === '1'" class="mt-3 p-3 bg-white dark:bg-gray-800 rounded border border-gray-300 dark:border-gray-600">
                            <label class="block text-xs font-semibold text-gray-600 dark:text-gray-400 mb-1">Upload Consent Letter/Email (PDF/DOCX, Max 2MB) *</label>
                            <input type="file" :name="`{{ $namePrefix }}[${index}][consent_doc]`" accept=".pdf,.doc,.docx" :required="examiner.has_consent === '1'" class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                        </div>
                    </div>
                </div>
            </div>
        </template>
    </div>
</div>

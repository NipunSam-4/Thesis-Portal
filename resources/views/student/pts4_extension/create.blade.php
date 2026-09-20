<x-app-layout>
    <div class="py-6" x-data="pts4ExtensionForm()">
        <div class="max-w-5xl mx-auto px-2 sm:px-6 lg:px-8 space-y-4">

            <!-- Top Back to Dashboard Button -->
            <div>
                <x-back-to-dashboard-button />
            </div>

            <!-- Page Header Card -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-5 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div class="space-y-1">
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white leading-tight">
                        {{ $isReverted ? __('Resubmit PTS-4 Extension Application') : __('Apply for PTS-4(Thesis Submission) Extension') }}
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

            <form action="{{ route('student.pts4_extension.store') }}" method="POST" class="space-y-2" @submit="clearDraft()">
                @csrf

                <!-- Reversion Alert Banner (If form was reverted) -->
                @if(($isReverted || (isset($pts4Extension) && $pts4Extension->status === 'reverted')) && $pts4Extension)
                    <div class="p-4 bg-amber-50 dark:bg-amber-950/40 border-l-4 border-amber-500 rounded-xl space-y-2">
                        <div class="flex items-center space-x-2 text-amber-900 dark:text-amber-200">
                            <svg class="w-5 h-5 text-amber-600 dark:text-amber-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                            </svg>
                            <span class="font-bold text-sm">Application Reverted by {{ $pts4Extension->getRevertedByRoleLabel() }}</span>
                        </div>
                        @if($pts4Extension->reversion_comment)
                            <div class="text-xs text-gray-700 dark:text-gray-300">
                                <strong>Reversion Comment:</strong>
                                <p class="italic bg-white dark:bg-gray-800 p-2.5 rounded-lg border border-amber-200 dark:border-amber-900 mt-1 whitespace-pre-wrap break-words [overflow-wrap:anywhere]">{{ trim($pts4Extension->reversion_comment) }}</p>
                            </div>
                        @endif
                    </div>
                @endif

                <!-- Section 1: Pre-filled Student Details -->
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
                    </div>
                </div>

                <!-- Section 2: Extension Details -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 space-y-6">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white border-b border-gray-100 dark:border-gray-700 pb-2">
                        2. Extension Details
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-6">
                        <div>
                            <x-readonly-label value="Open Seminar Date" />
                            <x-readonly-input :value="isset($seminarDate) && $seminarDate ? $seminarDate->format('d-M-Y') : 'N/A'" />
                        </div>

                        <div>
                            <x-readonly-label value="Extended Date Required" :required="true" />
                            <x-form-input 
                                type="date" 
                                name="extended_until_date" 
                                required 
                                :min="$minExtensionDate ? $minExtensionDate->format('Y-m-d') : \Carbon\Carbon::tomorrow()->format('Y-m-d')"
                                :max="$maxExtensionDate ? $maxExtensionDate->format('Y-m-d') : ''"
                                x-model="extendedUntilDate"
                                :value="old('extended_until_date', isset($pts4Extension) && $pts4Extension->extended_until_date ? $pts4Extension->extended_until_date->format('Y-m-d') : ($minExtensionDate ? $minExtensionDate->format('Y-m-d') : ''))" 
                            />
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                Select the extended deadline date till which you are requesting extension for PTS-4 form submission.
                            </p>
                            @if(isset($minExtensionDate) && isset($maxExtensionDate))
                                <p class="text-xs text-indigo-600 dark:text-indigo-400 mt-1 font-medium">
                                    Permitted Range: {{ $minExtensionDate->format('d-M-Y') }} &mdash; {{ $maxExtensionDate->format('d-M-Y') }} (31 to 60 days from Open Seminar)
                                </p>
                            @endif
                        </div>
                    </div>

                    <div>
                        <x-form-label value="Detailed Reason for Extension" :required="true" />
                        <textarea name="reason_for_extension" 
                                    rows="5" 
                                    required 
                                    x-model="reasonForExtension"
                                    placeholder="Please provide a comprehensive description of the reason for requesting PTS-4 thesis submission extension" 
                                    class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-purple-500 focus:border-purple-500 text-sm leading-relaxed whitespace-pre-wrap">{{ trim(old('reason_for_extension', isset($pts4Extension) ? $pts4Extension->reason_for_extension : '')) }}</textarea>
                    </div>
                </div>

                <!-- Submit Button Container -->
                <div class="flex justify-center sm:justify-end pt-4">
                    <button type="submit" 
                            class="w-full sm:w-auto justify-center px-6 py-3 bg-purple-600 hover:bg-purple-700 text-white font-bold text-sm rounded-xl shadow-lg transition flex items-center space-x-2">
                        <span>{{ 'Submit for approval' }}</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                        </svg>
                    </button>
                </div>
            </form>

        </div>
    </div>

    <script>
        function pts4ExtensionForm() {
            const userId = @js(auth()->id());
            const thesisId = @js($thesis->id);
            const formId = @js(isset($pts4Extension) && $pts4Extension ? $pts4Extension->id : null);
            const draftKey = 'pts4_extension_student_draft_user_' + userId + '_thesis_' + thesisId + (formId ? '_form_' + formId : '');

            let savedDraft = {};
            try {
                savedDraft = JSON.parse(sessionStorage.getItem(draftKey) || '{}');
            } catch (e) {}

            return {
                extendedUntilDate: @js(old('extended_until_date')) || savedDraft.extendedUntilDate || @js(isset($pts4Extension) && $pts4Extension->extended_until_date ? $pts4Extension->extended_until_date->format('Y-m-d') : ''),
                reasonForExtension: @js(old('reason_for_extension')) || savedDraft.reasonForExtension || @js(isset($pts4Extension) ? $pts4Extension->reason_for_extension : ''),

                init() {
                    this.$watch('extendedUntilDate', () => this.saveDraft());
                    this.$watch('reasonForExtension', () => this.saveDraft());
                },

                saveDraft() {
                    try {
                        sessionStorage.setItem(draftKey, JSON.stringify({
                            extendedUntilDate: this.extendedUntilDate,
                            reasonForExtension: this.reasonForExtension,
                        }));
                    } catch (e) {}
                },

                clearDraft() {
                    try {
                        sessionStorage.removeItem(draftKey);
                    } catch (e) {}
                }
            }
        }
    </script>
</x-app-layout>

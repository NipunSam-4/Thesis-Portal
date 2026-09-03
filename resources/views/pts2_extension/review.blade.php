@php
    $student = $extension->thesis->student ?? ($student ?? null);
    $formPrefix = (isset($student) && $student->isPhd()) ? 'PTS' : 'MSRTS';
@endphp

<x-app-layout>
    <div class="py-6" x-data="pts2ExtensionReviewForm()">
        <div class="max-w-5xl mx-auto px-2 sm:px-6 lg:px-8 space-y-4">

            <!-- Top Back to Dashboard Button -->
            <div>
                <x-back-to-dashboard-button />
            </div>

            <!-- Page Header Card -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-5 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div class="space-y-1">
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white leading-tight">
                        {{ __('PTS-2 Extension Evaluation Portal') }}
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

            @if(session('error'))
                <div x-data="{ show: true }" x-show="show" x-transition class="p-4 bg-red-100 dark:bg-red-900/40 border-l-4 border-red-500 text-red-800 dark:text-red-200 rounded-xl shadow-sm flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <svg class="w-5 h-5 text-red-600 dark:text-red-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <span class="font-bold text-sm">{{ session('error') }}</span>
                    </div>
                </div>
            @endif

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

            <!-- Section 1: Read-Only Student Information (Exact PTS-1 Card 1 Design) -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
                <div class="flex items-center justify-between border-b border-gray-100 dark:border-gray-700 pb-3 mb-4">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white flex items-center">
                        1. Student Information
                    </h3>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <x-readonly-label value="Student Name" />
                        <x-readonly-input :value="$extension->thesis->student->user->name ?? 'N/A'" />
                    </div>

                    <div>
                        <x-readonly-label value="Roll Number" />
                        <x-readonly-input :value="$extension->thesis->student->roll_number ?? 'N/A'" />
                    </div>

                    <div>
                        <x-readonly-label value="Department" />
                        <x-readonly-input :value="$extension->thesis->student->department->name ?? 'N/A'" />
                    </div>

                    <div>
                        <x-readonly-label value="Date of Registration" />
                        <x-readonly-input :value="$extension->thesis->student->date_registration ? \Carbon\Carbon::parse($extension->thesis->student->date_registration)->format('d-m-Y') : 'N/A'" />
                    </div>

                    <div>
                        <x-readonly-label value="Date of Joining" />
                        <x-readonly-input :value="$extension->thesis->student->date_joining ? \Carbon\Carbon::parse($extension->thesis->student->date_joining)->format('d-m-Y') : 'N/A'" />
                    </div>

                    <div>
                        <x-readonly-label value="Date of Confirmation" />
                        <x-readonly-input :value="$extension->thesis->student->date_confirmation ? \Carbon\Carbon::parse($extension->thesis->student->date_confirmation)->format('d-m-Y') : 'N/A'" />
                    </div>

                    <div>
                        <x-readonly-label value="Date of Submission" />
                        <x-readonly-input :value="$extension->created_at ? $extension->created_at->format('d-m-Y') : 'N/A'" />
                    </div>
                </div>
            </div>

            <!-- Section 2: Extension Details Submitted by Student -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 space-y-4">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white border-b border-gray-100 dark:border-gray-700 pb-2">
                    2. Extension Application Details
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 sm:gap-6">
                    <div>
                        <x-readonly-label value="Open Seminar Date" />
                        <x-readonly-input :value="$seminarDate ? $seminarDate->format('d-M-Y') : 'N/A'" />
                    </div>

                    <div>
                        <x-readonly-label value="Extended Deadline Requested" />
                        <input type="text" value="📅 {{ $extension->extended_until_date ? $extension->extended_until_date->format('d-M-Y') : 'N/A' }}" readonly class="w-full bg-purple-50 dark:bg-purple-950/40 text-purple-900 dark:text-purple-200 rounded-lg border-purple-200 dark:border-purple-800 cursor-not-allowed font-medium text-sm">
                    </div>

                    <div>
                        <x-readonly-label value="Application Date" />
                        <x-readonly-input :value="$extension->created_at ? $extension->created_at->format('d-M-Y H:i') : 'N/A'" />
                    </div>
                </div>

                <div>
                    <x-readonly-label value="Reason for Extension" />
                    <div class="p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg border border-gray-300 dark:border-gray-600 text-gray-800 dark:text-gray-200 leading-relaxed whitespace-pre-wrap break-words [overflow-wrap:anywhere] cursor-not-allowed text-sm">{{ trim($extension->reason_for_extension) }}</div>
                </div>
            </div>

            <!-- Section 3: Prior Authority Evaluations Trail (Strict Confidentiality: Only Previous Ranks) -->
            @php
                $viewerRank = \App\Models\Pts2Extension::getRoleRank($userRole);
                $hasPriorRecommendation = 
                    ($viewerRank >= 1 && $extension->main_supervisor_recommendation !== null) ||
                    ($viewerRank > 2 && $extension->dpgc_recommendation !== null) ||
                    ($viewerRank > 3 && $extension->hod_recommendation !== null) ||
                    ($viewerRank > 4 && $extension->academic_office_recommendation !== null);
            @endphp

            @if($viewerRank >= 2)
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 space-y-4">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white border-b border-gray-100 dark:border-gray-700 pb-2">
                         3. Prior Authority Recommendations & Remarks
                    </h3>

                    @if($hasPriorRecommendation)
                    <div class="space-y-4">
                        <!-- Main Supervisor Evaluation (Rank 1) -->
                        @if($viewerRank >= 1 && $extension->main_supervisor_recommendation !== null)
                            <x-role-card role="main_supervisor">
                                <div class="flex items-center justify-between gap-2 sm:gap-4">
                                    <span class="font-bold text-sm text-indigo-900 dark:text-indigo-200">Main Supervisor Recommendation</span>
                                    <span class="px-2.5 py-0.5 rounded text-xs font-bold whitespace-nowrap shrink-0 {{ $extension->main_supervisor_recommendation ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/60 dark:text-emerald-300' : 'bg-red-100 text-red-800 dark:bg-red-900/60 dark:text-red-300' }}">
                                        {{ $extension->main_supervisor_recommendation ? '✓ Recommended' : '❌ Not Recommended' }}
                                    </span>
                                </div>
                                <div class="space-y-1">
                                    <x-feedback-box :text="$extension->main_supervisor_confidential_remark" fallback="Remark not provided" role="main_supervisor" />
                                </div>
                            </x-role-card>
                        @endif

                        <!-- DPGC Evaluation (Rank 2) -->
                        @if($viewerRank > 2 && $extension->dpgc_recommendation !== null)
                            <x-role-card role="dpgc">
                                <div class="flex items-center justify-between gap-2 sm:gap-4">
                                    <span class="font-bold text-sm text-teal-900 dark:text-teal-200">DPGC Recommendation</span>
                                    <span class="px-2.5 py-0.5 rounded text-xs font-bold whitespace-nowrap shrink-0 {{ $extension->dpgc_recommendation ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/60 dark:text-emerald-300' : 'bg-red-100 text-red-800 dark:bg-red-900/60 dark:text-red-300' }}">
                                        {{ $extension->dpgc_recommendation ? '✓ Recommended' : '❌ Not Recommended' }}
                                    </span>
                                </div>
                                <div class="space-y-1">
                                    <x-feedback-box :text="$extension->dpgc_confidential_remark" fallback="Remark not provided" role="dpgc" />
                                </div>
                            </x-role-card>
                        @endif

                        <!-- HOD Evaluation (Rank 3) -->
                        @if($viewerRank > 3 && $extension->hod_recommendation !== null)
                            <x-role-card role="hod">
                                <div class="flex items-center justify-between gap-2 sm:gap-4">
                                    <span class="font-bold text-sm text-sky-900 dark:text-sky-200">HOD Recommendation</span>
                                    <span class="px-2.5 py-0.5 rounded text-xs font-bold whitespace-nowrap shrink-0 {{ $extension->hod_recommendation ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/60 dark:text-emerald-300' : 'bg-red-100 text-red-800 dark:bg-red-900/60 dark:text-red-300' }}">
                                        {{ $extension->hod_recommendation ? '✓ Recommended' : '❌ Not Recommended' }}
                                    </span>
                                </div>
                                <div class="space-y-1">
                                    <x-feedback-box :text="$extension->hod_confidential_remark" fallback="Remark not provided" role="hod" />
                                </div>
                            </x-role-card>
                        @endif

                        <!-- Academic Office Evaluation (Rank 4) -->
                        @if($viewerRank > 4 && $extension->academic_office_recommendation !== null)
                            <x-role-card role="academic_office">
                                <div class="flex items-center justify-between gap-2 sm:gap-4">
                                    <span class="font-bold text-sm text-violet-900 dark:text-violet-200">Academic Office Verification</span>
                                    <span class="px-2.5 py-0.5 rounded text-xs font-bold bg-emerald-100 text-emerald-800 dark:bg-emerald-900/60 dark:text-emerald-300 whitespace-nowrap shrink-0">
                                        ✓ Verified &amp; Forwarded
                                    </span>
                                </div>
                                <div class="space-y-1">
                                    <x-feedback-box :text="$extension->academic_office_confidential_remark" fallback="Remark not provided" role="academic_office" />
                                </div>
                            </x-role-card>
                        @endif
                    </div>
                    @else
                        <div class="flex items-center space-x-2 text-xs text-gray-600 dark:text-gray-500 bg-gray-200/60 dark:bg-gray-900/40 p-3 rounded-lg border border-dashed border-gray-200 dark:border-gray-700/60">
                            <svg class="w-4 h-4 text-gray-400 dark:text-gray-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 4.418 9 8z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3l18 18"></path></svg>
                            <span class="italic font-normal">No prior authority recommendations or comments to display yet.</span>
                        </div>
                    @endif
                </div>
            @endif

            <!-- Section 4: Action Required (Evaluation & Decision - Exact PTS-1 Format) -->
            @if($extension->current_stage === $userRole)
                <form action="{{ route('pts2_extension.endorse', $extension->id) }}" method="POST" class="space-y-8" @submit="clearDraft()">
                    @csrf

                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-indigo-100 dark:border-indigo-900/50 p-6 space-y-6">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white border-b border-indigo-100 dark:border-indigo-900/50 pb-2 flex items-center">
                            @if($viewerRank >= 2)4.
                            @else 3.                            
                            @endif 
                            Endorsement Evaluation & Recommendation 
                        </h3>

                        @if($userRole === 'academic_office')
                            <!-- Academic Office Verification Checkbox & Declaration (Exact PTS-1 Layout) -->
                            <div class="space-y-4">
                                <label class="block font-bold text-gray-900 dark:text-white text-sm">
                                    Academic Office Verification: <span class="text-red-500">*</span>
                                </label>

                                <label class="p-4 rounded-xl border-2 border-emerald-500 bg-emerald-50/50 dark:bg-emerald-950/30 flex items-start space-x-3 cursor-pointer">
                                    <input type="checkbox" name="verified_details" value="1" required x-model="isVerified" class="mt-1 text-emerald-600 focus:ring-emerald-500 rounded w-4 h-4">
                                    <div>
                                        <span class="block font-bold text-sm text-emerald-900 dark:text-emerald-300 uppercase tracking-wide">
                                            ✓ Verification Confirmation
                                        </span>
                                        <p class="text-xs text-gray-700 dark:text-gray-300 mt-1 leading-relaxed">
                                            I have verified all student details, academic records, extension request details, and attached documentation for this PTS-2 Extension submission.
                                        </p>
                                    </div>
                                </label>
                            </div>

                            <!-- Academic Office Verification Remark -->
                            <div class="space-y-2 pt-2">
                                <label class="block font-bold text-gray-900 dark:text-white text-sm">
                                    Verification Remark <span class="text-red-500">*</span>
                                </label>
                                <div class="pt-0.5">
                                    <x-snippet-dropdown target="confidentialRemark" form-type="pts2_extension" role="academic_office" comment-type="verification_remark" />
                                </div>
                                <textarea name="confidential_remark" 
                                          rows="5" 
                                          required 
                                          x-model="confidentialRemark" 
                                          placeholder="Provide mandatory verification remarks" 
                                          class="w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm focus:ring-2 focus:ring-emerald-500 whitespace-pre-wrap">{{ trim(old('confidential_remark')) }}</textarea>
                            </div>

                            <!-- Academic Office: Optional Acting DOAA Dropdown -->
                            <div class="space-y-2 pt-2 border-t border-gray-100 dark:border-gray-700">
                                <label class="block font-bold text-gray-900 dark:text-white text-sm">
                                    Assign Acting DOAA (Optional)
                                </label>
                                <select name="acting_doaa_email" class="w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm focus:ring-2 focus:ring-indigo-500">
                                    <option value="">None (Forward to Default DOAA only)</option>
                                    @foreach($actingDoaaUsers as $actingUser)
                                        <option value="{{ $actingUser->email }}" {{ (old('acting_doaa_email', $extension?->acting_doaa_email) === $actingUser->email) ? 'selected' : '' }}>
                                            {{ $actingUser->name }} ({{ $actingUser->email }})
                                        </option>
                                    @endforeach
                                </select>
                                <p class="text-xs text-gray-500 dark:text-gray-400">
                                    If selected, this form will be visible and actionable for the chosen Acting DOAA alongside the DOAA.
                                </p>
                            </div>
                        @else
                            @if($userRole === 'doaa')
                                <x-recommendation-block 
                                    label="Approval Status for Student PTS-2 Extension Submission"
                                    positive-label="(a) APPROVE"
                                    positive-desc="Approve the student's PTS-2 extension submission."
                                    negative-label="(b) DO NOT APPROVE"
                                    negative-desc="Do not approve the student's PTS-2 extension submission."
                                    remark-label-positive="Recommendation Remark (Optional)"
                                    remark-label-negative="Non-Recommendation Remark"
                                    remark-placeholder-positive="Optional evaluation remarks for higher academic authorities"
                                    remark-placeholder-negative="Provide mandatory non-recommendation remarks"
                                    remark-rows="5"
                                    :remark-value="old('confidential_remark')" />
                            @else
                                <x-recommendation-block 
                                    label="Recommendation Status for Student PTS-2 Extension Submission"
                                    positive-desc="Recommend the student's PTS-2 extension submission for forwarding to the next stage in the academic pipeline."
                                    negative-desc="Do not recommend the submission in its present form without further improvements."
                                    remark-placeholder-positive="Optional evaluation remarks for higher academic authorities"
                                    remark-placeholder-negative="Provide mandatory non-recommendation remarks"
                                    remark-rows="5"
                                    :remark-value="old('confidential_remark')" />
                            @endif
                        @endif

                        <!-- Item 3: Approved Date & Mandatory Student Remarks specifically for DOAA -->
                        @if($userRole === 'doaa')
                            <div x-show="recommendation === '1'" class="p-4 bg-purple-50 dark:bg-purple-950/40 border border-purple-200 dark:border-purple-800 rounded-xl space-y-2">
                                <label class="block text-sm font-bold text-purple-900 dark:text-purple-200">
                                    Approved Extension Until Date <span class="text-red-500">*</span>
                                </label>
                                <input type="date" 
                                       name="approved_extended_until_date" 
                                       :required="recommendation === '1'" 
                                       :disabled="recommendation !== '1'"
                                       min="{{ isset($minExtensionDate) && $minExtensionDate ? $minExtensionDate->format('Y-m-d') : '' }}"
                                       max="{{ isset($maxExtensionDate) && $maxExtensionDate ? $maxExtensionDate->format('Y-m-d') : '' }}"
                                       x-model="approvedExtendedUntilDate"
                                       value="{{ old('approved_extended_until_date', $extension->extended_until_date ? $extension->extended_until_date->format('Y-m-d') : '') }}" 
                                       class="w-full md:w-1/2 rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:[color-scheme:dark] text-sm focus:ring-2 focus:ring-purple-500">
                                <p class="text-xs text-purple-700 dark:text-purple-300">
                                    Must be between 16 days ({{ isset($minExtensionDate) && $minExtensionDate ? $minExtensionDate->format('d-M-Y') : 'N/A' }}) and 30 days ({{ isset($maxExtensionDate) && $maxExtensionDate ? $maxExtensionDate->format('d-M-Y') : 'N/A' }}) from Open Seminar. Defaults to student's requested date ({{ $extension->extended_until_date ? $extension->extended_until_date->format('d-M-Y') : 'N/A' }}).
                                </p>
                            </div>

                            <div class="space-y-2 pt-2">
                                <label class="block text-sm font-bold text-indigo-900 dark:text-indigo-200">
                                    Mandatory Remarks for Student <span class="text-red-500">*</span>
                                </label>
                                <textarea name="doaa_student_comment" 
                                          rows="3" 
                                          required 
                                          x-model="doaaStudentComment" 
                                          placeholder="Enter comments specifically visible to the student upon completion" 
                                          class="w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm focus:ring-2 focus:ring-indigo-500 whitespace-pre-wrap">{{ trim(old('doaa_student_comment')) }}</textarea>
                            </div>
                        @endif
                    </div>

                    <!-- Submit & Revert Action Buttons Bar (Exact PTS-1 Layout) -->
                    <div class="flex flex-col-reverse sm:flex-row items-center sm:justify-end gap-3 pt-4">
                        @if($userRole !== 'academic_office')
                            <!-- Revert Button (Triggers Independent Pop-Up Modal) -->
                            <button type="button" 
                                    @click="showRevertModal = true" 
                                    class="w-full sm:w-auto justify-center bg-red-600 hover:bg-red-700 text-white text-base font-bold px-6 py-3 rounded-xl shadow-lg transition flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"></path>
                                </svg>
                                Revert Form
                            </button>
                        @endif

                        <!-- Main Submit Button (Forwards or Approves depending on stage) -->
                        @if($userRole === 'academic_office')
                            <button type="submit" 
                                    :disabled="!isVerified" 
                                    :class="!isVerified ? 'bg-gray-400 opacity-50 cursor-not-allowed shadow-none' : 'bg-emerald-600 hover:bg-emerald-700 shadow-lg'"
                                    class="w-full sm:w-auto justify-center text-white text-base font-bold px-8 py-3 rounded-xl transition flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                                </svg>
                                Submit &amp; Forward
                            </button>
                        @else
                            <button type="submit" 
                                    class="w-full sm:w-auto justify-center text-white text-base font-bold px-8 py-3 rounded-xl shadow-lg transition flex items-center"
                                    :class="recommendation === '0' && '{{ $userRole }}' === 'doaa' ? 'bg-red-600 hover:bg-red-700' : 'bg-emerald-600 hover:bg-emerald-700'">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                                </svg>
                                Submit @if($userRole !== 'doaa') &amp; Forward @endif
                            </button>
                        @endif
                    </div>
                </form>

                <!-- Revert Confirmation Pop-Up Modal -->
                <x-revert-modal 
                    show="showRevertModal" 
                    :action="route('pts2_extension.revert', $extension->id)" 
                    :title="__('Revert :prefix-2 Extension to Student', ['prefix' => $formPrefix])"
                    subtitle="Send form back to student for required changes"
                    onSubmit="clearDraft()"
                />
            @endif

        </div>
    </div>

    <script>
        function pts2ExtensionReviewForm() {
            const userId = @js(auth()->id());
            const thesisId = @js($extension->thesis_id);
            const formId = @js($extension->id);
            const draftKey = 'pts2_extension_review_draft_user_' + userId + '_thesis_' + thesisId + '_form_' + formId;

            let savedDraft = {};
            try {
                savedDraft = JSON.parse(sessionStorage.getItem(draftKey) || '{}');
            } catch (e) {}

            return {
                showRevertModal: false,
                recommendation: @js(old('recommendation')) || savedDraft.recommendation || '1',
                isVerified: savedDraft.isVerified !== undefined ? savedDraft.isVerified : false,
                confidentialRemark: @js(old('confidential_remark')) || savedDraft.confidentialRemark || '',
                approvedExtendedUntilDate: @js(old('approved_extended_until_date')) || savedDraft.approvedExtendedUntilDate || @js($extension->extended_until_date ? $extension->extended_until_date->format('Y-m-d') : ''),
                doaaStudentComment: @js(old('doaa_student_comment')) || savedDraft.doaaStudentComment || '',

                init() {
                    const watchFields = ['recommendation', 'isVerified', 'confidentialRemark', 'approvedExtendedUntilDate', 'doaaStudentComment'];
                    watchFields.forEach(field => {
                        this.$watch(field, () => this.saveDraft());
                    });
                },

                saveDraft() {
                    try {
                        sessionStorage.setItem(draftKey, JSON.stringify({
                            recommendation: this.recommendation,
                            isVerified: this.isVerified,
                            confidentialRemark: this.confidentialRemark,
                            approvedExtendedUntilDate: this.approvedExtendedUntilDate,
                            doaaStudentComment: this.doaaStudentComment,
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

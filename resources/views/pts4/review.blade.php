<x-app-layout>
    @php
        $user = $user ?? auth()->user();
        $formPrefix = isset($student) && $student->isPhd() ? 'PTS' : 'MSRTS';
    @endphp
    <div class="py-6" x-data="pts4EndorseForm()">
        <div class="max-w-5xl mx-auto px-2 sm:px-6 lg:px-8 space-y-4">

            <!-- Top Back to Dashboard Button -->
            <div>
                <x-back-to-dashboard-button />
            </div>

            <!-- Page Header Card -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-5 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div class="space-y-1">
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white leading-tight">
                        {{ __("Review & Endorse {$formPrefix}-4 Thesis Form") }}
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

            <!-- Common Form Information Sections (1 to 3) -->
            @include('pts4.form_info', ['pts4' => $pts4])

            <!-- Section 4: Authority Recommendations & Remarks -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 space-y-6">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white border-b border-gray-100 dark:border-gray-700 pb-2">
                    4. Authority Recommendations &amp; Remarks
                </h3>

                @php
                    $currStage = $pts4->current_stage;
                    $isDept = false;
                    $isGlobal = in_array($currStage, ['academic_office', 'dr']);
                    $isDeptOrGlobal = $isDept || $isGlobal;
                    $defaultOpen = $isDept;

                    $passedCoSupervisors = $pts4->hasPassedStage('co_supervisors');
                    $activeCoSupervisors = [];
                    foreach ($coSupervisors as $i => $coUser) {
                        $remCol = "co_supervisor_{$i}_confidential_remark";
                        $recCol = "co_supervisor_{$i}_recommendation";
                        $comCol = "co_supervisor_{$i}_student_comment";
                        if (!is_null($pts4->$remCol) || !is_null($pts4->$recCol) || !is_null($pts4->$comCol) || $passedCoSupervisors) {
                            $isExt = $coUser->isExternalSupervisor();
                            $activeCoSupervisors[] = [
                                'slot' => $i,
                                'user' => $coUser,
                                'roleTitle' => $student ? $student->getSupervisorRoleTitle($coUser) : ($isExt ? 'External Supervisor' : "Co-Supervisor {$i}"),
                                'inst' => ($isExt && $coUser->externalSupervisorProfile?->affiliated_institute) ? ' - ' . $coUser->externalSupervisorProfile->affiliated_institute : '',
                                'recommended' => (bool)$pts4->$recCol,
                                'studentComment' => $pts4->$comCol,
                                'remark' => $pts4->$remCol,
                            ];
                        }
                    }

                    $immediateStage = match($currStage) {
                        'academic_office' => (!empty($activeCoSupervisors) ? 'co_supervisors' : 'main_supervisor'),
                        'dr' => 'academic_office',
                        default => null,
                    };

                    $hasEarlierStages = false;
                    if ($isDeptOrGlobal && $immediateStage) {
                        if ($immediateStage === 'co_supervisors' && $pts4->hasPassedStage('main_supervisor')) {
                            $hasEarlierStages = true;
                        } elseif ($immediateStage === 'academic_office') {
                            $hasEarlierStages = $pts4->hasPassedStage('main_supervisor') || !empty($activeCoSupervisors);
                        }
                    }
                @endphp

                <div class="space-y-4">
                    @if($isDeptOrGlobal && $immediateStage)
                        <!-- Earlier Authority Recommendations (Collapsed by default for Global, Open for Dept) -->
                        @if($hasEarlierStages)
                            <x-collapsible-earlier-remarks :default-open="$defaultOpen">
                                <!-- Main Supervisor Evaluation -->
                                @if($pts4->hasPassedStage('main_supervisor') && $immediateStage !== 'main_supervisor')
                                    <x-role-card role="main_supervisor">
                                        <div class="flex items-center justify-between text-base gap-2 sm:gap-4">
                                            <span class="font-bold text-indigo-900 dark:text-indigo-200 text-base">
                                                Main Supervisor @if(isset($mainSupervisor))<span class="block sm:inline text-xs font-normal text-gray-500 dark:text-gray-400 mt-0.5 sm:mt-0">({{ $mainSupervisor->name }})</span>@endif
                                            </span>
                                            @if(!is_null($pts4->main_supervisor_recommendation))
                                                <span class="font-bold text-xs whitespace-nowrap shrink-0 {{ $pts4->main_supervisor_recommendation ? 'text-emerald-600 dark:text-emerald-400' : 'text-red-600 dark:text-red-400' }}">
                                                    {{ $pts4->main_supervisor_recommendation ? '✓ Recommended' : '❌ Not Recommended' }}
                                                </span>
                                            @endif
                                        </div>
                                        <div class="text-xs text-gray-700 dark:text-gray-300 space-y-1">
                                            <strong>Student Comment:</strong>
                                            <x-feedback-box :text="$pts4->main_supervisor_student_comment" fallback="Comment not provided" role="main_supervisor" />
                                        </div>
                                        <div class="text-xs text-gray-700 dark:text-gray-300 space-y-1">
                                            <strong>Confidential Remark:</strong>
                                            <x-feedback-box :text="$pts4->main_supervisor_confidential_remark" fallback="Remark not provided" role="main_supervisor" />
                                        </div>
                                    </x-role-card>
                                @endif

                                <!-- Co-Supervisors Recommendations -->
                                @if(!empty($activeCoSupervisors) && $immediateStage !== 'co_supervisors')
                                    <x-role-card role="co_supervisor">
                                        <h5 class="text-base font-bold text-blue-900 dark:text-blue-200">Co-Supervisors</h5>
                                        
                                        @foreach($activeCoSupervisors as $index => $co)
                                            <div class="text-xs space-y-1.5 pt-1.5 {{ $index > 0 ? 'border-t border-blue-100 dark:border-blue-900' : '' }}">
                                                <div class="flex items-center justify-between font-semibold text-sm gap-2 sm:gap-4">
                                                    <span>{{ $co['roleTitle'] }} <span class="block sm:inline text-xs font-normal text-gray-500 dark:text-gray-400 mt-0.5 sm:mt-0">({{ $co['user']->name }}{{ $co['inst'] }})</span>:</span>
                                                    <span class="font-bold whitespace-nowrap shrink-0 {{ $co['recommended'] ? 'text-emerald-600 dark:text-emerald-400' : 'text-red-600 dark:text-red-400' }}">
                                                        {{ $co['recommended'] ? '✓ Recommended' : '❌ Not Recommended' }}
                                                    </span>
                                                </div>
                                                <div class="space-y-1">
                                                    <strong>Student Comment:</strong>
                                                    <x-feedback-box :text="$co['studentComment']" fallback="Comment not provided" role="co_supervisor" />
                                                </div>
                                                <div class="space-y-1">
                                                    <strong>Confidential Remark:</strong>
                                                    <x-feedback-box :text="$co['remark']" fallback="Remark not provided" role="co_supervisor" />
                                                </div>
                                            </div>
                                        @endforeach
                                    </x-role-card>
                                @endif
                            </x-collapsible-earlier-remarks>
                        @endif

                        <!-- Immediate Previous Stage (Always Open / Visible directly) -->
                        @if($immediateStage === 'main_supervisor' && $pts4->hasPassedStage('main_supervisor'))
                            <x-role-card role="main_supervisor">
                                <div class="flex items-center justify-between text-base gap-2 sm:gap-4">
                                    <span class="font-bold text-indigo-900 dark:text-indigo-200 text-base">
                                        Main Supervisor @if(isset($mainSupervisor))<span class="block sm:inline text-xs font-normal text-gray-500 dark:text-gray-400 mt-0.5 sm:mt-0">({{ $mainSupervisor->name }})</span>@endif
                                    </span>
                                    @if(!is_null($pts4->main_supervisor_recommendation))
                                        <span class="font-bold text-xs whitespace-nowrap shrink-0 {{ $pts4->main_supervisor_recommendation ? 'text-emerald-600 dark:text-emerald-400' : 'text-red-600 dark:text-red-400' }}">
                                            {{ $pts4->main_supervisor_recommendation ? '✓ Recommended' : '❌ Not Recommended' }}
                                        </span>
                                    @endif
                                </div>
                                <div class="text-xs text-gray-700 dark:text-gray-300 space-y-1">
                                    <strong>Student Comment:</strong>
                                    <x-feedback-box :text="$pts4->main_supervisor_student_comment" fallback="Comment not provided" role="main_supervisor" />
                                </div>
                                <div class="text-xs text-gray-700 dark:text-gray-300 space-y-1">
                                    <strong>Confidential Remark:</strong>
                                    <x-feedback-box :text="$pts4->main_supervisor_confidential_remark" fallback="Remark not provided" role="main_supervisor" />
                                </div>
                            </x-role-card>
                        @elseif($immediateStage === 'co_supervisors' && !empty($activeCoSupervisors))
                            <x-role-card role="co_supervisor">
                                <h5 class="text-base font-bold text-blue-900 dark:text-blue-200">Co-Supervisors</h5>
                                
                                @foreach($activeCoSupervisors as $index => $co)
                                    <div class="text-xs space-y-1.5 pt-1.5 {{ $index > 0 ? 'border-t border-blue-100 dark:border-blue-900' : '' }}">
                                        <div class="flex items-center justify-between font-semibold text-sm gap-2 sm:gap-4">
                                            <span>{{ $co['roleTitle'] }} <span class="block sm:inline text-xs font-normal text-gray-500 dark:text-gray-400 mt-0.5 sm:mt-0">({{ $co['user']->name }}{{ $co['inst'] }})</span>:</span>
                                            <span class="font-bold whitespace-nowrap shrink-0 {{ $co['recommended'] ? 'text-emerald-600 dark:text-emerald-400' : 'text-red-600 dark:text-red-400' }}">
                                                {{ $co['recommended'] ? '✓ Recommended' : '❌ Not Recommended' }}
                                            </span>
                                        </div>
                                        <div class="space-y-1">
                                            <strong>Student Comment:</strong>
                                            <x-feedback-box :text="$co['studentComment']" fallback="Comment not provided" role="co_supervisor" />
                                        </div>
                                        <div class="space-y-1">
                                            <strong>Confidential Remark:</strong>
                                            <x-feedback-box :text="$co['remark']" fallback="Remark not provided" role="co_supervisor" />
                                        </div>
                                    </div>
                                @endforeach
                            </x-role-card>
                        @elseif($immediateStage === 'academic_office' && $pts4->hasPassedStage('academic_office'))
                            @php
                                $academicOfficeRemark = $pts4->academic_office_verification_remark;
                            @endphp
                            <x-role-card role="academic_office" title="Academic Office">
                                <x-slot:badge>
                                    <span class="font-bold text-xs text-emerald-600 dark:text-emerald-400 whitespace-nowrap shrink-0">
                                        ✓ Verified &amp; Forwarded
                                    </span>
                                </x-slot:badge>
                                <div class="text-xs text-gray-700 dark:text-gray-300 space-y-1">
                                    <strong>Verification Remark:</strong>
                                    <x-feedback-box :text="$academicOfficeRemark" fallback="Remark not provided" role="academic_office" />
                                </div>
                            </x-role-card>
                        @endif
                    @else
                        <!-- Stages before Dept/Global - Render all passed stages directly -->
                        <!-- Main Supervisor Evaluation -->
                        @if($pts4->hasPassedStage('main_supervisor'))
                            <x-role-card role="main_supervisor">
                                <div class="flex items-center justify-between text-base gap-2 sm:gap-4">
                                    <span class="font-bold text-indigo-900 dark:text-indigo-200 text-base">
                                        Main Supervisor @if(isset($mainSupervisor))<span class="block sm:inline text-xs font-normal text-gray-500 dark:text-gray-400 mt-0.5 sm:mt-0">({{ $mainSupervisor->name }})</span>@endif
                                    </span>
                                    @if(!is_null($pts4->main_supervisor_recommendation))
                                        <span class="font-bold text-xs whitespace-nowrap shrink-0 {{ $pts4->main_supervisor_recommendation ? 'text-emerald-600 dark:text-emerald-400' : 'text-red-600 dark:text-red-400' }}">
                                            {{ $pts4->main_supervisor_recommendation ? '✓ Recommended' : '❌ Not Recommended' }}
                                        </span>
                                    @endif
                                </div>
                                <div class="text-xs text-gray-700 dark:text-gray-300 space-y-1">
                                    <strong>Student Comment:</strong>
                                    <x-feedback-box :text="$pts4->main_supervisor_student_comment" fallback="Comment not provided" role="main_supervisor" />
                                </div>
                                <div class="text-xs text-gray-700 dark:text-gray-300 space-y-1">
                                    <strong>Confidential Remark:</strong>
                                    <x-feedback-box :text="$pts4->main_supervisor_confidential_remark" fallback="Remark not provided" role="main_supervisor" />
                                </div>
                            </x-role-card>
                        @endif

                        <!-- Co-Supervisors Recommendations -->
                        @if(!empty($activeCoSupervisors))
                            <x-role-card role="co_supervisor">
                                <h5 class="text-base font-bold text-blue-900 dark:text-blue-200">Co-Supervisors</h5>
                                
                                @foreach($activeCoSupervisors as $index => $co)
                                    <div class="text-xs space-y-1.5 pt-1.5 {{ $index > 0 ? 'border-t border-blue-100 dark:border-blue-900' : '' }}">
                                        <div class="flex items-center justify-between font-semibold text-sm gap-2 sm:gap-4">
                                            <span>{{ $co['roleTitle'] }} <span class="block sm:inline text-xs font-normal text-gray-500 dark:text-gray-400 mt-0.5 sm:mt-0">({{ $co['user']->name }}{{ $co['inst'] }})</span>:</span>
                                            <span class="font-bold whitespace-nowrap shrink-0 {{ $co['recommended'] ? 'text-emerald-600 dark:text-emerald-400' : 'text-red-600 dark:text-red-400' }}">
                                                {{ $co['recommended'] ? '✓ Recommended' : '❌ Not Recommended' }}
                                            </span>
                                        </div>
                                        <div class="space-y-1">
                                            <strong>Student Comment:</strong>
                                            <x-feedback-box :text="$co['studentComment']" fallback="Comment not provided" role="co_supervisor" />
                                        </div>
                                        <div class="space-y-1">
                                            <strong>Confidential Remark:</strong>
                                            <x-feedback-box :text="$co['remark']" fallback="Remark not provided" role="co_supervisor" />
                                        </div>
                                    </div>
                                @endforeach
                            </x-role-card>
                        @endif

                        <!-- Academic Office Verification -->
                        @if($pts4->hasPassedStage('academic_office'))
                            @php
                                $academicOfficeRemark = $pts4->academic_office_verification_remark;
                            @endphp
                            <x-role-card role="academic_office" title="Academic Office">
                                <x-slot:badge>
                                    <span class="font-bold text-xs text-emerald-600 dark:text-emerald-400 whitespace-nowrap shrink-0">
                                        ✓ Verified &amp; Forwarded
                                    </span>
                                </x-slot:badge>
                                <div class="text-xs text-gray-700 dark:text-gray-300 space-y-1">
                                    <strong>Verification Remark:</strong>
                                    <x-feedback-box :text="$academicOfficeRemark" fallback="Remark not provided" role="academic_office" />
                                </div>
                            </x-role-card>
                        @endif

                        <!-- DR Acceptance -->
                        @if($pts4->hasPassedStage('dr'))
                            <x-role-card role="dr" title="Deputy Registrar (DR)">
                                <x-slot:badge>
                                    <span class="font-bold text-xs whitespace-nowrap shrink-0 {{ $pts4->dr_acceptance ? 'text-emerald-600 dark:text-emerald-400' : 'text-red-600 dark:text-red-400' }}">
                                        {{ $pts4->dr_acceptance ? '✓ Accepted' : '❌ Not Accepted' }}
                                    </span>
                                </x-slot:badge>
                                <div class="text-xs text-gray-700 dark:text-gray-300 space-y-1">
                                    <strong>Student Comment:</strong>
                                    <x-feedback-box :text="$pts4->dr_student_comment" fallback="Comment not provided" role="dr" />
                                </div>
                                <div class="text-xs text-gray-700 dark:text-gray-300 space-y-1">
                                    <strong>Confidential Remark:</strong>
                                    <x-feedback-box :text="$pts4->dr_confidential_remark" fallback="Remark not provided" role="dr" />
                                </div>
                            </x-role-card>
                        @endif
                    @endif
                </div>
            </div>

            <!-- Section 5: Action Required (Evaluation & Decision) -->
            <form action="{{ route('pts4.endorse', $pts4->id) }}" method="POST" class="space-y-8" @submit="clearDraft()">
                @csrf

                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-indigo-100 dark:border-indigo-900/50 p-6 space-y-6">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white border-b border-indigo-100 dark:border-indigo-900/50 pb-2 flex items-center">
                            5. Endorsement Evaluation &amp; Recommendation
                        </h3>

                        <!-- Stage-Specific Evaluation Inputs -->
                        @if($pts4->current_stage === 'academic_office')
                            <div class="space-y-6">
                                <!-- Verification Confirmation -->
                                <div class="space-y-2 pt-2">
                                    <label class="block font-bold text-gray-900 dark:text-white text-sm">
                                        Verification: <span class="text-red-500">*</span>
                                    </label>

                                    <label class="p-4 rounded-xl border-2 border-emerald-500 bg-emerald-50/50 dark:bg-emerald-950/30 flex items-start space-x-3 cursor-pointer">
                                        <input type="checkbox" name="verified_details" value="1" required x-model="isVerified" class="mt-1 text-emerald-600 focus:ring-emerald-500 rounded w-4 h-4">
                                        <div>
                                            <span class="block font-bold text-sm text-emerald-900 dark:text-emerald-300 uppercase tracking-wide">
                                                ✓ Verification Confirmation
                                            </span>
                                            <p class="text-xs text-gray-700 dark:text-gray-300 mt-1 leading-relaxed">
                                                I have verified all student details, academic records, open seminar date, and attached thesis documentation for this PTS-4 submission.
                                            </p>
                                        </div>
                                    </label>
                                </div>
                            </div>
                        @elseif ($pts4->current_stage === 'dr')
                            <x-recommendation-block 
                                label="Acceptance Status for Student PTS-4 Thesis Submission"
                                positive-label="(a) ACCEPT"
                                positive-desc="Accept the student's PTS-4 thesis submission."
                                negative-label="(b) DO NOT ACCEPT"
                                negative-desc="Do not accept the student's PTS-4 thesis submission."
                                remark-label-positive="Acceptance Remark (Optional)"
                                remark-label-negative="Non-Acceptance Remark"
                                remark-placeholder-positive="Optional acceptance remarks"
                                remark-placeholder-negative="Provide mandatory non-acceptance remarks"
                                remark-rows="5"
                                form-type="pts4"
                                role="dr"
                                :remark-value="old('confidential_remark')" />

                            <x-student-comment-input :value="old('student_comment')" form-type="pts4" role="dr" />
                        @else
                            <x-recommendation-block 
                                label="Recommendation Status for Student Thesis Submission"
                                form-type="pts4"
                                role="co_supervisor"
                                remark-rows="3"
                                :remark-value="old('confidential_remark')" />

                            <x-student-comment-input :value="old('student_comment')" form-type="pts4" role="co_supervisor" />
                        @endif
                        
                        @if($pts4->current_stage === 'academic_office')
                            <!-- Academic Office Verification Remark -->
                            <div class="space-y-2 pt-2">
                                <label class="block font-bold text-gray-900 dark:text-white text-sm">
                                    Verification Remark <span class="text-red-500">*</span>
                                </label>
                                <div class="pt-0.5">
                                    <x-snippet-dropdown target="verificationRemark" form-type="pts4" role="academic_office" comment-type="verification_remark" />
                                </div>
                                <textarea name="verification_remark" 
                                           rows="5" 
                                           required 
                                           x-model="verificationRemark" 
                                           placeholder="Provide mandatory verification remarks" 
                                           class="w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm focus:ring-2 focus:ring-indigo-500 whitespace-pre-wrap">{{ trim(old('verification_remark')) }}</textarea>
                            </div>
                        @endif 
                    </div>
                    
                    <!-- Submit & Revert Action Buttons Bar -->
                    <div class="flex flex-col-reverse sm:flex-row items-center sm:justify-end gap-3 pt-4">
                        @if(!$user->isAcademicOffice())
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

                        <!-- Main Submit Button -->
                        @if($pts4->current_stage === 'academic_office')
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
                                    class="w-full sm:w-auto justify-center bg-emerald-600 hover:bg-emerald-700 text-white text-base font-bold px-8 py-3 rounded-xl shadow-lg transition flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                                </svg>
                                Submit @if($pts4->current_stage !== 'dr') &amp; Forward @endif
                            </button>
                        @endif
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
    </div>

    <script>
        function pts4EndorseForm() {
            const userId = @js(auth()->id());
            const thesisId = @js($thesis->id);
            const formId = @js($pts4->id);
            const draftKey = 'pts4_review_draft_user_' + userId + '_thesis_' + thesisId + '_form_' + formId;

            let savedDraft = {};
            try {
                savedDraft = JSON.parse(sessionStorage.getItem(draftKey) || '{}');
            } catch (e) {}

            return {
                showRevertModal: false,
                recommendation: @js(old('recommendation')) || savedDraft.recommendation || '1',
                isVerified: savedDraft.isVerified !== undefined ? savedDraft.isVerified : false,
                confidentialRemark: @js(old('dr_confidential_remark') ?? old('confidential_remark')) || savedDraft.confidentialRemark || '',
                studentComment: @js(old('dr_student_comment') ?? old('student_comment')) || savedDraft.studentComment || '',
                verificationRemark: @js(old('verification_remark')) || savedDraft.verificationRemark || '',

                init() {
                    const watchFields = [
                        'recommendation',
                        'isVerified',
                        'confidentialRemark',
                        'studentComment',
                        'verificationRemark'
                    ];
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
                            studentComment: this.studentComment,
                            verificationRemark: this.verificationRemark,
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

<x-app-layout>
    @php
        $user = $user ?? auth()->user();
        $formPrefix = isset($student) && $student->isPhd() ? 'PTS' : 'MSRTS';
    @endphp
    <div class="py-6" x-data="pts1EndorseForm()">
        <div class="max-w-5xl mx-auto px-2 sm:px-6 lg:px-8 space-y-4">

            <!-- Top Back to Dashboard Button -->
            <div>
                <x-back-to-dashboard-button />
            </div>

            <!-- Page Header Card -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-5 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div class="space-y-1">
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white leading-tight">
                        {{ __("Review & Endorse {$formPrefix}-1 Form") }}
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

            @if(session('info'))
                <div x-data="{ show: true }" x-show="show" x-transition class="p-4 bg-blue-100 dark:bg-blue-900/40 border-l-4 border-blue-500 text-blue-800 dark:text-blue-200 rounded-xl shadow-sm flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <svg class="w-5 h-5 text-blue-600 dark:text-blue-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <span class="font-bold text-sm">{{ session('info') }}</span>
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

            <!-- Common Form Information Sections (1 to 5) -->
            @include('pts1.form_info', ['pts1' => $pts1])

            <!-- Section 6: Authority Recommendations & Remarks -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 space-y-6">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white border-b border-gray-100 dark:border-gray-700 pb-2">
                    6. Authority Recommendations & Remarks
                </h3>

                <div class="space-y-4">
                    <!-- Main Supervisor Evaluation -->
                    @if($pts1->hasPassedStage('main_supervisor'))
                        <x-role-card role="main_supervisor">
                            <div class="flex items-center justify-between text-sm font-bold gap-2 sm:gap-4">
                                <span class="font-bold text-indigo-900 dark:text-indigo-200">
                                    Main Supervisor @if(isset($mainSupervisor))<span class="block sm:inline text-xs font-normal text-gray-500 dark:text-gray-400 mt-0.5 sm:mt-0">({{ $mainSupervisor->name }})</span>@endif
                                </span>
                                <span class="px-2.5 py-1 bg-emerald-600 text-white font-bold rounded-lg uppercase text-[10px] text-center leading-tight whitespace-normal max-w-[120px] sm:max-w-none">
                                    Open Seminar Status: {{ strtoupper($pts1->work_status) }}
                                </span>
                            </div>
                            <div class="text-xs text-gray-700 dark:text-gray-300 space-y-1">
                                <strong>Additional Comment:</strong>
                                <x-feedback-box :text="$pts1->main_supervisor_student_comment" fallback="Comment not provided" role="main_supervisor" />
                            </div>
                       
                            <div class="text-xs text-gray-700 dark:text-gray-300 space-y-1">
                                <strong>Main Supervisor Remark:</strong>
                                <x-feedback-box :text="$pts1->main_supervisor_confidential_remark" fallback="Remark not provided" role="main_supervisor" />
                            </div>
                        </x-role-card>
                    @endif

                    <!-- Co-Supervisors Recommendations (Slots 1..10) -->
                    @php
                        $passedCoSupervisors = $pts1->hasPassedStage('co_supervisors');
                        $hasAnyCoRecommended = false;
                        for ($i = 1; $i <= 10; $i++) {
                            $idCol = "co_supervisor_{$i}_id";
                            $remCol = "co_supervisor_{$i}_confidential_remark";
                            if ($pts1->$idCol && (!is_null($pts1->$remCol) || $passedCoSupervisors)) {
                                $hasAnyCoRecommended = true;
                                break;
                            }
                        }
                    @endphp

                    @if($hasAnyCoRecommended)
                        <x-role-card role="co_supervisor">
                            <h5 class="text-sm font-bold text-blue-900 dark:text-blue-200">Co-Supervisors</h5>
                            
                            @for($i = 1; $i <= 10; $i++)
                                @php
                                    $idCol = "co_supervisor_{$i}_id";
                                    $recCol = "co_supervisor_{$i}_recommendation";
                                    $remCol = "co_supervisor_{$i}_confidential_remark";
                                    $coUser = $coSupervisors[$i] ?? null;
                                @endphp

                                @if($coUser && (!is_null($pts1->$remCol) || $passedCoSupervisors))
                                    @php
                                        $isExt = $coUser->isExternalSupervisor();
                                        $roleTitle = $student ? $student->getSupervisorRoleTitle($coUser) : ($isExt ? 'External Supervisor' : "Co-Supervisor {$i}");
                                        $inst = ($isExt && $coUser->externalSupervisorProfile?->affiliated_institute) ? ' - ' . $coUser->externalSupervisorProfile->affiliated_institute : '';
                                    @endphp
                                    <div class="text-xs space-y-1.5 pt-1.5 {{ $i > 1 ? 'border-t border-blue-100 dark:border-blue-900' : '' }}">
                                        <div class="flex items-center justify-between font-semibold gap-2 sm:gap-4">
                                            <span>{{ $roleTitle }} <span class="block sm:inline text-xs font-normal text-gray-500 dark:text-gray-400 mt-0.5 sm:mt-0">({{ $coUser->name }}{{ $inst }})</span>:</span>
                                            <span class="font-bold whitespace-nowrap shrink-0 {{ $pts1->$recCol ? 'text-emerald-600 dark:text-emerald-400' : 'text-red-600 dark:text-red-400' }}">
                                                {{ $pts1->$recCol ? '✓ Recommended' : '❌ Not Recommended' }}
                                            </span>
                                        </div>
                                        <div class="space-y-1">
                                            <x-feedback-box :text="$pts1->$remCol" fallback="Remark not provided" role="co_supervisor" />
                                        </div>
                                    </div>
                                @endif
                            @endfor
                        </x-role-card>
                    @endif

                    <!-- PSPC Committee Recommendations (Slots 1..10) -->
                    @php
                        $passedPspcMembers = $pts1->hasPassedStage('pspc_members');
                        $hasAnyPspcRecommended = false;
                        for ($i = 1; $i <= 10; $i++) {
                            $idCol = "pspc_member_{$i}_id";
                            $remCol = "pspc_member_{$i}_confidential_remark";
                            if ($pts1->$idCol && (!is_null($pts1->$remCol) || $passedPspcMembers)) {
                                $hasAnyPspcRecommended = true;
                                break;
                            }
                        }
                    @endphp

                    @if($hasAnyPspcRecommended)
                        <x-role-card role="pspc">
                            <h5 class="text-sm font-bold text-cyan-900 dark:text-cyan-200">PSPC Committee</h5>
                            
                            @for($i = 1; $i <= 10; $i++)
                                @php
                                    $idCol = "pspc_member_{$i}_id";
                                    $recCol = "pspc_member_{$i}_recommendation";
                                    $remCol = "pspc_member_{$i}_confidential_remark";
                                    $pspcUser = $pspcMembers[$i] ?? null;
                                @endphp

                                @if($pspcUser && (!is_null($pts1->$remCol) || $passedPspcMembers))
                                    <div class="text-xs space-y-1.5 pt-1.5 {{ $i > 1 ? 'border-t border-cyan-100 dark:border-cyan-900' : '' }}">
                                        <div class="flex items-center justify-between font-semibold gap-2 sm:gap-4">
                                            <span>PSPC Member {{ $i }} <span class="block sm:inline text-xs font-normal text-gray-500 dark:text-gray-400 mt-0.5 sm:mt-0">({{ $pspcUser->name }})</span>:</span>
                                            <span class="font-bold whitespace-nowrap shrink-0 {{ $pts1->$recCol ? 'text-emerald-600 dark:text-emerald-400' : 'text-red-600 dark:text-red-400' }}">
                                                {{ $pts1->$recCol ? '✓ Recommended' : '❌ Not Recommended' }}
                                            </span>
                                        </div>
                                        <div class="space-y-1">
                                            <x-feedback-box :text="$pts1->$remCol" fallback="Remark not provided" role="pspc" />
                                        </div>
                                    </div>
                                @endif
                            @endfor
                        </x-role-card>
                    @endif

                    <!-- DPGC Endorsement -->
                    @if($pts1->hasPassedStage('dpgc'))
                        <x-role-card role="dpgc">
                            <div class="flex items-center justify-between font-bold text-teal-900 dark:text-teal-200 gap-2 sm:gap-4">
                                <h5 class="text-sm font-bold">Department Postgraduate Committee (DPGC)</h5>
                                <span class="font-bold text-xs whitespace-nowrap shrink-0 {{ $pts1->dpgc_recommendation ? 'text-emerald-600 dark:text-emerald-400' : 'text-red-600 dark:text-red-400' }}">
                                    {{ $pts1->dpgc_recommendation ? '✓ Recommended' : '❌ Not Recommended' }}
                                </span>
                            </div>
                            <div class="text-xs text-gray-700 dark:text-gray-300 space-y-1">
                                <strong>Student Comment:</strong>
                                <x-feedback-box :text="$pts1->dpgc_student_comment" fallback="Comment not provided" role="dpgc" />
                            </div>
                            <div class="text-xs text-gray-700 dark:text-gray-300 space-y-1">
                                <strong>DPGC Remark:</strong>
                                <x-feedback-box :text="$pts1->dpgc_confidential_remark" fallback="Remark not provided" role="dpgc" />
                            </div>
                        </x-role-card>
                    @endif

                    <!-- HOD Endorsement -->
                    @if($pts1->hasPassedStage('hod'))
                        <x-role-card role="hod">
                            <div class="flex items-center justify-between font-bold text-sky-900 dark:text-sky-200 gap-2 sm:gap-4">
                                <h5 class="text-sm font-bold">Head of Department (HOD)</h5>
                                <span class="font-bold text-xs whitespace-nowrap shrink-0 {{ $pts1->hod_recommendation ? 'text-emerald-600 dark:text-emerald-400' : 'text-red-600 dark:text-red-400' }}">
                                    {{ $pts1->hod_recommendation ? '✓ Recommended' : '❌ Not Recommended' }}
                                </span>
                            </div>
                            <div class="text-xs text-gray-700 dark:text-gray-300 space-y-1">
                                <strong>Student Comment:</strong>
                                <x-feedback-box :text="$pts1->hod_student_comment" fallback="Comment not provided" role="hod" />
                            </div>
                            <div class="text-xs text-gray-700 dark:text-gray-300 space-y-1">
                                <strong>HOD Remark:</strong>
                                <x-feedback-box :text="$pts1->hod_confidential_remark" fallback="Remark not provided" role="hod" />
                            </div>
                        </x-role-card>
                    @endif

                    <!-- Academic Office Endorsement -->
                    @if($pts1->hasPassedStage('academic_office'))
                        <x-role-card role="academic_office">
                            <div class="flex items-center justify-between font-bold text-violet-900 dark:text-violet-200 gap-2 sm:gap-4">
                                <h5 class="text-sm font-bold">Academic Office</h5>
                                <span class="font-bold text-xs text-emerald-600 dark:text-emerald-400 whitespace-nowrap shrink-0">
                                    ✓ Verified &amp; Forwarded
                                </span>
                            </div>
                            <div class="text-xs text-gray-700 dark:text-gray-300 space-y-1">
                                <strong>Verification Remark:</strong>
                                <x-feedback-box :text="$pts1->academic_office_confidential_remark" fallback="Remark not provided" role="academic_office" />
                            </div>
                        </x-role-card>
                    @endif

                    <!-- DOAA Approval -->
                    @if($pts1->hasPassedStage('doaa'))
                        <x-role-card role="doaa">
                            <div class="flex items-center justify-between font-bold text-purple-900 dark:text-purple-200 gap-2 sm:gap-4">
                                <h5 class="text-sm font-bold">Dean of Academic Affairs (DOAA)</h5>
                                <span class="font-bold text-xs whitespace-nowrap shrink-0 {{ $pts1->doaa_approval ? 'text-emerald-600 dark:text-emerald-400' : 'text-red-600 dark:text-red-400' }}">
                                    {{ $pts1->doaa_approval ? '✓ Approved' : '❌ Not Approved' }}
                                    @if($pts1->approvedBy)
                                        by: {{ $pts1->approvedBy->email }}
                                    @endif
                                </span>
                            </div>
                            <div class="text-xs text-gray-700 dark:text-gray-300 space-y-1">
                                <strong>Student Comment:</strong>
                                <x-feedback-box :text="$pts1->doaa_student_comment" fallback="Comment not provided" role="doaa" />
                            </div>
                            <div class="text-xs text-gray-700 dark:text-gray-300 space-y-1">
                                <strong>DOAA Remark:</strong>
                                <x-feedback-box :text="$pts1->doaa_confidential_remark" fallback="Remark not provided" role="doaa" />
                            </div>
                        </x-role-card>
                    @endif
                </div>
            </div>

            <!-- Section 7: Action Required (Evaluation & Decision) -->
            <form action="{{ route('pts1.endorse', $pts1->id) }}" method="POST" class="space-y-8" @submit="clearDraft()">
                @csrf

                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-indigo-100 dark:border-indigo-900/50 p-6 space-y-6">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white border-b border-indigo-100 dark:border-indigo-900/50 pb-2 flex items-center">
                            7. Endorsement Evaluation & Recommendation
                        </h3>

                        <!-- Item 1: Recommendation or Verification Status -->
                        @if($pts1->current_stage === 'academic_office')
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
                                            I have verified all student details, academic records, seminar information, and attached documentation for this PTS-1 submission.
                                        </p>
                                    </div>
                                </label>
                            </div>
                        @elseif($pts1->current_stage === 'doaa')
                            <x-recommendation-block 
                                label="Approval Status for Student Thesis Submission"
                                positive-label="(a) APPROVE"
                                positive-desc="Approve the student's PTS-1 submission."
                                negative-label="(b) DO NOT APPROVE"
                                negative-desc="Do not approve the student's PTS-1 submission."
                                remark-label-positive="Approval Remark (Optional)"
                                remark-label-negative="Non-Approval Remark"
                                remark-placeholder-positive="Optional approval remarks"
                                remark-placeholder-negative="Provide mandatory non-approval remarks"
                                remark-rows="3"
                                :remark-value="old('confidential_remark')" />
                        @else
                            <x-recommendation-block 
                                label="Recommendation Status for Student Thesis Submission"
                                remark-rows="5"
                                :remark-value="old('confidential_remark')" />
                        @endif
                        
                        @if($pts1->current_stage === 'academic_office')
                            <!-- Item 2: Academic Office Mandatory Verification Remark -->
                            <div class="space-y-2 pt-2">
                                <label class="block font-bold text-gray-900 dark:text-white text-sm">
                                    Verification Remark <span class="text-red-500">*</span>
                                </label>
                                <div class="pt-0.5">
                                    <x-snippet-dropdown target="confidentialRemark" form-type="pts1" role="academic_office" comment-type="verification_remark" />
                                </div>
                                <textarea name="confidential_remark" 
                                          rows="5" 
                                          required 
                                          x-model="confidentialRemark" 
                                          placeholder="Provide mandatory verification remarks" 
                                          class="w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm focus:ring-2 focus:ring-indigo-500 whitespace-pre-wrap">{{ trim(old('confidential_remark')) }}</textarea>
                            </div>
                        @endif

                        <!-- Item 3: Student Comments Textarea (For DPGC, HOD, DOAA) -->
                        @if(in_array($pts1->current_stage, ['dpgc', 'hod', 'doaa']))
                            <x-student-comment-input :value="old('student_comment')" />
                        @endif

                        <!-- Academic Office: Optional Acting DOAA Dropdown -->
                        @if($pts1->current_stage === 'academic_office')
                            <div class="space-y-2 pt-2 border-t border-gray-100 dark:border-gray-700">
                                <label class="block font-bold text-gray-900 dark:text-white text-sm">
                                    Assign Acting DOAA (Optional)
                                </label>
                                <select name="acting_doaa_email" class="w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm focus:ring-2 focus:ring-indigo-500">
                                    <option value="">None (Forward to Default DOAA only)</option>
                                    @foreach($actingDoaaUsers as $actingUser)
                                        <option value="{{ $actingUser->email }}" {{ (old('acting_doaa_email', $pts1?->acting_doaa_email) === $actingUser->email) ? 'selected' : '' }}>
                                            {{ $actingUser->name }} ({{ $actingUser->email }})
                                        </option>
                                    @endforeach
                                </select>
                                <p class="text-xs text-gray-500 dark:text-gray-400">
                                    If selected, this form will be visible and actionable for the chosen Acting DOAA alongside the DOAA.
                                </p>
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

                        <!-- Main Submit Button (Forwards or Approves depending on stage) -->
                        @if($pts1->current_stage === 'academic_office')
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
                                    :class="recommendation === '0' && '{{ $pts1->current_stage }}' === 'doaa' ? 'bg-red-600 hover:bg-red-700' : 'bg-emerald-600 hover:bg-emerald-700'">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                                </svg>
                                @if($pts1->current_stage === 'doaa')
                                    <span x-show="recommendation === '1'">Approve PTS-1 Form</span>
                                    <span x-show="recommendation === '0'">Reject PTS-1 Form</span>
                                @else
                                    Submit &amp; Forward
                                @endif
                            </button>
                        @endif
                    </div>
                </form>

                <!-- Revert Confirmation Pop-Up Modal -->
                <x-revert-modal 
                    show="showRevertModal" 
                    :action="route('pts1.revert', $pts1->id)" 
                    :title="__('Revert :prefix-1 Form to Student', ['prefix' => 'PTS'])"
                    subtitle="Send form back to student for required changes"
                    onSubmit="clearDraft()"
                />

        </div>
    </div>

    @php
        $existingPubListUrl = $pts1->getEffectivePublicationListPath() 
            ? route('pts.document.serve', ['pts1', $pts1->id, $pts1->getEffectivePublicationListField()]) 
            : null;
    @endphp

    <script>
        function pts1EndorseForm() {
            const userId = @js(auth()->id());
            const thesisId = @js($thesis->id);
            const formId = @js($pts1->id);
            const draftKey = 'pts1_review_draft_user_' + userId + '_thesis_' + thesisId + '_form_' + formId;

            let savedDraft = {};
            try {
                savedDraft = JSON.parse(sessionStorage.getItem(draftKey) || '{}');
            } catch (e) {}

            return {
                showRevertModal: false,
                recommendation: @js(old('recommendation')) || savedDraft.recommendation || '1',
                isVerified: savedDraft.isVerified !== undefined ? savedDraft.isVerified : false,
                confidentialRemark: @js(old('confidential_remark')) || savedDraft.confidentialRemark || '',
                studentComment: @js(old('student_comment')) || savedDraft.studentComment || '',

                existingPubListUrl: @json($existingPubListUrl),

                init() {
                    const watchFields = ['recommendation', 'isVerified', 'confidentialRemark', 'studentComment'];
                    watchFields.forEach(field => {
                        this.$watch(field, () => this.saveDraft());
                    });
                    if (this.existingPubListUrl) {
                        this.loadExcelFromUrl(this.existingPubListUrl, 'Existing Publication List Preview');
                    }
                },

                loadExcelFromUrl(url, titlePrefix = 'Existing Publication List Preview') {
                    window.previewExcelUrl(url, { titlePrefix });
                },

                handleExcelPreview(event) {
                    const file = event.target.files[0];
                    if (!file) return;
                    window.previewExcelFile(file, { titlePrefix: 'New Selected Publication List Preview' });
                },

                saveDraft() {
                    try {
                        sessionStorage.setItem(draftKey, JSON.stringify({
                            recommendation: this.recommendation,
                            isVerified: this.isVerified,
                            confidentialRemark: this.confidentialRemark,
                            studentComment: this.studentComment,
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

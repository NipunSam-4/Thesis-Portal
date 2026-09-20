<x-app-layout>
    <div class="py-6">
        <div class="max-w-5xl mx-auto px-2 sm:px-6 lg:px-8 space-y-4">

            <!-- Top Back to Dashboard Button -->
            <div>
                <x-back-to-dashboard-button />
            </div>

            <!-- Page Header Card -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-5 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div class="space-y-1">
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white leading-tight">
                        {{ __('PTS-4 Extension Application Details') }}
                    </h2>
                </div>
                <div class="flex items-center space-x-3 shrink-0">
                    @if($extension->status === 'approved')
                        <span class="px-3.5 py-1.5 bg-emerald-100 dark:bg-emerald-900/60 text-emerald-800 dark:text-emerald-300 text-xs font-extrabold rounded-full uppercase tracking-wider flex items-center shadow-xs border border-emerald-200 dark:border-emerald-800">
                            ✓ Approved
                        </span>
                    @elseif($extension->status === 'rejected')
                        <span class="px-3.5 py-1.5 bg-red-100 dark:bg-red-900/60 text-red-800 dark:text-red-300 text-xs font-extrabold rounded-full uppercase tracking-wider flex items-center shadow-xs border border-red-200 dark:border-red-800">
                            ❌ Rejected
                        </span>
                    @elseif($extension->status === 'reverted')
                        <span class="px-3.5 py-1.5 bg-amber-100 dark:bg-amber-900/60 text-amber-800 dark:text-amber-300 text-xs font-extrabold rounded-full uppercase tracking-wider flex items-center shadow-xs border border-amber-200 dark:border-amber-800">
                            ⚠️ Reverted
                        </span>
                    @else
                        <span class="px-3.5 py-1.5 bg-blue-100 dark:bg-blue-900/60 text-blue-800 dark:text-blue-300 text-xs font-extrabold rounded-full uppercase tracking-wider flex items-center shadow-xs border border-blue-200 dark:border-blue-800">
                            ⏳ In Progress
                        </span>
                    @endif
                </div>
            </div>

            <!-- Section 1: Pre-filled Student Details -->
            @php
                $student = $extension->thesis?->student;
            @endphp
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
                        <x-readonly-input :value="$student?->user?->name ?? 'N/A'" />
                    </div>

                    <div>
                        <x-readonly-label value="Roll Number" />
                        <x-readonly-input :value="$student?->roll_number ?? 'N/A'" />
                    </div>

                    <div>
                        <x-readonly-label value="Department" />
                        <x-readonly-input :value="$student?->department?->name ?? 'N/A'" />
                    </div>
                </div>

                <!-- Collapsible Remaining Details -->
                <div x-show="showStudentInfo" x-transition class="grid grid-cols-1 md:grid-cols-3 gap-6 pt-4 border-t border-gray-100 dark:border-gray-700">

                    <div>
                        <x-readonly-label value="Date of Registration" />
                        <x-readonly-input :value="$student?->date_registration ? \Carbon\Carbon::parse($student->date_registration)->format('d-m-Y') : 'N/A'" />
                    </div>

                    <div>
                        <x-readonly-label value="Date of Joining" />
                        <x-readonly-input :value="$student?->date_joining ? \Carbon\Carbon::parse($student->date_joining)->format('d-m-Y') : 'N/A'" />
                    </div>

                    <div>
                        <x-readonly-label value="Date of Confirmation" />
                        <x-readonly-input :value="$student?->date_confirmation ? \Carbon\Carbon::parse($student->date_confirmation)->format('d-m-Y') : 'N/A'" />
                    </div>

                    <div>
                        <x-readonly-label value="Date of Submission" />
                        <x-readonly-input :value="$extension->created_at ? $extension->created_at->format('d-m-Y') : 'N/A'" />
                    </div>
                </div>
            </div>

            <!-- Section 2: Extension Details -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 space-y-4">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white border-b border-gray-100 dark:border-gray-700 pb-2">
                    2. Extension Application Details
                </h3>

                @php
                    $seminarDate = $extension->thesis?->getOpenSeminarDate();
                @endphp

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

            <!-- Section 3: Final Decision & Remarks -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 space-y-4">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white border-b border-gray-100 dark:border-gray-700 pb-2">
                    3. Evaluation Status & Comment
                </h3>

                @if($extension->status === 'reverted')
                    <div class="p-4 bg-amber-50 dark:bg-amber-950/40 border-l-4 border-amber-500 rounded-xl space-y-2">
                        <div class="flex items-center space-x-2 text-amber-900 dark:text-amber-200">
                            <svg class="w-5 h-5 text-amber-600 dark:text-amber-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                            </svg>
                            <span class="font-bold text-sm">Application Reverted by {{ $extension->getRevertedByRoleLabel() }}</span>
                        </div>
                        @if($extension->reversion_comment)
                            <div class="text-xs text-gray-700 dark:text-gray-300">
                                <strong>Reversion Comment:</strong>
                                <p class="italic bg-white dark:bg-gray-800 p-2.5 rounded-lg border border-amber-200 dark:border-amber-900 mt-1 whitespace-pre-wrap break-words [overflow-wrap:anywhere]">{{ trim($extension->reversion_comment) }}</p>
                            </div>
                        @endif

                        @if($user->isStudent())
                            <div class="pt-2">
                                <a href="{{ route('student.pts4_extension.create') }}" class="inline-flex items-center px-4 py-2 bg-amber-600 hover:bg-amber-700 text-white font-bold text-xs rounded-lg shadow transition">
                                    Edit & Resubmit Extension Application &rarr;
                                </a>
                            </div>
                        @endif
                    </div>
                @elseif($extension->status === 'approved')
                    <div class="p-4 bg-emerald-50 dark:bg-emerald-950/40 border-l-4 border-emerald-500 rounded-xl space-y-3">
                        <div class="p-3 bg-white dark:bg-gray-800 rounded-lg border border-emerald-200 dark:border-emerald-900">
                            <label class="block text-xs font-semibold text-emerald-800 dark:text-emerald-300 uppercase mb-1">Approved Extension Until Date</label>
                            <div class="text-base font-bold text-emerald-900 dark:text-emerald-200">
                                📅 {{ ($extension->approved_extended_until_date)?->format('d-M-Y') }}
                            </div>
                        </div>

                        <div>
                            <div class="flex items-center justify-between mb-1 gap-2 sm:gap-4">
                                <label class="block text-xs font-semibold text-emerald-800 dark:text-emerald-300 uppercase">DOAA Student Comment</label>
                                <span class="font-bold text-xs whitespace-nowrap shrink-0 text-emerald-600 dark:text-emerald-400">
                                    ✓ Approved
                                </span>
                            </div>
                            <x-feedback-box :text="$extension->doaa_student_comment" fallback="Comment not provided" role="doaa" />
                        </div>
                    </div>
                @elseif($extension->status === 'rejected')
                    <div class="p-4 bg-red-50 dark:bg-red-950/40 border-l-4 border-red-500 rounded-xl space-y-3">
                        <div>
                            <div class="flex items-center justify-between mb-1 gap-2 sm:gap-4">
                                <label class="block text-xs font-semibold text-red-800 dark:text-red-300 uppercase">DOAA Student Comment</label>
                                <span class="font-bold text-xs whitespace-nowrap shrink-0 text-red-600 dark:text-red-400">
                                    ❌ Rejected
                                </span>
                            </div>
                            <x-feedback-box :text="$extension->doaa_student_comment" fallback="Comment not provided" role="doaa" />
                        </div>
                    </div>
                @else
                    <div class="p-4 bg-blue-50 dark:bg-blue-950/40 border-l-4 border-blue-500 rounded-xl space-y-2">
                        <div class="flex items-center justify-between gap-2 sm:gap-4">
                            <span class="font-bold text-blue-900 dark:text-blue-200 text-sm">⏳ Application Under Review</span>
                            <span class="text-xs font-bold text-blue-800 dark:text-blue-300 bg-blue-100 dark:bg-blue-900 px-3 py-1 rounded-full">
                                Stage: {{ $extension->stage_label }}
                            </span>
                        </div>
                        <p class="text-xs text-blue-700 dark:text-blue-300">
                            Your PTS-4 Extension application is currently progressing through the academic evaluation chain.
                        </p>
                    </div>
                @endif
            </div>

            <!-- Section 4: Authority Recommendations & Remarks Trail -->
            @if($user->isFaculty() || $user->isDeptAuthority() || $user->isGlobalAuthority() || $user->isActingApprovalAuthority())
                @php
                    $viewerRole = $userRole ?? \App\Models\Thesis::determineExtensionUserRole($user, $extension);
                    $viewerRank = \App\Models\Pts4Extension::getRoleRank($viewerRole);
                    $hasAnyAuthorityRecommendation = 
                        ($viewerRank >= 1 && $extension->main_supervisor_recommendation !== null) ||
                        ($viewerRank >= 2 && $extension->dpgc_recommendation !== null) ||
                        ($viewerRank >= 3 && $extension->hod_recommendation !== null) ||
                        ($viewerRank >= 4 && $extension->academic_office_recommendation !== null) ||
                        ($viewerRank >= 5 && $extension->doaa_recommendation !== null);
                @endphp

                @if($viewerRank > 1 || $extension->reverted_by_role !== 'main_supervisor')
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 space-y-4">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white border-b border-gray-100 dark:border-gray-700 pb-2">
                        4. Authority Recommendations & Confidential Remarks
                    </h3>

                    @if($hasAnyAuthorityRecommendation)
                    <div class="space-y-4">
                        <!-- Main Supervisor Evaluation (Rank 1) -->
                        @if($viewerRank >= 1 && $extension->main_supervisor_recommendation !== null)
                            <x-role-card role="main_supervisor">
                                <div class="flex items-center justify-between gap-2 sm:gap-4">
                                    <span class="font-bold text-base text-indigo-900 dark:text-indigo-200">Main Supervisor</span>
                                    <span class="font-bold text-xs whitespace-nowrap shrink-0 {{ $extension->main_supervisor_recommendation ? 'text-emerald-600 dark:text-emerald-400' : 'text-red-600 dark:text-red-400' }}">
                                        {{ $extension->main_supervisor_recommendation ? '✓ Recommended' : '❌ Not Recommended' }}
                                    </span>
                                </div>
                                <div class="text-xs text-gray-700 dark:text-gray-300 space-y-1">
                                    <strong>Confidential Remark:</strong>
                                    <x-feedback-box :text="$extension->main_supervisor_confidential_remark" fallback="Remark not provided" role="main_supervisor" />
                                </div>
                            </x-role-card>
                        @endif

                        <!-- DPGC Evaluation (Rank 2) -->
                        @if($viewerRank >= 2 && $extension->dpgc_recommendation !== null)
                            <x-role-card role="dpgc" title="Department Postgraduate Committee (DPGC)">
                                <x-slot:badge>
                                    <span class="font-bold text-xs whitespace-nowrap shrink-0 {{ $extension->dpgc_recommendation ? 'text-emerald-600 dark:text-emerald-400' : 'text-red-600 dark:text-red-400' }}">
                                        {{ $extension->dpgc_recommendation ? '✓ Recommended' : '❌ Not Recommended' }}
                                    </span>
                                </x-slot:badge>
                                <div class="text-xs text-gray-700 dark:text-gray-300 space-y-1">
                                    <strong>Confidential Remark:</strong>
                                    <x-feedback-box :text="$extension->dpgc_confidential_remark" fallback="Remark not provided" role="dpgc" />
                                </div>
                            </x-role-card>
                        @endif

                        <!-- HOD Evaluation (Rank 3) -->
                        @if($viewerRank >= 3 && $extension->hod_recommendation !== null)
                            <x-role-card role="hod" title="Head of Department (HOD)">
                                <x-slot:badge>
                                    <span class="font-bold text-xs whitespace-nowrap shrink-0 {{ $extension->hod_recommendation ? 'text-emerald-600 dark:text-emerald-400' : 'text-red-600 dark:text-red-400' }}">
                                        {{ $extension->hod_recommendation ? '✓ Recommended' : '❌ Not Recommended' }}
                                    </span>
                                </x-slot:badge>
                                <div class="text-xs text-gray-700 dark:text-gray-300 space-y-1">
                                    <strong>Confidential Remark:</strong>
                                    <x-feedback-box :text="$extension->hod_confidential_remark" fallback="Remark not provided" role="hod" />
                                </div>
                            </x-role-card>
                        @endif

                        <!-- Academic Office Evaluation (Rank 4) -->
                        @if($viewerRank >= 4 && $extension->academic_office_recommendation !== null)
                            <x-role-card role="academic_office" title="Academic Office">
                                <x-slot:badge>
                                    <span class="font-bold text-xs text-emerald-600 dark:text-emerald-400 whitespace-nowrap shrink-0">
                                        ✓ Verified &amp; Forwarded
                                    </span>
                                </x-slot:badge>
                                <div class="text-xs text-gray-700 dark:text-gray-300 space-y-1">
                                    <strong>Verification Remark:</strong>
                                    <x-feedback-box :text="$extension->academic_office_confidential_remark" fallback="Remark not provided" role="academic_office" />
                                </div>
                                @if(Auth::user()?->isAcademicOffice())
                                    <div class="text-xs text-gray-700 dark:text-gray-300 space-y-1 pt-1">
                                        <strong>Assigned Acting DOAA:</strong>
                                        @if(!empty($extension->acting_doaa_email))
                                            @php
                                                $actingDoaaUser = \App\Models\User::where('email', $extension->acting_doaa_email)->first();
                                            @endphp
                                            <div class="p-2 bg-indigo-50/70 dark:bg-indigo-950/40 border border-indigo-200 dark:border-indigo-800 rounded-lg font-semibold text-indigo-900 dark:text-indigo-200">
                                                {{ $actingDoaaUser->name ?? $extension->acting_doaa_email }} ({{ $extension->acting_doaa_email }})
                                            </div>
                                        @else
                                            <div class="p-2 bg-gray-100 dark:bg-gray-700/60 border border-gray-200 dark:border-gray-600 rounded-lg text-gray-600 dark:text-gray-400 font-medium">
                                                None
                                            </div>
                                        @endif
                                    </div>
                                @endif
                            </x-role-card>
                        @endif

                        <!-- DOAA Decision (Rank 5) -->
                        @if($viewerRank >= 5 && $extension->doaa_recommendation !== null)
                            <x-role-card role="doaa" title="Dean of Academic Affairs (DOAA)">
                                <x-slot:badge>
                                    <span class="font-bold text-xs whitespace-nowrap shrink-0 {{ $extension->doaa_recommendation ? 'text-emerald-600 dark:text-emerald-400' : 'text-red-600 dark:text-red-400' }}">
                                        {{ $extension->doaa_recommendation ? '✓ Approved' : '❌ Rejected' }}
                                        @if($extension->approvedBy)
                                            by: {{ $extension->approvedBy->email }}
                                        @endif
                                    </span>
                                </x-slot:badge>
                                <div class="text-xs text-gray-700 dark:text-gray-300 space-y-1">
                                    <strong>Confidential Remark:</strong>
                                    <x-feedback-box :text="$extension->doaa_confidential_remark" fallback="Remark not provided" role="doaa" />
                                </div>
                            </x-role-card>
                        @endif
                    </div>
                    @else
                        <div class="flex items-center space-x-2 text-xs text-gray-600 dark:text-gray-500 bg-gray-200/60 dark:bg-gray-900/40 p-3 rounded-lg border border-dashed border-gray-200 dark:border-gray-700/60">
                            <svg class="w-4 h-4 text-gray-400 dark:text-gray-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 4.418 9 8z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3l18 18"></path></svg>
                            <span class="italic font-normal">No authority recommendations or comments to display yet.</span>
                        </div>
                    @endif
                </div>
                @endif
            @endif
        </div>
    </div>
</x-app-layout>

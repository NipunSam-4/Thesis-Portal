<x-app-layout>
    @php
        $formPrefix = isset($student) && $student->isPhd() ? 'PTS' : 'MSRTS';
    @endphp
    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-4">

            <!-- Top Back to Dashboard Button -->
            <div>
                <x-back-to-dashboard-button />
            </div>

            <!-- Page Header Card -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-5 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div class="space-y-1">
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white leading-tight">
                        @if($pts3->status === 'approved')
                            {{ __("Approved {$formPrefix}-3 Form (Panels of Examiners)") }}
                        @elseif($pts3->status === 'rejected')
                            {{ __("Rejected {$formPrefix}-3 Form") }}
                        @elseif($pts3->status === 'reverted')
                            {{ __("Reverted {$formPrefix}-3 Form") }}
                        @else
                            {{ __("{$formPrefix}-3 Form Details (In Progress)") }}
                        @endif
                    </h2>
                </div>
                <div class="flex items-center space-x-3 shrink-0">
                    @if($pts3->status === 'approved')
                        <span class="px-3.5 py-1.5 bg-emerald-100 dark:bg-emerald-900/60 text-emerald-800 dark:text-emerald-300 text-xs font-extrabold rounded-full uppercase tracking-wider flex items-center shadow-xs border border-emerald-200 dark:border-emerald-800">
                            ✓ Approved
                        </span>
                    @elseif($pts3->status === 'rejected')
                        <span class="px-3.5 py-1.5 bg-red-100 dark:bg-red-900/60 text-red-800 dark:text-red-300 text-xs font-extrabold rounded-full uppercase tracking-wider flex items-center shadow-xs border border-red-200 dark:border-red-800">
                            ❌ Rejected
                        </span>
                    @elseif($pts3->status === 'reverted')
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

            <!-- Flash Notifications -->
            @if(session('success'))
                <div class="p-4 bg-emerald-50 dark:bg-emerald-950/50 border border-emerald-200 dark:border-emerald-800 rounded-xl text-emerald-800 dark:text-emerald-300 text-sm flex items-center gap-3">
                    <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            @if(session('warning'))
                <div class="p-4 bg-amber-50 dark:bg-amber-950/50 border border-amber-200 dark:border-amber-800 rounded-xl text-amber-800 dark:text-amber-300 text-sm flex items-center gap-3">
                    <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    <span>{{ session('warning') }}</span>
                </div>
            @endif

            @if(session('error'))
                <div class="p-4 bg-rose-50 dark:bg-rose-950/50 border border-rose-200 dark:border-rose-800 rounded-xl text-rose-800 dark:text-rose-300 text-sm flex items-center gap-3">
                    <svg class="w-5 h-5 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            <!-- Form Status Overview Card -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-sm border border-gray-100 dark:border-gray-700">
                <div class="flex flex-wrap items-center justify-between gap-4">
                    <div>
                        <span class="text-xs font-semibold uppercase tracking-wider text-gray-400">Active Workflow Stage</span>
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white mt-1">
                            {{ $pts3->stage_label }}
                        </h3>
                    </div>

                    <div class="flex items-center gap-3">
                        <x-submission-timeline-modal :steps="$pts3->getSubmittedTimeline()" modalId="pts3TimelineModal" />
                    </div>
                </div>
            </div>

            <!-- Student & Thesis Info -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-sm border border-gray-100 dark:border-gray-700 space-y-4">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white pb-2 border-b border-gray-100 dark:border-gray-700">
                    Student & Thesis Details
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 text-sm">
                    <div>
                        <span class="text-gray-500 dark:text-gray-400 block">Student Name</span>
                        <span class="font-medium text-gray-900 dark:text-white">{{ $studentUser->name ?? 'N/A' }}</span>
                    </div>
                    <div>
                        <span class="text-gray-500 dark:text-gray-400 block">Roll Number</span>
                        <span class="font-medium text-gray-900 dark:text-white">{{ $student->roll_number ?? 'N/A' }}</span>
                    </div>
                    <div>
                        <span class="text-gray-500 dark:text-gray-400 block">Department</span>
                        <span class="font-medium text-gray-900 dark:text-white">{{ $student->department->name ?? 'N/A' }}</span>
                    </div>
                    <div>
                        <span class="text-gray-500 dark:text-gray-400 block">Program</span>
                        <span class="font-medium text-gray-900 dark:text-white">{{ strtoupper($student->program ?? 'PhD') }}</span>
                    </div>
                    <div class="md:col-span-2">
                        <span class="text-gray-500 dark:text-gray-400 block">Thesis Title</span>
                        <span class="font-medium text-gray-900 dark:text-white">{{ $pts3->thesis_title ?? $thesis->title ?? 'N/A' }}</span>
                    </div>
                </div>
            </div>

            <!-- Indian Examiners Panel -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-sm border border-gray-100 dark:border-gray-700 space-y-4">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white pb-2 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between">
                    <span>Proposed Indian Examiners</span>
                    <span class="text-xs px-2.5 py-1 bg-indigo-50 dark:bg-indigo-950 text-indigo-700 dark:text-indigo-300 rounded-full font-medium">
                        Count: {{ $indianExaminers->count() }}
                    </span>
                </h3>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-gray-600 dark:text-gray-300">
                        <thead class="bg-gray-50 dark:bg-gray-700/50 text-xs uppercase text-gray-500 dark:text-gray-400">
                            <tr>
                                <th class="p-3">#</th>
                                <th class="p-3">Examiner Name</th>
                                <th class="p-3">Designation & Org</th>
                                <th class="p-3">Contact Info</th>
                                <th class="p-3">Consent</th>
                                @if($userRank >= 6) <th class="p-3 text-center">DOAA Priority</th> @endif
                                @if($userRank >= 7) <th class="p-3 text-center">Senate Priority</th> @endif
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                            @foreach($indianExaminers as $index => $ex)
                                <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-750">
                                    <td class="p-3 font-medium text-gray-900 dark:text-white">{{ $index + 1 }}</td>
                                    <td class="p-3">
                                        <div class="font-semibold text-gray-900 dark:text-white">{{ $ex->name }}</div>
                                        <div class="text-xs text-gray-400">{{ $ex->research_area }}</div>
                                    </td>
                                    <td class="p-3">
                                        <div>{{ $ex->designation }}</div>
                                        <div class="text-xs text-gray-400">{{ $ex->organization }}</div>
                                    </td>
                                    <td class="p-3">
                                        <div>{{ $ex->email }}</div>
                                        <div class="text-xs text-gray-400">{{ $ex->phone_country_code }} {{ $ex->phone_number }}</div>
                                    </td>
                                    <td class="p-3">
                                        @if($ex->has_consent)
                                            <span class="px-2 py-0.5 bg-emerald-100 text-emerald-800 rounded text-xs">Yes (Consent Obtained)</span>
                                        @else
                                            <span class="px-2 py-0.5 bg-gray-100 text-gray-600 rounded text-xs">No</span>
                                        @endif
                                    </td>
                                    @if($userRank >= 6)
                                        <td class="p-3 text-center font-bold text-indigo-600 dark:text-indigo-400">
                                            {{ $ex->doaa_priority ?? '-' }}
                                        </td>
                                    @endif
                                    @if($userRank >= 7)
                                        <td class="p-3 text-center font-bold text-purple-600 dark:text-purple-400">
                                            {{ $ex->senate_chairperson_priority ?? '-' }}
                                        </td>
                                    @endif
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- International Examiners Panel -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-sm border border-gray-100 dark:border-gray-700 space-y-4">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white pb-2 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between">
                    <span>Proposed International Examiners</span>
                    <span class="text-xs px-2.5 py-1 bg-purple-50 dark:bg-purple-950 text-purple-700 dark:text-purple-300 rounded-full font-medium">
                        Count: {{ $internationalExaminers->count() }}
                    </span>
                </h3>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-gray-600 dark:text-gray-300">
                        <thead class="bg-gray-50 dark:bg-gray-700/50 text-xs uppercase text-gray-500 dark:text-gray-400">
                            <tr>
                                <th class="p-3">#</th>
                                <th class="p-3">Examiner Name</th>
                                <th class="p-3">Designation & Org</th>
                                <th class="p-3">Contact Info</th>
                                <th class="p-3">Consent</th>
                                @if($userRank >= 6) <th class="p-3 text-center">DOAA Priority</th> @endif
                                @if($userRank >= 7) <th class="p-3 text-center">Senate Priority</th> @endif
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                            @foreach($internationalExaminers as $index => $ex)
                                <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-750">
                                    <td class="p-3 font-medium text-gray-900 dark:text-white">{{ $index + 1 }}</td>
                                    <td class="p-3">
                                        <div class="font-semibold text-gray-900 dark:text-white">{{ $ex->name }}</div>
                                        <div class="text-xs text-gray-400">{{ $ex->research_area }}</div>
                                    </td>
                                    <td class="p-3">
                                        <div>{{ $ex->designation }}</div>
                                        <div class="text-xs text-gray-400">{{ $ex->organization }}</div>
                                    </td>
                                    <td class="p-3">
                                        <div>{{ $ex->email }}</div>
                                        <div class="text-xs text-gray-400">{{ $ex->phone_country_code }} {{ $ex->phone_number }}</div>
                                    </td>
                                    <td class="p-3">
                                        @if($ex->has_consent)
                                            <span class="px-2 py-0.5 bg-emerald-100 text-emerald-800 rounded text-xs">Yes (Consent Obtained)</span>
                                        @else
                                            <span class="px-2 py-0.5 bg-gray-100 text-gray-600 rounded text-xs">No</span>
                                        @endif
                                    </td>
                                    @if($userRank >= 6)
                                        <td class="p-3 text-center font-bold text-indigo-600 dark:text-indigo-400">
                                            {{ $ex->doaa_priority ?? '-' }}
                                        </td>
                                    @endif
                                    @if($userRank >= 7)
                                        <td class="p-3 text-center font-bold text-purple-600 dark:text-purple-400">
                                            {{ $ex->senate_chairperson_priority ?? '-' }}
                                        </td>
                                    @endif
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Proposed Oral Examination Board (OEB) Members -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-sm border border-gray-100 dark:border-gray-700 space-y-4">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white pb-2 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between">
                    <span>Proposed Oral Examination Board (OEB) Members</span>
                    <span class="text-xs px-2.5 py-1 bg-amber-50 dark:bg-amber-950 text-amber-700 dark:text-amber-300 rounded-full font-medium">
                        Count: {{ $oebMembers->count() }}
                    </span>
                </h3>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-gray-600 dark:text-gray-300">
                        <thead class="bg-gray-50 dark:bg-gray-700/50 text-xs uppercase text-gray-500 dark:text-gray-400">
                            <tr>
                                <th class="p-3">#</th>
                                <th class="p-3">Member Name</th>
                                <th class="p-3">Designation</th>
                                <th class="p-3">Department</th>
                                <th class="p-3">Email</th>
                                @if($userRank >= 6) <th class="p-3 text-center">DOAA Priority</th> @endif
                                @if($userRank >= 7) <th class="p-3 text-center">Senate Priority</th> @endif
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                            @foreach($oebMembers as $index => $oeb)
                                <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-750">
                                    <td class="p-3 font-medium text-gray-900 dark:text-white">{{ $index + 1 }}</td>
                                    <td class="p-3 font-semibold text-gray-900 dark:text-white">{{ $oeb->name }}</td>
                                    <td class="p-3">{{ $oeb->designation }}</td>
                                    <td class="p-3">{{ $oeb->department }}</td>
                                    <td class="p-3">{{ $oeb->email }}</td>
                                    @if($userRank >= 6)
                                        <td class="p-3 text-center font-bold text-indigo-600 dark:text-indigo-400">
                                            {{ $oeb->doaa_priority ?? '-' }}
                                        </td>
                                    @endif
                                    @if($userRank >= 7)
                                        <td class="p-3 text-center font-bold text-purple-600 dark:text-purple-400">
                                            {{ $oeb->senate_chairperson_priority ?? '-' }}
                                        </td>
                                    @endif
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Stage Evaluation Trail (Restricted by User Rank) -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-sm border border-gray-100 dark:border-gray-700 space-y-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white pb-2 border-b border-gray-100 dark:border-gray-700">
                    Evaluation & Recommendations Trail
                </h3>

                <div class="space-y-4">
                    <!-- 1. Main Supervisor -->
                    @if($userRank >= 1)
                        <div class="p-4 bg-gray-50 dark:bg-gray-750 rounded-xl border border-gray-100 dark:border-gray-700">
                            <div class="flex items-center justify-between">
                                <span class="font-semibold text-gray-900 dark:text-white">Main Supervisor Recommendation</span>
                                @if($pts3->main_supervisor_recommendation)
                                    <span class="text-xs px-2.5 py-1 bg-emerald-100 text-emerald-800 rounded-full font-medium">✓ Recommended</span>
                                @else
                                    <span class="text-xs px-2.5 py-1 bg-gray-100 text-gray-600 rounded-full font-medium">Pending</span>
                                @endif
                            </div>
                            @if($pts3->main_supervisor_submitted_at)
                                <p class="text-xs text-gray-400 mt-1">Submitted at: {{ $pts3->main_supervisor_submitted_at->format('d M Y, h:i A') }}</p>
                            @endif
                        </div>
                    @endif

                    <!-- 2. Co-Supervisors -->
                    @if($userRank >= 2)
                        <div class="p-4 bg-gray-50 dark:bg-gray-750 rounded-xl border border-gray-100 dark:border-gray-700">
                            <div class="flex items-center justify-between">
                                <span class="font-semibold text-gray-900 dark:text-white">Co-Supervisors Recommendations</span>
                                @if($pts3->co_supervisors_submitted_at)
                                    <span class="text-xs px-2.5 py-1 bg-emerald-100 text-emerald-800 rounded-full font-medium">✓ Completed</span>
                                @else
                                    <span class="text-xs px-2.5 py-1 bg-amber-100 text-amber-800 rounded-full font-medium">In Progress</span>
                                @endif
                            </div>
                            <div class="mt-3 space-y-2 text-xs">
                                @for ($i = 1; $i <= 10; $i++)
                                    @php $coUser = $pts3->getCoSupervisor($i); @endphp
                                    @if($coUser)
                                        <div class="flex items-center justify-between p-2 bg-white dark:bg-gray-800 rounded border border-gray-100 dark:border-gray-700">
                                            <span>{{ $coUser->name }}</span>
                                            @if($pts3->{"co_supervisor_{$i}_recommendation"})
                                                <span class="text-emerald-600 font-semibold">✓ Recommended</span>
                                            @else
                                                <span class="text-amber-600 font-semibold">Pending</span>
                                            @endif
                                        </div>
                                    @endif
                                @endfor
                            </div>
                        </div>
                    @endif

                    <!-- 3. DPGC -->
                    @if($userRank >= 3)
                        <div class="p-4 bg-gray-50 dark:bg-gray-750 rounded-xl border border-gray-100 dark:border-gray-700">
                            <div class="flex items-center justify-between">
                                <span class="font-semibold text-gray-900 dark:text-white">DPGC Recommendation</span>
                                @if($pts3->dpgc_recommendation)
                                    <span class="text-xs px-2.5 py-1 bg-emerald-100 text-emerald-800 rounded-full font-medium">✓ Recommended</span>
                                @else
                                    <span class="text-xs px-2.5 py-1 bg-gray-100 text-gray-600 rounded-full font-medium">Pending</span>
                                @endif
                            </div>
                            @if($pts3->dpgc_submitted_at)
                                <p class="text-xs text-gray-400 mt-1">Submitted at: {{ $pts3->dpgc_submitted_at->format('d M Y, h:i A') }}</p>
                            @endif
                        </div>
                    @endif

                    <!-- 4. HOD -->
                    @if($userRank >= 4)
                        <div class="p-4 bg-gray-50 dark:bg-gray-750 rounded-xl border border-gray-100 dark:border-gray-700">
                            <div class="flex items-center justify-between">
                                <span class="font-semibold text-gray-900 dark:text-white">HOD Recommendation</span>
                                @if($pts3->hod_recommendation)
                                    <span class="text-xs px-2.5 py-1 bg-emerald-100 text-emerald-800 rounded-full font-medium">✓ Recommended</span>
                                @else
                                    <span class="text-xs px-2.5 py-1 bg-gray-100 text-gray-600 rounded-full font-medium">Pending</span>
                                @endif
                            </div>
                            @if($pts3->hod_submitted_at)
                                <p class="text-xs text-gray-400 mt-1">Submitted at: {{ $pts3->hod_submitted_at->format('d M Y, h:i A') }}</p>
                            @endif
                        </div>
                    @endif

                    <!-- 5. Academic Office -->
                    @if($userRank >= 5)
                        <div class="p-4 bg-gray-50 dark:bg-gray-750 rounded-xl border border-gray-100 dark:border-gray-700">
                            <div class="flex items-center justify-between">
                                <span class="font-semibold text-gray-900 dark:text-white">Academic Office Verification</span>
                                @if($pts3->academic_office_is_verified)
                                    <span class="text-xs px-2.5 py-1 bg-emerald-100 text-emerald-800 rounded-full font-medium">✓ Verified</span>
                                @else
                                    <span class="text-xs px-2.5 py-1 bg-gray-100 text-gray-600 rounded-full font-medium">Pending</span>
                                @endif
                            </div>
                            @if($pts3->academic_office_submitted_at)
                                <p class="text-xs text-gray-400 mt-1">Submitted at: {{ $pts3->academic_office_submitted_at->format('d M Y, h:i A') }}</p>
                            @endif
                        </div>
                    @endif

                    <!-- 6. DOAA Evaluation -->
                    @if($userRank >= 6)
                        <div class="p-4 bg-gray-50 dark:bg-gray-750 rounded-xl border border-gray-100 dark:border-gray-700">
                            <div class="flex items-center justify-between">
                                <span class="font-semibold text-gray-900 dark:text-white">DOAA Evaluation & Priority Ranking</span>
                                @if($pts3->doaa_is_verified)
                                    <span class="text-xs px-2.5 py-1 bg-emerald-100 text-emerald-800 rounded-full font-medium">✓ Evaluated</span>
                                @else
                                    <span class="text-xs px-2.5 py-1 bg-gray-100 text-gray-600 rounded-full font-medium">Pending</span>
                                @endif
                            </div>
                            @if($pts3->doaa_submitted_at)
                                <p class="text-xs text-gray-400 mt-1">Submitted at: {{ $pts3->doaa_submitted_at->format('d M Y, h:i A') }}</p>
                            @endif
                        </div>
                    @endif

                    <!-- 7. Senate Chairperson Approval -->
                    @if($userRank >= 7)
                        <div class="p-4 bg-gray-50 dark:bg-gray-750 rounded-xl border border-gray-100 dark:border-gray-700 space-y-2">
                            <div class="flex items-center justify-between">
                                <span class="font-semibold text-gray-900 dark:text-white">Senate Chairperson Approval</span>
                                @if($pts3->senate_chairperson_approval === true)
                                    <span class="text-xs px-2.5 py-1 bg-emerald-100 text-emerald-800 rounded-full font-medium">✓ Approved</span>
                                @elseif($pts3->senate_chairperson_approval === false)
                                    <span class="text-xs px-2.5 py-1 bg-rose-100 text-rose-800 rounded-full font-medium">Rejected</span>
                                @else
                                    <span class="text-xs px-2.5 py-1 bg-gray-100 text-gray-600 rounded-full font-medium">Pending</span>
                                @endif
                            </div>
                            @if($pts3->senate_chairperson_approval_remark)
                                <div class="text-sm text-gray-700 dark:text-gray-300">
                                    <span class="font-semibold">Remark:</span> {{ $pts3->senate_chairperson_approval_remark }}
                                </div>
                            @endif
                            @if($pts3->senate_chairperson_confidential_remark && (auth()->user()?->isDoaa() || auth()->user()?->role === 'senate_chairperson' || auth('admin')->check()))
                                <div class="text-sm text-amber-700 dark:text-amber-300 bg-amber-50 dark:bg-amber-950/50 p-2.5 rounded-lg border border-amber-200 dark:border-amber-800">
                                    <span class="font-semibold">Confidential Remark (DOAA & Senate Only):</span> {{ $pts3->senate_chairperson_confidential_remark }}
                                </div>
                            @endif
                            @if($pts3->senate_chairperson_submitted_at)
                                <p class="text-xs text-gray-400">Submitted at: {{ $pts3->senate_chairperson_submitted_at->format('d M Y, h:i A') }}</p>
                            @endif
                        </div>
                    @endif

                </div>
            </div>

            <!-- Active Evaluation / Endorsement Action Form -->
            @if($canEvaluate)
                <div class="bg-gradient-to-r from-indigo-50 to-purple-50 dark:from-indigo-950/50 dark:to-purple-950/50 rounded-2xl p-6 shadow-sm border border-indigo-200 dark:border-indigo-800 space-y-6">
                    <h3 class="text-xl font-bold text-indigo-950 dark:text-indigo-200 border-b border-indigo-200 dark:border-indigo-800 pb-3 flex items-center gap-2">
                        <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span>Action Required: Evaluate PTS-3 Form</span>
                    </h3>

                    <!-- Endorse Form -->
                    <form action="{{ route('pts3.endorse', $pts3) }}" method="POST" class="space-y-6">
                        @csrf

                        <!-- DOAA Priority Inputs -->
                        @if($pts3->current_stage === 'doaa')
                            <div class="space-y-4 bg-white dark:bg-gray-800 p-5 rounded-xl border border-indigo-100 dark:border-gray-700">
                                <h4 class="font-semibold text-gray-900 dark:text-white">Assign DOAA Priorities for Examiners</h4>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    @foreach($indianExaminers as $ex)
                                        <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg text-sm">
                                            <span class="font-medium text-gray-800 dark:text-gray-200">{{ $ex->name }} (Indian)</span>
                                            <input type="number" min="1" max="10" name="doaa_examiner_priority[{{ $ex->id }}]" value="{{ $ex->doaa_priority }}" class="w-20 text-sm rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800 text-gray-900 dark:text-white" placeholder="Priority">
                                        </div>
                                    @endforeach
                                    @foreach($internationalExaminers as $ex)
                                        <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg text-sm">
                                            <span class="font-medium text-gray-800 dark:text-gray-200">{{ $ex->name }} (International)</span>
                                            <input type="number" min="1" max="10" name="doaa_examiner_priority[{{ $ex->id }}]" value="{{ $ex->doaa_priority }}" class="w-20 text-sm rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800 text-gray-900 dark:text-white" placeholder="Priority">
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <!-- Senate Chairperson Inputs -->
                        @if($pts3->current_stage === 'senate_chairperson')
                            <div class="space-y-4 bg-white dark:bg-gray-800 p-5 rounded-xl border border-indigo-100 dark:border-gray-700">
                                <h4 class="font-semibold text-gray-900 dark:text-white">Assign Senate Priorities & Decision</h4>
                                
                                <div class="flex items-center gap-6 p-4 bg-gray-50 dark:bg-gray-700 rounded-xl">
                                    <label class="flex items-center gap-2 text-sm font-semibold text-emerald-700 dark:text-emerald-400 cursor-pointer">
                                        <input type="radio" name="decision" value="approve" checked class="text-emerald-600 focus:ring-emerald-500">
                                        <span>Approve PTS-3 Form</span>
                                    </label>
                                    <label class="flex items-center gap-2 text-sm font-semibold text-rose-700 dark:text-rose-400 cursor-pointer">
                                        <input type="radio" name="decision" value="reject" class="text-rose-600 focus:ring-rose-500">
                                        <span>Reject Form</span>
                                    </label>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    @foreach($indianExaminers as $ex)
                                        <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg text-sm">
                                            <span class="font-medium text-gray-800 dark:text-gray-200">{{ $ex->name }} (Indian)</span>
                                            <input type="number" min="1" max="10" name="senate_examiner_priority[{{ $ex->id }}]" value="{{ $ex->senate_chairperson_priority }}" class="w-20 text-sm rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800 text-gray-900 dark:text-white" placeholder="Priority">
                                        </div>
                                    @endforeach
                                    @foreach($internationalExaminers as $ex)
                                        <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg text-sm">
                                            <span class="font-medium text-gray-800 dark:text-gray-200">{{ $ex->name }} (International)</span>
                                            <input type="number" min="1" max="10" name="senate_examiner_priority[{{ $ex->id }}]" value="{{ $ex->senate_chairperson_priority }}" class="w-20 text-sm rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800 text-gray-900 dark:text-white" placeholder="Priority">
                                        </div>
                                    @endforeach
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Approval Remark (Visible to all authorities)</label>
                                    <textarea name="senate_chairperson_approval_remark" rows="2" class="w-full text-sm rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800 text-gray-900 dark:text-white" placeholder="Optional remark..."></textarea>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-amber-700 dark:text-amber-400 mb-1">Confidential Remark (Visible to DOAA & Senate Chairperson only)</label>
                                    <textarea name="senate_chairperson_confidential_remark" rows="2" class="w-full text-sm rounded-lg border-amber-300 dark:border-amber-800 dark:bg-gray-800 text-gray-900 dark:text-white" placeholder="Optional confidential remark..."></textarea>
                                </div>
                            </div>
                        @endif

                        <div class="flex items-center gap-4">
                            <button type="submit" class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-sm font-semibold transition shadow-md flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                <span>Submit Endorsement</span>
                            </button>
                        </div>
                    </form>

                    <!-- Revert Form -->
                    <div class="pt-4 border-t border-indigo-200 dark:border-indigo-800" x-data="{ showRevert: false }">
                        <button type="button" @click="showRevert = !showRevert" class="text-sm font-medium text-rose-600 hover:text-rose-700 dark:text-rose-400 flex items-center gap-1">
                            <span>Revert Form to Main Supervisor</span>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>

                        <div x-show="showRevert" x-cloak class="mt-4 p-4 bg-white dark:bg-gray-800 rounded-xl border border-rose-200 dark:border-rose-800 space-y-4">
                            <form action="{{ route('pts3.revert', $pts3) }}" method="POST" class="space-y-4">
                                @csrf
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Reason for Reversion</label>
                                    <textarea name="reversion_comment" rows="3" required class="w-full text-sm rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800 text-gray-900 dark:text-white" placeholder="Specify reasons for reverting back to the Main Supervisor..."></textarea>
                                </div>
                                <button type="submit" class="px-5 py-2 bg-rose-600 hover:bg-rose-700 text-white rounded-lg text-sm font-semibold transition shadow-sm">
                                    Confirm Revert to Main Supervisor
                                </button>
                            </form>
                        </div>
                    </div>

                </div>
            @endif

        </div>
    </div>
</x-app-layout>

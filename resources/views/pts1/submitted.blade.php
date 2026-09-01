<x-app-layout>
    @php
        $pts1 = $pts1 ?? $form ?? null;
        $form = $form ?? $pts1 ?? null;
        $thesis = $thesis ?? $form?->thesis;
        $student = $student ?? $thesis?->student;
        $studentUser = $studentUser ?? $student?->user;
        $currentUser = $user ?? auth()->user();
        $viewPerspective = $viewPerspective ?? ($currentUser?->isStudent() ? 'student' : ($currentUser?->isDpgc() ? 'dpgc' : ($currentUser?->isHod() ? 'hod' : ($currentUser?->isAcademicOffice() ? 'academic_office' : ($currentUser?->isDoaa() ? 'doaa' : 'authority')))));
        $formPrefix = isset($student) && $student->isPhd() ? 'PTS' : 'MSRTS';
    @endphp
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
                        {{ __("Submitted {$formPrefix}-1 Form") }}
                    </h2>
                </div>
                <div class="flex items-center space-x-3 shrink-0">
                    <span class="px-3.5 py-1.5 bg-blue-100 dark:bg-blue-900/60 text-blue-800 dark:text-blue-300 text-xs font-extrabold rounded-full uppercase tracking-wider flex items-center shadow-xs border border-blue-200 dark:border-blue-800">
                        ⏳ In Progress
                    </span>
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

            <!-- Common Form Information Sections (1 to 5) -->
            @include('pts1.form_info', ['pts1' => $pts1, 'viewPerspective' => $viewPerspective])

            <!-- Section 6: Role-Scoped Review & Comments -->
            @if($viewPerspective !== 'student')
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 space-y-6">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white border-b border-gray-100 dark:border-gray-700 pb-2">
                        6. Evaluation &amp; Comments
                    </h3>

                    <div class="space-y-4">
                        <!-- 1. Main Supervisor Evaluation (Visible to Main Supervisor, Co-Supervisors, PSPC, DPGC, HOD, DOAA) -->
                        @if(!is_null($pts1->main_supervisor_submitted_at) || !is_null($pts1->main_supervisor_recommendation))
                            <div class="p-4 bg-indigo-50/70 dark:bg-indigo-950/40 border-l-4 border-indigo-500 rounded-xl space-y-2">
                                <div class="flex items-center justify-between text-s gap-2 sm:gap-4">
                                    <span class="font-bold text-indigo-900 dark:text-indigo-200">
                                        Main Supervisor @if(isset($mainSupervisor))<span class="block sm:inline text-xs font-normal text-indigo-700 dark:text-indigo-300 mt-0.5 sm:mt-0">({{ $mainSupervisor->name }})</span>@endif
                                    </span>
                                    @if($pts1->work_status)
                                        <span class="px-2.5 py-1 bg-emerald-600 text-white font-bold rounded-lg uppercase text-[10px] text-center leading-tight whitespace-normal max-w-[120px] sm:max-w-none">
                                            Open Seminar Status: {{ strtoupper($pts1->work_status) }}
                                        </span>
                                    @elseif(!is_null($pts1->main_supervisor_recommendation))
                                        <span class="font-bold whitespace-nowrap shrink-0 {{ $pts1->main_supervisor_recommendation ? 'text-emerald-600 dark:text-emerald-400' : 'text-red-600 dark:text-red-400' }}">
                                            {{ $pts1->main_supervisor_recommendation ? '✓ Recommended' : '❌ Not Recommended' }}
                                        </span>
                                    @endif
                                </div>
                                <div class="text-xs text-gray-700 dark:text-gray-300">
                                    <strong>Additional Comment:</strong>
                                    @if($pts1->main_supervisor_student_comment)
                                        <p class="italic bg-white dark:bg-gray-800 p-2.5 rounded-lg border border-indigo-100 dark:border-indigo-900 mt-1 whitespace-pre-wrap break-words [overflow-wrap:anywhere]">{{ trim($pts1->main_supervisor_student_comment) }}</p>
                                    @else
                                        <div class="flex items-center space-x-2 text-xs text-gray-600 dark:text-gray-500 bg-gray-200/60 dark:bg-gray-900/40 p-2.5 rounded-lg border border-dashed border-gray-200 dark:border-gray-700/60 mt-1">
                                            <svg class="w-4 h-4 text-gray-400 dark:text-gray-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 4.418 9 8z"></path>
                                            </svg>
                                            <span class="italic font-normal">Comment not provided</span>
                                        </div>
                                    @endif
                                </div>

                                @if($viewPerspective === 'main_supervisor' || in_array($viewPerspective, ['dpgc', 'hod', 'academic_office', 'doaa']))
                                    <div class="text-xs text-gray-700 dark:text-gray-300 pt-1">
                                        <strong>Confidential Remark:</strong>
                                        @if($pts1->main_supervisor_confidential_remark)
                                            <p class="italic bg-white dark:bg-gray-800 p-2.5 rounded-lg border border-indigo-100 dark:border-indigo-900 mt-1 whitespace-pre-wrap break-words [overflow-wrap:anywhere]">{{ trim($pts1->main_supervisor_confidential_remark) }}</p>
                                        @else
                                            <div class="flex items-center space-x-2 text-xs text-gray-600 dark:text-gray-500 bg-gray-200/60 dark:bg-gray-900/40 p-2.5 rounded-lg border border-dashed border-gray-200 dark:border-gray-700/60 mt-1">
                                                <svg class="w-4 h-4 text-gray-400 dark:text-gray-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 4.418 9 8z"></path>
                                                </svg>
                                                <span class="italic font-normal">Remark not provided</span>
                                            </div>
                                        @endif
                                    </div>
                                @endif
                            </div>
                        @endif

                        <!-- 2. Co-Supervisors Endorsements -->
                        <!-- If viewPerspective == 'co_supervisor', show ONLY $myCoSupSlot -->
                        <!-- If viewPerspective in ['pspc_member', 'dpgc', 'hod', 'academic_office', 'doaa'], show ALL submitted co-supervisors -->
                        @if($viewPerspective === 'co_supervisor' && $myCoSupSlot)
                            @php
                                $recCol = "co_supervisor_{$myCoSupSlot}_recommendation";
                                $commCol = "co_supervisor_{$myCoSupSlot}_student_comment";
                                $remCol = "co_supervisor_{$myCoSupSlot}_confidential_remark";
                                $coUser = auth()->user();
                                $isExt = $coUser ? $coUser->isExternalSupervisor() : false;
                                $roleTitle = ($student && $coUser) ? $student->getSupervisorRoleTitle($coUser) : ($isExt ? 'External Supervisor' : "Co-Supervisor {$myCoSupSlot}");
                                $inst = ($isExt && $coUser?->externalSupervisorProfile?->affiliated_institute) ? ' - ' . $coUser->externalSupervisorProfile->affiliated_institute : '';
                            @endphp
                            @if(!is_null($pts1->$recCol))
                                <div class="p-4 bg-blue-50/70 dark:bg-blue-950/40 border-l-4 border-blue-500 rounded-xl space-y-3">
                                    <div class="flex items-center justify-between font-semibold gap-2 sm:gap-4">
                                        <h5 class="text-s font-bold text-blue-900 dark:text-blue-200">
                                            {{ $roleTitle }} <span class="block sm:inline text-xs font-normal text-gray-500 dark:text-gray-400 mt-0.5 sm:mt-0">({{ $coUser?->name }}{{ $inst }})</span>
                                        </h5>
                                        <span class="font-bold text-xs whitespace-nowrap shrink-0 {{ $pts1->$recCol ? 'text-emerald-600' : 'text-red-600' }}">
                                            {{ $pts1->$recCol ? '✓ Recommended' : '❌ Not Recommended' }}
                                        </span>
                                    </div>
                                    <div class="text-xs text-gray-700 dark:text-gray-300">
                                        <strong>Student Comment:</strong>
                                        @if($pts1->$commCol)
                                            <p class="italic bg-white dark:bg-gray-800 p-2.5 rounded-lg border border-blue-100 dark:border-blue-900 mt-1 whitespace-pre-wrap break-words [overflow-wrap:anywhere]">{{ trim($pts1->$commCol) }}</p>
                                        @else
                                            <div class="flex items-center space-x-2 text-xs text-gray-600 dark:text-gray-500 bg-gray-200/60 dark:bg-gray-900/40 p-2.5 rounded-lg border border-dashed border-gray-200 dark:border-gray-700/60 mt-1">
                                                <svg class="w-4 h-4 text-gray-400 dark:text-gray-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 4.418 9 8z"></path></svg>
                                                <span class="italic font-normal">Comment not provided</span>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="text-xs text-gray-700 dark:text-gray-300">
                                        <strong>Confidential Remark:</strong>
                                        @if($pts1->$remCol)
                                            <p class="italic bg-white dark:bg-gray-800 p-2.5 rounded-lg border border-blue-100 dark:border-blue-900 mt-1 whitespace-pre-wrap break-words [overflow-wrap:anywhere]">{{ trim($pts1->$remCol) }}</p>
                                        @else
                                            <div class="flex items-center space-x-2 text-xs text-gray-600 dark:text-gray-500 bg-gray-200/60 dark:bg-gray-900/40 p-2.5 rounded-lg border border-dashed border-gray-200 dark:border-gray-700/60 mt-1">
                                                <svg class="w-4 h-4 text-gray-400 dark:text-gray-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 4.418 9 8z"></path></svg>
                                                <span class="italic font-normal">Remark not provided</span>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endif
                        @elseif(in_array($viewPerspective, ['pspc_member', 'dpgc', 'hod', 'academic_office', 'doaa']))
                            @php
                                $hasAnyCoSubmitted = false;
                                for ($i = 1; $i <= 10; $i++) {
                                    $recCol = "co_supervisor_{$i}_recommendation";
                                    if (!is_null($pts1->$recCol)) { $hasAnyCoSubmitted = true; break; }
                                }
                            @endphp
                            @if($hasAnyCoSubmitted)
                                <div class="p-4 bg-blue-50/70 dark:bg-blue-950/40 border-l-4 border-blue-500 rounded-xl space-y-3">
                                    <h5 class="text-s font-bold text-blue-900 dark:text-blue-200">Co-Supervisors</h5>
                                    @for($i = 1; $i <= 10; $i++)
                                        @php
                                            $idCol = "co_supervisor_{$i}_id";
                                            $recCol = "co_supervisor_{$i}_recommendation";
                                            $commCol = "co_supervisor_{$i}_student_comment";
                                            $remCol = "co_supervisor_{$i}_confidential_remark";
                                            $coUser = $pts1->$idCol ? \App\Models\User::find($pts1->$idCol) : null;
                                        @endphp
                                        @if($coUser && !is_null($pts1->$recCol))
                                            @php
                                                $isExt = $coUser->isExternalSupervisor();
                                                $roleTitle = $student ? $student->getSupervisorRoleTitle($coUser) : ($isExt ? 'External Supervisor' : "Co-Supervisor {$i}");
                                                $inst = ($isExt && $coUser->externalSupervisorProfile?->affiliated_institute) ? ' - ' . $coUser->externalSupervisorProfile->affiliated_institute : '';
                                            @endphp
                                            <div class="text-xs space-y-1 pt-1 {{ $i > 1 ? 'border-t border-blue-100 dark:border-blue-900' : '' }}">
                                                <div class="flex items-center justify-between font-semibold gap-2 sm:gap-4">
                                                    <span>{{ $roleTitle }} <span class="block sm:inline text-xs font-normal text-gray-500 dark:text-gray-400 mt-0.5 sm:mt-0">({{ $coUser->name }}{{ $inst }})</span>:</span>
                                                    <span class="font-bold whitespace-nowrap shrink-0 {{ $pts1->$recCol ? 'text-emerald-600' : 'text-red-600' }}">
                                                        {{ $pts1->$recCol ? '✓ Recommended' : '❌ Not Recommended' }}
                                                    </span>
                                                </div>
                                                @if($pts1->$remCol)
                                                    <p class="italic text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 p-2.5 rounded-lg border border-blue-100 dark:border-blue-900 mt-1 whitespace-pre-wrap break-words [overflow-wrap:anywhere]">{{ trim($pts1->$remCol) }}</p>
                                                @else
                                                    <div class="flex items-center space-x-2 text-xs text-gray-600 dark:text-gray-500 bg-gray-200/60 dark:bg-gray-900/40 p-2.5 rounded-lg border border-dashed border-gray-200 dark:border-gray-700/60 mt-1">
                                                        <svg class="w-4 h-4 text-gray-400 dark:text-gray-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 4.418 9 8z"></path></svg>
                                                        <span class="italic font-normal">Remark not provided</span>
                                                    </div>
                                                @endif
                                            </div>
                                        @endif
                                    @endfor
                                </div>
                            @endif
                        @endif

                        <!-- 3. PSPC Committee Endorsements -->
                        @if($viewPerspective === 'pspc_member' && $myPspcSlot)
                            @php
                                $recCol = "pspc_member_{$myPspcSlot}_recommendation";
                                $remCol = "pspc_member_{$myPspcSlot}_confidential_remark";
                                $pspcUser = auth()->user();
                            @endphp
                            @if(!is_null($pts1->$recCol))
                                <div class="p-4 bg-purple-50/70 dark:bg-purple-950/40 border-l-4 border-purple-500 rounded-xl space-y-3">
                                    <div class="flex items-center justify-between font-semibold gap-2 sm:gap-4">
                                        <h5 class="text-s font-bold text-purple-900 dark:text-purple-200">
                                            PSPC Member {{ $myPspcSlot }} <span class="block sm:inline text-xs font-normal text-gray-500 dark:text-gray-400 mt-0.5 sm:mt-0">({{ $pspcUser?->name }})</span>
                                        </h5>
                                        <span class="font-bold text-xs whitespace-nowrap shrink-0 {{ $pts1->$recCol ? 'text-emerald-600' : 'text-red-600' }}">
                                            {{ $pts1->$recCol ? '✓ Recommended' : '❌ Not Recommended' }}
                                        </span>
                                    </div>
                                    <div class="text-xs text-gray-700 dark:text-gray-300">
                                        <strong>Confidential Remark:</strong>
                                        @if($pts1->$remCol)
                                            <p class="italic bg-white dark:bg-gray-800 p-2.5 rounded-lg border border-purple-100 dark:border-purple-900 mt-1 whitespace-pre-wrap break-words [overflow-wrap:anywhere]">{{ trim($pts1->$remCol) }}</p>
                                        @else
                                            <div class="flex items-center space-x-2 text-xs text-gray-600 dark:text-gray-500 bg-gray-200/60 dark:bg-gray-900/40 p-2.5 rounded-lg border border-dashed border-gray-200 dark:border-purple-700/60 mt-1">
                                                <svg class="w-4 h-4 text-gray-400 dark:text-gray-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 4.418 9 8z"></path></svg>
                                                <span class="italic font-normal">Remark not provided</span>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endif
                        @elseif(in_array($viewPerspective, ['dpgc', 'hod', 'academic_office', 'doaa']))
                            @php
                                $hasAnyPspcSubmitted = false;
                                for ($i = 1; $i <= 10; $i++) {
                                    $recCol = "pspc_member_{$i}_recommendation";
                                    if (!is_null($pts1->$recCol)) { $hasAnyPspcSubmitted = true; break; }
                                }
                            @endphp
                            @if($hasAnyPspcSubmitted)
                                <div class="p-4 bg-purple-50/70 dark:bg-purple-950/40 border-l-4 border-purple-500 rounded-xl space-y-3">
                                    <h5 class="text-s font-bold text-purple-900 dark:text-purple-200">PSPC Committee</h5>
                                    @for($i = 1; $i <= 10; $i++)
                                        @php
                                            $idCol = "pspc_member_{$i}_id";
                                            $recCol = "pspc_member_{$i}_recommendation";
                                            $remCol = "pspc_member_{$i}_confidential_remark";
                                            $pspcUser = $pts1->$idCol ? \App\Models\User::find($pts1->$idCol) : null;
                                        @endphp
                                        @if($pspcUser && !is_null($pts1->$recCol))
                                            <div class="text-xs space-y-1 pt-1 {{ $i > 1 ? 'border-t border-purple-100 dark:border-purple-900' : '' }}">
                                                <div class="flex items-center justify-between font-semibold gap-2 sm:gap-4">
                                                    <span>PSPC Member {{ $i }} <span class="block sm:inline text-xs font-normal text-gray-500 dark:text-gray-400 mt-0.5 sm:mt-0">({{ $pspcUser->name }})</span>:</span>
                                                    <span class="font-bold whitespace-nowrap shrink-0 {{ $pts1->$recCol ? 'text-emerald-600' : 'text-red-600' }}">
                                                        {{ $pts1->$recCol ? '✓ Recommended' : '❌ Not Recommended' }}
                                                    </span>
                                                </div>
                                                @if($pts1->$remCol)
                                                    <p class="italic text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 p-2.5 rounded-lg border border-purple-100 dark:border-purple-900 mt-1 whitespace-pre-wrap break-words [overflow-wrap:anywhere]">{{ trim($pts1->$remCol) }}</p>
                                                @else
                                                    <div class="flex items-center space-x-2 text-xs text-gray-600 dark:text-gray-500 bg-gray-200/60 dark:bg-gray-900/40 p-2.5 rounded-lg border border-dashed border-gray-200 dark:border-gray-700/60 mt-1">
                                                        <svg class="w-4 h-4 text-gray-400 dark:text-gray-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 4.418 9 8z"></path></svg>
                                                        <span class="italic font-normal">Remark not provided</span>
                                                    </div>
                                                @endif
                                            </div>
                                        @endif
                                    @endfor
                                </div>
                            @endif
                        @endif

                        <!-- 4. DPGC Endorsement -->
                        @if(in_array($viewPerspective, ['dpgc', 'hod', 'academic_office', 'doaa']) && !is_null($pts1->dpgc_recommendation))
                            <div class="p-4 bg-teal-50/70 dark:bg-teal-950/40 border-l-4 border-teal-500 rounded-xl space-y-2">
                                <div class="flex items-center justify-between font-bold text-teal-900 dark:text-teal-200 gap-2 sm:gap-4">
                                    <h5 class="text-s">Department Postgraduate Committee (DPGC)</h5>
                                    <span class="font-bold text-xs whitespace-nowrap shrink-0 {{ $pts1->dpgc_recommendation ? 'text-emerald-600' : 'text-red-600' }}">
                                        {{ $pts1->dpgc_recommendation ? '✓ Recommended' : '❌ Not Recommended' }}
                                    </span>
                                </div>
                                <div class="text-xs text-gray-700 dark:text-gray-300">
                                    <strong>Student Comment:</strong>
                                    @if($pts1->dpgc_student_comment)
                                        <p class="italic bg-white dark:bg-gray-800 p-2.5 rounded-lg border border-teal-100 dark:border-teal-900 mt-0.5 whitespace-pre-wrap break-words [overflow-wrap:anywhere]">{{ trim($pts1->dpgc_student_comment) }}</p>
                                    @else
                                        <div class="flex items-center space-x-2 text-xs text-gray-600 dark:text-gray-500 bg-gray-200/60 dark:bg-gray-900/40 p-2.5 rounded-lg border border-dashed border-gray-200 dark:border-gray-700/60 mt-0.5">
                                            <svg class="w-4 h-4 text-gray-400 dark:text-gray-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 4.418 9 8z"></path></svg>
                                            <span class="italic font-normal">Comment not provided</span>
                                        </div>
                                    @endif
                                </div>
                                <div class="text-xs text-gray-700 dark:text-gray-300 pt-0.5">
                                    <strong>DPGC Remark:</strong>
                                    @if($pts1->dpgc_confidential_remark)
                                        <p class="italic bg-white dark:bg-gray-800 p-2.5 rounded-lg border border-teal-100 dark:border-teal-900 mt-0.5 whitespace-pre-wrap break-words [overflow-wrap:anywhere]">{{ trim($pts1->dpgc_confidential_remark) }}</p>
                                    @else
                                        <div class="flex items-center space-x-2 text-xs text-gray-600 dark:text-gray-500 bg-gray-200/60 dark:bg-gray-900/40 p-2.5 rounded-lg border border-dashed border-gray-200 dark:border-gray-700/60 mt-0.5">
                                            <svg class="w-4 h-4 text-gray-400 dark:text-gray-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 4.418 9 8z"></path></svg>
                                            <span class="italic font-normal">Remark not provided</span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endif

                        <!-- 5. HOD Endorsement -->
                        @if(in_array($viewPerspective, ['hod', 'academic_office', 'doaa']) && !is_null($pts1->hod_recommendation))
                            <div class="p-4 bg-amber-50/70 dark:bg-amber-950/40 border-l-4 border-amber-500 rounded-xl space-y-2">
                                <div class="flex items-center justify-between font-bold text-amber-900 dark:text-amber-200 gap-2 sm:gap-4">
                                    <h5 class="text-s">Head of Department (HOD)</h5>
                                    <span class="font-bold text-xs whitespace-nowrap shrink-0 {{ $pts1->hod_recommendation ? 'text-emerald-600' : 'text-red-600' }}">
                                        {{ $pts1->hod_recommendation ? '✓ Recommended' : '❌ Not Recommended' }}
                                    </span>
                                </div>
                                <div class="text-xs text-gray-700 dark:text-gray-300">
                                    <strong>Student Comment:</strong>
                                    @if($pts1->hod_student_comment)
                                        <p class="italic bg-white dark:bg-gray-800 p-2.5 rounded-lg border border-amber-100 dark:border-amber-900 mt-0.5 whitespace-pre-wrap break-words [overflow-wrap:anywhere]">{{ trim($pts1->hod_student_comment) }}</p>
                                    @else
                                        <div class="flex items-center space-x-2 text-xs text-gray-600 dark:text-gray-500 bg-gray-200/60 dark:bg-gray-900/40 p-2.5 rounded-lg border border-dashed border-gray-200 dark:border-gray-700/60 mt-0.5">
                                            <svg class="w-4 h-4 text-gray-400 dark:text-gray-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 4.418 9 8z"></path></svg>
                                            <span class="italic font-normal">Comment not provided</span>
                                        </div>
                                    @endif
                                </div>
                                <div class="text-xs text-gray-700 dark:text-gray-300 pt-0.5">
                                    <strong>HOD Remark:</strong>
                                    @if($pts1->hod_confidential_remark)
                                        <p class="italic bg-white dark:bg-gray-800 p-2.5 rounded-lg border border-amber-100 dark:border-amber-900 mt-0.5 whitespace-pre-wrap break-words [overflow-wrap:anywhere]">{{ trim($pts1->hod_confidential_remark) }}</p>
                                    @else
                                        <div class="flex items-center space-x-2 text-xs text-gray-600 dark:text-gray-500 bg-gray-200/60 dark:bg-gray-900/40 p-2.5 rounded-lg border border-dashed border-gray-200 dark:border-gray-700/60 mt-0.5">
                                            <svg class="w-4 h-4 text-gray-400 dark:text-gray-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 4.418 9 8z"></path></svg>
                                            <span class="italic font-normal">Remark not provided</span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endif

                        <!-- 6. Academic Office Verification -->
                        @if(in_array($viewPerspective, ['academic_office', 'doaa']) && (!is_null($pts1->academic_office_verified) || !is_null($pts1->academic_office_submitted_at)))
                            <div class="p-4 bg-rose-50/70 dark:bg-rose-950/40 border-l-4 border-rose-500 rounded-xl space-y-2">
                                <div class="flex items-center justify-between font-bold text-rose-900 dark:text-rose-200 gap-2 sm:gap-4">
                                    <h5 class="text-s">Academic Office</h5>
                                    <span class="font-bold text-xs whitespace-nowrap shrink-0 {{ $pts1->academic_office_verified ? 'text-emerald-600 dark:text-emerald-400' : 'text-red-600 dark:text-red-400' }}">
                                        {{ $pts1->academic_office_verified ? '✓ Verified & Forwarded' : '❌ Verification Rejected' }}
                                    </span>
                                </div>
                                <div class="text-xs text-gray-700 dark:text-gray-300 pt-0.5">
                                    <strong>Verification Remark:</strong>
                                    @if($pts1->academic_office_confidential_remark)
                                        <p class="italic bg-white dark:bg-gray-800 p-2.5 rounded-lg border border-rose-100 dark:border-rose-900 mt-0.5 whitespace-pre-wrap break-words [overflow-wrap:anywhere]">{{ trim($pts1->academic_office_confidential_remark) }}</p>
                                    @else
                                        <div class="flex items-center space-x-2 text-xs text-gray-600 dark:text-gray-500 bg-gray-200/60 dark:bg-gray-900/40 p-2.5 rounded-lg border border-dashed border-gray-200 dark:border-gray-700/60 mt-0.5">
                                            <svg class="w-4 h-4 text-gray-400 dark:text-gray-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 4.418 9 8z"></path></svg>
                                            <span class="italic font-normal">Remark not provided</span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endif

                        <!-- 7. DOAA Approval -->
                        @if($viewPerspective === 'doaa' && !is_null($pts1->doaa_approval))
                            <div class="p-4 bg-emerald-50/70 dark:bg-emerald-950/40 border-l-4 border-emerald-500 rounded-xl space-y-2">
                                <div class="flex items-center justify-between font-bold text-emerald-900 dark:text-emerald-200 gap-2 sm:gap-4">
                                    <h5 class="text-s">Dean of Academic Affairs (DOAA)</h5>
                                    <span class="font-bold text-xs whitespace-nowrap shrink-0 {{ $pts1->doaa_approval ? 'text-emerald-600' : 'text-red-600' }}">
                                        {{ $pts1->doaa_approval ? '✓ Approved' : '❌ Rejected' }}
                                    </span>
                                </div>
                                <div class="text-xs text-gray-700 dark:text-gray-300">
                                    <strong>Student Comment:</strong>
                                    @if($pts1->doaa_student_comment)
                                        <p class="italic bg-white dark:bg-gray-800 p-2.5 rounded-lg border border-emerald-100 dark:border-emerald-900 mt-0.5 whitespace-pre-wrap break-words [overflow-wrap:anywhere]">{{ trim($pts1->doaa_student_comment) }}</p>
                                    @else
                                        <div class="flex items-center space-x-2 text-xs text-gray-600 dark:text-gray-500 bg-gray-200/60 dark:bg-gray-900/40 p-2.5 rounded-lg border border-dashed border-gray-200 dark:border-gray-700/60 mt-0.5">
                                            <svg class="w-4 h-4 text-gray-400 dark:text-gray-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 4.418 9 8z"></path></svg>
                                            <span class="italic font-normal">Comment not provided</span>
                                        </div>
                                    @endif
                                </div>
                                <div class="text-xs text-gray-700 dark:text-gray-300 pt-0.5">
                                    <strong>DOAA Remark:</strong>
                                    @if($pts1->doaa_confidential_remark)
                                        <p class="italic bg-white dark:bg-gray-800 p-2.5 rounded-lg border border-emerald-100 dark:border-emerald-900 mt-0.5 whitespace-pre-wrap break-words [overflow-wrap:anywhere]">{{ trim($pts1->doaa_confidential_remark) }}</p>
                                    @else
                                        <div class="flex items-center space-x-2 text-xs text-gray-600 dark:text-gray-500 bg-gray-200/60 dark:bg-gray-900/40 p-2.5 rounded-lg border border-dashed border-gray-200 dark:border-gray-700/60 mt-0.5">
                                            <svg class="w-4 h-4 text-gray-400 dark:text-gray-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 4.418 9 8z"></path></svg>
                                            <span class="italic font-normal">Remark not provided</span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endif

                    </div>
                </div>
            @endif

        </div>
    </div>

    @php
        $existingPubListUrl = ($viewPerspective === 'student')
            ? ($pts1->publication_list_doc_path ? route('pts.document.serve', ['pts1', $pts1->id, 'publication_list_doc_path']) : null)
            : ($pts1->getEffectivePublicationListPath() ? route('pts.document.serve', ['pts1', $pts1->id, $pts1->getEffectivePublicationListField()]) : null);
    @endphp

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const pubListUrl = @json($existingPubListUrl);
            if (pubListUrl) {
                window.previewExcelUrl(pubListUrl, {
                    titlePrefix: 'Publication and Other Recognition Preview'
                });
            }
        });
    </script>
</x-app-layout>

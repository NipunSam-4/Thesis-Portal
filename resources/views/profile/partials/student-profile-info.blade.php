<section class="space-y-6">
    <div class="border-b border-gray-200 dark:border-gray-700 pb-4">
        <h2 class="text-xl font-bold text-gray-900 dark:text-gray-100 flex items-center gap-2">
            <span>Academic Profile</span>
        </h2>
        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
            Official student record details as registered with Academic Office.
        </p>
    </div>

    @if(!$student)
        <div class="p-4 bg-amber-50 dark:bg-amber-950/40 border border-amber-200 dark:border-amber-800 rounded-xl text-sm font-medium text-amber-800 dark:text-amber-300">
            ⚠️ Student record details could not be found. Please contact Academic Office.
        </div>
    @else
        <!-- Single Unified Information Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">

            <!-- Full Name -->
            <div>
                <label class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider block mb-1">Full Name</label>
                <div class="text-base font-semibold text-gray-900 dark:text-gray-100">
                    {{ $user->name }}
                </div>
            </div>

            <!-- Roll Number -->
            <div>
                <label class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider block mb-1">Roll Number</label>
                <div class="text-base font-bold font-semibold text-gray-900 dark:text-gray-100">
                    {{ $student->roll_number }}
                </div>
            </div>

            <!-- Program -->
            <div>
                <label class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider block mb-1">Program</label>
                <div class="text-base font-semibold text-gray-900 dark:text-gray-100">
                    {{ $student->isPhd() ? 'Ph.D.' : 'M.S. (Research)' }}
                </div>
            </div>

            <!-- Official Email -->
            <div>
                <label class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider block mb-1">Official Email</label>
                <div class="text-base font-medium text-gray-900 dark:text-gray-100 truncate">
                    {{ $user->email }}
                </div>
            </div>

            <!-- Department -->
            <div>
                <label class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider block mb-1">Department</label>
                <div class="text-base font-medium text-gray-900 dark:text-gray-100">
                    {{ $student->department->name ?? 'Not Assigned' }}
                    @if(isset($student->department->code))
                        <span class="text-xs font-semibold text-gray-500 dark:text-gray-400">({{ $student->department->code }})</span>
                    @endif
                </div>
            </div>

            <!-- Admission Category -->
            <div>
                <label class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider block mb-1">Admission Category</label>
                <div class="text-base font-medium text-gray-900 dark:text-gray-100">
                    {{ $student->admission_category ?: 'TA (Teaching Assistantship)' }}
                </div>
            </div>

            <!-- Course Credits Required -->
            <div>
                <label class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider flex items-center mb-1">
                    <span>Course Credits Required</span>
                    <x-info-button text="Minimum course credits required for the degree program." />
                </label>
                <div class="text-base font-medium text-gray-900 dark:text-gray-100">
                    {{ is_numeric($student->course_credits_required) ? ($student->course_credits_required == (int)$student->course_credits_required ? (int)$student->course_credits_required : $student->course_credits_required) . ' Credits' : 'N/A' }}
                </div>
            </div>

            <!-- Course Credits Earned -->
            <div>
                <label class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider flex items-center mb-1">
                    <span>Course Credits Earned (From System)</span>
                    <x-info-button text="Course credits earned including coursework, seminars, and research credits." />
                </label>
                <div class="text-base font-medium text-gray-900 dark:text-gray-100">
                    {{ is_numeric($student->course_credits_earned) ? ($student->course_credits_earned == (int)$student->course_credits_earned ? (int)$student->course_credits_earned : $student->course_credits_earned) . ' Credits' : 'N/A' }}
                </div>
            </div>

            <!-- Date of Joining -->
            <div>
                <label class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider block mb-1">Date of Joining</label>
                <div class="text-base font-medium text-gray-900 dark:text-gray-100">
                    {{ $student->date_joining ? \Carbon\Carbon::parse($student->date_joining)->format('d-m-Y') : 'N/A' }}
                </div>
            </div>

            <!-- Date of Registration -->
            <div>
                <label class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider block mb-1">Date of Registration</label>
                <div class="text-base font-medium text-gray-900 dark:text-gray-100">
                    {{ $student->date_registration ? \Carbon\Carbon::parse($student->date_registration)->format('d-m-Y') : 'N/A' }}
                </div>
            </div>

            <!-- Date of Confirmation -->
            <div>
                <label class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider block mb-1">Date of Confirmation</label>
                <div class="text-base font-medium text-gray-900 dark:text-gray-100">
                    {{ $student->date_confirmation ? \Carbon\Carbon::parse($student->date_confirmation)->format('d-m-Y') : 'N/A' }}
                </div>
            </div>

            <!-- Main Supervisor -->
            <div>
                <label class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider block mb-1">Main Supervisor</label>
                <div class="text-base font-medium text-gray-900 dark:text-gray-100">
                    {{ $student->mainSupervisors->pluck('name')->join(', ') ?: ($student->supervisors->first()?->name ?? 'Not Assigned') }}
                </div>
            </div>

            <!-- Co-Supervisor(s) -->
            <div>
                <label class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider block mb-1">Co-Supervisor(s)</label>
                <div class="text-base font-medium text-gray-900 dark:text-gray-100">
                    {{ $student->coSupervisors->pluck('name')->join(', ') ?: 'None' }}
                </div>
            </div>

            <!-- External Supervisor(s) -->
            <div>
                <label class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider block mb-1">External Supervisor(s)</label>
                <div class="text-base font-medium text-gray-900 dark:text-gray-100">
                    @if($student->externalSupervisors->isNotEmpty())
                        {{ $student->externalSupervisors->map(fn($s) => $s->name . ($s->externalSupervisorProfile?->affiliated_institute ? ' (' . $s->externalSupervisorProfile->affiliated_institute . ')' : ''))->join(', ') }}
                    @else
                        None
                    @endif
                </div>
            </div>

            <!-- PSPC Committee Member(s) -->
            <div>
                <label class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider block mb-1">PSPC Committee Member(s)</label>
                <div class="text-base font-medium text-gray-900 dark:text-gray-100">
                    {{ $student->pspcMembers->pluck('name')->join(', ') ?: 'Not Assigned' }}
                </div>
            </div>

        </div>

    @endif
</section>
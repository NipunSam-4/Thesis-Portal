@props(['student'])

@php
    $studentUser = $student->user;
@endphp

<div class="inline-flex items-center" @click.stop>
    <!-- Info Trigger Button -->
    <button type="button" 
            @click.stop="$dispatch('open-modal', 'student-info-modal-{{ $student->id }}')" 
            title="See Student Information" 
            class="inline-flex items-center justify-center p-1 ml-1 rounded-full text-slate-400 hover:text-indigo-600 dark:hover:text-indigo-400 hover:bg-indigo-50 dark:hover:bg-indigo-950/50 transition-all duration-150 focus:outline-none cursor-pointer">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
        </svg>
    </button>

    <!-- Student Info Modal Popup -->
    <x-modal name="student-info-modal-{{ $student->id }}" maxWidth="2xl">
        <div class="p-6 space-y-6 text-left">
            <!-- Modal Header -->
            <div class="flex items-center justify-between border-b border-gray-200 dark:border-gray-700 pb-4">
                <div>
                    <h2 class="text-lg font-bold text-gray-900 dark:text-gray-100 flex items-center gap-2">
                        <span>🎓 Student Academic Profile</span>
                    </h2>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                        Official academic details registered with Academic Office.
                    </p>
                </div>
                <button type="button" @click="$dispatch('close')" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 transition-colors p-1 text-lg font-bold">
                    ✕
                </button>
            </div>

            <!-- Profile Data Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5 text-sm">

                <!-- Full Name -->
                <div>
                    <label class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider block mb-1">Full Name</label>
                    <div class="text-base font-medium text-gray-900 dark:text-gray-100">
                        {{ $studentUser->name ?? 'N/A' }}
                    </div>
                </div>

                <!-- Roll Number -->
                <div>
                    <label class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider block mb-1">Roll Number</label>
                    <div class="text-base font-medium text-gray-900 dark:text-gray-100">
                        {{ $student->roll_number }}
                    </div>
                </div>

                <!-- Program -->
                <div>
                    <label class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider block mb-1">Program</label>
                    <div class="text-base font-medium text-gray-900 dark:text-gray-100">
                        {{ $student->isPhd() ? 'Ph.D.' : 'M.S. (Research)' }}
                    </div>
                </div>

                <!-- Official Email -->
                <div>
                    <label class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider block mb-1">Official Email</label>
                    <div class="text-base font-medium text-gray-900 dark:text-gray-100 truncate">
                        {{ $studentUser->email ?? 'N/A' }}
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

                <!-- Course Credits Earned -->
                <div>
                    <label class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider block mb-1">Course Credits Earned</label>
                    <div class="text-base font-medium text-gray-900 dark:text-gray-100">
                        {{ is_numeric($student->course_credits_earned) ? ($student->course_credits_earned == (int)$student->course_credits_earned ? (int)$student->course_credits_earned : $student->course_credits_earned) : 0 }} Credits
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

                <!-- Supervisor(s) -->
                <div>
                    <label class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider block mb-1">Supervisor(s)</label>
                    <div class="text-base font-medium text-gray-900 dark:text-gray-100">
                        {{ $student->supervisors->pluck('name')->join(', ') ?: 'Not Assigned' }}
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

            <!-- Modal Footer -->
            <div class="flex justify-end pt-4 border-t border-gray-200 dark:border-gray-700">
                <x-secondary-button x-on:click="$dispatch('close')">
                    {{ __('Close') }}
                </x-secondary-button>
            </div>
        </div>
    </x-modal>
</div>

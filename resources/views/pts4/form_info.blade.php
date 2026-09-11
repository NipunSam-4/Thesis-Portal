@php
    $pts4 = $pts4 ?? $form ?? null;
    $thesis = $thesis ?? $pts4?->thesis;
    $student = $student ?? $thesis?->student;
    $studentUser = $studentUser ?? $student?->user;
@endphp

<!-- Section 1: Read-Only Student Information -->
<div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6" x-data="{ showStudentInfo: true }">
    <div class="flex items-center justify-between border-b border-gray-100 dark:border-gray-700 pb-3 mb-4 cursor-pointer select-none" @click="showStudentInfo = !showStudentInfo">
        <h3 class="text-lg font-bold text-gray-900 dark:text-white flex items-center gap-2">
            <span>1. Student Information</span>
        </h3>
        <button type="button" class="p-1.5 rounded-lg text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 transition" @click.stop="showStudentInfo = !showStudentInfo">
            <svg class="w-5 h-5 transform transition-transform duration-200" :class="{ 'rotate-180': !showStudentInfo }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
            </svg>
        </button>
    </div>

    <div x-show="showStudentInfo" x-transition class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div>
            <x-readonly-label value="Student Name" />
            <x-readonly-input :value="$studentUser->name ?? 'N/A'" />
        </div>

        <div>
            <x-readonly-label value="Student Name (Hindi)" />
            <x-readonly-input :value="$pts4->hindi_name ?? ($student->hindi_name ?? 'N/A')" class="font-hindi" />
        </div>

        <div>
            <x-readonly-label value="Roll Number" />
            <x-readonly-input :value="$student->roll_number ?? 'N/A'" />
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

        <div>
            <x-readonly-label value="Open Seminar Date" />
            <x-readonly-input :value="$thesis->getOpenSeminarDate()?->format('d-m-Y') ?? 'N/A'" />
        </div>

        <div>
            <x-readonly-label value="Primary Email Address" />
            <x-readonly-input :value="$studentUser->email ?? 'N/A'" />
        </div>

        <div>
            <x-readonly-label value="Recent Contact No." />
            <x-readonly-input :value="$student->phone_number ? (($student->phone_country_code ?: '+91') . ' ' . $student->phone_number) : 'N/A'" />
        </div>

        <div>
            <x-readonly-label value="Alternate Contact No." />
            <x-readonly-input :value="$pts4->getFormattedAlternatePhoneNumber()" />
        </div>

        <div>
            <x-readonly-label value="Alternate Email Address" />
            <x-readonly-input :value="$pts4->alternate_email ?: ($student->alternate_email ?: 'N/A')" />
        </div>

        <div class="md:col-span-3">
            <x-readonly-label value="Main Supervisor" />
            <x-readonly-input :value="$student?->mainSupervisors?->pluck('name')->join(', ') ?: ($student?->supervisors?->first()?->name ?? 'Not Assigned')" />
        </div>

        <div class="md:col-span-3">
            <x-readonly-label value="Co-Supervisor(s)" />
            <x-readonly-input :value="$student?->coSupervisors?->pluck('name')->join(', ') ?: 'None'" />
        </div>

        <div class="md:col-span-3">
            <x-readonly-label value="External Supervisor(s)" />
            @php
                $extSupText = ($student && $student->externalSupervisors->isNotEmpty())
                    ? $student->externalSupervisors->map(fn($s) => $s->name . ($s->externalSupervisorProfile?->affiliated_institute ? ' (' . $s->externalSupervisorProfile->affiliated_institute . ')' : ''))->join(', ')
                    : 'None';
            @endphp
            <x-readonly-input :value="$extSupText" />
        </div>

        <div>
            <x-readonly-label value="Date of Submission" />
            <x-readonly-input :value="$pts4->created_at ? $pts4->created_at->format('d-m-Y') : 'N/A'" />
        </div>
    </div>
</div>

<!-- Section 2: Name of Thesis -->
<div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 space-y-2">
    <h3 class="text-lg font-bold text-gray-900 dark:text-white border-b border-gray-100 dark:border-gray-700 pb-2">
        2. Name of Thesis
    </h3>
    <div>
        <x-form-label class="flex items-center gap-2">
            <span>Thesis Title</span>
            @if($pts4->main_supervisor_thesis_title && $pts4->main_supervisor_thesis_title !== $pts4->thesis_title)
                <x-modified-badge 
                    :old-value="$pts4->thesis_title ?: ($thesis->title ?? 'N/A')" 
                    :new-value="$pts4->main_supervisor_thesis_title" />
            @endif
        </x-form-label>
        @php
            $dispThesisTitle = $pts4->effective_thesis_title;
        @endphp
        <x-readonly-input :value="$dispThesisTitle" class="font-medium" />
    </div>
</div>

<!-- Section 3: Uploaded Documents Inspection (Read-Only) -->
<div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 space-y-6">
    <h3 class="text-lg font-bold text-gray-900 dark:text-white border-b border-gray-100 dark:border-gray-700 pb-2">
        3. Uploaded Documents
    </h3>

    <div class="p-4 bg-gray-50 dark:bg-gray-700/50 rounded-xl border border-gray-200 dark:border-gray-600 space-y-2">
        <div class="text-xs font-bold text-gray-500 uppercase flex items-center gap-2">
            <span>{{ ($student && $student->isPhd()) ? 'PhD' : 'MS(R)' }} Thesis Document</span>
            @if($pts4->main_supervisor_thesis_doc_path && $pts4->main_supervisor_thesis_doc_path !== $pts4->thesis_doc_path)
                <x-modified-badge />
            @endif
        </div>
        @php
            $thesisDocPath = $pts4->getEffectiveThesisDocPath();
            $thesisDocField = $pts4->getEffectiveThesisDocField();
        @endphp
        @if($thesisDocPath)
            <a href="{{ route('pts.document.serve', ['pts4', $pts4->id, $thesisDocField]) }}" target="_blank" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-lg shadow transition inline-flex items-center">
                📄 View Thesis Document
            </a>
        @else
            <span class="text-xs font-bold text-gray-400 italic">No File Uploaded</span>
        @endif
    </div>
</div>
<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div class="flex items-center space-x-3">
                <h2 class="font-bold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                    {{ __('PTS-1 Review Portal') }}
                </h2>
                @if(in_array($pts1->current_stage, ['completed', 'rejected']))
                    @if($pts1->status === 'accepted')
                        <span class="px-3 py-1 bg-emerald-100 dark:bg-emerald-900/60 text-emerald-800 dark:text-emerald-300 text-xs font-extrabold rounded-full uppercase tracking-wider flex items-center shadow-sm">
                            ✓ Approved
                        </span>
                    @else
                        <span class="px-3 py-1 bg-red-100 dark:bg-red-900/60 text-red-800 dark:text-red-300 text-xs font-extrabold rounded-full uppercase tracking-wider flex items-center shadow-sm">
                            ❌ Rejected
                        </span>
                    @endif
                @endif
            </div>
            <x-back-to-dashboard-button />
        </div>
    </x-slot>

    <div class="py-8" x-data="{
        showRevertModal: false,
        recommendation: '1',
        isVerified: false
    }">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">

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

            <!-- Section 1: Read-Only Student Information -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
                <div class="flex items-center justify-between border-b border-gray-100 dark:border-gray-700 pb-3 mb-4">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white flex items-center">
                        1. Student Information
                    </h3>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label class="block text-xs font-semibold uppercase text-gray-500 mb-1">Student Name</label>
                        <input type="text" value="{{ $studentUser->name }}" readonly class="w-full bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200 rounded-lg border-gray-300 dark:border-gray-600 cursor-not-allowed">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold uppercase text-gray-500 mb-1">Roll Number</label>
                        <input type="text" value="{{ $student->roll_number }}" readonly class="w-full bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200 rounded-lg border-gray-300 dark:border-gray-600 cursor-not-allowed">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold uppercase text-gray-500 mb-1">Department</label>
                        <input type="text" value="{{ $student->department->name ?? 'N/A' }}" readonly class="w-full bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200 rounded-lg border-gray-300 dark:border-gray-600 cursor-not-allowed">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold uppercase text-gray-500 mb-1">Date of Registration</label>
                        <input type="text" value="{{ $student->date_registration ? \Carbon\Carbon::parse($student->date_registration)->format('d-m-Y') : 'N/A' }}" readonly class="w-full bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200 rounded-lg border-gray-300 dark:border-gray-600 cursor-not-allowed">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold uppercase text-gray-500 mb-1">Date of Joining</label>
                        <input type="text" value="{{ $student->date_joining ? \Carbon\Carbon::parse($student->date_joining)->format('d-m-Y') : 'N/A' }}" readonly class="w-full bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200 rounded-lg border-gray-300 dark:border-gray-600 cursor-not-allowed">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold uppercase text-gray-500 mb-1">Date of Confirmation</label>
                        <input type="text" value="{{ $student->date_confirmation ? \Carbon\Carbon::parse($student->date_confirmation)->format('d-m-Y') : 'N/A' }}" readonly class="w-full bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200 rounded-lg border-gray-300 dark:border-gray-600 cursor-not-allowed">
                    </div>
                </div>
            </div>

            <!-- Section 2: Name of Thesis (Read-Only) -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 space-y-2">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white border-b border-gray-100 dark:border-gray-700 pb-2">
                    2. Name of Thesis
                </h3>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Thesis Title</label>
                    <input type="text" value="{{ $thesis->title }}" readonly class="w-full bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200 rounded-lg border-gray-300 dark:border-gray-600 cursor-not-allowed">
                </div>
            </div>

            <!-- Section 3: Seminar & Confirmation Details (Read-Only) -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4 border-b border-gray-100 dark:border-gray-700 pb-2">
                    3. Open Seminar Details
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Date of Open Seminar</label>
                        <input type="text" value="{{ $pts1->seminar_date ? $pts1->seminar_date->format('d-m-Y') : 'N/A' }}" readonly class="w-full bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200 rounded-lg border-gray-300 dark:border-gray-600 cursor-not-allowed">
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Time of Open Seminar</label>
                        <input type="text" value="{{ $pts1->seminar_time ?? 'N/A' }}" readonly class="w-full bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200 rounded-lg border-gray-300 dark:border-gray-600 cursor-not-allowed">
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Venue of Open Seminar</label>
                        <input type="text" value="{{ $pts1->seminar_venue ?? 'N/A' }}" readonly class="w-full bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200 rounded-lg border-gray-300 dark:border-gray-600 cursor-not-allowed">
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Online Meeting Link</label>
                        <input type="text" value="{{ $pts1->meeting_link ?? '' }}" readonly class="w-full bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200 rounded-lg border-gray-300 dark:border-gray-600 cursor-not-allowed">
                    </div>
                </div>
            </div>

            <!-- Section 4: Institute Norms & Criteria Verification (Read-Only) -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 space-y-6">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white border-b border-gray-100 dark:border-gray-700 pb-2">
                    4. Institute Norms & Criteria Verification
                </h3>
                
                <!-- Minimum Time Requirement -->
                <div class="p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="font-semibold text-gray-900 dark:text-white text-sm">
                            Fulfilling minimum time requirement criteria for thesis submission?
                        </span>
                        <span class="px-3 py-1 text-xs font-bold rounded-full {{ $pts1->min_time_req_fulfilled ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/60 dark:text-emerald-300' : 'bg-red-100 text-red-800 dark:bg-red-900/60 dark:text-red-300' }}">
                            {{ $pts1->min_time_req_fulfilled ? 'Yes' : 'No' }}
                        </span>
                    </div>
                    
                    @if(!$pts1->min_time_req_fulfilled)
                    <div class="pt-3 border-t border-gray-200 dark:border-gray-600 space-y-2">
                        <div class="flex items-center justify-between">
                            <span class="font-semibold text-gray-900 dark:text-white text-sm">
                                Special approval taken for minimum time relaxation?
                            </span>
                            <span class="px-3 py-1 text-xs font-bold rounded-full {{ $pts1->special_approval_min_time ? 'bg-emerald-100 text-emerald-800' : 'bg-red-100 text-red-800' }}">
                                {{ $pts1->special_approval_min_time ? 'Yes' : 'No' }}
                            </span>
                        </div>
                        @if($pts1->min_time_approval_doc_path)
                        <div class="pt-1">
                            <a href="{{ route('pts.document.serve', ['pts1', $pts1->id, 'min_time_approval_doc_path']) }}" target="_blank" class="inline-flex items-center text-xs font-bold text-blue-600 dark:text-blue-400 hover:underline">
                                📄 View Special Minimum Time Approval Copy
                            </a>
                        </div>
                        @endif
                    </div>
                    @endif
                </div>
                
                <!-- Publication Norm -->
                <div class="p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="font-semibold text-gray-900 dark:text-white text-sm">
                            Fulfilling Institute publication norm for open seminar?
                        </span>
                        <span class="px-3 py-1 text-xs font-bold rounded-full {{ $pts1->publication_norm_fulfillment ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/60 dark:text-emerald-300' : 'bg-red-100 text-red-800 dark:bg-red-900/60 dark:text-red-300' }}">
                            {{ $pts1->publication_norm_fulfillment ? 'Yes' : 'No' }}
                        </span>
                    </div>

                    @if(!$pts1->publication_norm_fulfillment)
                        <div class="pt-3 border-t border-gray-200 dark:border-gray-600 space-y-2">
                            <div class="flex items-center justify-between">
                                <span class="font-semibold text-gray-900 dark:text-white text-sm">
                                    Special approval taken for publication norm relaxation?
                                </span>
                                <span class="px-3 py-1 text-xs font-bold rounded-full {{ $pts1->special_approval_publication ? 'bg-emerald-100 text-emerald-800' : 'bg-red-100 text-red-800' }}">
                                    {{ $pts1->special_approval_publication ? 'Yes' : 'No' }}
                                </span>
                            </div>
                            @if($pts1->publication_approval_doc_path)
                                <div class="pt-1">
                                    <a href="{{ route('pts.document.serve', ['pts1', $pts1->id, 'publication_approval_doc_path']) }}" target="_blank" class="inline-flex items-center text-xs font-bold text-blue-600 dark:text-blue-400 hover:underline">
                                        📄 View Special Publication Approval Copy
                                    </a>
                                </div>
                            @endif
                        </div>
                    @endif
                </div>
            </div>

            <!-- Section 5: Uploaded Documents Inspection (Read-Only) -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 space-y-6">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white border-b border-gray-100 dark:border-gray-700 pb-2">
                    5. Uploaded Documents
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Draft Synopsis Card -->
                    <div class="p-4 bg-gray-50 dark:bg-gray-700/50 rounded-xl border border-gray-200 dark:border-gray-600 space-y-2">
                        <div class="text-xs font-bold text-gray-500 uppercase">Draft Synopsis Report</div>
                        @if($pts1->draft_synopsis_report_doc_path)
                            <a href="{{ route('pts.document.serve', ['pts1', $pts1->id, 'draft_synopsis_report_doc_path']) }}" target="_blank" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-lg shadow transition inline-flex items-center">
                                📄 View Draft Synopsis Report
                            </a>
                        @else
                            <span class="text-xs font-bold text-gray-400 italic">No File Uploaded</span>
                        @endif
                    </div>

                    <!-- Publication List Card -->
                    <div class="p-4 bg-gray-50 dark:bg-gray-700/50 rounded-xl border border-gray-200 dark:border-gray-600 space-y-2">
                        <div class="text-xs font-bold text-gray-500 uppercase">Publication List</div>
                        @if($pts1->publication_list_doc_path)
                            <a href="{{ route('pts.document.serve', ['pts1', $pts1->id, 'publication_list_doc_path']) }}" target="_blank" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-lg shadow transition inline-flex items-center">
                                📊 Download / View Publication List
                            </a>
                        @else
                            <span class="text-xs font-bold text-gray-400 italic">No File Uploaded</span>
                        @endif
                    </div>
                </div>
            </div>

            @php
                $stage = $pts1->current_stage;
                $stageWeights = [
                    'main_supervisor' => 1,
                    'co_supervisors'  => 2,
                    'pspc_members'    => 3,
                    'dpgc'            => 4,
                    'hod'             => 5,
                    'section_officer' => 6,
                    'doaa'            => 7,
                    'completed'       => 8,
                ];

                $currentWeight = $stageWeights[$stage] ?? 1;

                $passedMainSupervisor = ($currentWeight > 1) || !is_null($pts1->main_supervisor_submitted_at) || !is_null($pts1->main_supervisor_recommendation);
                $passedCoSupervisors  = ($currentWeight > 2) || !is_null($pts1->co_supervisors_submitted_at);
                $passedPspcMembers    = ($currentWeight > 3) || !is_null($pts1->pspc_members_submitted_at);
                $passedDpgc           = ($currentWeight > 4) || !is_null($pts1->dpgc_recommendation);
                $passedHod            = ($currentWeight > 5) || !is_null($pts1->hod_recommendation);
                $passedSectionOfficer = ($currentWeight > 6) || !is_null($pts1->section_officer_recommendation);
                $passedDoaa           = ($currentWeight > 7) || !is_null($pts1->doaa_approval) || !is_null($pts1->pts1_submitted_at);
            @endphp

            <!-- Section 6: Previous Authority Recommendations & Comments Audit Trail -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 space-y-6">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white border-b border-gray-100 dark:border-gray-700 pb-2">
                    6. Previous Authority Recommendations & Audit Trail
                </h3>

                <div class="space-y-4">
                    <!-- Main Supervisor Evaluation -->
                    @if($passedMainSupervisor)
                        <div class="p-4 bg-indigo-50/70 dark:bg-indigo-950/40 border-l-4 border-indigo-500 rounded-xl space-y-2">
                            <div class="flex items-center justify-between text-s">
                                <span class="font-bold text-indigo-900 dark:text-indigo-200">
                                    Main Supervisor
                                </span>
                                <span class="px-2.5 py-0.5 bg-emerald-600 text-white font-bold rounded-full uppercase text-[10px]">
                                    Open Seminar Status: {{ strtoupper($pts1->work_status) }}
                                </span>
                            </div>
                            <div class="text-xs text-gray-700 dark:text-gray-300">
                                <strong>Additional Comment:</strong>
                                @if($pts1->main_supervisor_student_comment)
                                    <p class="italic bg-white dark:bg-gray-800 p-2.5 rounded-lg border border-indigo-100 dark:border-indigo-900 mt-1">
                                        {{ $pts1->main_supervisor_student_comment }}
                                    </p>
                                @else
                                    <div class="flex items-center space-x-2 text-xs text-gray-600 dark:text-gray-500 bg-gray-200/60 dark:bg-gray-900/40 p-2.5 rounded-lg border border-dashed border-gray-200 dark:border-gray-700/60 mt-1">
                                        <svg class="w-4 h-4 text-gray-400 dark:text-gray-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 4.418 9 8z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3l18 18"></path>
                                        </svg>
                                        <span class="italic font-normal">Student Comment not provided</span>
                                    </div>
                                @endif
                            </div>
                       
                            <div class="text-xs text-gray-700 dark:text-gray-300 pt-1">
                                <strong>Main Supervisor Remark:</strong>
                                @if($pts1->main_supervisor_confidential_remark)
                                    <p class="italic bg-white dark:bg-gray-800 p-2.5 rounded-lg border border-indigo-100 dark:border-indigo-900 mt-1">
                                        {{ $pts1->main_supervisor_confidential_remark }}
                                    </p>
                                @else
                                    <div class="flex items-center space-x-2 text-xs text-gray-600 dark:text-gray-500 bg-gray-200/60 dark:bg-gray-900/40 p-2.5 rounded-lg border border-dashed border-gray-200 dark:border-gray-700/60 mt-1">
                                        <svg class="w-4 h-4 text-gray-400 dark:text-gray-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 4.418 9 8z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3l18 18"></path>
                                        </svg>
                                        <span class="italic font-normal">Confidential Remark not provided</span>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif

                    <!-- Co-Supervisors Recommendations (Slots 1..10) -->
                    @php
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
                        <div class="p-4 bg-blue-50/70 dark:bg-blue-950/40 border-l-4 border-blue-500 rounded-xl space-y-3">
                            <h5 class="text-s font-bold text-blue-900 dark:text-blue-200">Co-Supervisor</h5>
                            
                            @for($i = 1; $i <= 10; $i++)
                                @php
                                    $idCol = "co_supervisor_{$i}_id";
                                    $recCol = "co_supervisor_{$i}_recommendation";
                                    $remCol = "co_supervisor_{$i}_confidential_remark";
                                    $coUser = $coSupervisors[$i] ?? ($pts1->$idCol ? \App\Models\User::find($pts1->$idCol) : null);
                                @endphp

                                @if($coUser && (!is_null($pts1->$remCol) || $passedCoSupervisors))
                                    <div class="text-xs space-y-1 pt-1 {{ $i > 1 ? 'border-t border-blue-100 dark:border-blue-900' : '' }}">
                                        <div class="flex justify-between font-semibold">
                                            <span>{{ $coUser->name }} (Co-Supervisor {{ $i }}) Remark:</span>
                                            <span class="font-bold {{ $pts1->$recCol ? 'text-emerald-600' : 'text-red-600' }}">
                                                {{ $pts1->$recCol ? '✓ Recommended' : '❌ Not Recommended' }}
                                            </span>
                                        </div>
                                        @if($pts1->$remCol)
                                            <p class="italic text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 p-2.5 rounded-lg border border-blue-100 dark:border-blue-900 mt-1">
                                                {{ $pts1->$remCol }}
                                            </p>
                                        @else
                                            <div class="flex items-center space-x-2 text-xs text-gray-600 dark:text-gray-500 bg-gray-200/60 dark:bg-gray-900/40 p-2.5 rounded-lg border border-dashed border-gray-200 dark:border-gray-700/60 mt-1">
                                                <svg class="w-4 h-4 text-gray-400 dark:text-gray-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 4.418 9 8z"></path>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3l18 18"></path>
                                                </svg>
                                                <span class="italic font-normal">Confidential Remark not provided</span>
                                            </div>
                                        @endif
                                    </div>
                                @endif
                            @endfor
                        </div>
                    @endif

                    <!-- PSPC Committee Recommendations (Slots 1..10) -->
                    @php
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
                        <div class="p-4 bg-purple-50/70 dark:bg-purple-950/40 border-l-4 border-purple-500 rounded-xl space-y-3">
                            <h5 class="text-s font-bold text-purple-900 dark:text-purple-200">PSPC Committee</h5>
                            
                            @for($i = 1; $i <= 10; $i++)
                                @php
                                    $idCol = "pspc_member_{$i}_id";
                                    $recCol = "pspc_member_{$i}_recommendation";
                                    $remCol = "pspc_member_{$i}_confidential_remark";
                                    $pspcUser = $pspcMembers[$i] ?? ($pts1->$idCol ? \App\Models\User::find($pts1->$idCol) : null);
                                @endphp

                                @if($pspcUser && (!is_null($pts1->$remCol) || $passedPspcMembers))
                                    <div class="text-xs space-y-1 pt-1 {{ $i > 1 ? 'border-t border-purple-100 dark:border-purple-900' : '' }}">
                                        <div class="flex justify-between font-semibold">
                                            <span>{{ $pspcUser->name }} (PSPC Member {{ $i }}) Remark:</span>
                                            <span class="font-bold {{ $pts1->$recCol ? 'text-emerald-600' : 'text-red-600' }}">
                                                {{ $pts1->$recCol ? '✓ Recommended' : '❌ Not Recommended' }}
                                            </span>
                                        </div>
                                        @if($pts1->$remCol)
                                            <p class="italic text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 p-2.5 rounded-lg border border-purple-100 dark:border-purple-900 mt-1">
                                                {{ $pts1->$remCol }}
                                            </p>
                                        @else
                                            <div class="flex items-center space-x-2 text-xs text-gray-600 dark:text-gray-500 bg-gray-200/60 dark:bg-gray-900/40 p-2.5 rounded-lg border border-dashed border-gray-200 dark:border-gray-700/60 mt-1">
                                                <svg class="w-4 h-4 text-gray-400 dark:text-gray-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 4.418 9 8z"></path>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3l18 18"></path>
                                                </svg>
                                                <span class="italic font-normal">Confidential Remark not provided</span>
                                            </div>
                                        @endif
                                    </div>
                                @endif
                            @endfor
                        </div>
                    @endif

                    <!-- DPGC Endorsement -->
                    @if($passedDpgc)
                        <div class="p-4 bg-teal-50/70 dark:bg-teal-950/40 border-l-4 border-teal-500 rounded-xl space-y-2">
                            <div class="flex justify-between font-bold text-teal-900 dark:text-teal-200">
                                <h5 class="text-s">Department Postgraduate Committee (DPGC)</h5>
                                <span class="font-bold text-xs {{ $pts1->dpgc_recommendation ? 'text-emerald-600' : 'text-red-600' }}">
                                    {{ $pts1->dpgc_recommendation ? '✓ Recommended' : '❌ Not Recommended' }}
                                </span>
                            </div>
                            <div class="text-xs text-gray-700 dark:text-gray-300">
                                <strong>Student Comment:</strong>
                                @if($pts1->dpgc_student_comment)
                                    <p class="italic bg-white dark:bg-gray-800 p-2.5 rounded-lg border border-teal-100 dark:border-teal-900 mt-0.5">
                                        {{ $pts1->dpgc_student_comment }}
                                    </p>
                                @else
                                    <div class="flex items-center space-x-2 text-xs text-gray-600 dark:text-gray-500 bg-gray-200/60 dark:bg-gray-900/40 p-2.5 rounded-lg border border-dashed border-gray-200 dark:border-gray-700/60 mt-0.5">
                                        <svg class="w-4 h-4 text-gray-400 dark:text-gray-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 4.418 9 8z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3l18 18"></path>
                                        </svg>
                                        <span class="italic font-normal">Student Comment not provided</span>
                                    </div>
                                @endif
                            </div>
                            <div class="text-xs text-gray-700 dark:text-gray-300 pt-0.5">
                                <strong>DPGC Remark:</strong>
                                @if($pts1->dpgc_confidential_remark)
                                    <p class="italic bg-white dark:bg-gray-800 p-2.5 rounded-lg border border-teal-100 dark:border-teal-900 mt-0.5">
                                        {{ $pts1->dpgc_confidential_remark }}
                                    </p>
                                @else
                                    <div class="flex items-center space-x-2 text-xs text-gray-600 dark:text-gray-500 bg-gray-200/60 dark:bg-gray-900/40 p-2.5 rounded-lg border border-dashed border-gray-200 dark:border-gray-700/60 mt-0.5">
                                        <svg class="w-4 h-4 text-gray-400 dark:text-gray-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 4.418 9 8z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3l18 18"></path>
                                        </svg>
                                        <span class="italic font-normal">Confidential Remark not provided</span>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif

                    <!-- HOD Endorsement -->
                    @if($passedHod)
                        <div class="p-4 bg-amber-50/70 dark:bg-amber-950/40 border-l-4 border-amber-500 rounded-xl space-y-2">
                            <div class="flex justify-between font-bold text-amber-900 dark:text-amber-200">
                                <h5 class="text-s">Head of Department (HOD)</h5>
                                <span class="font-bold text-xs {{ $pts1->hod_recommendation ? 'text-emerald-600' : 'text-red-600' }}">
                                    {{ $pts1->hod_recommendation ? '✓ Recommended' : '❌ Not Recommended' }}
                                </span>
                            </div>
                            <div class="text-xs text-gray-700 dark:text-gray-300">
                                <strong>Student Comment:</strong>
                                @if($pts1->hod_student_comment)
                                    <p class="italic bg-white dark:bg-gray-800 p-2.5 rounded-lg border border-amber-100 dark:border-amber-900 mt-0.5">
                                        {{ $pts1->hod_student_comment }}
                                    </p>
                                @else
                                    <div class="flex items-center space-x-2 text-xs text-gray-600 dark:text-gray-500 bg-gray-200/60 dark:bg-gray-900/40 p-2.5 rounded-lg border border-dashed border-gray-200 dark:border-gray-700/60 mt-0.5">
                                        <svg class="w-4 h-4 text-gray-400 dark:text-gray-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 4.418 9 8z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3l18 18"></path>
                                        </svg>
                                        <span class="italic font-normal">Student Comment not provided</span>
                                    </div>
                                @endif
                            </div>
                            <div class="text-xs text-gray-700 dark:text-gray-300 pt-0.5">
                                <strong>HOD Remark:</strong>
                                @if($pts1->hod_confidential_remark)
                                    <p class="italic bg-white dark:bg-gray-800 p-2.5 rounded-lg border border-amber-100 dark:border-amber-900 mt-0.5">
                                        {{ $pts1->hod_confidential_remark }}
                                    </p>
                                @else
                                    <div class="flex items-center space-x-2 text-xs text-gray-600 dark:text-gray-500 bg-gray-200/60 dark:bg-gray-900/40 p-2.5 rounded-lg border border-dashed border-gray-200 dark:border-gray-700/60 mt-0.5">
                                        <svg class="w-4 h-4 text-gray-400 dark:text-gray-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 4.418 9 8z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3l18 18"></path>
                                        </svg>
                                        <span class="italic font-normal">Confidential Remark not provided</span>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif

                    <!-- Section Officer Endorsement -->
                    @if($passedSectionOfficer)
                        <div class="p-4 bg-rose-50/70 dark:bg-rose-950/40 border-l-4 border-rose-500 rounded-xl space-y-2">
                            <div class="flex justify-between font-bold text-rose-900 dark:text-rose-200">
                                <h5 class="text-s">Section Officer</h5>
                                <span class="font-bold text-xs text-emerald-600">
                                    ✓ Details Verified & Forwarded
                                </span>
                            </div>
                            <div class="text-xs text-gray-700 dark:text-gray-300 pt-0.5">
                                <strong>Verification Remark:</strong>
                                @if($pts1->section_officer_confidential_remark)
                                    <p class="italic bg-white dark:bg-gray-800 p-2.5 rounded-lg border border-rose-100 dark:border-rose-900 mt-0.5">
                                        {{ $pts1->section_officer_confidential_remark }}
                                    </p>
                                @else
                                    <div class="flex items-center space-x-2 text-xs text-gray-600 dark:text-gray-500 bg-gray-200/60 dark:bg-gray-900/40 p-2.5 rounded-lg border border-dashed border-gray-200 dark:border-gray-700/60 mt-0.5">
                                        <svg class="w-4 h-4 text-gray-400 dark:text-gray-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 4.418 9 8z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3l18 18"></path>
                                        </svg>
                                        <span class="italic font-normal">Confidential Remark not provided</span>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif

                    <!-- DOAA Approval -->
                    @if($passedDoaa)
                        <div class="p-4 bg-emerald-50/70 dark:bg-emerald-950/40 border-l-4 border-emerald-500 rounded-xl space-y-2">
                            <div class="flex justify-between font-bold text-emerald-900 dark:text-emerald-200">
                                <h5 class="text-s">Dean of Academic Affairs (DOAA)</h5>
                                <span class="font-bold text-xs {{ $pts1->doaa_approval ? 'text-emerald-600' : 'text-red-600' }}">
                                    {{ $pts1->doaa_approval ? '✓ Approved' : '❌ Not Approved' }}
                                </span>
                            </div>
                            <div class="text-xs text-gray-700 dark:text-gray-300">
                                <strong>Student Comment:</strong>
                                @if($pts1->doaa_student_comment)
                                    <p class="italic bg-white dark:bg-gray-800 p-2.5 rounded-lg border border-emerald-100 dark:border-emerald-900 mt-0.5">
                                        {{ $pts1->doaa_student_comment }}
                                    </p>
                                @else
                                    <div class="flex items-center space-x-2 text-xs text-gray-600 dark:text-gray-500 bg-gray-200/60 dark:bg-gray-900/40 p-2.5 rounded-lg border border-dashed border-gray-200 dark:border-gray-700/60 mt-0.5">
                                        <svg class="w-4 h-4 text-gray-400 dark:text-gray-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 4.418 9 8z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3l18 18"></path>
                                        </svg>
                                        <span class="italic font-normal">Student Comment not provided</span>
                                    </div>
                                @endif
                            </div>
                            <div class="text-xs text-gray-700 dark:text-gray-300 pt-0.5">
                                <strong>DOAA Remark:</strong>
                                @if($pts1->doaa_confidential_remark)
                                    <p class="italic bg-white dark:bg-gray-800 p-2.5 rounded-lg border border-emerald-100 dark:border-emerald-900 mt-0.5">
                                        {{ $pts1->doaa_confidential_remark }}
                                    </p>
                                @else
                                    <div class="flex items-center space-x-2 text-xs text-gray-600 dark:text-gray-500 bg-gray-200/60 dark:bg-gray-900/40 p-2.5 rounded-lg border border-dashed border-gray-200 dark:border-gray-700/60 mt-0.5">
                                        <svg class="w-4 h-4 text-gray-400 dark:text-gray-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 4.418 9 8z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3l18 18"></path>
                                        </svg>
                                        <span class="italic font-normal">Confidential Remark not provided</span>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            @php
                $showActionForm = false;
                $user = auth()->user();
                if ($pts1->current_stage === 'co_supervisors') {
                    for ($i = 1; $i <= 10; $i++) {
                        $idCol = "co_supervisor_{$i}_id";
                        $recCol = "co_supervisor_{$i}_recommendation";
                        if ($pts1->$idCol === $user->id && is_null($pts1->$recCol)) {
                            $showActionForm = true;
                            break;
                        }
                    }
                } elseif ($pts1->current_stage === 'pspc_members') {
                    for ($i = 1; $i <= 10; $i++) {
                        $idCol = "pspc_member_{$i}_id";
                        $recCol = "pspc_member_{$i}_recommendation";
                        if ($pts1->$idCol === $user->id && is_null($pts1->$recCol)) {
                            $showActionForm = true;
                            break;
                        }
                    }
                } elseif ($pts1->current_stage === 'dpgc') {
                    if ($user->isDpgc() && is_null($pts1->dpgc_recommendation)) $showActionForm = true;
                } elseif ($pts1->current_stage === 'hod') {
                    if ($user->isHod() && is_null($pts1->hod_recommendation)) $showActionForm = true;
                } elseif ($pts1->current_stage === 'section_officer') {
                    if ($user->isSectionOfficer() && is_null($pts1->section_officer_recommendation)) $showActionForm = true;
                } elseif ($pts1->current_stage === 'doaa') {
                    if (($user->isDoaa()) && is_null($pts1->doaa_approval)) $showActionForm = true;
                }
            @endphp

            @if($showActionForm)
                <!-- Section 6: Action Required (Evaluation & Decision) -->
                <form action="{{ route('pts1.endorse', $pts1->id) }}" method="POST" class="space-y-8">
                    @csrf

                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-indigo-100 dark:border-indigo-900/50 p-6 space-y-6">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white border-b border-indigo-100 dark:border-indigo-900/50 pb-2 flex items-center">
                            7. Endorsement Evaluation & Recommendation
                        </h3>

                        <!-- Item 1: Recommendation or Verification Status -->
                        @if($pts1->current_stage === 'section_officer')
                            <div class="space-y-4">
                                <label class="block font-bold text-gray-900 dark:text-white text-sm">
                                    Section Officer Verification: <span class="text-red-500">*</span>
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
                        @elseif ($pts1->current_stage ==='doaa')
                            <div class="space-y-4">
                                <label class="block font-bold text-gray-900 dark:text-white text-sm">
                                    Approval Status for Candidate Thesis Submission: <span class="text-red-500">*</span>
                                </label>

                                <div class="grid grid-cols-1 gap-4">
                                    <!-- Option (a) APPROVE -->
                                    <label class="p-4 rounded-xl border-2 transition cursor-pointer flex items-start space-x-3" :class="recommendation === '1' ? 'border-emerald-500 bg-emerald-50/50 dark:bg-emerald-950/30' : 'border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800'">
                                        <input type="radio" name="recommendation" value="1" required x-model="recommendation" class="mt-1 text-emerald-600 focus:ring-emerald-500">
                                        <div>
                                            <span class="block font-bold text-sm text-emerald-900 dark:text-emerald-300 uppercase tracking-wide">
                                                (a) APPROVE
                                            </span>
                                            <p class="text-xs text-gray-600 dark:text-gray-300 mt-1 leading-relaxed">
                                                Approve the candidate's PTS-1 submission.
                                            </p>
                                        </div>
                                    </label>

                                    <!-- Option (b) NOT RECOMMENDED -->
                                    <label class="p-4 rounded-xl border-2 transition cursor-pointer flex items-start space-x-3" :class="recommendation === '0' ? 'border-red-500 bg-red-50/50 dark:bg-red-950/30' : 'border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800'">
                                        <input type="radio" name="recommendation" value="0" required x-model="recommendation" class="mt-1 text-red-600 focus:ring-red-500">
                                        <div>
                                            <span class="block font-bold text-sm text-red-900 dark:text-red-300 uppercase tracking-wide">
                                                (b) DO NOT APPROVE
                                            </span>
                                            <p class="text-xs text-gray-600 dark:text-gray-300 mt-1 leading-relaxed">
                                                Do not approve the candidate's PTS-1 submission.
                                            </p>
                                        </div>
                                    </label>
                                </div>
                            </div>
                        @else
                            <div class="space-y-4">
                                <label class="block font-bold text-gray-900 dark:text-white text-sm">
                                    Recommendation Status for Candidate Thesis Submission: <span class="text-red-500">*</span>
                                </label>

                                <div class="grid grid-cols-1 gap-4">
                                    <!-- Option (a) RECOMMENDED -->
                                    <label class="p-4 rounded-xl border-2 transition cursor-pointer flex items-start space-x-3" :class="recommendation === '1' ? 'border-emerald-500 bg-emerald-50/50 dark:bg-emerald-950/30' : 'border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800'">
                                        <input type="radio" name="recommendation" value="1" required x-model="recommendation" class="mt-1 text-emerald-600 focus:ring-emerald-500">
                                        <div>
                                            <span class="block font-bold text-sm text-emerald-900 dark:text-emerald-300 uppercase tracking-wide">
                                                (a) RECOMMENDED
                                            </span>
                                            <p class="text-xs text-gray-600 dark:text-gray-300 mt-1 leading-relaxed">
                                                Recommend the candidate's PTS-1 submission for forwarding to the next stage in the academic pipeline.
                                            </p>
                                        </div>
                                    </label>

                                    <!-- Option (b) NOT RECOMMENDED -->
                                    <label class="p-4 rounded-xl border-2 transition cursor-pointer flex items-start space-x-3" :class="recommendation === '0' ? 'border-red-500 bg-red-50/50 dark:bg-red-950/30' : 'border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800'">
                                        <input type="radio" name="recommendation" value="0" required x-model="recommendation" class="mt-1 text-red-600 focus:ring-red-500">
                                        <div>
                                            <span class="block font-bold text-sm text-red-900 dark:text-red-300 uppercase tracking-wide">
                                                (b) NOT RECOMMENDED
                                            </span>
                                            <p class="text-xs text-gray-600 dark:text-gray-300 mt-1 leading-relaxed">
                                                Do not recommend the submission in its present form without further improvements.
                                            </p>
                                        </div>
                                    </label>
                                </div>
                            </div>
                        @endif
                        
                        <!-- Item 2: Dynamic Recommendation / Verification Remark -->
                        <div class="space-y-2 pt-2">
                            <label class="block font-bold text-gray-900 dark:text-white text-sm">
                                @if($pts1->current_stage === 'section_officer')
                                Verification Remark <span class="text-red-500">*</span>
                                @elseif($pts1->current_stage === 'doaa')
                                <span x-show="recommendation === '1'">Approval Remark (Optional)</span>
                                <span x-show="recommendation === '0'">Non-Approval Remark <span class="text-red-500">*</span></span>
                                @else
                                <span x-show="recommendation === '1'">Recommendation Remark (Optional)</span>
                                <span x-show="recommendation === '0'">Non-Recommendation Remark <span class="text-red-500">*</span></span>
                                @endif
                            </label>
                            @if($pts1->current_stage === 'section_officer')
                            <textarea name="confidential_remark" 
                            rows="3" 
                            required 
                            placeholder="Provide mandatory verification remarks" 
                            class="w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm"></textarea>
                            @elseif($pts1->current_stage === 'doaa')
                            <textarea name="confidential_remark" 
                            rows="3" 
                            :required="recommendation === '0'" 
                            :placeholder="recommendation === '1' ? 'Optional approval remarks' : 'Provide mandatory non-approval remarks'" 
                            class="w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm"></textarea>
                            @else
                            <textarea name="confidential_remark" 
                            rows="3" 
                            :required="recommendation === '0'" 
                            :placeholder="recommendation === '1' ? 'Optional recommendation remarks' : 'Provide mandatory non-recommendation remarks'" 
                            class="w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm"></textarea>
                            @endif
                        </div>

                        <!-- Item 3: Student Comments Textarea (For DPGC, HOD, DOAA) -->
                        @if(in_array($pts1->current_stage, ['dpgc', 'hod', 'doaa']))
                            <div class="space-y-2 pt-2">
                                <label class="block font-bold text-gray-900 dark:text-white text-sm">
                                    Student Comment (Optional)
                                </label>
                                <textarea name="student_comment" rows="3" placeholder="Provide optional comments or observations for the student" class="w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm"></textarea>
                            </div>
                        @endif 
                    </div>
                    
                    <!-- Submit & Revert Action Buttons Bar -->
                    <div class="flex items-center justify-end space-x-4 pt-4">
                        @if(!$user->isSectionOfficer())
                            <!-- Revert Button (Triggers Independent Pop-Up Modal) -->
                            <button type="button" 
                                    @click="showRevertModal = true" 
                                    class="bg-red-600 hover:bg-red-700 text-white text-base font-bold px-6 py-3 rounded-xl shadow-lg transition flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"></path>
                                </svg>
                                Revert Form
                            </button>
                        @endif

                        <!-- Main Submit Button (Forwards or Approves depending on stage) -->
                        @if($pts1->current_stage === 'section_officer')
                            <button type="submit" 
                                    :disabled="!isVerified" 
                                    :class="!isVerified ? 'bg-gray-400 opacity-50 cursor-not-allowed shadow-none' : 'bg-emerald-600 hover:bg-emerald-700 shadow-lg'"
                                    class="text-white text-base font-bold px-8 py-3 rounded-xl transition flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                                </svg>
                                Submit @if($pts1->current_stage !== 'doaa') &amp; Forward @endif
                            </button>
                        @else
                            <button type="submit" 
                                    class="bg-emerald-600 hover:bg-emerald-700 text-white text-base font-bold px-8 py-3 rounded-xl shadow-lg transition flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                                </svg>
                                Submit @if($pts1->current_stage !== 'doaa') &amp; Forward @endif
                            </button>
                        @endif
                    </div>
                </form>

                <!-- Revert Confirmation Pop-Up Modal -->
                <div x-show="showRevertModal" 
                     x-cloak 
                     class="fixed inset-0 z-50 overflow-y-auto bg-gray-900/60 backdrop-blur-sm flex items-center justify-center p-4"
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0"
                     x-transition:enter-end="opacity-100"
                     x-transition:leave="transition ease-in duration-150"
                     x-transition:leave-start="opacity-100"
                     x-transition:leave-end="opacity-0">

                    <div @click.away="showRevertModal = false" 
                         class="bg-white dark:bg-gray-800 rounded-2xl max-w-lg w-full p-6 shadow-2xl border border-gray-100 dark:border-gray-700 space-y-5"
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 scale-95"
                         x-transition:enter-end="opacity-100 scale-100">

                        <div class="flex items-center space-x-3 border-b border-gray-100 dark:border-gray-700 pb-3">
                            <div class="p-2.5 bg-amber-100 dark:bg-amber-900/40 rounded-xl text-amber-600 dark:text-amber-400">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-lg font-bold text-gray-900 dark:text-white">Revert PTS-1 Form to Student</h3>
                                <p class="text-xs text-gray-500 dark:text-gray-400">Send form back to candidate for required changes</p>
                            </div>
                        </div>

                        <!-- Independent Revert Form -->
                        <form action="{{ route('pts1.revert', $pts1->id) }}" method="POST" class="space-y-4">
                            @csrf

                            <div>
                                <label class="block text-sm font-bold text-gray-800 dark:text-gray-200 mb-1.5">
                                    Reversion Comment <span class="text-red-500">*</span>
                                </label>
                                <textarea name="reversion_comment" 
                                          required 
                                          rows="4" 
                                          placeholder="Provide clear reasons/instructions for the student regarding required modifications" 
                                          class="w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm focus:ring-2 focus:ring-amber-500"></textarea>
                            </div>

                            <div class="flex justify-end space-x-3 pt-2">
                                <button type="button" 
                                        @click="showRevertModal = false" 
                                        class="px-5 py-2.5 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200 font-bold text-sm rounded-xl transition">
                                    Cancel
                                </button>
                                <button type="submit" 
                                        class="px-5 py-2.5 bg-red-600 hover:bg-red-700 text-white font-bold text-sm rounded-xl shadow-md transition flex items-center">
                                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"></path>
                                    </svg>
                                    Confirm Revert
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            @endif

        </div>
    </div>
</x-app-layout>

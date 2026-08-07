<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-bold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('PTS-1 Review & Endorsement Portal') }}
            </h2>
            <a href="{{ route('dashboard') }}" class="px-4 py-1.5 bg-gray-600 hover:bg-gray-700 text-white font-bold text-xs rounded-lg shadow transition">
                &larr; Back to Dashboard
            </a>
        </div>
    </x-slot>

    <div class="py-8" x-data="{
        action: 'approve'
    }">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Scholar Profile Header Card -->
            <div class="bg-indigo-900 text-white rounded-xl p-6 shadow-sm space-y-3">
                <div class="flex justify-between items-start border-b border-indigo-700 pb-3">
                    <div>
                        <span class="text-xs uppercase text-indigo-300 font-bold">Ph.D. Scholar Profile</span>
                        <h3 class="text-2xl font-bold mt-0.5">{{ $scholarUser->name }}</h3>
                    </div>
                    <span class="bg-indigo-700 text-indigo-100 text-xs font-mono font-bold px-3 py-1 rounded-full border border-indigo-600">
                        Roll: {{ $student->roll_number }}
                    </span>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-xs pt-1">
                    <div>
                        <span class="text-indigo-300 block">Department:</span>
                        <strong class="text-white font-semibold text-sm">{{ $student->department->name ?? 'N/A' }}</strong>
                    </div>
                    <div>
                        <span class="text-indigo-300 block">Registration Date:</span>
                        <strong class="text-white font-semibold text-sm">{{ $student->date_registration }}</strong>
                    </div>
                    <div>
                        <span class="text-indigo-300 block">Confirmation Date:</span>
                        <strong class="text-white font-semibold text-sm">{{ $student->date_confirmation ?? 'N/A' }}</strong>
                    </div>
                </div>
            </div>

            <!-- PTS-1 Details & Document Inspection -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 space-y-6">
                
                <h4 class="font-bold text-base text-gray-900 dark:text-white border-b border-gray-100 dark:border-gray-700 pb-2">
                    1. PTS-1 Open Seminar & Submission Details
                </h4>

                <!-- Seminar Date, Time, Venue, Link -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 p-4 bg-gray-50 dark:bg-gray-700/40 rounded-xl border border-gray-200 dark:border-gray-600 text-xs space-y-2 md:space-y-0">
                    <div>
                        <span class="text-gray-500 block">Open Seminar Date & Time:</span>
                        <strong class="text-gray-900 dark:text-white font-bold text-sm">
                            {{ $pts1->seminar_date?->format('F d, Y') }} at {{ $pts1->seminar_time }}
                        </strong>
                    </div>
                    <div>
                        <span class="text-gray-500 block">Venue / Location:</span>
                        <strong class="text-gray-900 dark:text-white font-bold text-sm">{{ $pts1->seminar_venue }}</strong>
                    </div>
                    @if($pts1->meeting_link)
                        <div class="md:col-span-2 pt-2 border-t border-gray-200 dark:border-gray-600">
                            <span class="text-gray-500 block">Online Meeting Link:</span>
                            <a href="{{ $pts1->meeting_link }}" target="_blank" class="text-indigo-600 dark:text-indigo-400 font-bold hover:underline">
                                {{ $pts1->meeting_link }}
                            </a>
                        </div>
                    @endif
                </div>

                <!-- Document Inspection Links -->
                <div class="p-4 bg-gray-50 dark:bg-gray-700/40 rounded-xl border border-gray-200 dark:border-gray-600 space-y-3">
                    <span class="text-xs font-bold text-gray-700 dark:text-gray-300 uppercase block">Submitted Documents</span>
                    <div class="flex flex-wrap gap-3">
                        <a href="{{ route('pts.document.serve', ['pts1', $pts1->id, 'draft_synopsis_report_doc_path']) }}" target="_blank" class="px-3 py-1.5 bg-blue-100 text-blue-800 text-xs font-bold rounded-lg hover:bg-blue-200">
                            📄 Inspect Synopsis Report
                        </a>
                        <a href="{{ route('pts.document.serve', ['pts1', $pts1->id, 'publication_list_doc_path']) }}" target="_blank" class="px-3 py-1.5 bg-emerald-100 text-emerald-800 text-xs font-bold rounded-lg hover:bg-emerald-200">
                            📄 Inspect Publication List
                        </a>
                    </div>
                </div>

                <!-- Previous Endorsements Audit Trail -->
                <h4 class="font-bold text-base text-gray-900 dark:text-white border-b border-gray-100 dark:border-gray-700 pb-2 pt-2">
                    2. Previous Authority Endorsements & Comments Audit Trail
                </h4>

                <div class="space-y-4">
                    <!-- Main Supervisor Evaluation -->
                    <div class="p-4 bg-indigo-50/70 dark:bg-indigo-950/40 border-l-4 border-indigo-500 rounded-xl space-y-2">
                        <div class="flex items-center justify-between text-xs">
                            <span class="font-bold text-indigo-900 dark:text-indigo-200">
                                Main Supervisor Evaluation
                            </span>
                            <span class="px-2.5 py-0.5 bg-emerald-600 text-white font-bold rounded-full uppercase text-[10px]">
                                Work Status: {{ strtoupper($pts1->work_status) }}
                            </span>
                        </div>
                        <p class="text-xs italic text-gray-800 dark:text-gray-200 bg-white dark:bg-gray-800 p-3 rounded-lg border border-indigo-100 dark:border-indigo-900">
                            "{{ $pts1->main_supervisor_comment ?: $pts1->additional_comments }}"
                        </p>
                    </div>

                    <!-- Co-Supervisors Endorsements -->
                    @if($coSupervisor1 && $pts1->co_supervisor_1_endorsement || $coSupervisor2 && $pts1->co_supervisor_2_endorsement || $coSupervisor3 && $pts1->co_supervisor_3_endorsement)
                        <div class="p-4 bg-blue-50/70 dark:bg-blue-950/40 border-l-4 border-blue-500 rounded-xl space-y-3">
                            <h5 class="text-xs font-bold text-blue-900 dark:text-blue-200">Co-Supervisor Endorsements</h5>
                            
                            @if($coSupervisor1 && $pts1->co_supervisor_1_endorsement)
                                <div class="text-xs space-y-1">
                                    <div class="flex justify-between font-semibold">
                                        <span>{{ $coSupervisor1->name }} (Co-Supervisor 1):</span>
                                        @if($pts1->co_supervisor_1_endorsement)
                                            <span class="text-emerald-600 font-bold">✓ Endorsed</span>
                                        @else
                                            <span class="text-amber-600 font-bold">Pending</span>
                                        @endif
                                    </div>
                                    @if($pts1->co_supervisor_1_comment)
                                        <p class="italic text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 p-2 rounded border border-blue-100 dark:border-blue-900">
                                            "{{ $pts1->co_supervisor_1_comment }}"
                                        </p>
                                    @endif
                                </div>
                            @endif

                            @if($coSupervisor2 && $pts1->co_supervisor_2_endorsement)
                                <div class="text-xs space-y-1 pt-1 border-t border-blue-100 dark:border-blue-900">
                                    <div class="flex justify-between font-semibold">
                                        <span>{{ $coSupervisor2->name }} (Co-Supervisor 2):</span>
                                        @if($pts1->co_supervisor_2_endorsement)
                                            <span class="text-emerald-600 font-bold">✓ Endorsed</span>
                                        @else
                                            <span class="text-amber-600 font-bold">Pending</span>
                                        @endif
                                    </div>
                                    @if($pts1->co_supervisor_2_comment)
                                        <p class="italic text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 p-2 rounded border border-blue-100 dark:border-blue-900">
                                            "{{ $pts1->co_supervisor_2_comment }}"
                                        </p>
                                    @endif
                                </div>
                            @endif

                            @if($coSupervisor3 && $pts1->co_supervisor_3_endorsement)
                                <div class="text-xs space-y-1 pt-1 border-t border-blue-100 dark:border-blue-900">
                                    <div class="flex justify-between font-semibold">
                                        <span>{{ $coSupervisor3->name }} (Co-Supervisor 3):</span>
                                        @if($pts1->co_supervisor_3_endorsement)
                                            <span class="text-emerald-600 font-bold">✓ Endorsed</span>
                                        @else
                                            <span class="text-amber-600 font-bold">Pending</span>
                                        @endif
                                    </div>
                                    @if($pts1->co_supervisor_3_comment)
                                        <p class="italic text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 p-2 rounded border border-blue-100 dark:border-blue-900">
                                            "{{ $pts1->co_supervisor_3_comment }}"
                                        </p>
                                    @endif
                                </div>
                            @endif
                        </div>
                    @endif

                    <!-- PSPC Committee Endorsements -->
                    @if($pspc1 && $pts1->pspc_member_1_endorsement || $pspc2 && $pts1->pspc_member_2_endorsement || $pspc3 && $pts1->pspc_member_3_endorsement)
                        <div class="p-4 bg-purple-50/70 dark:bg-purple-950/40 border-l-4 border-purple-500 rounded-xl space-y-3">
                            <h5 class="text-xs font-bold text-purple-900 dark:text-purple-200">PSPC Committee Endorsements</h5>
                            
                            @if($pspc1 && $pts1->pspc_member_1_endorsement)
                                <div class="text-xs space-y-1">
                                    <div class="flex justify-between font-semibold">
                                        <span>{{ $pspc1->name }} (PSPC Member 1):</span>
                                        @if($pts1->pspc_member_1_endorsement)
                                            <span class="text-emerald-600 font-bold">✓ Endorsed</span>
                                        @else
                                            <span class="text-amber-600 font-bold">Pending</span>
                                        @endif
                                    </div>
                                    @if($pts1->pspc_member_1_comment)
                                        <p class="italic text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 p-2 rounded border border-purple-100 dark:border-purple-900">
                                            "{{ $pts1->pspc_member_1_comment }}"
                                        </p>
                                    @endif
                                </div>
                            @endif

                            @if($pspc2 && $pts1->pspc_member_2_endorsement)
                                <div class="text-xs space-y-1 pt-1 border-t border-purple-100 dark:border-purple-900">
                                    <div class="flex justify-between font-semibold">
                                        <span>{{ $pspc2->name }} (PSPC Member 2):</span>
                                        @if($pts1->pspc_member_2_endorsement)
                                            <span class="text-emerald-600 font-bold">✓ Endorsed</span>
                                        @else
                                            <span class="text-amber-600 font-bold">Pending</span>
                                        @endif
                                    </div>
                                    @if($pts1->pspc_member_2_comment)
                                        <p class="italic text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 p-2 rounded border border-purple-100 dark:border-purple-900">
                                            "{{ $pts1->pspc_member_2_comment }}"
                                        </p>
                                    @endif
                                </div>
                            @endif

                            @if($pspc3 && $pts1->pspc_member_3_endorsement)
                                <div class="text-xs space-y-1 pt-1 border-t border-purple-100 dark:border-purple-900">
                                    <div class="flex justify-between font-semibold">
                                        <span>{{ $pspc3->name }} (PSPC Member 3):</span>
                                        @if($pts1->pspc_member_3_endorsement)
                                            <span class="text-emerald-600 font-bold">✓ Endorsed</span>
                                        @else
                                            <span class="text-amber-600 font-bold">Pending</span>
                                        @endif
                                    </div>
                                    @if($pts1->pspc_member_3_comment)
                                        <p class="italic text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 p-2 rounded border border-purple-100 dark:border-purple-900">
                                            "{{ $pts1->pspc_member_3_comment }}"
                                        </p>
                                    @endif
                                </div>
                            @endif
                        </div>
                    @endif

                    <!-- DPGC Endorsement -->
                    @if($pts1->dpgc_endorsement)
                        <div class="p-4 bg-teal-50/70 dark:bg-teal-950/40 border-l-4 border-teal-500 rounded-xl space-y-2 text-xs">
                            <div class="flex justify-between font-bold text-teal-900 dark:text-teal-200">
                                <span>Department Postgraduate Committee (DPGC):</span>
                                <span class="text-emerald-600">✓ Endorsed</span>
                            </div>
                            @if($pts1->dpgc_comment)
                                <p class="italic text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 p-2 rounded border border-teal-100 dark:border-teal-900">
                                    "{{ $pts1->dpgc_comment }}"
                                </p>
                            @endif
                        </div>
                    @endif

                    <!-- HOD Endorsement -->
                    @if($pts1->hod_endorsement)
                        <div class="p-4 bg-amber-50/70 dark:bg-amber-950/40 border-l-4 border-amber-500 rounded-xl space-y-2 text-xs">
                            <div class="flex justify-between font-bold text-amber-900 dark:text-amber-200">
                                <span>Head of Department (HOD):</span>
                                <span class="text-emerald-600">✓ Endorsed</span>
                            </div>
                            @if($pts1->hod_comment)
                                <p class="italic text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 p-2 rounded border border-amber-100 dark:border-amber-900">
                                    "{{ $pts1->hod_comment }}"
                                </p>
                            @endif
                        </div>
                    @endif

                    <!-- Section Officer Endorsement -->
                    @if($pts1->section_officer_endorsement)
                        <div class="p-4 bg-rose-50/70 dark:bg-rose-950/40 border-l-4 border-rose-500 rounded-xl space-y-2 text-xs">
                            <div class="flex justify-between font-bold text-rose-900 dark:text-rose-200">
                                <span>Academic Section Officer:</span>
                                <span class="text-emerald-600">✓ Endorsed</span>
                            </div>
                            @if($pts1->section_officer_comment)
                                <p class="italic text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 p-2 rounded border border-rose-100 dark:border-rose-900">
                                    "{{ $pts1->section_officer_comment }}"
                                </p>
                            @endif
                        </div>
                    @endif
                </div>

                @php
                    $showActionForm = false;
                    $user = auth()->user();
                    if ($pts1->current_stage === 'co_supervisors') {
                        if ($pts1->co_supervisor_1_id === $user->id && !$pts1->co_supervisor_1_endorsement) $showActionForm = true;
                        if ($pts1->co_supervisor_2_id === $user->id && !$pts1->co_supervisor_2_endorsement) $showActionForm = true;
                        if ($pts1->co_supervisor_3_id === $user->id && !$pts1->co_supervisor_3_endorsement) $showActionForm = true;
                    } elseif ($pts1->current_stage === 'pspc_members') {
                        if ($pts1->pspc_member_1_id === $user->id && !$pts1->pspc_member_1_endorsement) $showActionForm = true;
                        if ($pts1->pspc_member_2_id === $user->id && !$pts1->pspc_member_2_endorsement) $showActionForm = true;
                        if ($pts1->pspc_member_3_id === $user->id && !$pts1->pspc_member_3_endorsement) $showActionForm = true;
                    } elseif ($pts1->current_stage === 'dpgc') {
                        if ($user->role === 'dpgc' && !$pts1->dpgc_endorsement) $showActionForm = true;
                    } elseif ($pts1->current_stage === 'hod') {
                        if ($user->role === 'hod' && !$pts1->hod_endorsement) $showActionForm = true;
                    } elseif ($pts1->current_stage === 'section_officer') {
                        if ($user->role === 'section_officer' && !$pts1->section_officer_endorsement) $showActionForm = true;
                    } elseif ($pts1->current_stage === 'doaa') {
                        if (in_array($user->role, ['doaa', 'adoaa', 'senate_chairperson', 'ar_academic']) && !$pts1->doaa_endorsement) $showActionForm = true;
                    }
                @endphp

                @if($showActionForm)
                    <!-- Authority Action Form Section -->
                    <h4 class="font-bold text-base text-gray-900 dark:text-white border-b border-gray-100 dark:border-gray-700 pb-2 pt-4">
                        3. Action Required: Evaluate, Endorse or Revert
                    </h4>

                    <form x-bind:action="action === 'approve' ? '{{ route('pts1.endorse', $pts1->id) }}' : '{{ route('pts1.revert', $pts1->id) }}'" method="POST" class="space-y-6">
                        @csrf

                        <!-- Action Selection Cards -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Approve Card -->
                            <div @click="action = 'approve'" :class="action === 'approve' ? 'border-emerald-500 bg-emerald-50/40 dark:bg-emerald-950/20 ring-2 ring-emerald-500' : 'border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800'" class="p-4 rounded-xl border cursor-pointer transition space-y-2">
                                <div class="flex items-center space-x-2">
                                    <input type="radio" name="action" value="approve" x-model="action" class="text-emerald-600 focus:ring-emerald-500">
                                    <span class="font-bold text-sm text-emerald-900 dark:text-emerald-300">
                                        {{ auth()->user()->role === 'section_officer' ? '✓ Forward' : '✓ Approve & Endorse' }}
                                    </span>
                                </div>
                                <p class="text-xs text-gray-500">
                                    {{ auth()->user()->role === 'section_officer' ? 'Forward PTS-1 form to the next stage in the academic pipeline.' : 'Endorse PTS-1 form and forward to the next stage in the academic pipeline.' }}
                                </p>
                            </div>
                            @if(!$sectionofficer)
                            <!-- Revert Card -->
                            <div @click="action = 'revert'" :class="action === 'revert' ? 'border-red-500 bg-red-50/40 dark:bg-red-950/20 ring-2 ring-red-500' : 'border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800'" class="p-4 rounded-xl border cursor-pointer transition space-y-2">
                                <div class="flex items-center space-x-2">
                                    <input type="radio" name="action" value="revert" x-model="action" class="text-red-600 focus:ring-red-500">
                                    <span class="font-bold text-sm text-red-900 dark:text-red-300">⚠️ Revert Back to Scholar</span>
                                </div>
                                <p class="text-xs text-gray-500">Revert form back to student with comments for necessary modifications.</p>
                            </div>
                            @endif
                        </div>

                        <!-- Comment Input (Required if Revert selected) -->
                        <div>
                            <label for="comment" class="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase mb-1">
                                Authority Comments <span x-show="action === 'revert'" class="text-red-500">* (Required for Reversion)</span>
                            </label>
                            <textarea name="comment" id="comment" rows="3" :required="action === 'revert'"
                                placeholder="Enter endorsement observations or reversion comments..."
                                class="w-full text-sm rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white focus:ring-indigo-500 focus:border-indigo-500"></textarea>
                        </div>

                        <!-- Submit Button -->
                        <div class="pt-4 border-t border-gray-100 dark:border-gray-700 flex justify-end">
                            <button type="submit" :class="action === 'approve' ? 'bg-emerald-600 hover:bg-emerald-700' : 'bg-red-600 hover:bg-red-700'" class="px-6 py-2.5 text-white font-bold text-sm rounded-xl shadow transition">
                                Submit Decision &rarr;
                            </button>
                        </div>
                    </form>
                @endif

            </div>

        </div>
    </div>
</x-app-layout>

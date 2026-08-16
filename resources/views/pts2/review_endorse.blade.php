<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-bold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('PTS-2 (Synopsis Report) Review & Endorsement Portal') }}
            </h2>
            <a href="{{ route('dashboard') }}" class="px-4 py-1.5 bg-gray-600 hover:bg-gray-700 text-white font-bold text-xs rounded-lg shadow transition">
                &larr; Back to Dashboard
            </a>
        </div>
    </x-slot>

    <div class="py-8" x-data="{
        action: 'approve'
    }">
        <div class="max-w-4xl mx-auto px-2 sm:px-6 lg:px-8 space-y-6">

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

            <!-- Student Profile Header Card -->
            <div class="bg-purple-900 text-white rounded-xl p-6 shadow-sm space-y-3">
                <div class="flex justify-between items-start border-b border-purple-700 pb-3">
                    <div>
                        <span class="text-xs uppercase text-purple-300 font-bold">PhD Student Profile</span>
                        <h3 class="text-2xl font-bold mt-0.5">{{ $studentUser->name }}</h3>
                    </div>
                    <span class="bg-purple-700 text-purple-100 text-xs font-semibold font-bold px-3 py-1 rounded-full border border-purple-600">
                        Roll: {{ $student->roll_number }}
                    </span>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-xs pt-1">
                    <div>
                        <span class="text-purple-300 block">Department:</span>
                        <strong class="text-white font-semibold text-sm">{{ $student->department->name ?? 'N/A' }}</strong>
                    </div>
                    <div>
                        <span class="text-purple-300 block">Registration Date:</span>
                        <strong class="text-white font-semibold text-sm">{{ $student->date_registration ? \Carbon\Carbon::parse($student->date_registration)->format('d-m-Y') : 'N/A' }}</strong>
                    </div>
                    <div>
                        <span class="text-purple-300 block">Thesis Status:</span>
                        <strong class="text-white font-semibold text-sm">{{ $thesis->current_status ?? 'In Progress' }}</strong>
                    </div>
                </div>
            </div>

            <!-- PTS-2 Details & Document Inspection -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 space-y-6">
                
                <h4 class="font-bold text-base text-gray-900 dark:text-white border-b border-gray-100 dark:border-gray-700 pb-2">
                    1. Synopsis Report Submission Details
                </h4>

                <div class="p-4 bg-gray-50 dark:bg-gray-700/40 rounded-xl border border-gray-200 dark:border-gray-600 space-y-3">
                    <div>
                        <span class="text-xs text-gray-500 block">Synopsis Title:</span>
                        <strong class="text-base text-gray-900 dark:text-white font-bold">{{ $pts2->thesis->title }}</strong>
                    </div>
                    @if($pts2->remarks)
                        <div>
                            <span class="text-xs text-gray-500 block">Student Remarks:</span>
                            <p class="text-xs text-gray-700 dark:text-gray-300 italic">{{ $pts2->remarks }}</p>
                        </div>
                    @endif
                        <a href="{{ route('pts.document.serve', ['pts2', $pts2->id, 'synopsis_doc_path']) }}" target="_blank" class="inline-block px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white text-xs font-bold rounded-lg shadow">
                            📄 Inspect Synopsis Report Document
                        </a>
                    </div>
                </div>

                <!-- Previous Endorsements Audit Trail -->
                <h4 class="font-bold text-base text-gray-900 dark:text-white border-b border-gray-100 dark:border-gray-700 pb-2 pt-2">
                    2. Previous Authority Endorsements & Comments Audit Trail
                </h4>

                <div class="space-y-4">
                    <!-- Main Supervisor Evaluation -->
                    <div class="p-4 bg-purple-50/70 dark:bg-purple-950/40 border-l-4 border-purple-500 rounded-xl space-y-2">
                        <div class="flex items-center justify-between text-xs">
                            <span class="font-bold text-purple-900 dark:text-purple-200">
                                Main Supervisor Evaluation
                            </span>
                            <span class="px-2.5 py-0.5 bg-emerald-600 text-white font-bold rounded-full uppercase text-[10px]">
                                Approved
                            </span>
                        </div>
                        <p class="text-xs italic text-gray-800 dark:text-gray-200 bg-white dark:bg-gray-800 p-3 rounded-lg border border-purple-100 dark:border-purple-900">
                            {{ $pts2->main_supervisor_confidential_remark ?: 'Remark not provided' }}
                        </p>
                    </div>

                    <!-- Co-Supervisors Endorsements -->
                    @if(($coSupervisor1 && $pts2->co_supervisor_1_recommendation) || ($coSupervisor2 && $pts2->co_supervisor_2_recommendation) || ($coSupervisor3 && $pts2->co_supervisor_3_recommendation))
                        <div class="p-4 bg-blue-50/70 dark:bg-blue-950/40 border-l-4 border-blue-500 rounded-xl space-y-3">
                            <h5 class="text-xs font-bold text-blue-900 dark:text-blue-200">Co-Supervisor Endorsements</h5>
                            
                            @if($coSupervisor1 && $pts2->co_supervisor_1_recommendation)
                                <div class="text-xs space-y-1">
                                    <div class="flex justify-between font-semibold">
                                        <span>{{ $coSupervisor1->name }} (Co-Supervisor 1):</span>
                                        <span class="text-emerald-600 font-bold">✓ Endorsed</span>
                                    </div>
                                    <p class="italic text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 p-2 rounded border border-blue-100 dark:border-blue-900">
                                        {{ $pts2->co_supervisor_1_confidential_remark ?: 'Remark not provided' }}
                                    </p>
                                </div>
                            @endif

                            @if($coSupervisor2 && $pts2->co_supervisor_2_recommendation)
                                <div class="text-xs space-y-1 pt-1 border-t border-blue-100 dark:border-blue-900">
                                    <div class="flex justify-between font-semibold">
                                        <span>{{ $coSupervisor2->name }} (Co-Supervisor 2):</span>
                                        <span class="text-emerald-600 font-bold">✓ Endorsed</span>
                                    </div>
                                    <p class="italic text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 p-2 rounded border border-blue-100 dark:border-blue-900">
                                        {{ $pts2->co_supervisor_2_confidential_remark ?: 'Remark not provided' }}
                                    </p>
                                </div>
                            @endif

                            @if($coSupervisor3 && $pts2->co_supervisor_3_recommendation)
                                <div class="text-xs space-y-1 pt-1 border-t border-blue-100 dark:border-blue-900">
                                    <div class="flex justify-between font-semibold">
                                        <span>{{ $coSupervisor3->name }} (Co-Supervisor 3):</span>
                                        <span class="text-emerald-600 font-bold">✓ Endorsed</span>
                                    </div>
                                    <p class="italic text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 p-2 rounded border border-blue-100 dark:border-blue-900">
                                        {{ $pts2->co_supervisor_3_confidential_remark ?: 'Remark not provided' }}
                                    </p>
                                </div>
                            @endif
                        </div>
                    @endif

                    <!-- PSPC Committee Endorsements -->
                    @if(($pspc1 && $pts2->pspc_member_1_recommendation) || ($pspc2 && $pts2->pspc_member_2_recommendation) || ($pspc3 && $pts2->pspc_member_3_recommendation))
                        <div class="p-4 bg-purple-50/70 dark:bg-purple-950/40 border-l-4 border-purple-500 rounded-xl space-y-3">
                            <h5 class="text-xs font-bold text-purple-900 dark:text-purple-200">PSPC Committee Endorsements</h5>
                            
                            @if($pspc1 && $pts2->pspc_member_1_recommendation)
                                <div class="text-xs space-y-1">
                                    <div class="flex justify-between font-semibold">
                                        <span>{{ $pspc1->name }} (PSPC Member 1):</span>
                                        <span class="text-emerald-600 font-bold">✓ Endorsed</span>
                                    </div>
                                    <p class="italic text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 p-2 rounded border border-purple-100 dark:border-purple-900">
                                        {{ $pts2->pspc_member_1_confidential_remark ?: 'Remark not provided' }}
                                    </p>
                                </div>
                            @endif

                            @if($pspc2 && $pts2->pspc_member_2_recommendation)
                                <div class="text-xs space-y-1 pt-1 border-t border-purple-100 dark:border-purple-900">
                                    <div class="flex justify-between font-semibold">
                                        <span>{{ $pspc2->name }} (PSPC Member 2):</span>
                                        <span class="text-emerald-600 font-bold">✓ Endorsed</span>
                                    </div>
                                    <p class="italic text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 p-2 rounded border border-purple-100 dark:border-purple-900">
                                        {{ $pts2->pspc_member_2_confidential_remark ?: 'Remark not provided' }}
                                    </p>
                                </div>
                            @endif

                            @if($pspc3 && $pts2->pspc_member_3_recommendation)
                                <div class="text-xs space-y-1 pt-1 border-t border-purple-100 dark:border-purple-900">
                                    <div class="flex justify-between font-semibold">
                                        <span>{{ $pspc3->name }} (PSPC Member 3):</span>
                                        <span class="text-emerald-600 font-bold">✓ Endorsed</span>
                                    </div>
                                    <p class="italic text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 p-2 rounded border border-purple-100 dark:border-purple-900">
                                        {{ $pts2->pspc_member_3_confidential_remark ?: 'Remark not provided' }}
                                    </p>
                                </div>
                            @endif
                        </div>
                    @endif

                    <!-- DPGC Endorsement -->
                    @if($pts2->dpgc_recommendation)
                        <div class="p-4 bg-teal-50/70 dark:bg-teal-950/40 border-l-4 border-teal-500 rounded-xl space-y-2 text-xs">
                            <div class="flex justify-between font-bold text-teal-900 dark:text-teal-200">
                                <span>Department Postgraduate Committee (DPGC):</span>
                                <span class="text-emerald-600">✓ Endorsed</span>
                            </div>
                            <p class="italic text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 p-2 rounded border border-teal-100 dark:border-teal-900">
                                {{ $pts2->dpgc_confidential_remark ?: 'Remark not provided' }}
                            </p>
                        </div>
                    @endif

                    <!-- HOD Endorsement -->
                    @if($pts2->hod_recommendation)
                        <div class="p-4 bg-amber-50/70 dark:bg-amber-950/40 border-l-4 border-amber-500 rounded-xl space-y-2 text-xs">
                            <div class="flex justify-between font-bold text-amber-900 dark:text-amber-200">
                                <span>Head of Department (HOD):</span>
                                <span class="text-emerald-600">✓ Endorsed</span>
                            </div>
                            <p class="italic text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 p-2 rounded border border-amber-100 dark:border-amber-900">
                                {{ $pts2->hod_confidential_remark ?: 'Remark not provided' }}
                            </p>
                        </div>
                    @endif

                    <!-- Section Officer Endorsement -->
                    @if($pts2->section_officer_recommendation)
                        <div class="p-4 bg-rose-50/70 dark:bg-rose-950/40 border-l-4 border-rose-500 rounded-xl space-y-2 text-xs">
                            <div class="flex justify-between font-bold text-rose-900 dark:text-rose-200">
                                <span>Academic Section Officer:</span>
                                <span class="text-emerald-600">✓ Endorsed</span>
                            </div>
                            <p class="italic text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 p-2 rounded border border-rose-100 dark:border-rose-900">
                                {{ $pts2->section_officer_confidential_remark ?: 'Remark not provided' }}
                            </p>
                        </div>
                    @endif
                </div>

                @php
                    $showActionForm = false;
                    $user = auth()->user();
                    if ($pts2->current_stage === 'co_supervisors') {
                        for ($i = 1; $i <= 10; $i++) {
                            $idCol = "co_supervisor_{$i}_id";
                            $recCol = "co_supervisor_{$i}_recommendation";
                            if ($pts2->$idCol === $user->id && is_null($pts2->$recCol)) {
                                $showActionForm = true;
                                break;
                            }
                        }
                    } elseif ($pts2->current_stage === 'pspc_members') {
                        for ($i = 1; $i <= 10; $i++) {
                            $idCol = "pspc_member_{$i}_id";
                            $recCol = "pspc_member_{$i}_recommendation";
                            if ($pts2->$idCol === $user->id && is_null($pts2->$recCol)) {
                                $showActionForm = true;
                                break;
                            }
                        }
                    } elseif ($pts2->current_stage === 'dpgc') {
                        if ($user->isDpgc() && is_null($pts2->dpgc_recommendation)) $showActionForm = true;
                    } elseif ($pts2->current_stage === 'hod') {
                        if ($user->isHod() && is_null($pts2->hod_recommendation)) $showActionForm = true;
                    } elseif ($pts2->current_stage === 'section_officer') {
                        if ($user->isSectionOfficer() && is_null($pts2->section_officer_recommendation)) $showActionForm = true;
                    } elseif ($pts2->current_stage === 'doaa') {
                        if (($user->isDoaa() || $user->isAdoaa() || $user->isSenateChairperson() || $user->isArAcademic()) && is_null($pts2->doaa_approval)) $showActionForm = true;
                    }
                @endphp

                @if($showActionForm)
                    <!-- Authority Action Form Section -->
                    <h4 class="font-bold text-base text-gray-900 dark:text-white border-b border-gray-100 dark:border-gray-700 pb-2 pt-4">
                        3. Action Required: Evaluate, Endorse or Revert
                    </h4>

                    <form x-bind:action="action === 'approve' ? '{{ route('pts2.endorse', $pts2->id) }}' : '{{ route('pts2.revert', $pts2->id) }}'" method="POST" class="space-y-6">
                        @csrf

                        <!-- Action Selection Cards -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Approve Card -->
                            <div @click="action = 'approve'" :class="action === 'approve' ? 'border-emerald-500 bg-emerald-50/40 dark:bg-emerald-950/20 ring-2 ring-emerald-500' : 'border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800'" class="p-4 rounded-xl border cursor-pointer transition space-y-2">
                                <div class="flex items-center space-x-2">
                                    <input type="radio" name="action" value="approve" x-model="action" class="text-emerald-600 focus:ring-emerald-500">
                                    <span class="font-bold text-sm text-emerald-900 dark:text-emerald-300">
                                        {{ $user->isSectionOfficer() ? '✓ Forward' : '✓ Approve & Endorse' }}
                                    </span>
                                </div>
                                <p class="text-xs text-gray-500">
                                    {{ $user->isSectionOfficer() ? 'Forward PTS-2 Synopsis form to the next stage in the academic pipeline.' : 'Endorse PTS-2 Synopsis form and forward to the next stage in the academic pipeline.' }}
                                </p>
                            </div>

                            @if(!$user->isSectionOfficer())
                                <!-- Revert Card -->
                                <div @click="action = 'revert'" :class="action === 'revert' ? 'border-red-500 bg-red-50/40 dark:bg-red-950/20 ring-2 ring-red-500' : 'border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800'" class="p-4 rounded-xl border cursor-pointer transition space-y-2">
                                    <div class="flex items-center space-x-2">
                                        <input type="radio" name="action" value="revert" x-model="action" class="text-red-600 focus:ring-red-500">
                                        <span class="font-bold text-sm text-red-900 dark:text-red-300">⚠️ Revert Back to Student</span>
                                    </div>
                                    <p class="text-xs text-gray-500">Revert synopsis form back to student with comments for necessary modifications.</p>
                                </div>
                            @endif
                        </div>

                        <!-- Comment Input (Required if Revert selected) -->
                        <div>
                            <label for="comment" class="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase mb-1">
                                Authority Comments <span x-show="action === 'revert'" class="text-red-500">* (Required for Reversion)</span>
                            </label>
                            <textarea :name="action === 'revert' ? 'reversion_comment' : 'comment'" id="comment" rows="3" :required="action === 'revert'"
                                placeholder="Enter endorsement observations or reversion comments..."
                                class="w-full text-sm rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white focus:ring-purple-500 focus:border-purple-500"></textarea>
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

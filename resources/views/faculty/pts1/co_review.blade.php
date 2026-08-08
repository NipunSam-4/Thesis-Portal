<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-bold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Co-Supervisor PTS-1 Review & Endorsement') }}
            </h2>
            <a href="{{ route('faculty.dashboard') }}" class="px-4 py-1.5 bg-gray-600 hover:bg-gray-700 text-white font-bold text-xs rounded-lg shadow transition">
                &larr; Back to Dashboard
            </a>
        </div>
    </x-slot>

    <div class="py-8" x-data="{
        action: 'approve'
    }">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Read-Only Student Profile -->
            <div class="bg-indigo-900 text-white rounded-xl p-6 shadow-sm space-y-3">
                <div class="flex justify-between items-start border-b border-indigo-700 pb-3">
                    <div>
                        <span class="text-xs uppercase text-indigo-300 font-bold">Ph.D. Student Profile</span>
                        <h3 class="text-2xl font-bold mt-0.5">{{ $studentUser->name }}</h3>
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

            <!-- PTS-1 Details & Main Supervisor Evaluation Card -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 space-y-6">
                
                <h4 class="font-bold text-base text-gray-900 dark:text-white border-b border-gray-100 dark:border-gray-700 pb-2">
                    1. PTS-1 Open Seminar & Submission Details
                </h4>

                <!-- Seminar Date, Time, Venue, Meeting Link -->
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

                <!-- Submitted Files Inspection -->
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

                <h4 class="font-bold text-base text-gray-900 dark:text-white border-b border-gray-100 dark:border-gray-700 pb-2 pt-2">
                    2. Main Supervisor Evaluation Summary
                </h4>

                <div class="p-4 bg-indigo-50 dark:bg-indigo-950/40 border-l-4 border-indigo-500 rounded-xl space-y-3">
                    <div class="flex items-center justify-between text-xs">
                        <span class="px-3 py-0.5 bg-emerald-600 text-white font-bold rounded-full uppercase text-[10px]">
                            Work Status: {{ strtoupper($pts1->work_status) }}
                        </span>
                    </div>

                    <div>
                        <span class="text-xs font-bold text-indigo-900 dark:text-indigo-200 block mb-1">Supervisor Comments & Observations:</span>
                        <div class="p-3 bg-white dark:bg-gray-800 rounded-lg border border-indigo-200 dark:border-indigo-900 text-xs italic text-gray-800 dark:text-gray-200">
                            "{{ $pts1->main_supervisor_confidential_remark ?: $pts1->main_supervisor_student_comment }}"
                        </div>
                    </div>
                </div>

                <h4 class="font-bold text-base text-gray-900 dark:text-white border-b border-gray-100 dark:border-gray-700 pb-2 pt-2">
                    3. Co-Supervisor Evaluation & Decision
                </h4>

                <form action="{{ route('faculty.pts1.co_update', $pts1->id) }}" method="POST" class="space-y-6">
                    @csrf
                    @method('PUT')

                    <!-- Action Radio Cards -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Approve Card -->
                        <div @click="action = 'approve'" :class="action === 'approve' ? 'border-emerald-500 bg-emerald-50/40 dark:bg-emerald-950/20 ring-2 ring-emerald-500' : 'border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800'" class="p-4 rounded-xl border cursor-pointer transition space-y-2">
                            <div class="flex items-center space-x-2">
                                <input type="radio" name="action" value="approve" x-model="action" class="text-emerald-600 focus:ring-emerald-500">
                                <span class="font-bold text-sm text-emerald-900 dark:text-emerald-300">✓ Approve & Endorse</span>
                            </div>
                            <p class="text-xs text-gray-500">Endorse PTS-1 form and forward to PSPC Committee / DPGC Convenor.</p>
                        </div>

                        <!-- Revert Card -->
                        <div @click="action = 'revert'" :class="action === 'revert' ? 'border-red-500 bg-red-50/40 dark:bg-red-950/20 ring-2 ring-red-500' : 'border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800'" class="p-4 rounded-xl border cursor-pointer transition space-y-2">
                            <div class="flex items-center space-x-2">
                                <input type="radio" name="action" value="revert" x-model="action" class="text-red-600 focus:ring-red-500">
                                <span class="font-bold text-sm text-red-900 dark:text-red-300">⚠️ Revert Back to Student</span>
                            </div>
                            <p class="text-xs text-gray-500">Revert form back to student with feedback comments for modifications.</p>
                        </div>
                    </div>

                    <!-- Comment Input (Required if Revert selected) -->
                    <div>
                        <label for="comment" class="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase mb-1">
                            Co-Supervisor Evaluation Comments <span x-show="action === 'revert'" class="text-red-500">* (Required for Reversion)</span>
                        </label>
                        <textarea name="comment" id="comment" rows="3" :required="action === 'revert'"
                            placeholder="Enter endorsement observations or reversion comments..."
                            class="w-full text-sm rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white focus:ring-indigo-500 focus:border-indigo-500"></textarea>
                    </div>

                    <!-- Submit Button -->
                    <div class="pt-4 border-t border-gray-100 dark:border-gray-700 flex justify-end">
                        <button type="submit" :class="action === 'approve' ? 'bg-emerald-600 hover:bg-emerald-700' : 'bg-red-600 hover:bg-red-700'" class="px-6 py-2.5 text-white font-bold text-sm rounded-xl shadow transition">
                            Submit Co-Supervisor Decision &rarr;
                        </button>
                    </div>
                </form>
            </div>

        </div>
    </div>
</x-app-layout>

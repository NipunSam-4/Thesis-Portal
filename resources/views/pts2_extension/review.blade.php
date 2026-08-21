<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-bold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('PTS-2 Extension Evaluation Portal') }}
            </h2>
            <x-back-to-dashboard-button />
        </div>
    </x-slot>

    <div class="py-6" x-data="pts2ExtensionReviewForm()">
        <div class="max-w-5xl mx-auto px-2 sm:px-6 lg:px-8 space-y-6">

            <!-- Flash Session Alerts -->
            @if(session('success'))
                <div x-data="{ show: true }" x-show="show" x-transition class="p-4 bg-emerald-100 dark:bg-emerald-900/40 border-l-4 border-emerald-500 text-emerald-800 dark:text-emerald-200 rounded-xl shadow-sm flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <span class="font-bold text-sm">{{ session('success') }}</span>
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

            <!-- Section 1: Read-Only Student Information (Exact PTS-1 Card 1 Design) -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
                <div class="flex items-center justify-between border-b border-gray-100 dark:border-gray-700 pb-3 mb-4">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white flex items-center">
                        1. Student Information
                    </h3>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label class="block text-xs font-semibold uppercase text-gray-500 mb-1">Student Name</label>
                        <input type="text" value="{{ $extension->thesis->student->user->name ?? 'N/A' }}" readonly class="w-full bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200 rounded-lg border-gray-300 dark:border-gray-600 cursor-not-allowed">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold uppercase text-gray-500 mb-1">Roll Number</label>
                        <input type="text" value="{{ $extension->thesis->student->roll_number ?? 'N/A' }}" readonly class="w-full bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200 rounded-lg border-gray-300 dark:border-gray-600 cursor-not-allowed">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold uppercase text-gray-500 mb-1">Department</label>
                        <input type="text" value="{{ $extension->thesis->student->department->name ?? 'N/A' }}" readonly class="w-full bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200 rounded-lg border-gray-300 dark:border-gray-600 cursor-not-allowed">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold uppercase text-gray-500 mb-1">Date of Registration</label>
                        <input type="text" value="{{ $extension->thesis->student->date_registration ? \Carbon\Carbon::parse($extension->thesis->student->date_registration)->format('d-m-Y') : 'N/A' }}" readonly class="w-full bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200 rounded-lg border-gray-300 dark:border-gray-600 cursor-not-allowed">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold uppercase text-gray-500 mb-1">Date of Joining</label>
                        <input type="text" value="{{ $extension->thesis->student->date_joining ? \Carbon\Carbon::parse($extension->thesis->student->date_joining)->format('d-m-Y') : 'N/A' }}" readonly class="w-full bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200 rounded-lg border-gray-300 dark:border-gray-600 cursor-not-allowed">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold uppercase text-gray-500 mb-1">Date of Confirmation</label>
                        <input type="text" value="{{ $extension->thesis->student->date_confirmation ? \Carbon\Carbon::parse($extension->thesis->student->date_confirmation)->format('d-m-Y') : 'N/A' }}" readonly class="w-full bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200 rounded-lg border-gray-300 dark:border-gray-600 cursor-not-allowed">
                    </div>
                </div>
            </div>

            <!-- Section 2: Extension Details Submitted by Student -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 space-y-4">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white border-b border-gray-100 dark:border-gray-700 pb-2">
                    2. Extension Application Details
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 sm:gap-6">
                    <div>
                        <label class="block text-xs font-semibold uppercase text-gray-500 mb-1">Open Seminar Date</label>
                        <input type="text" value="{{ $seminarDate ? $seminarDate->format('d-M-Y') : 'N/A' }}" readonly class="w-full bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200 rounded-lg border-gray-300 dark:border-gray-600 cursor-not-allowed">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold uppercase text-gray-500 mb-1">Extended Deadline Requested</label>
                        <input type="text" value="📅 {{ $extension->extended_until_date ? $extension->extended_until_date->format('d-M-Y') : 'N/A' }}" readonly class="w-full bg-purple-50 dark:bg-purple-950/40 text-purple-900 dark:text-purple-200 rounded-lg border-purple-200 dark:border-purple-800 cursor-not-allowed font-medium">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold uppercase text-gray-500 mb-1">Application Date</label>
                        <input type="text" value="{{ $extension->created_at ? $extension->created_at->format('d-M-Y H:i') : 'N/A' }}" readonly class="w-full bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200 rounded-lg border-gray-300 dark:border-gray-600 cursor-not-allowed">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold uppercase text-gray-500 mb-1">Reason for Extension</label>
                    <div class="p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg border border-gray-300 dark:border-gray-600 text-gray-800 dark:text-gray-200 leading-relaxed whitespace-pre-wrap break-words [overflow-wrap:anywhere] cursor-not-allowed">{{ trim($extension->reason_for_extension) }}</div>
                </div>
            </div>

            <!-- Section 3: Prior Authority Evaluations Trail (Strict Confidentiality: Only Previous Ranks) -->
            @php
                $viewerRank = \App\Models\Pts2Extension::getRoleRank($userRole);
            @endphp

            @if($viewerRank >= 2)
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 space-y-4">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white border-b border-gray-100 dark:border-gray-700 pb-2">
                         3. Prior Authority Recommendations & Remarks
                    </h3>

                    <div class="space-y-4">
                        <!-- Main Supervisor Evaluation (Rank 1) -->
                        @if($viewerRank >= 1 && $extension->main_supervisor_recommendation !== null)
                            <div class="p-4 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700/30 space-y-2">
                                <div class="flex items-center justify-between gap-2 sm:gap-4">
                                    <span class="font-bold text-sm text-gray-900 dark:text-white">Main Supervisor Recommendation</span>
                                    <span class="px-2.5 py-0.5 rounded text-xs font-bold whitespace-nowrap shrink-0 {{ $extension->main_supervisor_recommendation ? 'bg-emerald-100 text-emerald-800' : 'bg-red-100 text-red-800' }}">
                                        {{ $extension->main_supervisor_recommendation ? '✓ Recommended' : '❌ Not Recommended' }}
                                    </span>
                                </div>
                                @if($extension->main_supervisor_confidential_remark)
                                    <p class="text-xs italic text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 p-3 rounded-lg border border-gray-200 dark:border-gray-700 whitespace-pre-wrap break-words [overflow-wrap:anywhere]">{{ trim($extension->main_supervisor_confidential_remark) }}</p>
                                @else
                                    <div class="flex items-center space-x-2 text-xs text-gray-600 dark:text-gray-500 bg-gray-200/60 dark:bg-gray-900/40 p-2.5 rounded-lg border border-dashed border-gray-200 dark:border-gray-700/60 mt-0.5">
                                        <svg class="w-4 h-4 text-gray-400 dark:text-gray-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 4.418 9 8z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3l18 18"></path></svg>
                                        <span class="italic font-normal">Remark not provided</span>
                                    </div>
                                @endif
                            </div>
                        @endif

                        <!-- DPGC Evaluation (Rank 2) -->
                        @if($viewerRank > 2 && $extension->dpgc_recommendation !== null)
                            <div class="p-4 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700/30 space-y-2">
                                <div class="flex items-center justify-between gap-2 sm:gap-4">
                                    <span class="font-bold text-sm text-gray-900 dark:text-white">DPGC Recommendation</span>
                                    <span class="px-2.5 py-0.5 rounded text-xs font-bold whitespace-nowrap shrink-0 {{ $extension->dpgc_recommendation ? 'bg-emerald-100 text-emerald-800' : 'bg-red-100 text-red-800' }}">
                                        {{ $extension->dpgc_recommendation ? '✓ Recommended' : '❌ Not Recommended' }}
                                    </span>
                                </div>
                                @if($extension->dpgc_confidential_remark)
                                    <p class="text-xs italic text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 p-3 rounded-lg border border-gray-200 dark:border-gray-700 whitespace-pre-wrap break-words [overflow-wrap:anywhere]">{{ trim($extension->dpgc_confidential_remark) }}</p>
                                @else
                                    <div class="flex items-center space-x-2 text-xs text-gray-600 dark:text-gray-500 bg-gray-200/60 dark:bg-gray-900/40 p-2.5 rounded-lg border border-dashed border-gray-200 dark:border-gray-700/60 mt-0.5">
                                        <svg class="w-4 h-4 text-gray-400 dark:text-gray-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 4.418 9 8z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3l18 18"></path></svg>
                                        <span class="italic font-normal">Remark not provided</span>
                                    </div>
                                @endif
                            </div>
                        @endif

                        <!-- HOD Evaluation (Rank 3) -->
                        @if($viewerRank > 3 && $extension->hod_recommendation !== null)
                            <div class="p-4 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700/30 space-y-2">
                                <div class="flex items-center justify-between gap-2 sm:gap-4">
                                    <span class="font-bold text-sm text-gray-900 dark:text-white">HOD Recommendation</span>
                                    <span class="px-2.5 py-0.5 rounded text-xs font-bold whitespace-nowrap shrink-0 {{ $extension->hod_recommendation ? 'bg-emerald-100 text-emerald-800' : 'bg-red-100 text-red-800' }}">
                                        {{ $extension->hod_recommendation ? '✓ Recommended' : '❌ Not Recommended' }}
                                    </span>
                                </div>
                                @if($extension->hod_confidential_remark)
                                    <p class="text-xs italic text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 p-3 rounded-lg border border-gray-200 dark:border-gray-700 whitespace-pre-wrap break-words [overflow-wrap:anywhere]">{{ trim($extension->hod_confidential_remark) }}</p>
                                @else
                                    <div class="flex items-center space-x-2 text-xs text-gray-600 dark:text-gray-500 bg-gray-200/60 dark:bg-gray-900/40 p-2.5 rounded-lg border border-dashed border-gray-200 dark:border-gray-700/60 mt-0.5">
                                        <svg class="w-4 h-4 text-gray-400 dark:text-gray-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 4.418 9 8z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3l18 18"></path></svg>
                                        <span class="italic font-normal">Remark not provided</span>
                                    </div>
                                @endif
                            </div>
                        @endif

                        <!-- Section Officer Evaluation (Rank 4) -->
                        @if($viewerRank > 4 && $extension->section_officer_recommendation !== null)
                            <div class="p-4 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700/30 space-y-2">
                                <div class="flex items-center justify-between gap-2 sm:gap-4">
                                    <span class="font-bold text-sm text-gray-900 dark:text-white">Section Officer Verification</span>
                                    <span class="px-2.5 py-0.5 rounded text-xs font-bold bg-emerald-100 text-emerald-800 whitespace-nowrap shrink-0">
                                        ✓ Verified &amp; Forwarded
                                    </span>
                                </div>
                                @if($extension->section_officer_confidential_remark)
                                    <p class="text-xs italic text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 p-3 rounded-lg border border-gray-200 dark:border-gray-700 whitespace-pre-wrap break-words [overflow-wrap:anywhere]">{{ trim($extension->section_officer_confidential_remark) }}</p>
                                @else
                                    <div class="flex items-center space-x-2 text-xs text-gray-600 dark:text-gray-500 bg-gray-200/60 dark:bg-gray-900/40 p-2.5 rounded-lg border border-dashed border-gray-200 dark:border-gray-700/60 mt-0.5">
                                        <svg class="w-4 h-4 text-gray-400 dark:text-gray-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 4.418 9 8z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3l18 18"></path></svg>
                                        <span class="italic font-normal">Remark not provided</span>
                                    </div>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            <!-- Section 4: Action Required (Evaluation & Decision - Exact PTS-1 Format) -->
            @if($extension->current_stage === $userRole)
                <form action="{{ route('pts2_extension.submit_review', $extension->id) }}" method="POST" class="space-y-8" @submit="clearDraft()">
                    @csrf

                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-indigo-100 dark:border-indigo-900/50 p-6 space-y-6">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white border-b border-indigo-100 dark:border-indigo-900/50 pb-2 flex items-center">
                            @if($viewerRank >= 2)4.
                            @else 3.                            
                            @endif 
                            Endorsement Evaluation & Recommendation 
                        </h3>

                        @if($userRole === 'section_officer')
                            <!-- Section Officer Verification Checkbox & Declaration (Exact PTS-1 Layout) -->
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
                                            I have verified all student details, academic records, extension request details, and attached documentation for this PTS-2 Extension submission.
                                        </p>
                                    </div>
                                </label>
                            </div>

                            <!-- Section Officer Verification Remark -->
                            <div class="space-y-2 pt-2">
                                <label class="block font-bold text-gray-900 dark:text-white text-sm">
                                    Verification Remark <span class="text-red-500">*</span>
                                </label>
                                <div class="pt-0.5">
                                    <x-snippet-dropdown target="confidentialRemark" form-type="pts2_extension" role="section_officer" comment-type="verification_remark" />
                                </div>
                                <textarea name="confidential_remark" 
                                          rows="3" 
                                          required 
                                          x-model="confidentialRemark" 
                                          placeholder="Provide mandatory verification remarks" 
                                          class="w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm focus:ring-2 focus:ring-emerald-500 whitespace-pre-wrap">{{ trim(old('confidential_remark')) }}</textarea>
                            </div>
                        @else
                            <!-- Item 1: Recommendation Status Radio Cards (Exact PTS-1 Layout) -->
                            <div class="space-y-4">
                                <label class="block font-bold text-gray-900 dark:text-white text-sm">
                                    @if($userRole === 'doaa')
                                        Approval Status for Candidate PTS-2 Extension Submission: <span class="text-red-500">*</span>
                                    @else
                                        Recommendation Status for Candidate PTS-2 Extension Submission: <span class="text-red-500">*</span>
                                    @endif
                                </label>

                                <div class="grid grid-cols-1 gap-4">
                                    <!-- Option (a) RECOMMENDED / APPROVE -->
                                    <label class="p-4 rounded-xl border-2 transition cursor-pointer flex items-start space-x-3" :class="recommendation === '1' ? 'border-emerald-500 bg-emerald-50/50 dark:bg-emerald-950/30' : 'border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800'">
                                        <input type="radio" name="recommendation" value="1" required x-model="recommendation" class="mt-1 text-emerald-600 focus:ring-emerald-500">
                                        <div>
                                            <span class="block font-bold text-sm text-emerald-900 dark:text-emerald-300 uppercase tracking-wide">
                                                {{ $userRole === 'doaa' ? '(a) APPROVE' : '(a) RECOMMENDED' }}
                                            </span>
                                            <p class="text-xs text-gray-600 dark:text-gray-300 mt-1 leading-relaxed">
                                                {{ $userRole === 'doaa' ? "Approve the candidate's PTS-2 extension submission." : "Recommend the candidate's PTS-2 extension submission for forwarding to the next stage in the academic pipeline." }}
                                            </p>
                                        </div>
                                    </label>

                                    <!-- Option (b) NOT RECOMMENDED / DO NOT APPROVE -->
                                    <label class="p-4 rounded-xl border-2 transition cursor-pointer flex items-start space-x-3" :class="recommendation === '0' ? 'border-red-500 bg-red-50/50 dark:bg-red-950/30' : 'border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800'">
                                        <input type="radio" name="recommendation" value="0" required x-model="recommendation" class="mt-1 text-red-600 focus:ring-red-500">
                                        <div>
                                            <span class="block font-bold text-sm text-red-900 dark:text-red-300 uppercase tracking-wide">
                                                {{ $userRole === 'doaa' ? '(b) DO NOT APPROVE' : '(b) NOT RECOMMENDED' }}
                                            </span>
                                            <p class="text-xs text-gray-600 dark:text-gray-300 mt-1 leading-relaxed">
                                                {{ $userRole === 'doaa' ? "Do not approve the candidate's PTS-2 extension submission." : "Do not recommend the submission in its present form without further improvements." }}
                                            </p>
                                        </div>
                                    </label>
                                </div>
                            </div>

                            <!-- Item 2: Dynamic Confidential Remark Field (Required when NOT RECOMMENDED) -->
                            <div class="space-y-2 pt-2">
                                <label class="block font-bold text-gray-900 dark:text-white text-sm">
                                    <span x-show="recommendation === '1'">Recommendation Remark (Optional)</span>
                                    <span x-show="recommendation === '0'">Non-Recommendation Remark <span class="text-red-500">*</span></span>
                                </label>
                                <textarea name="confidential_remark" 
                                          rows="3" 
                                          :required="recommendation === '0'" 
                                          x-model="confidentialRemark" 
                                          :placeholder="recommendation === '1' ? 'Optional evaluation remarks for higher academic authorities' : 'Provide mandatory non-recommendation remarks'" 
                                          class="w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm focus:ring-2 focus:ring-purple-500 whitespace-pre-wrap">{{ trim(old('confidential_remark')) }}</textarea>
                            </div>
                        @endif

                        <!-- Item 3: Approved Date & Mandatory Student Remarks specifically for DOAA -->
                        @if($userRole === 'doaa')
                            <div x-show="recommendation === '1'" class="p-4 bg-purple-50 dark:bg-purple-950/40 border border-purple-200 dark:border-purple-800 rounded-xl space-y-2">
                                <label class="block text-sm font-bold text-purple-900 dark:text-purple-200">
                                    Approved Extension Until Date <span class="text-red-500">*</span>
                                </label>
                                <input type="date" 
                                       name="approved_extended_until_date" 
                                       :required="recommendation === '1'" 
                                       :disabled="recommendation !== '1'"
                                       min="{{ isset($minExtensionDate) && $minExtensionDate ? $minExtensionDate->format('Y-m-d') : '' }}"
                                       max="{{ isset($maxExtensionDate) && $maxExtensionDate ? $maxExtensionDate->format('Y-m-d') : '' }}"
                                       x-model="approvedExtendedUntilDate"
                                       value="{{ old('approved_extended_until_date', $extension->extended_until_date ? $extension->extended_until_date->format('Y-m-d') : '') }}" 
                                       class="w-full md:w-1/2 rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:[color-scheme:dark] text-sm focus:ring-2 focus:ring-purple-500">
                                <p class="text-xs text-purple-700 dark:text-purple-300">
                                    Must be between 16 days ({{ isset($minExtensionDate) && $minExtensionDate ? $minExtensionDate->format('d-M-Y') : 'N/A' }}) and 30 days ({{ isset($maxExtensionDate) && $maxExtensionDate ? $maxExtensionDate->format('d-M-Y') : 'N/A' }}) from Open Seminar. Defaults to student's requested date ({{ $extension->extended_until_date ? $extension->extended_until_date->format('d-M-Y') : 'N/A' }}).
                                </p>
                            </div>

                            <div class="space-y-2 pt-2">
                                <label class="block text-sm font-bold text-indigo-900 dark:text-indigo-200">
                                    Mandatory Remarks for Student <span class="text-red-500">*</span>
                                </label>
                                <textarea name="doaa_student_comment" 
                                          rows="3" 
                                          required 
                                          x-model="doaaStudentComment" 
                                          placeholder="Enter comments specifically visible to the student upon completion..." 
                                          class="w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm focus:ring-2 focus:ring-indigo-500 whitespace-pre-wrap">{{ trim(old('doaa_student_comment')) }}</textarea>
                            </div>
                        @endif
                    </div>

                    <!-- Submit & Revert Action Buttons Bar (Exact PTS-1 Layout) -->
                    <div class="flex flex-col-reverse sm:flex-row items-center sm:justify-end gap-3 pt-4">
                        @if($userRole !== 'section_officer')
                            <!-- Revert Button (Triggers Independent Pop-Up Modal) -->
                            <button type="button" 
                                    @click="showRevertModal = true" 
                                    class="w-full sm:w-auto justify-center bg-red-600 hover:bg-red-700 text-white text-base font-bold px-6 py-3 rounded-xl shadow-lg transition flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"></path>
                                </svg>
                                Revert Form
                            </button>
                        @endif

                        <!-- Main Submit Button (Forwards or Approves depending on stage) -->
                        @if($userRole === 'section_officer')
                            <button type="submit" 
                                    :disabled="!isVerified" 
                                    :class="!isVerified ? 'bg-gray-400 opacity-50 cursor-not-allowed shadow-none' : 'bg-emerald-600 hover:bg-emerald-700 shadow-lg'"
                                    class="w-full sm:w-auto justify-center text-white text-base font-bold px-8 py-3 rounded-xl transition flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                                </svg>
                                Submit &amp; Forward
                            </button>
                        @else
                            <button type="submit" 
                                    class="w-full sm:w-auto justify-center bg-emerald-600 hover:bg-emerald-700 text-white text-base font-bold px-8 py-3 rounded-xl shadow-lg transition flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                                </svg>
                                Submit @if($userRole !== 'doaa') &amp; Forward @endif
                            </button>
                        @endif
                    </div>
                </form>

                <!-- Revert Confirmation Pop-Up Modal (Exact PTS-1 Pop-Up Design) -->
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
                                <h3 class="text-lg font-bold text-gray-900 dark:text-white">Revert PTS-2 Extension to Student</h3>
                                <p class="text-xs text-gray-500 dark:text-gray-400">Send form back to candidate for required changes</p>
                            </div>
                        </div>

                        <!-- Independent Revert Form -->
                        <form action="{{ route('pts2_extension.submit_review', $extension->id) }}" method="POST" class="space-y-4">
                            @csrf
                            <input type="hidden" name="action" value="revert">

                            <div>
                                <label class="block text-sm font-bold text-gray-800 dark:text-gray-200 mb-1.5">
                                    Reversion Comment <span class="text-red-500">*</span>
                                </label>
                                <textarea name="reversion_comment" 
                                          required 
                                          rows="4" 
                                          placeholder="Provide clear reasons/instructions for the student regarding required modifications" 
                                          class="w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm focus:ring-2 focus:ring-amber-500 whitespace-pre-wrap"></textarea>
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

    <script>
        function pts2ExtensionReviewForm() {
            const draftKey = 'pts2_extension_review_draft_thesis_' + @js($extension->thesis_id);
            let savedDraft = {};
            try {
                savedDraft = JSON.parse(sessionStorage.getItem(draftKey) || '{}');
            } catch (e) {}

            return {
                showRevertModal: false,
                recommendation: @js(old('recommendation')) || savedDraft.recommendation || '1',
                isVerified: savedDraft.isVerified !== undefined ? savedDraft.isVerified : false,
                confidentialRemark: @js(old('confidential_remark')) || savedDraft.confidentialRemark || '',
                approvedExtendedUntilDate: @js(old('approved_extended_until_date')) || savedDraft.approvedExtendedUntilDate || @js($extension->extended_until_date ? $extension->extended_until_date->format('Y-m-d') : ''),
                doaaStudentComment: @js(old('doaa_student_comment')) || savedDraft.doaaStudentComment || '',

                init() {
                    const watchFields = ['recommendation', 'isVerified', 'confidentialRemark', 'approvedExtendedUntilDate', 'doaaStudentComment'];
                    watchFields.forEach(field => {
                        this.$watch(field, () => this.saveDraft());
                    });
                },

                saveDraft() {
                    try {
                        sessionStorage.setItem(draftKey, JSON.stringify({
                            recommendation: this.recommendation,
                            isVerified: this.isVerified,
                            confidentialRemark: this.confidentialRemark,
                            approvedExtendedUntilDate: this.approvedExtendedUntilDate,
                            doaaStudentComment: this.doaaStudentComment,
                        }));
                    } catch (e) {}
                },

                clearDraft() {
                    try {
                        sessionStorage.removeItem(draftKey);
                    } catch (e) {}
                }
            }
        }
    </script>
</x-app-layout>

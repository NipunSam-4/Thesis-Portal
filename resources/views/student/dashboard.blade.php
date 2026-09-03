<x-app-layout>
    @php
        $degreeType = $student->isPhd() ? 'PhD' : 'MS(R)';
        $degreeTypeFull = $student->isPhd() ? 'Ph.D.' : 'M.S. (Research)';
        $ptsPrefix = $student->isPhd() ? 'PTS' : 'MSRTS';
    @endphp
    <div class="py-8">
        <div class="max-w-7xl mx-auto px-2 sm:px-6 lg:px-8 space-y-6">

            <!-- Success/Info Flash Alerts -->
            @if(session('success'))
                <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)" x-transition:leave="transition ease-in duration-500" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="p-4 bg-emerald-100 border-l-4 border-emerald-500 text-emerald-800 rounded-lg shadow-sm font-semibold">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('info'))
                <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)" x-transition:leave="transition ease-in duration-500" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="p-4 bg-blue-100 border-l-4 border-blue-500 text-blue-800 rounded-lg shadow-sm font-semibold">
                    {{ session('info') }}
                </div>
            @endif

            @if(session('warning'))
                <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)" x-transition:leave="transition ease-in duration-500" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="p-4 bg-amber-100 border-l-4 border-amber-500 text-amber-800 rounded-lg shadow-sm font-semibold">
                    {{ session('warning') }}
                </div>
            @endif
            
            <!-- Welcome Header Banner -->
            <div class="bg-gradient-to-r from-indigo-600 to-purple-700 rounded-xl shadow-sm p-6 text-white flex items-center justify-between">
                <div>
                    <h2 class="text-2xl font-bold mb-1">Welcome, {{ Auth::user()->name }}</h2>
                    <p class="text-indigo-100 text-sm">Track your thesis progress and manage your submissions here.</p>
                </div>
                <div class="hidden md:block p-3 bg-white/10 rounded-lg">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"></path></svg>
                </div>
            </div>

            <!-- Unified Dashboard Layout Grid -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                
                <!-- Left Sidebar: Profile & Form Action Center -->
                <div class="md:col-span-1 space-y-6">
                    
                    <!-- My Academic Profile Card -->
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
                        <div class="px-3 sm:px-6 py-3 sm:py-4 border-b border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50 flex justify-between items-center">
                            <h3 class="font-bold text-gray-900 dark:text-white">My Academic Profile</h3>
                            <a href="{{ route('profile.edit') }}" class="text-xs font-semibold text-indigo-600 dark:text-indigo-400 hover:underline">
                                View Full Profile &rarr;
                            </a>
                        </div>
                        <div class="p-3 sm:p-6 pt-2 space-y-2.5">
                            <div>
                                <label class="text-[11px] font-semibold text-gray-500 uppercase tracking-wider">Roll Number</label>
                                <div class="mt-0.5 text-gray-900 dark:text-gray-100 font-medium text-sm">{{ $student->roll_number }}</div>
                            </div>
                            <div>
                                <label class="text-[11px] font-semibold text-gray-500 uppercase tracking-wider">Program</label>
                                <div class="mt-0.5 text-gray-900 dark:text-gray-100 font-medium text-sm">{{ $degreeTypeFull }}</div>
                            </div>
                            <div>
                                <label class="text-[11px] font-semibold text-gray-500 uppercase tracking-wider">Department</label>
                                <div class="mt-0.5 text-gray-900 dark:text-gray-100 font-medium text-sm">{{ $student->department->name ?? 'Not Assigned' }}</div>
                            </div>
                            <div>
                                <label class="text-[11px] font-semibold text-gray-500 uppercase tracking-wider">Main Supervisor</label>
                                <div class="mt-0.5 text-gray-900 dark:text-gray-100 font-medium text-sm">{{ $student->mainSupervisors->pluck('name')->join(', ') ?: ($student->supervisors->first()?->name ?? 'Not Assigned') }}</div>
                            </div>
                            <div>
                                <label class="text-[11px] font-semibold text-gray-500 uppercase tracking-wider">Co-Supervisor(s)</label>
                                <div class="mt-0.5 text-gray-900 dark:text-gray-100 font-medium text-sm">{{ $student->coSupervisors->pluck('name')->join(', ') ?: 'None' }}</div>
                            </div>
                            <div>
                                <label class="text-[11px] font-semibold text-gray-500 uppercase tracking-wider">External Supervisor(s)</label>
                                <div class="mt-0.5 text-gray-900 dark:text-gray-100 font-medium text-sm">
                                    {{ $student->externalSupervisors->map(fn($s) => $s->name . ($s->externalSupervisorProfile?->affiliated_institute ? ' (' . $s->externalSupervisorProfile->affiliated_institute . ')' : ''))->join(', ') ?: 'None' }}
                                </div>
                            </div>
                            <div>
                                <label class="text-[11px] font-semibold text-gray-500 uppercase tracking-wider">PSPC Member(s)</label>
                                <div class="mt-0.5 text-gray-900 dark:text-gray-100 font-medium text-sm">{{ $student->pspcMembers->pluck('name')->join(', ') ?: 'Not Assigned' }}</div>
                            </div>
                        </div>
                    </div>

                    <!-- Form Action Center -->
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-3 sm:p-6 space-y-4">
                        <h3 class="font-bold text-gray-900 dark:text-white border-b border-gray-100 dark:border-gray-700 pb-2">
                            Thesis Action Center
                        </h3>

                        <div class="space-y-3">
                            @if($activeThesis && $activeThesis->status === 'completed')
                                <!-- Notice when thesis is completed -->
                                <div class="p-3 bg-emerald-50 dark:bg-emerald-900/30 border border-emerald-200 dark:border-emerald-800 rounded-xl">
                                    <div class="text-xs font-bold text-emerald-900 dark:text-emerald-200 mb-1 flex items-center">
                                        <span class="mr-1.5">✓</span> Thesis Completed
                                    </div>
                                    <p class="text-[11px] text-emerald-700 dark:text-emerald-300">
                                        Your thesis has been successfully completed and approved.
                                    </p>
                                </div>
                            @elseif($activeThesis && $activeThesis->status === 'in_progress')
                                <!-- Notice when an active thesis is in progress -->
                                <div class="p-3 bg-amber-50 dark:bg-amber-900/30 border border-amber-200 dark:border-amber-800 rounded-xl">
                                    <div class="text-xs font-bold text-amber-900 dark:text-amber-200 mb-1 flex items-center">
                                        <span class="mr-1.5">🔒</span> Re-initiation Disabled
                                    </div>
                                    <p class="text-[11px] text-amber-700 dark:text-amber-300">
                                        Re-initiation is disabled while your thesis submission is currently in progress.
                                    </p>
                                </div>

                                <form action="{{ route('student.thesis.store') }}" method="POST" class="space-y-3">
                                    @csrf
                                    <div>
                                        <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">
                                             Thesis Title <span class="text-red-500">*</span>
                                        </label>
                                        <input type="text" name="title" disabled placeholder="Thesis submission in progress" class="w-full rounded-xl border-gray-300 dark:border-gray-600 bg-gray-100 dark:bg-gray-700 text-gray-400 text-xs py-2.5 px-3 cursor-not-allowed opacity-60">
                                    </div>

                                    <button type="button" disabled class="w-full px-4 py-2.5 bg-gray-400 dark:bg-gray-600 text-white font-bold text-xs rounded-xl shadow cursor-not-allowed opacity-60">
                                        Re-initiation Disabled (Thesis In Progress)
                                    </button>
                                </form>
                            @elseif($student->canReinitiateThesis())
                                <!-- Message when title has been registered once before and no thesis in progress -->
                                <div class="p-3 bg-indigo-50 dark:bg-indigo-900/30 border border-indigo-200 dark:border-indigo-800 rounded-xl">
                                    <div class="text-xs font-bold text-indigo-900 dark:text-indigo-200 mb-1 flex items-center">
                                        <span class="mr-1.5">🔄</span> Re-initiate your thesis submission
                                    </div>
                                    <p class="text-[11px] text-indigo-700 dark:text-indigo-300">
                                        Enter a new thesis title below to re-initiate your thesis submission process.
                                    </p>
                                </div>

                                <form action="{{ route('student.thesis.store') }}" method="POST" class="space-y-3">
                                    @csrf
                                    <div>
                                        <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">
                                            Thesis Title <span class="text-red-500">*</span>
                                        </label>
                                        <input type="text" name="title" required value="{{ old('title') }}" placeholder="e.g. Deep Learning Architectures" class="w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-xs py-2.5 px-3">
                                    </div>

                                    <button type="submit" class="w-full px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs rounded-xl shadow transition">
                                        Re-initiate Thesis Submission
                                    </button>
                                </form>
                            @else

                                <form action="{{ route('student.thesis.store') }}" method="POST" class="space-y-3">
                                    @csrf
                                    <div>
                                        <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">
                                            Thesis Title <span class="text-red-500">*</span>
                                        </label>
                                        <input type="text" name="title" required value="{{ old('title') }}" placeholder="e.g. Deep Learning Architectures" class="w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-xs py-2.5 px-3">
                                    </div>

                                    <button type="submit" class="w-full px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs rounded-xl shadow transition">
                                        Register Thesis Title
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Right Main Content: Academic Milestones Grid -->
                <div class="md:col-span-2 space-y-6">

                    <!-- Past Rejected Theses Collapsible Component -->
                    <x-past-rejected-theses :rejectedTheses="$rejectedTheses" :student="$student" />

                    @if(!$activeThesis)
                        @if($rejectedTheses->isNotEmpty())
                            <!-- PREVIOUS THESIS REJECTED NOTICE -->
                            <div class="bg-rose-50/60 dark:bg-rose-950/30 rounded-2xl shadow-xs border border-rose-200 dark:border-rose-900/50 p-5 space-y-3">
                                <div class="flex items-start space-x-3.5">
                                    <div class="w-10 h-10 rounded-xl bg-rose-100 dark:bg-rose-900/60 text-rose-700 dark:text-rose-300 flex items-center justify-center font-bold text-lg shrink-0 border border-rose-200 dark:border-rose-800 shadow-xs">
                                        ❌
                                    </div>
                                    <div>
                                        <h3 class="text-base font-bold text-rose-950 dark:text-rose-100">Previous {{ $degreeType }} Thesis Submission Rejected</h3>
                                        <p class="text-xs text-rose-700 dark:text-rose-300/80 mt-0.5 leading-relaxed">Your previous thesis submission was rejected. You can enter a new thesis title in the Thesis Action Center on the left sidebar to re-initiate your thesis submission process.</p>
                                    </div>
                                </div>
                            </div>
                        @else
                            <!-- NO THESIS REGISTERED NOTICE -->
                            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 space-y-4">
                                <div class="flex items-center space-x-3 border-gray-100 dark:border-gray-700 pb-1">
                                    <div class="w-10 h-10 rounded-full bg-amber-100 dark:bg-amber-900 text-amber-700 dark:text-amber-300 flex items-center justify-center font-bold text-lg shrink-0">
                                        ⚠️
                                    </div>
                                    <div>
                                        <h3 class="text-lg font-bold text-gray-900 dark:text-white">No {{ $degreeType }} Thesis Registered Yet</h3>
                                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Please enter your thesis title in the Thesis Action Center on the left sidebar to initiate your thesis submission.</p>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @else
                        <!-- THESIS REGISTERED MAIN MILESTONE CONTAINER -->
                        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-3 sm:p-6 space-y-4" x-data="{ activeTab: 'in_progress' }">
                            
                            <!-- Registered Thesis Title Header directly above Milestones -->
                            <div class="border-b border-gray-100 dark:border-gray-700 pb-4">
                                <span class="text-[10px] font-bold text-indigo-600 dark:text-indigo-400 uppercase tracking-wider">Registered Thesis Title</span>
                                <h2 class="text-lg font-bold text-gray-900 dark:text-white mt-1 leading-snug">
                                    {{ $activeThesis->title }}
                                </h2>
                            </div>
                            
                            <div class="flex items-center justify-between mt-2 text-xs">
                                <h3 class="font-bold text-gray-900 dark:text-white text-base pt-1">
                                    {{ $degreeType }} Thesis Submission ({{ $ptsPrefix }}) Forms
                                </h3>         </h3>
                                @if($activeThesis->status === 'completed')
                                    <span class="bg-emerald-100 text-emerald-800 dark:bg-emerald-900/60 dark:text-emerald-300 text-[11px] font-bold px-3 py-0.5 rounded-full shrink-0">
                                        Completed
                                    </span>
                                @else
                                    <span class="bg-blue-100 text-blue-800 dark:bg-blue-900/60 dark:text-blue-300 text-[11px] font-bold px-3 py-0.5 rounded-full shrink-0">
                                        In Progress
                                    </span>
                                @endif
                            </div>
                            
                            <!-- Tabs Navigation -->
                            <div class="flex border-b border-gray-200 dark:border-gray-700">
                                <button 
                                    @click="activeTab = 'in_progress'"
                                    :class="activeTab === 'in_progress' ? 'border-indigo-500 text-indigo-600 dark:text-indigo-400' : 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300'"
                                    class="w-1/2 py-2 px-1 text-center border-b-2 font-semibold text-sm transition focus:outline-none flex items-center justify-center space-x-1.5"
                                >
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    <span>{{ $activeThesis->status === 'completed' ? 'Approved' : 'Active / In Progress' }}</span>
                                </button>
                                <button 
                                    @click="activeTab = 'rejected'"
                                    :class="activeTab === 'rejected' ? 'border-indigo-500 text-indigo-600 dark:text-indigo-400' : 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300'"
                                    class="w-1/2 py-2 px-1 text-center border-b-2 font-semibold text-sm transition focus:outline-none flex items-center justify-center space-x-1.5"
                                >
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    <span>Rejected Forms</span>
                                    @if($currentRejectedForms->isNotEmpty())
                                        <span class="bg-red-100 dark:bg-red-900/50 text-red-800 dark:text-red-300 text-[10px] font-bold px-2 py-0.5 rounded-full ml-1.5">
                                            {{ $currentRejectedForms->count() }}
                                        </span>
                                    @endif
                                </button>
                            </div>

                            <!-- In Progress Tab Content -->
                            <div x-show="activeTab === 'in_progress'" class="grid grid-cols-1 md:grid-cols-2 gap-4">

                                <!-- Draft Synopsis Circulation Card -->
                                <div class="p-2.5 sm:p-4 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700/30 space-y-3 flex flex-col justify-between">
                                    <div class="space-y-2">
                                        <div class="flex justify-between items-start gap-2">
                                            <span class="font-bold text-sm text-gray-900 dark:text-white leading-snug">Draft Synopsis Circulation</span>
                                            <div class="flex items-center gap-1 shrink-0">
                                                <x-submission-timeline-modal :form="$draftSynopsis" title="Draft Synopsis Timeline" />
                                                @if($draftSynopsis)
                                                    <span class="shrink-0 bg-blue-100 text-blue-800 text-xs font-bold px-2 py-0.5 rounded whitespace-nowrap">Circulated</span>
                                                @elseif($pts1Approved)
                                                    <span class="shrink-0 bg-gray-200 text-gray-700 text-xs font-bold px-2 py-0.5 rounded whitespace-nowrap">🔒 Disabled</span>
                                                @else
                                                    <span class="shrink-0 bg-blue-100 text-blue-800 text-xs font-bold px-2 py-0.5 rounded whitespace-nowrap">Ready to Circulate</span>
                                                @endif
                                            </div>
                                        </div>
                                        <p class="text-xs text-gray-500 dark:text-gray-400 leading-relaxed">
                                            Circulate draft synopsis report to Supervisors and PSPC Members for early comments before Open Seminar.
                                        </p>

                                        @if($draftSynopsis)
                                            <div class="p-2.5 bg-blue-50 dark:bg-blue-950/40 border-l-4 border-blue-500 rounded-lg text-xs space-y-1 my-2">
                                                <div class="font-bold text-blue-900 dark:text-blue-200">
                                                    {{ $draftSynopsis->comments->count() }} {{ Str::plural('Comment', $draftSynopsis->comments->count()) }} Received
                                                </div>
                                            </div>
                                        @endif
                                    </div>

                                    <div class="pt-2 border-t border-gray-200 dark:border-gray-600">
                                        @if($draftSynopsis)
                                            <a href="{{ route('student.draft_synopsis.show') }}" class="block w-full text-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs rounded-lg shadow transition">
                                                View Circulated Synopsis & Comments &rarr;
                                            </a>
                                        @elseif(!$pts1Approved)
                                            <a href="{{ route('student.draft_synopsis.show') }}" class="block w-full text-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs rounded-lg shadow transition">
                                                Circulate Draft Synopsis Report &rarr;
                                            </a>
                                        @else
                                            <button type="button" disabled class="block w-full text-center px-4 py-2 bg-gray-300 dark:bg-gray-700 text-gray-500 font-bold text-xs rounded-lg cursor-not-allowed">
                                                No Draft Synopsis Circulated
                                            </button>
                                        @endif
                                    </div>
                                </div>

                                <!-- PTS-1 Milestone Card (Open Seminar Report) -->
                                <div class="p-2.5 sm:p-4 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700/30 space-y-3 flex flex-col justify-between">
                                    <div class="space-y-2">
                                        <div class="flex justify-between items-start gap-2">
                                            <span class="font-bold text-sm text-gray-900 dark:text-white leading-snug">{{ $ptsPrefix }}-1: Open Seminar Report</span>
                                            <div class="flex items-center gap-1 shrink-0">
                                                <x-submission-timeline-modal :form="$pts1Form" :title="$ptsPrefix . '-1 Submission Timeline'" />
                                                @if(!$pts1Form)
                                                    <span class="shrink-0 bg-blue-100 text-blue-800 text-xs font-bold px-2 py-0.5 rounded whitespace-nowrap">Ready to Submit</span>
                                                @elseif($pts1Form->status === 'approved')
                                                    <span class="shrink-0 bg-emerald-100 text-emerald-800 text-xs font-bold px-2 py-0.5 rounded whitespace-nowrap">Approved</span>
                                                @elseif($pts1Form->status === 'reverted')
                                                    <span class="shrink-0 bg-amber-100 text-amber-800 text-xs font-bold px-2 py-0.5 rounded whitespace-nowrap">Reverted</span>
                                                @elseif($pts1Form->status === 'rejected')
                                                    <span class="shrink-0 bg-red-100 text-red-800 text-xs font-bold px-2 py-0.5 rounded whitespace-nowrap">Rejected</span>
                                                @else
                                                    <span class="shrink-0 bg-blue-100 text-blue-800 text-xs font-bold px-2 py-0.5 rounded whitespace-nowrap">In Progress</span>
                                                @endif
                                            </div>
                                        </div>
                                        <p class="text-xs text-gray-500 dark:text-gray-400 leading-relaxed">
                                            Submission of open seminar details, institute norms, draft synopsis report, publication and other recognitions list.
                                        </p>

                                        @if($pts1Form && $pts1Form->status === 'reverted')
                                            <div class="p-3 bg-amber-50 dark:bg-amber-950/40 border-l-4 border-amber-500 rounded-lg text-xs my-2">
                                                <div class="font-bold text-amber-900 dark:text-amber-200">
                                                    ⚠️ Reverted by {{ $pts1Form->getRevertedByRoleLabel() }}
                                                </div>
                                            </div>
                                        @endif
                                    </div>

                                    <div class="pt-2 border-t border-gray-200 dark:border-gray-600">
                                        @if(!$pts1Form)
                                            <a href="{{ route('student.pts1.create') }}" class="block w-full text-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs rounded-lg shadow transition">
                                                Create {{ $ptsPrefix }}-1 Form &rarr;
                                            </a>
                                        @elseif($pts1Form->status === 'reverted')
                                            <div class="space-y-2">
                                                <a href="{{ route('student.pts1.edit') }}" class="block w-full text-center px-4 py-2 bg-amber-600 hover:bg-amber-700 text-white font-bold text-xs rounded-lg shadow transition">
                                                    Edit your {{ $ptsPrefix }}-1 Form &rarr;
                                                </a>
                                            </div>
                                        @elseif($pts1Form->status === 'in_progress')
                                            <div class="flex items-center justify-between gap-2 pt-1">
                                                <div class="text-[11px] text-blue-700 dark:text-blue-300 font-semibold py-1 leading-tight break-words">
                                                    ⏳ Under Review Stage: {{ $pts1Form->stage_label }}
                                                </div>
                                                <a href="{{ route('pts1.submitted', $pts1Form->id) }}" class="inline-flex items-center px-3 py-1 bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs rounded-lg shadow transition shrink-0 whitespace-nowrap">
                                                    View Submitted Form &rarr;
                                                </a>
                                            </div>
                                        @elseif($pts1Form->status === 'approved')
                                            <div class="flex items-center justify-between gap-2 pt-1">
                                                <span class="text-[11px] text-emerald-700 dark:text-emerald-300 font-bold">✓ {{ $ptsPrefix }}-1 Form Approved</span>
                                                <a href="{{ route('pts1.show', $pts1Form->id) }}" class="inline-flex items-center px-3 py-1 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-lg shadow transition shrink-0 whitespace-nowrap">
                                                    View Approved Form &rarr;
                                                </a>
                                            </div>
                                        @elseif($pts1Form->status === 'rejected')
                                            <div class="space-y-2">
                                                <div class="flex items-center justify-between gap-2 pt-1">
                                                    <span class="text-[11px] text-red-700 dark:text-red-300 font-bold">❌ {{ $ptsPrefix }}-1 Form Rejected</span>
                                                    <a href="{{ route('pts1.show', $pts1Form->id) }}" class="inline-flex items-center px-3 py-1 bg-red-600 hover:bg-red-700 text-white font-bold text-xs rounded-lg shadow transition shrink-0 whitespace-nowrap">
                                                        View Rejected Form &rarr;
                                                    </a>
                                                </div>
                                                <div class="pt-2">
                                                    <a href="{{ route('student.pts1.create') }}" class="block w-full text-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs rounded-lg shadow transition">
                                                        Create New {{ $ptsPrefix }}-1 Form &rarr;
                                                    </a>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                <!-- PTS-2 Milestone Card (Synopsis Report Submission) -->
                                <div class="p-2.5 sm:p-4 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700/30 space-y-3 flex flex-col justify-between">
                                    <div class="space-y-2">
                                        <div class="flex justify-between items-start gap-2">
                                            <span class="font-bold text-sm text-gray-900 dark:text-white leading-snug">{{ $ptsPrefix }}-2: Synopsis Report Submission</span>
                                            <div class="flex items-center gap-1 shrink-0">
                                                <x-submission-timeline-modal :form="$pts2Form" :title="$ptsPrefix . '-2 Submission Timeline'" />
                                                @if(!$pts1Approved)
                                                    <span class="shrink-0 bg-gray-200 text-gray-700 text-xs font-bold px-2 py-0.5 rounded whitespace-nowrap">🔒 Locked</span>
                                                @elseif(!$pts2Form)
                                                    <span class="shrink-0 bg-blue-100 text-blue-800 text-xs font-bold px-2.5 py-0.5 rounded whitespace-nowrap">Ready to Submit</span>
                                                @elseif($pts2Form->status === 'approved')
                                                    <span class="shrink-0 bg-emerald-100 text-emerald-800 text-xs font-bold px-2.5 py-0.5 rounded whitespace-nowrap">Approved</span>
                                                @elseif($pts2Form->status === 'reverted')
                                                    <span class="shrink-0 bg-amber-100 text-amber-800 text-xs font-bold px-2.5 py-0.5 rounded whitespace-nowrap">Reverted</span>
                                                @elseif($pts2Form->status === 'rejected')
                                                    <span class="shrink-0 bg-red-100 text-red-800 text-xs font-bold px-2.5 py-0.5 rounded whitespace-nowrap">Rejected</span>
                                                @else
                                                    <span class="shrink-0 bg-blue-100 text-blue-800 text-xs font-bold px-2.5 py-0.5 rounded whitespace-nowrap">In Progress</span>
                                                @endif
                                            </div>
                                        </div>
                                        <p class="text-xs text-gray-500 dark:text-gray-400 leading-relaxed">
                                            Submission and sequential endorsement of the {{ $degreeType }} Synopsis Report.
                                        </p>

                                        @if($pts2Form && $pts2Form->status === 'reverted')
                                            <div class="p-3 bg-amber-50 dark:bg-amber-950/40 border-l-4 border-amber-500 rounded-lg text-xs my-2">
                                                <div class="font-bold text-amber-900 dark:text-amber-200">
                                                    ⚠️ Reverted by {{ $pts2Form->getRevertedByRoleLabel() }}
                                                </div>
                                            </div>
                                        @endif
                                    </div>

                                    <div class="pt-2 border-t border-gray-200 dark:border-gray-600 space-y-2">
                                        @if(!$pts1Approved)
                                            <button disabled class="w-full text-center px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-400 dark:text-gray-500 font-bold text-xs rounded-lg cursor-not-allowed">
                                                Requires {{ $ptsPrefix }}-1 Approval
                                            </button>
                                        @elseif($pts2Form && $pts2Form->status === 'approved')
                                            <div class="flex items-center justify-between gap-2 pt-1">
                                                <span class="text-[11px] text-emerald-700 dark:text-emerald-300 font-bold">✓ {{ $ptsPrefix }}-2 Form Approved</span>
                                                <a href="{{ route('pts2.show', $pts2Form->id) }}" class="inline-flex items-center px-3 py-1 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-lg shadow transition shrink-0 whitespace-nowrap">
                                                    View Approved Form &rarr;
                                                </a>
                                            </div>
                                        @elseif($pts2Form && $pts2Form->status === 'in_progress')
                                            <div class="flex items-center justify-between gap-2 pt-1">
                                                <div class="text-[11px] text-blue-700 dark:text-blue-300 font-semibold py-1 leading-tight break-words">
                                                    ⏳ Under Review Stage: {{ $pts2Form->stage_label }}
                                                </div>
                                                <a href="{{ route('pts2.submitted', $pts2Form->id) }}" class="inline-flex items-center px-3 py-1 bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs rounded-lg shadow transition shrink-0 whitespace-nowrap">
                                                    View Submitted Form &rarr;
                                                </a>
                                            </div>
                                        @elseif($pts2Form && $pts2Form->status === 'rejected')
                                            <div class="space-y-2">
                                                <div class="flex items-center justify-between gap-2 pt-1">
                                                    <span class="text-[11px] text-red-700 dark:text-red-300 font-bold">❌ {{ $ptsPrefix }}-2 Form Rejected</span>
                                                    <a href="{{ route('pts2.show', $pts2Form->id) }}" class="inline-flex items-center px-3 py-1 bg-red-600 hover:bg-red-700 text-white font-bold text-xs rounded-lg shadow transition shrink-0 whitespace-nowrap">
                                                        View Rejected Form &rarr;
                                                    </a>
                                                </div>
                                                <div class="pt-2">
                                                    <a href="{{ route('student.pts2.create') }}" class="block w-full text-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs rounded-lg shadow transition whitespace-nowrap">
                                                        Create New {{ $ptsPrefix }}-2 Form &rarr;
                                                    </a>
                                                </div>
                                            </div>
                                        @elseif($activeThesis->isPts2SubmissionActive())
                                            @php
                                                $pts2Deadline = $activeThesis->getPts2Deadline();
                                                $hasApprovedExt = $pts2Extension && $pts2Extension->status === 'approved';
                                            @endphp
                                            @if(!$pts2Form)
                                                <a href="{{ route('student.pts2.create') }}" class="block w-full text-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs rounded-lg shadow transition">
                                                    Create {{ $ptsPrefix }}-2 Form &rarr;
                                                </a>
                                                <div class="text-[10px] text-blue-700 dark:text-blue-300 text-center font-medium">
                                                    ⏳ Submission Deadline: <strong>{{ $pts2Deadline?->format('d-M-Y') }}</strong> ({{ $hasApprovedExt ? 'Approved Extension' : '15 days from Open Seminar' }})
                                                </div>
                                            @elseif($pts2Form->status === 'reverted')
                                                <div class="space-y-2">
                                                    <a href="{{ route('student.pts2.edit') }}" class="block w-full text-center px-4 py-2 bg-amber-600 hover:bg-amber-700 text-white font-bold text-xs rounded-lg shadow transition">
                                                        Edit your {{ $ptsPrefix }}-2 Form &rarr;
                                                    </a>
                                                    <div class="text-[10px] text-amber-700 dark:text-amber-300 text-center font-medium">
                                                        ⏳ Submission Deadline: <strong>{{ $pts2Deadline?->format('d-M-Y') }}</strong> ({{ $hasApprovedExt ? 'Approved Extension' : '15 days from Open Seminar' }})
                                                    </div>
                                                </div>
                                            @endif
                                        @else
                                            @php
                                                $pts2Deadline = $activeThesis->getPts2Deadline();
                                            @endphp
                                            <button disabled class="w-full text-center px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-400 dark:text-gray-500 font-bold text-xs rounded-lg cursor-not-allowed">
                                                Submission Deadline Passed ({{ $pts2Deadline?->format('d-M-Y') }})
                                            </button>
                                            <p class="text-[11px] text-red-600 dark:text-red-400 text-center leading-tight">
                                                {{ $ptsPrefix }}-2 submission window closed on {{ $pts2Deadline?->format('d-M-Y') }}.
                                                @if($activeThesis->canApplyForPts2Extension())
                                                    You may apply for an extension below.
                                                @endif
                                            </p>
                                        @endif

                                        <!-- PTS-2 Extension Section (Positioned Below PTS-2 Actions) -->
                                        @if($pts2Extension)
                                            <div class="p-3 rounded-xl border text-xs my-2 space-y-2 {{ $pts2Extension->status === 'approved' ? 'bg-emerald-50 dark:bg-emerald-950/40 border-emerald-300 dark:border-emerald-800 text-emerald-900 dark:text-emerald-200' : ($pts2Extension->status === 'reverted' ? 'bg-amber-50 dark:bg-amber-950/40 border-amber-300 dark:border-amber-800 text-amber-900 dark:text-amber-200' : ($pts2Extension->status === 'rejected' ? 'bg-red-50 dark:bg-red-950/40 border-red-300 dark:border-red-800 text-red-900 dark:text-red-200' : 'bg-purple-50 dark:bg-purple-950/40 border-purple-300 dark:border-purple-800 text-purple-900 dark:text-purple-200')) }}">
                                                <div class="flex items-center justify-between font-bold">
                                                    <span>📅 {{ $ptsPrefix }}-2 Extension</span>
                                                    <div class="flex items-center gap-1">
                                                        <x-submission-timeline-modal :form="$pts2Extension" :title="$ptsPrefix . '-2 Extension Timeline'" />
                                                        @if($pts2Extension->status !== 'reverted')
                                                             <span class="text-[10px] px-2.5 py-0.5 rounded-full uppercase font-extrabold tracking-wider {{ $pts2Extension->status === 'approved' ? 'bg-emerald-100 dark:bg-emerald-900/60 text-emerald-800 dark:text-emerald-300' : ($pts2Extension->status === 'reverted' ? 'bg-amber-100 dark:bg-amber-900/60 text-amber-800 dark:text-amber-300' : ($pts2Extension->status === 'rejected' ? 'bg-red-100 dark:bg-red-900/60 text-red-800 dark:text-red-300' : 'bg-purple-100 dark:bg-purple-900/60 text-purple-800 dark:text-purple-300')) }}">
                                                                @if($pts2Extension->status === 'approved')
                                                                    ✓ Approved
                                                                @elseif($pts2Extension->status === 'rejected')
                                                                    ❌ Rejected
                                                                @else
                                                                    {{ str_replace('_', ' ', $pts2Extension->status) }}
                                                                @endif
                                                            </span>
                                                        @endif
                                                    </div>
                                                </div>

                                                @if($pts2Extension->status === 'in_progress')
                                                    <div class="text-[11px] flex justify-between items-center gap-2 pt-0.5">
                                                        <span class="font-semibold text-purple-800 dark:text-purple-300 leading-tight break-words">⏳ Under Review Stage: {{ $pts2Extension->stage_label }}</span>
                                                        <a href="{{ route('pts2_extension.show', $pts2Extension->id) }}" class="underline font-bold text-purple-700 hover:text-purple-900 dark:text-purple-300 dark:hover:text-purple-100 shrink-0 whitespace-nowrap">View Submitted Form &rarr;</a>
                                                    </div>
                                                @elseif($pts2Extension->status === 'approved')
                                                    <div class="text-[11px] flex justify-between items-center gap-2 pt-1">
                                                        <span>Extended Until: {{ ($pts2Extension->approved_extended_until_date)?->format('d-M-Y') ?? 'N/A' }}</span>
                                                        <a href="{{ route('pts2_extension.show', $pts2Extension->id) }}" class="underline font-bold hover:text-emerald-700 shrink-0 whitespace-nowrap">View Approved Form &rarr;</a>
                                                    </div>
                                                @elseif($pts2Extension->status === 'reverted')
                                                    <div class="p-3 bg-amber-50 dark:bg-amber-950/40 border-l-4 border-amber-500 rounded-lg text-xs">
                                                        <div class="font-bold text-amber-900 dark:text-amber-200">
                                                            ⚠️ Reverted by {{ $pts2Extension->getRevertedByRoleLabel() }}
                                                        </div>
                                                    </div>
                                                    <div class="pt-1">
                                                        <a href="{{ route('student.pts2_extension.create') }}" class="block w-full text-center px-3 py-1.5 bg-amber-600 hover:bg-amber-700 text-white font-bold text-xs rounded-lg shadow transition whitespace-nowrap">
                                                            ⚠️ Resubmit {{ $ptsPrefix }}-2 Extension &rarr;
                                                        </a>
                                                    </div>
                                                @elseif($pts2Extension->status === 'rejected')
                                                    <div class="text-[11px] flex justify-between items-center gap-2 pt-1">
                                                        <span>Application Rejected</span>
                                                        <a href="{{ route('pts2_extension.show', $pts2Extension->id) }}" class="underline font-bold hover:text-red-700 shrink-0 whitespace-nowrap">View Rejected Form &rarr;</a>
                                                    </div>
                                                @endif
                                            </div>
                                        @endif

                                        <!-- Apply for Extension Button (Purple Theme) -->
                                        @if($pts1Approved && (!$pts2Form || $pts2Form->status !== 'approved'))
                                            @if(!$pts2Extension || $pts2Extension->status === 'approved' || $pts2Extension->status === 'rejected')
                                                <div class="my-2 space-y-1">
                                                    @if($activeThesis->canApplyForPts2Extension())
                                                        <a href="{{ route('student.pts2_extension.create') }}" class="block w-full text-center px-3 py-1.5 bg-purple-50 hover:bg-purple-100 dark:bg-purple-900/30 dark:hover:bg-purple-900/50 text-purple-700 dark:text-purple-300 font-semibold text-xs rounded-lg transition border border-purple-200 dark:border-purple-800">
                                                            📅 Apply for {{ $ptsPrefix }}-2 (Synopsis) Extension &rarr;
                                                        </a>
                                                        <div class="text-[10px] text-purple-700 dark:text-purple-300 text-center font-medium">
                                                            Extension window open till {{ $activeThesis->getMaxExtensionDate()?->format('d-M-Y') }} (30 days from Open Seminar)
                                                        </div>
                                                    @else
                                                        <div class="p-2 bg-gray-100 dark:bg-gray-800 rounded-lg text-center text-[10px] text-gray-500 dark:text-gray-400 border border-gray-200 dark:border-gray-700">
                                                            🔒 Extension application window closed (30 days from Open Seminar elapsed on {{ $activeThesis->getMaxExtensionDate()?->format('d-M-Y') }}).
                                                        </div>
                                                    @endif
                                                </div>
                                            @endif
                                        @endif
                                    </div>
                                </div>

                                <!-- PTS-3 Milestone Card (Panels of Examiners) -->
                                <div class="p-2.5 sm:p-4 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700/30 space-y-3 flex flex-col justify-between">
                                    <div class="space-y-2">
                                        <div class="flex justify-between items-start gap-2">
                                            <span class="font-bold text-sm text-gray-900 dark:text-white leading-snug">
                                                {{ $ptsPrefix }}-3: Panels of Examiners
                                            </span>
                                            <div class="flex items-center gap-1 shrink-0">
                                                <x-submission-timeline-modal :form="$pts3Form" :title="$ptsPrefix . '-3 Submission Timeline'" />
                                                @if(!$pts2Approved)
                                                    <span class="shrink-0 bg-gray-200 text-gray-700 text-xs font-bold px-2 py-0.5 rounded whitespace-nowrap">🔒 Locked</span>
                                                @elseif(!$pts3Form)
                                                    <span class="shrink-0 bg-blue-100 text-blue-800 text-xs font-bold px-2.5 py-0.5 rounded whitespace-nowrap">Pending Initiation</span>
                                                @elseif($pts3Form->status === 'approved')
                                                    <span class="shrink-0 bg-emerald-100 text-emerald-800 text-xs font-bold px-2.5 py-0.5 rounded whitespace-nowrap">Approved</span>
                                                @elseif($pts3Form->status === 'reverted')
                                                    <span class="shrink-0 bg-amber-100 text-amber-800 text-xs font-bold px-2.5 py-0.5 rounded whitespace-nowrap">Reverted</span>
                                                @elseif($pts3Form->status === 'rejected')
                                                    <span class="shrink-0 bg-red-100 text-red-800 text-xs font-bold px-2.5 py-0.5 rounded whitespace-nowrap">Rejected</span>
                                                @else
                                                    <span class="shrink-0 bg-blue-100 text-blue-800 text-xs font-bold px-2.5 py-0.5 rounded whitespace-nowrap">In Progress</span>
                                                @endif
                                            </div>
                                        </div>
                                        <p class="text-xs text-gray-500 dark:text-gray-400 leading-relaxed">
                                            Panel of Indian &amp; International Examiners and Oral Examination Board (OEB) recommendations.
                                        </p>

                                        @if($pts3Form && $pts3Form->status === 'reverted')
                                            <div class="p-3 bg-amber-50 dark:bg-amber-950/40 border-l-4 border-amber-500 rounded-lg text-xs my-2">
                                                <div class="font-bold text-amber-900 dark:text-amber-200">
                                                    ⚠️ Reverted by {{ $pts3Form->getRevertedByRoleLabel() }}
                                                </div>
                                            </div>
                                        @endif
                                    </div>

                                    <div class="pt-2 border-t border-gray-200 dark:border-gray-600 space-y-2">
                                        @if(!$pts2Approved)
                                            <button disabled class="w-full text-center px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-400 dark:text-gray-500 font-bold text-xs rounded-lg cursor-not-allowed">
                                                Requires {{ $ptsPrefix }}-2 Approval
                                            </button>
                                        @elseif($pts3Form && $pts3Form->status === 'approved')
                                            <div class="flex items-center justify-between gap-2 pt-1">
                                                <span class="text-[11px] text-emerald-700 dark:text-emerald-300 font-bold">✓ {{ $ptsPrefix }}-3 Form Approved</span>
                                                <a href="{{ route('pts3.show', $pts3Form->id) }}" class="inline-flex items-center px-3 py-1 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-lg shadow transition shrink-0 whitespace-nowrap">
                                                    View Approved Form &rarr;
                                                </a>
                                            </div>
                                        @elseif($pts3Form && $pts3Form->status === 'in_progress')
                                            <div class="flex items-center justify-between gap-2 pt-1">
                                                <div class="text-[11px] text-blue-700 dark:text-blue-300 font-semibold py-1 leading-tight break-words">
                                                    ⏳ Under Review Stage: {{ $pts3Form->stage_label }}
                                                </div>
                                                <a href="{{ route('pts3.show', $pts3Form->id) }}" class="inline-flex items-center px-3 py-1 bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs rounded-lg shadow transition shrink-0 whitespace-nowrap">
                                                    View Submitted Form &rarr;
                                                </a>
                                            </div>
                                        @elseif($pts3Form && $pts3Form->status === 'reverted')
                                            <div class="flex items-center justify-between gap-2 pt-1">
                                                <div class="text-[11px] text-amber-700 dark:text-amber-300 font-semibold py-1 leading-tight break-words">
                                                    ⚠️ Form Reverted to Main Supervisor
                                                </div>
                                                <a href="{{ route('pts3.show', $pts3Form->id) }}" class="inline-flex items-center px-3 py-1 bg-amber-600 hover:bg-amber-700 text-white font-bold text-xs rounded-lg shadow transition shrink-0 whitespace-nowrap">
                                                    View Submitted Form &rarr;
                                                </a>
                                            </div>
                                        @elseif($pts3Form && $pts3Form->status === 'rejected')
                                            <div class="flex items-center justify-between gap-2 pt-1">
                                                <span class="text-[11px] text-red-700 dark:text-red-300 font-bold">❌ {{ $ptsPrefix }}-3 Form Rejected</span>
                                                <a href="{{ route('pts3.show', $pts3Form->id) }}" class="inline-flex items-center px-3 py-1 bg-red-600 hover:bg-red-700 text-white font-bold text-xs rounded-lg shadow transition shrink-0 whitespace-nowrap">
                                                    View Rejected Form &rarr;
                                                </a>
                                            </div>
                                        @else
                                            <button disabled class="w-full text-center px-4 py-2 bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 font-semibold text-xs rounded-lg border border-blue-200 dark:border-blue-800 cursor-not-allowed">
                                                Waiting for the Main Supervisor to Initiate
                                            </button>
                                        @endif
                                    </div>
                                </div>

                                <!-- PTS-4 Milestone Card (Thesis Submission) -->
                                <div class="p-2.5 sm:p-4 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700/30 space-y-3 flex flex-col justify-between">
                                    <div class="space-y-2">
                                        <div class="flex justify-between items-start gap-2">
                                            <span class="font-bold text-sm text-gray-900 dark:text-white leading-snug">
                                                {{ $ptsPrefix }}-4: Thesis Submission
                                            </span>
                                            <div class="flex items-center gap-1">
                                                @if($pts4Form)
                                                    <x-submission-timeline-modal :form="$pts4Form" :title="$ptsPrefix . '-4 Submission Timeline'" />
                                                @endif
                                                @if(!$pts2Approved)
                                                    <span class="shrink-0 bg-gray-200 text-gray-700 text-xs font-bold px-2 py-0.5 rounded whitespace-nowrap">🔒 Locked</span>
                                                @elseif(!$pts4Form)
                                                    <span class="shrink-0 bg-blue-100 text-blue-800 text-xs font-bold px-2.5 py-0.5 rounded whitespace-nowrap">Ready to Submit</span>
                                                @elseif($pts4Form->status === 'approved')
                                                    <span class="shrink-0 bg-emerald-100 text-emerald-800 text-xs font-bold px-2.5 py-0.5 rounded whitespace-nowrap">Approved</span>
                                                @elseif($pts4Form->status === 'reverted')
                                                    <span class="shrink-0 bg-amber-100 text-amber-800 text-xs font-bold px-2.5 py-0.5 rounded whitespace-nowrap">Reverted</span>
                                                @elseif($pts4Form->status === 'rejected')
                                                    <span class="shrink-0 bg-red-100 text-red-800 text-xs font-bold px-2.5 py-0.5 rounded whitespace-nowrap">Rejected</span>
                                                @else
                                                    <span class="shrink-0 bg-blue-100 text-blue-800 text-xs font-bold px-2.5 py-0.5 rounded whitespace-nowrap">In Progress</span>
                                                @endif
                                            </div>
                                        </div>
                                        <p class="text-xs text-gray-500 dark:text-gray-400 leading-relaxed">
                                            Submission and sequential endorsement of the {{ $degreeType }} Thesis.
                                        </p>

                                        @if($pts4Form && $pts4Form->status === 'reverted')
                                            <div class="p-3 bg-amber-50 dark:bg-amber-950/40 border-l-4 border-amber-500 rounded-lg text-xs my-2">
                                                <div class="font-bold text-amber-900 dark:text-amber-200">
                                                    ⚠️ Reverted by {{ $pts4Form->getRevertedByRoleLabel() }}
                                                </div>
                                            </div>
                                        @endif
                                    </div>

                                    <div class="pt-2 border-t border-gray-200 dark:border-gray-600 space-y-2">
                                        @if(!$pts2Approved)
                                            <button disabled class="w-full text-center px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-400 dark:text-gray-500 font-bold text-xs rounded-lg cursor-not-allowed">
                                                Requires {{ $ptsPrefix }}-2 Approval
                                            </button>
                                        @elseif($pts4Form && $pts4Form->status === 'approved')
                                            <div class="flex items-center justify-between gap-2 pt-1">
                                                <span class="text-[11px] text-emerald-700 dark:text-emerald-300 font-bold">✓ {{ $ptsPrefix }}-4 Form Approved</span>
                                                <a href="{{ route('pts4.show', $pts4Form->id) }}" class="inline-flex items-center px-3 py-1 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-lg shadow transition shrink-0 whitespace-nowrap">
                                                    View Approved Form &rarr;
                                                </a>
                                            </div>
                                        @elseif($pts4Form && $pts4Form->status === 'in_progress')
                                            <div class="flex items-center justify-between gap-2 pt-1">
                                                <div class="text-[11px] text-blue-700 dark:text-blue-300 font-semibold py-1 leading-tight break-words">
                                                    ⏳ Under Review Stage: {{ $pts4Form->stage_label }}
                                                </div>
                                                <a href="{{ route('pts4.submitted', $pts4Form->id) }}" class="inline-flex items-center px-3 py-1 bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs rounded-lg shadow transition shrink-0 whitespace-nowrap">
                                                    View Submitted Form &rarr;
                                                </a>
                                            </div>
                                        @elseif($pts4Form && $pts4Form->status === 'rejected')
                                            <div class="space-y-2">
                                                <div class="flex items-center justify-between gap-2 pt-1">
                                                    <span class="text-[11px] text-red-700 dark:text-red-300 font-bold">❌ {{ $ptsPrefix }}-4 Form Rejected</span>
                                                    <a href="{{ route('pts4.show', $pts4Form->id) }}" class="inline-flex items-center px-3 py-1 bg-red-600 hover:bg-red-700 text-white font-bold text-xs rounded-lg shadow transition shrink-0 whitespace-nowrap">
                                                        View Rejected Form &rarr;
                                                    </a>
                                                </div>
                                                <div class="pt-2">
                                                    <a href="{{ route('student.pts4.create') }}" class="block w-full text-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs rounded-lg shadow transition whitespace-nowrap">
                                                        Create New {{ $ptsPrefix }}-4 Form &rarr;
                                                    </a>
                                                </div>
                                            </div>
                                        @elseif($activeThesis->isPts4SubmissionActive())
                                            @php
                                                $pts4Deadline = $activeThesis->getPts4Deadline();
                                                $hasApprovedExt = $pts4Extension && $pts4Extension->status === 'approved';
                                            @endphp
                                            @if(!$pts4Form)
                                                <a href="{{ route('student.pts4.create') }}" class="block w-full text-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs rounded-lg shadow transition">
                                                    Create {{ $ptsPrefix }}-4 Form &rarr;
                                                </a>
                                                <div class="text-[10px] text-blue-700 dark:text-blue-300 text-center font-medium">
                                                    ⏳ Submission Deadline: <strong>{{ $pts4Deadline?->format('d-M-Y') }}</strong> ({{ $hasApprovedExt ? 'Approved Extension' : '30 days from Open Seminar' }})
                                                </div>
                                            @elseif($pts4Form->status === 'reverted')
                                                <div class="space-y-2">
                                                    <a href="{{ route('student.pts4.edit') }}" class="block w-full text-center px-4 py-2 bg-amber-600 hover:bg-amber-700 text-white font-bold text-xs rounded-lg shadow transition">
                                                        Edit your {{ $ptsPrefix }}-4 Form &rarr;
                                                    </a>
                                                    <div class="text-[10px] text-amber-700 dark:text-amber-300 text-center font-medium">
                                                        ⏳ Submission Deadline: <strong>{{ $pts4Deadline?->format('d-M-Y') }}</strong> ({{ $hasApprovedExt ? 'Approved Extension' : '30 days from Open Seminar' }})
                                                    </div>
                                                </div>
                                            @endif
                                        @else
                                            @php
                                                $pts4Deadline = $activeThesis->getPts4Deadline();
                                            @endphp
                                            <button disabled class="w-full text-center px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-400 dark:text-gray-500 font-bold text-xs rounded-lg cursor-not-allowed">
                                                Submission Deadline Passed ({{ $pts4Deadline?->format('d-M-Y') }})
                                            </button>
                                            <p class="text-[11px] text-red-600 dark:text-red-400 text-center leading-tight">
                                                {{ $ptsPrefix }}-4 submission window closed on {{ $pts4Deadline?->format('d-M-Y') }}.
                                                @if($activeThesis->canApplyForPts4Extension())
                                                    You may apply for an extension below.
                                                @endif
                                            </p>
                                        @endif

                                        <!-- PTS-4 Extension Section (Positioned Below PTS-4 Actions) -->
                                        @if($pts4Extension)
                                            <div class="p-3 rounded-xl border text-xs my-2 space-y-2 {{ $pts4Extension->status === 'approved' ? 'bg-emerald-50 dark:bg-emerald-950/40 border-emerald-300 dark:border-emerald-800 text-emerald-900 dark:text-emerald-200' : ($pts4Extension->status === 'reverted' ? 'bg-amber-50 dark:bg-amber-950/40 border-amber-300 dark:border-amber-800 text-amber-900 dark:text-amber-200' : ($pts4Extension->status === 'rejected' ? 'bg-red-50 dark:bg-red-950/40 border-red-300 dark:border-red-800 text-red-900 dark:text-red-200' : 'bg-purple-50 dark:bg-purple-950/40 border-purple-300 dark:border-purple-800 text-purple-900 dark:text-purple-200')) }}">
                                                <div class="flex items-center justify-between font-bold">
                                                    <span>📅 {{ $ptsPrefix }}-4 Extension</span>
                                                    <div class="flex items-center gap-1">
                                                        <x-submission-timeline-modal :form="$pts4Extension" :title="$ptsPrefix . '-4 Extension Timeline'" />
                                                        @if($pts4Extension->status !== 'reverted')
                                                            <span class="text-[10px] px-2.5 py-0.5 rounded-full uppercase font-extrabold tracking-wider {{ $pts4Extension->status === 'approved' ? 'bg-emerald-100 dark:bg-emerald-900/60 text-emerald-800 dark:text-emerald-300' : ($pts4Extension->status === 'reverted' ? 'bg-amber-100 dark:bg-amber-900/60 text-amber-800 dark:text-amber-300' : ($pts4Extension->status === 'rejected' ? 'bg-red-100 dark:bg-red-900/60 text-red-800 dark:text-red-300' : 'bg-purple-100 dark:bg-purple-900/60 text-purple-800 dark:text-purple-300')) }}">
                                                                @if($pts4Extension->status === 'approved')
                                                                    ✓ Approved
                                                                @elseif($pts4Extension->status === 'rejected')
                                                                    ❌ Rejected
                                                                @else
                                                                    {{ str_replace('_', ' ', $pts4Extension->status) }}
                                                                @endif
                                                            </span>
                                                        @endif
                                                    </div>
                                                </div>

                                                @if($pts4Extension->status === 'in_progress')
                                                    <div class="text-[11px] flex justify-between items-center gap-2 pt-0.5">
                                                        <span class="font-semibold text-purple-800 dark:text-purple-300 leading-tight break-words">⏳ Under Review Stage: {{ $pts4Extension->stage_label }}</span>
                                                        <a href="{{ route('pts4_extension.show', $pts4Extension->id) }}" class="underline font-bold text-purple-700 hover:text-purple-900 dark:text-purple-300 dark:hover:text-purple-100 shrink-0 whitespace-nowrap">View Submitted Form &rarr;</a>
                                                    </div>
                                                @elseif($pts4Extension->status === 'approved')
                                                    <div class="text-[11px] flex justify-between items-center gap-2 pt-1">
                                                        <span>Extended Until: {{ ($pts4Extension->approved_extended_until_date)?->format('d-M-Y') ?? 'N/A' }}</span>
                                                        <a href="{{ route('pts4_extension.show', $pts4Extension->id) }}" class="underline font-bold hover:text-emerald-700 shrink-0 whitespace-nowrap">View Approved Form &rarr;</a>
                                                    </div>
                                                @elseif($pts4Extension->status === 'reverted')
                                                    <div class="p-3 bg-amber-50 dark:bg-amber-950/40 border-l-4 border-amber-500 rounded-lg text-xs">
                                                        <div class="font-bold text-amber-900 dark:text-amber-200">
                                                            ⚠️ Reverted by {{ $pts4Extension->getRevertedByRoleLabel() }}
                                                        </div>
                                                    </div>
                                                    <div class="pt-1">
                                                        <a href="{{ route('student.pts4_extension.create') }}" class="block w-full text-center px-3 py-1.5 bg-amber-600 hover:bg-amber-700 text-white font-bold text-xs rounded-lg shadow transition whitespace-nowrap">
                                                            ⚠️ Resubmit {{ $ptsPrefix }}-4 Extension &rarr;
                                                        </a>
                                                    </div>
                                                @elseif($pts4Extension->status === 'rejected')
                                                    <div class="text-[11px] flex justify-between items-center gap-2 pt-1">
                                                        <span>Application Rejected</span>
                                                        <a href="{{ route('pts4_extension.show', $pts4Extension->id) }}" class="underline font-bold hover:text-red-700 shrink-0 whitespace-nowrap">View Rejected Form &rarr;</a>
                                                    </div>
                                                @endif
                                            </div>
                                        @endif

                                        <!-- Apply for PTS-4 Extension Button (Purple Theme) -->
                                        @if($pts2Approved && (!$pts4Form || $pts4Form->status !== 'approved'))
                                            @if(!$pts4Extension || $pts4Extension->status === 'approved' || $pts4Extension->status === 'rejected')
                                                <div class="my-2 space-y-1">
                                                    @if($activeThesis->canApplyForPts4Extension())
                                                        <a href="{{ route('student.pts4_extension.create') }}" class="block w-full text-center px-3 py-1.5 bg-purple-50 hover:bg-purple-100 dark:bg-purple-900/30 dark:hover:bg-purple-900/50 text-purple-700 dark:text-purple-300 font-semibold text-xs rounded-lg transition border border-purple-200 dark:border-purple-800">
                                                            📅 Apply for {{ $ptsPrefix }}-4 (Thesis) Extension &rarr;
                                                        </a>
                                                        <div class="text-[10px] text-purple-700 dark:text-purple-300 text-center font-medium">
                                                            Extension window open till {{ $activeThesis->getMaxPts4ExtensionDate()?->format('d-M-Y') }} (60 days from Open Seminar)
                                                        </div>
                                                    @else
                                                        <div class="p-2 bg-gray-100 dark:bg-gray-800 rounded-lg text-center text-[10px] text-gray-500 dark:text-gray-400 border border-gray-200 dark:border-gray-700">
                                                            🔒 Extension application window closed (60 days from Open Seminar elapsed on {{ $activeThesis->getMaxPts4ExtensionDate()?->format('d-M-Y') }}).
                                                        </div>
                                                    @endif
                                                </div>
                                            @endif
                                        @endif
                                    </div>
                                </div>

                                <!-- PTS-5 Milestone Card (Examiners Report & Defense) -->
                                <div class="p-2.5 sm:p-4 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700/30 space-y-3 flex flex-col justify-between">
                                    <div class="space-y-2">
                                        <div class="flex justify-between items-start gap-2">
                                            <span class="font-bold text-sm text-gray-900 dark:text-white leading-snug">
                                                {{ $ptsPrefix }}-5: Examiners Report &amp; Defense
                                            </span>
                                            <div class="flex items-center gap-1 shrink-0">
                                                @if($pts5Form)
                                                    <x-submission-timeline-modal :form="$pts5Form" :title="$ptsPrefix . '-5 Submission Timeline'" />
                                                @endif
                                                @if(!$pts3Approved || !$pts4Approved)
                                                    <span class="shrink-0 bg-gray-200 text-gray-700 text-xs font-bold px-2 py-0.5 rounded whitespace-nowrap">🔒 Locked</span>
                                                @elseif($pts5Form && $pts5Form->status === 'approved')
                                                    <span class="shrink-0 bg-emerald-100 text-emerald-800 text-xs font-bold px-2.5 py-0.5 rounded whitespace-nowrap">Approved</span>
                                                @elseif($pts5Form && $pts5Form->status === 'in_progress')
                                                    <span class="shrink-0 bg-blue-100 text-blue-800 text-xs font-bold px-2.5 py-0.5 rounded whitespace-nowrap">In Progress</span>
                                                @elseif($pts5Form && $pts5Form->status === 'rejected')
                                                    <span class="shrink-0 bg-red-100 text-red-800 text-xs font-bold px-2.5 py-0.5 rounded whitespace-nowrap">Rejected</span>
                                                @else
                                                    <span class="shrink-0 bg-blue-100 text-blue-800 text-xs font-bold px-2.5 py-0.5 rounded whitespace-nowrap">Ready to Submit</span>
                                                @endif
                                            </div>
                                        </div>
                                        <p class="text-xs text-gray-500 dark:text-gray-400 leading-relaxed">
                                            Evaluation of {{ $degreeType }} thesis by external examiners and oral defense recommendations.
                                        </p>
                                    </div>

                                    <div class="pt-2 border-t border-gray-200 dark:border-gray-600 space-y-2">
                                        @if(!$pts3Approved || !$pts4Approved)
                                            <button disabled class="w-full text-center px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-400 dark:text-gray-500 font-bold text-xs rounded-lg cursor-not-allowed">
                                                Requires {{ $ptsPrefix }}-3 &amp; {{ $ptsPrefix }}-4 Approval
                                            </button>
                                        @elseif($pts5Form && $pts5Form->status === 'approved')
                                            <div class="flex items-center justify-between gap-2 pt-1">
                                                <span class="text-[11px] text-emerald-700 dark:text-emerald-300 font-bold">✓ {{ $ptsPrefix }}-5 Form Approved</span>
                                            </div>
                                        @elseif($pts5Form && $pts5Form->status === 'in_progress')
                                            <div class="flex items-center justify-between gap-2 pt-1">
                                                <div class="text-[11px] text-blue-700 dark:text-blue-300 font-semibold py-1 leading-tight break-words">
                                                    ⏳ Under Review Stage: {{ $pts5Form->stage_label }}
                                                </div>
                                            </div>
                                        @else
                                            <button disabled class="w-full text-center px-4 py-2 bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 font-semibold text-xs rounded-lg border border-blue-200 dark:border-blue-800 cursor-not-allowed">
                                                Examiner Evaluation &amp; Defense Stage
                                            </button>
                                        @endif
                                    </div>
                                </div>

                                <!-- PTS-6 Milestone Card (Oral Examination Report) -->
                                <div class="p-2.5 sm:p-4 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700/30 space-y-3 flex flex-col justify-between">
                                    <div class="space-y-2">
                                        <div class="flex justify-between items-start gap-2">
                                            <span class="font-bold text-sm text-gray-900 dark:text-white leading-snug">
                                                {{ $ptsPrefix }}-6: Report of {{ $degreeType }} Thesis Oral Examination
                                            </span>
                                            <div class="flex items-center gap-1 shrink-0">
                                                @if($pts6Form)
                                                    <x-submission-timeline-modal :form="$pts6Form" :title="$ptsPrefix . '-6 Submission Timeline'" />
                                                @endif
                                                @if(!$pts5Approved)
                                                    <span class="shrink-0 bg-gray-200 text-gray-700 text-xs font-bold px-2 py-0.5 rounded whitespace-nowrap">🔒 Locked</span>
                                                @elseif($pts6Form && $pts6Form->status === 'approved')
                                                    <span class="shrink-0 bg-emerald-100 text-emerald-800 text-xs font-bold px-2.5 py-0.5 rounded whitespace-nowrap">Approved</span>
                                                @elseif($pts6Form && $pts6Form->status === 'in_progress')
                                                    <span class="shrink-0 bg-blue-100 text-blue-800 text-xs font-bold px-2.5 py-0.5 rounded whitespace-nowrap">In Progress</span>
                                                @else
                                                    <span class="shrink-0 bg-blue-100 text-blue-800 text-xs font-bold px-2.5 py-0.5 rounded whitespace-nowrap">Ready to Submit</span>
                                                @endif
                                            </div>
                                        </div>
                                        <p class="text-xs text-gray-500 dark:text-gray-400 leading-relaxed">
                                            Report of {{ $degreeType }} Thesis Oral Examination and Final Degree Recommendation.
                                        </p>
                                    </div>
                                    <div class="pt-2 border-t border-gray-200 dark:border-gray-600">
                                        @if(!$pts5Approved)
                                            <button disabled class="w-full text-center px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-400 dark:text-gray-500 font-bold text-xs rounded-lg cursor-not-allowed">
                                                Requires {{ $ptsPrefix }}-5 Approval
                                            </button>
                                        @elseif($pts6Form && $pts6Form->status === 'approved')
                                            <div class="flex items-center justify-between gap-2 pt-1">
                                                <span class="text-[11px] text-emerald-700 dark:text-emerald-300 font-bold">✓ {{ $ptsPrefix }}-6 Form Approved</span>
                                            </div>
                                        @else
                                            <button disabled class="w-full text-center px-4 py-2 bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 font-semibold text-xs rounded-lg border border-blue-200 dark:border-blue-800 cursor-not-allowed">
                                                Final Degree Award Stage
                                            </button>
                                        @endif
                                    </div>
                                </div>

                            </div>

                            <!-- Rejected Tab Content (Direct Rejected Forms) -->
                            <div x-show="activeTab === 'rejected'" class="space-y-4" style="display: none;">
                                @if($currentRejectedForms->isEmpty())
                                    <div class="text-center py-8 text-gray-500 dark:text-gray-400 text-sm bg-gray-50 dark:bg-gray-800/50 rounded-xl border border-dashed border-gray-200 dark:border-gray-700">
                                        No rejected {{ $ptsPrefix }} forms found for your current active thesis.
                                    </div>
                                @else
                                    <div class="overflow-x-auto rounded-xl border border-gray-200 dark:border-gray-700">
                                        <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400 min-w-[500px]">
                                            <thead class="text-xs text-gray-700 uppercase bg-gray-100 dark:bg-gray-700/80 dark:text-gray-300 border-b border-gray-200 dark:border-gray-600">
                                                <tr>
                                                    <th scope="col" class="px-4 py-3 font-extrabold whitespace-nowrap min-w-[160px]">Form Type</th>
                                                    <th scope="col" class="px-4 py-3 font-extrabold whitespace-nowrap min-w-[160px]">Timestamps</th>
                                                    <th scope="col" class="px-4 py-3 font-extrabold whitespace-nowrap min-w-[120px] text-center">Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                                                @foreach($currentRejectedForms as $form)
                                                    <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition">
                                                        <td class="px-4 py-3.5 font-bold text-gray-900 dark:text-white whitespace-nowrap">
                                                            @if($form instanceof \App\Models\Pts1Form)
                                                                {{ $ptsPrefix }}-1 (Open Seminar)
                                                            @elseif($form instanceof \App\Models\Pts2Form)
                                                                {{ $ptsPrefix }}-2 (Synopsis)
                                                            @elseif($form instanceof \App\Models\Pts2Extension)
                                                                {{ $ptsPrefix }}-2 Extension
                                                            @elseif($form instanceof \App\Models\Pts3Form)
                                                                {{ $ptsPrefix }}-3 (Examiners)
                                                            @elseif($form instanceof \App\Models\Pts4Extension)
                                                                {{ $ptsPrefix }}-4 Extension
                                                            @elseif($form instanceof \App\Models\Pts4Form)
                                                                {{ $ptsPrefix }}-4 (Thesis)
                                                            @elseif($form instanceof \App\Models\Pts5Form)
                                                                {{ $ptsPrefix }}-5
                                                            @elseif($form instanceof \App\Models\Pts6Form)
                                                                {{ $ptsPrefix }}-6
                                                            @else
                                                                {{ $ptsPrefix }} Form
                                                            @endif
                                                        </td>
                                                        <td class="px-4 py-3.5 whitespace-nowrap text-gray-700 dark:text-gray-300 font-medium">
                                                            @php
                                                                $modalTitle = match(true) {
                                                                    $form instanceof \App\Models\Pts1Form => $ptsPrefix . '-1 Submission Timeline',
                                                                    $form instanceof \App\Models\Pts2Form => $ptsPrefix . '-2 Submission Timeline',
                                                                    $form instanceof \App\Models\Pts2Extension => $ptsPrefix . '-2 Extension Submission Timeline',
                                                                    $form instanceof \App\Models\Pts3Form => $ptsPrefix . '-3 Submission Timeline',
                                                                    $form instanceof \App\Models\Pts4Extension => $ptsPrefix . '-4 Extension Submission Timeline',
                                                                    $form instanceof \App\Models\Pts4Form => $ptsPrefix . '-4 Submission Timeline',
                                                                    $form instanceof \App\Models\Pts5Form => $ptsPrefix . '-5 Submission Timeline',
                                                                    $form instanceof \App\Models\Pts6Form => $ptsPrefix . '-6 Submission Timeline',
                                                                    default => $ptsPrefix . ' Submission Timeline',
                                                                };
                                                            @endphp
                                                            <x-submission-timeline-modal :form="$form" :title="$modalTitle">
                                                                <x-slot name="trigger">
                                                                    <button type="button" class="inline-flex items-center px-3 py-1.5 bg-slate-100 dark:bg-slate-700 hover:bg-slate-200 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-200 font-bold text-xs rounded-lg shadow-sm transition border border-slate-200 dark:border-slate-600">
                                                                        <svg class="w-3.5 h-3.5 mr-1.5 text-slate-500 dark:text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                                        </svg>
                                                                        View Timestamps
                                                                    </button>
                                                                </x-slot>
                                                            </x-submission-timeline-modal>
                                                        </td>
                                                        <td class="px-4 py-3.5 whitespace-nowrap text-center">
                                                            @php
                                                                $formRoute = match(true) {
                                                                    $form instanceof \App\Models\Pts1Form => route('pts1.show', $form->id),
                                                                    $form instanceof \App\Models\Pts2Form => route('pts2.show', $form->id),
                                                                    $form instanceof \App\Models\Pts2Extension => route('pts2_extension.show', $form->id),
                                                                    $form instanceof \App\Models\Pts3Form => route('pts3.show', $form->id),
                                                                    $form instanceof \App\Models\Pts4Extension => route('pts4_extension.show', $form->id),
                                                                    default => '#'
                                                                };
                                                            @endphp
                                                            <a href="{{ $formRoute }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs rounded-lg shadow transition">
                                                                View Form &rarr;
                                                            </a>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
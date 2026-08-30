<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-bold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Thesis Management Portal') }}
            </h2>
            <div class="flex justify-between items-center">
                <span class="bg-blue-100 text-blue-800 text-xs font-medium px-3 py-1 rounded-full dark:bg-blue-900 dark:text-blue-300">
                    {{ $student->isPhd() ? 'PhD' : 'MS(R)' }} Student
                </span>
                <x-profile_dropdown/>
            </div>
        </div>
    </x-slot>

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
                                <div class="mt-0.5 text-gray-900 dark:text-gray-100 font-medium text-sm">{{ $student->isPhd() ? 'Ph.D.' : 'M.S. (Research)' }}</div>
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
                                    @if($student->externalSupervisors->isNotEmpty())
                                        {{ $student->externalSupervisors->map(fn($s) => $s->name . ($s->externalSupervisorProfile?->affiliated_institute ? ' (' . $s->externalSupervisorProfile->affiliated_institute . ')' : ''))->join(', ') }}
                                    @else
                                        None
                                    @endif
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
                            @if($activeThesis)
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
                                        <input type="text" name="title" disabled placeholder="Thesis submission in progress..." class="w-full rounded-xl border-gray-300 dark:border-gray-600 bg-gray-100 dark:bg-gray-700 text-gray-400 text-xs py-2.5 px-3 cursor-not-allowed opacity-60">
                                    </div>

                                    <button type="button" disabled class="w-full px-4 py-2.5 bg-gray-400 dark:bg-gray-600 text-white font-bold text-xs rounded-xl shadow cursor-not-allowed opacity-60">
                                        Re-initiation Disabled (Thesis In Progress)
                                    </button>
                                </form>
                            @elseif($student->theses->isNotEmpty())
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
                                        <input type="text" name="title" required value="{{ old('title') }}" placeholder="e.g. Deep Learning Architectures..." class="w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-xs py-2.5 px-3">
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
                                        <input type="text" name="title" required value="{{ old('title') }}" placeholder="e.g. Deep Learning Architectures..." class="w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-xs py-2.5 px-3">
                                    </div>

                                    <button type="submit" class="w-full px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs rounded-xl shadow transition">
                                        Register Thesis Title
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Right Main Content: PhD Academic Milestones Grid -->
                <div class="md:col-span-2 space-y-6">

                    @if(!$activeThesis)
                        <!-- NO THESIS REGISTERED NOTICE -->
                        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 space-y-4">
                            <div class="flex items-center space-x-3 border-gray-100 dark:border-gray-700 pb-3">
                                <div class="w-10 h-10 rounded-full bg-amber-100 dark:bg-amber-900 text-amber-700 dark:text-amber-300 flex items-center justify-center font-bold text-lg shrink-0">
                                    ⚠️
                                </div>
                                <div>
                                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">No {{ $student->isPhd() ? 'PhD' : 'MS(R)' }} Thesis Registered Yet</h3>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">Please enter your thesis title in the Thesis Action Center on the left sidebar to initiate your thesis submission.</p>
                                </div>
                            </div>
                        </div>
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
                                    {{ $student->isPhd() ? 'PhD Thesis Submission (PTS) Forms' : 'MS(R) Thesis Submission (MSRTS) Forms'  }}
                                </h3>
                                <span class="bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300 text-[11px] font-bold px-3 py-0.5 rounded-full shrink-0">
                                    {{ $activeThesis->current_status }}
                                </span>
                            </div>
                            
                            <!-- Tabs Navigation -->
                            <div class="flex border-b border-gray-200 dark:border-gray-700">
                                <button 
                                    @click="activeTab = 'in_progress'"
                                    :class="activeTab === 'in_progress' ? 'border-indigo-500 text-indigo-600 dark:text-indigo-400' : 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300'"
                                    class="w-1/2 py-2 px-1 text-center border-b-2 font-semibold text-sm transition focus:outline-none flex items-center justify-center space-x-1.5"
                                >
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    <span>Active / In Progress</span>
                                </button>
                                <button 
                                    @click="activeTab = 'rejected'"
                                    :class="activeTab === 'rejected' ? 'border-indigo-500 text-indigo-600 dark:text-indigo-400' : 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300'"
                                    class="w-1/2 py-2 px-1 text-center border-b-2 font-semibold text-sm transition focus:outline-none flex items-center justify-center space-x-1.5"
                                >
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    <span>Rejected History</span>
                                        <span class="ml-1.5 bg-red-100 text-red-800 text-[10px] font-bold px-1.5 py-0.5 rounded-full">
                                            {{ $rejectedForms->count() }}
                                        </span>
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
                                                    <span class="shrink-0 bg-indigo-100 text-indigo-800 text-xs font-bold px-2 py-0.5 rounded whitespace-nowrap">Circulated</span>
                                                @elseif($pts1Approved)
                                                    <span class="shrink-0 bg-gray-200 text-gray-700 text-xs font-bold px-2 py-0.5 rounded whitespace-nowrap">🔒 Disabled</span>
                                                @else
                                                    <span class="shrink-0 bg-blue-100 text-blue-800 text-xs font-bold px-2 py-0.5 rounded whitespace-nowrap">Ready to Circulate</span>
                                                @endif
                                            </div>
                                        </div>
                                        <p class="text-xs text-gray-500 dark:text-gray-400 leading-relaxed">
                                            Circulate draft synopsis report to all academic authorities for early comments before Open Seminar.
                                        </p>

                                        @if($draftSynopsis)
                                            <div class="p-2.5 bg-indigo-50 dark:bg-indigo-950/40 border-l-4 border-indigo-500 rounded-lg text-xs space-y-1 my-2">
                                                <div class="font-bold text-indigo-900 dark:text-indigo-200">
                                                    {{ $draftSynopsis->comments->count() }} {{ Str::plural('Comment', $draftSynopsis->comments->count()) }} Received
                                                </div>
                                            </div>
                                        @endif
                                    </div>

                                    <div class="pt-2 border-t border-gray-200 dark:border-gray-600">
                                        @if($draftSynopsis)
                                            <a href="{{ route('student.draft_synopsis.show') }}" class="block w-full text-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs rounded-lg shadow transition">
                                                View Circulated Synopsis & Comments &rarr;
                                            </a>
                                        @elseif($pts1Approved)
                                            <button type="button" disabled class="block w-full text-center px-4 py-2 bg-gray-300 dark:bg-gray-700 text-gray-500 font-bold text-xs rounded-lg cursor-not-allowed">
                                                No Draft Synopsis Circulated
                                            </button>
                                        @else
                                            <a href="{{ route('student.draft_synopsis.show') }}" class="block w-full text-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs rounded-lg shadow transition">
                                                Circulate Draft Synopsis Report &rarr;
                                            </a>
                                        @endif
                                    </div>
                                </div>
                                
                                <!-- PTS-1 Milestone Card (With Embedded Action Button) -->
                                <div class="p-2.5 sm:p-4 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700/30 space-y-3 flex flex-col justify-between">
                                    <div class="space-y-2">
                                        <div class="flex justify-between items-start gap-2">
                                            <span class="font-bold text-sm text-gray-900 dark:text-white leading-snug">{{ $student->isPhd() ? 'PTS' : 'MSRTS' }}-1: Open Seminar Report</span>
                                            <div class="flex items-center gap-1 shrink-0">
                                                <x-submission-timeline-modal :form="$pts1Form" title="PTS-1 Submission Timeline" />
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
                                                Create {{ $student->isPhd() ? 'PTS' : 'MSRTS' }}-1 Form &rarr;
                                            </a>
                                        @elseif($pts1Form->status === 'reverted')
                                            <div class="space-y-2">
                                                <a href="{{ route('student.pts1.edit') }}" class="block w-full text-center px-4 py-2 bg-amber-600 hover:bg-amber-700 text-white font-bold text-xs rounded-lg shadow transition">
                                                 Edit your {{ $student->isPhd() ? 'PTS' : 'MSRTS' }}-1 Form &rarr;
                                                </a>
                                            </div>
                                        @elseif($pts1Form->status === 'in_progress')
                                            <div class="flex items-center justify-between gap-2 pt-1">
                                                <div class="text-[11px] text-blue-700 dark:text-blue-300 font-semibold py-1 leading-tight break-words">
                                                    ⏳ Under Review Stage: {{ $pts1Form->stage_label }}
                                                </div>
                                                <a href="{{ route('pts1.submitted', $pts1Form->id) }}" class="inline-flex items-center px-3 py-1 bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs rounded-lg shadow transition shrink-0 whitespace-nowrap">
                                                    View Submitted &rarr;
                                                </a>
                                            </div>
                                        @elseif($pts1Form->status === 'approved')
                                            <div class="flex items-center justify-between gap-2 pt-1">
                                                <span class="text-[11px] text-emerald-700 dark:text-emerald-300 font-bold">✓ {{ $student->isPhd() ? 'PTS' : 'MSRTS' }}-1 Form Fully Approved</span>
                                                <a href="{{ route('pts1.show', $pts1Form->id) }}" class="inline-flex items-center px-3 py-1 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-lg shadow transition shrink-0 whitespace-nowrap">
                                                    View Submission &rarr;
                                                </a>
                                            </div>
                                        @elseif($pts1Form->status === 'rejected')
                                            <div class="space-y-2">
                                                <div class="flex items-center justify-between gap-2 pt-1">
                                                    <span class="text-[11px] text-red-700 dark:text-red-300 font-bold">❌ {{ $student->isPhd() ? 'PTS' : 'MSRTS' }}-1 Form Rejected</span>
                                                    <a href="{{ route('pts1.show', $pts1Form->id) }}" class="inline-flex items-center px-3 py-1 bg-red-600 hover:bg-red-700 text-white font-bold text-xs rounded-lg shadow transition shrink-0 whitespace-nowrap">
                                                        View Submission &rarr;
                                                    </a>
                                                </div>
                                                <div class="pt-2">
                                                    <a href="{{ route('student.pts1.create') }}" class="block w-full text-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs rounded-lg shadow transition">
                                                        Create New {{ $student->isPhd() ? 'PTS' : 'MSRTS' }}-1 Form &rarr;
                                                    </a>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                <!-- PTS-2 Milestone Card (With Embedded Action Button) -->
                                <div class="p-2.5 sm:p-4 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700/30 space-y-3 flex flex-col justify-between">
                                    <div class="space-y-2">
                                        <div class="flex justify-between items-start gap-2">
                                            <span class="font-bold text-sm text-gray-900 dark:text-white leading-snug">{{ $student->isPhd() ? 'PTS' : 'MSRTS' }}-2: Synopsis Report Submission</span>
                                            <div class="flex items-center gap-1 shrink-0">
                                                <x-submission-timeline-modal :form="$pts2Form" title="PTS-2 Submission Timeline" />
                                                @if(!$pts1Approved)
                                                    <span class="shrink-0 bg-gray-200 text-gray-700 text-xs font-bold px-2 py-0.5 rounded whitespace-nowrap">🔒 Locked</span>
                                                @elseif(!$pts2Form)
                                                    <span class="shrink-0 bg-purple-100 text-purple-800 text-xs font-bold px-2.5 py-0.5 rounded whitespace-nowrap">Ready to Submit</span>
                                                @elseif($pts2Form->status === 'approved')
                                                    <span class="shrink-0 bg-emerald-100 text-emerald-800 text-xs font-bold px-2.5 py-0.5 rounded whitespace-nowrap">Approved</span>
                                                @elseif($pts2Form->status === 'reverted')
                                                    <span class="shrink-0 bg-amber-100 text-amber-800 text-xs font-bold px-2.5 py-0.5 rounded whitespace-nowrap">Reverted</span>
                                                @else
                                                    <span class="shrink-0 bg-purple-100 text-purple-800 text-xs font-bold px-2.5 py-0.5 rounded whitespace-nowrap">In Progress</span>
                                                @endif
                                            </div>
                                        </div>
                                        <p class="text-xs text-gray-500 dark:text-gray-400 leading-relaxed">
                                            Submission and sequential endorsement of the {{ $student->isPhd() ? 'PhD' : 'MS(R)' }} Synopsis Report.
                                        </p>

                                        @if($pts2Extension)
                                            <div class="p-3 rounded-xl border text-xs my-2 space-y-2 {{ $pts2Extension->status === 'approved' ? 'bg-emerald-50 dark:bg-emerald-950/40 border-emerald-300 dark:border-emerald-800 text-emerald-900 dark:text-emerald-200' : ($pts2Extension->status === 'reverted' ? 'bg-amber-50 dark:bg-amber-950/40 border-amber-300 dark:border-amber-800 text-amber-900 dark:text-amber-200' : ($pts2Extension->status === 'rejected' ? 'bg-red-50 dark:bg-red-950/40 border-red-300 dark:border-red-800 text-red-900 dark:text-red-200' : 'bg-purple-50 dark:bg-purple-950/40 border-purple-300 dark:border-purple-800 text-purple-900 dark:text-purple-200')) }}">
                                                <div class="flex items-center justify-between font-bold">
                                                    <span>📅 PTS-2 Extension</span>
                                                    <div class="flex items-center gap-1">
                                                        <x-submission-timeline-modal :form="$pts2Extension" title="PTS-2 Extension Timeline" />
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
                                                        <span class="font-semibold text-purple-800 dark:text-purple-300 leading-tight break-words">⏳ Stage: {{ $pts2Extension->stage_label }}</span>
                                                        <a href="{{ route('pts2_extension.show', $pts2Extension->id) }}" class="underline font-bold hover:text-purple-600 shrink-0 whitespace-nowrap">View Submitted Form &rarr;</a>
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
                                                            ⚠️ Resubmit PTS-2 Extension &rarr;
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

                                        <!-- Apply for Extension Button (shown when PTS-1 is approved and no pending in_progress extension) -->
                                        @if($pts1Approved && (!$pts2Form || $pts2Form->status !== 'approved'))
                                            @if(!$pts2Extension || $pts2Extension->status === 'approved' || $pts2Extension->status === 'rejected')
                                                <div class="my-2 space-y-1">
                                                    @if($activeThesis->canApplyForPts2Extension())
                                                        <a href="{{ route('student.pts2_extension.create') }}" class="block w-full text-center px-3 py-1.5 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200 font-semibold text-xs rounded-lg transition border border-gray-300 dark:border-gray-600">
                                                            📅 Apply for PTS-2 (Synopsis) Extension &rarr;
                                                        </a>
                                                        <div class="text-[10px] text-gray-500 dark:text-gray-400 text-center">
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
                                                Requires {{ $student->isPhd() ? 'PTS' : 'MSRTS' }}-1 Approval
                                            </button>
                                        @elseif($pts2Form && $pts2Form->status === 'approved')
                                            <div class="flex items-center justify-between gap-2 pt-1">
                                                <span class="text-[11px] text-emerald-700 dark:text-emerald-300 font-bold">✓ {{ $student->isPhd() ? 'PTS' : 'MSRTS' }}-2 Form Approved</span>
                                                <a href="{{ route('pts2.show', $pts2Form->id) }}" class="inline-flex items-center px-3 py-1 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-lg shadow transition shrink-0 whitespace-nowrap">
                                                    View Submission &rarr;
                                                </a>
                                            </div>
                                        @elseif($pts2Form && $pts2Form->status === 'in_progress')
                                            <div class="flex items-center justify-between gap-2 pt-1">
                                                <div class="text-[11px] text-purple-700 dark:text-purple-300 font-semibold py-1 leading-tight break-words">
                                                    ⏳ Under Review Stage: {{ $pts2Form->stage_label }}
                                                </div>
                                                <a href="{{ route('pts2.submitted', $pts2Form->id) }}" class="inline-flex items-center px-3 py-1 bg-purple-600 hover:bg-purple-700 text-white font-bold text-xs rounded-lg shadow transition shrink-0 whitespace-nowrap">
                                                    View Submitted &rarr;
                                                </a>
                                            </div>
                                        @elseif($pts2Form && $pts2Form->status === 'rejected')
                                            <div class="space-y-2">
                                                <div class="flex items-center justify-between gap-2 pt-1">
                                                    <span class="text-[11px] text-red-700 dark:text-red-300 font-bold">❌ {{ $student->isPhd() ? 'PTS' : 'MSRTS' }}-2 Form Rejected</span>
                                                    <a href="{{ route('pts2.show', $pts2Form->id) }}" class="inline-flex items-center px-3 py-1 bg-red-600 hover:bg-red-700 text-white font-bold text-xs rounded-lg shadow transition shrink-0 whitespace-nowrap">
                                                        View Submission &rarr;
                                                    </a>
                                                </div>
                                                <div class="pt-2">
                                                    <a href="{{ route('student.pts2.create') }}" class="block w-full text-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs rounded-lg shadow transition whitespace-nowrap">
                                                        Create New {{ $student->isPhd() ? 'PTS' : 'MSRTS' }}-2 Form &rarr;
                                                    </a>
                                                </div>
                                            </div>
                                        @elseif($activeThesis->isPts2SubmissionActive())
                                            @php
                                                $pts2Deadline = $activeThesis->getPts2Deadline();
                                                $hasApprovedExt = $pts2Extension && $pts2Extension->status === 'approved';
                                            @endphp
                                            @if(!$pts2Form)
                                                <a href="{{ route('student.pts2.create') }}" class="block w-full text-center px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white font-bold text-xs rounded-lg shadow transition">
                                                    Create {{ $student->isPhd() ? 'PTS' : 'MSRTS' }}-2 Form &rarr;
                                                </a>
                                                <div class="text-[10px] text-purple-700 dark:text-purple-300 text-center font-medium">
                                                    ⏳ Submission Deadline: <strong>{{ $pts2Deadline?->format('d-M-Y') }}</strong> ({{ $hasApprovedExt ? 'Approved Extension' : '15 days from Open Seminar' }})
                                                </div>
                                            @elseif($pts2Form->status === 'reverted')
                                                <div class="space-y-2">
                                                    <a href="{{ route('student.pts2.edit') }}" class="block w-full text-center px-4 py-2 bg-amber-600 hover:bg-amber-700 text-white font-bold text-xs rounded-lg shadow transition">
                                                        Edit your {{ $student->isPhd() ? 'PTS' : 'MSRTS' }}-2 Form &rarr;
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
                                                PTS-2 submission window closed on {{ $pts2Deadline?->format('d-M-Y') }}.
                                                @if($activeThesis->canApplyForPts2Extension())
                                                    You may apply for an extension above.
                                                @endif
                                            </p>
                                        @endif
                                    </div>
                                </div>

                                <!-- PTS-4 Dummy Milestone Card (Thesis Submission) -->
                                <div class="p-2.5 sm:p-4 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-800/40 space-y-3 opacity-80 flex flex-col justify-between">
                                    <div class="space-y-2">
                                        <div class="flex justify-between items-start gap-2">
                                            <span class="font-bold text-sm text-gray-900 dark:text-white leading-snug">
                                                {{ $student->isPhd() ? 'PTS' : 'MSRTS' }}-4: Thesis Submission
                                            </span>
                                            <span class="shrink-0 bg-gray-200 text-gray-700 text-xs font-bold px-2 py-0.5 rounded whitespace-nowrap">🔒 Locked</span>
                                        </div>
                                        <p class="text-xs text-gray-500 dark:text-gray-400 leading-relaxed">
                                            Submission of the {{ $student->isPhd() ? 'PhD' : 'MS(R)' }} Thesis.
                                        </p>
                                    </div>
                                    <div class="pt-2 border-t border-gray-200 dark:border-gray-600">
                                        <button disabled class="w-full text-center px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-400 dark:text-gray-500 font-bold text-xs rounded-lg cursor-not-allowed">
                                            Unlocks after approval of {{ $student->isPhd() ? 'PTS' : 'MSRTS' }}-2
                                        </button>
                                    </div>
                                </div>

                                <!-- PTS-6 Dummy Milestone Card (Oral Examination Report) -->
                                <div class="p-2.5 sm:p-4 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-800/40 space-y-3 opacity-80 flex flex-col justify-between">
                                    <div class="space-y-2">
                                        <div class="flex justify-between items-start gap-2">
                                            <span class="font-bold text-sm text-gray-900 dark:text-white leading-snug">
                                                {{ $student->isPhd() ? 'PTS' : 'MSRTS' }}-6: Report of {{ $student->isPhd() ? 'PhD' : 'MS(R)' }} Thesis Oral Examination
                                            </span>
                                            <span class="shrink-0 bg-gray-200 text-gray-700 text-xs font-bold px-2 py-0.5 rounded whitespace-nowrap">🔒 Locked</span>
                                        </div>
                                        <p class="text-xs text-gray-500 dark:text-gray-400 leading-relaxed">
                                            Report of {{ $student->isPhd() ? 'PhD' : 'MS(R)' }} Thesis Oral Examination.
                                        </p>
                                    </div>
                                    <div class="pt-2 border-t border-gray-200 dark:border-gray-600">
                                        <button disabled class="w-full text-center px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-400 dark:text-gray-500 font-bold text-xs rounded-lg cursor-not-allowed">
                                            Final Stage
                                        </button>
                                    </div>
                                </div>

                            </div>

                            <!-- Rejected Tab Content -->
                            <div x-show="activeTab === 'rejected'" class="space-y-4" style="display: none;">
                                @if($rejectedForms->isEmpty())
                                    <div class="text-center py-8 text-gray-500 dark:text-gray-400 text-sm">
                                        No rejected PTS forms found for this thesis.
                                    </div>
                                @else
                                    <div class="overflow-x-auto rounded-xl border border-gray-200 dark:border-gray-700">
                                        <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400 min-w-[500px]">
                                            <thead class="text-xs text-gray-700 uppercase bg-gray-100 dark:bg-gray-700/80 dark:text-gray-300 border-b border-gray-200 dark:border-gray-600">
                                                <tr>
                                                    <th scope="col" class="px-4 py-4 font-extrabold whitespace-nowrap min-w-[160px]">Form Type</th>
                                                    <th scope="col" class="px-4 py-4 font-extrabold whitespace-nowrap min-w-[160px]">Timestamps</th>
                                                    <th scope="col" class="px-4 py-4 font-extrabold whitespace-nowrap min-w-[120px] text-center">Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                                                @foreach($rejectedForms as $form)
                                                    <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition">
                                                        <td class="px-4 py-4 font-bold text-gray-900 dark:text-white whitespace-nowrap">
                                                            @if($form instanceof \App\Models\Pts1Form)
                                                                {{ $student->isPhd() ? 'PTS' : 'MSRTS' }}-1 (Open Seminar)
                                                            @elseif($form instanceof \App\Models\Pts2Form)
                                                                {{ $student->isPhd() ? 'PTS' : 'MSRTS' }}-2 (Synopsis)
                                                            @else
                                                                {{ $student->isPhd() ? 'PTS' : 'MSRTS' }}-2 Extension
                                                            @endif
                                                        </td>
                                                        <td class="px-4 py-4 whitespace-nowrap text-gray-700 dark:text-gray-300 font-medium">
                                                            @php
                                                                $modalTitle = match(true) {
                                                                    $form instanceof \App\Models\Pts1Form => ($student->isPhd() ? 'PTS' : 'MSRTS') . '-1 Submission Timeline',
                                                                    $form instanceof \App\Models\Pts2Form => ($student->isPhd() ? 'PTS' : 'MSRTS') . '-2 Submission Timeline',
                                                                    $form instanceof \App\Models\Pts2Extension => ($student->isPhd() ? 'PTS' : 'MSRTS') . '-2 Extension Submission Timeline',
                                                                    default => ($student->isPhd() ? 'PTS' : 'MSRTS') . ' Submission Timeline',
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
                                                        <td class="px-4 py-4 whitespace-nowrap text-center">
                                                            @if($form instanceof \App\Models\Pts1Form)
                                                                <a href="{{ route('pts1.show', $form->id) }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs rounded-lg shadow transition">
                                                                    View Form &rarr;
                                                                </a>
                                                            @elseif($form instanceof \App\Models\Pts2Form)
                                                                <a href="{{ route('pts2.show', $form->id) }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs rounded-lg shadow transition">
                                                                    View Form &rarr;
                                                                </a>
                                                            @elseif($form instanceof \App\Models\Pts2Extension)
                                                                <a href="{{ route('pts2_extension.show', $form->id) }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs rounded-lg shadow transition">
                                                                    View Form &rarr;
                                                                </a>
                                                            @else
                                                                <a href="{{ route('pts2_extension.show', $form->id) }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs rounded-lg shadow transition">
                                                                    View Form &rarr;
                                                                </a>
                                                            @endif
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
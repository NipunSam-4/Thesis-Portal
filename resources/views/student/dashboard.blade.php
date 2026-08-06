<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-bold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Student Academic Portal') }}
            </h2>
            <span class="bg-indigo-100 text-indigo-800 text-xs font-medium px-3 py-1 rounded-full dark:bg-indigo-900 dark:text-indigo-300">
                PhD Scholar
            </span>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Success/Info Flash Alerts -->
            @if(session('success'))
                <div class="p-4 bg-emerald-100 border-l-4 border-emerald-500 text-emerald-800 rounded-lg shadow-sm font-semibold">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('info'))
                <div class="p-4 bg-blue-100 border-l-4 border-blue-500 text-blue-800 rounded-lg shadow-sm font-semibold">
                    {{ session('info') }}
                </div>
            @endif

            @if(session('warning'))
                <div class="p-4 bg-amber-100 border-l-4 border-amber-500 text-amber-800 rounded-lg shadow-sm font-semibold">
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
                        <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50">
                            <h3 class="font-bold text-gray-900 dark:text-white">My Academic Profile</h3>
                        </div>
                        <div class="p-6 space-y-4">
                            <div>
                                <label class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Roll Number</label>
                                <div class="mt-1 text-gray-900 dark:text-gray-100 font-medium font-mono">{{ $student->roll_number }}</div>
                            </div>
                            <div>
                                <label class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Department</label>
                                <div class="mt-1 text-gray-900 dark:text-gray-100 font-medium">{{ $student->department->name ?? 'Not Assigned' }}</div>
                            </div>
                            <div>
                                <label class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Supervisor(s)</label>
                                <div class="mt-1 text-gray-900 dark:text-gray-100 font-medium">{{ $latestThesis ? $latestThesis->supervisors->pluck('name')->join(', ') : 'Not Assigned' }}</div>
                            </div>
                        </div>
                    </div>

                    <!-- Form Action Center -->
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 space-y-4">
                        <h3 class="font-bold text-gray-900 dark:text-white border-b border-gray-100 dark:border-gray-700 pb-2">
                            Form Action Center
                        </h3>

                        @if(!$latestThesis)
                            <!-- Thesis Registration Form inside Action Center -->
                            <div class="space-y-3">
                                <div class="p-3 bg-indigo-50 dark:bg-indigo-900/30 border border-indigo-200 dark:border-indigo-800 rounded-xl">
                                    <div class="text-xs font-bold text-indigo-900 dark:text-indigo-200 mb-1 flex items-center">
                                        <span class="mr-1.5">📝</span> Step 1: Register Thesis Title
                                    </div>
                                    <p class="text-[11px] text-indigo-700 dark:text-indigo-300">
                                        Welcome! To unlock your Ph.D. Thesis Forms, please register your thesis title below.
                                    </p>
                                </div>

                                <form action="{{ route('student.thesis.store') }}" method="POST" class="space-y-3">
                                    @csrf
                                    <div>
                                        <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">
                                            Thesis Research Title <span class="text-red-500">*</span>
                                        </label>
                                        <input type="text" name="title" required value="{{ old('title') }}" placeholder="e.g. Deep Learning Architectures..." class="w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-xs py-2.5 px-3">
                                        <p class="text-[10px] text-gray-400 mt-1">This title will be associated with your Ph.D. profile and cannot be updated.</p>
                                    </div>

                                    <button type="submit" class="w-full px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs rounded-xl shadow transition">
                                        Register Thesis Title & Unlock PTS Forms
                                    </button>
                                </form>
                            </div>
                        @else
                            <!-- Action Center Stage Status Summaries when Thesis exists -->
                            <div class="space-y-3 text-xs">
                                <div>
                                    <div class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1">PTS-1 Status:</div>
                                    @if(!$pts1Form)
                                        <div class="p-2.5 bg-blue-50 dark:bg-blue-900/30 border border-blue-200 dark:border-blue-800 rounded-lg text-blue-700 dark:text-blue-300 font-semibold">
                                            Ready to Submit (See PTS-1 card on right)
                                        </div>
                                    @elseif($pts1Form->status === 'in_progress')
                                        <div class="p-2.5 bg-blue-50 dark:bg-blue-900/30 border border-blue-200 dark:border-blue-800 rounded-lg text-blue-700 dark:text-blue-300 font-semibold flex justify-between items-center">
                                            <span>Under Review</span>
                                            <span class="bg-blue-600 text-white text-[9px] uppercase px-1.5 py-0.5 rounded">In Progress</span>
                                        </div>
                                    @elseif($pts1Form->status === 'reverted')
                                        <div class="p-2.5 bg-amber-50 dark:bg-amber-950/40 border border-amber-200 dark:border-amber-900 rounded-lg text-amber-800 dark:text-amber-300 space-y-1">
                                            <div class="font-bold">⚠️ Reverted by {{ $pts1Form->getRevertedByRoleLabel() }}</div>
                                            @if($pts1Form->getReversionComment())
                                                <p class="italic text-[11px]">"{{ $pts1Form->getReversionComment() }}"</p>
                                            @endif
                                        </div>
                                    @elseif($pts1Form->status === 'accepted')
                                        <div class="p-2.5 bg-emerald-50 dark:bg-emerald-900/30 border border-emerald-200 dark:border-emerald-800 rounded-lg text-emerald-800 dark:text-emerald-300 font-bold flex justify-between items-center">
                                            <span>PTS-1 Approved</span>
                                            <span class="text-emerald-600">✓</span>
                                        </div>
                                    @endif
                                </div>

                                <div class="pt-2 border-t border-gray-100 dark:border-gray-700">
                                    <div class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1">PTS-2 Status:</div>
                                    @if(!$pts1Approved)
                                        <div class="p-2.5 bg-gray-50 dark:bg-gray-800/50 border border-gray-200 dark:border-gray-700 rounded-lg text-gray-500 text-center">
                                            🔒 Locked until PTS-1 Approved
                                        </div>
                                    @elseif(!$pts2Form)
                                        <div class="p-2.5 bg-purple-50 dark:bg-purple-900/30 border border-purple-200 dark:border-purple-800 rounded-lg text-purple-700 dark:text-purple-300 font-semibold">
                                            Ready to Submit (See PTS-2 card on right)
                                        </div>
                                    @elseif($pts2Form->status === 'in_progress')
                                        <div class="p-2.5 bg-purple-50 dark:bg-purple-900/30 border border-purple-200 dark:border-purple-800 rounded-lg text-purple-700 dark:text-purple-300 font-semibold flex justify-between items-center">
                                            <span>Under Review</span>
                                            <span class="bg-purple-600 text-white text-[9px] uppercase px-1.5 py-0.5 rounded">In Progress</span>
                                        </div>
                                    @elseif($pts2Form->status === 'reverted')
                                        <div class="p-2.5 bg-amber-50 dark:bg-amber-950/40 border border-amber-200 dark:border-amber-900 rounded-lg text-amber-800 dark:text-amber-300 space-y-1">
                                            <div class="font-bold">⚠️ Reverted by {{ $pts2Form->getRevertedByRoleLabel() }}</div>
                                            @if($pts2Form->getReversionComment())
                                                <p class="italic text-[11px]">"{{ $pts2Form->getReversionComment() }}"</p>
                                            @endif
                                        </div>
                                    @elseif($pts2Form->status === 'accepted')
                                        <div class="p-2.5 bg-emerald-50 dark:bg-emerald-900/30 border border-emerald-200 dark:border-emerald-800 rounded-lg text-emerald-800 dark:text-emerald-300 font-bold flex justify-between items-center">
                                            <span>PTS-2 Approved</span>
                                            <span class="text-emerald-600">✓</span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Right Main Content: Ph.D. Academic Milestones Grid -->
                <div class="md:col-span-2 space-y-6">

                    @if(!$latestThesis)
                        <!-- NO THESIS REGISTERED NOTICE -->
                        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 space-y-4">
                            <div class="flex items-center space-x-3 border-b border-gray-100 dark:border-gray-700 pb-3">
                                <div class="w-10 h-10 rounded-full bg-amber-100 dark:bg-amber-900 text-amber-700 dark:text-amber-300 flex items-center justify-center font-bold text-lg shrink-0">
                                    ⚠️
                                </div>
                                <div>
                                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">No Ph.D. Thesis Registered Yet</h3>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">Please enter your research title in the Form Action Center on the left sidebar to activate your PTS milestone forms.</p>
                                </div>
                            </div>

                            <div class="space-y-4 opacity-60">
                                <h4 class="font-bold text-sm text-gray-900 dark:text-white">Ph.D. Thesis Submission (PTS) Forms</h4>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div class="p-4 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700/30 space-y-2">
                                        <div class="flex justify-between items-start gap-2">
                                            <span class="font-bold text-sm text-gray-900 dark:text-white leading-snug">PTS-1: Open Seminar</span>
                                            <span class="shrink-0 bg-amber-100 text-amber-800 text-[10px] font-bold px-2 py-0.5 rounded whitespace-nowrap">🔒 Locked</span>
                                        </div>
                                        <p class="text-xs text-gray-500">Submission of open seminar details, publication norm, draft synopsis report and minimum time requirement.</p>
                                    </div>

                                    <div class="p-4 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700/30 space-y-2">
                                        <div class="flex justify-between items-start gap-2">
                                            <span class="font-bold text-sm text-gray-900 dark:text-white leading-snug">PTS-2: Synopsis Report</span>
                                            <span class="shrink-0 bg-amber-100 text-amber-800 text-[10px] font-bold px-2 py-0.5 rounded whitespace-nowrap">🔒 Locked</span>
                                        </div>
                                        <p class="text-xs text-gray-500">Submission and sequential endorsement of the Ph.D. Synopsis Report.</p>
                                    </div>

                                    <div class="p-4 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700/30 space-y-2">
                                        <div class="flex justify-between items-start gap-2">
                                            <span class="font-bold text-sm text-gray-900 dark:text-white leading-snug">PTS-4: Thesis Submission</span>
                                            <span class="shrink-0 bg-amber-100 text-amber-800 text-[10px] font-bold px-2 py-0.5 rounded whitespace-nowrap">🔒 Locked</span>
                                        </div>
                                        <p class="text-xs text-gray-500">Submission of the Ph.D. Thesis.</p>
                                    </div>

                                    <div class="p-4 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700/30 space-y-2">
                                        <div class="flex justify-between items-start gap-2">
                                            <span class="font-bold text-sm text-gray-900 dark:text-white leading-snug">PTS-6: Report of Ph.D. Thesis Oral Examination</span>
                                            <span class="shrink-0 bg-amber-100 text-amber-800 text-[10px] font-bold px-2 py-0.5 rounded whitespace-nowrap">🔒 Locked</span>
                                        </div>
                                        <p class="text-xs text-gray-500">Report of Phd Thesis Oral Examination.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @else
                        <!-- THESIS REGISTERED MAIN MILESTONE CONTAINER -->
                        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 space-y-4">
                            
                            <!-- Registered Thesis Title Header directly above Milestones -->
                            <div class="border-b border-gray-100 dark:border-gray-700 pb-4">
                                <span class="text-[10px] font-bold text-indigo-600 dark:text-indigo-400 uppercase tracking-wider">Registered Research Thesis Title</span>
                                <h2 class="text-lg font-bold text-gray-900 dark:text-white mt-1 leading-snug">
                                    {{ $latestThesis->title }}
                                </h2>
                                <div class="flex items-center justify-between mt-2 text-xs">
                                    <span class="text-gray-500">Supervisors: {{ $latestThesis->supervisors->pluck('name')->join(', ') ?: 'Assigned during review' }}</span>
                                    <span class="bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300 text-[11px] font-bold px-3 py-0.5 rounded-full shrink-0">
                                        {{ $latestThesis->current_status }}
                                    </span>
                                </div>
                            </div>

                            <h3 class="font-bold text-gray-900 dark:text-white text-base pt-1">
                                Ph.D. Thesis Submission (PTS) Forms Status
                            </h3>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                
                                <!-- PTS-1 Milestone Card (With Embedded Action Button) -->
                                <div class="p-4 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700/30 space-y-3 flex flex-col justify-between">
                                    <div class="space-y-2">
                                        <div class="flex justify-between items-start gap-2">
                                            <span class="font-bold text-sm text-gray-900 dark:text-white leading-snug">PTS-1: Open Seminar</span>
                                            @if(!$pts1Form)
                                                <span class="shrink-0 bg-blue-100 text-blue-800 text-xs font-bold px-2 py-0.5 rounded whitespace-nowrap">Ready to Submit</span>
                                            @elseif($pts1Form->status === 'accepted')
                                                <span class="shrink-0 bg-emerald-100 text-emerald-800 text-xs font-bold px-2 py-0.5 rounded whitespace-nowrap">Approved</span>
                                            @elseif($pts1Form->status === 'reverted')
                                                <span class="shrink-0 bg-amber-100 text-amber-800 text-xs font-bold px-2 py-0.5 rounded whitespace-nowrap">Reverted</span>
                                            @else
                                                <span class="shrink-0 bg-blue-100 text-blue-800 text-xs font-bold px-2 py-0.5 rounded whitespace-nowrap">In Progress</span>
                                            @endif
                                        </div>
                                        <p class="text-xs text-gray-500 dark:text-gray-400 leading-relaxed">
                                            Submission of open seminar details, publication norm, draft synopsis report and minimum time requirement.
                                        </p>
                                    </div>

                                    <div class="pt-2 border-t border-gray-200 dark:border-gray-600">
                                        @if(!$pts1Form)
                                            <a href="{{ route('student.pts1.create') }}" class="block w-full text-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs rounded-lg shadow transition">
                                                Create PTS-1 Form &rarr;
                                            </a>
                                        @elseif($pts1Form->status === 'reverted')
                                            <a href="{{ route('student.pts1.create') }}" class="block w-full text-center px-4 py-2 bg-amber-600 hover:bg-amber-700 text-white font-bold text-xs rounded-lg shadow transition">
                                                Create New PTS-1 Form &rarr;
                                            </a>
                                        @elseif($pts1Form->status === 'in_progress')
                                            <div class="text-[11px] text-blue-700 dark:text-blue-300 font-semibold text-center py-1">
                                                ⏳ Under Review Stage: {{ str_replace('_', ' ', $pts1Form->current_stage) }}
                                            </div>
                                        @elseif($pts1Form->status === 'accepted')
                                            <div class="text-[11px] text-emerald-700 dark:text-emerald-300 font-bold text-center py-1 flex items-center justify-center">
                                                <span>✓ PTS-1 Form Fully Approved</span>
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                <!-- PTS-2 Milestone Card (With Embedded Action Button) -->
                                <div class="p-4 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700/30 space-y-3 flex flex-col justify-between">
                                    <div class="space-y-2">
                                        <div class="flex justify-between items-start gap-2">
                                            <span class="font-bold text-sm text-gray-900 dark:text-white leading-snug">PTS-2: Synopsis Report</span>
                                            @if(!$pts1Approved)
                                                <span class="shrink-0 bg-gray-200 text-gray-700 text-xs font-bold px-2 py-0.5 rounded whitespace-nowrap">🔒 Locked</span>
                                            @elseif(!$pts2Form)
                                                <span class="shrink-0 bg-purple-100 text-purple-800 text-xs font-bold px-2 py-0.5 rounded whitespace-nowrap">Ready to Submit</span>
                                            @elseif($pts2Form->status === 'accepted')
                                                <span class="shrink-0 bg-emerald-100 text-emerald-800 text-xs font-bold px-2 py-0.5 rounded whitespace-nowrap">Approved</span>
                                            @elseif($pts2Form->status === 'reverted')
                                                <span class="shrink-0 bg-amber-100 text-amber-800 text-xs font-bold px-2 py-0.5 rounded whitespace-nowrap">Reverted</span>
                                            @else
                                                <span class="shrink-0 bg-purple-100 text-purple-800 text-xs font-bold px-2 py-0.5 rounded whitespace-nowrap">In Progress</span>
                                            @endif
                                        </div>
                                        <p class="text-xs text-gray-500 dark:text-gray-400 leading-relaxed">
                                            Submission and sequential endorsement of the Ph.D. Synopsis Report.
                                        </p>
                                    </div>

                                    <div class="pt-2 border-t border-gray-200 dark:border-gray-600">
                                        @if(!$pts1Approved)
                                            <button disabled class="w-full text-center px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-400 dark:text-gray-500 font-bold text-xs rounded-lg cursor-not-allowed">
                                                🔒 Locked (Requires PTS-1 Approval)
                                            </button>
                                        @elseif(!$pts2Form)
                                            <a href="{{ route('student.pts2.create') }}" class="block w-full text-center px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white font-bold text-xs rounded-lg shadow transition">
                                                Create PTS-2 Synopsis Form &rarr;
                                            </a>
                                        @elseif($pts2Form->status === 'reverted')
                                            <a href="{{ route('student.pts2.create') }}" class="block w-full text-center px-4 py-2 bg-amber-600 hover:bg-amber-700 text-white font-bold text-xs rounded-lg shadow transition">
                                                Resubmit PTS-2 Synopsis Form &rarr;
                                            </a>
                                        @elseif($pts2Form->status === 'in_progress')
                                            <div class="text-[11px] text-purple-700 dark:text-purple-300 font-semibold text-center py-1">
                                                ⏳ Under Review Stage: {{ str_replace('_', ' ', $pts2Form->current_stage) }}
                                            </div>
                                        @elseif($pts2Form->status === 'accepted')
                                            <div class="text-[11px] text-emerald-700 dark:text-emerald-300 font-bold text-center py-1 flex items-center justify-center">
                                                <span>✓ PTS-2 Form Fully Approved</span>
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                <!-- PTS-4 Dummy Milestone Card (Thesis Submission) -->
                                <div class="p-4 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-800/40 space-y-3 opacity-80 flex flex-col justify-between">
                                    <div class="space-y-2">
                                        <div class="flex justify-between items-start gap-2">
                                            <span class="font-bold text-sm text-gray-900 dark:text-white leading-snug">
                                                PTS-4: Thesis Submission
                                            </span>
                                            <span class="shrink-0 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 text-[10px] font-bold px-2 py-0.5 rounded whitespace-nowrap">
                                                🔒 Locked
                                            </span>
                                        </div>
                                        <p class="text-xs text-gray-500 dark:text-gray-400 leading-relaxed">
                                            Submission of the Ph.D. Thesis.
                                        </p>
                                    </div>
                                    <div class="pt-2 border-t border-gray-200 dark:border-gray-600">
                                        <button disabled class="w-full text-center px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-400 dark:text-gray-500 font-bold text-xs rounded-lg cursor-not-allowed">
                                            🔒 Locked (Unlocks after approval of PTS-2)
                                        </button>
                                    </div>
                                </div>

                                <!-- PTS-6 Dummy Milestone Card (Oral Examination Report) -->
                                <div class="p-4 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-800/40 space-y-3 opacity-80 flex flex-col justify-between">
                                    <div class="space-y-2">
                                        <div class="flex justify-between items-start gap-2">
                                            <span class="font-bold text-sm text-gray-900 dark:text-white leading-snug">
                                                PTS-6: Report of Ph.D. Thesis Oral Examination
                                            </span>
                                            <span class="shrink-0 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 text-[10px] font-bold px-2 py-0.5 rounded whitespace-nowrap">
                                                🔒 Locked
                                            </span>
                                        </div>
                                        <p class="text-xs text-gray-500 dark:text-gray-400 leading-relaxed">
                                            Report of Phd Thesis Oral Examination.
                                        </p>
                                    </div>
                                    <div class="pt-2 border-t border-gray-200 dark:border-gray-600">
                                        <button disabled class="w-full text-center px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-400 dark:text-gray-500 font-bold text-xs rounded-lg cursor-not-allowed">
                                            🔒 Locked (Final Stage)
                                        </button>
                                    </div>
                                </div>

                            </div>
                        </div>
                    @endif

                </div>
            </div>

        </div>
    </div>
</x-app-layout>
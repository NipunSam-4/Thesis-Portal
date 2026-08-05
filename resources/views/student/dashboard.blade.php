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
            
            <div class="bg-gradient-to-r from-indigo-600 to-purple-700 rounded-xl shadow-sm p-6 text-white flex items-center justify-between">
                <div>
                    <h2 class="text-2xl font-bold mb-1">Welcome, {{ Auth::user()->name }}</h2>
                    <p class="text-indigo-100 text-sm">Track your thesis progress and manage your submissions here.</p>
                </div>
                <div class="hidden md:block p-3 bg-white/10 rounded-lg">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"></path></svg>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                
                <!-- Left Sidebar: Profile & Quick Form Actions -->
                <div class="md:col-span-1 space-y-6">
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

                    <!-- Progressive Form Submission Module -->
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 space-y-4">
                        <h3 class="font-bold text-gray-900 dark:text-white border-b border-gray-100 dark:border-gray-700 pb-2">
                            Form Action Center
                        </h3>
                        
                        <!-- PTS-1 Submission Action -->
                        <div>
                            @if(!$pts1Form)
                                <a href="{{ route('student.pts1.create') }}" class="w-full flex items-center justify-center px-4 py-2.5 rounded-xl shadow text-sm font-bold text-white bg-blue-600 hover:bg-blue-700 transition">
                                    Create PTS-1 Form
                                </a>
                            @elseif($pts1Form->status === 'in_progress')
                                <div class="p-3 bg-blue-50 dark:bg-blue-900/30 border border-blue-200 dark:border-blue-800 rounded-xl">
                                    <div class="text-xs text-blue-700 dark:text-blue-300 font-semibold flex items-center justify-between">
                                        <span>PTS-1 Form Status</span>
                                        <span class="bg-blue-600 text-white text-[10px] uppercase font-bold px-2 py-0.5 rounded">In Progress</span>
                                    </div>
                                    <p class="text-xs text-blue-600 dark:text-blue-400 mt-1">Your PTS-1 form is currently undergoing sequential review.</p>
                                </div>
                            @elseif($pts1Form->status === 'reverted')
                                <div class="p-4 bg-amber-50 dark:bg-amber-950/40 border-l-4 border-amber-500 rounded-xl space-y-3 shadow-sm">
                                    <div class="flex items-center justify-between">
                                        <span class="text-xs text-amber-900 dark:text-amber-300 font-bold uppercase tracking-wider flex items-center">
                                            <svg class="w-4 h-4 mr-1.5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                                            PTS-1 Reverted by {{ $pts1Form->getRevertedByRoleLabel() }}
                                        </span>
                                        <span class="bg-amber-600 text-white text-[10px] uppercase font-bold px-2 py-0.5 rounded">Reverted</span>
                                    </div>
                                    
                                    @if($pts1Form->getReversionComment())
                                        <div class="p-3 bg-white dark:bg-gray-800 rounded-lg border border-amber-200 dark:border-amber-900/50">
                                            <div class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1">Reversion Reason / Feedback:</div>
                                            <p class="text-xs text-gray-800 dark:text-gray-200 italic leading-relaxed">
                                                "{{ $pts1Form->getReversionComment() }}"
                                            </p>
                                        </div>
                                    @endif

                                    <p class="text-xs text-amber-800 dark:text-amber-300">
                                        Please address the feedback above and submit a new PTS-1 form to restart the review workflow.
                                    </p>
                                    <a href="{{ route('student.pts1.create') }}" class="block text-center w-full px-4 py-2 rounded-lg text-xs font-bold text-white bg-amber-600 hover:bg-amber-700 shadow transition">
                                        Create New PTS-1 Form
                                    </a>
                                </div>
                            @elseif($pts1Form->status === 'accepted')
                                <div class="p-3 bg-emerald-50 dark:bg-emerald-900/30 border border-emerald-200 dark:border-emerald-800 rounded-xl">
                                    <div class="text-xs text-emerald-800 dark:text-emerald-300 font-bold flex items-center justify-between">
                                        <span>PTS-1 Form Status</span>
                                        <span class="bg-emerald-600 text-white text-[10px] uppercase font-bold px-2 py-0.5 rounded">Approved</span>
                                    </div>
                                </div>
                            @endif
                        </div>

                        <!-- PTS-2 Submission Action -->
                        <div class="pt-2 border-t border-gray-100 dark:border-gray-700">
                            @if($pts1Approved)
                                @if(!$pts2Form)
                                    <a href="{{ route('student.pts2.create') }}" class="w-full flex items-center justify-center px-4 py-2.5 rounded-xl shadow text-sm font-bold text-white bg-purple-600 hover:bg-purple-700 transition">
                                        Create PTS-2 Synopsis Form
                                    </a>
                                @elseif($pts2Form->status === 'in_progress')
                                    <div class="p-3 bg-purple-50 dark:bg-purple-900/30 border border-purple-200 dark:border-purple-800 rounded-xl">
                                        <div class="text-xs text-purple-700 dark:text-purple-300 font-semibold flex items-center justify-between">
                                            <span>PTS-2 Form Status</span>
                                            <span class="bg-purple-600 text-white text-[10px] uppercase font-bold px-2 py-0.5 rounded">In Progress</span>
                                        </div>
                                        <p class="text-xs text-purple-600 dark:text-purple-400 mt-1">Your PTS-2 Synopsis Form is undergoing sequential review.</p>
                                    </div>
                                @elseif($pts2Form->status === 'reverted')
                                    <div class="p-4 bg-amber-50 dark:bg-amber-950/40 border-l-4 border-amber-500 rounded-xl space-y-3 shadow-sm">
                                        <div class="flex items-center justify-between">
                                            <span class="text-xs text-amber-900 dark:text-amber-300 font-bold uppercase tracking-wider flex items-center">
                                                ⚠️ PTS-2 Reverted by {{ $pts2Form->getRevertedByRoleLabel() }}
                                            </span>
                                            <span class="bg-amber-600 text-white text-[10px] uppercase font-bold px-2 py-0.5 rounded">Reverted</span>
                                        </div>

                                        @if($pts2Form->getReversionComment())
                                            <div class="p-3 bg-white dark:bg-gray-800 rounded-lg border border-amber-200 dark:border-amber-900/50">
                                                <div class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1">Reversion Reason / Feedback:</div>
                                                <p class="text-xs text-gray-800 dark:text-gray-200 italic leading-relaxed">
                                                    "{{ $pts2Form->getReversionComment() }}"
                                                </p>
                                            </div>
                                        @endif

                                        <a href="{{ route('student.pts2.create') }}" class="block text-center w-full px-4 py-2 rounded-lg text-xs font-bold text-white bg-amber-600 hover:bg-amber-700 shadow transition">
                                            Resubmit PTS-2 Synopsis Form
                                        </a>
                                    </div>
                                @elseif($pts2Form->status === 'accepted')
                                    <div class="p-3 bg-emerald-50 dark:bg-emerald-900/30 border border-emerald-200 dark:border-emerald-800 rounded-xl">
                                        <div class="text-xs text-emerald-800 dark:text-emerald-300 font-bold flex items-center justify-between">
                                            <span>PTS-2 Form Status</span>
                                            <span class="bg-emerald-600 text-white text-[10px] uppercase font-bold px-2 py-0.5 rounded">Approved</span>
                                        </div>
                                    </div>
                                @endif
                            @else
                                <div class="p-3 bg-gray-50 dark:bg-gray-800/50 border border-gray-200 dark:border-gray-700 rounded-xl text-center">
                                    <p class="text-xs text-gray-500 dark:text-gray-400">
                                        🔒 PTS-2 Form submission will unlock after PTS-1 Form is approved.
                                    </p>
                                </div>
                            @endif
                        </div>

                    </div>
                </div>

                <!-- Right Main Content: Active Thesis & Form Overview -->
                <div class="md:col-span-2 space-y-6">

                    <!-- Milestone Progress Overview -->
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 space-y-4">
                        <h3 class="font-bold text-gray-900 dark:text-white border-b border-gray-100 dark:border-gray-700 pb-2">
                            Ph.D. Academic Status
                        </h3>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- PTS-1 Milestone Card -->
                            <div class="p-4 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700/30 space-y-2">
                                <div class="flex justify-between items-center">
                                    <span class="font-bold text-sm text-gray-900 dark:text-white">PTS-1: Open Seminar</span>
                                    @if(!$pts1Form)
                                        <span class="bg-gray-200 text-gray-700 text-xs font-bold px-2 py-0.5 rounded">Not Started</span>
                                    @elseif($pts1Form->status === 'accepted')
                                        <span class="bg-emerald-100 text-emerald-800 text-xs font-bold px-2 py-0.5 rounded">Approved</span>
                                    @elseif($pts1Form->status === 'reverted')
                                        <span class="bg-amber-100 text-amber-800 text-xs font-bold px-2 py-0.5 rounded">Reverted</span>
                                    @else
                                        <span class="bg-blue-100 text-blue-800 text-xs font-bold px-2 py-0.5 rounded">In Progress</span>
                                    @endif
                                </div>
                            </div>

                            <!-- PTS-2 Milestone Card -->
                            <div class="p-4 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700/30 space-y-2">
                                <div class="flex justify-between items-center">
                                    <span class="font-bold text-sm text-gray-900 dark:text-white">PTS-2: Synopsis Report</span>
                                    @if(!$pts1Approved)
                                        <span class="bg-gray-200 text-gray-700 text-xs font-bold px-2 py-0.5 rounded">🔒 Locked</span>
                                    @elseif(!$pts2Form)
                                        <span class="bg-purple-100 text-purple-800 text-xs font-bold px-2 py-0.5 rounded">Ready to Submit</span>
                                    @elseif($pts2Form->status === 'accepted')
                                        <span class="bg-emerald-100 text-emerald-800 text-xs font-bold px-2 py-0.5 rounded">Approved</span>
                                    @elseif($pts2Form->status === 'reverted')
                                        <span class="bg-amber-100 text-amber-800 text-xs font-bold px-2 py-0.5 rounded">Reverted</span>
                                    @else
                                        <span class="bg-purple-100 text-purple-800 text-xs font-bold px-2 py-0.5 rounded">In Progress</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

        </div>
    </div>
</x-app-layout>
<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-bold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Faculty Academic Portal') }}
            </h2>
            <span class="bg-blue-100 text-blue-800 text-xs font-medium px-3 py-1 rounded-full dark:bg-blue-900 dark:text-blue-300">
                Faculty Member
            </span>
        </div>
    </x-slot>

    <div class="py-8" x-data="{
        activeTab: 'main',
        expandedStudent: null,
        toggleStudent(id) {
            this.expandedStudent = (this.expandedStudent === id) ? null : id;
        }
    }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Success/Warning Flash Alerts -->
            @if(session('success'))
                <div class="p-4 bg-emerald-100 border-l-4 border-emerald-500 text-emerald-800 rounded-lg shadow-sm font-semibold">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('warning'))
                <div class="p-4 bg-amber-100 border-l-4 border-amber-500 text-amber-800 rounded-lg shadow-sm font-semibold">
                    {{ session('warning') }}
                </div>
            @endif

            <!-- Welcome Banner -->
            <div class="bg-gradient-to-r from-blue-600 to-indigo-700 rounded-xl shadow-sm p-6 text-white flex items-center justify-between">
                <div>
                    <h2 class="text-2xl font-bold mb-1">Welcome, {{ $user->name }}</h2>
                    <p class="text-blue-100 text-sm">Select a role tab below to inspect your assigned scholars, their registered theses, and PTS milestone progress.</p>
                </div>
                <div class="hidden md:block p-3 bg-white/10 rounded-lg">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"></path></svg>
                </div>
            </div>

            <!-- Role-Based Navigation Tabs -->
            <div class="flex flex-wrap gap-2 border-b border-gray-200 dark:border-gray-700 pb-2">
                <button type="button" @click="activeTab = 'main'" :class="activeTab === 'main' ? 'bg-indigo-600 text-white shadow-md' : 'bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 border border-gray-200 dark:border-gray-700 hover:bg-gray-50'" class="px-5 py-2.5 rounded-xl font-bold text-sm transition flex items-center">
                    👤 Main Supervisor
                    <span class="ml-2 text-xs px-2 py-0.5 rounded-full" :class="activeTab === 'main' ? 'bg-white/20 text-white' : 'bg-indigo-100 text-indigo-800 dark:bg-indigo-900 dark:text-indigo-300'">
                        {{ $mainStudents->count() }}
                    </span>
                </button>

                <button type="button" @click="activeTab = 'co'" :class="activeTab === 'co' ? 'bg-blue-600 text-white shadow-md' : 'bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 border border-gray-200 dark:border-gray-700 hover:bg-gray-50'" class="px-5 py-2.5 rounded-xl font-bold text-sm transition flex items-center">
                    👥 Co-Supervisor
                    <span class="ml-2 text-xs px-2 py-0.5 rounded-full" :class="activeTab === 'co' ? 'bg-white/20 text-white' : 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300'">
                        {{ $coStudents->count() }}
                    </span>
                </button>

                <button type="button" @click="activeTab = 'pspc'" :class="activeTab === 'pspc' ? 'bg-purple-600 text-white shadow-md' : 'bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 border border-gray-200 dark:border-gray-700 hover:bg-gray-50'" class="px-5 py-2.5 rounded-xl font-bold text-sm transition flex items-center">
                    🔬 PSPC Member
                    <span class="ml-2 text-xs px-2 py-0.5 rounded-full" :class="activeTab === 'pspc' ? 'bg-white/20 text-white' : 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-300'">
                        {{ $pspcStudents->count() }}
                    </span>
                </button>
            </div>

            <!-- TAB 1: MAIN SUPERVISOR SCHOLARS -->
            <div x-show="activeTab === 'main'" x-cloak class="space-y-4">
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4 border-b border-gray-100 dark:border-gray-700 pb-2">
                        Scholars Supervised as Main Supervisor ({{ $mainStudents->count() }})
                    </h3>

                    @forelse($mainStudents as $student)
                        <div class="mb-4 border border-gray-200 dark:border-gray-700 rounded-xl overflow-hidden shadow-sm bg-gray-50/50 dark:bg-gray-800/50">
                            <!-- Student Header Card -->
                            <div @click="toggleStudent('main-{{ $student->id }}')" class="p-4 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-750 cursor-pointer flex flex-col md:flex-row md:items-center justify-between gap-4 transition">
                                <div class="flex items-center space-x-3">
                                    <div class="w-10 h-10 rounded-full bg-indigo-100 dark:bg-indigo-900 text-indigo-700 dark:text-indigo-300 flex items-center justify-center font-bold text-base border border-indigo-200 dark:border-indigo-800">
                                        {{ substr($student->user->name ?? 'S', 0, 1) }}
                                    </div>
                                    <div>
                                        <h4 class="font-bold text-gray-900 dark:text-white text-base flex items-center">
                                            {{ $student->user->name }}
                                            <span class="ml-2 text-xs font-mono font-medium text-gray-500 bg-gray-100 dark:bg-gray-700 px-2 py-0.5 rounded">
                                                {{ $student->roll_number }}
                                            </span>
                                        </h4>
                                        <p class="text-xs text-gray-500 mt-0.5">
                                            Dept: {{ $student->department->name ?? 'N/A' }} | Joining: {{ $student->date_joining }}
                                        </p>
                                    </div>
                                </div>

                                <div class="flex items-center space-x-3 text-xs font-bold">
                                    <span class="text-indigo-600 dark:text-indigo-400">
                                        {{ $student->theses->count() }} Registered Thesis(es)
                                    </span>
                                    <svg class="w-5 h-5 text-gray-400 transform transition-transform" :class="expandedStudent === 'main-{{ $student->id }}' ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                </div>
                            </div>

                            <!-- Expandable Student Theses & PTS Form Breakdown -->
                            <div x-show="expandedStudent === 'main-{{ $student->id }}'" x-cloak class="p-5 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 space-y-5">
                                @foreach($student->theses as $thesis)
                                    <div class="p-4 bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 space-y-4">
                                        <div class="border-b border-gray-100 dark:border-gray-700 pb-2">
                                            <div class="text-[10px] uppercase font-bold text-indigo-600 dark:text-indigo-400">Thesis Title</div>
                                            <h5 class="font-bold text-base text-gray-900 dark:text-white">{{ $thesis->title }}</h5>
                                        </div>

                                        <!-- PTS Milestone Forms Breakdown -->
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                            <!-- PTS-1 Card -->
                                            <div class="p-4 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700/30 space-y-3">
                                                <div class="flex justify-between items-center">
                                                    <span class="font-bold text-sm text-gray-900 dark:text-white">PTS-1 (Open Seminar)</span>
                                                    @if($thesis->pts1Form)
                                                        @if($thesis->pts1Form->status === 'in_progress')
                                                            <span class="bg-blue-100 text-blue-800 text-xs font-bold px-2.5 py-0.5 rounded">In Progress</span>
                                                        @elseif($thesis->pts1Form->status === 'reverted')
                                                            <span class="bg-amber-100 text-amber-800 text-xs font-bold px-2.5 py-0.5 rounded">Reverted by {{ $thesis->pts1Form->getRevertedByRoleLabel() }}</span>
                                                        @elseif($thesis->pts1Form->status === 'accepted')
                                                            <span class="bg-emerald-100 text-emerald-800 text-xs font-bold px-2.5 py-0.5 rounded">Approved</span>
                                                        @endif
                                                    @else
                                                        <span class="bg-gray-200 text-gray-700 text-xs font-bold px-2.5 py-0.5 rounded">Not Submitted</span>
                                                    @endif
                                                </div>

                                                @if($thesis->pts1Form)
                                                    <div class="text-xs text-gray-600 dark:text-gray-300 space-y-1">
                                                        <div>Open Seminar: <strong>{{ $thesis->pts1Form->seminar_date?->format('M d, Y') }}</strong> at {{ $thesis->pts1Form->seminar_time }}</div>
                                                        <div class="text-indigo-600 dark:text-indigo-400 font-semibold">Current Stage: {{ str_replace('_', ' ', $thesis->pts1Form->current_stage) }}</div>
                                                    </div>

                                                    <!-- Main Supervisor Review Action for PTS-1 -->
                                                    @if($thesis->pts1Form->status === 'in_progress' && $thesis->pts1Form->current_stage === 'main_supervisor')
                                                        <div class="pt-2">
                                                            <a href="{{ route('faculty.pts1.edit', $thesis->pts1Form->id) }}" class="block w-full text-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs rounded-lg shadow transition">
                                                                Review & Evaluate PTS-1 Form &rarr;
                                                            </a>
                                                        </div>
                                                    @endif
                                                @endif
                                            </div>

                                            <!-- PTS-2 Card -->
                                            <div class="p-4 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700/30 space-y-3">
                                                <div class="flex justify-between items-center">
                                                    <span class="font-bold text-sm text-gray-900 dark:text-white">PTS-2 (Synopsis Report)</span>
                                                    @if(!$thesis->pts1Form || $thesis->pts1Form->status !== 'accepted')
                                                        <span class="bg-gray-200 text-gray-700 text-xs font-bold px-2 py-0.5 rounded">🔒 Locked</span>
                                                    @elseif($thesis->pts2Form)
                                                        @if($thesis->pts2Form->status === 'in_progress')
                                                            <span class="bg-purple-100 text-purple-800 text-xs font-bold px-2.5 py-0.5 rounded">In Progress</span>
                                                        @elseif($thesis->pts2Form->status === 'reverted')
                                                            <span class="bg-amber-100 text-amber-800 text-xs font-bold px-2.5 py-0.5 rounded">Reverted by {{ $thesis->pts2Form->getRevertedByRoleLabel() }}</span>
                                                        @elseif($thesis->pts2Form->status === 'accepted')
                                                            <span class="bg-emerald-100 text-emerald-800 text-xs font-bold px-2.5 py-0.5 rounded">Approved</span>
                                                        @endif
                                                    @else
                                                        <span class="bg-gray-200 text-gray-700 text-xs font-bold px-2.5 py-0.5 rounded">Not Submitted</span>
                                                    @endif
                                                </div>

                                                @if($thesis->pts2Form)
                                                    <div class="text-xs text-gray-600 dark:text-gray-300 space-y-1">
                                                        <div>Synopsis Title: <strong>{{ $thesis->pts2Form->synopsis_title }}</strong></div>
                                                        <div class="text-purple-600 dark:text-purple-400 font-semibold">Current Stage: {{ str_replace('_', ' ', $thesis->pts2Form->current_stage) }}</div>
                                                    </div>

                                                    <!-- Main Supervisor Review Action for PTS-2 -->
                                                    @if($thesis->pts2Form->status === 'in_progress' && $thesis->pts2Form->current_stage === 'main_supervisor')
                                                        <div class="pt-2">
                                                            <a href="{{ route('faculty.pts2.edit', $thesis->pts2Form->id) }}" class="block w-full text-center px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white font-bold text-xs rounded-lg shadow transition">
                                                                Review & Evaluate PTS-2 Form &rarr;
                                                            </a>
                                                        </div>
                                                    @endif
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-gray-500 text-center py-6">No scholars assigned as Main Supervisor.</p>
                    @endforelse
                </div>
            </div>

            <!-- TAB 2: CO-SUPERVISOR SCHOLARS -->
            <div x-show="activeTab === 'co'" x-cloak class="space-y-4">
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4 border-b border-gray-100 dark:border-gray-700 pb-2">
                        Scholars Supervised as Co-Supervisor ({{ $coStudents->count() }})
                    </h3>

                    @forelse($coStudents as $student)
                        <div class="mb-4 border border-gray-200 dark:border-gray-700 rounded-xl overflow-hidden shadow-sm bg-gray-50/50 dark:bg-gray-800/50">
                            <!-- Student Header Card -->
                            <div @click="toggleStudent('co-{{ $student->id }}')" class="p-4 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-750 cursor-pointer flex flex-col md:flex-row md:items-center justify-between gap-4 transition">
                                <div class="flex items-center space-x-3">
                                    <div class="w-10 h-10 rounded-full bg-blue-100 dark:bg-blue-900 text-blue-700 dark:text-blue-300 flex items-center justify-center font-bold text-base border border-blue-200 dark:border-blue-800">
                                        {{ substr($student->user->name ?? 'S', 0, 1) }}
                                    </div>
                                    <div>
                                        <h4 class="font-bold text-gray-900 dark:text-white text-base flex items-center">
                                            {{ $student->user->name }}
                                            <span class="ml-2 text-xs font-mono font-medium text-gray-500 bg-gray-100 dark:bg-gray-700 px-2 py-0.5 rounded">
                                                {{ $student->roll_number }}
                                            </span>
                                        </h4>
                                        <p class="text-xs text-gray-500 mt-0.5">
                                            Dept: {{ $student->department->name ?? 'N/A' }}
                                        </p>
                                    </div>
                                </div>

                                <div class="flex items-center space-x-3 text-xs font-bold">
                                    <span class="text-blue-600 dark:text-blue-400">
                                        {{ $student->theses->count() }} Registered Thesis(es)
                                    </span>
                                    <svg class="w-5 h-5 text-gray-400 transform transition-transform" :class="expandedStudent === 'co-{{ $student->id }}' ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                </div>
                            </div>

                            <!-- Expandable Student Theses & PTS Form Breakdown -->
                            <div x-show="expandedStudent === 'co-{{ $student->id }}'" x-cloak class="p-5 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 space-y-5">
                                @foreach($student->theses as $thesis)
                                    <div class="p-4 bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 space-y-4">
                                        <div class="border-b border-gray-100 dark:border-gray-700 pb-2">
                                            <div class="text-[10px] uppercase font-bold text-blue-600 dark:text-blue-400">Thesis Title</div>
                                            <h5 class="font-bold text-base text-gray-900 dark:text-white">{{ $thesis->title }}</h5>
                                        </div>

                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                            <!-- PTS-1 Card -->
                                            <div class="p-4 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700/30 space-y-3">
                                                <div class="flex justify-between items-center">
                                                    <span class="font-bold text-sm text-gray-900 dark:text-white">PTS-1 (Open Seminar)</span>
                                                    @if($thesis->pts1Form)
                                                        @if($thesis->pts1Form->status === 'in_progress')
                                                            <span class="bg-blue-100 text-blue-800 text-xs font-bold px-2.5 py-0.5 rounded">In Progress</span>
                                                        @elseif($thesis->pts1Form->status === 'reverted')
                                                            <span class="bg-amber-100 text-amber-800 text-xs font-bold px-2.5 py-0.5 rounded">Reverted</span>
                                                        @elseif($thesis->pts1Form->status === 'accepted')
                                                            <span class="bg-emerald-100 text-emerald-800 text-xs font-bold px-2.5 py-0.5 rounded">Approved</span>
                                                        @endif
                                                    @else
                                                        <span class="bg-gray-200 text-gray-700 text-xs font-bold px-2.5 py-0.5 rounded">Not Submitted</span>
                                                    @endif
                                                </div>

                                                @if($thesis->pts1Form)
                                                    <div class="text-xs text-gray-600 dark:text-gray-300 space-y-1">
                                                        <div>Open Seminar: <strong>{{ $thesis->pts1Form->seminar_date?->format('M d, Y') }}</strong></div>
                                                        <div class="text-blue-600 dark:text-blue-400 font-semibold">Current Stage: {{ str_replace('_', ' ', $thesis->pts1Form->current_stage) }}</div>
                                                    </div>

                                                    <!-- Dedicated Review & Endorse Button for Co-Supervisor for PTS-1 -->
                                                    @if($thesis->pts1Form->status === 'in_progress' && $thesis->pts1Form->current_stage === 'co_supervisors')
                                                        <div class="pt-2">
                                                            <a href="{{ route('faculty.pts1.co_edit', $thesis->pts1Form->id) }}" class="block w-full text-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs rounded-lg shadow transition">
                                                                Review & Endorse PTS-1 Form &rarr;
                                                            </a>
                                                        </div>
                                                    @endif
                                                @endif
                                            </div>

                                            <!-- PTS-2 Card -->
                                            <div class="p-4 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700/30 space-y-3">
                                                <div class="flex justify-between items-center">
                                                    <span class="font-bold text-sm text-gray-900 dark:text-white">PTS-2 (Synopsis Report)</span>
                                                    @if(!$thesis->pts1Form || $thesis->pts1Form->status !== 'accepted')
                                                        <span class="bg-gray-200 text-gray-700 text-xs font-bold px-2 py-0.5 rounded">🔒 Locked</span>
                                                    @elseif($thesis->pts2Form)
                                                        @if($thesis->pts2Form->status === 'in_progress')
                                                            <span class="bg-purple-100 text-purple-800 text-xs font-bold px-2.5 py-0.5 rounded">In Progress</span>
                                                        @elseif($thesis->pts2Form->status === 'reverted')
                                                            <span class="bg-amber-100 text-amber-800 text-xs font-bold px-2.5 py-0.5 rounded">Reverted</span>
                                                        @elseif($thesis->pts2Form->status === 'accepted')
                                                            <span class="bg-emerald-100 text-emerald-800 text-xs font-bold px-2.5 py-0.5 rounded">Approved</span>
                                                        @endif
                                                    @else
                                                        <span class="bg-gray-200 text-gray-700 text-xs font-bold px-2 py-0.5 rounded">Not Submitted</span>
                                                    @endif
                                                </div>

                                                @if($thesis->pts2Form)
                                                    <div class="text-xs text-gray-600 dark:text-gray-300 space-y-1">
                                                        <div class="text-purple-600 dark:text-purple-400 font-semibold">Current Stage: {{ str_replace('_', ' ', $thesis->pts2Form->current_stage) }}</div>
                                                    </div>

                                                    <!-- Pending Co-Supervisor Action for PTS-2 -->
                                                    @if($thesis->pts2Form->status === 'in_progress' && $thesis->pts2Form->current_stage === 'co_supervisors')
                                                        <div class="pt-2">
                                                            <a href="{{ route('pts2.review_endorse', $thesis->pts2Form->id) }}" class="block w-full text-center px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white font-bold text-xs rounded-lg shadow transition">
                                                                Review & Endorse PTS-2 Form &rarr;
                                                            </a>
                                                        </div>
                                                    @endif
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-gray-500 text-center py-6">No scholars assigned as Co-Supervisor.</p>
                    @endforelse
                </div>
            </div>

            <!-- TAB 3: PSPC MEMBER SCHOLARS -->
            <div x-show="activeTab === 'pspc'" x-cloak class="space-y-4">
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4 border-b border-gray-100 dark:border-gray-700 pb-2">
                        Scholars Assigned for PSPC Committee ({{ $pspcStudents->count() }})
                    </h3>

                    @forelse($pspcStudents as $student)
                        <div class="mb-4 border border-gray-200 dark:border-gray-700 rounded-xl overflow-hidden shadow-sm bg-gray-50/50 dark:bg-gray-800/50">
                            <!-- Student Header Card -->
                            <div @click="toggleStudent('pspc-{{ $student->id }}')" class="p-4 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-750 cursor-pointer flex flex-col md:flex-row md:items-center justify-between gap-4 transition">
                                <div class="flex items-center space-x-3">
                                    <div class="w-10 h-10 rounded-full bg-purple-100 dark:bg-purple-900 text-purple-700 dark:text-purple-300 flex items-center justify-center font-bold text-base border border-purple-200 dark:border-purple-800">
                                        {{ substr($student->user->name ?? 'S', 0, 1) }}
                                    </div>
                                    <div>
                                        <h4 class="font-bold text-gray-900 dark:text-white text-base flex items-center">
                                            {{ $student->user->name }}
                                            <span class="ml-2 text-xs font-mono font-medium text-gray-500 bg-gray-100 dark:bg-gray-700 px-2 py-0.5 rounded">
                                                {{ $student->roll_number }}
                                            </span>
                                        </h4>
                                        <p class="text-xs text-gray-500 mt-0.5">
                                            Dept: {{ $student->department->name ?? 'N/A' }}
                                        </p>
                                    </div>
                                </div>

                                <div class="flex items-center space-x-3 text-xs font-bold">
                                    <span class="text-purple-600 dark:text-purple-400">
                                        {{ $student->theses->count() }} Registered Thesis(es)
                                    </span>
                                    <svg class="w-5 h-5 text-gray-400 transform transition-transform" :class="expandedStudent === 'pspc-{{ $student->id }}' ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                </div>
                            </div>

                            <!-- Expandable Student Theses & PTS Form Breakdown -->
                            <div x-show="expandedStudent === 'pspc-{{ $student->id }}'" x-cloak class="p-5 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 space-y-5">
                                @foreach($student->theses as $thesis)
                                    <div class="p-4 bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 space-y-4">
                                        <div class="border-b border-gray-100 dark:border-gray-700 pb-2">
                                            <div class="text-[10px] uppercase font-bold text-purple-600 dark:text-purple-400">Thesis Title</div>
                                            <h5 class="font-bold text-base text-gray-900 dark:text-white">{{ $thesis->title }}</h5>
                                        </div>

                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                            <!-- PTS-1 Card -->
                                            <div class="p-4 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700/30 space-y-3">
                                                <div class="flex justify-between items-center">
                                                    <span class="font-bold text-sm text-gray-900 dark:text-white">PTS-1 (Open Seminar)</span>
                                                    @if($thesis->pts1Form)
                                                        @if($thesis->pts1Form->status === 'in_progress')
                                                            <span class="bg-blue-100 text-blue-800 text-xs font-bold px-2.5 py-0.5 rounded">In Progress</span>
                                                        @elseif($thesis->pts1Form->status === 'reverted')
                                                            <span class="bg-amber-100 text-amber-800 text-xs font-bold px-2.5 py-0.5 rounded">Reverted</span>
                                                        @elseif($thesis->pts1Form->status === 'accepted')
                                                            <span class="bg-emerald-100 text-emerald-800 text-xs font-bold px-2.5 py-0.5 rounded">Approved</span>
                                                        @endif
                                                    @else
                                                        <span class="bg-gray-200 text-gray-700 text-xs font-bold px-2.5 py-0.5 rounded">Not Submitted</span>
                                                    @endif
                                                </div>

                                                @if($thesis->pts1Form)
                                                    <div class="text-xs text-gray-600 dark:text-gray-300 space-y-1">
                                                        <div>Open Seminar: <strong>{{ $thesis->pts1Form->seminar_date?->format('M d, Y') }}</strong></div>
                                                        <div class="text-purple-600 dark:text-purple-400 font-semibold">Current Stage: {{ str_replace('_', ' ', $thesis->pts1Form->current_stage) }}</div>
                                                    </div>

                                                    <!-- Dedicated Review & Endorse Button for PSPC Member for PTS-1 -->
                                                    @if($thesis->pts1Form->status === 'in_progress' && $thesis->pts1Form->current_stage === 'pspc_members')
                                                        <div class="pt-2">
                                                            <a href="{{ route('pts1.review_endorse', $thesis->pts1Form->id) }}" class="block w-full text-center px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white font-bold text-xs rounded-lg shadow transition">
                                                                Review & Endorse PTS-1 Form &rarr;
                                                            </a>
                                                        </div>
                                                    @endif
                                                @endif
                                            </div>

                                            <!-- PTS-2 Card -->
                                            <div class="p-4 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700/30 space-y-3">
                                                <div class="flex justify-between items-center">
                                                    <span class="font-bold text-sm text-gray-900 dark:text-white">PTS-2 (Synopsis Report)</span>
                                                    @if(!$thesis->pts1Form || $thesis->pts1Form->status !== 'accepted')
                                                        <span class="bg-gray-200 text-gray-700 text-xs font-bold px-2 py-0.5 rounded">🔒 Locked</span>
                                                    @elseif($thesis->pts2Form)
                                                        @if($thesis->pts2Form->status === 'in_progress')
                                                            <span class="bg-purple-100 text-purple-800 text-xs font-bold px-2.5 py-0.5 rounded">In Progress</span>
                                                        @elseif($thesis->pts2Form->status === 'reverted')
                                                            <span class="bg-amber-100 text-amber-800 text-xs font-bold px-2.5 py-0.5 rounded">Reverted</span>
                                                        @elseif($thesis->pts2Form->status === 'accepted')
                                                            <span class="bg-emerald-100 text-emerald-800 text-xs font-bold px-2.5 py-0.5 rounded">Approved</span>
                                                        @endif
                                                    @else
                                                        <span class="bg-gray-200 text-gray-700 text-xs font-bold px-2 py-0.5 rounded">Not Submitted</span>
                                                    @endif
                                                </div>

                                                @if($thesis->pts2Form)
                                                    <div class="text-xs text-gray-600 dark:text-gray-300 space-y-1">
                                                        <div class="text-purple-600 dark:text-purple-400 font-semibold">Current Stage: {{ str_replace('_', ' ', $thesis->pts2Form->current_stage) }}</div>
                                                    </div>

                                                    <!-- Dedicated Review & Endorse Button for PSPC Member for PTS-2 -->
                                                    @if($thesis->pts2Form->status === 'in_progress' && $thesis->pts2Form->current_stage === 'pspc_members')
                                                        <div class="pt-2">
                                                            <a href="{{ route('pts2.review_endorse', $thesis->pts2Form->id) }}" class="block w-full text-center px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white font-bold text-xs rounded-lg shadow transition">
                                                                Review & Endorse PTS-2 Form &rarr;
                                                            </a>
                                                        </div>
                                                    @endif
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-gray-500 text-center py-6">No scholars assigned for PSPC Committee.</p>
                    @endforelse
                </div>
            </div>

        </div>
    </div>
</x-app-layout>

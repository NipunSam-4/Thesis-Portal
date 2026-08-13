<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-bold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Thesis Management Portal') }}
            </h2>
            <div class="flex justify-between items-center">
                <span class="bg-blue-100 text-blue-800 text-xs font-medium px-3 py-1 rounded-full dark:bg-blue-900 dark:text-blue-300">
                    Faculty Member
                </span>
                <x-profile_dropdown/>
            </div>
        </div>
    </x-slot>

    <div class="py-8" x-data="{
        programTab: 'phd',
        activeTab: 'main',
        expandedStudent: null,
        searchQuery: '',
        toggleStudent(id) {
            this.expandedStudent = (this.expandedStudent === id) ? null : id;
        },
        matchesSearch(target) {
            if (!this.searchQuery || !this.searchQuery.trim()) return true;
            if (!target) return false;
            return target.toLowerCase().includes(this.searchQuery.toLowerCase().trim());
        }
    }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Success/Warning Flash Alerts -->
            @if(session('success'))
                <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)" x-transition:leave="transition ease-in duration-500" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="p-4 bg-emerald-100 border-l-4 border-emerald-500 text-emerald-800 rounded-lg shadow-sm font-semibold">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('warning'))
                <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)" x-transition:leave="transition ease-in duration-500" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="p-4 bg-amber-100 border-l-4 border-amber-500 text-amber-800 rounded-lg shadow-sm font-semibold">
                    {{ session('warning') }}
                </div>
            @endif

            <!-- Welcome Banner -->
            <div class="bg-gradient-to-r from-blue-600 to-indigo-700 rounded-xl shadow-sm p-6 text-white flex items-center justify-between">
                <div>
                    <h2 class="text-2xl font-bold mb-1">Welcome, {{ $user->name }}</h2>
                    <p class="text-blue-100 text-sm">Select a degree program and role tab below to inspect your assigned students and their registered thesis submissions.</p>
                </div>
            </div>

            <!-- Top-Level Degree Program Selection Tabs (PhD vs MS(R)) -->
            <div class="grid grid-cols-2 w-full bg-gray-200/80 dark:bg-gray-800 rounded-xl border border-gray-300/70 dark:border-gray-700 shadow-inner">
                <!-- PhD Students Program Tab -->
                <button type="button" 
                        @click="programTab = 'phd'" 
                        :class="programTab === 'phd' 
                            ? 'bg-indigo-600 text-white shadow-md rounded-xl font-bold' 
                            : 'bg-white dark:bg-gray-700/80 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 rounded-xl font-semibold'" 
                        class="w-full py-4 px-6 transition-all duration-200 flex items-center justify-center space-x-3 group">
                    <div class="flex items-center space-x-2.5">
                        <svg class="w-5 h-5" :class="programTab === 'phd' ? 'text-white' : 'text-indigo-600 dark:text-indigo-400'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0112 20.055a11.952 11.952 0 01-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"></path>
                        </svg>
                        <span class="text-sm tracking-wide">PhD Students</span>
                    </div>
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-extrabold transition-colors" :class="programTab === 'phd' ? 'bg-white/20 text-white' : 'bg-indigo-100 text-indigo-800 dark:bg-indigo-900/60 dark:text-indigo-300'">
                        {{ $mainStudents->filter(fn($s) => $s->isPhd())->count() + $coStudents->filter(fn($s) => $s->isPhd())->count() + $pspcStudents->filter(fn($s) => $s->isPhd())->count() }}
                    </span>
                </button>

                <!-- MS(R) Students Program Tab -->
                <button type="button" 
                        @click="programTab = 'msr'" 
                        :class="programTab === 'msr' 
                            ? 'bg-indigo-600 text-white shadow-md rounded-xl font-bold' 
                            : 'bg-white dark:bg-gray-700/80 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 rounded-xl font-semibold'" 
                        class="w-full py-4 px-6 transition-all duration-200 flex items-center justify-center space-x-3 group">
                    <div class="flex items-center space-x-2.5">
                        <svg class="w-5 h-5" :class="programTab === 'msr' ? 'text-white' : 'text-blue-600 dark:text-blue-400'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L5.6 15.12a2 2 0 00-1.183.184l-1.02.51a2 2 0 00-1.107 1.789v.894a2 2 0 002 2h15.428a2 2 0 002-2v-.894a2 2 0 00-1.107-1.789l-1.02-.51z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12a4 4 0 108 0 4 4 0 00-8 0z"></path>
                        </svg>
                        <span class="text-sm tracking-wide">MS(R) Students</span>
                    </div>
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-extrabold transition-colors" :class="programTab === 'msr' ? 'bg-white/20 text-white' : 'bg-blue-100 text-blue-800 dark:bg-blue-900/60 dark:text-blue-300'">
                        {{ $mainStudents->filter(fn($s) => $s->isMsr())->count() + $coStudents->filter(fn($s) => $s->isMsr())->count() + $pspcStudents->filter(fn($s) => $s->isMsr())->count() }}
                    </span>
                </button>
            </div>

            <!-- Navigation Sub-Tabs & Search Input Bar -->
            <div class="space-y-4">
                <!-- Role-Based Navigation Sub-Tabs -->
                <div class="grid grid-cols-3 sm:grid-cols-3 gap-4 w-full">
                    <!-- Main Supervisor Tab Card -->
                    <button type="button" 
                            @click="activeTab = 'main'" 
                            :class="activeTab === 'main' 
                                ? 'bg-indigo-600 text-white border-2 border-indigo-600 shadow-md ring-2 ring-indigo-500/20 scale-[1.01]' 
                                : 'bg-white dark:bg-gray-800/90 border-2 border-gray-200 dark:border-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-750'" 
                            class="w-full px-5 py-2 rounded-2xl font-semibold text-sm transition-all duration-200 flex items-center justify-between group">
                        <div class="flex items-center space-x-3">
                            <svg class="w-5 h-5 transition-colors" :class="activeTab === 'main' ? 'text-white' : 'text-indigo-600 dark:text-indigo-400'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                            <span class="font-bold tracking-wide" :class="activeTab === 'main' ? 'text-white' : 'text-gray-800 dark:text-gray-100'">Main supervisor</span>
                        </div>
                        <span class="font-extrabold text-sm px-2.5 py-0.5 rounded-lg transition-colors" :class="activeTab === 'main' ? 'bg-white/20 text-white' : 'bg-indigo-100 text-indigo-800 dark:bg-indigo-900/60 dark:text-indigo-300'">
                            <span x-show="programTab === 'phd'">{{ $mainStudents->filter(fn($s) => $s->isPhd())->count() }}</span>
                            <span x-show="programTab === 'msr'">{{ $mainStudents->filter(fn($s) => $s->isMsr())->count() }}</span>
                        </span>
                    </button>

                    <!-- Co-Supervisor Tab Card -->
                    <button type="button" 
                            @click="activeTab = 'co'" 
                            :class="activeTab === 'co' 
                                ? 'bg-blue-600 text-white border-2 border-blue-600 shadow-md ring-2 ring-blue-500/20 scale-[1.01]' 
                                : 'bg-white dark:bg-gray-800/90 border-2 border-gray-200 dark:border-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-750'" 
                            class="w-full px-5 py-2 rounded-2xl font-semibold text-sm transition-all duration-200 flex items-center justify-between group">
                        <div class="flex items-center space-x-3">
                            <svg class="w-5 h-5 transition-colors" :class="activeTab === 'co' ? 'text-white' : 'text-blue-600 dark:text-blue-400'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                            <span class="font-bold tracking-wide" :class="activeTab === 'co' ? 'text-white' : 'text-gray-800 dark:text-gray-100'">Co-supervisor</span>
                        </div>
                        <span class="font-extrabold text-sm px-2.5 py-0.5 rounded-lg transition-colors" :class="activeTab === 'co' ? 'bg-white/20 text-white' : 'bg-blue-100 text-blue-800 dark:bg-blue-900/60 dark:text-blue-300'">
                            <span x-show="programTab === 'phd'">{{ $coStudents->filter(fn($s) => $s->isPhd())->count() }}</span>
                            <span x-show="programTab === 'msr'">{{ $coStudents->filter(fn($s) => $s->isMsr())->count() }}</span>
                        </span>
                    </button>

                    <!-- PSPC Member Tab Card -->
                    <button type="button" 
                            @click="activeTab = 'pspc'" 
                            :class="activeTab === 'pspc' 
                                ? 'bg-emerald-600 text-white border-2 border-emerald-600 shadow-md ring-2 ring-emerald-500/20 scale-[1.01]' 
                                : 'bg-white dark:bg-gray-800/90 border-2 border-gray-200 dark:border-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-750'" 
                            class="w-full px-5 py-2 rounded-2xl font-semibold text-sm transition-all duration-200 flex items-center justify-between group">
                        <div class="flex items-center space-x-3">
                            <svg class="w-5 h-5 transition-colors" :class="activeTab === 'pspc' ? 'text-white' : 'text-emerald-600 dark:text-emerald-400'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0112 20.055a11.952 11.952 0 01-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"></path>
                            </svg>
                            <span class="font-bold tracking-wide" :class="activeTab === 'pspc' ? 'text-white' : 'text-gray-800 dark:text-gray-100'">PSPC member</span>
                        </div>
                        <span class="font-extrabold text-sm px-2.5 py-0.5 rounded-lg transition-colors" :class="activeTab === 'pspc' ? 'bg-white/20 text-white' : 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/60 dark:text-emerald-300'">
                            <span x-show="programTab === 'phd'">{{ $pspcStudents->filter(fn($s) => $s->isPhd())->count() }}</span>
                            <span x-show="programTab === 'msr'">{{ $pspcStudents->filter(fn($s) => $s->isMsr())->count() }}</span>
                        </span>
                    </button>
                </div>
            </div>

            <!-- Student Search Input Bar -->
            <div class="flex justify-end">
                <div class="w-full md:w-80 lg:w-96 relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                    <input type="text" x-model="searchQuery" placeholder="Search a student by their Name or Roll number" class="w-full pl-9 pr-9 py-2 bg-white dark:bg-gray-800 text-gray-900 dark:text-white rounded-xl border border-gray-200 dark:border-gray-700 focus:ring-2 focus:ring-indigo-500 text-sm font-medium transition shadow-sm">
                    <button x-show="searchQuery" @click="searchQuery = ''" type="button" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>
            </div>

            <!-- TAB 1: MAIN SUPERVISOR SCHOLARS -->
            <div x-show="activeTab === 'main'" x-cloak class="space-y-4">
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4 border-b border-gray-100 dark:border-gray-700 pb-2">
                        Students Supervised as Main Supervisor (<span x-show="programTab === 'phd'">{{ $mainStudents->filter(fn($s) => $s->isPhd())->count() }}</span><span x-show="programTab === 'msr'">{{ $mainStudents->filter(fn($s) => $s->isMsr())->count() }}</span>)
                    </h3>

                    @php
                        $phdMainCount = $mainStudents->filter(fn($s) => $s->isPhd())->count();
                        $msrMainCount = $mainStudents->filter(fn($s) => $s->isMsr())->count();
                    @endphp

                    <div x-show="programTab === 'phd' && {{ $phdMainCount }} === 0" class="text-center py-8 bg-gray-50 dark:bg-gray-750/50 rounded-xl border border-dashed border-gray-200 dark:border-gray-700">
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">No PhD students assigned to you for this role.</p>
                    </div>
                    <div x-show="programTab === 'msr' && {{ $msrMainCount }} === 0" class="text-center py-8 bg-gray-50 dark:bg-gray-750/50 rounded-xl border border-dashed border-gray-200 dark:border-gray-700">
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">No MS(R) students assigned to you for this role.</p>
                    </div>

                    @foreach($mainStudents as $student)
                        <div x-show="(programTab === '{{ $student->isMsr() ? 'msr' : 'phd' }}') && matchesSearch(@js($student->searchable_text))" class="mb-4 border border-gray-200 dark:border-gray-700 rounded-xl overflow-hidden shadow-sm bg-gray-50/50 dark:bg-gray-800/50">
                            <!-- Student Header Card -->
                            <div @click="toggleStudent('main-{{ $student->id }}')" class="p-4 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-750 cursor-pointer flex flex-col md:flex-row md:items-center justify-between gap-4 transition">
                                <div class="flex items-center space-x-3">
                                    <div class="w-10 h-10 rounded-full bg-indigo-100 dark:bg-indigo-900 text-indigo-700 dark:text-indigo-300 flex items-center justify-center font-bold text-base border border-indigo-200 dark:border-indigo-800">
                                        {{ substr($student->user->name ?? 'S', 0, 1) }}
                                    </div>
                                    <div>
                                        <h4 class="font-bold text-gray-900 dark:text-white text-base flex items-center">
                                            <span>{{ $student->user->name }}</span>
                                            <x-student-info-modal :student="$student" />
                                            <span class="ml-2 text-xs font-semibold font-medium text-gray-500 bg-gray-100 dark:bg-gray-700 px-2 py-0.5 rounded">
                                                {{ $student->roll_number }}
                                            </span>
                                        </h4>
                                    </div>
                                </div>

                                <div class="flex items-center space-x-2 text-xs font-bold">
                                    @php
                                        $stageLabel = $student->getThesisStageLabel();
                                        $needsAction = $student->requiresActionFromUser($user, 'main');
                                    @endphp

                                    @if($needsAction)
                                        <span class="inline-flex items-center gap-1 bg-amber-100 dark:bg-amber-900/60 text-amber-800 dark:text-amber-300 text-xs font-extrabold px-2.5 py-0.5 rounded-full border border-amber-300 dark:border-amber-700 shadow-sm">
                                            Action Required
                                        </span>
                                    @endif

                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold shadow-sm border
                                        @if($stageLabel === 'Unregistered')
                                            bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300 border-gray-200 dark:border-gray-600
                                        @else
                                            bg-blue-100 text-blue-800 dark:bg-blue-900/60 dark:text-blue-300 border-blue-200 dark:border-blue-800
                                        @endif
                                    ">
                                        {{ $stageLabel }}
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
                                                        @elseif($thesis->pts1Form->status === 'rejected')
                                                            <span class="bg-red-100 text-red-800 text-xs font-bold px-2.5 py-0.5 rounded">Rejected</span>
                                                        @endif
                                                    @else
                                                        <span class="bg-gray-200 text-gray-700 text-xs font-bold px-2.5 py-0.5 rounded">Not Submitted</span>
                                                    @endif
                                                </div>

                                                @if($thesis->pts1Form)
                                                    <div class="text-xs text-gray-600 dark:text-gray-300 space-y-1">
                                                        <div>Open Seminar: <strong>{{ $thesis->pts1Form->seminar_date?->format('d-m-Y') }}</strong> at {{ $thesis->pts1Form->seminar_time }}</div>
                                                        <div class="text-indigo-600 dark:text-indigo-400 font-semibold">Current Stage: {{ str_replace('_', ' ', $thesis->pts1Form->current_stage) }}</div>
                                                    </div>

                                                    <!-- Main Supervisor Review Action for PTS-1 -->
                                                    @if($thesis->pts1Form->status === 'in_progress' && $thesis->pts1Form->current_stage === 'main_supervisor')
                                                        <div class="pt-2">
                                                            <a href="{{ route('faculty.pts1.edit', $thesis->pts1Form->id) }}" class="block w-full text-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs rounded-lg shadow transition">
                                                                Review & Evaluate PTS-1 Form &rarr;
                                                            </a>
                                                        </div>
                                                    @elseif(in_array($thesis->pts1Form->status, ['accepted', 'rejected', 'reverted']))
                                                        <div class="pt-2">
                                                            <a href="{{ route($thesis->pts1Form->status === 'reverted' ? 'pts1.show' : 'pts1.review_endorse', $thesis->pts1Form->id) }}" 
                                                               class="block w-full text-center px-4 py-2 {{ $thesis->pts1Form->status === 'reverted' ? 'bg-amber-600 hover:bg-amber-700' : 'bg-emerald-600 hover:bg-emerald-700' }} text-white font-bold text-xs rounded-lg shadow transition">
                                                                {{ $thesis->pts1Form->status === 'reverted' ? ('View Reverted ' . ($student->isPhd() ? 'PTS' : 'MSRTS') . '-1 Form') : ('View Submitted ' . ($student->isPhd() ? 'PTS' : 'MSRTS') . '-1 Form') }} &rarr;
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
                                                        @elseif($thesis->pts2Form->status === 'rejected')
                                                            <span class="bg-red-100 text-red-800 text-xs font-bold px-2.5 py-0.5 rounded">Rejected</span>
                                                        @endif
                                                    @else
                                                        <span class="bg-gray-200 text-gray-700 text-xs font-bold px-2.5 py-0.5 rounded">Not Submitted</span>
                                                    @endif
                                                </div>

                                                @if($thesis->pts2Form)
                                                    <div class="text-xs text-gray-600 dark:text-gray-300 space-y-1">
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
                    @endforeach
                </div>
            </div>

            <!-- TAB 2: CO-SUPERVISOR SCHOLARS -->
            <div x-show="activeTab === 'co'" x-cloak class="space-y-4">
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4 border-b border-gray-100 dark:border-gray-700 pb-2">
                        Students Supervised as Co-Supervisor (<span x-show="programTab === 'phd'">{{ $coStudents->filter(fn($s) => $s->isPhd())->count() }}</span><span x-show="programTab === 'msr'">{{ $coStudents->filter(fn($s) => $s->isMsr())->count() }}</span>)
                    </h3>

                    @php
                        $phdCoCount = $coStudents->filter(fn($s) => $s->isPhd())->count();
                        $msrCoCount = $coStudents->filter(fn($s) => $s->isMsr())->count();
                    @endphp

                    <div x-show="programTab === 'phd' && {{ $phdCoCount }} === 0" class="text-center py-8 bg-gray-50 dark:bg-gray-750/50 rounded-xl border border-dashed border-gray-200 dark:border-gray-700">
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">No PhD students assigned to you for this role.</p>
                    </div>
                    <div x-show="programTab === 'msr' && {{ $msrCoCount }} === 0" class="text-center py-8 bg-gray-50 dark:bg-gray-750/50 rounded-xl border border-dashed border-gray-200 dark:border-gray-700">
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">No MS(R) students assigned to you for this role.</p>
                    </div>

                    @foreach($coStudents as $student)
                        <div x-show="(programTab === '{{ $student->isMsr() ? 'msr' : 'phd' }}') && matchesSearch(@js($student->searchable_text))" class="mb-4 border border-gray-200 dark:border-gray-700 rounded-xl overflow-hidden shadow-sm bg-gray-50/50 dark:bg-gray-800/50">
                            <!-- Student Header Card -->
                            <div @click="toggleStudent('co-{{ $student->id }}')" class="p-4 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-750 cursor-pointer flex flex-col md:flex-row md:items-center justify-between gap-4 transition">
                                <div class="flex items-center space-x-3">
                                    <div class="w-10 h-10 rounded-full bg-blue-100 dark:bg-blue-900 text-blue-700 dark:text-blue-300 flex items-center justify-center font-bold text-base border border-blue-200 dark:border-blue-800">
                                        {{ substr($student->user->name ?? 'S', 0, 1) }}
                                    </div>
                                    <div>
                                        <h4 class="font-bold text-gray-900 dark:text-white text-base flex items-center">
                                            <span>{{ $student->user->name }}</span>
                                            <x-student-info-modal :student="$student" />
                                            <span class="ml-2 text-xs font-semibold font-medium text-gray-500 bg-gray-100 dark:bg-gray-700 px-2 py-0.5 rounded">
                                                {{ $student->roll_number }}
                                            </span>
                                        </h4>
                                        <p class="text-xs text-gray-500 mt-0.5">
                                            Dept: {{ $student->department->name ?? 'N/A' }}
                                        </p>
                                    </div>
                                </div>

                                <div class="flex items-center space-x-2 text-xs font-bold">
                                    @php
                                        $stageLabel = $student->getThesisStageLabel();
                                        $needsAction = $student->requiresActionFromUser($user, 'co');
                                    @endphp

                                    @if($needsAction)
                                        <span class="inline-flex items-center gap-1 bg-amber-100 dark:bg-amber-900/60 text-amber-800 dark:text-amber-300 text-xs font-extrabold px-2.5 py-0.5 rounded-full border border-amber-300 dark:border-amber-700 shadow-sm">
                                            Action Required
                                        </span>
                                    @endif

                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold shadow-sm border
                                        @if($stageLabel === 'Unregistered')
                                            bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300 border-gray-200 dark:border-gray-600
                                        @else
                                            bg-blue-100 text-blue-800 dark:bg-blue-900/60 dark:text-blue-300 border-blue-200 dark:border-blue-800
                                        @endif
                                    ">
                                        {{ $stageLabel }}
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
                                                            <span class="bg-amber-100 text-amber-800 text-xs font-bold px-2.5 py-0.5 rounded">Reverted by {{ $thesis->pts1Form->getRevertedByRoleLabel() }}</span>
                                                        @elseif($thesis->pts1Form->status === 'accepted')
                                                            <span class="bg-emerald-100 text-emerald-800 text-xs font-bold px-2.5 py-0.5 rounded">Approved</span>
                                                        @elseif($thesis->pts1Form->status === 'rejected')
                                                            <span class="bg-red-100 text-red-800 text-xs font-bold px-2.5 py-0.5 rounded">Rejected</span>
                                                        @endif
                                                    @else
                                                        <span class="bg-gray-200 text-gray-700 text-xs font-bold px-2.5 py-0.5 rounded">Not Submitted</span>
                                                    @endif
                                                </div>

                                                @if($thesis->pts1Form)
                                                    <div class="text-xs text-gray-600 dark:text-gray-300 space-y-1">
                                                        <div>Open Seminar: <strong>{{ $thesis->pts1Form->seminar_date?->format('d-m-Y') }}</strong></div>
                                                        <div class="text-blue-600 dark:text-blue-400 font-semibold">Current Stage: {{ str_replace('_', ' ', $thesis->pts1Form->current_stage) }}</div>
                                                    </div>

                                                    <!-- Dedicated Co-Supervisor Review Action for PTS-1 -->
                                                    @if($thesis->pts1Form->status === 'in_progress' && $thesis->pts1Form->current_stage === 'co_supervisors')
                                                        @php
                                                            $alreadyEndorsed = false;
                                                            for ($i = 1; $i <= 10; $i++) {
                                                                $idCol = "co_supervisor_{$i}_id";
                                                                $recCol = "co_supervisor_{$i}_recommendation";
                                                                if ($thesis->pts1Form->$idCol === $user->id && !is_null($thesis->pts1Form->$recCol)) {
                                                                    $alreadyEndorsed = true;
                                                                    break;
                                                                }
                                                            }
                                                        @endphp
                                                        @if(!$alreadyEndorsed)
                                                            <div class="pt-2">
                                                                <a href="{{ route('pts1.review_endorse', $thesis->pts1Form->id) }}" class="block w-full text-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs rounded-lg shadow transition">
                                                                    Review & Endorse PTS-1 Form &rarr;
                                                                </a>
                                                            </div>
                                                        @endif
                                                    @elseif(in_array($thesis->pts1Form->status, ['accepted', 'rejected', 'reverted']))
                                                        <div class="pt-2">
                                                            <a href="{{ route($thesis->pts1Form->status === 'reverted' ? 'pts1.show' : 'pts1.review_endorse', $thesis->pts1Form->id) }}" 
                                                               class="block w-full text-center px-4 py-2 {{ $thesis->pts1Form->status === 'reverted' ? 'bg-amber-600 hover:bg-amber-700' : 'bg-emerald-600 hover:bg-emerald-700' }} text-white font-bold text-xs rounded-lg shadow transition">
                                                                {{ $thesis->pts1Form->status === 'reverted' ? ('View Reverted ' . ($student->isPhd() ? 'PTS' : 'MSRTS') . '-1 Form') : ('View Submitted ' . ($student->isPhd() ? 'PTS' : 'MSRTS') . '-1 Form') }} &rarr;
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
                                                        <div class="text-purple-600 dark:text-purple-400 font-semibold">Current Stage: {{ str_replace('_', ' ', $thesis->pts2Form->current_stage) }}</div>
                                                    </div>

                                                    <!-- Dedicated Co-Supervisor Review Action for PTS-2 -->
                                                    @if($thesis->pts2Form->status === 'in_progress' && $thesis->pts2Form->current_stage === 'co_supervisors')
                                                        @php
                                                            $alreadyEndorsed = false;
                                                            if ($thesis->pts2Form->co_supervisor_1_id === $user->id && $thesis->pts2Form->co_supervisor_1_recommendation) $alreadyEndorsed = true;
                                                            if ($thesis->pts2Form->co_supervisor_2_id === $user->id && $thesis->pts2Form->co_supervisor_2_recommendation) $alreadyEndorsed = true;
                                                            if ($thesis->pts2Form->co_supervisor_3_id === $user->id && $thesis->pts2Form->co_supervisor_3_recommendation) $alreadyEndorsed = true;
                                                        @endphp
                                                        @if(!$alreadyEndorsed)
                                                            <div class="pt-2">
                                                                <a href="{{ route('pts2.review_endorse', $thesis->pts2Form->id) }}" class="block w-full text-center px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white font-bold text-xs rounded-lg shadow transition">
                                                                    Review & Endorse PTS-2 Form &rarr;
                                                                </a>
                                                            </div>
                                                        @endif
                                                    @endif
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- TAB 3: PSPC MEMBER SCHOLARS -->
            <div x-show="activeTab === 'pspc'" x-cloak class="space-y-4">
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4 border-b border-gray-100 dark:border-gray-700 pb-2">
                        Students Assigned for PSPC Committee (<span x-show="programTab === 'phd'">{{ $pspcStudents->filter(fn($s) => $s->isPhd())->count() }}</span><span x-show="programTab === 'msr'">{{ $pspcStudents->filter(fn($s) => $s->isMsr())->count() }}</span>)
                    </h3>

                    @php
                        $phdPspcCount = $pspcStudents->filter(fn($s) => $s->isPhd())->count();
                        $msrPspcCount = $pspcStudents->filter(fn($s) => $s->isMsr())->count();
                    @endphp

                    <div x-show="programTab === 'phd' && {{ $phdPspcCount }} === 0" class="text-center py-8 bg-gray-50 dark:bg-gray-750/50 rounded-xl border border-dashed border-gray-200 dark:border-gray-700">
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">No PhD students assigned to you for this role.</p>
                    </div>
                    <div x-show="programTab === 'msr' && {{ $msrPspcCount }} === 0" class="text-center py-8 bg-gray-50 dark:bg-gray-750/50 rounded-xl border border-dashed border-gray-200 dark:border-gray-700">
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">No MS(R) students assigned to you for this role.</p>
                    </div>

                    @foreach($pspcStudents as $student)
                        <div x-show="(programTab === '{{ $student->isMsr() ? 'msr' : 'phd' }}') && matchesSearch(@js($student->searchable_text))" class="mb-4 border border-gray-200 dark:border-gray-700 rounded-xl overflow-hidden shadow-sm bg-gray-50/50 dark:bg-gray-800/50">
                            <!-- Student Header Card -->
                            <div @click="toggleStudent('pspc-{{ $student->id }}')" class="p-4 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-750 cursor-pointer flex flex-col md:flex-row md:items-center justify-between gap-4 transition">
                                <div class="flex items-center space-x-3">
                                    <div class="w-10 h-10 rounded-full bg-purple-100 dark:bg-purple-900 text-purple-700 dark:text-purple-300 flex items-center justify-center font-bold text-base border border-purple-200 dark:border-purple-800">
                                        {{ substr($student->user->name ?? 'S', 0, 1) }}
                                    </div>
                                    <div>
                                        <h4 class="font-bold text-gray-900 dark:text-white text-base flex items-center">
                                            <span>{{ $student->user->name }}</span>
                                            <x-student-info-modal :student="$student" />
                                            <span class="ml-2 text-xs font-semibold font-medium text-gray-500 bg-gray-100 dark:bg-gray-700 px-2 py-0.5 rounded">
                                                {{ $student->roll_number }}
                                            </span>
                                        </h4>
                                        <p class="text-xs text-gray-500 mt-0.5">
                                            Dept: {{ $student->department->name ?? 'N/A' }}
                                        </p>
                                    </div>
                                </div>

                                <div class="flex items-center space-x-2 text-xs font-bold">
                                    @php
                                        $stageLabel = $student->getThesisStageLabel();
                                        $needsAction = $student->requiresActionFromUser($user, 'pspc');
                                    @endphp

                                    @if($needsAction)
                                        <span class="inline-flex items-center gap-1 bg-amber-100 dark:bg-amber-900/60 text-amber-800 dark:text-amber-300 text-xs font-extrabold px-2.5 py-0.5 rounded-full border border-amber-300 dark:border-amber-700 shadow-sm">
                                            Action Required
                                        </span>
                                    @endif

                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold shadow-sm border
                                        @if($stageLabel === 'Unregistered')
                                            bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300 border-gray-200 dark:border-gray-600
                                        @else
                                            bg-blue-100 text-blue-800 dark:bg-blue-900/60 dark:text-blue-300 border-blue-200 dark:border-blue-800
                                        @endif
                                    ">
                                        {{ $stageLabel }}
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
                                                            <span class="bg-amber-100 text-amber-800 text-xs font-bold px-2.5 py-0.5 rounded">Reverted by {{ $thesis->pts1Form->getRevertedByRoleLabel() }}</span>
                                                        @elseif($thesis->pts1Form->status === 'accepted')
                                                            <span class="bg-emerald-100 text-emerald-800 text-xs font-bold px-2.5 py-0.5 rounded">Approved</span>
                                                        @elseif($thesis->pts1Form->status === 'rejected')
                                                            <span class="bg-red-100 text-red-800 text-xs font-bold px-2.5 py-0.5 rounded">Rejected</span>
                                                        @endif
                                                    @else
                                                        <span class="bg-gray-200 text-gray-700 text-xs font-bold px-2.5 py-0.5 rounded">Not Submitted</span>
                                                    @endif
                                                </div>

                                                @if($thesis->pts1Form)
                                                    <div class="text-xs text-gray-600 dark:text-gray-300 space-y-1">
                                                        <div>Open Seminar: <strong>{{ $thesis->pts1Form->seminar_date?->format('d-m-Y') }}</strong></div>
                                                        <div class="text-purple-600 dark:text-purple-400 font-semibold">Current Stage: {{ str_replace('_', ' ', $thesis->pts1Form->current_stage) }}</div>
                                                    </div>

                                                    <!-- Dedicated PSPC Review Action for PTS-1 -->
                                                    @if($thesis->pts1Form->status === 'in_progress' && $thesis->pts1Form->current_stage === 'pspc_members')
                                                        @php
                                                            $alreadyEndorsed = false;
                                                            for ($i = 1; $i <= 10; $i++) {
                                                                $idCol = "pspc_member_{$i}_id";
                                                                $recCol = "pspc_member_{$i}_recommendation";
                                                                if ($thesis->pts1Form->$idCol === $user->id && !is_null($thesis->pts1Form->$recCol)) {
                                                                    $alreadyEndorsed = true;
                                                                    break;
                                                                }
                                                            }
                                                        @endphp
                                                        @if(!$alreadyEndorsed)
                                                            <div class="pt-2">
                                                                <a href="{{ route('pts1.review_endorse', $thesis->pts1Form->id) }}" class="block w-full text-center px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white font-bold text-xs rounded-lg shadow transition">
                                                                    Review & Endorse PTS-1 Form &rarr;
                                                                </a>
                                                            </div>
                                                        @endif
                                                    @elseif(in_array($thesis->pts1Form->status, ['accepted', 'rejected', 'reverted']))
                                                        <div class="pt-2">
                                                            <a href="{{ route($thesis->pts1Form->status === 'reverted' ? 'pts1.show' : 'pts1.review_endorse', $thesis->pts1Form->id) }}" 
                                                               class="block w-full text-center px-4 py-2 {{ $thesis->pts1Form->status === 'reverted' ? 'bg-amber-600 hover:bg-amber-700' : 'bg-emerald-600 hover:bg-emerald-700' }} text-white font-bold text-xs rounded-lg shadow transition">
                                                                {{ $thesis->pts1Form->status === 'reverted' ? ('View Reverted ' . ($student->isPhd() ? 'PTS' : 'MSRTS') . '-1 Form') : ('View Submitted ' . ($student->isPhd() ? 'PTS' : 'MSRTS') . '-1 Form') }} &rarr;
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
                                                        <div class="text-purple-600 dark:text-purple-400 font-semibold">Current Stage: {{ str_replace('_', ' ', $thesis->pts2Form->current_stage) }}</div>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

        </div>
    </div>
</x-app-layout>

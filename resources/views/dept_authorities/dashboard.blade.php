<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-bold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Thesis Management Portal') }}
            </h2>
            <div class="flex justify-between items-center">
                <span class="bg-blue-100 text-blue-800 text-xs font-medium px-3 py-1 rounded-full dark:bg-blue-900 dark:text-blue-300">
                    @if($user->isDpgc()) DPGC Member @endif
                    @if($user->isHod()) HOD @endif
                </span>
                <x-profile_dropdown/>
            </div>
        </div>
    </x-slot>

    <div class="py-8" x-data="{
        programTab: 'phd',
        expandedStudent: null,
        searchQuery: '',
        toggleStudent(id) {
            this.expandedStudent = (this.expandedStudent === id) ? null : id;
        },
        matchesSearch(targetText) {
            if (!this.searchQuery || !this.searchQuery.trim()) return true;
            if (!targetText) return false;
            return targetText.toLowerCase().includes(this.searchQuery.toLowerCase().trim());
        }
    }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Flash Alerts -->
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

            <!-- Banner Header -->
            <div class="{{ $user->isDpgc() ? 'bg-purple-600 dark:bg-purple-900' : 'bg-indigo-700 dark:bg-indigo-900' }} rounded-xl shadow-sm p-6 text-white flex justify-between items-center">
                <div>
                    <h2 class="text-2xl font-bold mb-1">Welcome, {{ $user->name }}</h2>
                    <p class="{{ $user->isDpgc() ? 'text-purple-100' : 'text-indigo-100' }} text-sm">
                        @if($user->isDpgc())
                            Department Postgraduate Committee (DPGC)- <strong>{{ $user->facultyProfile->department->name ?? 'N/A' }}</strong>
                        @elseif($user->isHod())
                            Head of Department (HOD) - <strong>{{ $user->facultyProfile->department->name ?? 'N/A' }}</strong>
                        @else
                            Departmental Authority - <strong>{{ $user->facultyProfile->department->name ?? 'N/A' }}</strong>
                        @endif
                    </p>
                </div>
                <div class="text-right text-xs bg-white/10 px-4 py-2 rounded-lg">
                    <div class="font-bold text-base">{{ $phdStudents->count() + $msrStudents->count() }}</div>
                    <div>Department Students</div>
                </div>
            </div>

            <!-- Top-Level Degree Program Selection Tabs (PhD vs MS(R)) -->
            <div class="grid grid-cols-2 w-full bg-gray-200/80 dark:bg-gray-800 rounded-xl border border-gray-300/70 dark:border-gray-700 shadow-inner">
                <!-- PhD Students Program Tab -->
                <button type="button" 
                        @click="programTab = 'phd'; searchQuery = ''" 
                        :class="programTab === 'phd' 
                            ? '{{ $user->isDpgc() ? 'bg-purple-600' : 'bg-indigo-600' }} text-white shadow-md rounded-xl font-bold' 
                            : 'bg-white dark:bg-gray-700/80 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 rounded-xl font-semibold'" 
                        class="w-full py-4 px-6 transition-all duration-200 flex items-center justify-center space-x-3 group">
                    <div class="flex items-center space-x-2.5">
                        <svg class="w-5 h-5" :class="programTab === 'phd' ? 'text-white' : 'text-indigo-600 dark:text-indigo-400'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0112 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"></path>
                        </svg>
                        <span class="text-sm tracking-wide">PhD Students</span>
                    </div>
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-extrabold transition-colors" :class="programTab === 'phd' ? 'bg-white/20 text-white' : 'bg-indigo-100 text-indigo-800 dark:bg-indigo-900/60 dark:text-indigo-300'">
                        {{ $phdStudents->count() }}
                    </span>
                </button>

                <!-- MS(R) Students Program Tab -->
                <button type="button" 
                        @click="programTab = 'msr'; searchQuery = ''" 
                        :class="programTab === 'msr' 
                            ? '{{ $user->isDpgc() ? 'bg-purple-600' : 'bg-indigo-600' }} text-white shadow-md rounded-xl font-bold' 
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
                        {{ $msrStudents->count() }}
                    </span>
                </button>
            </div>

            <!-- Department Students List Card -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 space-y-4">
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 border-b border-gray-100 dark:border-gray-700 pb-3">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white flex items-center">
                        <span class="w-2.5 h-2.5 rounded-full {{ $user->isDpgc() ? 'bg-purple-500' : 'bg-indigo-600' }} mr-2"></span>
                        <span x-show="programTab === 'phd'">Department PhD Students ({{ $phdStudents->count() }})</span>
                        <span x-show="programTab === 'msr'">Department MS(R) Students ({{ $msrStudents->count() }})</span>
                    </h3>
                    
                    <!-- Search Input Bar -->
                    <div class="w-full md:w-80 lg:w-96 relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>
                        <input type="text" x-model="searchQuery" placeholder=" Search student by Name, Roll Number, or Email" class="w-full pl-9 pr-9 py-2 bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-white rounded-xl border border-gray-200 dark:border-gray-700 focus:ring-2 {{ $user->isDpgc() ? 'focus:ring-purple-500' : 'focus:ring-indigo-500' }} text-sm font-medium transition shadow-sm">
                        <button x-show="searchQuery" @click="searchQuery = ''" type="button" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                    </div>
                </div>

                <!-- SECTION 1: PhD Students List -->
                <div x-show="programTab === 'phd'">
                    @forelse($phdStudents as $student)
                        <div x-show="matchesSearch(@js($student->searchable_text))" class="mb-4 border border-gray-200 dark:border-gray-700 rounded-xl overflow-hidden shadow-sm bg-gray-50/50 dark:bg-gray-800/50">
                            <!-- Student Header Card -->
                            <div @click="toggleStudent({{ $student->id }})" class="p-4 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-750 cursor-pointer flex flex-col md:flex-row md:items-center justify-between gap-4 transition">
                                <div class="flex items-center space-x-3">
                                    <div class="w-10 h-10 rounded-full {{ $user->isDpgc() ? 'bg-purple-100 dark:bg-purple-900 text-purple-700 dark:text-purple-300 border-purple-200 dark:border-purple-800' : 'bg-indigo-100 dark:bg-indigo-900 text-indigo-700 dark:text-indigo-300 border-indigo-200 dark:border-indigo-800' }} flex items-center justify-center font-bold text-base border">
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
                                        $needsAction = $student->requiresActionFromUser($user);
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

                                    <svg class="w-5 h-5 text-gray-400 transform transition-transform" :class="expandedStudent === {{ $student->id }} ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                </div>
                            </div>

                            <!-- Expandable Student Theses & PTS Form Breakdown -->
                            <div x-show="expandedStudent === {{ $student->id }}" x-cloak class="p-5 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 space-y-5">
                                @forelse($student->theses as $thesis)
                                    <div class="p-4 bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 space-y-4">
                                        <div class="border-b border-gray-100 dark:border-gray-700 pb-2">
                                            <div class="text-[10px] uppercase font-bold {{ $user->isDpgc() ? 'text-purple-600 dark:text-purple-400' : 'text-indigo-600 dark:text-indigo-400' }}">Thesis Title</div>
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
                                                        <div class="{{ $user->isDpgc() ? 'text-purple-600 dark:text-purple-400' : 'text-indigo-600 dark:text-indigo-400' }} font-semibold">Current Stage: {{ str_replace('_', ' ', $thesis->pts1Form->current_stage) }}</div>
                                                    </div>

                                                    <!-- Dedicated DPGC / HOD PTS-1 Review Button -->
                                                    @if(($user->isDpgc() && $thesis->pts1Form->status === 'in_progress' && $thesis->pts1Form->current_stage === 'dpgc') || ($user->isHod() && $thesis->pts1Form->status === 'in_progress' && $thesis->pts1Form->current_stage === 'hod'))
                                                        <div class="pt-2">
                                                            <a href="{{ route('pts1.review_endorse', $thesis->pts1Form->id) }}" class="block w-full text-center px-4 py-2 {{ $user->isDpgc() ? 'bg-purple-600 hover:bg-purple-700' : 'bg-indigo-600 hover:bg-indigo-700' }} text-white font-bold text-xs rounded-lg shadow transition">
                                                                Review & Endorse PTS-1 Form &rarr;
                                                            </a>
                                                        </div>
                                                    @elseif(in_array($thesis->pts1Form->status, ['accepted', 'rejected', 'reverted']))
                                                        @if($thesis->pts1Form->status !== 'reverted' || $thesis->pts1Form->canUserViewRevertedForm($user))
                                                            <div class="pt-2">
                                                                <a href="{{ route($thesis->pts1Form->status === 'reverted' ? 'pts1.show' : 'pts1.review_endorse', $thesis->pts1Form->id) }}" 
                                                                   class="block w-full text-center px-4 py-2 {{ $thesis->pts1Form->status === 'reverted' ? 'bg-amber-600 hover:bg-amber-700' : 'bg-emerald-600 hover:bg-emerald-700' }} text-white font-bold text-xs rounded-lg shadow transition">
                                                                    {{ $thesis->pts1Form->status === 'reverted' ? 'View Reverted PTS-1 Form' : 'View Submitted PTS-1 Form' }} &rarr;
                                                                </a>
                                                            </div>
                                                        @endif
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

                                                    <!-- Dedicated DPGC / HOD PTS-2 Review Button -->
                                                    @if(($user->isDpgc() && $thesis->pts2Form->status === 'in_progress' && $thesis->pts2Form->current_stage === 'dpgc') || ($user->isHod() && $thesis->pts2Form->status === 'in_progress' && $thesis->pts2Form->current_stage === 'hod'))
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
                                @empty
                                    <p class="text-xs text-gray-500">No theses registered for this student.</p>
                                @endforelse
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-8 text-sm text-gray-500 dark:text-gray-400 font-medium">
                            No PhD students registered in your department.
                        </div>
                    @endforelse
                </div>

                <!-- SECTION 2: MS(R) Students List -->
                <div x-show="programTab === 'msr'">
                    @forelse($msrStudents as $student)
                        <div x-show="matchesSearch(@js($student->searchable_text))" class="mb-4 border border-gray-200 dark:border-gray-700 rounded-xl overflow-hidden shadow-sm bg-gray-50/50 dark:bg-gray-800/50">
                            <!-- Student Header Card -->
                            <div @click="toggleStudent({{ $student->id }})" class="p-4 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-750 cursor-pointer flex flex-col md:flex-row md:items-center justify-between gap-4 transition">
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
                                    </div>
                                </div>

                                <div class="flex items-center space-x-2 text-xs font-bold">
                                    @php
                                        $stageLabel = $student->getThesisStageLabel();
                                        $needsAction = $student->requiresActionFromUser($user);
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

                                    <svg class="w-5 h-5 text-gray-400 transform transition-transform" :class="expandedStudent === {{ $student->id }} ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                </div>
                            </div>

                            <!-- Expandable Student Theses & PTS Form Breakdown -->
                            <div x-show="expandedStudent === {{ $student->id }}" x-cloak class="p-5 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 space-y-5">
                                @forelse($student->theses as $thesis)
                                    <div class="p-4 bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 space-y-4">
                                        <div class="border-b border-gray-100 dark:border-gray-700 pb-2">
                                            <div class="text-[10px] uppercase font-bold text-blue-600 dark:text-blue-400">Thesis Title</div>
                                            <h5 class="font-bold text-base text-gray-900 dark:text-white">{{ $thesis->title }}</h5>
                                        </div>

                                        <!-- MSRTS Milestone Forms Breakdown -->
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                            <!-- MSRTS-1 Card -->
                                            <div class="p-4 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700/30 space-y-3">
                                                <div class="flex justify-between items-center">
                                                    <span class="font-bold text-sm text-gray-900 dark:text-white">MSRTS-1 (Open Seminar)</span>
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
                                                        <div class="text-blue-600 dark:text-blue-400 font-semibold">Current Stage: {{ str_replace('_', ' ', $thesis->pts1Form->current_stage) }}</div>
                                                    </div>

                                                    <!-- Dedicated DPGC / HOD MSRTS-1 Review Button -->
                                                    @if(($user->isDpgc() && $thesis->pts1Form->status === 'in_progress' && $thesis->pts1Form->current_stage === 'dpgc') || ($user->isHod() && $thesis->pts1Form->status === 'in_progress' && $thesis->pts1Form->current_stage === 'hod'))
                                                        <div class="pt-2">
                                                            <a href="{{ route('pts1.review_endorse', $thesis->pts1Form->id) }}" class="block w-full text-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs rounded-lg shadow transition">
                                                                Review & Endorse MSRTS-1 Form &rarr;
                                                            </a>
                                                        </div>
                                                    @elseif(in_array($thesis->pts1Form->status, ['accepted', 'rejected', 'reverted']))
                                                        @if($thesis->pts1Form->status !== 'reverted' || $thesis->pts1Form->canUserViewRevertedForm($user))
                                                            <div class="pt-2">
                                                                <a href="{{ route($thesis->pts1Form->status === 'reverted' ? 'pts1.show' : 'pts1.review_endorse', $thesis->pts1Form->id) }}" 
                                                                   class="block w-full text-center px-4 py-2 {{ $thesis->pts1Form->status === 'reverted' ? 'bg-amber-600 hover:bg-amber-700' : 'bg-emerald-600 hover:bg-emerald-700' }} text-white font-bold text-xs rounded-lg shadow transition">
                                                                    {{ $thesis->pts1Form->status === 'reverted' ? 'View Reverted MSRTS-1 Form' : 'View Submitted MSRTS-1 Form' }} &rarr;
                                                                </a>
                                                            </div>
                                                        @endif
                                                    @endif
                                                @endif
                                            </div>

                                            <!-- MSRTS-2 Card -->
                                            <div class="p-4 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700/30 space-y-3">
                                                <div class="flex justify-between items-center">
                                                    <span class="font-bold text-sm text-gray-900 dark:text-white">MSRTS-2 (Synopsis Report)</span>
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
                                                        <span class="bg-gray-200 text-gray-700 text-xs font-bold px-2 py-0.5 rounded">Not Submitted</span>
                                                    @endif
                                                </div>

                                                @if($thesis->pts2Form)
                                                    <div class="text-xs text-gray-600 dark:text-gray-300 space-y-1">
                                                        <div class="text-purple-600 dark:text-purple-400 font-semibold">Current Stage: {{ str_replace('_', ' ', $thesis->pts2Form->current_stage) }}</div>
                                                    </div>

                                                    <!-- Dedicated DPGC / HOD MSRTS-2 Review Button -->
                                                    @if(($user->isDpgc() && $thesis->pts2Form->status === 'in_progress' && $thesis->pts2Form->current_stage === 'dpgc') || ($user->isHod() && $thesis->pts2Form->status === 'in_progress' && $thesis->pts2Form->current_stage === 'hod'))
                                                        <div class="pt-2">
                                                            <a href="{{ route('pts2.review_endorse', $thesis->pts2Form->id) }}" class="block w-full text-center px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white font-bold text-xs rounded-lg shadow transition">
                                                                Review & Endorse MSRTS-2 Form &rarr;
                                                            </a>
                                                        </div>
                                                    @endif
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <p class="text-xs text-gray-500">No theses registered for this student.</p>
                                @endforelse
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-8 text-sm text-gray-500 dark:text-gray-400 font-medium">
                            No MS(R) students registered in your department.
                        </div>
                    @endforelse
                </div>

            </div>
        </div>
    </div>
</x-app-layout>

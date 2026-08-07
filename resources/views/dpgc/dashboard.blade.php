<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-bold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('DPGC Member Portal') }}
            </h2>
            <span class="bg-purple-100 text-purple-800 text-xs font-medium px-3 py-1 rounded-full dark:bg-purple-900 dark:text-purple-300">
                DPGC Member
            </span>
        </div>
    </x-slot>

    <div class="py-8" x-data="{
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

            <!-- Flash Alerts -->
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

            <!-- Banner Header -->
            <div class="bg-purple-600 dark:bg-purple-900 rounded-xl shadow-sm p-6 text-white flex justify-between items-center">
                <div>
                    <h2 class="text-2xl font-bold mb-1">Welcome, {{ $user->name }}</h2>
                    <p class="text-purple-100 text-sm">Department Postgraduate Committee (DPGC) Portal - <strong>{{ $user->facultyProfile->department->name ?? 'N/A' }}</strong></p>
                </div>
                <div class="text-right text-xs bg-white/10 px-4 py-2 rounded-lg">
                    <div class="font-bold text-base">{{ $departmentStudents->count() }}</div>
                    <div>Department Scholars</div>
                </div>
            </div>

            <!-- Department Scholars List Card -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 space-y-4">
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 border-b border-gray-100 dark:border-gray-700 pb-3">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white flex items-center">
                        <span class="w-2.5 h-2.5 rounded-full bg-purple-500 mr-2"></span>
                        Department PhD Scholars ({{ $departmentStudents->count() }})
                    </h3>
                    
                    <!-- Search Input Bar -->
                    <div class="w-full md:w-80 lg:w-96 relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>
                        <input type="text" x-model="searchQuery" placeholder="🔍 Search scholar by Name, Roll, or Email..." class="w-full pl-9 pr-9 py-2 bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-white rounded-xl border border-gray-200 dark:border-gray-700 focus:ring-2 focus:ring-purple-500 text-sm font-medium transition shadow-sm">
                        <button x-show="searchQuery" @click="searchQuery = ''" type="button" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                    </div>
                </div>

                @forelse($departmentStudents as $student)
                    <div x-show="!searchQuery.trim() || '{{ addslashes(mb_strtolower(($student->user->name ?? '') . ' ' . ($student->roll_number ?? '') . ' ' . ($student->user->email ?? '') . ' ' . ($student->theses->pluck('title')->join(' ') ?? ''))) }}'.includes(searchQuery.toLowerCase().trim())" class="mb-4 border border-gray-200 dark:border-gray-700 rounded-xl overflow-hidden shadow-sm bg-gray-50/50 dark:bg-gray-800/50">
                        <!-- Student Header Card -->
                        <div @click="toggleStudent({{ $student->id }})" class="p-4 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-750 cursor-pointer flex flex-col md:flex-row md:items-center justify-between gap-4 transition">
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
                                        Reg Date: {{ $student->date_registration }} | Joining: {{ $student->date_joining }}
                                    </p>
                                </div>
                            </div>

                            <div class="flex items-center space-x-3 text-xs font-bold">
                                <span class="text-purple-600 dark:text-purple-400">
                                    {{ $student->theses->count() }} Registered Thesis(es)
                                </span>
                                <svg class="w-5 h-5 text-gray-400 transform transition-transform" :class="expandedStudent === {{ $student->id }} ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                            </div>
                        </div>

                        <!-- Expandable Student Theses & PTS Form Breakdown -->
                        <div x-show="expandedStudent === {{ $student->id }}" x-cloak class="p-5 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 space-y-5">
                            @forelse($student->theses as $thesis)
                                <div class="p-4 bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 space-y-4">
                                    <div class="border-b border-gray-100 dark:border-gray-700 pb-2">
                                        <div class="text-[10px] uppercase font-bold text-purple-600 dark:text-purple-400">Thesis Title</div>
                                        <h5 class="font-bold text-base text-gray-900 dark:text-white">{{ $thesis->title }}</h5>
                                        <div class="text-xs text-gray-500 mt-1">Supervisors: {{ $thesis->supervisors->pluck('name')->join(', ') ?: 'Unassigned' }}</div>
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
                                                    <div class="text-purple-600 dark:text-purple-400 font-semibold">Current Stage: {{ str_replace('_', ' ', $thesis->pts1Form->current_stage) }}</div>
                                                </div>

                                                <!-- Dedicated DPGC PTS-1 Review Button -->
                                                @if($thesis->pts1Form->status === 'in_progress' && $thesis->pts1Form->current_stage === 'dpgc')
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

                                                <!-- Dedicated DPGC PTS-2 Review Button -->
                                                @if($thesis->pts2Form->status === 'in_progress' && $thesis->pts2Form->current_stage === 'dpgc')
                                                    <div class="pt-2">
                                                        <a href="{{ route('pts2.review_endorse', $thesis->pts2Form->id) }}" class="block w-full text-center px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white font-bold text-xs rounded-lg shadow transition">
                                                            Review & Endorse PTS-2 Form &rarr;
                                                        </a>
                                                    </div>
                                                @endif
                                            @endif
                                        </div>
                                    </div>
                                @empty
                                    <p class="text-xs text-gray-500">No theses registered for this scholar.</p>
                                @endforelse
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-gray-500 text-center py-6">No scholars registered in your department.</p>
                    @endforelse
                </div>

            </div>
        </div>
    </div>
</x-app-layout>

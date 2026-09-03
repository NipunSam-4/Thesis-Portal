<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-bold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Thesis Management Portal') }}
            </h2>
            <div class="flex justify-between items-center">
                <span class="bg-blue-100 text-blue-800 text-xs font-medium px-3 py-1 rounded-full dark:bg-blue-900 dark:text-blue-300">
                    @if($user->isDpgc()) DPGC Convener @endif
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
        },
        matchesFilter(targetText, dept = null) {
            return this.matchesSearch(targetText);
        }
    }">
        <div class="max-w-7xl mx-auto px-2 sm:px-6 lg:px-8 space-y-6">

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
                            Department Postgraduate Committee (DPGC) Convener - <strong>{{ $user->deptAuthorityProfile->department->name ?? $user->facultyProfile->department->name ?? 'N/A' }}</strong>
                        @elseif($user->isHod())
                            Head of Department (HOD) - <strong>{{ $user->deptAuthorityProfile->department->name ?? $user->facultyProfile->department->name ?? 'N/A' }}</strong>
                        @else
                            Departmental Authority - <strong>{{ $user->deptAuthorityProfile->department->name ?? $user->facultyProfile->department->name ?? 'N/A' }}</strong>
                        @endif
                    </p>
                </div>
                <div class="text-right text-xs bg-white/10 px-4 py-2 rounded-lg hidden sm:block">
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
                        class="w-full py-3 sm:py-4 px-2 sm:px-6 transition-all duration-200 flex items-center justify-center sm:justify-between group">
                    <div class="flex items-center space-x-1.5 sm:space-x-2.5">
                        <svg class="hidden sm:block w-5 h-5" :class="programTab === 'phd' ? 'text-white' : 'text-indigo-600 dark:text-indigo-400'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0112 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"></path>
                        </svg>
                        <span class="text-xs sm:text-sm tracking-wide">PhD Students</span>
                    </div>
                    <span class="hidden sm:inline-flex px-2.5 py-0.5 rounded-full text-xs font-extrabold transition-colors" :class="programTab === 'phd' ? 'bg-white/20 text-white' : 'bg-indigo-100 text-indigo-800 dark:bg-indigo-900/60 dark:text-indigo-300'">
                        {{ $phdStudents->count() }}
                    </span>
                </button>

                <!-- MS(R) Students Program Tab -->
                <button type="button" 
                        @click="programTab = 'msr'; searchQuery = ''" 
                        :class="programTab === 'msr' 
                            ? '{{ $user->isDpgc() ? 'bg-purple-600' : 'bg-indigo-600' }} text-white shadow-md rounded-xl font-bold' 
                            : 'bg-white dark:bg-gray-700/80 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 rounded-xl font-semibold'" 
                        class="w-full py-3 sm:py-4 px-2 sm:px-6 transition-all duration-200 flex items-center justify-center sm:justify-between group">
                    <div class="flex items-center space-x-1.5 sm:space-x-2.5">
                        <svg class="hidden sm:block w-5 h-5" :class="programTab === 'msr' ? 'text-white' : 'text-blue-600 dark:text-blue-400'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L5.6 15.12a2 2 0 00-1.183.184l-1.02.51a2 2 0 00-1.107 1.789v.894a2 2 0 002 2h15.428a2 2 0 002-2v-.894a2 2 0 00-1.107-1.789l-1.02-.51z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12a4 4 0 108 0 4 4 0 00-8 0z"></path>
                        </svg>
                        <span class="text-xs sm:text-sm tracking-wide">MS(R) Students</span>
                    </div>
                    <span class="hidden sm:inline-flex px-2.5 py-0.5 rounded-full text-xs font-extrabold transition-colors" :class="programTab === 'msr' ? 'bg-white/20 text-white' : 'bg-blue-100 text-blue-800 dark:bg-blue-900/60 dark:text-blue-300'">
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
                        <x-student-card :student="$student" :user="$user" />
                    @empty
                        <div class="text-center py-8 text-sm text-gray-500 dark:text-gray-400 font-medium bg-gray-50 dark:bg-gray-800/50 rounded-xl border border-dashed border-gray-200 dark:border-gray-700">
                            No PhD students registered in your department.
                        </div>
                    @endforelse
                </div>

                <!-- SECTION 2: MS(R) Students List -->
                <div x-show="programTab === 'msr'">
                    @forelse($msrStudents as $student)
                        <x-student-card :student="$student" :user="$user" />
                    @empty
                        <div class="text-center py-8 text-sm text-gray-500 dark:text-gray-400 font-medium bg-gray-50 dark:bg-gray-800/50 rounded-xl border border-dashed border-gray-200 dark:border-gray-700">
                            No MS(R) students registered in your department.
                        </div>
                    @endforelse
                </div>

            </div>
        </div>
    </div>
</x-app-layout>

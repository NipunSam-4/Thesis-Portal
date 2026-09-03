<x-app-layout>
    <div class="py-8" x-data="{
        programTab: 'phd',
        subTab: 'acting',
        expandedStudent: null,
        searchQuery: '',
        selectedDept: 'all',
        selectedDeptName: 'All Departments ({{ $departments->count() }})',
        deptDropdownOpen: false,
        selectDepartment(id, name) {
            this.selectedDept = id;
            this.selectedDeptName = name;
            this.deptDropdownOpen = false;
        },
        toggleStudent(id) {
            this.expandedStudent = (this.expandedStudent === id) ? null : id;
        },
        matchesFilter(targetText, deptId) {
            const matchesDept = (this.selectedDept === 'all') || (String(this.selectedDept) === String(deptId));
            if (!matchesDept) return false;
            if (!this.searchQuery || !this.searchQuery.trim()) return true;
            if (!targetText) return false;
            return targetText.toLowerCase().includes(this.searchQuery.toLowerCase().trim());
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

            <!-- Dynamic Header Card -->
            <div class="bg-gradient-to-r from-indigo-700 to-purple-800 rounded-xl shadow-sm p-6 text-white flex justify-between items-center">
                <div>
                    <h2 class="text-2xl font-bold mb-1">Welcome, {{ $user->name }}</h2>
                    <p class="text-indigo-100 text-sm">
                        Acting Approval Authority Executive Dashboard
                    </p>
                </div>
            </div>

            <!-- Top-Level Degree Program Selection Tabs (PhD vs MS(R)) -->
            <div class="grid grid-cols-2 w-full bg-gray-200/80 dark:bg-gray-800 rounded-xl border border-gray-300/70 dark:border-gray-700 shadow-inner">
                <!-- PhD Students Program Tab -->
                <button type="button" 
                        @click="programTab = 'phd'; searchQuery = ''" 
                        :class="programTab === 'phd' 
                            ? 'bg-indigo-600 text-white shadow-md rounded-xl font-bold' 
                            : 'bg-white dark:bg-gray-700/80 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 rounded-xl font-semibold'" 
                        class="w-full py-3 sm:py-4 px-2 sm:px-6 transition-all duration-200 flex items-center justify-center sm:justify-between group">
                    <div class="flex items-center space-x-1.5 sm:space-x-2.5">
                        <svg class="hidden sm:block w-5 h-5" :class="programTab === 'phd' ? 'text-white' : 'text-indigo-600 dark:text-indigo-400'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0112 20.055a11.952 11.952 0 01-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"></path>
                        </svg>
                        <span class="text-xs sm:text-sm tracking-wide">PhD Students</span>
                    </div>
                    <span class="hidden sm:inline-flex px-2.5 py-0.5 rounded-full text-xs font-extrabold transition-colors" :class="programTab === 'phd' ? 'bg-white/20 text-white' : 'bg-indigo-100 text-indigo-800 dark:bg-indigo-900/60 dark:text-indigo-300'">
                        {{ $actingPhdStudents->merge($vestedPhdStudents)->unique('id')->count() }}
                    </span>
                </button>

                <!-- MS(R) Students Program Tab -->
                <button type="button" 
                        @click="programTab = 'msr'; searchQuery = ''" 
                        :class="programTab === 'msr' 
                            ? 'bg-indigo-600 text-white shadow-md rounded-xl font-bold' 
                            : 'bg-white dark:bg-gray-700/80 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 rounded-xl font-semibold'" 
                        class="w-full py-3 sm:py-4 px-2 sm:px-6 transition-all duration-200 flex items-center justify-center sm:justify-between group">
                    <div class="flex items-center space-x-1.5 sm:space-x-2.5">
                        <svg class="hidden sm:block w-5 h-5" :class="programTab === 'msr' ? 'text-white' : 'text-indigo-600 dark:text-indigo-400'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                        </svg>
                        <span class="text-xs sm:text-sm tracking-wide">MS(R) Students</span>
                    </div>
                    <span class="hidden sm:inline-flex px-2.5 py-0.5 rounded-full text-xs font-extrabold transition-colors" :class="programTab === 'msr' ? 'bg-white/20 text-white' : 'bg-indigo-100 text-indigo-800 dark:bg-indigo-900/60 dark:text-indigo-300'">
                        {{ $actingMsrStudents->merge($vestedMsrStudents)->unique('id')->count() }}
                    </span>
                </button>
            </div>

            <!-- Navigation Sub-Tabs (Acting DOAA vs Vested DOAA) styled like Faculty dashboard -->
            <div class="space-y-4">
                <div class="grid grid-cols-2 gap-2 sm:gap-4 w-full">
                    <!-- Acting DOAA Tab Card -->
                    <button type="button" 
                            @click="subTab = 'acting'" 
                            :class="subTab === 'acting' 
                                ? 'bg-indigo-600 text-white border-2 border-indigo-600 shadow-md ring-2 ring-indigo-500/20 scale-[1.01]' 
                                : 'bg-white dark:bg-gray-800/90 border-2 border-gray-200 dark:border-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700'" 
                            class="w-full px-2 sm:px-5 py-2 rounded-2xl font-semibold text-xs sm:text-sm transition-all duration-200 flex items-center justify-center sm:justify-between group text-center sm:text-left">
                        <div class="flex items-center space-x-1.5 sm:space-x-3">
                            <svg class="hidden sm:block w-5 h-5 transition-colors" :class="subTab === 'acting' ? 'text-white' : 'text-indigo-600 dark:text-indigo-400'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                            <span class="font-bold tracking-wide" :class="subTab === 'acting' ? 'text-white' : 'text-gray-800 dark:text-gray-100'">Acting DOAA</span>
                        </div>
                        <span class="hidden sm:inline-flex font-extrabold text-sm px-2.5 py-0.5 rounded-lg transition-colors" :class="subTab === 'acting' ? 'bg-white/20 text-white' : 'bg-indigo-100 text-indigo-800 dark:bg-indigo-900/60 dark:text-indigo-300'">
                            <span x-show="programTab === 'phd'">{{ $actingPhdStudents->count() }}</span>
                            <span x-show="programTab === 'msr'">{{ $actingMsrStudents->count() }}</span>
                        </span>
                    </button>

                    <!-- Vested DOAA Tab Card -->
                    <button type="button" 
                            @click="subTab = 'vested'" 
                            :class="subTab === 'vested' 
                                ? 'bg-blue-600 text-white border-2 border-blue-600 shadow-md ring-2 ring-blue-500/20 scale-[1.01]' 
                                : 'bg-white dark:bg-gray-800/90 border-2 border-gray-200 dark:border-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700'" 
                            class="w-full px-2 sm:px-5 py-2 rounded-2xl font-semibold text-xs sm:text-sm transition-all duration-200 flex items-center justify-center sm:justify-between group text-center sm:text-left">
                        <div class="flex items-center space-x-1.5 sm:space-x-3">
                            <svg class="hidden sm:block w-5 h-5 transition-colors" :class="subTab === 'vested' ? 'text-white' : 'text-blue-600 dark:text-blue-400'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                            <span class="font-bold tracking-wide" :class="subTab === 'vested' ? 'text-white' : 'text-gray-800 dark:text-gray-100'">Vested DOAA</span>
                        </div>
                        <span class="hidden sm:inline-flex font-extrabold text-sm px-2.5 py-0.5 rounded-lg transition-colors" :class="subTab === 'vested' ? 'bg-white/20 text-white' : 'bg-blue-100 text-blue-800 dark:bg-blue-900/60 dark:text-blue-300'">
                            <span x-show="programTab === 'phd'">{{ $vestedPhdStudents->count() }}</span>
                            <span x-show="programTab === 'msr'">{{ $vestedMsrStudents->count() }}</span>
                        </span>
                    </button>
                </div>
            </div>

            <!-- Student Directory Card -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-xl border border-gray-200 dark:border-gray-700 p-6 space-y-6">
                
                <!-- Filter Bar: Search & Department Selection -->
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 border-b border-gray-100 dark:border-gray-700 pb-4">
                    <div>
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white flex items-center gap-2">
                            <span>Student Submissions</span>
                            <span class="text-xs px-2.5 py-0.5 rounded-full bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 font-semibold" x-text="programTab.toUpperCase()"></span>
                        </h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                            Filter students by department or search by name and roll number.
                        </p>
                    </div>

                    <div class="flex flex-wrap items-center gap-3">
                        <!-- Custom Searchable Department Dropdown -->
                        <div class="relative" @click.away="deptDropdownOpen = false">
                            <button type="button" 
                                    @click="deptDropdownOpen = !deptDropdownOpen" 
                                    class="w-full sm:w-56 px-3.5 py-2 bg-gray-50 dark:bg-gray-900 text-gray-800 dark:text-gray-200 rounded-xl border border-gray-200 dark:border-gray-700 text-xs font-semibold flex items-center justify-between hover:bg-gray-100 dark:hover:bg-gray-800 transition shadow-sm">
                                <span class="truncate" x-text="selectedDeptName"></span>
                                <svg class="w-4 h-4 text-gray-400 shrink-0 ml-2 transform transition-transform" :class="deptDropdownOpen ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </button>

                            <!-- Dropdown Menu -->
                            <div x-show="deptDropdownOpen" 
                                 x-cloak 
                                 x-transition:enter="transition ease-out duration-100" 
                                 x-transition:enter-start="transform opacity-0 scale-95" 
                                 x-transition:enter-end="transform opacity-100 scale-100" 
                                 class="absolute right-0 mt-2 w-72 bg-white dark:bg-gray-800 rounded-xl shadow-xl border border-gray-200 dark:border-gray-700 py-1.5 z-50 max-h-60 overflow-y-auto">
                                
                                <button type="button" 
                                        @click="selectDepartment('all', 'All Departments ({{ $departments->count() }})')" 
                                        class="w-full px-4 py-2 text-left text-xs font-semibold hover:bg-indigo-50 dark:hover:bg-indigo-950/50 flex items-center justify-between transition" 
                                        :class="selectedDept === 'all' ? 'text-indigo-600 dark:text-indigo-400 bg-indigo-50/70 dark:bg-indigo-950/30' : 'text-gray-700 dark:text-gray-300'">
                                    <span>All Departments ({{ $departments->count() }})</span>
                                    <svg x-show="selectedDept === 'all'" class="w-4 h-4 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                </button>

                                <div class="border-t border-gray-100 dark:border-gray-700 my-1"></div>

                                <div class="px-1">
                                    @foreach($departments as $dept)
                                        <button type="button" 
                                                @click="selectDepartment('{{ $dept->id }}', '{{ $dept->name }} ({{ $dept->code }})')" 
                                                class="w-full px-3 py-1.5 text-left text-xs rounded-lg hover:bg-indigo-50 dark:hover:bg-indigo-950/50 flex items-center justify-between transition" 
                                                :class="selectedDept === '{{ $dept->id }}' ? 'text-indigo-600 dark:text-indigo-400 font-bold bg-indigo-50/70 dark:bg-indigo-950/30' : 'text-gray-700 dark:text-gray-300'">
                                            <div class="flex items-center space-x-2 truncate">
                                                <span class="w-8 shrink-0 text-center font-bold px-1.5 py-0.5 rounded bg-gray-100 dark:bg-gray-700 text-[10px]">{{ $dept->code }}</span>
                                                <span class="truncate">{{ $dept->name }}</span>
                                            </div>
                                            <svg x-show="selectedDept === '{{ $dept->id }}'" class="w-4 h-4 text-indigo-600 dark:text-indigo-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                            </svg>
                                        </button>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <!-- Search Input Bar -->
                        <div class="w-full sm:w-72 lg:w-80 relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                            </div>
                            <input type="text" x-model="searchQuery" placeholder="Search by Name, Roll No" class="w-full pl-9 pr-9 py-2 bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-white rounded-xl border border-gray-200 dark:border-gray-700 focus:ring-2 focus:ring-indigo-500 text-xs font-medium transition shadow-sm">
                            <button x-show="searchQuery" @click="searchQuery = ''" type="button" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Active Filter Pill (if a specific department is selected) -->
                <div x-show="selectedDept !== 'all'" x-cloak class="flex items-center gap-2 pt-1">
                    <span class="text-xs text-gray-500 dark:text-gray-400 font-medium">Filtered by Department:</span>
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-indigo-100 dark:bg-indigo-950/60 text-indigo-800 dark:text-indigo-300 text-xs font-bold rounded-full border border-indigo-200 dark:border-indigo-800 shadow-sm">
                        <span x-text="selectedDeptName"></span>
                        <button type="button" @click="selectDepartment('all', 'All Departments ({{ $departments->count() }})')" class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-900 dark:hover:text-indigo-100 font-bold focus:outline-none ml-1 text-sm">
                            &times;
                        </button>
                    </span>
                </div>

                <!-- SECTION 1: PhD Students List -->
                <div x-show="programTab === 'phd'">
                    <!-- PhD: Acting DOAA Subtab -->
                    <div x-show="subTab === 'acting'">
                        @forelse($actingPhdStudents as $student)
                            <x-student-card :student="$student" :user="$user" :subTab="'acting'" />
                        @empty
                            <div class="text-center py-8 text-sm text-gray-500 dark:text-gray-400 font-medium bg-gray-50 dark:bg-gray-800/50 rounded-xl border border-dashed border-gray-200 dark:border-gray-700">
                                No PhD students found where you are appointed as Acting DOAA.
                            </div>
                        @endforelse
                    </div>

                    <!-- PhD: Vested DOAA Subtab -->
                    <div x-show="subTab === 'vested'">
                        @forelse($vestedPhdStudents as $student)
                            <x-student-card :student="$student" :user="$user" :subTab="'vested'" />
                        @empty
                            <div class="text-center py-8 text-sm text-gray-500 dark:text-gray-400 font-medium bg-gray-50 dark:bg-gray-800/50 rounded-xl border border-dashed border-gray-200 dark:border-gray-700">
                                No PhD students found where you are designated as Vested DOAA.
                            </div>
                        @endforelse
                    </div>
                </div>

                <!-- SECTION 2: MS(R) Students List -->
                <div x-show="programTab === 'msr'">
                    <!-- MS(R): Acting DOAA Subtab -->
                    <div x-show="subTab === 'acting'">
                        @forelse($actingMsrStudents as $student)
                            <x-student-card :student="$student" :user="$user" :subTab="'acting'" />
                        @empty
                            <div class="text-center py-8 text-sm text-gray-500 dark:text-gray-400 font-medium bg-gray-50 dark:bg-gray-800/50 rounded-xl border border-dashed border-gray-200 dark:border-gray-700">
                                No MS(R) students found where you are appointed as Acting DOAA.
                            </div>
                        @endforelse
                    </div>

                    <!-- MS(R): Vested DOAA Subtab -->
                    <div x-show="subTab === 'vested'">
                        @forelse($vestedMsrStudents as $student)
                            <x-student-card :student="$student" :user="$user" :subTab="'vested'" />
                        @empty
                            <div class="text-center py-8 text-sm text-gray-500 dark:text-gray-400 font-medium bg-gray-50 dark:bg-gray-800/50 rounded-xl border border-dashed border-gray-200 dark:border-gray-700">
                                No MS(R) students found where you are designated as Vested DOAA.
                            </div>
                        @endforelse
                    </div>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>

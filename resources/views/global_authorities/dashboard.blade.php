<x-app-layout>
    <div class="py-8" x-data="{
        programTab: 'phd',
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
                        @if($user->isDoaa()) Dean of Academic Affairs (DOAA) Executive Dashboard @endif
                        @if($user->isAdoaa()) Associate Dean of Academic Affairs (ADoAA) Dashboard @endif
                        @if($user->isSenateChairperson()) Senate Chairperson Executive Dashboard @endif
                        @if($user->isArAcademic()) Assistant Registrar (Academic) Dashboard @endif
                        @if($user->isAcademicOffice()) Academic Office Dashboard @endif
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
                            ? 'bg-indigo-600 text-white shadow-md rounded-xl font-bold' 
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

            <!-- Institute Students List Card -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 space-y-4">
                <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-3 border-b border-gray-100 dark:border-gray-700 pb-3">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white flex items-center">
                        <span class="w-2.5 h-2.5 rounded-full bg-indigo-600 mr-2"></span>
                        <span x-show="programTab === 'phd'">Institute PhD Students ({{ $phdStudents->count() }})</span>
                        <span x-show="programTab === 'msr'">Institute MS(R) Students ({{ $msrStudents->count() }})</span>
                    </h3>
                    
                    <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
                        <!-- Custom Department Filter Dropdown -->
                        <div class="relative min-w-[220px] sm:min-w-[260px]" @click.outside="deptDropdownOpen = false" @keydown.escape.window="deptDropdownOpen = false">
                            <button type="button" 
                                    @click="deptDropdownOpen = !deptDropdownOpen" 
                                    class="w-full flex items-center justify-between gap-2 px-3.5 py-2 bg-white dark:bg-gray-800 text-gray-800 dark:text-gray-200 rounded-xl border border-gray-200 dark:border-gray-700 hover:border-indigo-400 dark:hover:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-xs font-semibold shadow-sm transition-all duration-150"
                                    :class="{ 'ring-2 ring-indigo-500 border-transparent': deptDropdownOpen, 'bg-indigo-50/50 dark:bg-indigo-950/30 border-indigo-300 dark:border-indigo-700 text-indigo-700 dark:text-indigo-300': selectedDept !== 'all' }">
                                <div class="flex items-center gap-2 truncate">
                                    <svg class="w-4 h-4 text-indigo-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                    </svg>
                                    <span class="truncate" x-text="selectedDeptName"></span>
                                </div>
                                <svg class="w-3.5 h-3.5 text-gray-400 shrink-0 transition-transform duration-200" :class="{ 'rotate-180 text-indigo-500': deptDropdownOpen }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </button>

                            <!-- Floating Dropdown Menu -->
                            <div x-show="deptDropdownOpen" 
                                 x-transition:enter="transition ease-out duration-100" 
                                 x-transition:enter-start="transform opacity-0 scale-95 -translate-y-1" 
                                 x-transition:enter-end="transform opacity-100 scale-100 translate-y-0" 
                                 x-transition:leave="transition ease-in duration-75" 
                                 x-transition:leave-start="transform opacity-100 scale-100 translate-y-0" 
                                 x-transition:leave-end="transform opacity-0 scale-95 -translate-y-1" 
                                 class="absolute left-0 right-0 mt-1.5 max-h-64 overflow-y-auto z-50 bg-white dark:bg-gray-800 rounded-xl shadow-2xl border border-gray-200 dark:border-gray-700 py-1 divide-y divide-gray-100 dark:divide-gray-700/60"
                                 style="display: none;">
                                
                                <!-- All Departments Option -->
                                <button type="button" 
                                        @click="selectDepartment('all', 'All Departments ({{ $departments->count() }})')" 
                                        class="w-full text-left px-3.5 py-2.5 hover:bg-indigo-50/80 dark:hover:bg-indigo-950/40 text-xs font-semibold text-gray-700 dark:text-gray-300 transition-colors flex items-center justify-between group"
                                        :class="{ 'bg-indigo-50 dark:bg-indigo-950/50 text-indigo-700 dark:text-indigo-300': selectedDept === 'all' }">
                                    <div class="flex items-center gap-2">
                                        <span class="w-2 h-2 rounded-full" :class="selectedDept === 'all' ? 'bg-indigo-600' : 'bg-gray-300 dark:bg-gray-600'"></span>
                                        <span>All Departments</span>
                                    </div>
                                    <span class="text-[11px] font-bold px-2 py-0.5 rounded-full bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 group-hover:bg-indigo-200/60 dark:group-hover:bg-indigo-900/60">
                                        {{ $departments->count() }}
                                    </span>
                                </button>

                                <!-- Department Options -->
                                <div class="py-1">
                                    @foreach($departments as $dept)
                                        <button type="button" 
                                                @click="selectDepartment('{{ $dept->id }}', @js(($dept->code ? $dept->code . ' - ' : '') . $dept->name))" 
                                                class="w-full text-left px-3.5 py-2 hover:bg-indigo-50/80 dark:hover:bg-indigo-950/40 text-xs text-gray-700 dark:text-gray-300 transition-colors flex items-center justify-between group"
                                                :class="{ 'bg-indigo-50 dark:bg-indigo-950/50 text-indigo-700 dark:text-indigo-300 font-bold': selectedDept === '{{ $dept->id }}' }">
                                            <div class="flex items-center gap-2 truncate pr-2">
                                                @if($dept->code)
                                                    <span class="px-1.5 py-0.5 rounded text-[10px] font-extrabold bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 border border-gray-200 dark:border-gray-600 group-hover:border-indigo-300 shrink-0">
                                                        {{ $dept->code }}
                                                    </span>
                                                @endif
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
                            <input type="text" x-model="searchQuery" placeholder="Search by Name, Roll No..." class="w-full pl-9 pr-9 py-2 bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-white rounded-xl border border-gray-200 dark:border-gray-700 focus:ring-2 focus:ring-indigo-500 text-xs font-medium transition shadow-sm">
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
                    @forelse($phdStudents as $student)
                        <x-student-card :student="$student" :user="$user" :isMsr="false" />
                    @empty
                        <div class="text-center py-8 text-sm text-gray-500 dark:text-gray-400 font-medium bg-gray-50 dark:bg-gray-800/50 rounded-xl border border-dashed border-gray-200 dark:border-gray-700">
                            No PhD students found matching criteria.
                        </div>
                    @endforelse
                </div>

                <!-- SECTION 2: MS(R) Students List -->
                <div x-show="programTab === 'msr'">
                    @forelse($msrStudents as $student)
                        <x-student-card :student="$student" :user="$user" :isMsr="true" />
                    @empty
                        <div class="text-center py-8 text-sm text-gray-500 dark:text-gray-400 font-medium bg-gray-50 dark:bg-gray-800/50 rounded-xl border border-dashed border-gray-200 dark:border-gray-700">
                            No MS(R) students found matching criteria.
                        </div>
                    @endforelse
                </div>

            </div>
        </div>
    </div>
</x-app-layout>

<x-app-layout>
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
        },
        matchesFilter(target, dept = null) {
            return this.matchesSearch(target);
        }
    }">
        <div class="max-w-7xl mx-auto px-2 sm:px-6 lg:px-8 space-y-6">

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
                        class="w-full py-3 sm:py-4 px-2 sm:px-6 transition-all duration-200 flex items-center justify-center sm:justify-between group">
                    <div class="flex items-center space-x-1.5 sm:space-x-2.5">
                        <svg class="hidden sm:block w-5 h-5" :class="programTab === 'phd' ? 'text-white' : 'text-indigo-600 dark:text-indigo-400'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0112 20.055a11.952 11.952 0 01-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"></path>
                        </svg>
                        <span class="text-xs sm:text-sm tracking-wide">PhD Students</span>
                    </div>
                    <span class="hidden sm:inline-flex px-2.5 py-0.5 rounded-full text-xs font-extrabold transition-colors" :class="programTab === 'phd' ? 'bg-white/20 text-white' : 'bg-indigo-100 text-indigo-800 dark:bg-indigo-900/60 dark:text-indigo-300'">
                        {{ $mainStudents->filter(fn($s) => $s->isPhd())->count() + $coStudents->filter(fn($s) => $s->isPhd())->count() + $pspcStudents->filter(fn($s) => $s->isPhd())->count() }}
                    </span>
                </button>

                <!-- MS(R) Students Program Tab -->
                <button type="button" 
                        @click="programTab = 'msr'" 
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
                        {{ $mainStudents->filter(fn($s) => $s->isMsr())->count() + $coStudents->filter(fn($s) => $s->isMsr())->count() + $pspcStudents->filter(fn($s) => $s->isMsr())->count() }}
                    </span>
                </button>
            </div>

            <!-- Navigation Sub-Tabs & Search Input Bar -->
            <div class="space-y-4">
                <!-- Role-Based Navigation Sub-Tabs -->
                <div class="grid grid-cols-3 gap-2 sm:gap-4 w-full">
                    <!-- Main Supervisor Tab Card -->
                    <button type="button" 
                            @click="activeTab = 'main'" 
                            :class="activeTab === 'main' 
                                ? 'bg-indigo-600 text-white border-2 border-indigo-600 shadow-md ring-2 ring-indigo-500/20 scale-[1.01]' 
                                : 'bg-white dark:bg-gray-800/90 border-2 border-gray-200 dark:border-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700'" 
                            class="w-full px-2 sm:px-5 py-2 rounded-2xl font-semibold text-xs sm:text-sm transition-all duration-200 flex items-center justify-center sm:justify-between group text-center sm:text-left">
                        <div class="flex items-center space-x-1.5 sm:space-x-3">
                            <svg class="hidden sm:block w-5 h-5 transition-colors" :class="activeTab === 'main' ? 'text-white' : 'text-indigo-600 dark:text-indigo-400'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                            <span class="font-bold tracking-wide" :class="activeTab === 'main' ? 'text-white' : 'text-gray-800 dark:text-gray-100'">Main supervisor</span>
                        </div>
                        <span class="hidden sm:inline-flex font-extrabold text-sm px-2.5 py-0.5 rounded-lg transition-colors" :class="activeTab === 'main' ? 'bg-white/20 text-white' : 'bg-indigo-100 text-indigo-800 dark:bg-indigo-900/60 dark:text-indigo-300'">
                            <span x-show="programTab === 'phd'">{{ $mainStudents->filter(fn($s) => $s->isPhd())->count() }}</span>
                            <span x-show="programTab === 'msr'">{{ $mainStudents->filter(fn($s) => $s->isMsr())->count() }}</span>
                        </span>
                    </button>

                    <!-- Co-Supervisor Tab Card -->
                    <button type="button" 
                            @click="activeTab = 'co'" 
                            :class="activeTab === 'co' 
                                ? 'bg-blue-600 text-white border-2 border-blue-600 shadow-md ring-2 ring-blue-500/20 scale-[1.01]' 
                                : 'bg-white dark:bg-gray-800/90 border-2 border-gray-200 dark:border-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700'" 
                            class="w-full px-2 sm:px-5 py-2 rounded-2xl font-semibold text-xs sm:text-sm transition-all duration-200 flex items-center justify-center sm:justify-between group text-center sm:text-left">
                        <div class="flex items-center space-x-1.5 sm:space-x-3">
                            <svg class="hidden sm:block w-5 h-5 transition-colors" :class="activeTab === 'co' ? 'text-white' : 'text-blue-600 dark:text-blue-400'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                            <span class="font-bold tracking-wide" :class="activeTab === 'co' ? 'text-white' : 'text-gray-800 dark:text-gray-100'">Co-supervisor</span>
                        </div>
                        <span class="hidden sm:inline-flex font-extrabold text-sm px-2.5 py-0.5 rounded-lg transition-colors" :class="activeTab === 'co' ? 'bg-white/20 text-white' : 'bg-blue-100 text-blue-800 dark:bg-blue-900/60 dark:text-blue-300'">
                            <span x-show="programTab === 'phd'">{{ $coStudents->filter(fn($s) => $s->isPhd())->count() }}</span>
                            <span x-show="programTab === 'msr'">{{ $coStudents->filter(fn($s) => $s->isMsr())->count() }}</span>
                        </span>
                    </button>

                    <!-- PSPC Member Tab Card -->
                    <button type="button" 
                            @click="activeTab = 'pspc'" 
                            :class="activeTab === 'pspc' 
                                ? 'bg-emerald-600 text-white border-2 border-emerald-600 shadow-md ring-2 ring-emerald-500/20 scale-[1.01]' 
                                : 'bg-white dark:bg-gray-800/90 border-2 border-gray-200 dark:border-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700'" 
                            class="w-full px-2 sm:px-5 py-2 rounded-2xl font-semibold text-xs sm:text-sm transition-all duration-200 flex items-center justify-center sm:justify-between group text-center sm:text-left">
                        <div class="flex items-center space-x-1.5 sm:space-x-3">
                            <svg class="hidden sm:block w-5 h-5 transition-colors" :class="activeTab === 'pspc' ? 'text-white' : 'text-emerald-600 dark:text-emerald-400'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0112 20.055a11.952 11.952 0 01-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"></path>
                            </svg>
                            <span class="font-bold tracking-wide" :class="activeTab === 'pspc' ? 'text-white' : 'text-gray-800 dark:text-gray-100'">PSPC member</span>
                        </div>
                        <span class="hidden sm:inline-flex font-extrabold text-sm px-2.5 py-0.5 rounded-lg transition-colors" :class="activeTab === 'pspc' ? 'bg-white/20 text-white' : 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/60 dark:text-emerald-300'">
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

                    <div x-show="programTab === 'phd' && {{ $phdMainCount }} === 0" class="text-center py-8 bg-gray-50 dark:bg-gray-900/50 rounded-xl border border-dashed border-gray-200 dark:border-gray-700">
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">No PhD students assigned to you for this role.</p>
                    </div>
                    <div x-show="programTab === 'msr' && {{ $msrMainCount }} === 0" class="text-center py-8 bg-gray-50 dark:bg-gray-900/50 rounded-xl border border-dashed border-gray-200 dark:border-gray-700">
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">No MS(R) students assigned to you for this role.</p>
                    </div>

                    @foreach($mainStudents as $student)
                        <div x-show="programTab === '{{ $student->isPhd() ? 'phd' : 'msr' }}'">
                            <x-student-card :student="$student" :user="$user" :role="'main'" />
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

                    <div x-show="programTab === 'phd' && {{ $phdCoCount }} === 0" class="text-center py-8 bg-gray-50 dark:bg-gray-900/50 rounded-xl border border-dashed border-gray-200 dark:border-gray-700">
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">No PhD students assigned to you for this role.</p>
                    </div>
                    <div x-show="programTab === 'msr' && {{ $msrCoCount }} === 0" class="text-center py-8 bg-gray-50 dark:bg-gray-900/50 rounded-xl border border-dashed border-gray-200 dark:border-gray-700">
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">No MS(R) students assigned to you for this role.</p>
                    </div>

                    @foreach($coStudents as $student)
                        <div x-show="programTab === '{{ $student->isPhd() ? 'phd' : 'msr' }}'">
                            <x-student-card :student="$student" :user="$user" :role="'co'" />
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

                    <div x-show="programTab === 'phd' && {{ $phdPspcCount }} === 0" class="text-center py-8 bg-gray-50 dark:bg-gray-900/50 rounded-xl border border-dashed border-gray-200 dark:border-gray-700">
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">No PhD students assigned to you for this role.</p>
                    </div>
                    <div x-show="programTab === 'msr' && {{ $msrPspcCount }} === 0" class="text-center py-8 bg-gray-50 dark:bg-gray-900/50 rounded-xl border border-dashed border-gray-200 dark:border-gray-700">
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">No MS(R) students assigned to you for this role.</p>
                    </div>

                    @foreach($pspcStudents as $student)
                        <div x-show="programTab === '{{ $student->isPhd() ? 'phd' : 'msr' }}'">
                            <x-student-card :student="$student" :user="$user" :role="'pspc'" />
                        </div>
                    @endforeach
                </div>
            </div>

        </div>
    </div>
</x-app-layout>

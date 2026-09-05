<x-app-layout>
    <x-admin-top-navbar />

    <div class="py-4 sm:py-8" x-data="{ 
        editModalOpen: false, 
        editId: '', 
        editName: '', 
        editEmail: '',
        searchQuery: '{{ request('search') }}',
        selectedDept: '{{ request('department_id', 'all') }}',
        selectedDeptName: 'All Departments',
        deptDropdownOpen: false,
        confirmModalOpen: false,
        confirmTitle: '',
        confirmMessage: '',
        confirmFormAction: '',
        confirmBtnText: 'Confirm',
        confirmBtnClass: 'bg-rose-600 hover:bg-rose-700 text-white',
        sortCol: '',
        sortDir: 'asc',
        sortTable(col) {
            if (this.sortCol === col) {
                this.sortDir = this.sortDir === 'asc' ? 'desc' : 'asc';
            } else {
                this.sortCol = col;
                this.sortDir = 'asc';
            }
            const tableBody = this.$refs.tableBody;
            if (!tableBody) return;
            const rows = Array.from(tableBody.querySelectorAll('tr[data-sortable]'));
            rows.sort((a, b) => {
                const valA = (a.dataset[col] || '').toLowerCase();
                const valB = (b.dataset[col] || '').toLowerCase();
                const cmp = valA.localeCompare(valB);
                return this.sortDir === 'asc' ? cmp : -cmp;
            });
            rows.forEach(row => tableBody.appendChild(row));
        },
        openConfirm(title, message, formAction, btnText = 'Confirm', btnClass = 'bg-rose-600 hover:bg-rose-700 text-white') {
            this.confirmTitle = title;
            this.confirmMessage = message;
            this.confirmFormAction = formAction;
            this.confirmBtnText = btnText;
            this.confirmBtnClass = btnClass;
            this.confirmModalOpen = true;
        },
        selectDepartment(id, name) {
            this.selectedDept = id;
            this.selectedDeptName = name;
            this.deptDropdownOpen = false;
        },
        matchesFilter(targetText, deptId) {
            const matchesDept = (this.selectedDept === 'all') || (String(this.selectedDept) === String(deptId));
            if (!matchesDept) return false;
            if (!this.searchQuery || !this.searchQuery.trim()) return true;
            return targetText.toLowerCase().includes(this.searchQuery.toLowerCase().trim());
        }
    }">
        <div class="max-w-7xl mx-auto px-3 sm:px-6 lg:px-8 space-y-4 sm:space-y-6">
            
            <!-- Navigation Back Button & Page Title Component -->
            <x-admin-header title="Faculties & External Supervisors" description="Manage institute faculty members and external co-supervisors." />

            @if($errors->any())
                <div class="bg-rose-50 dark:bg-rose-900/30 border border-rose-200 dark:border-rose-800 text-rose-800 dark:text-rose-300 px-4 py-3 rounded-xl text-sm shadow-sm">
                    {{ $errors->first() }}
                </div>
            @endif

            <!-- Search & Department Filter Controls -->
            <div class="bg-white dark:bg-slate-900 p-4 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-800 flex flex-col md:flex-row justify-between items-stretch md:items-center gap-4">
                
                <!-- Live Search Box -->
                <div class="relative w-full md:w-96">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </div>
                    <input type="text" x-model="searchQuery" placeholder="Search by name or email" class="w-full pl-10 pr-4 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm focus:ring-indigo-500 focus:border-indigo-500 dark:text-white">
                </div>

                <!-- Department Filter Dropdown -->
                <div class="flex items-center gap-3 w-full md:w-auto">
                    <div class="relative w-full md:w-64" @click.away="deptDropdownOpen = false">
                        <button @click="deptDropdownOpen = !deptDropdownOpen" class="w-full flex items-center justify-between px-4 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-semibold text-slate-700 dark:text-slate-300">
                            <span x-text="selectedDeptName">All Departments</span>
                            <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div x-show="deptDropdownOpen" style="display: none;" class="absolute z-50 mt-2 w-full bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl shadow-xl py-1 text-xs">
                            <button @click="selectDepartment('all', 'All Departments')" class="w-full text-left px-4 py-2 hover:bg-slate-100 dark:hover:bg-slate-700 dark:text-slate-200">All Departments</button>
                            @foreach($departments as $dept)
                                <button @click="selectDepartment('{{ $dept->id }}', '{{ addslashes($dept->name) }} ({{ $dept->code }})')" class="w-full text-left px-4 py-2 hover:bg-slate-100 dark:hover:bg-slate-700 dark:text-slate-200">
                                    {{ $dept->name }} ({{ $dept->code }})
                                </button>
                            @endforeach
                        </div>
                    </div>
                </div>

            </div>

            <!-- Faculties Table -->
            <div class="bg-white dark:bg-slate-900 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-800 overflow-hidden">
                <div class="p-5 border-b border-slate-100 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-800/50 flex justify-between items-center">
                    <div>
                        <h3 class="font-bold text-slate-800 dark:text-slate-200">Registered Faculty & External Supervisors</h3>
                        <span class="text-xs text-slate-500">Total Records: {{ $faculties->count() }}</span>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="border-b border-slate-200 dark:border-slate-800 text-xs font-semibold text-slate-500 uppercase bg-slate-50 dark:bg-slate-800/40">
                                <th @click="sortTable('name')" class="p-4 text-center min-w-[140px] max-w-[200px] cursor-pointer select-none group hover:bg-slate-100 dark:hover:bg-slate-800/80 transition">
                                    <div class="inline-flex items-center justify-center space-x-1">
                                        <span>Faculty Name</span>
                                        <span class="inline-flex flex-col text-[9px] leading-none text-slate-400 group-hover:text-slate-600 dark:group-hover:text-slate-300">
                                            <svg class="w-2.5 h-2.5" :class="{ 'text-indigo-600 dark:text-indigo-400 font-bold scale-125': sortCol === 'name' && sortDir === 'asc' }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 15l7-7 7 7"/></svg>
                                            <svg class="w-2.5 h-2.5 -mt-0.5" :class="{ 'text-indigo-600 dark:text-indigo-400 font-bold scale-125': sortCol === 'name' && sortDir === 'desc' }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M19 9l-7 7-7-7"/></svg>
                                        </span>
                                    </div>
                                </th>
                                <th @click="sortTable('email')" class="p-4 text-center min-w-[180px] max-w-[240px] cursor-pointer select-none group hover:bg-slate-100 dark:hover:bg-slate-800/80 transition">
                                    <div class="inline-flex items-center justify-center space-x-1">
                                        <span>Email</span>
                                        <span class="inline-flex flex-col text-[9px] leading-none text-slate-400 group-hover:text-slate-600 dark:group-hover:text-slate-300">
                                            <svg class="w-2.5 h-2.5" :class="{ 'text-indigo-600 dark:text-indigo-400 font-bold scale-125': sortCol === 'email' && sortDir === 'asc' }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 15l7-7 7 7"/></svg>
                                            <svg class="w-2.5 h-2.5 -mt-0.5" :class="{ 'text-indigo-600 dark:text-indigo-400 font-bold scale-125': sortCol === 'email' && sortDir === 'desc' }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M19 9l-7 7-7-7"/></svg>
                                        </span>
                                    </div>
                                </th>
                                <th @click="sortTable('role')" class="p-4 text-center min-w-[150px] cursor-pointer select-none group hover:bg-slate-100 dark:hover:bg-slate-800/80 transition">
                                    <div class="inline-flex items-center justify-center space-x-1">
                                        <span>Role / Designation</span>
                                        <span class="inline-flex flex-col text-[9px] leading-none text-slate-400 group-hover:text-slate-600 dark:group-hover:text-slate-300">
                                            <svg class="w-2.5 h-2.5" :class="{ 'text-indigo-600 dark:text-indigo-400 font-bold scale-125': sortCol === 'role' && sortDir === 'asc' }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 15l7-7 7 7"/></svg>
                                            <svg class="w-2.5 h-2.5 -mt-0.5" :class="{ 'text-indigo-600 dark:text-indigo-400 font-bold scale-125': sortCol === 'role' && sortDir === 'desc' }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M19 9l-7 7-7-7"/></svg>
                                        </span>
                                    </div>
                                </th>
                                <th @click="sortTable('dept')" class="p-4 text-center min-w-[140px] max-w-[200px] cursor-pointer select-none group hover:bg-slate-100 dark:hover:bg-slate-800/80 transition">
                                    <div class="inline-flex items-center justify-center space-x-1">
                                        <span>Department</span>
                                        <span class="inline-flex flex-col text-[9px] leading-none text-slate-400 group-hover:text-slate-600 dark:group-hover:text-slate-300">
                                            <svg class="w-2.5 h-2.5" :class="{ 'text-indigo-600 dark:text-indigo-400 font-bold scale-125': sortCol === 'dept' && sortDir === 'asc' }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 15l7-7 7 7"/></svg>
                                            <svg class="w-2.5 h-2.5 -mt-0.5" :class="{ 'text-indigo-600 dark:text-indigo-400 font-bold scale-125': sortCol === 'dept' && sortDir === 'desc' }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M19 9l-7 7-7-7"/></svg>
                                        </span>
                                    </div>
                                </th>
                                <th @click="sortTable('status')" class="p-4 text-center min-w-[90px] cursor-pointer select-none group hover:bg-slate-100 dark:hover:bg-slate-800/80 transition">
                                    <div class="inline-flex items-center justify-center space-x-1">
                                        <span>Status</span>
                                        <span class="inline-flex flex-col text-[9px] leading-none text-slate-400 group-hover:text-slate-600 dark:group-hover:text-slate-300">
                                            <svg class="w-2.5 h-2.5" :class="{ 'text-indigo-600 dark:text-indigo-400 font-bold scale-125': sortCol === 'status' && sortDir === 'asc' }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 15l7-7 7 7"/></svg>
                                            <svg class="w-2.5 h-2.5 -mt-0.5" :class="{ 'text-indigo-600 dark:text-indigo-400 font-bold scale-125': sortCol === 'status' && sortDir === 'desc' }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M19 9l-7 7-7-7"/></svg>
                                        </span>
                                    </div>
                                </th>
                                <th class="p-4 text-center min-w-[180px]">Actions</th>
                            </tr>
                        </thead>
                        <tbody x-ref="tableBody" class="divide-y divide-slate-100 dark:divide-slate-800 text-sm">
                            @forelse($faculties as $user)
                                @php
                                    $deptId = $user->facultyProfile->department_id ?? 'all';
                                    $deptName = $user->facultyProfile->department->name ?? ($user->role === 'external_supervisor' ? 'External' : 'N/A');
                                    $targetSearch = strtolower($user->name . ' ' . $user->email . ' ' . $deptName);
                                @endphp
                                <tr data-sortable="true" data-name="{{ addslashes($user->name) }}" data-email="{{ addslashes($user->email) }}" data-role="{{ $user->role }}" data-dept="{{ addslashes($deptName) }}" data-status="{{ $user->is_active ? 'active' : 'inactive' }}" x-show="matchesFilter('{{ addslashes($targetSearch) }}', '{{ $deptId }}')" class="hover:bg-slate-50/50 dark:hover:bg-slate-800/30 transition">
                                    <td class="p-4 font-semibold text-slate-900 dark:text-white text-center min-w-[140px] max-w-[200px] whitespace-normal break-words">
                                        {{ $user->name }}
                                    </td>
                                    <td class="p-4 text-slate-600 dark:text-slate-300 text-center min-w-[180px] max-w-[240px] whitespace-normal break-words">
                                        {{ $user->email }}
                                    </td>
                                    <td class="p-4 text-center min-w-[150px]">
                                        @if($user->role === 'external_supervisor')
                                            <span class="bg-purple-100 dark:bg-purple-900/40 text-purple-800 dark:text-purple-300 text-xs font-bold px-2.5 py-1 rounded-full border border-purple-200 dark:border-purple-800">
                                                External Supervisor
                                            </span>
                                        @else
                                            <span class="bg-blue-100 dark:bg-blue-900/40 text-blue-800 dark:text-blue-300 text-xs font-bold px-2.5 py-1 rounded-full border border-blue-200 dark:border-blue-800">
                                                Internal Faculty
                                            </span>
                                        @endif
                                    </td>
                                    <td class="p-4 text-center font-medium text-slate-700 dark:text-slate-300 min-w-[140px] max-w-[200px] whitespace-normal break-words">
                                        {{ $deptName }}
                                    </td>
                                    <td class="p-4 text-center min-w-[90px]">
                                        @if($user->is_active)
                                            <span class="bg-emerald-100 dark:bg-emerald-900/40 text-emerald-800 dark:text-emerald-300 text-xs font-medium px-2.5 py-1 rounded-full">Active</span>
                                        @else
                                            <span class="bg-slate-100 dark:bg-slate-800 text-slate-500 text-xs font-medium px-2.5 py-1 rounded-full">Inactive</span>
                                        @endif
                                    </td>
                                    <td class="p-4 text-center min-w-[180px]">
                                        <div class="flex items-center justify-center space-x-3">
                                            <button @click="editModalOpen = true; editId = '{{ $user->id }}'; editName = '{{ addslashes($user->name) }}'; editEmail = '{{ addslashes($user->email) }}'" class="text-indigo-600 hover:text-indigo-900 font-medium px-3 py-1 rounded bg-indigo-50 hover:bg-indigo-100 dark:bg-indigo-900/30 dark:text-indigo-300 transition text-xs">
                                                Edit
                                            </button>

                                            @if($user->is_active)
                                                <button type="button" @click="openConfirm('Deactivate Faculty/Supervisor', 'Are you sure you want to deactivate {{ addslashes($user->name) }}?', '{{ route('admin.users.toggle', $user->id) }}', 'Deactivate Profile', 'bg-rose-600 hover:bg-rose-700 text-white')" class="text-rose-600 hover:text-rose-900 dark:text-rose-400 dark:hover:text-rose-300 font-medium px-3 py-1 rounded bg-rose-50 hover:bg-rose-100 dark:bg-rose-900/30 dark:hover:bg-rose-900/50 transition text-xs w-24 text-center">
                                                    Deactivate
                                                </button>
                                            @else
                                                <button type="button" @click="openConfirm('Reactivate Faculty/Supervisor', 'Are you sure you want to reactivate {{ addslashes($user->name) }}?', '{{ route('admin.users.toggle', $user->id) }}', 'Reactivate Profile', 'bg-emerald-600 hover:bg-emerald-700 text-white')" class="text-emerald-600 hover:text-emerald-900 dark:text-emerald-400 dark:hover:text-emerald-300 font-medium px-3 py-1 rounded bg-emerald-50 hover:bg-emerald-100 dark:bg-emerald-900/30 transition text-xs w-24 text-center">
                                                    Activate
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="p-8 text-center text-slate-400">
                                        No faculty members or external supervisors found.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>

        <!-- Edit Faculty Name Modal (Name Only) -->
        <div x-show="editModalOpen" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div x-show="editModalOpen" class="fixed inset-0 bg-slate-900 bg-opacity-75 transition-opacity" @click="editModalOpen = false"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
                <div x-show="editModalOpen" class="inline-block align-bottom bg-white dark:bg-slate-900 rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full border border-slate-200 dark:border-slate-800">
                    <form :action="`/admin/users/${editId}/name`" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="bg-white dark:bg-slate-900 px-6 pt-6 pb-4">
                            <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-4">Edit Faculty Name</h3>
                            
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Email (Read Only)</label>
                                <input type="email" :value="editEmail" class="w-full bg-slate-100 dark:bg-slate-800 border-slate-200 dark:border-slate-700 text-slate-500 rounded-xl shadow-sm text-sm" disabled readonly>
                            </div>

                            <div class="mb-4">
                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Full Name <span class="text-rose-500">*</span></label>
                                <input type="text" name="name" x-model="editName" class="w-full border-slate-300 dark:border-slate-700 dark:bg-slate-800 text-slate-900 dark:text-white rounded-xl shadow-sm text-sm focus:ring-indigo-500 focus:border-indigo-500" required>
                            </div>
                        </div>
                        <div class="bg-slate-50 dark:bg-slate-800/50 border-t border-slate-100 dark:border-slate-800 px-6 py-4 flex flex-row-reverse gap-3">
                            <button type="submit" class="w-full sm:w-auto inline-flex justify-center rounded-xl border border-transparent px-5 py-2.5 bg-indigo-600 text-white hover:bg-indigo-700 text-sm font-semibold transition">
                                Save Name
                            </button>
                            <button type="button" @click="editModalOpen = false" class="w-full sm:w-auto inline-flex justify-center rounded-xl border border-slate-300 dark:border-slate-700 px-5 py-2.5 bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700 text-sm font-medium transition">
                                Cancel
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Reusable Custom Confirm Modal -->
        <x-confirm-modal />

    </div>
</x-app-layout>

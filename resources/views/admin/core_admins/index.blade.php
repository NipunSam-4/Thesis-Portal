<x-app-layout>
    <x-admin-top-navbar />

    <div class="py-4 sm:py-8" x-data="{ 
        createModalOpen: false, 
        editModalOpen: false, 
        showCreatePassword: false,
        showCreatePasswordConfirm: false,
        selectedUser: null, 
        editId: '', 
        editName: '', 
        editEmail: '',
        searchQuery: '{{ request('search') }}',
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
        matchesFilter(targetText) {
            if (!this.searchQuery || !this.searchQuery.trim()) return true;
            return targetText.toLowerCase().includes(this.searchQuery.toLowerCase().trim());
        }
    }">
        <div class="max-w-7xl mx-auto px-3 sm:px-6 lg:px-8 space-y-4 sm:space-y-6">

            <!-- Responsive Header Component -->
            <x-admin-header title="Manage Admins" description="Create and manage  Admin accounts" />

            @if($errors->any())
                <div class="bg-red-50 text-red-700 p-4 rounded-lg shadow-sm border border-red-200 dark:bg-red-900/30 dark:border-red-800 dark:text-red-400">
                    {{ $errors->first() }}
                </div>
            @endif

            <!-- Search Box -->
            <div class="bg-white dark:bg-gray-800 p-4 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700">
                <div class="relative w-full sm:w-96">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </div>
                    <input type="text" x-model="searchQuery" placeholder="Search admin by name or email" class="w-full pl-10 pr-4 py-2 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-lg text-sm focus:ring-emerald-500 focus:border-emerald-500 dark:text-white">
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 shadow-sm rounded-xl border border-gray-100 dark:border-gray-700 overflow-hidden">
                <div class="p-4 sm:p-6 border-b border-gray-200 dark:border-gray-700 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-gray-50 dark:bg-gray-800/50">
                    <div>
                        <h3 class="text-base sm:text-lg font-bold text-gray-900 dark:text-white">Admins</h3>
                        <span class="text-xs sm:text-sm text-gray-500 dark:text-gray-400">Total Accounts: {{ $admins->count() }}</span>
                    </div>
                    
                    <button @click="createModalOpen = true" class="w-full sm:w-auto bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-2 px-4 rounded-lg shadow-sm transition transform hover:-translate-y-0.5 flex items-center justify-center text-xs sm:text-sm">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                        Create Super Admin
                    </button>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-50 dark:bg-gray-900/50 text-gray-500 dark:text-gray-400 text-xs uppercase tracking-wider border-b border-gray-200 dark:border-gray-700">
                                <th @click="sortTable('name')" class="p-4 font-medium text-center min-w-[140px] max-w-[200px] cursor-pointer select-none group hover:bg-gray-100 dark:hover:bg-gray-700/80 transition">
                                    <div class="inline-flex items-center justify-center space-x-1">
                                        <span>Admin Name</span>
                                        <span class="inline-flex flex-col text-[9px] leading-none text-gray-400 group-hover:text-gray-600 dark:group-hover:text-gray-300">
                                            <svg class="w-2.5 h-2.5" :class="{ 'text-emerald-600 dark:text-emerald-400 font-bold scale-125': sortCol === 'name' && sortDir === 'asc' }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 15l7-7 7 7"/></svg>
                                            <svg class="w-2.5 h-2.5 -mt-0.5" :class="{ 'text-emerald-600 dark:text-emerald-400 font-bold scale-125': sortCol === 'name' && sortDir === 'desc' }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M19 9l-7 7-7-7"/></svg>
                                        </span>
                                    </div>
                                </th>
                                <th @click="sortTable('email')" class="p-4 font-medium text-center min-w-[180px] max-w-[240px] cursor-pointer select-none group hover:bg-gray-100 dark:hover:bg-gray-700/80 transition">
                                    <div class="inline-flex items-center justify-center space-x-1">
                                        <span>Admin Email</span>
                                        <span class="inline-flex flex-col text-[9px] leading-none text-gray-400 group-hover:text-gray-600 dark:group-hover:text-gray-300">
                                            <svg class="w-2.5 h-2.5" :class="{ 'text-emerald-600 dark:text-emerald-400 font-bold scale-125': sortCol === 'email' && sortDir === 'asc' }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 15l7-7 7 7"/></svg>
                                            <svg class="w-2.5 h-2.5 -mt-0.5" :class="{ 'text-emerald-600 dark:text-emerald-400 font-bold scale-125': sortCol === 'email' && sortDir === 'desc' }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M19 9l-7 7-7-7"/></svg>
                                        </span>
                                    </div>
                                </th>
                                <th @click="sortTable('role')" class="p-4 font-medium text-center min-w-[130px] cursor-pointer select-none group hover:bg-gray-100 dark:hover:bg-gray-700/80 transition">
                                    <div class="inline-flex items-center justify-center space-x-1">
                                        <span>Admin Role</span>
                                        <span class="inline-flex flex-col text-[9px] leading-none text-gray-400 group-hover:text-gray-600 dark:group-hover:text-gray-300">
                                            <svg class="w-2.5 h-2.5" :class="{ 'text-emerald-600 dark:text-emerald-400 font-bold scale-125': sortCol === 'role' && sortDir === 'asc' }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 15l7-7 7 7"/></svg>
                                            <svg class="w-2.5 h-2.5 -mt-0.5" :class="{ 'text-emerald-600 dark:text-emerald-400 font-bold scale-125': sortCol === 'role' && sortDir === 'desc' }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M19 9l-7 7-7-7"/></svg>
                                        </span>
                                    </div>
                                </th>
                                <th @click="sortTable('dept')" class="p-4 font-medium text-center min-w-[160px] max-w-[220px] cursor-pointer select-none group hover:bg-gray-100 dark:hover:bg-gray-700/80 transition">
                                    <div class="inline-flex items-center justify-center space-x-1">
                                        <span>Department</span>
                                        <span class="inline-flex flex-col text-[9px] leading-none text-gray-400 group-hover:text-gray-600 dark:group-hover:text-gray-300">
                                            <svg class="w-2.5 h-2.5" :class="{ 'text-emerald-600 dark:text-emerald-400 font-bold scale-125': sortCol === 'dept' && sortDir === 'asc' }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 15l7-7 7 7"/></svg>
                                            <svg class="w-2.5 h-2.5 -mt-0.5" :class="{ 'text-emerald-600 dark:text-emerald-400 font-bold scale-125': sortCol === 'dept' && sortDir === 'desc' }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M19 9l-7 7-7-7"/></svg>
                                        </span>
                                    </div>
                                </th>
                                <th @click="sortTable('status')" class="p-4 font-medium text-center min-w-[90px] cursor-pointer select-none group hover:bg-gray-100 dark:hover:bg-gray-700/80 transition">
                                    <div class="inline-flex items-center justify-center space-x-1">
                                        <span>Status</span>
                                        <span class="inline-flex flex-col text-[9px] leading-none text-gray-400 group-hover:text-gray-600 dark:group-hover:text-gray-300">
                                            <svg class="w-2.5 h-2.5" :class="{ 'text-emerald-600 dark:text-emerald-400 font-bold scale-125': sortCol === 'status' && sortDir === 'asc' }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 15l7-7 7 7"/></svg>
                                            <svg class="w-2.5 h-2.5 -mt-0.5" :class="{ 'text-emerald-600 dark:text-emerald-400 font-bold scale-125': sortCol === 'status' && sortDir === 'desc' }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M19 9l-7 7-7-7"/></svg>
                                        </span>
                                    </div>
                                </th>
                                <th class="p-4 font-medium text-center min-w-[180px]">Actions</th>
                            </tr>
                        </thead>
                        <tbody x-ref="tableBody" class="divide-y divide-gray-200 dark:divide-gray-700 text-sm">
                            @forelse($admins as $adminAcc)
                                @php
                                    $deptName = $adminAcc->department ? $adminAcc->department->name : 'All Departments (Global)';
                                    $targetSearch = strtolower($adminAcc->name . ' ' . $adminAcc->email);
                                @endphp
                                <tr data-sortable="true" data-name="{{ addslashes($adminAcc->name) }}" data-email="{{ addslashes($adminAcc->email) }}" data-role="{{ $adminAcc->admin_type }}" data-dept="{{ addslashes($deptName) }}" data-status="{{ $adminAcc->is_active ? 'active' : 'inactive' }}" x-show="matchesFilter('{{ addslashes($targetSearch) }}')" class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition">
                                    <td class="p-4 font-semibold text-gray-900 dark:text-gray-100 text-center min-w-[140px] max-w-[200px] whitespace-normal break-words">
                                        {{ $adminAcc->name }}
                                    </td>

                                    <td class="p-4 text-gray-600 dark:text-gray-300 font-medium text-center min-w-[180px] max-w-[240px] whitespace-normal break-words">
                                        {{ $adminAcc->email }}
                                    </td>
                                    
                                    <td class="p-4 text-center min-w-[130px]">
                                        @if($adminAcc->isSystemAdmin())
                                            <span class="bg-blue-100 text-blue-800 text-xs font-bold px-2.5 py-1 rounded dark:bg-blue-900/40 dark:text-blue-300">
                                                System Admin
                                            </span>
                                        @else
                                            <span class="bg-emerald-100 text-emerald-800 text-xs font-bold px-2.5 py-1 rounded dark:bg-emerald-900/40 dark:text-emerald-300">
                                                Super Admin
                                            </span>
                                        @endif
                                    </td>

                                    <td class="p-4 font-medium text-gray-800 dark:text-gray-200 text-center min-w-[160px] max-w-[220px] whitespace-normal break-words">
                                        @if($adminAcc->department)
                                            {{ $adminAcc->department->name }} ({{ $adminAcc->department->code }})
                                        @else
                                            <span class="text-gray-400 text-xs italic">All Departments (Global)</span>
                                        @endif
                                    </td>
                                    
                                    <td class="p-4 text-center min-w-[90px]">
                                        @if($adminAcc->is_active)
                                            <span class="bg-green-100 text-green-800 text-xs font-medium px-2.5 py-0.5 rounded-full dark:bg-green-900 dark:text-green-300">Active</span>
                                        @else
                                            <span class="bg-red-100 text-red-800 text-xs font-medium px-2.5 py-0.5 rounded-full dark:bg-red-900 dark:text-red-300">Inactive</span>
                                        @endif
                                    </td>

                                    <td class="p-4 text-center min-w-[180px]">
                                        <div class="flex items-center justify-center space-x-3">
                                            @if($adminAcc->isSystemAdmin())
                                                <span class="text-xs text-gray-400 font-semibold italic flex items-center px-2">Not Modifiable</span>
                                            @else
                                                <button @click="editModalOpen = true; editId = '{{ $adminAcc->id }}'; editName = '{{ addslashes($adminAcc->name) }}'; editEmail = '{{ addslashes($adminAcc->email) }}'" class="text-indigo-600 hover:text-indigo-900 font-medium px-3 py-1 rounded bg-indigo-50 hover:bg-indigo-100 dark:bg-indigo-900/30 dark:text-indigo-300 transition">
                                                    Edit
                                                </button>

                                                @if($adminAcc->is_active)
                                                    <button type="button" @click="openConfirm('Deactivate Super Admin', 'Are you sure you want to deactivate {{ addslashes($adminAcc->name) }}?', '{{ route('admin.admins.toggle', $adminAcc->id) }}', 'Deactivate Admin', 'bg-rose-600 hover:bg-rose-700 text-white')" class="text-rose-600 hover:text-rose-900 dark:text-rose-400 dark:hover:text-rose-300 font-medium px-3 py-1 rounded bg-rose-50 hover:bg-rose-100 dark:bg-rose-900/30 dark:hover:bg-rose-900/50 transition w-24 text-center">
                                                        Deactivate
                                                    </button>
                                                @else
                                                    <button type="button" @click="openConfirm('Reactivate Super Admin', 'Are you sure you want to reactivate {{ addslashes($adminAcc->name) }}?', '{{ route('admin.admins.toggle', $adminAcc->id) }}', 'Reactivate Admin', 'bg-emerald-600 hover:bg-emerald-700 text-white')" class="text-green-600 hover:text-green-900 dark:text-green-400 dark:hover:text-green-300 font-medium px-3 py-1 rounded bg-green-50 hover:bg-green-100 dark:bg-green-900/30 transition w-24 text-center">
                                                        Activate
                                                    </button>
                                                @endif
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="p-8 text-center text-gray-500 dark:text-gray-400">
                                        No admin accounts found.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Create Super Admin Modal -->
        <div x-show="createModalOpen" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div x-show="createModalOpen" class="fixed inset-0 bg-slate-900/75 dark:bg-slate-900/80 transition-opacity" @click="createModalOpen = false"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
                <div x-show="createModalOpen" class="inline-block align-bottom bg-white dark:bg-slate-900 rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full border border-slate-200 dark:border-slate-800">
                    
                    <form action="{{ route('admin.admins.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="role" value="super_admin">
                        <div class="bg-white dark:bg-slate-900 p-6">
                            <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-1">Create Department Super Admin</h3>
                            
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Full Name <span class="text-rose-500">*</span></label>
                                <input type="text" name="name" class="w-full border-slate-300 dark:border-slate-700 dark:bg-slate-800 text-slate-900 dark:text-white rounded-xl shadow-sm text-sm focus:ring-emerald-500" required>
                            </div>
                            
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Email Address <span class="text-rose-500">*</span></label>
                                <input type="email" name="email" class="w-full border-slate-300 dark:border-slate-700 dark:bg-slate-800 text-slate-900 dark:text-white rounded-xl shadow-sm text-sm focus:ring-emerald-500" required>
                            </div>

                            <div class="mb-4">
                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Assigned Department <span class="text-rose-500">*</span></label>
                                <select name="department_id" class="w-full border-slate-300 dark:border-slate-700 dark:bg-slate-800 text-slate-900 dark:text-white rounded-xl shadow-sm text-sm focus:ring-emerald-500" required>
                                    <option value="" disabled selected>Select Department</option>
                                    @foreach($departments as $dept)
                                        <option value="{{ $dept->id }}">{{ $dept->name }} ({{ $dept->code }})</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Password <span class="text-rose-500">*</span></label>
                                <div class="relative">
                                    <input :type="showCreatePassword ? 'text' : 'password'" name="password" class="w-full border-slate-300 dark:border-slate-700 dark:bg-slate-800 text-slate-900 dark:text-white rounded-xl shadow-sm text-sm focus:ring-emerald-500 pr-10" required minlength="8" placeholder="Minimum 8 characters">
                                    <button type="button" @click="showCreatePassword = !showCreatePassword" class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600 dark:hover:text-slate-200">
                                        <svg x-show="!showCreatePassword" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                        <svg x-show="showCreatePassword" style="display: none;" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858-5.908a10.03 10.03 0 013.982-.963c4.478 0 8.268 2.943 9.543 7a9.97 9.97 0 01-1.563 3.029m-5.858 5.908L3 3l18 18"/></svg>
                                    </button>
                                </div>
                            </div>

                            <div class="mb-4">
                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Confirm Password <span class="text-rose-500">*</span></label>
                                <div class="relative">
                                    <input :type="showCreatePasswordConfirm ? 'text' : 'password'" name="password_confirmation" class="w-full border-slate-300 dark:border-slate-700 dark:bg-slate-800 text-slate-900 dark:text-white rounded-xl shadow-sm text-sm focus:ring-emerald-500 pr-10" required minlength="8" placeholder="Confirm password">
                                    <button type="button" @click="showCreatePasswordConfirm = !showCreatePasswordConfirm" class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600 dark:hover:text-slate-200">
                                        <svg x-show="!showCreatePasswordConfirm" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                        <svg x-show="showCreatePasswordConfirm" style="display: none;" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858-5.908a10.03 10.03 0 013.982-.963c4.478 0 8.268 2.943 9.543 7a9.97 9.97 0 01-1.563 3.029m-5.858 5.908L3 3l18 18"/></svg>
                                    </button>
                                </div>
                            </div>

                        </div>
                        <div class="bg-slate-50 dark:bg-slate-800/50 border-t border-slate-100 dark:border-slate-800 px-6 py-4 flex flex-row-reverse gap-3">
                            <button type="submit" class="w-full sm:w-auto inline-flex justify-center rounded-xl border border-transparent px-5 py-2.5 bg-emerald-600 text-white hover:bg-emerald-700 text-sm font-semibold transition shadow-sm">
                                Create Super Admin
                            </button>
                            <button type="button" @click="createModalOpen = false" class="w-full sm:w-auto inline-flex justify-center rounded-xl border border-slate-300 dark:border-slate-700 px-5 py-2.5 bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700 text-sm font-medium transition">
                                Cancel
                            </button>
                        </div>
                    </form>

                </div>
            </div>
        </div>

        <!-- Edit Admin Name & Email Modal -->
        <div x-show="editModalOpen" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div x-show="editModalOpen" class="fixed inset-0 bg-slate-900/75 dark:bg-slate-900/80 transition-opacity" @click="editModalOpen = false"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
                <div x-show="editModalOpen" class="inline-block align-bottom bg-white dark:bg-slate-900 rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full border border-slate-200 dark:border-slate-800">
                    <form :action="`/admin/admins/${editId}`" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="bg-white dark:bg-slate-900 p-6">
                            <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-4">Edit Super Admin Account</h3>
                            
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Full Name <span class="text-rose-500">*</span></label>
                                <input type="text" name="name" x-model="editName" class="w-full border-slate-300 dark:border-slate-700 dark:bg-slate-800 text-slate-900 dark:text-white rounded-xl shadow-sm text-sm focus:ring-emerald-500" required>
                            </div>

                            <div class="mb-4">
                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Email Address <span class="text-rose-500">*</span></label>
                                <input type="email" name="email" x-model="editEmail" class="w-full border-slate-300 dark:border-slate-700 dark:bg-slate-800 text-slate-900 dark:text-white rounded-xl shadow-sm text-sm focus:ring-emerald-500" required>
                            </div>
                        </div>
                        <div class="bg-slate-50 dark:bg-slate-800/50 border-t border-slate-100 dark:border-slate-800 px-6 py-4 flex flex-row-reverse gap-3">
                            <button type="submit" class="w-full sm:w-auto inline-flex justify-center rounded-xl border border-transparent px-5 py-2.5 bg-emerald-600 text-white hover:bg-emerald-700 text-sm font-semibold transition shadow-sm">
                                Save Details
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
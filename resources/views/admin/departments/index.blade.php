<x-app-layout>
    <x-admin-top-navbar />

    <div class="py-4 sm:py-8" x-data="{ 
        createModalOpen: false, 
        editModalOpen: false, 
        editId: '', 
        editName: '', 
        editCode: '', 
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
            <x-admin-header title="Department Management" description="Create, edit, and toggle active status for departments across the institute." />

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
                    <input type="text" x-model="searchQuery" placeholder="Search department by name or code" class="w-full pl-10 pr-4 py-2 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-lg text-sm focus:ring-blue-500 focus:border-blue-500 dark:text-white">
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 shadow-sm rounded-xl border border-gray-100 dark:border-gray-700 overflow-hidden">
                
                <div class="p-4 sm:p-6 border-b border-gray-200 dark:border-gray-700 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-gray-50 dark:bg-gray-800/50">
                    <div>
                        <h3 class="text-base sm:text-lg font-bold text-gray-900 dark:text-white">Academic Departments Directory</h3>
                        <span class="text-xs sm:text-sm text-gray-500 dark:text-gray-400">Total: {{ $departments->count() }}</span>
                    </div>
                    
                    <button @click="createModalOpen = true" class="w-full sm:w-auto bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg shadow-sm transition transform hover:-translate-y-0.5 flex items-center justify-center text-xs sm:text-sm">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                        New Department
                    </button>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-50 dark:bg-gray-900/50 text-gray-500 dark:text-gray-400 text-xs uppercase tracking-wider border-b border-gray-200 dark:border-gray-700">
                                <th @click="sortTable('name')" class="p-4 font-medium text-center min-w-[200px] max-w-[300px] cursor-pointer select-none group hover:bg-gray-100 dark:hover:bg-gray-700/80 transition">
                                    <div class="inline-flex items-center justify-center space-x-1">
                                        <span>Department Name</span>
                                        <span class="inline-flex flex-col text-[9px] leading-none text-gray-400 group-hover:text-gray-600 dark:group-hover:text-gray-300">
                                            <svg class="w-2.5 h-2.5" :class="{ 'text-blue-600 dark:text-blue-400 font-bold scale-125': sortCol === 'name' && sortDir === 'asc' }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 15l7-7 7 7"/></svg>
                                            <svg class="w-2.5 h-2.5 -mt-0.5" :class="{ 'text-blue-600 dark:text-blue-400 font-bold scale-125': sortCol === 'name' && sortDir === 'desc' }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M19 9l-7 7-7-7"/></svg>
                                        </span>
                                    </div>
                                </th>
                                <th @click="sortTable('code')" class="p-4 font-medium text-center min-w-[100px] max-w-[150px] cursor-pointer select-none group hover:bg-gray-100 dark:hover:bg-gray-700/80 transition">
                                    <div class="inline-flex items-center justify-center space-x-1">
                                        <span>Code</span>
                                        <span class="inline-flex flex-col text-[9px] leading-none text-gray-400 group-hover:text-gray-600 dark:group-hover:text-gray-300">
                                            <svg class="w-2.5 h-2.5" :class="{ 'text-blue-600 dark:text-blue-400 font-bold scale-125': sortCol === 'code' && sortDir === 'asc' }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 15l7-7 7 7"/></svg>
                                            <svg class="w-2.5 h-2.5 -mt-0.5" :class="{ 'text-blue-600 dark:text-blue-400 font-bold scale-125': sortCol === 'code' && sortDir === 'desc' }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M19 9l-7 7-7-7"/></svg>
                                        </span>
                                    </div>
                                </th>
                                <th @click="sortTable('status')" class="p-4 font-medium text-center min-w-[100px] cursor-pointer select-none group hover:bg-gray-100 dark:hover:bg-gray-700/80 transition">
                                    <div class="inline-flex items-center justify-center space-x-1">
                                        <span>Status</span>
                                        <span class="inline-flex flex-col text-[9px] leading-none text-gray-400 group-hover:text-gray-600 dark:group-hover:text-gray-300">
                                            <svg class="w-2.5 h-2.5" :class="{ 'text-blue-600 dark:text-blue-400 font-bold scale-125': sortCol === 'status' && sortDir === 'asc' }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 15l7-7 7 7"/></svg>
                                            <svg class="w-2.5 h-2.5 -mt-0.5" :class="{ 'text-blue-600 dark:text-blue-400 font-bold scale-125': sortCol === 'status' && sortDir === 'desc' }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M19 9l-7 7-7-7"/></svg>
                                        </span>
                                    </div>
                                </th>
                                <th class="p-4 font-medium text-center min-w-[180px]">Actions</th>
                            </tr>
                        </thead>
                        <tbody x-ref="tableBody" class="divide-y divide-gray-200 dark:divide-gray-700 text-sm">
                            @forelse($departments as $dept)
                                @php
                                    $targetSearch = strtolower($dept->name . ' ' . $dept->code);
                                @endphp
                                <tr data-sortable="true" data-name="{{ addslashes($dept->name) }}" data-code="{{ addslashes($dept->code) }}" data-status="{{ $dept->is_active ? 'active' : 'inactive' }}" x-show="matchesFilter('{{ addslashes($targetSearch) }}')" class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition">
                                    <td class="p-4 font-semibold text-gray-900 dark:text-gray-100 text-center min-w-[200px] max-w-[300px] whitespace-normal break-words">
                                        {{ $dept->name }}
                                        @if(!$dept->is_active)
                                            <span class="ml-2 text-xs text-red-500 italic font-normal">(Inactive)</span>
                                        @endif
                                    </td>
                                    <td class="p-4 text-blue-600 dark:text-blue-400 font-bold text-center min-w-[100px] max-w-[150px] whitespace-normal break-words">{{ $dept->code }}</td>
                                    
                                    <td class="p-4 text-center min-w-[100px]">
                                        @if($dept->is_active)
                                            <span class="bg-green-100 text-green-800 text-xs font-medium px-2.5 py-0.5 rounded-full dark:bg-green-900 dark:text-green-300">Active</span>
                                        @else
                                            <span class="bg-red-100 text-red-800 text-xs font-medium px-2.5 py-0.5 rounded-full dark:bg-red-900 dark:text-red-300">Inactive</span>
                                        @endif
                                    </td>

                                    <td class="p-4 text-center min-w-[180px]">
                                        <div class="flex items-center justify-center space-x-3">
                                            <button @click="editModalOpen = true; editId = '{{ $dept->id }}'; editName = '{{ addslashes($dept->name) }}'; editCode = '{{ $dept->code }}'" class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300 font-medium px-3 py-1 rounded bg-indigo-50 hover:bg-indigo-100 dark:bg-indigo-900/30 dark:hover:bg-indigo-900/50 transition">
                                                Edit
                                            </button>

                                            @if($dept->is_active)
                                                <button type="button" @click="openConfirm('Deactivate Department', 'Are you sure you want to deactivate {{ addslashes($dept->name) }}? Users in this department may lose access.', '{{ route('admin.departments.toggle', $dept->id) }}', 'Deactivate Department', 'bg-rose-600 hover:bg-rose-700 text-white')" class="text-rose-600 hover:text-rose-900 dark:text-rose-400 dark:hover:text-rose-300 font-medium px-3 py-1 rounded bg-rose-50 hover:bg-rose-100 dark:bg-rose-900/30 dark:hover:bg-rose-900/50 transition w-24 text-center">
                                                    Deactivate
                                                </button>
                                            @else
                                                <button type="button" @click="openConfirm('Reactivate Department', 'Are you sure you want to reactivate {{ addslashes($dept->name) }}?', '{{ route('admin.departments.toggle', $dept->id) }}', 'Reactivate Department', 'bg-emerald-600 hover:bg-emerald-700 text-white')" class="text-green-600 hover:text-green-900 dark:text-green-400 dark:hover:text-green-300 font-medium px-3 py-1 rounded bg-green-50 hover:bg-green-100 dark:bg-green-900/30 dark:hover:bg-green-900/50 transition w-24 text-center">
                                                    Activate
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="p-8 text-center text-gray-500 dark:text-gray-400">
                                        No departments found. Click "New Department" to get started!
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div x-show="createModalOpen" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div x-show="createModalOpen" class="fixed inset-0 bg-gray-500 bg-opacity-75 dark:bg-gray-900 dark:bg-opacity-80 transition-opacity" @click="createModalOpen = false"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
                <div x-show="createModalOpen" class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full border border-gray-100 dark:border-gray-700">
                    
                    <form action="{{ route('admin.departments.store') }}" method="POST">
                        @csrf
                        <div class="bg-white dark:bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Create New Department</h3>
                            
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Department Name <span class="text-rose-500">*</span></label>
                                <input type="text" name="name" placeholder="e.g., Computer Science" class="w-full border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-300 rounded-lg shadow-sm focus:ring-blue-500" required>
                            </div>
                            
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Department Code <span class="text-rose-500">*</span></label>
                                <input type="text" name="code" placeholder="e.g., CSE" class="w-full border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-300 rounded-lg shadow-sm focus:ring-blue-500" required>
                            </div>

                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Initial Status <span class="text-rose-500">*</span></label>
                                <select name="is_active" class="w-full border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-300 rounded-lg shadow-sm focus:ring-blue-500" required>
                                    <option value="1" selected>Active</option>
                                    <option value="0">Inactive</option>
                                </select>
                            </div>
                        </div>
                        <div class="bg-gray-50 dark:bg-gray-700/50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                            <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 sm:ml-3 sm:w-auto sm:text-sm transition">
                                Create Department
                            </button>
                            <button type="button" @click="createModalOpen = false" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 dark:border-gray-600 shadow-sm px-4 py-2 bg-white dark:bg-gray-800 text-base font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm transition">
                                Cancel
                            </button>
                        </div>
                    </form>

                </div>
            </div>
        </div>

        <div x-show="editModalOpen" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div x-show="editModalOpen" class="fixed inset-0 bg-gray-500 bg-opacity-75 dark:bg-gray-900 dark:bg-opacity-80 transition-opacity" @click="editModalOpen = false"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
                <div x-show="editModalOpen" class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full border border-gray-100 dark:border-gray-700">
                    <form :action="`/admin/departments/${editId}`" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="bg-white dark:bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Edit Department</h3>
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Department Name <span class="text-rose-500">*</span></label>
                                <input type="text" name="name" x-model="editName" class="w-full border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-300 rounded-lg shadow-sm focus:ring-blue-500" required>
                            </div>
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Department Code <span class="text-rose-500">*</span></label>
                                <input type="text" name="code" x-model="editCode" class="w-full border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-300 rounded-lg shadow-sm focus:ring-blue-500" required>
                            </div>
                        </div>
                        <div class="bg-gray-50 dark:bg-gray-700/50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                            <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 sm:ml-3 sm:w-auto sm:text-sm transition">
                                Save Changes
                            </button>
                            <button type="button" @click="editModalOpen = false" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 dark:border-gray-600 shadow-sm px-4 py-2 bg-white dark:bg-gray-800 text-base font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm transition">
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
<x-app-layout>
    <x-admin-top-navbar />

    <div class="py-4 sm:py-8" x-data="{ 
        createModalOpen: false, 
        editModalOpen: false, 
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
            <x-admin-header title="Global Authorities Management" description="Assign and manage DOAA, ADOAA, Senate Chairperson, Academic Office, AR, and DR roles." />

            @if($errors->any())
                <div class="bg-red-50 text-red-700 p-4 rounded-lg shadow-sm border border-red-200">
                    {{ $errors->first() }}
                </div>
            @endif

            <!-- Search Box -->
            <div class="bg-white dark:bg-gray-800 p-4 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700">
                <div class="relative w-full sm:w-96">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </div>
                    <input type="text" x-model="searchQuery" placeholder="Search global authority by name or email" class="w-full pl-10 pr-4 py-2 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-lg text-sm focus:ring-orange-500 focus:border-orange-500 dark:text-white">
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 shadow-sm rounded-xl border border-gray-100 dark:border-gray-700 overflow-hidden">
                <div class="p-4 sm:p-6 border-b border-gray-200 dark:border-gray-700 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-gray-50 dark:bg-gray-800/50">
                    <div>
                        <h3 class="text-base sm:text-lg font-bold text-gray-900 dark:text-white">Institute Global Authorities</h3>
                        <span class="text-xs sm:text-sm text-gray-500 dark:text-gray-400">Total Assigned: {{ $authorities->count() }}</span>
                    </div>
                    
                    <button @click="createModalOpen = true" class="w-full sm:w-auto bg-orange-600 hover:bg-orange-700 text-white font-bold py-2 px-4 rounded-lg shadow-sm transition transform hover:-translate-y-0.5 flex items-center justify-center text-xs sm:text-sm">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                        Assign Global Role
                    </button>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-50 dark:bg-gray-900/50 text-gray-500 dark:text-gray-400 text-xs uppercase tracking-wider border-b border-gray-200 dark:border-gray-700">
                                <th @click="sortTable('name')" class="p-4 font-medium text-center min-w-[140px] max-w-[200px] cursor-pointer select-none group hover:bg-gray-100 dark:hover:bg-gray-700/80 transition">
                                    <div class="inline-flex items-center justify-center space-x-1">
                                        <span>Authority Name</span>
                                        <span class="inline-flex flex-col text-[9px] leading-none text-gray-400 group-hover:text-gray-600 dark:group-hover:text-gray-300">
                                            <svg class="w-2.5 h-2.5" :class="{ 'text-orange-600 dark:text-orange-400 font-bold scale-125': sortCol === 'name' && sortDir === 'asc' }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 15l7-7 7 7"/></svg>
                                            <svg class="w-2.5 h-2.5 -mt-0.5" :class="{ 'text-orange-600 dark:text-orange-400 font-bold scale-125': sortCol === 'name' && sortDir === 'desc' }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M19 9l-7 7-7-7"/></svg>
                                        </span>
                                    </div>
                                </th>
                                <th @click="sortTable('email')" class="p-4 font-medium text-center min-w-[180px] max-w-[240px] cursor-pointer select-none group hover:bg-gray-100 dark:hover:bg-gray-700/80 transition">
                                    <div class="inline-flex items-center justify-center space-x-1">
                                        <span>Authority Email</span>
                                        <span class="inline-flex flex-col text-[9px] leading-none text-gray-400 group-hover:text-gray-600 dark:group-hover:text-gray-300">
                                            <svg class="w-2.5 h-2.5" :class="{ 'text-orange-600 dark:text-orange-400 font-bold scale-125': sortCol === 'email' && sortDir === 'asc' }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 15l7-7 7 7"/></svg>
                                            <svg class="w-2.5 h-2.5 -mt-0.5" :class="{ 'text-orange-600 dark:text-orange-400 font-bold scale-125': sortCol === 'email' && sortDir === 'desc' }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M19 9l-7 7-7-7"/></svg>
                                        </span>
                                    </div>
                                </th>
                                <th @click="sortTable('role')" class="p-4 font-medium text-center min-w-[180px] max-w-[250px] cursor-pointer select-none group hover:bg-gray-100 dark:hover:bg-gray-700/80 transition">
                                    <div class="inline-flex items-center justify-center space-x-1">
                                        <span>Global Role Position</span>
                                        <span class="inline-flex flex-col text-[9px] leading-none text-gray-400 group-hover:text-gray-600 dark:group-hover:text-gray-300">
                                            <svg class="w-2.5 h-2.5" :class="{ 'text-orange-600 dark:text-orange-400 font-bold scale-125': sortCol === 'role' && sortDir === 'asc' }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 15l7-7 7 7"/></svg>
                                            <svg class="w-2.5 h-2.5 -mt-0.5" :class="{ 'text-orange-600 dark:text-orange-400 font-bold scale-125': sortCol === 'role' && sortDir === 'desc' }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M19 9l-7 7-7-7"/></svg>
                                        </span>
                                    </div>
                                </th>
                                <th @click="sortTable('status')" class="p-4 font-medium text-center min-w-[90px] cursor-pointer select-none group hover:bg-gray-100 dark:hover:bg-gray-700/80 transition">
                                    <div class="inline-flex items-center justify-center space-x-1">
                                        <span>Status</span>
                                        <span class="inline-flex flex-col text-[9px] leading-none text-gray-400 group-hover:text-gray-600 dark:group-hover:text-gray-300">
                                            <svg class="w-2.5 h-2.5" :class="{ 'text-orange-600 dark:text-orange-400 font-bold scale-125': sortCol === 'status' && sortDir === 'asc' }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 15l7-7 7 7"/></svg>
                                            <svg class="w-2.5 h-2.5 -mt-0.5" :class="{ 'text-orange-600 dark:text-orange-400 font-bold scale-125': sortCol === 'status' && sortDir === 'desc' }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M19 9l-7 7-7-7"/></svg>
                                        </span>
                                    </div>
                                </th>
                                <th class="p-4 font-medium text-center min-w-[180px]">Actions</th>
                            </tr>
                        </thead>
                        <tbody x-ref="tableBody" class="divide-y divide-gray-200 dark:divide-gray-700 text-sm">
                            @forelse($authorities as $user)
                                @php
                                    $targetSearch = strtolower($user->name . ' ' . $user->email . ' ' . $user->role);
                                @endphp
                                <tr data-sortable="true" data-name="{{ addslashes($user->name) }}" data-email="{{ addslashes($user->email) }}" data-role="{{ $user->role }}" data-status="{{ $user->is_active ? 'active' : 'inactive' }}" x-show="matchesFilter('{{ addslashes($targetSearch) }}')" class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition">
                                    <td class="p-4 font-semibold text-gray-900 dark:text-gray-100 text-center min-w-[140px] max-w-[200px] whitespace-normal break-words">
                                        {{ $user->name }}
                                    </td>

                                    <td class="p-4 text-gray-600 dark:text-gray-300 font-medium text-center min-w-[180px] max-w-[240px] whitespace-normal break-words">
                                        {{ $user->email }}
                                    </td>
                                    
                                    <td class="p-4 text-center min-w-[180px] max-w-[250px] whitespace-normal break-words">
                                        <span class="bg-orange-100 text-orange-800 text-xs font-bold px-2.5 py-1 rounded dark:bg-orange-900/40 dark:text-orange-300 uppercase">
                                            @if($user->isDoaa()) Dean of Academic Affairs (DOAA) @endif
                                            @if($user->isAdoaa()) Assoc. Dean of Academic Affairs (ADoAA) @endif
                                            @if($user->isSenateChairperson()) Senate Chairperson @endif
                                            @if($user->isAcademicOffice()) Academic Office @endif
                                            @if($user->role === 'ar') Assistant Registrar (AR) @endif
                                            @if($user->role === 'dr') Deputy Registrar (DR) @endif
                                        </span>
                                    </td>
                                    
                                    <td class="p-4 text-center min-w-[90px]">
                                        @if($user->is_active)
                                            <span class="bg-green-100 text-green-800 text-xs font-medium px-2.5 py-0.5 rounded-full dark:bg-green-900 dark:text-green-300">Active</span>
                                        @else
                                            <span class="bg-red-100 text-red-800 text-xs font-medium px-2.5 py-0.5 rounded-full dark:bg-red-900 dark:text-red-300">Inactive</span>
                                        @endif
                                    </td>

                                    <td class="p-4 text-center min-w-[180px]">
                                        <div class="flex items-center justify-center space-x-3">
                                            <button @click="editModalOpen = true; editId = '{{ $user->id }}'; editName = '{{ addslashes($user->name) }}'; editEmail = '{{ addslashes($user->email) }}'" class="text-indigo-600 hover:text-indigo-900 font-medium px-3 py-1 rounded bg-indigo-50 hover:bg-indigo-100 dark:bg-indigo-900/30 dark:text-indigo-300 transition">
                                                Edit
                                            </button>

                                            @if($user->is_active)
                                                <button type="button" @click="openConfirm('Deactivate Global Authority', 'Are you sure you want to deactivate {{ addslashes($user->name) }} ({{ strtoupper(str_replace('_', ' ', $user->role)) }})?', '{{ route('admin.users.toggle', $user->id) }}', 'Deactivate Authority', 'bg-rose-600 hover:bg-rose-700 text-white')" class="text-rose-600 hover:text-rose-900 dark:text-rose-400 dark:hover:text-rose-300 font-medium px-3 py-1 rounded bg-rose-50 hover:bg-rose-100 dark:bg-rose-900/30 dark:hover:bg-rose-900/50 transition w-24 text-center">
                                                    Deactivate
                                                </button>
                                            @else
                                                <button type="button" @click="openConfirm('Reactivate Global Authority', 'Are you sure you want to reactivate {{ addslashes($user->name) }} ({{ strtoupper(str_replace('_', ' ', $user->role)) }})?', '{{ route('admin.users.toggle', $user->id) }}', 'Reactivate Authority', 'bg-emerald-600 hover:bg-emerald-700 text-white')" class="text-green-600 hover:text-green-900 dark:text-green-400 dark:hover:text-green-300 font-medium px-3 py-1 rounded bg-green-50 hover:bg-green-100 dark:bg-green-900/30 transition w-24 text-center">
                                                    Activate
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="p-8 text-center text-gray-500 dark:text-gray-400">
                                        No Global Authorities found. Click "Assign Global Role" to add one!
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Assign Global Authority Modal -->
        <div x-show="createModalOpen" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div x-show="createModalOpen" class="fixed inset-0 bg-slate-900/75 dark:bg-slate-900/80 transition-opacity" @click="createModalOpen = false"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
                <div x-show="createModalOpen" class="inline-block align-bottom bg-white dark:bg-slate-900 rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full border border-slate-200 dark:border-slate-800">
                    
                    <form action="{{ route('admin.global_authorities.store') }}" method="POST">
                        @csrf
                        <div class="bg-white dark:bg-slate-900 p-6">
                            <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-4">Assign Global Authority</h3>
                            
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Full Name <span class="text-rose-500">*</span></label>
                                <input type="text" name="name" class="w-full border-slate-300 dark:border-slate-700 dark:bg-slate-800 text-slate-900 dark:text-white rounded-xl shadow-sm text-sm focus:ring-orange-500" required>
                            </div>
                            
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Email Address <span class="text-rose-500">*</span></label>
                                <input type="email" name="email" class="w-full border-slate-300 dark:border-slate-700 dark:bg-slate-800 text-slate-900 dark:text-white rounded-xl shadow-sm text-sm focus:ring-orange-500" required>
                            </div>

                            <div class="mb-4">
                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Global Role <span class="text-rose-500">*</span></label>
                                <select name="role" class="w-full border-slate-300 dark:border-slate-700 dark:bg-slate-800 text-slate-900 dark:text-white rounded-xl shadow-sm text-sm focus:ring-orange-500" required>
                                    <option value="" disabled selected>Select an institute-level role</option>
                                    <option value="doaa">Dean of Academic Affairs (DoAA)</option>
                                    <option value="adoaa">Assoc. Dean of Academic Affairs (ADoAA)</option>
                                    <option value="academic_office">Academic Office</option>
                                    <option value="ar">Assistant Registrar (Academic)</option>
                                    <option value="dr">Deputy Registrar (Academic)</option>
                                    <option value="senate_chairperson">Senate Chairperson</option>
                                </select>
                            </div>

                        </div>
                        <div class="bg-slate-50 dark:bg-slate-800/50 border-t border-slate-100 dark:border-slate-800 px-6 py-4 flex flex-row-reverse gap-3">
                            <button type="submit" class="w-full sm:w-auto inline-flex justify-center rounded-xl border border-transparent px-5 py-2.5 bg-orange-600 text-white hover:bg-orange-700 text-sm font-semibold transition shadow-sm">
                                Assign Authority
                            </button>
                            <button type="button" @click="createModalOpen = false" class="w-full sm:w-auto inline-flex justify-center rounded-xl border border-slate-300 dark:border-slate-700 px-5 py-2.5 bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700 text-sm font-medium transition">
                                Cancel
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Edit Authority Name Modal (Name Only) -->
        <div x-show="editModalOpen" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div x-show="editModalOpen" class="fixed inset-0 bg-slate-900/75 dark:bg-slate-900/80 transition-opacity" @click="editModalOpen = false"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
                <div x-show="editModalOpen" class="inline-block align-bottom bg-white dark:bg-slate-900 rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full border border-slate-200 dark:border-slate-800">
                    <form :action="`/admin/users/${editId}/name`" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="bg-white dark:bg-slate-900 p-6">
                            <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-4">Edit Authority Name</h3>
                            
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Email (Read Only)</label>
                                <input type="email" :value="editEmail" class="w-full bg-slate-100 dark:bg-slate-800 border-slate-200 dark:border-slate-700 text-slate-500 rounded-xl shadow-sm text-sm" disabled readonly>
                            </div>

                            <div class="mb-4">
                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Full Name <span class="text-rose-500">*</span></label>
                                <input type="text" name="name" x-model="editName" class="w-full border-slate-300 dark:border-slate-700 dark:bg-slate-800 text-slate-900 dark:text-white rounded-xl shadow-sm text-sm focus:ring-orange-500" required>
                            </div>
                        </div>
                        <div class="bg-slate-50 dark:bg-slate-800/50 border-t border-slate-100 dark:border-slate-800 px-6 py-4 flex flex-row-reverse gap-3">
                            <button type="submit" class="w-full sm:w-auto inline-flex justify-center rounded-xl border border-transparent px-5 py-2.5 bg-orange-600 text-white hover:bg-orange-700 text-sm font-semibold transition shadow-sm">
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
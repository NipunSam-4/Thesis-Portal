<x-app-layout>
    <div class="py-4 sm:py-8" x-data="{ 
        activeTab: 'pool', 
        createModalOpen: false,
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
            
            <!-- Navigation Back Button & Page Title Component -->
            <x-admin-header title="DOAA Pool & Delegation Management" description="Manage DOAA pool members, configure candidate visibility for Academic Office verification, and assign or remove Vested DOAA authority." />

            @if($errors->any())
                <div class="mb-4 sm:mb-6 bg-rose-50 dark:bg-rose-900/30 border border-rose-200 dark:border-rose-800 text-rose-800 dark:text-rose-300 px-4 py-3 rounded-xl text-sm shadow-sm">
                    {{ $errors->first() }}
                </div>
            @endif

            <!-- Search Box -->
            <div class="bg-white dark:bg-slate-900 p-4 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-800">
                <div class="relative w-full sm:w-96">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </div>
                    <input type="text" x-model="searchQuery" placeholder="Search pool member by name or email" class="w-full pl-10 pr-4 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm focus:ring-indigo-500 focus:border-indigo-500 dark:text-white">
                </div>
            </div>

            <!-- Tab Navigation Bar -->
            <div class="flex overflow-x-auto whitespace-nowrap scrollbar-none border-b border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 px-4 sm:px-6 pt-4 space-x-4 sm:space-x-6 rounded-t-2xl">
                <button @click="activeTab = 'pool'" :class="{ 'border-indigo-600 text-indigo-600 font-bold border-b-2': activeTab === 'pool', 'text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-200': activeTab !== 'pool' }" class="pb-3 px-2 text-xs sm:text-sm transition flex items-center flex-shrink-0">
                    1. Manage DOAA Pool
                </button>
                <button @click="activeTab = 'acting'" :class="{ 'border-indigo-600 text-indigo-600 font-bold border-b-2': activeTab === 'acting', 'text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-200': activeTab !== 'acting' }" class="pb-3 px-2 text-xs sm:text-sm transition flex items-center flex-shrink-0">
                    2. Acting DOAA Options
                </button>
                <button @click="activeTab = 'vested'" :class="{ 'border-indigo-600 text-indigo-600 font-bold border-b-2': activeTab === 'vested', 'text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-200': activeTab !== 'vested' }" class="pb-3 px-2 text-xs sm:text-sm transition flex items-center flex-shrink-0">
                    3. Vested DOAA Assignment
                </button>
            </div>

            <!-- TAB 1: DOAA POOL MEMBERSHIP -->
            <div x-show="activeTab === 'pool'" class="bg-white dark:bg-slate-900 rounded-b-2xl shadow-sm border border-slate-200 dark:border-slate-800 overflow-hidden space-y-6">
                
                <form action="{{ route('admin.doaa_delegation.pool.save') }}" method="POST">
                    @csrf
                    <div class="p-4 sm:p-5 border-b border-slate-100 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-800/50 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                        <div>
                            <h3 class="font-bold text-slate-800 dark:text-slate-200 text-base sm:text-lg">DOAA Pool Members</h3>
                            <p class="text-xs text-slate-500">Check/uncheck members to enable or disable participation in Acting DOAA options for Verifying officer.</p>
                        </div>
                        <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2 sm:gap-3 w-full md:w-auto">
                            <button type="button" @click="createModalOpen = true" class="inline-flex items-center justify-center px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs sm:text-sm font-semibold rounded-xl shadow-sm transition">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                Assign New Member
                            </button>
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="border-b border-slate-200 dark:border-slate-800 text-xs font-semibold text-slate-500 uppercase bg-slate-50 dark:bg-slate-800/40">
                                    <th class="p-4 text-center min-w-[120px]">Pool Member</th>
                                    <th @click="sortTable('name')" class="p-4 text-center min-w-[140px] max-w-[200px] cursor-pointer select-none group hover:bg-slate-100 dark:hover:bg-slate-800/80 transition">
                                        <div class="inline-flex items-center justify-center space-x-1">
                                            <span>Name</span>
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
                                    <th @click="sortTable('poolstatus')" class="p-4 text-center min-w-[140px] cursor-pointer select-none group hover:bg-slate-100 dark:hover:bg-slate-800/80 transition">
                                        <div class="inline-flex items-center justify-center space-x-1">
                                            <span>Pool Status</span>
                                            <span class="inline-flex flex-col text-[9px] leading-none text-slate-400 group-hover:text-slate-600 dark:group-hover:text-slate-300">
                                                <svg class="w-2.5 h-2.5" :class="{ 'text-indigo-600 dark:text-indigo-400 font-bold scale-125': sortCol === 'poolstatus' && sortDir === 'asc' }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 15l7-7 7 7"/></svg>
                                                <svg class="w-2.5 h-2.5 -mt-0.5" :class="{ 'text-indigo-600 dark:text-indigo-400 font-bold scale-125': sortCol === 'poolstatus' && sortDir === 'desc' }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M19 9l-7 7-7-7"/></svg>
                                            </span>
                                        </div>
                                    </th>
                                    <th @click="sortTable('actingstatus')" class="p-4 text-center min-w-[160px] cursor-pointer select-none group hover:bg-slate-100 dark:hover:bg-slate-800/80 transition">
                                        <div class="inline-flex items-center justify-center space-x-1">
                                            <span>Acting DOAA Status</span>
                                            <span class="inline-flex flex-col text-[9px] leading-none text-slate-400 group-hover:text-slate-600 dark:group-hover:text-slate-300">
                                                <svg class="w-2.5 h-2.5" :class="{ 'text-indigo-600 dark:text-indigo-400 font-bold scale-125': sortCol === 'actingstatus' && sortDir === 'asc' }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 15l7-7 7 7"/></svg>
                                                <svg class="w-2.5 h-2.5 -mt-0.5" :class="{ 'text-indigo-600 dark:text-indigo-400 font-bold scale-125': sortCol === 'actingstatus' && sortDir === 'desc' }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M19 9l-7 7-7-7"/></svg>
                                            </span>
                                        </div>
                                    </th>
                                </tr>
                            </thead>
                            <tbody x-ref="tableBody" class="divide-y divide-slate-100 dark:divide-slate-800 text-sm">
                                @forelse($doaaPoolUsers as $user)
                                    @php
                                        $targetSearch = strtolower($user->name . ' ' . $user->email);
                                        $isActing = in_array($user->id, $activeActingUserIds);
                                    @endphp
                                    <tr data-sortable="true" data-name="{{ addslashes($user->name) }}" data-email="{{ addslashes($user->email) }}" data-role="{{ $user->role }}" data-poolstatus="{{ $user->is_active ? 'active' : 'inactive' }}" data-actingstatus="{{ $isActing ? 'visible' : 'hidden' }}" x-show="matchesFilter('{{ addslashes($targetSearch) }}')" class="hover:bg-slate-50/50 dark:hover:bg-slate-800/30 transition">
                                        <td class="p-4 text-center min-w-[120px]">
                                            <input type="checkbox" name="active_user_ids[]" value="{{ $user->id }}" {{ $user->is_active ? 'checked' : '' }} class="h-5 w-5 rounded border-slate-300 text-indigo-600 shadow-sm focus:ring-indigo-500 cursor-pointer">
                                        </td>
                                        <td class="p-4 font-semibold text-slate-900 dark:text-white text-center min-w-[140px] max-w-[200px] whitespace-normal break-words">
                                            {{ $user->name }}
                                        </td>
                                        <td class="p-4 text-slate-600 dark:text-slate-300 text-center min-w-[180px] max-w-[240px] whitespace-normal break-words">
                                            {{ $user->email }}
                                        </td>
                                        <td class="p-4 text-center min-w-[140px]">
                                            @if($user->is_active)
                                                <span class="bg-emerald-100 dark:bg-emerald-900/40 text-emerald-800 dark:text-emerald-300 text-xs font-medium px-2.5 py-1 rounded-full">Active</span>
                                            @else
                                                <span class="bg-slate-100 dark:bg-slate-800 text-slate-500 text-xs font-medium px-2.5 py-1 rounded-full">Inactive</span>
                                            @endif
                                        </td>
                                        <td class="p-4 text-center min-w-[160px]">
                                            @if($isActing)
                                                <span class="bg-indigo-100 dark:bg-indigo-900/40 text-indigo-800 dark:text-indigo-300 text-xs font-bold px-2.5 py-1 rounded-full border border-indigo-200 dark:border-indigo-800">
                                                    Visible
                                                </span>
                                            @else
                                                <span class="bg-slate-100 dark:bg-slate-800 text-slate-500 text-xs font-medium px-2.5 py-1 rounded-full">
                                                    Hidden
                                                </span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="p-8 text-center text-slate-400">
                                            No members in the DOAA pool. Click "Assign New Member" to add one!
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="p-4 border-t border-slate-100 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-800/50 flex justify-end">
                        <button type="submit" class="inline-flex items-center px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-xl shadow-sm transition">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            Save DOAA Pool
                        </button>
                    </div>
                </form>
            </div>

            <!-- SECTION 2: MODIFY ACTING DOAAS SHOWN TO VERIFYING OFFICER (ACADEMIC OFFICE) -->
            <div x-show="activeTab === 'acting'" style="display: none;" class="bg-white dark:bg-slate-900 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-800 overflow-hidden">
                <form action="{{ route('admin.doaa_delegation.visibility.save') }}" method="POST">
                    @csrf
                    <div class="p-6 border-b border-slate-100 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-800/50 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                        <div>
                            <h3 class="font-bold text-slate-800 dark:text-slate-200 text-lg">Modify Acting DOAAs Shown to Verifying Officer (Academic Office)</h3>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Select candidates from the pool who will appear in the dropdown presented to Academic Office during form endorsement, then click Save.</p>
                        </div>
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-xl shadow-sm transition">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            Save Acting DOAA Dropdown Selections
                        </button>
                    </div>

                    <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-4">
                        @forelse($doaaPoolUsers->where('is_active', true) as $user)
                            @php
                                $isVisible = in_array($user->id, $activeActingUserIds);
                                $targetSearch = strtolower($user->name . ' ' . $user->email);
                            @endphp
                            <label x-show="matchesFilter('{{ addslashes($targetSearch) }}')" class="p-4 border rounded-xl flex items-center justify-between transition cursor-pointer {{ $isVisible ? 'border-indigo-300 dark:border-indigo-800 bg-indigo-50/40 dark:bg-indigo-900/20' : 'border-slate-200 dark:border-slate-800 bg-slate-50/30 dark:bg-slate-800/30' }}">
                                <div class="flex items-center space-x-3">
                                    <input type="checkbox" name="acting_user_ids[]" value="{{ $user->id }}" {{ $isVisible ? 'checked' : '' }} class="h-5 w-5 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500 cursor-pointer">
                                    <div>
                                        <div class="font-bold text-slate-900 dark:text-white">{{ $user->name }}</div>
                                        <div class="text-xs text-slate-500 dark:text-slate-400">{{ $user->email }} ({{ str_replace('_', ' ', $user->role) }})</div>
                                    </div>
                                </div>
                                <div>
                                    @if($isVisible)
                                        <span class="bg-emerald-100 text-emerald-800 dark:bg-emerald-900/40 dark:text-emerald-300 text-xs font-bold px-2.5 py-1 rounded-full">Visible</span>
                                    @else
                                        <span class="bg-slate-100 text-slate-500 dark:bg-slate-800 dark:text-slate-400 text-xs font-medium px-2.5 py-1 rounded-full">Hidden</span>
                                    @endif
                                </div>
                            </label>
                        @empty
                            <div class="col-span-2 p-8 text-center text-slate-400">
                                No active candidates in the DOAA pool. Please enable candidates in "Manage DOAA Pool" tab first.
                            </div>
                        @endforelse
                    </div>

                    <div class="p-4 border-t border-slate-100 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-800/50 flex justify-end">
                        <button type="submit" class="inline-flex items-center px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-xl shadow-sm transition">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            Save Acting DOAA Dropdown Selections
                        </button>
                    </div>
                </form>
            </div>

            <!-- SECTION 3: MANAGE VESTED DOAA -->
            <div x-show="activeTab === 'vested'" style="display: none;" class="bg-white dark:bg-slate-900 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-800 overflow-hidden">
                <div class="p-6 border-b border-slate-100 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-800/50">
                    <h3 class="font-bold text-slate-800 dark:text-slate-200 text-lg">Manage Vested DOAA</h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Designate one active Vested DOAA authority or remove Vested DOAA (which resets vested DOAA email in all in-progress forms to null).</p>
                </div>

                <div class="p-6 space-y-6">
                    <!-- Current Status Card -->
                    <div class="p-5 rounded-xl border {{ $vestedUser ? 'border-amber-300 bg-amber-50/50 dark:border-amber-800 dark:bg-amber-900/20' : 'border-slate-200 bg-slate-50 dark:border-slate-800 dark:bg-slate-800/40' }} flex justify-between items-center">
                        <div>
                            <span class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Current Vested DOAA Status</span>
                            @if($vestedUser)
                                <h4 class="text-xl font-bold text-slate-900 dark:text-white mt-1">{{ $vestedUser->name }}</h4>
                                <p class="text-xs text-slate-600 dark:text-slate-300 font-medium">{{ $vestedUser->email }}</p>
                            @else
                                <h4 class="text-xl font-bold text-slate-500 dark:text-slate-400 mt-1">No Active Vested DOAA (Set to Null)</h4>
                                <p class="text-xs text-slate-400">All in-progress forms currently have vested DOAA email set to null.</p>
                            @endif
                        </div>

                        @if($vestedUser)
                            <button type="button" @click="openConfirm('Remove Vested DOAA', 'Are you sure you want to remove Vested DOAA? This will set vested DOAA email in all in-progress forms to NULL.', '{{ route('admin.doaa_delegation.vested.update') }}', 'Remove Vested DOAA', 'bg-rose-600 hover:bg-rose-700 text-white')" class="px-4 py-2 bg-rose-600 hover:bg-rose-700 text-white font-semibold text-xs rounded-xl shadow-sm transition">
                                Remove Vested DOAA (Set to Null)
                            </button>
                        @endif
                    </div>

                    <!-- Select New Vested DOAA Form -->
                    <form action="{{ route('admin.doaa_delegation.vested.update') }}" method="POST" class="bg-slate-50 dark:bg-slate-800/50 p-6 rounded-xl border border-slate-200 dark:border-slate-700">
                        @csrf
                        <h4 class="font-bold text-slate-900 dark:text-white mb-2">Select / Update Vested DOAA</h4>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mb-4">Choose a candidate from the DOAA Pool to vest authority, or select "None" to set all in-progress forms vested DOAA to null.</p>

                        <div class="mb-4">
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Vested Candidate</label>
                            <select name="vested_user_id" class="w-full border-slate-300 dark:border-slate-700 dark:bg-slate-900 dark:text-white rounded-xl shadow-sm" required>
                                <option value="none" {{ !$vestedUserId ? 'selected' : '' }}>-- None (Remove Vested DOAA & Set In-Progress Forms to Null) --</option>
                                @foreach($doaaPoolUsers->where('is_active', true) as $user)
                                    <option value="{{ $user->id }}" {{ $user->id === $vestedUserId ? 'selected' : '' }}>
                                        {{ $user->name }} ({{ $user->email }}) [{{ str_replace('_', ' ', $user->role) }}]
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="flex justify-end">
                            <button type="submit" class="px-5 py-2.5 bg-amber-600 hover:bg-amber-700 text-white font-semibold text-sm rounded-xl shadow-sm transition">
                                Save Vested DOAA Selection
                            </button>
                        </div>
                    </form>
                </div>
            </div>

        </div>

        <!-- Assign New Member Modal -->
        <div x-show="createModalOpen" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div x-show="createModalOpen" class="fixed inset-0 bg-slate-900 bg-opacity-75 transition-opacity" @click="createModalOpen = false"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
                <div x-show="createModalOpen" class="inline-block align-bottom bg-white dark:bg-slate-900 rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full border border-slate-200 dark:border-slate-800">
                    
                    <form action="{{ route('admin.doaa_delegation.user.store') }}" method="POST">
                        @csrf
                        <div class="bg-white dark:bg-slate-900 px-6 pt-6 pb-4">
                            <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-1">Assign New Member to DOAA Pool</h3>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mb-5">Create a new authority account with Acting Approval Authority role and add them to the pool.</p>

                            <div class="mb-4">
                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Full Name <span class="text-rose-500">*</span></label>
                                <input type="text" name="name" class="w-full border-slate-300 dark:border-slate-700 dark:bg-slate-800 dark:text-white rounded-xl shadow-sm focus:ring-indigo-500 focus:border-indigo-500" required>
                            </div>

                            <div class="mb-4">
                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Email Address <span class="text-rose-500">*</span></label>
                                <input type="email" name="email" class="w-full border-slate-300 dark:border-slate-700 dark:bg-slate-800 dark:text-white rounded-xl shadow-sm focus:ring-indigo-500 focus:border-indigo-500" required>
                            </div>
                        </div>

                        <div class="bg-slate-50 dark:bg-slate-800/50 border-t border-slate-100 dark:border-slate-800 px-6 py-4 flex flex-row-reverse gap-3">
                            <button type="submit" class="w-full sm:w-auto inline-flex justify-center rounded-xl border border-transparent px-5 py-2.5 bg-indigo-600 text-white hover:bg-indigo-700 text-sm font-semibold transition">
                                Register & Add to Pool
                            </button>
                            <button type="button" @click="createModalOpen = false" class="w-full sm:w-auto inline-flex justify-center rounded-xl border border-slate-300 dark:border-slate-700 px-5 py-2.5 bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700 text-sm font-medium transition">
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

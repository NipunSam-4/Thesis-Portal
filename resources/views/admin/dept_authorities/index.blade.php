<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center space-x-4">
            <a href="{{ route('system_admin.dashboard') }}" class="text-gray-500 hover:text-blue-600 dark:text-gray-400 dark:hover:text-blue-400 transition">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            </a>
            <h2 class="font-bold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Manage Department Authorities') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12" x-data="{ createModalOpen: false, editModalOpen: false, editId: '', editName: '', editEmail: '' }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if(session('success'))
                <div class="bg-green-50 text-green-700 p-4 rounded-lg shadow-sm border border-green-200 flex items-center">
                    <svg class="h-5 w-5 mr-2" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                    {{ session('success') }}
                </div>
            @endif
            @if($errors->any())
                <div class="bg-red-50 text-red-700 p-4 rounded-lg shadow-sm border border-red-200">
                    {{ $errors->first() }}
                </div>
            @endif

            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-xl border border-gray-100 dark:border-gray-700 overflow-hidden">
                <div class="p-6 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center bg-gray-50 dark:bg-gray-800/50">
                    <div>
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white">Department Authorities Directory</h3>
                        <span class="text-sm text-gray-500 dark:text-gray-400">Total: {{ $authorities->count() }}</span>
                    </div>
                    
                    <button @click="createModalOpen = true" class="bg-purple-600 hover:bg-purple-700 text-white font-bold py-2 px-4 rounded-lg shadow-sm transition transform hover:-translate-y-0.5 flex items-center text-sm">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path></svg>
                        Assign Authority Role
                    </button>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-50 dark:bg-gray-900/50 text-gray-500 text-xs uppercase tracking-wider border-b border-gray-200">
                                <th class="p-4 font-medium">Faculty/Staff Name</th>
                                <th class="p-4 font-medium">Academic Roles</th>
                                <th class="p-4 font-medium text-center">System Status</th>
                                <th class="p-4 font-medium text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700 text-sm">
                            @forelse($authorities as $user)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition">
                                    <td class="p-4">
                                        <div class="font-semibold text-gray-900 dark:text-gray-100">{{ $user->name }}</div>
                                        <div class="text-gray-500 text-xs">{{ $user->email }}</div>
                                    </td>
                                    
                                    <td class="p-4 space-y-1">
                                        @if($user->role === 'hod') <div class="bg-red-100 text-red-800 text-xs font-bold px-2 py-0.5 rounded w-max">Head of Department ({{ $user->facultyProfile->department->code ?? 'N/A' }})</div> @endif
                                        @if($user->role === 'dpgc') <div class="bg-purple-100 text-purple-800 text-xs font-bold px-2 py-0.5 rounded w-max">DPGC Member ({{ $user->facultyProfile->department->code ?? 'N/A' }})</div> @endif
                                        @if($user->role === 'section_officer') <div class="bg-orange-100 text-orange-800 text-xs font-bold px-2 py-0.5 rounded w-max">Section Officer</div> @endif
                                        @if($user->role === 'faculty') <div class="bg-blue-100 text-blue-800 text-xs font-bold px-2 py-0.5 rounded w-max">Faculty Member ({{ $user->facultyProfile->department->code ?? 'N/A' }})</div> @endif
                                    </td>
                                    
                                    <td class="p-4 text-center">
                                        @if($user->is_active)
                                            <span class="bg-green-100 text-green-800 text-xs font-medium px-2.5 py-0.5 rounded-full">Active</span>
                                        @else
                                            <span class="bg-red-100 text-red-800 text-xs font-medium px-2.5 py-0.5 rounded-full">Inactive</span>
                                        @endif
                                    </td>

                                    <td class="p-4 flex justify-end space-x-3">
                                        <button @click="editModalOpen = true; editId = '{{ $user->id }}'; editName = '{{ addslashes($user->name) }}'; editEmail = '{{ addslashes($user->email) }}'" class="text-indigo-600 hover:text-indigo-900 font-medium px-3 py-1 rounded bg-indigo-50 hover:bg-indigo-100 transition">
                                            Edit Info
                                        </button>

                                        <form action="{{ route('system_admin.users.toggle', $user->id) }}" method="POST">
                                            @csrf
                                            @method('PATCH')
                                            @if($user->is_active)
                                                <button type="submit" onclick="return confirm('Deactivate this user? They will lose all portal access.');" class="text-orange-600 hover:text-orange-900 font-medium px-3 py-1 rounded bg-orange-50 hover:bg-orange-100 transition w-24 text-center">
                                                    Deactivate
                                                </button>
                                            @else
                                                <button type="submit" class="text-green-600 hover:text-green-900 font-medium px-3 py-1 rounded bg-green-50 hover:bg-green-100 transition w-24 text-center">
                                                    Activate
                                                </button>
                                            @endif
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="p-8 text-center text-gray-500">
                                        No Department Authorities found. Click "Assign Authority Role" to add one!
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
                <div x-show="createModalOpen" class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="createModalOpen = false"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
                <div x-show="createModalOpen" class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full">
                    
                    <form action="{{ route('system_admin.dept_authorities.store') }}" method="POST">
                        @csrf
                        <div class="bg-white dark:bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Assign Department Authority</h3>
                            
                            <div class="bg-purple-50 dark:bg-purple-900/30 text-purple-800 dark:text-purple-300 p-3 rounded-lg text-xs mb-4">
                                If you enter the email of an existing user, the role will be added to their account. If it is a new email, a new account will be created.
                            </div>

                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Full Name</label>
                                <input type="text" name="name" class="w-full border-gray-300 rounded-lg shadow-sm" required>
                            </div>
                            
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Email Address</label>
                                <input type="email" name="email" class="w-full border-gray-300 rounded-lg shadow-sm" required>
                            </div>

                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Authority Role</label>
                                <select name="role" class="w-full border-gray-300 rounded-lg shadow-sm" required>
                                    <option value="" disabled selected>Select an academic role...</option>
                                    <option value="hod">Head of Department (HoD)</option>
                                    <option value="dpgc">DPGC Member</option>
                                    <option value="pspc">PSPC Member</option>
                                    <option value="section_officer">Section Officer</option>
                                    <option value="office_member">Office Member</option>
                                </select>
                            </div>

                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Target Department</label>
                                <select name="department_id" class="w-full border-gray-300 rounded-lg shadow-sm" required>
                                    <option value="" disabled selected>Select Department...</option>
                                    @foreach($departments as $dept)
                                        <option value="{{ $dept->id }}">{{ $dept->name }} ({{ $dept->code }})</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="bg-gray-50 dark:bg-gray-700/50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                            <button type="submit" class="w-full inline-flex justify-center rounded-md px-4 py-2 bg-purple-600 text-white hover:bg-purple-700 sm:ml-3 sm:w-auto text-sm font-medium">
                                Assign Role
                            </button>
                            <button type="button" @click="createModalOpen = false" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 px-4 py-2 bg-white text-gray-700 hover:bg-gray-50 sm:mt-0 sm:ml-3 sm:w-auto text-sm font-medium">
                                Cancel
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div x-show="editModalOpen" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div x-show="editModalOpen" class="fixed inset-0 bg-gray-500 bg-opacity-75" @click="editModalOpen = false"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
                <div x-show="editModalOpen" class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full">
                    <form :action="`/admin/users/${editId}`" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="bg-white dark:bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Edit User Details</h3>
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700">Name</label>
                                <input type="text" name="name" x-model="editName" class="w-full border-gray-300 rounded-lg shadow-sm" required>
                            </div>
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700">Email</label>
                                <input type="email" name="email" x-model="editEmail" class="w-full border-gray-300 rounded-lg shadow-sm" required>
                            </div>
                        </div>
                        <div class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse">
                            <button type="submit" class="w-full inline-flex justify-center rounded-md px-4 py-2 bg-indigo-600 text-white hover:bg-indigo-700 sm:ml-3 sm:w-auto text-sm font-medium">
                                Save Changes
                            </button>
                            <button type="button" @click="editModalOpen = false" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 px-4 py-2 bg-white text-gray-700 hover:bg-gray-50 sm:mt-0 sm:ml-3 sm:w-auto text-sm font-medium">
                                Cancel
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>
</x-app-layout>
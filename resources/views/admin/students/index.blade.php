<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center space-x-4">
            <a href="{{ route('system_admin.dashboard') }}" class="text-gray-500 hover:text-blue-600 dark:text-gray-400 transition">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            </a>
            <h2 class="font-bold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Manage PhD Students') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12" x-data="{ createModalOpen: false, editModalOpen: false, editId: '', editName: '', editEmail: '' }">
        <div class="max-w-7xl mx-auto px-2 sm:px-6 lg:px-8 space-y-6">

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
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white">Registered PhD Students</h3>
                        <span class="text-sm text-gray-500 dark:text-gray-400">Total Enrolled: {{ $students->count() }}</span>
                    </div>
                    
                    <button @click="createModalOpen = true" class="bg-pink-600 hover:bg-pink-700 text-white font-bold py-2 px-4 rounded-lg shadow-sm transition transform hover:-translate-y-0.5 flex items-center text-sm">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                        Enroll New Student
                    </button>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-50 dark:bg-gray-900/50 text-gray-500 text-xs uppercase tracking-wider border-b border-gray-200">
                                <th class="p-4 font-medium">Student Details</th>
                                <th class="p-4 font-medium">Roll Number</th>
                                <th class="p-4 font-medium">Department & Supervisor</th>
                                <th class="p-4 font-medium text-center">Status</th>
                                <th class="p-4 font-medium text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700 text-sm">
                            @forelse($students as $user)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition">
                                    <td class="p-4">
                                        <div class="font-semibold text-gray-900 dark:text-gray-100">{{ $user->name }}</div>
                                        <div class="text-gray-500 text-xs">{{ $user->email }}</div>
                                    </td>
                                    
                                    <td class="p-4">
                                        <span class="bg-gray-100 text-gray-800 text-xs font-bold px-2.5 py-1 rounded dark:bg-gray-700 dark:text-gray-300">
                                            {{ $user->student->roll_number }}
                                        </span>
                                    </td>

                                    <td class="p-4">
                                        <div class="text-gray-900 dark:text-gray-100 font-medium">{{ $user->student->department->name ?? 'N/A' }}</div>
                                        <div class="text-xs text-gray-500 mt-0.5">Supervisor(s): 
                                            <span class="font-semibold">
                                                {{ $user->student->supervisors->pluck('name')->join(', ') ?: 'Unassigned' }}
                                            </span>
                                        </div>
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
                                            Edit Auth
                                        </button>

                                        <form action="{{ route('system_admin.users.toggle', $user->id) }}" method="POST">
                                            @csrf
                                            @method('PATCH')
                                            @if($user->is_active)
                                                <button type="submit" onclick="return confirm('Deactivate this student? They will not be able to log in or submit forms.');" class="text-orange-600 hover:text-orange-900 font-medium px-3 py-1 rounded bg-orange-50 hover:bg-orange-100 transition w-24 text-center">
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
                                    <td colspan="5" class="p-8 text-center text-gray-500 dark:text-gray-400">
                                        No PhD Students enrolled yet. Click "Enroll New Student" to get started!
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
                <div x-show="createModalOpen" class="fixed inset-0 bg-gray-500 bg-opacity-75" @click="createModalOpen = false"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
                <div x-show="createModalOpen" class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full">
                    
                    <form action="{{ route('system_admin.students.store') }}" method="POST">
                        @csrf
                        <div class="bg-white dark:bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Enroll New PhD Student</h3>

                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Full Name</label>
                                <input type="text" name="name" class="w-full border-gray-300 rounded-lg shadow-sm" required>
                            </div>
                            
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Email Address</label>
                                <input type="email" name="email" class="w-full border-gray-300 rounded-lg shadow-sm" required>
                            </div>

                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Roll Number</label>
                                <input type="text" name="roll_number" placeholder="e.g. 230001001" class="w-full border-gray-300 rounded-lg shadow-sm uppercase" required>
                            </div>

                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Department</label>
                                <select name="department_id" class="w-full border-gray-300 rounded-lg shadow-sm" required>
                                    <option value="" disabled selected>Select Department...</option>
                                    @foreach($departments as $dept)
                                        <option value="{{ $dept->id }}">{{ $dept->name }} ({{ $dept->code }})</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="grid grid-cols-3 gap-4 mb-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Admission Category</label>
                                    <input type="text" name="admission_category" placeholder="e.g. TA, FA, Sponsored" class="w-full border-gray-300 rounded-lg shadow-sm">
                                </div>
                                <div>
                                    <label class="flex items-center text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                        <span>Course Credits Required</span>
                                        <x-info-button text="Minimum course credits required for the degree program." />
                                    </label>
                                    <input type="number" step="0.5" name="course_credits_required" placeholder="e.g. 36" min="0" @wheel="$event.target.blur()" onwheel="this.blur()" class="w-full border-gray-300 rounded-lg shadow-sm">
                                </div>
                                <div>
                                    <label class="flex items-center text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                        <span>Course Credits Earned</span>
                                        <x-info-button text="Course credits earned including coursework, seminars, and research credits." />
                                    </label>
                                    <input type="number" step="0.5" name="course_credits_earned" placeholder="e.g. 36.5" min="0" @wheel="$event.target.blur()" onwheel="this.blur()" class="w-full border-gray-300 rounded-lg shadow-sm">
                                </div>
                            </div>

                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Main Supervisor(s)</label>
                                <select name="main_supervisor_ids[]" multiple class="w-full border-gray-300 rounded-lg shadow-sm h-32" required>
                                    @foreach($supervisors as $supervisor)
                                        <option value="{{ $supervisor->id }}">
                                            {{ $supervisor->name }} [Dept: {{ $supervisor->department->code ?? 'N/A' }}]
                                        </option>
                                    @endforeach
                                </select>
                                <p class="text-xs text-gray-500 mt-1">Hold CTRL (Windows) or CMD (Mac) to select multiple joint supervisors.</p>
                            </div>

                        </div>
                        <div class="bg-gray-50 dark:bg-gray-700/50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                            <button type="submit" class="w-full inline-flex justify-center rounded-md px-4 py-2 bg-pink-600 text-white hover:bg-pink-700 sm:ml-3 sm:w-auto text-sm font-medium">
                                Register Student
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
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Edit Student Login Info</h3>
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
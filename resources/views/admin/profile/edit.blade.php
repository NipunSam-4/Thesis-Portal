<x-app-layout>
    <x-admin-top-navbar />

    <div class="py-4 sm:py-8" x-data="{
        showCurrent: false,
        showNew: false,
        showConfirm: false,
        tooltipCurrent: false,
        tooltipNew: false
    }">
        <div class="max-w-4xl mx-auto px-3 sm:px-6 lg:px-8 space-y-4 sm:space-y-6">

            <!-- Responsive Header Component -->
            <x-admin-header title="Admin Account Profile" description="Manage your administrator account credentials, email, and password settings." />

            <!-- Alerts -->
            @if(session('success'))
                <div class="bg-emerald-50 dark:bg-emerald-900/30 border border-emerald-200 dark:border-emerald-800 text-emerald-800 dark:text-emerald-300 px-4 py-3 rounded-xl text-sm flex items-center shadow-sm">
                    <svg class="w-5 h-5 mr-2 text-emerald-600 dark:text-emerald-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    {{ session('success') }}
                </div>
            @endif

            @if($errors->any())
                <div class="bg-rose-50 dark:bg-rose-900/30 border border-rose-200 dark:border-rose-800 text-rose-800 dark:text-rose-300 px-4 py-3 rounded-xl text-sm shadow-sm">
                    <ul class="list-disc list-inside space-y-1">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Profile Form Card -->
            <div class="bg-white dark:bg-slate-900 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-800 overflow-hidden">
                <div class="p-6 border-b border-slate-100 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-800/50">
                    <h3 class="font-bold text-slate-900 dark:text-white text-lg">Profile Information & Security</h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Update your account's profile name, email address, and security password.</p>
                </div>

                <form action="{{ route('admin.profile.update') }}" method="POST" class="p-6 space-y-6">
                    @csrf
                    @method('PUT')

                    <!-- Full Name Field -->
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Full Name <span class="text-rose-500">*</span></label>
                        <input type="text" name="name" value="{{ old('name', $admin->name) }}" class="w-full border-slate-300 dark:border-slate-700 dark:bg-slate-800 dark:text-white rounded-xl shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-sm" required>
                    </div>

                    <!-- Email Address Field -->
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Email Address <span class="text-rose-500">*</span></label>
                        <input type="email" name="email" value="{{ old('email', $admin->email) }}" class="w-full border-slate-300 dark:border-slate-700 dark:bg-slate-800 dark:text-white rounded-xl shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-sm" required>
                    </div>

                    <div class="pt-4 border-t border-slate-100 dark:border-slate-800">
                        <h4 class="font-bold text-slate-900 dark:text-white text-base mb-1">Update Security Password</h4>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mb-4">Leave password fields blank if you do not wish to change your current password.</p>

                        <!-- Current Password Field -->
                        <div class="mb-4">
                            <div class="flex items-center justify-between mb-1">
                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 flex items-center">
                                    Current Password
                                    <div class="relative inline-block ml-1.5" @mouseenter="tooltipCurrent = true" @mouseleave="tooltipCurrent = false">
                                        <svg class="w-4 h-4 text-slate-400 hover:text-indigo-600 cursor-pointer" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        <div x-show="tooltipCurrent" style="display: none;" class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 w-56 p-2 bg-slate-900 text-white text-xs rounded-lg shadow-xl text-center z-50">
                                            Enter your current password to authorize a password change.
                                        </div>
                                    </div>
                                </label>
                            </div>
                            <div class="relative">
                                <input :type="showCurrent ? 'text' : 'password'" name="current_password" class="w-full border-slate-300 dark:border-slate-700 dark:bg-slate-800 dark:text-white rounded-xl shadow-sm pr-10 focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                                <button type="button" @click="showCurrent = !showCurrent" class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600 dark:hover:text-slate-200">
                                    <svg x-show="!showCurrent" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    <svg x-show="showCurrent" style="display: none;" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858-5.908a10.025 10.025 0 012.122-.363c4.478 0 8.268 2.943 9.542 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21M3 3l18 18"/></svg>
                                </button>
                            </div>
                        </div>

                        <!-- New Password Field -->
                        <div class="mb-4">
                            <div class="flex items-center justify-between mb-1">
                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 flex items-center">
                                    New Password
                                    <div class="relative inline-block ml-1.5" @mouseenter="tooltipNew = true" @mouseleave="tooltipNew = false">
                                        <svg class="w-4 h-4 text-slate-400 hover:text-indigo-600 cursor-pointer" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        <div x-show="tooltipNew" style="display: none;" class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 w-56 p-2 bg-slate-900 text-white text-xs rounded-lg shadow-xl text-center z-50">
                                            Password must be at least 8 characters long.
                                        </div>
                                    </div>
                                </label>
                            </div>
                            <div class="relative">
                                <input :type="showNew ? 'text' : 'password'" name="password" class="w-full border-slate-300 dark:border-slate-700 dark:bg-slate-800 dark:text-white rounded-xl shadow-sm pr-10 focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                                <button type="button" @click="showNew = !showNew" class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600 dark:hover:text-slate-200">
                                    <svg x-show="!showNew" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    <svg x-show="showNew" style="display: none;" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858-5.908a10.025 10.025 0 012.122-.363c4.478 0 8.268 2.943 9.542 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21M3 3l18 18"/></svg>
                                </button>
                            </div>
                        </div>

                        <!-- Confirm New Password Field -->
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Confirm New Password</label>
                            <div class="relative">
                                <input :type="showConfirm ? 'text' : 'password'" name="password_confirmation" class="w-full border-slate-300 dark:border-slate-700 dark:bg-slate-800 dark:text-white rounded-xl shadow-sm pr-10 focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                                <button type="button" @click="showConfirm = !showConfirm" class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600 dark:hover:text-slate-200">
                                    <svg x-show="!showConfirm" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    <svg x-show="showConfirm" style="display: none;" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858-5.908a10.025 10.025 0 012.122-.363c4.478 0 8.268 2.943 9.542 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21M3 3l18 18"/></svg>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="flex justify-end pt-4 border-t border-slate-100 dark:border-slate-800">
                        <button type="submit" class="inline-flex items-center px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-xl shadow-sm transition">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            Save Admin Profile Changes
                        </button>
                    </div>

                </form>
            </div>

        </div>
    </div>
</x-app-layout>

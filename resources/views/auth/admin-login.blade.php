<x-guest-layout>
    <div class="mb-5 text-center">
        <h2 class="text-xl font-bold text-slate-900 dark:text-white">Administrator Login</h2>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('admin.login') }}">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" value="Email" class="dark:text-gray-400" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required
                autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <div class="flex items-center gap-1.5">
                <x-input-label for="password" value="Password" class="dark:text-gray-400" />
                <div class="password-tooltip">
                    <svg class="w-4 h-4 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <div class="tooltip-box bg-slate-900 text-white border border-slate-700 shadow-xl dark:bg-slate-100 dark:text-slate-900 dark:border-slate-300">
                        Your password must be at least 8 characters long and may contain letters, numbers, and symbols.
                    </div>
                </div>
            </div>

            <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required
                autocomplete="current-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Remember Me -->
        <div class="flex items-center justify-between mt-4">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox"
                    class="rounded border-gray-300 dark:border-gray-700 text-indigo-600 shadow-sm focus:ring-indigo-500 dark:bg-gray-900"
                    name="remember">
                <span class="ms-2 text-sm text-gray-600 dark:text-gray-400">
                    Remember me
                </span>
            </label>

            @if (Route::has('password.request'))
                <a href="{{ route('password.request') }}"
                    class="text-sm underline text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100">
                    Forgot password?
                </a>
            @endif
        </div>

        <!-- Actions -->
        <div class="mt-6 space-y-4">
            <x-primary-button class="w-full justify-center">
                Log In
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>

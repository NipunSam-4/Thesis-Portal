<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
            {{ __('Profile Information') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
            {{ __("Your official account details as registered with the Academic Office.") }}
        </p>
    </header>

    @if($user->isStudent())
        <div class="mt-4 p-3 bg-amber-50 dark:bg-amber-950/40 border border-amber-200 dark:border-amber-800 rounded-lg text-xs font-semibold text-amber-800 dark:text-amber-300">
            🔒 Note: Student profile details are managed centrally by Academic Office and cannot be edited.
        </div>
    @endif

    <div class="mt-6 space-y-6">
        <div>
            <x-input-label for="name" :value="__('Name')" />
            <x-text-input id="name" name="name" type="text" class="mt-1 block bg-gray-100 dark:bg-gray-700 text-gray-500 cursor-not-allowed" :value="old('name', $user->name)" :disabled="true" :readonly="true" required autocomplete="name" />
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" name="email" type="email" class="mt-1 block bg-gray-100 dark:bg-gray-700 text-gray-500 cursor-not-allowed" :value="old('email', $user->email)" :disabled="true" :readonly="true" required autocomplete="username" />
            <x-input-error class="mt-2" :messages="$errors->get('email')" />
        </div>
    </div>
</section>

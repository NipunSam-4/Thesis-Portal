<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
            {{ __('Profile Information') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
            @if($user->isStudent())
                {{ __("Your official account details as registered with Academic Administration.") }}
            @else
                {{ __("Update your account's profile information and email address.") }}
            @endif
        </p>
    </header>

    @if($user->isStudent())
        <div class="mt-4 p-3 bg-amber-50 dark:bg-amber-950/40 border border-amber-200 dark:border-amber-800 rounded-lg text-xs font-semibold text-amber-800 dark:text-amber-300">
            🔒 Note: Student profile details (Name & Email) are managed centrally by Academic Administration and cannot be edited.
        </div>
    @endif

    <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-6">
        @csrf
        @method('patch')

        <div>
            <x-input-label for="name" :value="__('Name')" />
            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full {{ $user->isStudent() ? 'bg-gray-100 dark:bg-gray-700 text-gray-500 cursor-not-allowed' : '' }}" :value="old('name', $user->name)" :disabled="$user->isStudent()" :readonly="$user->isStudent()" required autocomplete="name" />
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>

        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full {{ $user->isStudent() ? 'bg-gray-100 dark:bg-gray-700 text-gray-500 cursor-not-allowed' : '' }}" :value="old('email', $user->email)" :disabled="$user->isStudent()" :readonly="$user->isStudent()" required autocomplete="username" />
            <x-input-error class="mt-2" :messages="$errors->get('email')" />
        </div>

        @if(!$user->isStudent())
            <div class="flex items-center gap-4">
                <x-primary-button>{{ __('Save') }}</x-primary-button>

                @if (session('status') === 'profile-updated')
                    <p
                        x-data="{ show: true }"
                        x-show="show"
                        x-transition
                        x-init="setTimeout(() => show = false, 2000)"
                        class="text-sm text-gray-600"
                    >{{ __('Saved.') }}</p>
                @endif
            </div>
        @endif
    </form>
</section>

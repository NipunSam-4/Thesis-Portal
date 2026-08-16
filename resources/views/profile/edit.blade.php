<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Profile Settings') }}
            </h2>
            <x-back-to-dashboard-button />
        </div>
    </x-slot>

    <div class="py-8 sm:py-12">
        <div class="max-w-7xl mx-auto px-2 sm:px-6 lg:px-8 space-y-6">

            @if(session('warning'))
                <div class="p-4 bg-amber-100 border-l-4 border-amber-500 text-amber-800 rounded-lg shadow-sm font-semibold text-sm">
                    {{ session('warning') }}
                </div>
            @endif

            <div class="p-3.5 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                @if(Auth::user()->isStudent())
                    @include('profile.partials.student-profile-info')
                @else
                    <div class="max-w-xl">
                        @include('profile.partials.update-profile-information-form')
                    </div>
                @endif
            </div>

            <div class="p-3.5 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.update-password-form')
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

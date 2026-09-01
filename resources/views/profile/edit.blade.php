<x-app-layout>
    <div class="py-6">
        <div class="max-w-3xl mx-auto px-2 sm:px-6 lg:px-8 space-y-4">

            <!-- Top Back to Dashboard Button -->
            <div>
                <x-back-to-dashboard-button />
            </div>

            <!-- Page Header Card -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-5">
                <h2 class="text-xl font-bold text-gray-900 dark:text-white leading-tight">
                    {{ __('Profile Settings') }}
                </h2>
            </div>

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

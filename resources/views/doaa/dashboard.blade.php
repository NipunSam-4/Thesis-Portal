<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-bold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Dean of Academic Affairs Portal') }}
            </h2>
            <span class="bg-blue-100 text-blue-800 text-xs font-medium px-3 py-1 rounded-full dark:bg-blue-900 dark:text-blue-300">
                DOAA
            </span>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="bg-blue-700 dark:bg-blue-900 rounded-xl shadow-sm p-6 text-white">
                <h2 class="text-2xl font-bold mb-1">Welcome, {{ $user->name }}</h2>
                <p class="text-blue-100 text-sm">Dean of Academic Affairs (DOAA) Executive Portal</p>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Institute Academic Submissions Overview</h3>
                @forelse($allTheses as $thesis)
                    <div class="p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg mb-3 flex justify-between items-center">
                        <div>
                            <div class="font-semibold text-gray-900 dark:text-white">{{ $thesis->student->user->name ?? 'Scholar' }} ({{ $thesis->student->department->code ?? 'N/A' }})</div>
                            <div class="text-sm text-gray-600 dark:text-gray-300 mt-1">{{ $thesis->title }}</div>
                        </div>
                        <span class="bg-blue-100 text-blue-800 text-xs font-bold px-2.5 py-1 rounded">
                            {{ $thesis->current_status }}
                        </span>
                    </div>
                @empty
                    <p class="text-sm text-gray-500">No active submissions found.</p>
                @endforelse
            </div>
        </div>
    </div>
</x-app-layout>

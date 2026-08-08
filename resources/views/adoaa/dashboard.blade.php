<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-bold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Associate Dean Portal') }}
            </h2>
            <span class="bg-indigo-100 text-indigo-800 text-xs font-medium px-3 py-1 rounded-full dark:bg-indigo-900 dark:text-indigo-300">
                ADoAA
            </span>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="bg-indigo-700 dark:bg-indigo-900 rounded-xl shadow-sm p-6 text-white">
                <h2 class="text-2xl font-bold mb-1">Welcome, {{ $user->name }}</h2>
                <p class="text-indigo-100 text-sm">Associate Dean of Academic Affairs (ADoAA) Portal</p>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Academic Review Submissions</h3>
                @forelse($allTheses as $thesis)
                    <div class="p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg mb-3 flex justify-between items-center">
                        <div>
                            <div class="font-semibold text-gray-900 dark:text-white">{{ $thesis->student->user->name ?? 'Student' }} ({{ $thesis->student->department->code ?? 'N/A' }})</div>
                            <div class="text-sm text-gray-600 dark:text-gray-300 mt-1">{{ $thesis->title }}</div>
                        </div>
                        <span class="bg-indigo-100 text-indigo-800 text-xs font-bold px-2.5 py-1 rounded">
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

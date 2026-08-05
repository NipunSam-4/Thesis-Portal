<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-bold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Supervisor Dashboard') }}
            </h2>
            <span class="bg-blue-100 text-blue-800 text-xs font-medium px-3 py-1 rounded-full dark:bg-blue-900 dark:text-blue-300">
                Main / Co-Supervisor
            </span>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            <div class="bg-blue-600 dark:bg-blue-900 rounded-xl shadow-sm p-6 text-white flex items-center justify-between">
                <div>
                    <h2 class="text-2xl font-bold mb-1">Welcome, {{ Auth::user()->name }}</h2>
                    <p class="text-blue-100 text-sm">Manage your assigned students and their thesis submissions.</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Main Supervisor Section -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50 flex justify-between items-center">
                        <h3 class="font-bold text-gray-900 dark:text-white">Main Supervised Students</h3>
                        <span class="bg-blue-100 text-blue-800 text-xs font-semibold px-2.5 py-0.5 rounded dark:bg-blue-200 dark:text-blue-800">{{ $supervisor->students->count() }}</span>
                    </div>
                    
                    <div class="p-0">
                        @if($supervisor->students->count() > 0)
                            <ul class="divide-y divide-gray-100 dark:divide-gray-700">
                                @foreach($supervisor->students as $student)
                                    <li class="p-6 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition">
                                        <div class="flex items-center justify-between">
                                            <div>
                                                <h4 class="text-sm font-bold text-gray-900 dark:text-white">{{ $student->user->name ?? 'Unknown Student' }}</h4>
                                                <p class="text-xs text-gray-500 mt-1">Roll Number: {{ $student->roll_number }}</p>
                                            </div>
                                            <a href="#" class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-full shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                                Manage
                                            </a>
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            <div class="p-6 text-center text-gray-500 dark:text-gray-400">
                                You are not assigned as a main supervisor to any students.
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Co-Supervisor Section -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50 flex justify-between items-center">
                        <h3 class="font-bold text-gray-900 dark:text-white">Co-Supervised Theses</h3>
                        <span class="bg-purple-100 text-purple-800 text-xs font-semibold px-2.5 py-0.5 rounded dark:bg-purple-200 dark:text-purple-800">{{ $supervisor->coSupervisedTheses->count() }}</span>
                    </div>
                    
                    <div class="p-0">
                        @if($supervisor->coSupervisedTheses->count() > 0)
                            <ul class="divide-y divide-gray-100 dark:divide-gray-700">
                                @foreach($supervisor->coSupervisedTheses as $thesis)
                                    <li class="p-6 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition">
                                        <div class="flex items-center justify-between">
                                            <div>
                                                <h4 class="text-sm font-bold text-gray-900 dark:text-white">{{ $thesis->student->user->name ?? 'Unknown Student' }}</h4>
                                                <p class="text-xs text-gray-500 mt-1">Roll Number: {{ $thesis->student->roll_number }}</p>
                                                <p class="text-xs text-gray-600 dark:text-gray-400 mt-1 font-semibold truncate max-w-[200px]" title="{{ $thesis->title }}">Thesis: {{ $thesis->title ?? 'N/A' }}</p>
                                            </div>
                                            <a href="#" class="inline-flex items-center px-3 py-1.5 border border-gray-300 text-xs font-medium rounded-full text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-600">
                                                Review
                                            </a>
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            <div class="p-6 text-center text-gray-500 dark:text-gray-400">
                                You are not assigned as a co-supervisor to any students.
                            </div>
                        @endif
                    </div>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>

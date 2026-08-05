<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-bold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('PSPC Dashboard') }}
            </h2>
            <span class="bg-indigo-100 text-indigo-800 text-xs font-medium px-3 py-1 rounded-full dark:bg-indigo-900 dark:text-indigo-300">
                Postgraduate Student Progress Committee
            </span>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            <div class="bg-indigo-600 dark:bg-indigo-900 rounded-xl shadow-sm p-6 text-white flex items-center justify-between">
                <div>
                    <h2 class="text-2xl font-bold mb-1">Welcome, {{ Auth::user()->name }}</h2>
                    <p class="text-indigo-100 text-sm">Review synopsis submissions (PTS-1 / MSRTS-1) pending your recommendation.</p>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50 flex justify-between items-center">
                    <h3 class="font-bold text-gray-900 dark:text-white">Pending Synopsis Reviews</h3>
                    <span class="bg-indigo-100 text-indigo-800 text-xs font-semibold px-2.5 py-0.5 rounded dark:bg-indigo-200 dark:text-indigo-800">{{ $pendingSynopsis->count() }}</span>
                </div>
                
                <div class="p-0">
                    @if($pendingSynopsis->count() > 0)
                        <ul class="divide-y divide-gray-100 dark:divide-gray-700">
                            @foreach($pendingSynopsis as $submission)
                                <li class="p-6 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition">
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <h4 class="text-sm font-bold text-gray-900 dark:text-white">{{ $submission->thesis->student->user->name ?? 'Unknown Student' }}</h4>
                                            <p class="text-xs text-gray-500 mt-1">Form Type: {{ $submission->form_type }}</p>
                                        </div>
                                        <button class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-full shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                            Review
                                        </button>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <div class="p-6 text-center text-gray-500 dark:text-gray-400">
                            There are currently no submissions pending your review.
                        </div>
                    @endif
                </div>
            </div>

        </div>
    </div>
</x-app-layout>

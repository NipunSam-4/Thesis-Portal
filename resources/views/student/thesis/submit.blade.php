<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-bold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Submit Thesis (PTS-2)') }}
            </h2>
            <a href="{{ route('student.dashboard') }}" class="text-indigo-600 dark:text-indigo-400 hover:underline text-sm font-medium">
                &larr; Back to Dashboard
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50">
                    <h3 class="font-bold text-gray-900 dark:text-white">PTS-2 Form: Thesis Submission</h3>
                    <p class="text-sm text-gray-500 mt-1">Please provide the necessary details and upload your thesis document.</p>
                </div>

                <div class="p-6">
                    <form action="#" method="POST" enctype="multipart/form-data" class="space-y-6">
                        @csrf

                        <div>
                            <x-input-label for="title" value="Thesis Title" />
                            <x-text-input id="title" class="block mt-1 w-full bg-gray-100" type="text" name="title" value="{{ $student->thesis->title }}" readonly />
                            <p class="text-xs text-gray-500 mt-1">This is your registered thesis title.</p>
                        </div>

                        <div>
                            <x-input-label for="thesis_document" value="Upload Thesis Document (PDF)" />
                            <input type="file" id="thesis_document" name="thesis_document" accept="application/pdf" class="block mt-1 w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 dark:text-gray-400 focus:outline-none dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 p-2">
                            <x-input-error :messages="$errors->get('thesis_document')" class="mt-2" />
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <x-primary-button class="ml-4">
                                {{ __('Submit PTS-2') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

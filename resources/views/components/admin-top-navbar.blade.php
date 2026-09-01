<x-slot name="header">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
        <h2 class="font-bold text-lg sm:text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Thesis Management Portal') }}
        </h2>
        <div class="flex items-center space-x-3 w-full sm:w-auto justify-between sm:justify-end">
            <span class="bg-blue-100 text-blue-800 text-xs font-medium px-3 py-1 rounded-full dark:bg-blue-900 dark:text-blue-300">
                @if(auth('admin')->user()->isSuperAdmin())
                    Super Admin
                @else
                    System Admin
                @endif
            </span>
            <x-profile_dropdown/>
        </div>
    </div>
</x-slot>

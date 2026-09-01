<x-dropdown align="right" width="48" content-classes="py-1 bg-white dark:bg-gray-800 dark:border dark:border-gray-700">
    <x-slot name="trigger">
        <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-700 dark:text-gray-200 bg-transparent hover:text-gray-900 dark:hover:text-white focus:outline-none transition ease-in-out duration-150">
            <div>{{ (Auth::user() ?? Auth::guard('admin')->user())->name ?? 'User' }}</div>

            <div class="ms-1">
                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                </svg>
            </div>
        </button>
    </x-slot>

    <x-slot name="content">
        <x-dropdown-link :href="Auth::guard('admin')->check() ? route('admin.profile.edit') : route('profile.edit')" class="font-normal text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 dark:hover:text-white">
            Profile
        </x-dropdown-link>

        <!-- Authentication -->
        <form method="POST" action="{{ Auth::guard('admin')->check() ? route('admin.logout') : route('logout') }}">
            @csrf

            <x-dropdown-link :href="Auth::guard('admin')->check() ? route('admin.logout') : route('logout')" onclick="event.preventDefault(); this.closest('form').submit();" class="font-normal text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 dark:hover:text-white">
                Log Out
            </x-dropdown-link>
        </form>
    </x-slot>
</x-dropdown>
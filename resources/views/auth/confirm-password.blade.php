<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Secure Area | IIT Indore</title>
    <link rel="icon" type="image/png" href="{{ asset('images/iiti-logo.png') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="antialiased bg-gray-50 text-gray-900 dark:bg-gray-900 dark:text-white transition-colors duration-300">
    <div class="min-h-screen flex flex-col items-center justify-center px-4 relative overflow-hidden">
        
        <div class="absolute top-0 left-0 w-full h-64 bg-blue-900 dark:bg-blue-950 shadow-lg -z-10 sm:rounded-b-[100px] transition-colors duration-300"></div>

        <div class="mb-8 text-center flex flex-col items-center">
            <img src="{{ asset('images/iiti-logo.png') }}" class="h-20 sm:h-24 w-auto mb-4 drop-shadow-xl bg-white/10 rounded-full p-2">
            <h2 class="text-2xl font-extrabold text-white">PhD Integrated Portal</h2>
            <p class="text-blue-100 font-medium">Indian Institute of Technology, Indore</p>
        </div>

        <div class="w-full sm:max-w-md bg-white dark:bg-gray-800 px-8 py-10 shadow-2xl rounded-2xl border border-gray-100 dark:border-gray-700">
            <p class="text-sm text-gray-600 dark:text-gray-400 mb-6 leading-relaxed">
                This is a secure area of the application. Please confirm your password before continuing.
            </p>

            <form method="POST" action="{{ route('password.confirm') }}">
                @csrf

                <div>
                    <label class="block font-semibold text-sm text-gray-700 dark:text-gray-300">
                        Password
                    </label>
                    <input type="password" 
                           name="password" 
                           required 
                           autocomplete="current-password"
                           class="block mt-2 w-full border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-lg shadow-sm" />
                    <x-input-error :messages="$errors->get('password')" class="mt-2 text-red-500" />
                </div>

                <div class="mt-8 flex justify-end">
                    <button type="submit" class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-lg shadow-md transition transform hover:-translate-y-0.5">
                        Confirm Password
                    </button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
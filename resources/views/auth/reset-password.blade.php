<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Update Password | IIT Indore</title>
    <link rel="icon" type="image/png" href="{{ asset('images/iiti-logo.png') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="antialiased bg-gray-50 text-gray-900 dark:bg-gray-900 dark:text-white transition-colors duration-300">
    <div class="min-h-screen flex flex-col items-center justify-center px-4 relative">
        <div class="absolute top-0 left-0 w-full h-64 bg-blue-900 dark:bg-blue-950 -z-10 sm:rounded-b-[100px]"></div>

        <div class="mb-8 text-center flex flex-col items-center">
            <img src="{{ asset('images/iiti-logo.png') }}" class="h-24 w-auto mb-4 bg-white/10 rounded-full p-2">
            <h2 class="text-2xl font-extrabold text-white">PhD Integrated Portal</h2>
            <p class="text-blue-100 font-medium text-center">Indian Institute of Technology, Indore</p>
        </div>

        <div class="w-full sm:max-w-md bg-white dark:bg-gray-800 px-8 py-10 shadow-2xl rounded-2xl">
            <form method="POST" action="{{ route('password.store') }}">
                @csrf
                <input type="hidden" name="token" value="{{ $request->route('token') }}">

                <div>
                    <label class="block font-semibold text-sm text-gray-700 dark:text-gray-300">Email Address</label>
                    <input type="email" name="email" value="{{ old('email', $request->email) }}" required class="block mt-2 w-full border-gray-300 dark:border-gray-600 dark:bg-gray-900 rounded-lg shadow-sm" />
                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                </div>

                <div class="mt-4">
                    <label class="block font-semibold text-sm text-gray-700 dark:text-gray-300">New Password</label>
                    <input type="password" name="password" required class="block mt-2 w-full border-gray-300 dark:border-gray-600 dark:bg-gray-900 rounded-lg shadow-sm" />
                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                </div>

                <div class="mt-4">
                    <label class="block font-semibold text-sm text-gray-700 dark:text-gray-300">Confirm Password</label>
                    <input type="password" name="password_confirmation" required class="block mt-2 w-full border-gray-300 dark:border-gray-600 dark:bg-gray-900 rounded-lg shadow-sm" />
                </div>

                <div class="mt-8">
                    <button type="submit" class="w-full px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-lg transition transform hover:-translate-y-0.5">
                        Reset Password
                    </button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
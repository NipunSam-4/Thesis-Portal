<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Reset Password | IIT Indore</title>
    <link rel="icon" type="image/png" href="{{ asset('images/iiti-logo.png') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="antialiased bg-gray-50 text-gray-900 dark:bg-gray-900 dark:text-white transition-colors duration-300">
    <div class="min-h-screen flex flex-col items-center justify-center px-4 relative">
        <div class="absolute top-0 left-0 w-full h-64 bg-blue-900 dark:bg-blue-950 shadow-lg -z-10 sm:rounded-b-[100px]"></div>

        <div class="mb-8 text-center flex flex-col items-center">
            <a href="/"><img src="{{ asset('images/iiti-logo.png') }}" class="h-24 w-auto mb-4 drop-shadow-xl p-2 bg-white/10 rounded-full"></a>
            <h2 class="text-2xl font-extrabold text-white">PhD Integrated Portal</h2>
            <p class="text-blue-100 font-medium">Indian Institute of Technology, Indore</p>
        </div>

        <div class="w-full sm:max-w-md bg-white dark:bg-gray-800 px-8 py-10 shadow-2xl rounded-2xl border border-gray-100 dark:border-gray-700">
            <p class="text-sm text-gray-600 dark:text-gray-400 mb-6 leading-relaxed">
                Forgot your password? No problem. Enter your institutional email address and we will email you a password reset link.
            </p>

            <x-auth-session-status class="mb-4" :status="session('status')" />

            <form method="POST" action="{{ route('password.email') }}">
                @csrf
                <div>
                    <label class="block font-semibold text-sm text-gray-700 dark:text-gray-300">Email Address</label>
                    <input type="email" name="email" :value="old('email')" required autofocus class="block mt-2 w-full border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-300 rounded-lg shadow-sm focus:ring-blue-500" />
                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                </div>

                <div class="mt-8">
                    <button type="submit" class="w-full px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-lg shadow-md transition transform hover:-translate-y-0.5">
                        Email Password Reset Link
                    </button>
                </div>
            </form>
        </div>
    </div>
</body>
</html><!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Reset Password | IIT Indore</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="antialiased bg-gray-50 text-gray-900 dark:bg-gray-900 dark:text-white transition-colors duration-300">
    <div class="min-h-screen flex flex-col items-center justify-center px-4 relative">
        <div class="absolute top-0 left-0 w-full h-64 bg-blue-900 dark:bg-blue-950 shadow-lg -z-10 sm:rounded-b-[100px]"></div>

        <div class="mb-8 text-center flex flex-col items-center">
            <a href="/"><img src="{{ asset('images/iiti-logo.png') }}" class="h-24 w-auto mb-4 drop-shadow-xl p-2 bg-white/10 rounded-full"></a>
            <h2 class="text-2xl font-extrabold text-white">PhD Integrated Portal</h2>
            <p class="text-blue-100 font-medium">Indian Institute of Technology, Indore</p>
        </div>

        <div class="w-full sm:max-w-md bg-white dark:bg-gray-800 px-8 py-10 shadow-2xl rounded-2xl border border-gray-100 dark:border-gray-700">
            <p class="text-sm text-gray-600 dark:text-gray-400 mb-6 leading-relaxed">
                Forgot your password? No problem. Enter your institutional email address and we will email you a password reset link.
            </p>

            <x-auth-session-status class="mb-4" :status="session('status')" />

            <form method="POST" action="{{ route('password.email') }}">
                @csrf
                <div>
                    <label class="block font-semibold text-sm text-gray-700 dark:text-gray-300">Email Address</label>
                    <input type="email" name="email" :value="old('email')" required autofocus class="block mt-2 w-full border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-300 rounded-lg shadow-sm focus:ring-blue-500" />
                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                </div>

                <div class="mt-8">
                    <button type="submit" class="w-full px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-lg shadow-md transition transform hover:-translate-y-0.5">
                        Email Password Reset Link
                    </button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
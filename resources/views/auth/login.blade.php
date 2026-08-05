<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login | PhD Portal | IIT Indore</title>\
    <link rel="icon" type="image/png" href="{{ asset('images/iiti-logo.png') }}">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="antialiased bg-gray-50 text-gray-900 dark:bg-gray-900 dark:text-white transition-colors duration-300 selection:bg-blue-500 selection:text-white">
    
    <div class="min-h-screen flex flex-col sm:justify-center items-center pt-10 sm:pt-0 px-4 relative overflow-hidden">
        
        <div class="absolute top-0 left-0 w-full h-64 bg-blue-900 dark:bg-blue-950 shadow-lg -z-10 transition-colors duration-300 sm:hidden"></div>

        <div class="mb-8 text-center flex flex-col items-center z-10">
            <a href="/">
                <img src="{{ asset('images/iiti-logo.png') }}" 
                     alt="IIT Indore Logo" 
                     class="h-20 sm:h-24 w-auto mb-4 drop-shadow-xl bg-white/10 dark:bg-transparent rounded-full p-2">
            </a>
            <h2 class="text-2xl sm:text-3xl font-extrabold text-white sm:text-gray-800 sm:dark:text-gray-100 drop-shadow-md sm:drop-shadow-none">
                PhD Integrated Portal
            </h2>
            <p class="text-blue-100 sm:text-gray-500 dark:text-gray-400 mt-2 font-medium">
                Sign in to your account
            </p>
        </div>

        <div class="w-full sm:max-w-md bg-white dark:bg-gray-800 px-8 py-10 shadow-2xl sm:rounded-2xl border border-gray-100 dark:border-gray-700 transition-colors duration-300 z-10">
            
            <x-auth-session-status class="mb-4" :status="session('status')" />

            <form method="POST" action="{{ route('login') }}">
                @csrf

                <div>
                    <label for="email" class="block font-semibold text-sm text-gray-700 dark:text-gray-300">
                        Email Address
                    </label>
                    <input id="email" 
                           type="email" 
                           name="email" 
                           value="{{ old('email') }}" 
                           required 
                           autofocus 
                           autocomplete="username"
                           class="block mt-2 w-full border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-300 focus:border-blue-500 dark:focus:border-blue-500 focus:ring-blue-500 dark:focus:ring-blue-500 rounded-lg shadow-sm transition-colors" />
                    <x-input-error :messages="$errors->get('email')" class="mt-2 text-red-500" />
                </div>

                <div class="mt-6">
                    <label for="password" class="block font-semibold text-sm text-gray-700 dark:text-gray-300">
                        Password
                    </label>
                    <input id="password" 
                           type="password" 
                           name="password" 
                           required 
                           autocomplete="current-password"
                           class="block mt-2 w-full border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-300 focus:border-blue-500 dark:focus:border-blue-500 focus:ring-blue-500 dark:focus:ring-blue-500 rounded-lg shadow-sm transition-colors" />
                    <x-input-error :messages="$errors->get('password')" class="mt-2 text-red-500" />
                </div>

                <div class="flex items-center justify-between mt-6">
                    <label for="remember_me" class="inline-flex items-center cursor-pointer">
                        <input id="remember_me" 
                               type="checkbox" 
                               name="remember"
                               class="rounded dark:bg-gray-900 border-gray-300 dark:border-gray-600 text-blue-600 shadow-sm focus:ring-blue-500 dark:focus:ring-blue-500 dark:focus:ring-offset-gray-800 transition-colors">
                        <span class="ms-2 text-sm text-gray-600 dark:text-gray-400 font-medium">Remember me</span>
                    </label>

                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}" class="text-sm font-medium text-blue-600 hover:text-blue-500 dark:text-blue-400 dark:hover:text-blue-300 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:focus:ring-offset-gray-800 transition-colors">
                            Forgot password?
                        </a>
                    @endif
                </div>

                <div class="mt-8">
                    <button type="submit" class="w-full flex justify-center items-center px-6 py-3 bg-blue-600 border border-transparent rounded-lg font-bold text-white uppercase tracking-wider hover:bg-blue-700 dark:bg-blue-700 dark:hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150 shadow-md transform hover:-translate-y-0.5">
                        Log In
                        <svg class="ml-2 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                        </svg>
                    </button>
                </div>
            </form>
        </div>
        
        <div class="mt-8 text-center">
            <a href="/" class="text-sm font-medium text-gray-500 hover:text-gray-800 dark:text-gray-500 dark:hover:text-gray-300 transition-colors inline-flex items-center">
                <svg class="mr-1 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Back to Welcome Page
            </a>
        </div>
    </div>
</body>
</html>
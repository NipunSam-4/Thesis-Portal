<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Verify Email | IIT Indore</title>
    <link rel="icon" type="image/png" href="{{ asset('images/iiti-logo.png') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="antialiased bg-gray-50 text-gray-900 dark:bg-gray-900 dark:text-white transition-colors duration-300">
    <div class="min-h-screen flex flex-col items-center justify-center px-4 relative overflow-hidden">
        
        <div class="absolute top-0 left-0 w-full h-64 bg-blue-900 dark:bg-blue-950 shadow-lg -z-10 sm:rounded-b-[100px]"></div>

        <div class="mb-8 text-center flex flex-col items-center">
            <img src="{{ asset('images/iiti-logo.png') }}" class="h-20 sm:h-24 w-auto mb-4 drop-shadow-xl bg-white/10 rounded-full p-2">
            <h2 class="text-2xl font-extrabold text-white">PhD Integrated Portal</h2>
            <p class="text-blue-100 font-medium">Indian Institute of Technology, Indore</p>
        </div>

        <div class="w-full sm:max-w-md bg-white dark:bg-gray-800 px-8 py-10 shadow-2xl rounded-2xl border border-gray-100 dark:border-gray-700">
            
            <p class="text-sm text-gray-600 dark:text-gray-400 mb-6 leading-relaxed">
                Thanks for signing up! Before getting started, could you verify your email address by clicking on the link we just emailed to you? If you didn't receive the email, we will gladly send you another.
            </p>

            @if (session('status') == 'verification-link-sent')
                <div class="mb-6 font-medium text-sm text-green-600 dark:text-green-400 bg-green-50 dark:bg-green-900/30 p-4 rounded-lg border border-green-200 dark:border-green-800">
                    A new verification link has been sent to the email address you provided during registration.
                </div>
            @endif

            <div class="mt-8 flex flex-col sm:flex-row items-center justify-between gap-4">
                
                <form method="POST" action="{{ route('verification.send') }}" class="w-full sm:w-auto">
                    @csrf
                    <button type="submit" class="w-full sm:w-auto px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-lg shadow-md transition transform hover:-translate-y-0.5 whitespace-nowrap">
                        Resend Verification Email
                    </button>
                </form>

                <form method="POST" action="{{ route('logout') }}" class="w-full sm:w-auto text-center">
                    @csrf
                    <button type="submit" class="text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white underline transition-colors focus:outline-none">
                        Log Out
                    </button>
                </form>
            </div>

        </div>
    </div>
</body>
</html>
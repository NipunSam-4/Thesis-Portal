<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>PhD Integrated Portal | IIT Indore</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="antialiased bg-gray-50 text-gray-900 dark:bg-gray-900 dark:text-white transition-colors duration-300 selection:bg-blue-500 selection:text-white">
    
    <div class="min-h-screen flex flex-col items-center justify-center relative overflow-hidden px-4 sm:px-6 lg:px-8">
        
        <div class="absolute top-0 left-0 w-full h-1/2 sm:h-96 bg-blue-900 dark:bg-blue-950 rounded-b-[100px] sm:rounded-b-[150px] shadow-2xl -z-10 transition-colors duration-300"></div>

        <div class="absolute top-8 left-1/2 transform -translate-x-1/2 flex flex-col items-center">
            <img src="{{ asset('images/iiti-logo.png') }}" 
                 alt="IIT Indore Logo" 
                 class="h-20 sm:h-24 md:h-28 w-auto object-contain drop-shadow-xl bg-white/10 dark:bg-transparent rounded-full p-2">
        </div>

        <div class="max-w-3xl w-full text-center mt-32 sm:mt-40 md:mt-32">
            
            <h1 class="text-3xl sm:text-4xl md:text-5xl font-extrabold tracking-tight text-white mb-2 sm:mb-4 drop-shadow-md">
                PhD Integrated Portal
            </h1>
            <h2 class="text-base sm:text-lg md:text-xl font-medium text-blue-100 mb-8 sm:mb-10 drop-shadow">
                Indian Institute of Technology Indore
            </h2>
            
            <div class="bg-white dark:bg-gray-800 p-6 sm:p-10 rounded-2xl shadow-xl border border-gray-100 dark:border-gray-700 transition-colors duration-300">
                <p class="text-base sm:text-lg text-gray-600 dark:text-gray-300 mb-8 leading-relaxed">
                    A centralized, secure platform for managing doctoral workflows, tracking thesis submissions, and coordinating faculty evaluations.
                </p>

                <div class="flex flex-col sm:flex-row justify-center items-center gap-4">
                    
                    @auth
                        <a href="{{ url('/dashboard') }}" class="w-full sm:w-auto px-8 py-3 bg-blue-600 hover:bg-blue-700 dark:bg-blue-700 dark:hover:bg-blue-600 text-white font-semibold rounded-lg shadow-md transition transform hover:-translate-y-0.5 text-center">
                            Return to Dashboard
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="w-full sm:w-auto px-8 py-3 bg-blue-600 hover:bg-blue-700 dark:bg-blue-700 dark:hover:bg-blue-600 text-white font-semibold rounded-lg shadow-md transition transform hover:-translate-y-0.5 text-center">
                            Log In to Portal
                        </a>
                        
                        <a href="mailto:admin@iiti.ac.in" class="w-full sm:w-auto px-8 py-3 bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 font-semibold rounded-lg shadow-sm border border-gray-200 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700 transition transform hover:-translate-y-0.5 text-center">
                            Contact IT Support
                        </a>
                    @endauth

                </div>
            </div>

        </div>

        <div class="absolute bottom-6 text-gray-500 dark:text-gray-500 text-xs sm:text-sm text-center w-full px-4">
            &copy; {{ date('Y') }} Indian Institute of Technology Indore. All rights reserved.
        </div>
    </div>

</body>
</html>
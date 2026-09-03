<!DOCTYPE html>
<html lang="en" class="antialiased">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title> Thesis Management Portal | IIT Indore</title>
    <link rel="icon" href="{{ asset('favicon.png') }}">

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:300,400,500,600,700,800&display=swap" rel="stylesheet" />

    <style>[x-cloak] { display: none !important; }</style>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans text-slate-800 dark:text-slate-200 bg-slate-50 dark:bg-slate-950 min-h-screen flex flex-col relative overflow-x-hidden transition-colors duration-300">
    
    <div class="fixed inset-0 z-0 pointer-events-none">
        <div class="absolute inset-0 opacity-[0.04] dark:opacity-[0.03] bg-[linear-gradient(to_right,#80808012_1px,transparent_1px),linear-gradient(to_bottom,#80808012_1px,transparent_1px)] bg-[size:24px_24px]"></div>
        
        <div class="absolute -top-32 -left-32 w-[40rem] h-[40rem] bg-blue-400/20 dark:bg-blue-500/20 rounded-full blur-[120px]"></div>
        <div class="absolute -bottom-32 -right-32 w-[40rem] h-[40rem] bg-indigo-400/20 dark:bg-indigo-500/20 rounded-full blur-[120px]"></div>
    </div>

    <header class="z-50 w-full bg-white/80 dark:bg-slate-900/80 backdrop-blur-md border-b border-slate-200 dark:border-slate-800 sticky top-0">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="relative flex items-center justify-center h-20 sm:h-24">
                
                <div class="flex-shrink-0">
                    <img src="{{ asset('images/iiti_logo.png') }}" alt="IIT Indore" class="h-12 sm:h-16 w-auto bg-white rounded-md p-1 border border-slate-200 dark:border-slate-700 shadow-sm transition-transform hover:scale-105 duration-300" />
                </div>

                <div class="flex flex-col items-center text-center px-6 sm:px-6">
                    <h1 class="text-sm sm:text-2xl font-bold text-slate-900 dark:text-white leading-tight transition-colors">
                        Indian Institute of Technology Indore
                    </h1>
                    <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 font-medium mt-1 transition-colors">
                        <span class="text-blue-700 dark:text-blue-400">Thesis Management Portal</span>
                    </p>
                </div>

            </div>
        </div>
    </header>

    <main class="relative z-10 flex-1 flex flex-col justify-center items-center py-12 px-4 sm:px-6">
        
        <div class="w-full sm:max-w-md mb-6 pl-2">
            <a href="/" class="group inline-flex items-center gap-2 text-sm font-medium text-slate-500 hover:text-blue-600 dark:text-slate-400 dark:hover:text-blue-400 transition-colors">
                <svg class="w-4 h-4 transform group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                Back to Home
            </a>
        </div>

        <div class="w-full sm:max-w-md px-8 py-8 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-xl rounded-2xl transition-all duration-300">
            {{ $slot }}
        </div>
        
    </main>

</body>
</html>
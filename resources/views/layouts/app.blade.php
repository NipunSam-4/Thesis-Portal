<!DOCTYPE html>
<html lang="en" class="antialiased">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Thesis Management Portal | IIT Indore</title>
    <link rel="icon" href="{{ asset('favicon.png') }}">

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css"
        integrity="sha512-2SwdPD6INVrV/lHTZbO2nodKhrnDdJK9/kg2XD1r9uGqPo1cUbujc+IYdlYdEErWNu69gVcYgdxlmVmzTWnetw=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans text-slate-800 dark:text-slate-200 bg-slate-50 dark:bg-slate-950 min-h-screen relative transition-colors duration-300">
    
    <div class="fixed inset-0 z-0 pointer-events-none">
        <div class="absolute inset-0 opacity-[0.04] dark:opacity-[0.03] bg-[linear-gradient(to_right,#80808012_1px,transparent_1px),linear-gradient(to_bottom,#80808012_1px,transparent_1px)] bg-[size:24px_24px]"></div>
        
        <div class="absolute -top-32 -left-32 w-[40rem] h-[40rem] bg-blue-400/20 dark:bg-blue-500/20 rounded-full blur-[120px]"></div>
        <div class="absolute bottom-0 right-0 translate-x-1/3 translate-y-1/3 w-[40rem] h-[40rem] bg-indigo-400/20 dark:bg-indigo-500/20 rounded-full blur-[120px]"></div>
    </div>

    <div class="relative z-10 min-h-screen flex flex-col">
        
        {{-- @include('layouts.navigation') --}}

        <header class="bg-white/80 dark:bg-slate-900/80 backdrop-blur-md shadow-sm border-b border-slate-200 dark:border-slate-800 z-30 transition-colors duration-300">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

                <div class="flex items-center justify-between min-h-[96px]">

                    <div class="flex items-center">
                        <a href="{{ url('/') }}">
                            <img src="{{ asset('images/iiti_logo.png') }}" alt="IIT Indore"
                                class="h-12 sm:h-14 lg:h-16 w-auto object-contain bg-white dark:bg-white rounded-xl p-1.5 shadow-sm border border-slate-200 dark:border-slate-700 transition-transform hover:scale-105 duration-300" />
                        </a>
                    </div>

                    <div class="flex-1 text-center px-4">
                        @isset($header)
                            <div class="text-xl font-bold text-slate-900 dark:text-white leading-tight">
                                {{ $header }}
                            </div>
                        @endisset
                    </div>
                </div>
            </div>
        </header>

        <main class="flex-1">
            {{ $slot }}
        </main>
        
    </div>
</body>

</html>
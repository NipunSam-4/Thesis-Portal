<!DOCTYPE html>
<html lang="en" class="antialiased">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Thesis Management Portal | IIT Indore</title>
    <link rel="icon" href="{{ asset('favicon.png') }}">

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:300,400,500,600,700,800&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans text-slate-800 dark:text-slate-200 bg-slate-50 dark:bg-slate-950 h-screen overflow-hidden transition-colors duration-300">

    <div class="fixed inset-0 z-0 pointer-events-none">
        <div class="absolute inset-0 opacity-[0.04] dark:opacity-[0.03] bg-[linear-gradient(to_right,#80808012_1px,transparent_1px),linear-gradient(to_bottom,#80808012_1px,transparent_1px)] bg-[size:24px_24px]"></div>
        
        <div class="absolute -top-32 -left-32 w-[40rem] h-[40rem] bg-blue-400/20 dark:bg-blue-500/20 rounded-full blur-[120px]"></div>
        <div class="absolute -bottom-32 -right-32 w-[40rem] h-[40rem] bg-indigo-400/20 dark:bg-indigo-500/20 rounded-full blur-[120px]"></div>
    </div>

    <div class="relative z-10 flex h-full w-full">
        
        <div class="hidden lg:flex relative w-1/2 border-r border-slate-200 dark:border-slate-800 flex-col justify-between p-12 transition-colors duration-300">
            
            <div class="relative z-10 flex items-center gap-4">
                <img src="{{ asset('images/iiti_logo.png') }}" alt="IIT Indore" class="h-20 w-auto bg-white dark:bg-white rounded-xl p-2 shadow-sm border border-slate-200 dark:border-slate-700" />
                <div>
                    <h2 class="text-xl font-bold text-slate-900 dark:text-white tracking-tight transition-colors">भारतीय प्रौद्योगिकी संस्थान इंदौर</h2>
                    <p class="text-slate-600 dark:text-slate-400 text-lg font-medium transition-colors">Indian Institute of Technology Indore</p>
                    <p class="text-blue-600 dark:text-slate-400 text-sm font-medium transition-colors">Thesis Management Portal</p>
                </div>
            </div>

            <div class="relative z-10 max-w-md">
                <h1 class="text-4xl md:text-5xl font-bold text-slate-900 dark:text-white leading-tight mb-6 transition-colors">
                    Streamline your <span class="text-blue-600 dark:text-blue-400">research journey.</span>
                </h1>
                <p class="text-lg text-slate-600 dark:text-slate-300 mb-8 leading-relaxed transition-colors">
                    Manage thesis submissions, track review status, and streamline the review and approval process.
                </p>
                <div class="flex items-center gap-4">
                    <a href="https://academic.iiti.ac.in/phdforms.php" target="_blank" class="inline-flex items-center gap-2 text-sm font-semibold text-slate-800 dark:text-white bg-white dark:bg-white/10 hover:bg-slate-50 dark:hover:bg-white/20 px-6 py-3 rounded-full shadow-sm backdrop-blur-md transition-all border border-slate-200 dark:border-white/10">
                        <svg class="w-5 h-5 text-blue-600 dark:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        More Information 
                    </a>
                </div>
            </div>

            <div class="relative z-10 text-sm text-slate-500">
                {{-- Footer Info --}}
            </div>
        </div>

        <div class="w-full lg:w-1/2 flex flex-col overflow-y-auto">
            
            <div class="lg:hidden flex items-center gap-4 p-6 border-b border-slate-200 dark:border-slate-800 bg-white/70 dark:bg-slate-900/70 backdrop-blur-md shadow-sm">
                <img src="{{ asset('images/iiti_logo.png') }}" alt="IIT Indore" class="h-14 w-auto bg-white rounded-md p-0.5" />
                <div>
                    <h1 class="text-sm font-bold text-slate-900 dark:text-white leading-tight">भारतीय प्रौद्योगिकी संस्थान इंदौर</h1>
                    <p class="text-sm text-slate-600 dark:text-blue-400 font-medium">Indian Institute of Technology Indore</p>
                    <p class="text-xs text-blue-600 dark:text-blue-400 font-medium">Thesis Management Portal</p>
                </div>
            </div>

            <div class="flex-1 flex flex-col justify-center p-8 sm:p-16 lg:p-24 max-w-2xl mx-auto w-full">
                
                <div class="mb-6 lg:mb-8">
                    <h2 class="text-3xl font-bold text-slate-900 dark:text-white mb-3">Portal Access</h2>
                </div>

                @if (Route::has('login'))
                    <div class="space-y-6">
                        @auth
                            <div class="group relative bg-white dark:bg-slate-900 rounded-2xl p-6 sm:p-8 shadow-md hover:shadow-xl border border-slate-200 dark:border-slate-800 transition-all duration-300">
                                {{-- <div class="flex items-start justify-between mb-6">
                                    <div>
                                        <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-blue-50 dark:bg-blue-500/10 text-blue-600 dark:text-blue-400 mb-4">
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                        </div>
                                        <h3 class="text-xl font-bold text-slate-900 dark:text-white">Welcome Back</h3>
                                        <p class="text-slate-600 dark:text-slate-400 text-sm mt-1">You are currently logged into the portal.</p>
                                    </div>
                                </div> --}}
                                <div>
                                    <div class="flex items-center gap-3 mb-4">
                                        <div class="w-10 h-10 bg-slate-50 dark:bg-slate-800 border border-slate-100 dark:border-slate-700 rounded-lg flex items-center justify-center text-slate-600 dark:text-slate-300 group-hover:bg-blue-50 group-hover:border-blue-100 group-hover:text-blue-600 dark:group-hover:bg-blue-500/10 dark:group-hover:border-blue-500/30 dark:group-hover:text-blue-400 transition-colors">
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                        </div>
                                        <h3 class="text-xl font-bold text-slate-900 dark:text-white">Welcome Back</h3>
                                    </div>
                                    <p class="text-sm text-slate-600 dark:text-slate-400 mb-6">You are currently logged into the portal.</p>
                                </div>
                                
                                <a href="{{ url('/dashboard') }}" class="flex items-center justify-center w-full px-6 py-3.5 text-sm font-semibold text-white bg-blue-600 hover:bg-blue-700 rounded-xl transition duration-200 shadow-sm">
                                    Go to Applicant Dashboard &rarr;
                                </a>
                            </div>
                        @else
                            <div class="grid grid-cols-1 sm:grid-cols-1">
                                
                                <a href="{{ route('login') }}" class="group flex flex-col justify-between bg-white dark:bg-slate-900 rounded-2xl p-6 border border-slate-200 dark:border-slate-800 shadow-md hover:shadow-xl  hover:border-blue-500 dark:hover:border-blue-500 hover:-translate-y-1 transition-all duration-300">
                                    <div>
                                        <div class="flex items-center gap-3 mb-4">
                                            <div class="w-10 h-10 bg-slate-50 dark:bg-slate-800 border border-slate-100 dark:border-slate-700 rounded-lg flex items-center justify-center text-slate-600 dark:text-slate-300 group-hover:bg-blue-50 group-hover:border-blue-100 group-hover:text-blue-600 dark:group-hover:bg-blue-500/10 dark:group-hover:border-blue-500/30 dark:group-hover:text-blue-400 transition-colors">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path></svg>
                                            </div>
                                            <h3 class="text-xl font-bold text-slate-900 dark:text-white">Institute Login</h3>
                                        </div>
                                        <p class="text-sm text-slate-600 dark:text-slate-400 mb-6">Log in to manage your submissions or continue drafting.</p>
                                    </div>
                                    <span class="text-sm font-semibold text-blue-600 dark:text-blue-400 flex items-center gap-1 group-hover:gap-2 transition-all">
                                        Log In to Portal<span aria-hidden="true">&rarr;</span>
                                    </span>
                                </a>

                            </div>
                        @endauth
                    </div>
                @endif

                <div class="mt-6 pt-4 border-t border-slate-200 dark:border-slate-800 flex flex-col gap-4">
                    
                    <div class="bg-white dark:bg-slate-800/50 rounded-xl p-4 flex flex-col sm:flex-row items-center justify-between gap-4 shadow-sm border border-slate-200 dark:border-slate-700">
                        <div class="flex items-center gap-3">
                            <div class="p-2 bg-slate-50 dark:bg-slate-700 border border-slate-100 dark:border-slate-600 rounded-lg shadow-sm text-slate-600 dark:text-slate-300">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                            </div>
                            <div class="text-left">
                                <p class="text-sm font-semibold text-slate-900 dark:text-white">Need help applying?</p>
                                <p class="text-xs text-slate-600 dark:text-slate-400">Download the step-by-step manual.</p>
                            </div>
                        </div>
                        <a href="{{ asset('uploads/Instructions.pdf') }}" target="_blank" class="w-full sm:w-auto px-4 py-2 text-sm font-semibold text-slate-700 dark:text-slate-200 bg-slate-50 dark:bg-slate-700 border border-slate-200 dark:border-slate-600 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-600 transition-colors text-center shadow-sm">
                            Get Manual
                        </a>
                    </div>
                    
                    <div class="lg:hidden flex items-center gap-3 justify-center mt-4">
                        <a href="https://academic.iiti.ac.in/phdforms.php" target="_blank" class="inline-flex items-center gap-2 text-sm font-semibold text-slate-800 dark:text-white bg-white dark:bg-white/10 hover:bg-slate-50 dark:hover:bg-white/20 px-6 py-3 rounded-full shadow-sm backdrop-blur-md transition-all border border-slate-200 dark:border-white/10">
                            <svg class="w-5 h-5 text-blue-600 dark:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            More Information 
                        </a>
                    </div>
                </div>

            </div>
        </div>
    </div>

</body>
</html>
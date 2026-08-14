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

    @php
        $authUser = Auth::user() ?? Auth::guard('admin')->user();
        $userRoleLabel = 'User';
        if ($authUser) {
            if (method_exists($authUser, 'isStudent') && $authUser->isStudent()) {
                $studentModel = $authUser->student;
                $userRoleLabel = ($studentModel && method_exists($studentModel, 'isPhd') && $studentModel->isPhd()) ? 'PhD Student' : (($studentModel && method_exists($studentModel, 'isMsR') && $studentModel->isMsR()) ? 'MS(R) Student' : 'Student');
            } elseif (method_exists($authUser, 'isFaculty') && $authUser->isFaculty()) {
                $userRoleLabel = 'Faculty Member';
            } elseif ($authUser->role === 'dpgc') {
                $userRoleLabel = 'DPGC';
            } elseif ($authUser->role === 'hod') {
                $userRoleLabel = 'Head of Department (HOD)';
            } elseif ($authUser->role === 'doaa') {
                $userRoleLabel = 'Dean of Academic Affairs (DOAA)';
            } elseif ($authUser->role === 'adoaa') {
                $userRoleLabel = 'Associate Dean (ADoAA)';
            } elseif ($authUser->role === 'senate_chairperson') {
                $userRoleLabel = 'Senate Chairperson';
            } elseif ($authUser->role === 'ar') {
                $userRoleLabel = 'Assistant Registrar (Academic)';
            } elseif ($authUser->role === 'dr') {
                $userRoleLabel = 'Deputy Registrar (Academic)';
            } elseif ($authUser->role === 'section_officer') {
                $userRoleLabel = 'Section Officer';
            } elseif (isset($authUser->role)) {
                $userRoleLabel = ucfirst(str_replace('_', ' ', $authUser->role));
            } else {
                $userRoleLabel = 'Administrator';
            }
        }
    @endphp

    <div class="relative z-10 min-h-screen flex flex-col" x-data="{ mobileMenuOpen: false }">
        
        <header class="bg-white/90 dark:bg-slate-900/90 backdrop-blur-md shadow-sm border-b border-slate-200 dark:border-slate-800 z-40 sticky top-0 transition-colors duration-300">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

                <!-- Navigation Bar Header Content -->
                <div class="flex items-center justify-between min-h-[72px] md:min-h-[96px] py-2">

                    <!-- Mobile & Desktop Left: Hamburger Button (Mobile) + Icon + Thesis Management Portal Title ONLY -->
                    <div class="flex items-center space-x-3">
                        <!-- Mobile Left Hamburger Icon Button (Before Icon) -->
                        <button @click="mobileMenuOpen = !mobileMenuOpen" 
                                type="button" 
                                class="md:hidden p-2 rounded-xl text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 border border-slate-200 dark:border-slate-700 focus:outline-none transition-colors shadow-sm"
                                aria-label="Open Navigation Menu">
                            <!-- Hamburger Icon -->
                            <svg x-show="!mobileMenuOpen" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                            </svg>
                            <!-- Close Icon -->
                            <svg x-show="mobileMenuOpen" x-cloak class="w-6 h-6 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>

                        <a href="{{ url('/') }}" class="flex items-center space-x-2.5 sm:space-x-3 group">
                            <img src="{{ asset('images/iiti_logo.png') }}" alt="IIT Indore"
                                class="h-10 sm:h-14 lg:h-16 w-auto object-contain bg-white dark:bg-white rounded-xl p-1 sm:p-1.5 shadow-sm border border-slate-200 dark:border-slate-700 transition-transform group-hover:scale-105 duration-300 shrink-0" />
                            <div class="flex flex-col">
                                <span class="text-base sm:text-xl font-bold text-slate-900 dark:text-white leading-tight">
                                    Thesis Management Portal
                                </span>
                                <span class="text-[10px] sm:text-xs text-slate-500 dark:text-slate-400 font-medium hidden sm:block">
                                    Indian Institute of Technology Indore
                                </span>
                            </div>
                        </a>
                    </div>

                    <!-- Desktop Right: Header Slot Content (Hidden on Mobile) -->
                    <div class="hidden md:flex flex-1 items-center justify-end space-x-4 pl-4">
                        @isset($header)
                            <div class="flex-1">
                                {{ $header }}
                            </div>
                        @endisset
                    </div>

                </div>
            </div>
        </header>

        <!-- FULL-SCREEN MOBILE SIDEBAR DRAWER (Root Viewport Level - Full Vertical 100vh Screen Space) -->
        <div x-show="mobileMenuOpen" 
             x-cloak 
             @keydown.escape.window="mobileMenuOpen = false"
             class="fixed inset-0 z-50 md:hidden" 
             role="dialog" 
             aria-modal="true">
            
            <!-- Backdrop Overlay -->
            <div x-show="mobileMenuOpen" 
                 x-transition:enter="transition-opacity ease-linear duration-200"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="transition-opacity ease-linear duration-200"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 @click="mobileMenuOpen = false"
                 class="fixed inset-0 bg-slate-900/70 backdrop-blur-xs"></div>

            <!-- Sidebar Panel Container (Full 100vh Screen Height, Slide from Left) -->
            <div class="fixed inset-y-0 left-0 max-w-xs w-full h-full min-h-screen bg-white dark:bg-slate-900 shadow-2xl border-r border-slate-200 dark:border-slate-800 flex flex-col justify-between overflow-y-auto z-50"
                 x-show="mobileMenuOpen"
                 x-transition:enter="transition ease-in-out duration-300 transform"
                 x-transition:enter-start="-translate-x-full"
                 x-transition:enter-end="translate-x-0"
                 x-transition:leave="transition ease-in-out duration-300 transform"
                 x-transition:leave-start="translate-x-0"
                 x-transition:leave-end="-translate-x-full">
                
                <div class="p-5 space-y-5 flex-1 flex flex-col">
                    <!-- Top Header of Sidebar: Title + Close Button -->
                    <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-3">
                        <span class="text-xs font-extrabold uppercase tracking-wider text-slate-700 dark:text-white">Navigation Menu</span>
                        <button @click="mobileMenuOpen = false" class="p-1.5 rounded-lg text-slate-400 hover:text-slate-600 dark:hover:text-slate-200">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>

                    <!-- 1. USER NAME & EMAIL (TOP OF SIDEBAR) -->
                    @if($authUser)
                        <div class="flex items-center space-x-3 bg-slate-50 dark:bg-slate-800/60 p-3 rounded-xl border border-slate-100 dark:border-slate-800">
                            <div class="w-10 h-10 rounded-xl bg-blue-600 text-white flex items-center justify-center font-bold text-base shadow-sm shrink-0">
                                {{ strtoupper(substr($authUser->name ?? 'U', 0, 1)) }}
                            </div>
                            <div class="overflow-hidden">
                                <div class="font-bold text-sm text-slate-900 dark:text-white leading-tight truncate">
                                    {{ $authUser->name }}
                                </div>
                                <div class="text-xs text-slate-500 dark:text-slate-400 truncate">
                                    {{ $authUser->email }}
                                </div>
                            </div>
                        </div>

                        <!-- 2. ASSIGNED ROLE / TRANSFER ROLE (TOP OF SIDEBAR BELOW NAME) -->
                        <div class="space-y-1.5">
                            <label class="text-[11px] font-bold uppercase tracking-wider text-slate-700 dark:text-white block">Assigned Role</label>
                            <div class="p-3 bg-blue-50/80 dark:bg-blue-950/40 border border-blue-200 dark:border-blue-800/60 rounded-xl flex items-center justify-between">
                                <span class="text-xs font-bold text-blue-900 dark:text-blue-200 flex items-center gap-1.5">
                                    <span class="w-2 h-2 rounded-full bg-blue-500"></span>
                                    {{ $userRoleLabel }}
                                </span>
                            </div>
                        </div>

                        <!-- 3. DASHBOARD & PROFILE LINKS (ALWAYS VISIBLE & NON-COLLAPSIBLE IN SIDEBAR) -->
                        <div class="space-y-2 pt-3 border-t border-slate-100 dark:border-slate-800 flex-1">
                            <!-- Dashboard Link -->
                            <a href="{{ route('dashboard') }}" 
                               class="flex items-center space-x-3 px-3.5 py-2.5 rounded-xl text-sm font-semibold {{ request()->routeIs('dashboard') ? 'text-blue-600 dark:text-blue-400 bg-blue-50 dark:bg-blue-950/40 border border-blue-200 dark:border-blue-800/60' : 'text-slate-700 dark:text-slate-200 bg-slate-100/70 dark:bg-slate-800/50 hover:bg-slate-200/70 dark:hover:bg-slate-800' }} transition">
                                <svg class="w-4 h-4 {{ request()->routeIs('dashboard') ? 'text-blue-600 dark:text-blue-400' : 'text-slate-500 dark:text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path>
                                </svg>
                                <span>Dashboard</span>
                            </a>

                            <!-- Profile Settings Link -->
                            <a href="{{ route('profile.edit') }}" 
                               class="flex items-center space-x-3 px-3.5 py-2.5 rounded-xl text-sm font-semibold {{ request()->routeIs('profile.edit') ? 'text-blue-600 dark:text-blue-400 bg-blue-50 dark:bg-blue-950/40 border border-blue-200 dark:border-blue-800/60' : 'text-slate-700 dark:text-slate-200 bg-slate-100/70 dark:bg-slate-800/50 hover:bg-slate-200/70 dark:hover:bg-slate-800' }} transition">
                                <svg class="w-4 h-4 {{ request()->routeIs('profile.edit') ? 'text-blue-600 dark:text-blue-400' : 'text-slate-500 dark:text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                                <span>Profile Settings</span>
                            </a>
                        </div>
                    @endif
                </div>

                <!-- Footer of Mobile Sidebar Drawer: Always-Visible Log Out Button -->
                <div class="p-4 border-t border-slate-100 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-900/50">
                    @if($authUser)
                        <!-- Log Out Form & Button -->
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" 
                                    class="w-full flex items-center justify-center space-x-3 px-3.5 py-2.5 rounded-xl text-sm font-semibold text-red-600 dark:text-red-400 bg-red-50 dark:bg-red-950/40 hover:bg-red-100 dark:hover:bg-red-900/40 border border-red-200 dark:border-red-800/50 transition shadow-sm">
                                <svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                                </svg>
                                <span>Log Out</span>
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>

        <main class="flex-1">
            {{ $slot }}
        </main>
        
    </div>
</body>

</html>
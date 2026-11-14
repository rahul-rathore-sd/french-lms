<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Academic Dashboard · French Learning Management System</title>
    <script src="https://www.gstatic.com/antigravity/web/dev/tailwindcss.min.js"></script>
    <script>
        // Enable Tailwind class-based dark mode
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        darkbg: '#0b0f19',
                        darkcard: '#111827',
                        darkborder: '#1f293d',
                        darkinput: '#1e293b',
                    }
                }
            }
        };

        // Initialize Dark Mode before render to avoid flash
        if (localStorage.getItem('theme') === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    </script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@600;700;800&family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Playfair+Display:ital,wght@0,500;0,600;0,700;1,400&display=swap" rel="stylesheet">
    
    <style>
        body { font-family: 'Plus Jakarta Sans', system-ui, sans-serif; }
        .font-royal { font-family: 'Cinzel', serif; }
        .font-serif { font-family: 'Playfair Display', Georgia, serif; }

        /* Card styling */
        .fi-card {
            border-radius: 1rem;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        }

        /* Light Mode */
        html:not(.dark) body { background-color: #f8fafc; color: #0f172a; }
        html:not(.dark) .fi-card {
            background-color: #ffffff;
            border: 1px solid rgba(226, 232, 240, 0.9);
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.04);
        }
        html:not(.dark) .fi-card:hover {
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05);
        }

        /* Dark Mode */
        html.dark body { background-color: #0b0f19; color: #f8fafc; }
        html.dark .fi-card {
            background-color: #111827;
            border: 1px solid #1f293d;
            box-shadow: 0 4px 20px -2px rgba(0, 0, 0, 0.35);
        }
        html.dark .fi-card:hover {
            border-color: #374151;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.5);
        }

        /* Canadian Red Accent */
        .brand-red-bg { background-color: #d52b1e; }
        .brand-red-text { color: #d52b1e; }
        .brand-red-border { border-color: #d52b1e; }

        /* Custom Scrollbar */
        ::-webkit-scrollbar { width: 5px; height: 5px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #94a3b8; border-radius: 9999px; }
        html.dark ::-webkit-scrollbar-thumb { background: #374151; }
    </style>
</head>
<body class="h-full flex overflow-hidden">

    <!-- ========================================== -->
    <!-- 1. SIDEBAR                                 -->
    <!-- ========================================== -->
    <aside id="sidebar" class="w-72 flex-shrink-0 bg-white dark:bg-[#0e1424] border-r border-slate-200/80 dark:border-slate-800 flex flex-col z-30 transition-all duration-300">
        
        <!-- Sidebar Brand Header with Real Flag of Canada -->
        <div class="h-16 flex items-center justify-between px-5 border-b border-slate-200/70 dark:border-slate-800">
            <a href="{{ route('dashboard') }}" class="flex items-center gap-3">
                
                <!-- Real Canada Flag Badge (Official 2:1 Pantone Flag of Canada) -->
                <div class="w-12 h-6 rounded-md overflow-hidden shadow-xs border border-slate-300/80 dark:border-slate-700 ring-1 ring-black/5 flex-shrink-0 hover:scale-105 transition" title="National Flag of Canada · Bilingual Academy">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 9600 4800" class="w-full h-full block">
                        <path fill="#d52b1e" d="m0 0h2400l99 99h4602l99-99h2400v4800h-2400l-99-99h-4602l-99 99H0z"/>
                        <path fill="#fff" d="m2400 0h4800v4800h-4800zm2490 4430-45-863a95 95 0 0 1 111-98l859 151-116-320a65 65 0 0 1 20-73l941-762-212-99a65 65 0 0 1-34-79l186-572-542 115a65 65 0 0 1-73-38l-105-247-423 454a65 65 0 0 1-111-57l204-1052-327 189a65 65 0 0 1-91-27l-332-652-332 652a65 65 0 0 1-91 27l-327-189 204 1052a65 65 0 0 1-111 57l-423-454-105 247a65 65 0 0 1-73 38l-542-115 186 572a65 65 0 0 1-34 79l-212 99 941 762a65 65 0 0 1 20 73l-116 320 859-151a95 95 0 0 1 111 98l-45 863z"/>
                    </svg>
                </div>

                <div>
                    <div class="flex items-center gap-1.5">
                        <span class="font-bold text-sm text-slate-900 dark:text-white tracking-tight">French Academy LMS</span>
                    </div>
                    <div class="flex items-center gap-1.5 mt-0.5">
                        <span class="text-[10px] font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">CEFR A1–C2</span>
                        <span class="text-[9px] font-bold px-1.5 py-0.2 rounded-full bg-red-50 dark:bg-red-950/60 text-red-600 dark:text-red-400 border border-red-200 dark:border-red-900">Official</span>
                    </div>
                </div>
            </a>
            <button onclick="toggleSidebar()" class="lg:hidden text-slate-400 hover:text-slate-600 p-1">
                ✕
            </button>
        </div>

        <!-- Sidebar Navigation Groups -->
        <div class="flex-1 overflow-y-auto px-3 py-3 space-y-6">

            <!-- Admin Console Shortcut -->
            <div class="px-1">
                <a href="{{ route('admin.index') }}" 
                   class="w-full flex items-center justify-between px-3.5 py-2.5 rounded-xl text-xs font-bold text-red-700 dark:text-red-300 bg-red-50 hover:bg-red-100 dark:bg-red-950/40 dark:hover:bg-red-900/50 border border-red-200 dark:border-red-900/60 transition shadow-2xs">
                    <div class="flex items-center gap-2.5">
                        <span class="text-sm">👑</span>
                        <span>Admin Console</span>
                    </div>
                    <span class="text-[9px] uppercase tracking-wider font-extrabold px-1.5 py-0.5 rounded bg-red-600 text-white">Manage</span>
                </a>
            </div>

            <!-- Group 1: Core Navigation -->
            <div>
                <div class="px-3 mb-2 text-[10px] font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500 font-mono">
                    Core Dashboard
                </div>
                <div class="space-y-1">
                    <button onclick="switchTab('overview')" id="nav-overview" class="nav-btn w-full flex items-center justify-between px-3 py-2 rounded-xl text-xs font-semibold transition bg-red-50 dark:bg-red-950/40 text-red-700 dark:text-red-400">
                        <div class="flex items-center gap-2.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                            <span>Academic Dashboard</span>
                        </div>
                        <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-white dark:bg-slate-800 shadow-2xs border border-slate-200 dark:border-slate-700">Live</span>
                    </button>
                </div>
            </div>

            <!-- Group 2: Academic Curriculum -->
            <div>
                <div class="px-3 mb-2 text-[10px] font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500 font-mono">
                    Academic LMS
                </div>
                <div class="space-y-1">
                    <button onclick="switchTab('courses')" id="nav-courses" class="nav-btn w-full flex items-center justify-between px-3 py-2 rounded-xl text-xs font-semibold text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 transition">
                        <div class="flex items-center gap-2.5">
                            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                            <span>Course Catalog</span>
                        </div>
                        <span class="text-[10px] font-mono px-2 py-0.5 rounded bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 font-semibold">{{ $allCourses->count() }}</span>
                    </button>

                    <button onclick="switchTab('my-curriculum')" id="nav-my-curriculum" class="nav-btn w-full flex items-center justify-between px-3 py-2 rounded-xl text-xs font-semibold text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 transition">
                        <div class="flex items-center gap-2.5">
                            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                            <span>My Enrolled Syllabus</span>
                        </div>
                        <span class="text-[10px] font-mono px-2 py-0.5 rounded bg-red-50 dark:bg-red-950/60 text-red-700 dark:text-red-400 font-bold border border-red-200 dark:border-red-900">{{ $enrolledCount }}</span>
                    </button>

                    <button onclick="switchTab('scholars')" id="nav-scholars" class="nav-btn w-full flex items-center justify-between px-3 py-2 rounded-xl text-xs font-semibold text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 transition">
                        <div class="flex items-center gap-2.5">
                            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                            <span>Academic Directory</span>
                        </div>
                        <span class="text-[10px] font-mono px-2 py-0.5 rounded bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 font-semibold">{{ $allScholars->count() }}</span>
                    </button>
                </div>
            </div>

            <!-- Group 3: Classical Immersion -->
            <div>
                <div class="px-3 mb-2 text-[10px] font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500 font-mono">
                    Classical Immersion
                </div>
                <div class="space-y-1">
                    <button onclick="switchTab('library')" id="nav-library" class="nav-btn w-full flex items-center justify-between px-3 py-2 rounded-xl text-xs font-semibold text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 transition">
                        <div class="flex items-center gap-2.5">
                            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                            <span>Classical Library</span>
                        </div>
                        <span class="text-[10px] font-mono px-2 py-0.5 rounded bg-red-50 dark:bg-red-950/60 text-red-700 dark:text-red-400 font-bold border border-red-200 dark:border-red-900">5 Books</span>
                    </button>

                    <!-- Enhanced Vocabulary Section under Classical Immersion -->
                    <button onclick="switchTab('dictionary')" id="nav-dictionary" class="nav-btn w-full flex items-center justify-between px-3 py-2 rounded-xl text-xs font-semibold text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 transition">
                        <div class="flex items-center gap-2.5">
                            <span>📖</span>
                            <span>Vocabulary Sanctuary</span>
                        </div>
                        <span class="text-[10px] font-mono px-2 py-0.5 rounded bg-red-100 dark:bg-red-950/60 text-red-700 dark:text-red-400 font-bold border border-red-200 dark:border-red-900" id="dict-count-badge">{{ $allVocabulary->count() }} Words</span>
                    </button>

                    <button onclick="switchTab('phrases')" id="nav-phrases" class="nav-btn w-full flex items-center justify-between px-3 py-2 rounded-xl text-xs font-semibold text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 transition">
                        <div class="flex items-center gap-2.5">
                            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                            <span>Daily Expressions</span>
                        </div>
                        <span class="text-[10px] font-mono px-2 py-0.5 rounded bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 font-semibold">100+</span>
                    </button>
                </div>
            </div>

            <!-- Group 4: Dark Mode Toggle Widget -->
            <div class="p-3.5 rounded-2xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700/80">
                <div class="flex items-center justify-between mb-1.5">
                    <span class="text-[11px] font-bold text-slate-800 dark:text-slate-200">Theme Mode</span>
                    <span class="text-[10px] font-mono font-bold text-slate-500 dark:text-slate-400 uppercase" id="sidebar-theme-status">Light</span>
                </div>
                <p class="text-[11px] text-slate-500 dark:text-slate-400 leading-tight mb-2.5">
                    Switch between Clean Light and Obsidian Dark mode:
                </p>
                <button onclick="toggleDarkMode()" class="w-full py-2 px-3 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-700/80 hover:bg-slate-100 dark:hover:bg-slate-700 text-slate-800 dark:text-slate-100 text-xs font-semibold transition flex items-center justify-center gap-2 shadow-2xs">
                    <span id="sidebar-theme-icon">🌙</span>
                    <span id="sidebar-theme-btn-text">Toggle Dark Mode</span>
                </button>
            </div>

        </div>

        <!-- Sidebar User Profile Footer Card -->
        <div class="p-3 border-t border-slate-200/80 dark:border-slate-800 bg-slate-50/60 dark:bg-[#0c1120]">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-full bg-red-600 text-white flex items-center justify-center font-bold text-xs shadow-sm">
                        {{ strtoupper(substr($user->name, 0, 1)) }}
                    </div>
                    <div class="min-w-0">
                        <p class="text-xs font-bold text-slate-900 dark:text-white truncate">{{ $user->name }}</p>
                        <p class="text-[10px] font-medium text-slate-500 dark:text-slate-400 flex items-center gap-1">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                            {{ $user->role_title ?? 'Scholar' }} · <span class="font-bold text-red-600 dark:text-red-400">{{ $user->level }}</span>
                        </p>
                    </div>
                </div>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="p-1.5 rounded-lg text-slate-400 hover:text-red-600 hover:bg-slate-100 dark:hover:bg-slate-800 transition" title="Log Out">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                    </button>
                </form>
            </div>
        </div>

    </aside>

    <!-- ========================================== -->
    <!-- 2. MAIN APP CONTENT CONTAINER              -->
    <!-- ========================================== -->
    <div class="flex-1 flex flex-col min-w-0 overflow-hidden bg-slate-50 dark:bg-[#0b0f19]">
        
        <!-- Top Navigation Bar -->
        <header class="h-16 flex-shrink-0 bg-white/95 dark:bg-[#0e1424]/95 backdrop-blur-md border-b border-slate-200/80 dark:border-slate-800 px-4 sm:px-8 flex items-center justify-between z-20">
            
            <div class="flex items-center gap-4">
                <button onclick="toggleSidebar()" class="lg:hidden p-2 rounded-xl text-slate-500 hover:bg-slate-100 dark:hover:bg-slate-800">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                </button>
                <div class="flex items-center gap-2">
                    <span class="font-royal text-sm sm:text-base font-bold text-slate-900 dark:text-white tracking-wide">Académie de Français</span>
                    <span class="text-slate-400 dark:text-slate-600">/</span>
                    <span class="text-xs sm:text-sm font-semibold text-slate-600 dark:text-slate-300" id="current-tab-title">Academic Dashboard</span>
                </div>
            </div>

            <!-- Header Right Controls: Dark Mode Toggle + Streak Counter + Admin Link -->
            <div class="flex items-center gap-3">
                
                <!-- Admin Console Link Button -->
                <a href="{{ route('admin.index') }}" 
                   class="inline-flex items-center px-3 py-1.5 rounded-xl text-xs font-bold text-red-700 dark:text-red-300 bg-red-50 hover:bg-red-100 dark:bg-red-950/50 dark:hover:bg-red-900/60 border border-red-200 dark:border-red-900/80 transition shadow-2xs">
                    <span class="mr-1.5">👑</span>
                    Admin Console
                </a>

                <!-- Streak Counter Pill -->
                <div class="flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-amber-50 dark:bg-amber-950/40 border border-amber-200 dark:border-amber-800/60 text-amber-800 dark:text-amber-300 text-xs font-bold font-mono">
                    <span id="topbar-streak-counter">🔥 {{ $streak }} Days</span>
                </div>

                <!-- Dark Mode Toggle Button -->
                <button id="dark-mode-btn" onclick="toggleDarkMode()" 
                        class="p-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 hover:bg-slate-100 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 transition" 
                        title="Toggle Light / Dark Mode">
                    <svg id="sun-icon" class="w-4 h-4 hidden dark:block text-amber-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.465 5.05l-.708-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 000 2h1z" clip-rule="evenodd"/>
                    </svg>
                    <svg id="moon-icon" class="w-4 h-4 block dark:hidden text-slate-700" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"/>
                    </svg>
                </button>

                <!-- User Level Avatar -->
                <div class="flex items-center gap-2 pl-2 border-l border-slate-200 dark:border-slate-800">
                    <div class="w-8 h-8 rounded-full bg-red-600 text-white flex items-center justify-center font-bold text-xs shadow-xs">
                        {{ strtoupper(substr($user->name, 0, 1)) }}
                    </div>
                    <span class="text-xs font-mono font-bold px-2 py-0.5 rounded bg-slate-100 dark:bg-slate-800 text-slate-800 dark:text-slate-200">
                        {{ $user->level }}
                    </span>
                </div>

            </div>

        </header>

        <!-- Main Body Scrollable View -->
        <main class="flex-1 overflow-y-auto px-4 sm:px-8 py-6 space-y-8">
            
            <!-- ========================================== -->
            <!-- TAB: OVERVIEW / DASHBOARD                  -->
            <!-- ========================================== -->
            <div id="tab-overview" class="tab-page space-y-6">

                <!-- Welcome Scholar Banner -->
                <div class="fi-card p-6 sm:p-8 bg-gradient-to-r from-red-500/10 via-amber-500/5 to-transparent border border-red-500/20 dark:border-red-500/30 flex flex-col md:flex-row items-start md:items-center justify-between gap-6">
                    <div class="max-w-2xl">
                        <div class="flex items-center gap-2 mb-2">
                            <span class="text-[11px] font-bold uppercase tracking-wider text-red-600 dark:text-red-400 font-mono">
                                Bilingual Immersion Program
                            </span>
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-red-100 text-red-800 dark:bg-red-950/60 dark:text-red-300 border border-red-200 dark:border-red-900">
                                Level {{ $user->level }}
                            </span>
                        </div>
                        <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 dark:text-white tracking-tight">
                            Bienvenue, {{ $user->name }}
                        </h1>
                        <p class="text-xs sm:text-sm text-slate-600 dark:text-slate-300 mt-2 leading-relaxed">
                            Your personalized CEFR study curriculum is fully synchronized. Complete daily lessons, answer interactive quizzes, and explore classical literature with English & Hindi meanings to maintain your academic streak.
                        </p>
                    </div>
                    <div class="flex items-center gap-2.5">
                        @if ($nextLesson)
                            <button onclick="openClassroomModal({{ $nextLesson->id }}, '{{ addslashes($nextLesson->course->title) }}')"
                                    class="px-4 py-2.5 rounded-xl bg-red-600 hover:bg-red-700 text-white text-xs font-bold shadow-md hover:opacity-95 active:scale-[0.99] flex items-center gap-2 transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/></svg>
                                <span>Resume: {{ $nextLesson->title }}</span>
                            </button>
                        @endif
                        <button onclick="switchTab('dictionary')" class="px-3.5 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-semibold text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-700 shadow-2xs transition">
                            Explore Vocabulary (EN & HI)
                        </button>
                    </div>
                </div>

                <!-- Stats Widgets -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    
                    <!-- Stat 1: Active Courses -->
                    <div class="fi-card p-5 relative overflow-hidden group">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Active Courses</span>
                            <div class="w-9 h-9 rounded-xl bg-red-50 dark:bg-red-950/60 text-red-600 dark:text-red-400 flex items-center justify-center font-bold">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                            </div>
                        </div>
                        <div class="mt-3 flex items-baseline gap-2">
                            <span class="text-2xl font-extrabold text-slate-900 dark:text-white" id="stat-enrolled-count">{{ $enrolledCount }}</span>
                            <span class="text-xs text-slate-500 dark:text-slate-400">enrolled</span>
                        </div>
                        <div class="mt-2.5 flex items-center gap-1.5 text-xs text-emerald-600 dark:text-emerald-400 font-semibold">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                            <span>Assigned to CEFR {{ $user->level }}</span>
                        </div>
                    </div>

                    <!-- Stat 2: Syllabus Mastery -->
                    <div class="fi-card p-5 relative overflow-hidden group">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Syllabus Mastery</span>
                            <div class="w-9 h-9 rounded-xl bg-emerald-50 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 flex items-center justify-center font-bold">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </div>
                        </div>
                        <div class="mt-3 flex items-baseline gap-2">
                            <span class="text-2xl font-extrabold text-slate-900 dark:text-white" id="stat-avg-progress">{{ $avgProgress }}%</span>
                            <span class="text-xs text-slate-500 dark:text-slate-400">completion rate</span>
                        </div>
                        <div class="mt-2.5 flex items-center gap-1.5 text-xs text-emerald-600 dark:text-emerald-400 font-semibold">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                            <span>{{ $completedLessonsTotal }} lessons completed</span>
                        </div>
                    </div>

                    <!-- Stat 3: Vocabulary Sanctuary -->
                    <div class="fi-card p-5 relative overflow-hidden group">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Classical Vocabulary</span>
                            <div class="w-9 h-9 rounded-xl bg-indigo-50 dark:bg-indigo-950/60 text-indigo-600 dark:text-indigo-400 flex items-center justify-center font-bold">
                                <span>📖</span>
                            </div>
                        </div>
                        <div class="mt-3 flex items-baseline gap-2">
                            <span class="text-2xl font-extrabold text-slate-900 dark:text-white" id="stat-vocab-count">{{ $allVocabulary->count() }}</span>
                            <span class="text-xs text-slate-500 dark:text-slate-400">words in lexicon</span>
                        </div>
                        <div class="mt-2.5 flex items-center gap-1.5 text-xs text-indigo-600 dark:text-indigo-400 font-semibold">
                            <span>FR · EN · HI bilingual terms</span>
                        </div>
                    </div>

                    <!-- Stat 4: Academic Streak Counter -->
                    <div class="fi-card p-5 relative overflow-hidden group">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Daily Study Streak</span>
                            <div class="w-9 h-9 rounded-xl bg-amber-50 dark:bg-amber-950/60 text-amber-600 dark:text-amber-400 flex items-center justify-center font-bold text-lg">
                                🔥
                            </div>
                        </div>
                        <div class="mt-3 flex items-baseline gap-2">
                            <span class="text-2xl font-extrabold text-slate-900 dark:text-white" id="stat-streak-counter">{{ $streak }} Days</span>
                            <span class="text-xs text-slate-500 dark:text-slate-400">active</span>
                        </div>
                        <div class="mt-2.5 flex items-center gap-1.5 text-xs text-amber-700 dark:text-amber-400 font-semibold">
                            <span id="stat-streak-subtext">{{ $streak > 0 ? '🔥 Streak active' : 'Complete 1 task to ignite' }}</span>
                        </div>
                    </div>

                </div>

                <!-- Day-by-Day Activity Streak Strip -->
                <div class="fi-card p-5 bg-gradient-to-r from-amber-500/10 via-red-500/5 to-transparent border border-amber-500/20 dark:border-amber-500/30">
                    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                        <div>
                            <div class="flex items-center gap-2">
                                <span class="text-xs font-bold uppercase tracking-wider text-amber-700 dark:text-amber-400 font-mono">Academic Activity Streak</span>
                                <span id="streak-badge-status" class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ $streak > 0 ? 'bg-amber-100 text-amber-800 dark:bg-amber-950/60 dark:text-amber-300' : 'bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-400' }}">
                                    {{ $streak > 0 ? '🔥 Active Daily Streak' : '⚡ Complete a task today to ignite streak' }}
                                </span>
                            </div>
                            <h3 class="text-base font-extrabold text-slate-900 dark:text-white mt-1 flex items-center gap-2">
                                <span>🔥</span>
                                <span id="streak-headline-count">{{ $streak }} Days Active</span>
                                <span class="text-xs font-normal text-slate-500 dark:text-slate-400">· Increments when completing lessons, quizzes, or vocabulary</span>
                            </h3>
                        </div>

                        <!-- 7-Day Day-by-Day Activity Tracker Dots -->
                        <div class="flex items-center gap-2 sm:gap-3" id="weekly-activity-tracker">
                            @foreach($weeklyActivity['days'] as $day)
                                <div class="flex flex-col items-center gap-1">
                                    <span class="text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase font-mono">{{ $day['day_name'] }}</span>
                                    <div class="w-9 h-9 rounded-xl flex items-center justify-center font-bold text-xs transition-all duration-300 relative
                                        @if($day['is_completed'])
                                            bg-amber-500 text-white shadow-md shadow-amber-500/30 ring-2 ring-amber-400/50
                                        @elseif($day['is_today'])
                                            bg-white dark:bg-slate-800 border-2 border-dashed border-amber-500 text-amber-600 dark:text-amber-400 animate-pulse
                                        @elseif($day['is_future'])
                                            bg-slate-100 dark:bg-slate-800/40 text-slate-400 dark:text-slate-600 border border-slate-200 dark:border-slate-800
                                        @else
                                            bg-slate-100 dark:bg-slate-800 text-slate-400 dark:text-slate-500 border border-slate-200 dark:border-slate-700
                                        @endif"
                                        title="{{ $day['date'] }} · {{ $day['is_completed'] ? 'Task Completed' : ($day['is_today'] ? 'Today (Pending task)' : ($day['is_future'] ? 'Upcoming' : 'Missed Day')) }}">
                                        @if($day['is_completed'])
                                            🔥
                                        @elseif($day['is_today'])
                                            {{ $day['day_number'] }}
                                        @elseif($day['is_future'])
                                            <span class="text-[10px]">{{ $day['day_number'] }}</span>
                                        @else
                                            <span class="text-[10px] text-slate-400">✕</span>
                                        @endif
                                    </div>
                                    <span class="text-[9px] font-mono text-slate-400">{{ $day['day_number'] }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Active Syllabus Progress Section -->
                <div class="fi-card p-6">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <h2 class="text-base font-bold text-slate-900 dark:text-white">Active Syllabus & Lesson Progress</h2>
                            <p class="text-xs text-slate-500 dark:text-slate-400">Track and advance through your CEFR {{ $user->level }} curriculum modules.</p>
                        </div>
                        <button onclick="switchTab('courses')" class="text-xs font-bold text-red-600 dark:text-red-400 hover:underline">
                            View All Courses →
                        </button>
                    </div>

                    <div class="space-y-4">
                        @forelse($enrollments as $enr)
                            <div class="p-4 rounded-xl border border-slate-200 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-800/40 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                                <div class="space-y-1">
                                    <div class="flex items-center gap-2">
                                        <span class="text-xs font-mono font-bold px-2 py-0.5 rounded bg-amber-100 dark:bg-amber-950/60 text-amber-800 dark:text-amber-300 border border-amber-300 dark:border-amber-800">
                                            {{ $enr->course->level }}
                                        </span>
                                        <h3 class="font-bold text-sm text-slate-900 dark:text-white">{{ $enr->course->title }}</h3>
                                    </div>
                                    <p class="text-xs text-slate-500 dark:text-slate-400">{{ $enr->course->short_desc }}</p>
                                    <div class="flex items-center gap-3 text-[11px] text-slate-500 pt-1">
                                        <span>Instructor: <strong class="text-slate-700 dark:text-slate-300">{{ $enr->course->instructor_name }}</strong></span>
                                        <span>·</span>
                                        <span>{{ $enr->completed_lessons_count }} of {{ $enr->course->lessons->count() }} lessons completed</span>
                                    </div>
                                </div>

                                <div class="w-full sm:w-64 space-y-2 flex-shrink-0">
                                    <div class="flex items-center justify-between text-xs font-semibold">
                                        <span class="text-slate-600 dark:text-slate-400">Mastery</span>
                                        <span class="font-mono text-red-600 dark:text-red-400">{{ $enr->progress_percent }}%</span>
                                    </div>
                                    <div class="w-full h-2 rounded-full bg-slate-200 dark:bg-slate-700 overflow-hidden">
                                        <div class="h-full bg-red-600 rounded-full transition-all duration-500" style="width: {{ $enr->progress_percent }}%"></div>
                                    </div>
                                    @php
                                        $courseNextLesson = $enr->course->lessons->first();
                                    @endphp
                                    @if($courseNextLesson)
                                        <button onclick="openClassroomModal({{ $courseNextLesson->id }}, '{{ addslashes($enr->course->title) }}')"
                                                class="w-full py-1.5 px-3 rounded-lg bg-red-600 hover:bg-red-700 text-white font-bold text-xs transition text-center block shadow-2xs">
                                            Continue Course →
                                        </button>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <div class="p-8 text-center text-slate-500 dark:text-slate-400">
                                <p class="text-sm">You are not enrolled in any courses yet.</p>
                                <button onclick="switchTab('courses')" class="mt-3 px-4 py-2 rounded-xl bg-red-600 text-white text-xs font-bold shadow-md hover:bg-red-700 transition">
                                    Browse Course Catalog
                                </button>
                            </div>
                        @endforelse
                    </div>
                </div>

            </div>

            <!-- ========================================== -->
            <!-- TAB: COURSES CATALOG                       -->
            <!-- ========================================== -->
            <div id="tab-courses" class="tab-page space-y-6 hidden">
                <div class="fi-card p-6">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
                        <div>
                            <h2 class="text-base font-bold text-slate-900 dark:text-white">Académie Course Catalog (CEFR A1–C2)</h2>
                            <p class="text-xs text-slate-500 dark:text-slate-400">Select any course to enroll or continue your study.</p>
                        </div>
                        <div class="flex items-center gap-1.5 bg-slate-100 dark:bg-slate-800 p-1 rounded-xl text-xs font-semibold">
                            <button onclick="filterCourses('ALL')" class="course-filter-btn px-3 py-1 rounded-lg bg-slate-900 text-white dark:bg-white dark:text-slate-900">All</button>
                            <button onclick="filterCourses('A1')" class="course-filter-btn px-3 py-1 rounded-lg text-slate-600 dark:text-slate-400 hover:text-slate-900">A1</button>
                            <button onclick="filterCourses('A2')" class="course-filter-btn px-3 py-1 rounded-lg text-slate-600 dark:text-slate-400 hover:text-slate-900">A2</button>
                            <button onclick="filterCourses('B1')" class="course-filter-btn px-3 py-1 rounded-lg text-slate-600 dark:text-slate-400 hover:text-slate-900">B1</button>
                            <button onclick="filterCourses('B2')" class="course-filter-btn px-3 py-1 rounded-lg text-slate-600 dark:text-slate-400 hover:text-slate-900">B2</button>
                            <button onclick="filterCourses('C1')" class="course-filter-btn px-3 py-1 rounded-lg text-slate-600 dark:text-slate-400 hover:text-slate-900">C1</button>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5" id="courses-grid">
                        @foreach($allCourses as $c)
                            @php
                                $isEnrolled = in_array($c->id, $enrolledCourseIds);
                                $courseEnr = $enrollments->firstWhere('course_id', $c->id);
                            @endphp
                            <div class="course-card fi-card p-5 border border-slate-200 dark:border-slate-800 flex flex-col justify-between" data-level="{{ $c->level }}">
                                <div>
                                    <div class="flex items-center justify-between mb-3">
                                        <span class="text-xs font-mono font-bold px-2.5 py-0.5 rounded bg-red-100 dark:bg-red-950/60 text-red-800 dark:text-red-300 border border-red-300 dark:border-red-800">
                                            {{ $c->level }}
                                        </span>
                                        <span class="text-[11px] font-semibold text-slate-500 dark:text-slate-400">{{ $c->category }}</span>
                                    </div>
                                    <h3 class="font-bold text-sm text-slate-900 dark:text-white leading-snug mb-1">{{ $c->title }}</h3>
                                    <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed mb-4">{{ $c->short_desc }}</p>
                                    
                                    <div class="text-[11px] text-slate-500 space-y-1 mb-4 border-t border-slate-100 dark:border-slate-800 pt-3">
                                        <div>Instructor: <strong class="text-slate-700 dark:text-slate-300">{{ $c->instructor_name }}</strong></div>
                                        <div>Duration: <strong class="text-slate-700 dark:text-slate-300">{{ $c->duration_weeks }} Weeks</strong> · {{ $c->lessons_count }} Lessons</div>
                                    </div>
                                </div>

                                <div>
                                    @if($isEnrolled)
                                        <div class="mb-3">
                                            <div class="flex items-center justify-between text-[11px] font-semibold text-slate-600 dark:text-slate-400 mb-1">
                                                <span>Enrolled</span>
                                                <span class="font-mono text-red-600">{{ $courseEnr->progress_percent ?? 0 }}%</span>
                                            </div>
                                            <div class="w-full h-1.5 rounded-full bg-slate-200 dark:bg-slate-700 overflow-hidden">
                                                <div class="h-full bg-red-600 rounded-full" style="width: {{ $courseEnr->progress_percent ?? 0 }}%"></div>
                                            </div>
                                        </div>
                                        @php
                                            $firstL = $c->lessons->first();
                                        @endphp
                                        @if($firstL)
                                            <button onclick="openClassroomModal({{ $firstL->id }}, '{{ addslashes($c->title) }}')"
                                                    class="w-full py-2 rounded-xl bg-slate-900 hover:bg-slate-800 text-white dark:bg-white dark:text-slate-900 dark:hover:bg-slate-100 font-bold text-xs transition">
                                                Open Classroom →
                                            </button>
                                        @endif
                                    @else
                                        <button onclick="enrollInCourse('{{ $c->slug }}')"
                                                class="w-full py-2 rounded-xl bg-red-600 hover:bg-red-700 text-white font-bold text-xs transition shadow-md shadow-red-600/20">
                                            Enroll in Course
                                        </button>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- ========================================== -->
            <!-- TAB: SCHOLARS DIRECTORY                    -->
            <!-- ========================================== -->
            <div id="tab-scholars" class="tab-page space-y-6 hidden">
                <div class="fi-card p-6">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
                        <div>
                            <h2 class="text-base font-bold text-slate-900 dark:text-white">Academic Directory & Scholar Profiles</h2>
                            <p class="text-xs text-slate-500 dark:text-slate-400">View registered scholars, their academic level, and study streaks.</p>
                        </div>
                        <input type="text" id="scholar-search-input" onkeyup="filterScholarsTable()" placeholder="Search scholar by name, country, or goal..."
                               class="text-xs px-3.5 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-800 dark:text-slate-200 w-full sm:w-72 focus:outline-none focus:border-red-500">
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs">
                            <thead class="border-b border-slate-200 dark:border-slate-800 text-slate-400 uppercase tracking-wider font-semibold">
                                <tr>
                                    <th class="py-3 px-4">Scholar</th>
                                    <th class="py-3 px-4">CEFR Level</th>
                                    <th class="py-3 px-4">Role</th>
                                    <th class="py-3 px-4">Active Streak</th>
                                    <th class="py-3 px-4">Completed Lessons</th>
                                    <th class="py-3 px-4">Country</th>
                                    <th class="py-3 px-4 text-right">Dossier</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 dark:divide-slate-800/60" id="scholars-table-body">
                                @foreach($allScholars as $s)
                                    <tr class="scholar-row hover:bg-slate-50/70 dark:hover:bg-slate-800/40 transition"
                                        data-name="{{ strtolower($s->name) }}"
                                        data-email="{{ strtolower($s->email) }}"
                                        data-country="{{ strtolower($s->country ?? '') }}"
                                        data-goal="{{ strtolower($s->target_goal ?? '') }}">
                                        <td class="py-3.5 px-4">
                                            <div class="flex items-center gap-3">
                                                <div class="w-8 h-8 rounded-full bg-slate-200 dark:bg-slate-700 text-slate-800 dark:text-white flex items-center justify-center font-bold text-xs flex-shrink-0">
                                                    {{ strtoupper(substr($s->name, 0, 1)) }}
                                                </div>
                                                <div>
                                                    <div class="font-bold text-slate-900 dark:text-white">{{ $s->name }}</div>
                                                    <div class="text-[11px] text-slate-400">{{ $s->email }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="py-3.5 px-4">
                                            <span class="px-2 py-0.5 rounded font-mono font-bold text-[10px] bg-red-100 dark:bg-red-950/60 text-red-800 dark:text-red-300">
                                                {{ $s->level }}
                                            </span>
                                        </td>
                                        <td class="py-3.5 px-4 text-slate-600 dark:text-slate-300 capitalize">
                                            {{ $s->role_title ?? $s->role }}
                                        </td>
                                        <td class="py-3.5 px-4 font-mono font-bold text-amber-600 dark:text-amber-400">
                                            🔥 {{ $s->current_streak }} Days
                                        </td>
                                        <td class="py-3.5 px-4 font-semibold text-emerald-600 dark:text-emerald-400">
                                            {{ $s->completions_count ?? 0 }} Lessons
                                        </td>
                                        <td class="py-3.5 px-4 text-slate-500">
                                            {{ $s->country ?? 'Canada' }}
                                        </td>
                                        <td class="py-3.5 px-4 text-right">
                                            <button onclick="openScholarDossier({{ $s->id }})" 
                                                    class="px-2.5 py-1 rounded-lg bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 font-semibold text-[11px] transition">
                                                View Dossier
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- ========================================== -->
            <!-- TAB: CLASSICAL LIBRARY                     -->
            <!-- ========================================== -->
            <div id="tab-library" class="tab-page space-y-6 hidden">
                <div class="fi-card p-6">
                    <div class="mb-6">
                        <h2 class="text-base font-bold text-slate-900 dark:text-white">Classical French Literature Sanctuary</h2>
                        <p class="text-xs text-slate-500 dark:text-slate-400">Immersion in timeless masterpieces: side-by-side French prose with faithful English academic translations.</p>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        @foreach($books as $b)
                            <div class="p-5 rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-800/40 space-y-4">
                                <div class="flex items-center justify-between">
                                    <span class="text-[10px] font-mono font-bold px-2 py-0.5 rounded bg-red-100 text-red-800 dark:bg-red-950/60 dark:text-red-300">
                                        {{ $b['level'] }} · {{ $b['year'] }}
                                    </span>
                                    <span class="text-[11px] font-semibold text-slate-400">{{ $b['badge'] }}</span>
                                </div>
                                <div>
                                    <h3 class="font-royal text-base font-bold text-slate-900 dark:text-white">{{ $b['title'] }}</h3>
                                    <p class="text-xs text-slate-500">by {{ $b['author'] }}</p>
                                </div>
                                <div class="p-4 rounded-xl bg-slate-50 dark:bg-slate-900/60 border border-slate-100 dark:border-slate-800 space-y-3">
                                    <div>
                                        <div class="text-[10px] font-bold text-red-600 dark:text-red-400 uppercase font-mono mb-1">Français Classique</div>
                                        <p class="font-serif text-xs text-slate-800 dark:text-slate-200 italic leading-relaxed">{{ $b['frenchExcerpt'] }}</p>
                                    </div>
                                    <div class="border-t border-slate-200/60 dark:border-slate-800 pt-2">
                                        <div class="text-[10px] font-bold text-slate-500 uppercase font-mono mb-1">English Academic Translation</div>
                                        <p class="text-xs text-slate-600 dark:text-slate-400 leading-relaxed">{{ $b['englishExcerpt'] }}</p>
                                    </div>
                                </div>
                                <button onclick="speakFrench('{{ addslashes(substr($b['frenchExcerpt'], 0, 150)) }}')"
                                        class="px-3 py-1.5 rounded-lg bg-red-600/10 text-red-600 dark:text-red-400 hover:bg-red-600/20 text-xs font-semibold flex items-center gap-1.5 transition">
                                    <span>🔊 Listen in Authentic French</span>
                                </button>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- ========================================== -->
            <!-- TAB: VOCABULARY SANCTUARY (Classical Immersion with Hindi & Use Cases) -->
            <!-- ========================================== -->
            <div id="tab-dictionary" class="tab-page space-y-6 hidden">
                <div class="fi-card p-6">
                    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
                        <div>
                            <div class="flex items-center gap-2 mb-1">
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-red-100 text-red-800 dark:bg-red-950/60 dark:text-red-400 border border-red-200 dark:border-red-900 font-mono">
                                    Classical Immersion · Lexicon
                                </span>
                                <span class="text-xs text-slate-400">·</span>
                                <span class="text-xs font-semibold text-emerald-600 dark:text-emerald-400 flex items-center gap-1">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                    Hermes AI Synchronized
                                </span>
                            </div>
                            <h2 class="text-xl font-bold text-slate-900 dark:text-white">French Vocabulary Sanctuary (FR · EN · HI)</h2>
                            <p class="text-xs text-slate-500 dark:text-slate-400">Every word features French phonetics, English definition, Hindi meaning (हिंदी अर्थ), and real-life contextual use cases.</p>
                        </div>
                        
                        <div class="flex items-center gap-2.5">
                            <button onclick="openAddWordModal()" class="px-3.5 py-2 rounded-xl bg-red-600 text-white font-bold text-xs hover:bg-red-700 shadow-md shadow-red-600/20 transition flex items-center gap-1.5">
                                <span>+ Add Personal Word</span>
                            </button>
                        </div>
                    </div>

                    <!-- Search & Filter Controls -->
                    <div class="flex flex-col sm:flex-row items-center justify-between gap-3 mb-6 bg-slate-50 dark:bg-slate-800/40 p-3 rounded-2xl border border-slate-200 dark:border-slate-800">
                        <div class="relative w-full sm:w-80">
                            <input type="text" id="vocab-search-input" onkeyup="filterVocabulary()" placeholder="Search word in French, English, or Hindi..."
                                   class="w-full text-xs pl-8 pr-4 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-darkinput text-slate-900 dark:text-white focus:outline-none focus:border-red-500">
                            <span class="absolute left-2.5 top-2.5 text-xs text-slate-400">🔍</span>
                        </div>
                        
                        <div class="flex items-center gap-1 bg-white dark:bg-slate-800 p-1 rounded-xl border border-slate-200 dark:border-slate-700 text-xs font-semibold overflow-x-auto w-full sm:w-auto">
                            <button onclick="filterVocabLevel('ALL')" class="vocab-lvl-btn px-2.5 py-1 rounded-lg bg-slate-900 text-white dark:bg-white dark:text-slate-900">All Levels</button>
                            <button onclick="filterVocabLevel('A1')" class="vocab-lvl-btn px-2.5 py-1 rounded-lg text-slate-600 dark:text-slate-400">A1</button>
                            <button onclick="filterVocabLevel('A2')" class="vocab-lvl-btn px-2.5 py-1 rounded-lg text-slate-600 dark:text-slate-400">A2</button>
                            <button onclick="filterVocabLevel('B1')" class="vocab-lvl-btn px-2.5 py-1 rounded-lg text-slate-600 dark:text-slate-400">B1</button>
                            <button onclick="filterVocabLevel('B2')" class="vocab-lvl-btn px-2.5 py-1 rounded-lg text-slate-600 dark:text-slate-400">B2</button>
                            <button onclick="filterVocabLevel('C1')" class="vocab-lvl-btn px-2.5 py-1 rounded-lg text-slate-600 dark:text-slate-400">C1</button>
                        </div>
                    </div>

                    <!-- Dynamic Vocabulary Cards Grid -->
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5" id="dictionary-grid">
                        <!-- Populated by JavaScript -->
                    </div>
                </div>
            </div>

            <!-- ========================================== -->
            <!-- TAB: DAILY EXPRESSIONS                     -->
            <!-- ========================================== -->
            <div id="tab-phrases" class="tab-page space-y-6 hidden">
                <div class="fi-card p-6">
                    <div class="mb-6">
                        <h2 class="text-base font-bold text-slate-900 dark:text-white">Daily Expressions & Classical Sentences</h2>
                        <p class="text-xs text-slate-500 dark:text-slate-400">High-frequency French idioms and conversational phrases for daily bilingual fluency.</p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4" id="sentences-grid">
                        <!-- Populated by JavaScript -->
                    </div>
                </div>
            </div>

        </main>
    </div>

    <!-- ========================================== -->
    <!-- MODALS & POPUPS                            -->
    <!-- ========================================== -->

    <!-- CLASSROOM LESSON MODAL -->
    <div id="classroom-modal" class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center hidden p-4 overflow-y-auto">
        <div class="fi-card bg-white dark:bg-[#111827] border border-slate-200 dark:border-slate-800 max-w-2xl w-full p-6 sm:p-8 my-8 shadow-2xl relative">
            <button onclick="closeClassroomModal()" class="absolute top-5 right-5 text-slate-400 hover:text-slate-600 dark:hover:text-white text-lg">✕</button>
            
            <div class="mb-4">
                <span class="text-[10px] font-mono font-bold text-red-600 dark:text-red-400 uppercase tracking-wider" id="modal-course-title">Course Title</span>
                <h2 class="text-xl font-bold text-slate-900 dark:text-white mt-1" id="modal-lesson-title">Lesson Title</h2>
            </div>

            <!-- French Passage -->
            <div class="p-5 rounded-2xl bg-slate-50 dark:bg-slate-900/60 border border-slate-200 dark:border-slate-800 space-y-3 mb-5">
                <div class="flex items-center justify-between">
                    <span class="text-[10px] font-bold text-red-600 dark:text-red-400 uppercase font-mono">French Reading</span>
                    <button onclick="playLessonAudio()" class="px-2.5 py-1 rounded bg-red-600 text-white font-bold text-xs hover:bg-red-700 transition">🔊 Listen (TTS)</button>
                </div>
                <p class="font-serif text-sm text-slate-900 dark:text-slate-100 leading-relaxed italic" id="modal-french-text"></p>
                <div class="border-t border-slate-200 dark:border-slate-800 pt-2.5">
                    <span class="text-[10px] font-bold text-slate-500 uppercase font-mono">English Translation</span>
                    <p class="text-xs text-slate-600 dark:text-slate-300 leading-relaxed mt-0.5" id="modal-english-text"></p>
                </div>
            </div>

            <!-- Vocabulary Cards -->
            <div class="mb-5">
                <h4 class="text-xs font-bold text-slate-900 dark:text-white uppercase tracking-wider mb-2">Lesson Lexicon</h4>
                <div class="grid grid-cols-2 gap-2.5" id="modal-vocab-list"></div>
            </div>

            <!-- Interactive Quiz (Updates Streak upon answer) -->
            <div class="p-4 rounded-xl bg-amber-500/10 border border-amber-500/20 mb-5">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-xs font-bold text-amber-800 dark:text-amber-300 uppercase tracking-wider font-mono">Interactive Quiz Question</span>
                    <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-amber-200 text-amber-900 dark:bg-amber-950 dark:text-amber-300">🔥 Streak Task</span>
                </div>
                <p class="text-xs font-bold text-slate-900 dark:text-white mb-3" id="modal-quiz-question"></p>
                <div class="space-y-2" id="modal-quiz-options"></div>
                <div id="modal-quiz-feedback" class="mt-2 text-xs font-bold hidden"></div>
            </div>

            <!-- Complete Lesson Button -->
            <div class="flex items-center justify-end gap-3 pt-2 border-t border-slate-100 dark:border-slate-800">
                <button onclick="closeClassroomModal()" class="px-4 py-2 rounded-xl text-xs font-semibold text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 transition">
                    Close
                </button>
                <button onclick="markLessonComplete()" class="px-5 py-2.5 rounded-xl bg-red-600 hover:bg-red-700 text-white font-bold text-xs transition shadow-md shadow-red-600/20">
                    Mark Lesson Complete & Update Streak 🔥
                </button>
            </div>
        </div>
    </div>

    <!-- ADD CUSTOM WORD MODAL (With Hindi & Use Case fields) -->
    <div id="add-word-modal" class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center hidden p-4 overflow-y-auto">
        <div class="fi-card bg-white dark:bg-[#111827] border border-slate-200 dark:border-slate-800 max-w-lg w-full p-6 shadow-2xl relative my-8">
            <button onclick="closeAddWordModal()" class="absolute top-4 right-4 text-slate-400 hover:text-slate-600 text-lg">✕</button>
            <div class="flex items-center justify-between mb-1">
                <h3 class="text-base font-bold text-slate-900 dark:text-white">Add Vocabulary to Sanctuary</h3>
                <span class="text-[10px] font-mono px-2 py-0.5 rounded bg-red-100 dark:bg-red-950/60 text-red-700 dark:text-red-400 font-bold">FR · EN · HI</span>
            </div>
            <p class="text-xs text-slate-500 mb-4">Expands your lexicon with Hindi & English meanings and updates your daily study streak!</p>
            
            <div class="space-y-3">
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-[11px] font-bold text-slate-700 dark:text-slate-300 mb-1">French Word *</label>
                        <input type="text" id="new-word-fr" placeholder="e.g. Bienveillance" class="w-full px-3 py-2 rounded-xl text-xs bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white focus:outline-none focus:border-red-500">
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-slate-700 dark:text-slate-300 mb-1">IPA Phonetics</label>
                        <input type="text" id="new-word-ipa" placeholder="/bjɛ̃.vɛ.jɑ̃s/" class="w-full px-3 py-2 rounded-xl text-xs bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white focus:outline-none focus:border-red-500">
                    </div>
                </div>

                <div>
                    <label class="block text-[11px] font-bold text-slate-700 dark:text-slate-300 mb-1">English Meaning *</label>
                    <input type="text" id="new-word-meaning" placeholder="Benevolence, compassionate kindness" class="w-full px-3 py-2 rounded-xl text-xs bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white focus:outline-none focus:border-red-500">
                </div>

                <div>
                    <label class="block text-[11px] font-bold text-slate-700 dark:text-slate-300 mb-1">Hindi Meaning (हिंदी अर्थ)</label>
                    <input type="text" id="new-word-meaning-hi" placeholder="उदा. सद्भावना, परोपकार, कृपाशीलता" class="w-full px-3 py-2 rounded-xl text-xs bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white focus:outline-none focus:border-red-500">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-[11px] font-bold text-slate-700 dark:text-slate-300 mb-1">CEFR Level</label>
                        <select id="new-word-level" class="w-full px-3 py-2 rounded-xl text-xs bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white">
                            <option value="A1">A1</option>
                            <option value="A2">A2</option>
                            <option value="B1" selected>B1</option>
                            <option value="B2">B2</option>
                            <option value="C1">C1</option>
                            <option value="C2">C2</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-slate-700 dark:text-slate-300 mb-1">Category</label>
                        <input type="text" id="new-word-category" value="Personal Lexicon" class="w-full px-3 py-2 rounded-xl text-xs bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white">
                    </div>
                </div>

                <div>
                    <label class="block text-[11px] font-bold text-slate-700 dark:text-slate-300 mb-1">French Example Sentence</label>
                    <input type="text" id="new-word-example-fr" placeholder="e.g. Il agit avec bienveillance." class="w-full px-3 py-2 rounded-xl text-xs bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white">
                </div>

                <div>
                    <label class="block text-[11px] font-bold text-slate-700 dark:text-slate-300 mb-1">Practical Use Case (कहाँ और कैसे इस्तेमाल होगा?)</label>
                    <textarea id="new-word-use-case" rows="2" placeholder="e.g. Used in polite conversations, letters, and describing kind teachers." class="w-full px-3 py-2 rounded-xl text-xs bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white"></textarea>
                </div>
            </div>

            <div class="flex items-center justify-end gap-2 mt-5">
                <button onclick="closeAddWordModal()" class="px-3 py-2 text-xs font-semibold text-slate-500 hover:text-slate-700">Cancel</button>
                <button onclick="submitNewWord()" class="px-4 py-2 rounded-xl bg-red-600 text-white font-bold text-xs hover:bg-red-700 transition">Save & Update Streak 🔥</button>
            </div>
        </div>
    </div>

    <!-- SCHOLAR DOSSIER MODAL -->
    <div id="scholar-dossier-modal" class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center hidden p-4">
        <div class="fi-card bg-white dark:bg-[#111827] border border-slate-200 dark:border-slate-800 max-w-md w-full p-6 shadow-2xl relative">
            <button onclick="document.getElementById('scholar-dossier-modal').classList.add('hidden')" class="absolute top-4 right-4 text-slate-400 hover:text-slate-600 text-lg">✕</button>
            
            <div class="flex items-center gap-3 mb-4">
                <div class="w-12 h-12 rounded-full bg-red-600 text-white flex items-center justify-center font-bold text-lg shadow-sm" id="dossier-modal-avatar">
                    A
                </div>
                <div>
                    <h3 class="text-base font-bold text-slate-900 dark:text-white" id="dossier-modal-name">Scholar Dossier</h3>
                    <p class="text-xs text-slate-500" id="dossier-modal-email">scholar@academie.fr</p>
                </div>
            </div>

            <div class="space-y-2.5 text-xs">
                <div class="p-3 rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-100 dark:border-slate-800 flex items-center justify-between">
                    <span class="text-slate-500">CEFR Placement:</span>
                    <span class="font-bold text-red-600 dark:text-red-400 font-mono" id="dossier-modal-level">B1</span>
                </div>
                <div class="p-3 rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-100 dark:border-slate-800 flex items-center justify-between">
                    <span class="text-slate-500">Daily Study Streak:</span>
                    <span class="font-bold text-amber-600 dark:text-amber-400 font-mono" id="dossier-modal-streak">🔥 14 Days</span>
                </div>
                <div class="p-3 rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-100 dark:border-slate-800 flex items-center justify-between">
                    <span class="text-slate-500">Country of Origin:</span>
                    <span class="font-semibold text-slate-800 dark:text-slate-200" id="dossier-modal-country">🇨🇦 Canada</span>
                </div>
                <div class="p-3 rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-100 dark:border-slate-800">
                    <span class="text-slate-500 block mb-1">Target Academic Goal:</span>
                    <p class="font-medium text-slate-800 dark:text-slate-200" id="dossier-modal-goal"></p>
                </div>
            </div>

            <div class="mt-4 pt-3 border-t border-slate-100 dark:border-slate-800 flex justify-end">
                <button onclick="document.getElementById('scholar-dossier-modal').classList.add('hidden')" class="px-4 py-2 rounded-xl bg-slate-900 dark:bg-white text-white dark:text-slate-900 font-bold text-xs">
                    Close Dossier
                </button>
            </div>
        </div>
    </div>

    <!-- ========================================== -->
    <!-- JAVASCRIPT LOGIC                           -->
    <!-- ========================================== -->
    <script>
        const ALL_COURSES = @json($allCourses);
        const ALL_SCHOLARS = @json($allScholars);
        let VOCAB_DATA = @json($allVocabulary, JSON_UNESCAPED_UNICODE);
        const SENTENCES_DATA = @json($sentences);

        let currentLessonId = null;
        let currentLessonAudioPhrase = '';
        let currentVocabFilterLevel = 'ALL';

        // 1. Dark Mode Toggle
        function toggleDarkMode() {
            const isDark = document.documentElement.classList.toggle('dark');
            localStorage.setItem('theme', isDark ? 'dark' : 'light');
            updateDarkModeUI(isDark);
        }

        function updateDarkModeUI(isDark) {
            const statusLabel = document.getElementById('sidebar-theme-status');
            const btnText = document.getElementById('sidebar-theme-btn-text');
            const icon = document.getElementById('sidebar-theme-icon');

            if (statusLabel) statusLabel.textContent = isDark ? 'Dark' : 'Light';
            if (btnText) btnText.textContent = isDark ? 'Switch to Light Mode' : 'Switch to Dark Mode';
            if (icon) icon.textContent = isDark ? '☀️' : '🌙';
        }

        // 2. Navigation Tab Switching
        function switchTab(tabName) {
            document.querySelectorAll('.tab-page').forEach(page => page.classList.add('hidden'));
            document.querySelectorAll('.nav-btn').forEach(btn => {
                btn.classList.remove('bg-red-50', 'dark:bg-red-950/40', 'text-red-700', 'dark:text-red-400');
                btn.classList.add('text-slate-600', 'dark:text-slate-300');
            });

            const targetPage = document.getElementById('tab-' + tabName);
            if (targetPage) targetPage.classList.remove('hidden');

            const activeBtn = document.getElementById('nav-' + tabName);
            if (activeBtn) {
                activeBtn.classList.add('bg-red-50', 'dark:bg-red-950/40', 'text-red-700', 'dark:text-red-400');
                activeBtn.classList.remove('text-slate-600', 'dark:text-slate-300');
            }

            const titles = {
                'overview': 'Academic Dashboard',
                'courses': 'Course Catalog (CEFR A1–C2)',
                'my-curriculum': 'My Enrolled Syllabus',
                'scholars': 'Academic Directory',
                'library': 'Classical French Literature',
                'dictionary': 'Vocabulary Sanctuary (FR · EN · HI)',
                'phrases': 'Daily Expressions'
            };
            document.getElementById('current-tab-title').textContent = titles[tabName] || 'Academic Dashboard';
        }

        // 3. Speech Synthesis
        function speakFrench(text) {
            if (!('speechSynthesis' in window)) {
                alert("Speech synthesis is not supported in this browser.");
                return;
            }
            window.speechSynthesis.cancel();
            const utter = new SpeechSynthesisUtterance(text);
            utter.lang = 'fr-FR';
            utter.rate = 0.9;
            window.speechSynthesis.speak(utter);
        }

        function playLessonAudio() {
            speakFrench(currentLessonAudioPhrase);
        }

        // 4. Render Enhanced Vocabulary Cards (With Hindi & Use Cases)
        function renderDictionary(words) {
            const container = document.getElementById('dictionary-grid');
            if (!container) return;

            if (!words || words.length === 0) {
                container.innerHTML = `
                    <div class="col-span-full py-12 text-center text-slate-400">
                        <p class="text-sm">No vocabulary words matching your criteria.</p>
                    </div>
                `;
                return;
            }

            container.innerHTML = words.map(w => {
                const safeWord = (w.word || '').replace(/'/g, "\\'");
                const safeExFr = (w.example_fr || '').replace(/'/g, "\\'");
                
                return `
                <div class="fi-card p-5 border border-slate-200 dark:border-slate-800 bg-white dark:bg-[#111827] flex flex-col justify-between hover:shadow-md transition">
                    <div>
                        <!-- Top Header: Level + Category + Pronounce Button -->
                        <div class="flex items-center justify-between mb-2.5">
                            <div class="flex items-center gap-2">
                                <span class="text-[10px] font-mono font-bold px-2 py-0.5 rounded bg-red-100 text-red-800 dark:bg-red-950/60 dark:text-red-300 border border-red-200 dark:border-red-900">
                                    ${w.level || 'B1'}
                                </span>
                                <span class="text-[11px] font-semibold text-slate-500 dark:text-slate-400 truncate max-w-[130px]" title="${w.category || 'General'}">
                                    ${w.category || 'General'}
                                </span>
                            </div>
                            <button onclick="speakFrench('${safeWord}')" class="p-1.5 rounded-lg bg-red-50 dark:bg-red-950/60 text-red-600 dark:text-red-400 hover:bg-red-100 dark:hover:bg-red-900/40 transition flex-shrink-0" title="Listen to French word">
                                🔊
                            </button>
                        </div>

                        <!-- Word & Phonetics -->
                        <h4 class="font-serif text-lg font-bold text-slate-900 dark:text-white tracking-tight">${w.word}</h4>
                        <p class="text-[11px] font-mono text-red-600 dark:text-red-400 mb-2.5">${w.ipa || ''}</p>

                        <!-- Meaning: English & Hindi -->
                        <div class="space-y-1.5 mb-3.5">
                            <div class="text-xs text-slate-700 dark:text-slate-200 leading-snug">
                                <strong class="text-slate-900 dark:text-white">EN:</strong> ${w.meaning}
                            </div>
                            ${w.meaning_hi ? `
                            <div class="text-xs text-amber-900 dark:text-amber-300 bg-amber-50 dark:bg-amber-950/50 p-2 rounded-lg border border-amber-200/60 dark:border-amber-900/60 font-medium">
                                <strong class="text-amber-800 dark:text-amber-400">HI:</strong> ${w.meaning_hi}
                            </div>` : ''}
                        </div>

                        <!-- Context Sentence -->
                        ${w.example_fr ? `
                        <div class="p-3 rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-100 dark:border-slate-800 space-y-1.5 mb-3 text-xs">
                            <div class="flex items-center justify-between">
                                <span class="text-[10px] font-bold text-red-600 dark:text-red-400 uppercase font-mono">Example Sentence</span>
                                <button onclick="speakFrench('${safeExFr}')" class="text-xs text-red-500 hover:text-red-700" title="Listen Sentence">🔊</button>
                            </div>
                            <p class="font-serif italic text-slate-800 dark:text-slate-200">« ${w.example_fr} »</p>
                            ${w.example_en ? `<p class="text-[11px] text-slate-500 dark:text-slate-400 leading-tight">${w.example_en}</p>` : ''}
                            ${w.example_hi ? `<p class="text-[11px] text-amber-700 dark:text-amber-400 leading-tight">${w.example_hi}</p>` : ''}
                        </div>` : ''}

                        <!-- Practical Use Case -->
                        ${w.use_case ? `
                        <div class="p-2.5 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800/60 text-[11px] text-emerald-800 dark:text-emerald-300 mb-2">
                            <span class="font-bold flex items-center gap-1 mb-0.5">
                                <span>💡</span>
                                <span>Use Case (उपयोग):</span>
                            </span>
                            <p class="leading-relaxed">${w.use_case}</p>
                        </div>` : ''}
                    </div>

                    <!-- Footer: Source Tag -->
                    <div class="pt-2.5 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between text-[10px] text-slate-400">
                        <span>Source: <strong class="text-slate-600 dark:text-slate-300">${w.source || 'Hermes AI'}</strong></span>
                        <button onclick="speakFrench('${safeWord}')" class="text-red-600 dark:text-red-400 font-semibold hover:underline">Pronounce</button>
                    </div>
                </div>
                `;
            }).join('');
        }

        // Filter Vocabulary by Search & Level
        function filterVocabulary() {
            const q = (document.getElementById('vocab-search-input').value || '').toLowerCase().trim();
            
            const filtered = VOCAB_DATA.filter(w => {
                const matchLevel = (currentVocabFilterLevel === 'ALL' || w.level === currentVocabFilterLevel);
                if (!matchLevel) return false;

                if (!q) return true;
                const fr = (w.word || '').toLowerCase();
                const en = (w.meaning || '').toLowerCase();
                const hi = (w.meaning_hi || '').toLowerCase();
                const uc = (w.use_case || '').toLowerCase();
                const cat = (w.category || '').toLowerCase();

                return fr.includes(q) || en.includes(q) || hi.includes(q) || uc.includes(q) || cat.includes(q);
            });

            renderDictionary(filtered);
        }

        function filterVocabLevel(level) {
            currentVocabFilterLevel = level;
            document.querySelectorAll('.vocab-lvl-btn').forEach(btn => {
                btn.classList.remove('bg-slate-900', 'text-white', 'dark:bg-white', 'dark:text-slate-900');
                btn.classList.add('text-slate-600', 'dark:text-slate-400');
            });
            event.target.classList.remove('text-slate-600', 'dark:text-slate-400');
            event.target.classList.add('bg-slate-900', 'text-white', 'dark:bg-white', 'dark:text-slate-900');

            filterVocabulary();
        }

        // 5. Render Sentences
        function renderSentences(sentences) {
            const container = document.getElementById('sentences-grid');
            if (!container) return;

            container.innerHTML = sentences.map(s => `
                <div class="p-4 rounded-xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-800/40 flex items-start justify-between gap-3">
                    <div class="space-y-1">
                        <span class="text-[10px] font-mono font-bold text-red-600 dark:text-red-400 uppercase">${s.cat}</span>
                        <p class="font-serif text-sm font-bold text-slate-900 dark:text-white">${s.fr}</p>
                        <p class="text-[11px] font-mono text-slate-500 dark:text-slate-400">${s.ipa}</p>
                        <p class="text-xs text-slate-600 dark:text-slate-300">${s.en}</p>
                    </div>
                    <button onclick="speakFrench('${s.fr.replace(/'/g, "\\'")}')" class="p-2 rounded-lg bg-red-50 dark:bg-red-950/60 text-red-600 dark:text-red-400 hover:bg-red-100 transition flex-shrink-0">
                        🔊
                    </button>
                </div>
            `).join('');
        }

        // 6. Filter Courses
        function filterCourses(level) {
            document.querySelectorAll('.course-filter-btn').forEach(b => {
                b.classList.remove('bg-slate-900', 'text-white', 'dark:bg-white', 'dark:text-slate-900');
                b.classList.add('text-slate-600', 'dark:text-slate-400');
            });
            event.target.classList.remove('text-slate-600', 'dark:text-slate-400');
            event.target.classList.add('bg-slate-900', 'text-white', 'dark:bg-white', 'dark:text-slate-900');

            document.querySelectorAll('.course-card').forEach(card => {
                if (level === 'ALL' || card.getAttribute('data-level') === level) {
                    card.style.display = '';
                } else {
                    card.style.display = 'none';
                }
            });
        }

        // 7. Enroll in Course AJAX
        async function enrollInCourse(slug) {
            try {
                const res = await fetch(`/courses/${slug}/enroll`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });
                const data = await res.json();
                if (data.status === 'ok') {
                    alert(`Congratulations! ${data.message}`);
                    window.location.reload();
                }
            } catch (err) {
                alert("Could not enroll in course.");
            }
        }

        // 8. Interactive Classroom Modal Logic
        async function openClassroomModal(lessonId, courseTitle) {
            currentLessonId = lessonId;
            document.getElementById('classroom-modal').classList.remove('hidden');

            let foundLesson = null;
            for (const c of ALL_COURSES) {
                if (c.lessons) {
                    const l = c.lessons.find(x => x.id === lessonId);
                    if (l) { foundLesson = l; break; }
                }
            }

            if (!foundLesson) {
                foundLesson = {
                    title: "Classical Reading & Phonetics",
                    section_title: "Module 1: Foundations",
                    french_text: "En français, chaque phrase porte une musique singulière. Écoutez attentivement le rythme et la mélodie des mots.",
                    english_translation: "In French, every sentence carries a singular musicality. Listen closely to the rhythm and melody of the words.",
                    audio_phrase: "En français, chaque phrase porte une musique singulière.",
                    vocabulary_json: [
                        { fr: 'Musique', ipa: '/my.zik/', en: 'Music / melody', type: 'noun (f)' },
                        { fr: 'Singulier', ipa: '/sɛ̃.ɡy.lje/', en: 'Singular / unique', type: 'adjective' }
                    ],
                    quiz_question: "What musical quality does French sentence structure emphasize?",
                    quiz_options_json: ["A melodic rhythm and fluid liaison", "A harsh staccato rhythm", "No tone variation", "Monotone pronunciation"],
                    quiz_correct_index: 0
                };
            }

            currentLessonAudioPhrase = foundLesson.audio_phrase || foundLesson.french_text;

            document.getElementById('modal-course-title').textContent = courseTitle || "Académie Curriculum";
            document.getElementById('modal-lesson-title').textContent = foundLesson.title;
            document.getElementById('modal-french-text').textContent = foundLesson.french_text;
            document.getElementById('modal-english-text').textContent = foundLesson.english_translation;

            // Render Vocab
            const vocabList = document.getElementById('modal-vocab-list');
            const vData = foundLesson.vocabulary_json || [];
            vocabList.innerHTML = vData.map(v => `
                <div class="p-3 rounded-xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700">
                    <div class="font-bold text-xs text-slate-900 dark:text-white">${v.fr} <button onclick="speakFrench('${v.fr.replace(/'/g, "\\'")}')" class="text-red-600 dark:text-red-400">🔊</button></div>
                    <div class="text-[10px] font-mono text-red-700 dark:text-red-400">${v.ipa}</div>
                    <div class="text-[11px] text-slate-600 dark:text-slate-300 mt-1">${v.en}</div>
                </div>
            `).join('');

            // Render Quiz
            document.getElementById('modal-quiz-question').textContent = foundLesson.quiz_question || "Comprehension Question";
            const optsContainer = document.getElementById('modal-quiz-options');
            let opts = foundLesson.quiz_options_json;
            if (typeof opts === 'string') {
                try { opts = JSON.parse(opts); } catch(e) { opts = ["Option A", "Option B"]; }
            }
            if (!Array.isArray(opts)) opts = ["Option A", "Option B"];

            optsContainer.innerHTML = opts.map((opt, idx) => `
                <button onclick="submitQuizOption(${lessonId}, ${idx})" class="quiz-opt-btn w-full text-left p-3 rounded-xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 hover:border-red-400 text-xs font-semibold text-slate-800 dark:text-slate-200 transition">
                    ${String.fromCharCode(65 + idx)}. ${opt}
                </button>
            `).join('');

            document.getElementById('modal-quiz-feedback').classList.add('hidden');
        }

        function closeClassroomModal() {
            document.getElementById('classroom-modal').classList.add('hidden');
        }

        // Live Dynamic Streak Updater
        function updateLiveStreak(streak, activityHistory) {
            if (streak !== undefined) {
                const statStreak = document.getElementById('stat-streak-counter');
                if (statStreak) statStreak.textContent = `${streak} Days`;
                
                const topStreak = document.getElementById('topbar-streak-counter');
                if (topStreak) topStreak.textContent = `🔥 ${streak} Days`;
                
                const headlineStreak = document.getElementById('streak-headline-count');
                if (headlineStreak) headlineStreak.textContent = `${streak} Days Active`;

                const subtext = document.getElementById('stat-streak-subtext');
                if (subtext) subtext.textContent = '🔥 Streak active';
                
                const badgeStreak = document.getElementById('streak-badge-status');
                if (badgeStreak) {
                    badgeStreak.className = 'px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800 dark:bg-amber-950/60 dark:text-amber-300';
                    badgeStreak.textContent = '🔥 Active Daily Streak';
                }
            }

            if (activityHistory && activityHistory.days) {
                renderWeeklyActivityTracker(activityHistory.days);
            }
        }

        function renderWeeklyActivityTracker(days) {
            const container = document.getElementById('weekly-activity-tracker');
            if (!container || !days) return;

            container.innerHTML = days.map(d => {
                let badgeClass = 'bg-slate-100 dark:bg-slate-800 text-slate-400 dark:text-slate-500 border border-slate-200 dark:border-slate-700';
                let inner = `<span class="text-[10px] text-slate-400">✕</span>`;

                if (d.is_completed) {
                    badgeClass = 'bg-amber-500 text-white shadow-md shadow-amber-500/30 ring-2 ring-amber-400/50';
                    inner = '🔥';
                } else if (d.is_today) {
                    badgeClass = 'bg-white dark:bg-slate-800 border-2 border-dashed border-amber-500 text-amber-600 dark:text-amber-400 animate-pulse';
                    inner = d.day_number;
                } else if (d.is_future) {
                    badgeClass = 'bg-slate-100 dark:bg-slate-800/40 text-slate-400 dark:text-slate-600 border border-slate-200 dark:border-slate-800';
                    inner = `<span class="text-[10px]">${d.day_number}</span>`;
                }

                return `
                    <div class="flex flex-col items-center gap-1">
                        <span class="text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase font-mono">${d.day_name}</span>
                        <div class="w-9 h-9 rounded-xl flex items-center justify-center font-bold text-xs transition-all duration-300 relative ${badgeClass}">
                            ${inner}
                        </div>
                        <span class="text-[9px] font-mono text-slate-400">${d.day_number}</span>
                    </div>
                `;
            }).join('');
        }

        async function submitQuizOption(lessonId, index) {
            const formData = new FormData();
            formData.append('selected_index', index);

            const res = await fetch(`/lessons/${lessonId}/quiz`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: formData
            });
            const data = await res.json();
            const feedback = document.getElementById('modal-quiz-feedback');
            feedback.classList.remove('hidden');

            if (data.correct) {
                feedback.className = "text-xs font-bold pt-1 text-emerald-700 dark:text-emerald-400 bg-emerald-100 dark:bg-emerald-950/60 p-2.5 rounded-xl";
                feedback.textContent = `✓ ${data.message}`;
                updateLiveStreak(data.streak, data.activityHistory);
            } else {
                feedback.className = "text-xs font-bold pt-1 text-rose-700 dark:text-rose-400 bg-rose-100 dark:bg-rose-950/60 p-2.5 rounded-xl";
                feedback.textContent = `✗ ${data.message}`;
            }
        }

        async function markLessonComplete() {
            if (!currentLessonId) return;

            const res = await fetch(`/lessons/${currentLessonId}/complete`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            });
            const data = await res.json();
            if (data.status === 'ok') {
                updateLiveStreak(data.streak, data.activityHistory);
                document.getElementById('stat-avg-progress').textContent = `${data.progressPercent}%`;
                alert(`Excellent work! ${data.message}`);
                closeClassroomModal();
                window.location.reload();
            }
        }

        // 9. Vocabulary Add Modal & AJAX (With Hindi & Use Case)
        function openAddWordModal() {
            document.getElementById('add-word-modal').classList.remove('hidden');
        }

        function closeAddWordModal() {
            document.getElementById('add-word-modal').classList.add('hidden');
        }

        async function submitNewWord() {
            const word = document.getElementById('new-word-fr').value.trim();
            const ipa = document.getElementById('new-word-ipa').value.trim();
            const meaning = document.getElementById('new-word-meaning').value.trim();
            const meaning_hi = document.getElementById('new-word-meaning-hi').value.trim();
            const level = document.getElementById('new-word-level').value;
            const category = document.getElementById('new-word-category').value.trim();
            const example_fr = document.getElementById('new-word-example-fr').value.trim();
            const use_case = document.getElementById('new-word-use-case').value.trim();

            if (!word || !meaning) {
                alert("Please provide both the French word and English meaning.");
                return;
            }

            const formData = new FormData();
            formData.append('word', word);
            formData.append('ipa', ipa);
            formData.append('meaning', meaning);
            formData.append('meaning_hi', meaning_hi);
            formData.append('level', level);
            formData.append('category', category);
            formData.append('example_fr', example_fr);
            formData.append('use_case', use_case);

            try {
                const res = await fetch('/api/add-word', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: formData
                });
                const data = await res.json();
                if (data.status === 'ok') {
                    updateLiveStreak(data.streak, data.activityHistory);
                    const newEntry = {
                        word: word,
                        ipa: ipa || '/' + word.toLowerCase() + '/',
                        meaning: meaning,
                        meaning_hi: meaning_hi,
                        level: level,
                        category: category,
                        example_fr: example_fr,
                        use_case: use_case,
                        source: 'Personal Lexicon'
                    };
                    VOCAB_DATA.unshift(newEntry);
                    filterVocabulary();
                    closeAddWordModal();
                    
                    const badge = document.getElementById('dict-count-badge');
                    if (badge) badge.textContent = `${VOCAB_DATA.length} Words`;
                    
                    alert(`Term "${word}" added! Academic streak updated.`);
                }
            } catch (e) {
                alert("Failed to save word.");
            }
        }

        // 10. Scholars Directory Search & Dossier Modal
        function filterScholarsTable() {
            const q = (document.getElementById('scholar-search-input').value || '').toLowerCase().trim();
            document.querySelectorAll('.scholar-row').forEach(row => {
                const name = row.getAttribute('data-name') || '';
                const email = row.getAttribute('data-email') || '';
                const goal = row.getAttribute('data-goal') || '';
                const country = row.getAttribute('data-country') || '';
                if (!q || name.includes(q) || email.includes(q) || goal.includes(q) || country.includes(q)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        }

        function openScholarDossier(scholarId) {
            const s = ALL_SCHOLARS.find(x => x.id === scholarId);
            if (!s) return;

            document.getElementById('dossier-modal-name').textContent = `${s.name}'s Academic Dossier`;
            document.getElementById('dossier-modal-avatar').textContent = s.name.charAt(0).toUpperCase();
            document.getElementById('dossier-modal-email').textContent = s.email;
            document.getElementById('dossier-modal-level').textContent = s.level;
            document.getElementById('dossier-modal-country').textContent = s.country === 'Canada' ? '🇨🇦 Canada' : (s.country === 'France' ? '🇫🇷 France' : '🇨🇭 Switzerland');
            document.getElementById('dossier-modal-goal').textContent = s.target_goal || "Master classical and conversational French.";
            document.getElementById('dossier-modal-streak').textContent = `🔥 ${s.current_streak} Days`;

            document.getElementById('scholar-dossier-modal').classList.remove('hidden');
        }

        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            sidebar.classList.toggle('-translate-x-full');
        }

        // Initialize on DOM load
        window.addEventListener('DOMContentLoaded', () => {
            renderDictionary(VOCAB_DATA);
            renderSentences(SENTENCES_DATA);
            updateDarkModeUI(document.documentElement.classList.contains('dark'));
        });
    </script>
</body>
</html>

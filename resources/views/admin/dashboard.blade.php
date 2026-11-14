<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Administration Console · Académie de Français</title>
    <script src="https://www.gstatic.com/antigravity/web/dev/tailwindcss.min.js"></script>
    <script>
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

        if (localStorage.getItem('theme') === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    </script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Merriweather:ital,wght@0,300;0,400;0,700;1,300&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .font-serif-fr { font-family: 'Merriweather', serif; }
        .tab-btn.active {
            border-bottom: 2px solid #D52B1E;
            color: #D52B1E;
            font-weight: 700;
        }
        .dark .tab-btn.active {
            border-bottom: 2px solid #ef4444;
            color: #f87171;
        }
    </style>
</head>
<body class="bg-slate-50 dark:bg-darkbg text-slate-900 dark:text-slate-100 min-h-screen flex flex-col antialiased transition-colors duration-200">

    <!-- Top Navigation Bar -->
    <header class="sticky top-0 z-30 bg-white/95 dark:bg-darkcard/95 backdrop-blur-md border-b border-slate-200 dark:border-darkborder shadow-xs">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                
                <!-- Logo & Brand -->
                <div class="flex items-center space-x-3">
                    <a href="{{ route('admin.index') }}" class="flex items-center space-x-3 group">
                        <!-- Real Official Flag of Canada -->
                        <div class="w-10 h-6 rounded shadow-xs overflow-hidden border border-slate-200 dark:border-slate-700 flex-shrink-0 transition group-hover:scale-105">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1000 500" class="w-full h-full object-cover">
                                <rect width="250" height="500" fill="#D52B1E"/>
                                <rect x="250" width="500" height="500" fill="#FFFFFF"/>
                                <rect x="750" width="250" height="500" fill="#D52B1E"/>
                                <path fill="#D52B1E" d="M 500,75 L 515,145 L 565,115 L 555,165 L 610,185 L 575,225 L 595,255 L 530,265 L 540,320 L 512,310 L 507,390 L 493,390 L 488,310 L 460,320 L 470,265 L 405,255 L 425,225 L 390,185 L 445,165 L 435,115 L 485,145 Z"/>
                            </svg>
                        </div>
                        <div>
                            <div class="flex items-center space-x-2">
                                <span class="font-bold text-base tracking-tight text-slate-900 dark:text-white">LMS Administration</span>
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-red-100 text-red-800 dark:bg-red-950/50 dark:text-red-400 border border-red-200 dark:border-red-900">
                                    Control Panel
                                </span>
                            </div>
                            <p class="text-[11px] text-slate-500 dark:text-slate-400">Académie Française Bilingual Curriculum</p>
                        </div>
                    </a>
                </div>

                <!-- Right Header Actions -->
                <div class="flex items-center space-x-3">
                    
                    <!-- Back to Student Dashboard Button -->
                    <a href="{{ route('dashboard') }}" 
                       class="inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-semibold bg-slate-100 hover:bg-slate-200 text-slate-700 dark:bg-slate-800 dark:hover:bg-slate-700 dark:text-slate-300 transition">
                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        Student Portal
                    </a>

                    <!-- Dark Mode Toggle Button -->
                    <button id="admin-dark-mode-btn" onclick="toggleDarkMode()" 
                            class="p-2 rounded-lg bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-700 transition"
                            title="Toggle Light/Dark Theme">
                        <svg id="admin-sun-icon" class="w-4 h-4 hidden dark:block text-amber-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.465 5.05l-.708-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 000 2h1z" clip-rule="evenodd"/>
                        </svg>
                        <svg id="admin-moon-icon" class="w-4 h-4 block dark:hidden text-slate-700" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"/>
                        </svg>
                    </button>

                    <!-- Admin Profile Pill -->
                    <div class="flex items-center space-x-2 pl-2 border-l border-slate-200 dark:border-darkborder">
                        <div class="w-7 h-7 rounded-full bg-red-600 text-white flex items-center justify-center font-bold text-xs shadow-xs">
                            {{ substr(Auth::user()->name, 0, 1) }}
                        </div>
                        <div class="hidden sm:block text-left">
                            <div class="text-xs font-bold text-slate-900 dark:text-white truncate max-w-[120px]">{{ Auth::user()->name }}</div>
                            <div class="text-[10px] text-slate-500 dark:text-slate-400 capitalize">{{ Auth::user()->role }}</div>
                        </div>
                    </div>

                    <!-- Logout -->
                    <form action="{{ route('logout') }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="p-2 text-slate-400 hover:text-red-600 dark:hover:text-red-400 transition" title="Log Out">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                            </svg>
                        </button>
                    </form>

                </div>
            </div>
        </div>
    </header>

    <!-- Main Content Area -->
    <main class="flex-1 max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-8">
        
        <!-- Flash Alert Messages -->
        @if(session('success'))
            <div class="mb-6 p-4 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 text-emerald-800 dark:text-emerald-300 text-xs font-semibold flex items-center justify-between shadow-xs">
                <div class="flex items-center space-x-2">
                    <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                    </svg>
                    <span>{{ session('success') }}</span>
                </div>
                <button onclick="this.parentElement.remove()" class="text-emerald-600 hover:text-emerald-900">&times;</button>
            </div>
        @endif

        @if(session('error'))
            <div class="mb-6 p-4 rounded-xl bg-red-50 dark:bg-red-950/40 border border-red-200 dark:border-red-800 text-red-800 dark:text-red-300 text-xs font-semibold flex items-center justify-between shadow-xs">
                <div class="flex items-center space-x-2">
                    <svg class="w-5 h-5 text-red-600 dark:text-red-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                    </svg>
                    <span>{{ session('error') }}</span>
                </div>
                <button onclick="this.parentElement.remove()" class="text-red-600 hover:text-red-900">&times;</button>
            </div>
        @endif

        <!-- Header Title Banner -->
        <div class="mb-8 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 dark:text-white tracking-tight">
                    Central Management Console
                </h1>
                <p class="text-xs sm:text-sm text-slate-600 dark:text-slate-400 mt-1">
                    Manage scholars, CEFR syllabi, course curriculums, quizzes, and vocabulary databases (with Hindi & Use Cases).
                </p>
            </div>
            
            <div class="flex items-center space-x-3">
                <button onclick="openAddScholarModal()" 
                        class="inline-flex items-center px-4 py-2 rounded-xl text-xs font-bold text-white bg-red-600 hover:bg-red-700 shadow-md shadow-red-600/20 transition">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                    </svg>
                    Add Scholar
                </button>
                <button onclick="openAddCourseModal()" 
                        class="inline-flex items-center px-4 py-2 rounded-xl text-xs font-bold text-slate-800 dark:text-white bg-white dark:bg-darkcard border border-slate-300 dark:border-darkborder hover:bg-slate-50 dark:hover:bg-slate-800 shadow-xs transition">
                    <svg class="w-4 h-4 mr-1.5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                    </svg>
                    New Course
                </button>
            </div>
        </div>

        <!-- Platform Metric Cards -->
        <div class="grid grid-cols-2 lg:grid-cols-5 gap-4 mb-8">
            <div class="p-4 rounded-2xl bg-white dark:bg-darkcard border border-slate-200/80 dark:border-darkborder shadow-xs">
                <div class="text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Registered Scholars</div>
                <div class="text-2xl font-black text-slate-900 dark:text-white mt-1">{{ $totalUsers }}</div>
                <div class="text-[10px] text-slate-500 mt-0.5">Multi-user database</div>
            </div>

            <div class="p-4 rounded-2xl bg-white dark:bg-darkcard border border-slate-200/80 dark:border-darkborder shadow-xs">
                <div class="text-[11px] font-bold text-amber-600 dark:text-amber-400 uppercase tracking-wider">Active Streaks</div>
                <div class="text-2xl font-black text-amber-600 dark:text-amber-400 mt-1 flex items-center">
                    <span>🔥 {{ $activeStreaksCount }}</span>
                </div>
                <div class="text-[10px] text-slate-500 mt-0.5">Scholars on daily streak</div>
            </div>

            <div class="p-4 rounded-2xl bg-white dark:bg-darkcard border border-slate-200/80 dark:border-darkborder shadow-xs">
                <div class="text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Active Courses</div>
                <div class="text-2xl font-black text-slate-900 dark:text-white mt-1">{{ $totalCourses }}</div>
                <div class="text-[10px] text-slate-500 mt-0.5">CEFR Foundations to Master</div>
            </div>

            <div class="p-4 rounded-2xl bg-white dark:bg-darkcard border border-slate-200/80 dark:border-darkborder shadow-xs">
                <div class="text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Curriculum Lessons</div>
                <div class="text-2xl font-black text-slate-900 dark:text-white mt-1">{{ $totalLessons }}</div>
                <div class="text-[10px] text-emerald-600 dark:text-emerald-400 font-semibold mt-0.5">{{ $totalCompletions }} Completions</div>
            </div>

            <div class="p-4 rounded-2xl bg-white dark:bg-darkcard border border-slate-200/80 dark:border-darkborder shadow-xs col-span-2 lg:col-span-1">
                <div class="text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Lexicon Words</div>
                <div class="text-2xl font-black text-slate-900 dark:text-white mt-1">{{ $totalWords }}</div>
                <div class="text-[10px] text-emerald-600 dark:text-emerald-400 font-semibold mt-0.5">French · English · Hindi</div>
            </div>
        </div>

        <!-- Navigation Tabs -->
        <div class="border-b border-slate-200 dark:border-darkborder mb-6">
            <nav class="flex space-x-6">
                <button onclick="switchTab('tab-users')" id="btn-tab-users" class="tab-btn pb-3 px-1 text-sm font-semibold text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white active">
                    👥 Scholars ({{ $users->count() }})
                </button>
                <button onclick="switchTab('tab-courses')" id="btn-tab-courses" class="tab-btn pb-3 px-1 text-sm font-semibold text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white">
                    📚 Courses ({{ $courses->count() }})
                </button>
                <button onclick="switchTab('tab-lessons')" id="btn-tab-lessons" class="tab-btn pb-3 px-1 text-sm font-semibold text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white">
                    📝 Lessons & Quizzes ({{ $lessons->count() }})
                </button>
                <button onclick="switchTab('tab-words')" id="btn-tab-words" class="tab-btn pb-3 px-1 text-sm font-semibold text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white">
                    📖 Lexicon & Vocabulary ({{ $words->count() }})
                </button>
            </nav>
        </div>

        <!-- TAB 1: USERS MANAGEMENT -->
        <div id="tab-users" class="tab-content">
            <div class="bg-white dark:bg-darkcard border border-slate-200 dark:border-darkborder rounded-2xl shadow-xs overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-200 dark:border-darkborder flex items-center justify-between">
                    <div>
                        <h2 class="text-sm font-bold text-slate-900 dark:text-white uppercase tracking-wider">All Registered Scholars & Accounts</h2>
                        <p class="text-xs text-slate-500 dark:text-slate-400">Edit scholar CEFR levels, manage roles, adjust streaks, or reset profiles.</p>
                    </div>
                    <span class="text-xs font-mono bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 px-2.5 py-1 rounded-md">
                        {{ $users->count() }} Accounts
                    </span>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-slate-50 dark:bg-slate-800/50 text-slate-500 dark:text-slate-400 font-semibold uppercase tracking-wider border-b border-slate-200 dark:border-darkborder">
                            <tr>
                                <th class="py-3.5 px-6">Scholar</th>
                                <th class="py-3.5 px-4">Level</th>
                                <th class="py-3.5 px-4">Role</th>
                                <th class="py-3.5 px-4">Daily Streak</th>
                                <th class="py-3.5 px-4">Courses Enrolled</th>
                                <th class="py-3.5 px-4">Completions</th>
                                <th class="py-3.5 px-4">Country</th>
                                <th class="py-3.5 px-6 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-darkborder">
                            @foreach($users as $u)
                                <tr class="hover:bg-slate-50/70 dark:hover:bg-slate-800/40 transition">
                                    <td class="py-3.5 px-6">
                                        <div class="flex items-center space-x-3">
                                            <div class="w-8 h-8 rounded-full bg-slate-200 dark:bg-slate-700 text-slate-800 dark:text-white flex items-center justify-center font-bold text-xs flex-shrink-0">
                                                {{ substr($u->name, 0, 1) }}
                                            </div>
                                            <div>
                                                <div class="font-bold text-slate-900 dark:text-white">{{ $u->name }}</div>
                                                <div class="text-[11px] text-slate-500 dark:text-slate-400">{{ $u->email }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="py-3.5 px-4">
                                        <span class="px-2 py-0.5 rounded font-mono font-bold text-[10px] 
                                            @if($u->level === 'A1') bg-rose-100 text-rose-800 dark:bg-rose-950/60 dark:text-rose-300 border border-rose-300 dark:border-rose-800
                                            @elseif($u->level === 'A2') bg-emerald-100 text-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-300 border border-emerald-300 dark:border-emerald-800
                                            @elseif($u->level === 'B1') bg-amber-100 text-amber-800 dark:bg-amber-950/60 dark:text-amber-300 border border-amber-300 dark:border-amber-800
                                            @elseif($u->level === 'B2') bg-blue-100 text-blue-800 dark:bg-blue-950/60 dark:text-blue-300 border border-blue-300 dark:border-blue-800
                                            @else bg-purple-100 text-purple-800 dark:bg-purple-950/60 dark:text-purple-300 border border-purple-300 dark:border-purple-800
                                            @endif">
                                            {{ $u->level }}
                                        </span>
                                    </td>
                                    <td class="py-3.5 px-4">
                                        <span class="px-2 py-0.5 rounded text-[10px] font-semibold capitalize
                                            @if($u->role === 'admin') bg-red-100 text-red-800 dark:bg-red-950/50 dark:text-red-300
                                            @elseif($u->role === 'instructor') bg-indigo-100 text-indigo-800 dark:bg-indigo-950/50 dark:text-indigo-300
                                            @else bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300
                                            @endif">
                                            {{ $u->role }}
                                        </span>
                                    </td>
                                    <td class="py-3.5 px-4 font-mono font-bold text-amber-600 dark:text-amber-400">
                                        🔥 {{ $u->current_streak }} Days
                                    </td>
                                    <td class="py-3.5 px-4 font-semibold text-slate-700 dark:text-slate-300">
                                        {{ $u->enrollments_count }}
                                    </td>
                                    <td class="py-3.5 px-4 font-semibold text-emerald-600 dark:text-emerald-400">
                                        {{ $u->completions_count }}
                                    </td>
                                    <td class="py-3.5 px-4 text-slate-600 dark:text-slate-400">
                                        {{ $u->country ?? 'Canada' }}
                                    </td>
                                    <td class="py-3.5 px-6 text-right space-x-2">
                                        <button onclick="openEditUserModal({{ json_encode($u) }})" 
                                                class="px-2.5 py-1 rounded bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 font-semibold text-[11px] transition">
                                            Edit
                                        </button>
                                        @if($u->id !== Auth::id())
                                            <form action="{{ route('admin.users.delete', $u->id) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this scholar?');">
                                                @csrf
                                                <button type="submit" class="px-2.5 py-1 rounded bg-red-50 hover:bg-red-100 dark:bg-red-950/40 dark:hover:bg-red-900/50 text-red-600 dark:text-red-400 font-semibold text-[11px] transition">
                                                    Delete
                                                </button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- TAB 2: COURSES MANAGEMENT -->
        <div id="tab-courses" class="tab-content hidden">
            <div class="bg-white dark:bg-darkcard border border-slate-200 dark:border-darkborder rounded-2xl shadow-xs overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-200 dark:border-darkborder flex items-center justify-between">
                    <div>
                        <h2 class="text-sm font-bold text-slate-900 dark:text-white uppercase tracking-wider">Curriculum Courses</h2>
                        <p class="text-xs text-slate-500 dark:text-slate-400">Add, edit, or adjust academic courses and CEFR tier allocations.</p>
                    </div>
                    <button onclick="openAddCourseModal()" class="px-3 py-1.5 rounded-lg bg-red-600 text-white font-bold text-xs hover:bg-red-700 transition">
                        + New Course
                    </button>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-slate-50 dark:bg-slate-800/50 text-slate-500 dark:text-slate-400 font-semibold uppercase tracking-wider border-b border-slate-200 dark:border-darkborder">
                            <tr>
                                <th class="py-3.5 px-6">Course Title</th>
                                <th class="py-3.5 px-4">Level</th>
                                <th class="py-3.5 px-4">Category</th>
                                <th class="py-3.5 px-4">Duration</th>
                                <th class="py-3.5 px-4">Lessons</th>
                                <th class="py-3.5 px-4">Instructor</th>
                                <th class="py-3.5 px-6 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-darkborder">
                            @foreach($courses as $c)
                                <tr class="hover:bg-slate-50/70 dark:hover:bg-slate-800/40 transition">
                                    <td class="py-3.5 px-6">
                                        <div class="font-bold text-slate-900 dark:text-white">{{ $c->title }}</div>
                                        <div class="text-[11px] text-slate-500 dark:text-slate-400 font-mono">{{ $c->slug }}</div>
                                    </td>
                                    <td class="py-3.5 px-4 font-mono font-bold">{{ $c->level }}</td>
                                    <td class="py-3.5 px-4 text-slate-600 dark:text-slate-400">{{ $c->category }}</td>
                                    <td class="py-3.5 px-4 font-mono text-slate-700 dark:text-slate-300">{{ $c->duration_weeks }} Weeks</td>
                                    <td class="py-3.5 px-4 font-bold text-slate-900 dark:text-white">{{ $c->lessons_count }} Lessons</td>
                                    <td class="py-3.5 px-4 text-slate-600 dark:text-slate-400">{{ $c->instructor_name }}</td>
                                    <td class="py-3.5 px-6 text-right space-x-2">
                                        <button onclick="openEditCourseModal({{ json_encode($c) }})" 
                                                class="px-2.5 py-1 rounded bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 font-semibold text-[11px] transition">
                                            Edit
                                        </button>
                                        <form action="{{ route('admin.courses.delete', $c->id) }}" method="POST" class="inline" onsubmit="return confirm('Delete this course? All associated lessons will also be removed.');">
                                            @csrf
                                            <button type="submit" class="px-2.5 py-1 rounded bg-red-50 hover:bg-red-100 dark:bg-red-950/40 dark:hover:bg-red-900/50 text-red-600 dark:text-red-400 font-semibold text-[11px] transition">
                                                Delete
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- TAB 3: LESSONS & QUIZZES MANAGEMENT -->
        <div id="tab-lessons" class="tab-content hidden">
            <div class="bg-white dark:bg-darkcard border border-slate-200 dark:border-darkborder rounded-2xl shadow-xs overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-200 dark:border-darkborder flex items-center justify-between">
                    <div>
                        <h2 class="text-sm font-bold text-slate-900 dark:text-white uppercase tracking-wider">Curriculum Lessons & Quizzes</h2>
                        <p class="text-xs text-slate-500 dark:text-slate-400">Manage lesson texts, French passages, audio pronunciation, and interactive questions.</p>
                    </div>
                    <button onclick="openAddLessonModal()" class="px-3 py-1.5 rounded-lg bg-red-600 text-white font-bold text-xs hover:bg-red-700 transition">
                        + New Lesson
                    </button>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-slate-50 dark:bg-slate-800/50 text-slate-500 dark:text-slate-400 font-semibold uppercase tracking-wider border-b border-slate-200 dark:border-darkborder">
                            <tr>
                                <th class="py-3.5 px-6">Course</th>
                                <th class="py-3.5 px-4">#</th>
                                <th class="py-3.5 px-4">Lesson Title</th>
                                <th class="py-3.5 px-4">Section</th>
                                <th class="py-3.5 px-4">Duration</th>
                                <th class="py-3.5 px-4">Quiz Question</th>
                                <th class="py-3.5 px-6 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-darkborder">
                            @foreach($lessons as $l)
                                <tr class="hover:bg-slate-50/70 dark:hover:bg-slate-800/40 transition">
                                    <td class="py-3.5 px-6 font-semibold text-slate-900 dark:text-white">
                                        {{ $l->course->title ?? 'Course #' . $l->course_id }}
                                    </td>
                                    <td class="py-3.5 px-4 font-mono font-bold text-slate-500">
                                        {{ $l->order }}
                                    </td>
                                    <td class="py-3.5 px-4 font-bold text-slate-900 dark:text-white">
                                        {{ $l->title }}
                                    </td>
                                    <td class="py-3.5 px-4 text-slate-500 dark:text-slate-400">
                                        {{ $l->section_title }}
                                    </td>
                                    <td class="py-3.5 px-4 font-mono">
                                        {{ $l->duration_mins }} min
                                    </td>
                                    <td class="py-3.5 px-4 text-slate-600 dark:text-slate-400 max-w-[200px] truncate">
                                        {{ $l->quiz_question }}
                                    </td>
                                    <td class="py-3.5 px-6 text-right space-x-2">
                                        <button onclick="openEditLessonModal({{ json_encode($l) }})" 
                                                class="px-2.5 py-1 rounded bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 font-semibold text-[11px] transition">
                                            Edit
                                        </button>
                                        <form action="{{ route('admin.lessons.delete', $l->id) }}" method="POST" class="inline" onsubmit="return confirm('Delete this lesson?');">
                                            @csrf
                                            <button type="submit" class="px-2.5 py-1 rounded bg-red-50 hover:bg-red-100 dark:bg-red-950/40 dark:hover:bg-red-900/50 text-red-600 dark:text-red-400 font-semibold text-[11px] transition">
                                                Delete
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- TAB 4: LEXICON & VOCABULARY (With Hindi, English & Use Cases) -->
        <div id="tab-words" class="tab-content hidden">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                
                <!-- Add Word Form -->
                <div class="bg-white dark:bg-darkcard border border-slate-200 dark:border-darkborder rounded-2xl p-6 shadow-xs h-fit">
                    <div class="flex items-center justify-between mb-2">
                        <h3 class="text-sm font-bold text-slate-900 dark:text-white uppercase tracking-wider">Add Lexicon Entry</h3>
                        <span class="text-[10px] font-mono px-2 py-0.5 rounded bg-red-100 dark:bg-red-950/60 text-red-700 dark:text-red-400 font-bold">FR · EN · HI</span>
                    </div>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mb-4">Add a new French term, IPA phonetics, English & Hindi meanings, and practical use cases.</p>
                    
                    <form action="{{ route('admin.words.store') }}" method="POST" class="space-y-3.5">
                        @csrf
                        <div>
                            <label class="block text-[11px] font-bold text-slate-700 dark:text-slate-300 mb-1">French Term *</label>
                            <input type="text" name="word" required placeholder="e.g. Bienveillance" 
                                   class="w-full px-3 py-2 rounded-xl text-xs bg-slate-50 dark:bg-darkinput border border-slate-200 dark:border-darkborder text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-red-500">
                        </div>

                        <div>
                            <label class="block text-[11px] font-bold text-slate-700 dark:text-slate-300 mb-1">IPA Phonetics</label>
                            <input type="text" name="ipa" placeholder="e.g. /bjɛ̃.vɛ.jɑ̃s/" 
                                   class="w-full px-3 py-2 rounded-xl text-xs bg-slate-50 dark:bg-darkinput border border-slate-200 dark:border-darkborder text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-red-500">
                        </div>

                        <div>
                            <label class="block text-[11px] font-bold text-slate-700 dark:text-slate-300 mb-1">English Meaning *</label>
                            <input type="text" name="meaning" required placeholder="e.g. Goodwill, kindness, benevolence" 
                                   class="w-full px-3 py-2 rounded-xl text-xs bg-slate-50 dark:bg-darkinput border border-slate-200 dark:border-darkborder text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-red-500">
                        </div>

                        <div>
                            <label class="block text-[11px] font-bold text-slate-700 dark:text-slate-300 mb-1">Hindi Meaning (हिंदी अर्थ)</label>
                            <input type="text" name="meaning_hi" placeholder="उदा. सद्भावना, परोपकार, कृपाशीलता" 
                                   class="w-full px-3 py-2 rounded-xl text-xs bg-slate-50 dark:bg-darkinput border border-slate-200 dark:border-darkborder text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-red-500">
                        </div>

                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-[11px] font-bold text-slate-700 dark:text-slate-300 mb-1">CEFR Level</label>
                                <select name="level" class="w-full px-3 py-2 rounded-xl text-xs bg-slate-50 dark:bg-darkinput border border-slate-200 dark:border-darkborder text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-red-500">
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
                                <input type="text" name="category" value="Classical Ethics" 
                                       class="w-full px-3 py-2 rounded-xl text-xs bg-slate-50 dark:bg-darkinput border border-slate-200 dark:border-darkborder text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-red-500">
                            </div>
                        </div>

                        <div>
                            <label class="block text-[11px] font-bold text-slate-700 dark:text-slate-300 mb-1">French Example Sentence</label>
                            <textarea name="example_fr" rows="2" placeholder="e.g. Le sage écouta le jeune voyageur avec une infinie bienveillance." 
                                      class="w-full px-3 py-2 rounded-xl text-xs bg-slate-50 dark:bg-darkinput border border-slate-200 dark:border-darkborder text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-red-500 font-serif-fr"></textarea>
                        </div>

                        <div>
                            <label class="block text-[11px] font-bold text-slate-700 dark:text-slate-300 mb-1">English Example Translation</label>
                            <input type="text" name="example_en" placeholder="e.g. The sage listened with infinite kindness." 
                                   class="w-full px-3 py-2 rounded-xl text-xs bg-slate-50 dark:bg-darkinput border border-slate-200 dark:border-darkborder text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-red-500">
                        </div>

                        <div>
                            <label class="block text-[11px] font-bold text-slate-700 dark:text-slate-300 mb-1">Hindi Example Translation (हिंदी अनुवाद)</label>
                            <input type="text" name="example_hi" placeholder="उदा. ज्ञानी ने अनंत दयालुता के साथ सुना।" 
                                   class="w-full px-3 py-2 rounded-xl text-xs bg-slate-50 dark:bg-darkinput border border-slate-200 dark:border-darkborder text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-red-500">
                        </div>

                        <div>
                            <label class="block text-[11px] font-bold text-slate-700 dark:text-slate-300 mb-1">Practical Use Case (कहाँ और कैसे इस्तेमाल होगा?)</label>
                            <textarea name="use_case" rows="2" placeholder="e.g. Used in formal correspondence, educational guidance, or when describing empathetic leadership." 
                                      class="w-full px-3 py-2 rounded-xl text-xs bg-slate-50 dark:bg-darkinput border border-slate-200 dark:border-darkborder text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-red-500"></textarea>
                        </div>

                        <button type="submit" class="w-full mt-2 py-2.5 rounded-xl bg-red-600 text-white font-bold text-xs hover:bg-red-700 shadow-md shadow-red-600/20 transition">
                            Save Term to Database
                        </button>
                    </form>
                </div>

                <!-- Word List Table with Hindi & Use Cases -->
                <div class="lg:col-span-2 bg-white dark:bg-darkcard border border-slate-200 dark:border-darkborder rounded-2xl shadow-xs overflow-hidden flex flex-col justify-between">
                    <div>
                        <div class="px-6 py-4 border-b border-slate-200 dark:border-darkborder flex items-center justify-between">
                            <div>
                                <h3 class="text-sm font-bold text-slate-900 dark:text-white uppercase tracking-wider">Database Lexicon Terms</h3>
                                <p class="text-xs text-slate-500 dark:text-slate-400">Searchable repository of words with Hindi meanings and real-life use cases.</p>
                            </div>
                            <span class="text-xs font-mono bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 px-2.5 py-1 rounded-md">
                                {{ $words->count() }} Words
                            </span>
                        </div>

                        <div class="overflow-x-auto max-h-[650px]">
                            <table class="w-full text-left text-xs">
                                <thead class="bg-slate-50 dark:bg-slate-800/50 text-slate-500 dark:text-slate-400 font-semibold uppercase tracking-wider border-b border-slate-200 dark:border-darkborder sticky top-0">
                                    <tr>
                                        <th class="py-3 px-4">Term & IPA</th>
                                        <th class="py-3 px-4">Meanings (EN & HI)</th>
                                        <th class="py-3 px-4">Level</th>
                                        <th class="py-3 px-4">Context Sentence & Use Case</th>
                                        <th class="py-3 px-4">Source</th>
                                        <th class="py-3 px-4 text-right">Action</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100 dark:divide-darkborder">
                                    @forelse($words as $w)
                                        <tr class="hover:bg-slate-50/70 dark:hover:bg-slate-800/40 transition">
                                            <td class="py-3 px-4">
                                                <div class="font-bold text-slate-900 dark:text-white font-serif-fr text-sm">{{ $w->word }}</div>
                                                <div class="font-mono text-[10px] text-red-600 dark:text-red-400">{{ $w->ipa }}</div>
                                            </td>
                                            <td class="py-3 px-4 space-y-1">
                                                <div class="font-medium text-slate-800 dark:text-slate-200">{{ $w->meaning }}</div>
                                                @if($w->meaning_hi)
                                                    <div class="text-[11px] font-semibold text-amber-700 dark:text-amber-400 bg-amber-50 dark:bg-amber-950/40 px-2 py-0.5 rounded border border-amber-200/50 dark:border-amber-900/40">
                                                        {{ $w->meaning_hi }}
                                                    </div>
                                                @endif
                                            </td>
                                            <td class="py-3 px-4 font-mono font-bold">
                                                <span class="px-2 py-0.5 rounded text-[10px] bg-red-100 dark:bg-red-950/60 text-red-800 dark:text-red-300">
                                                    {{ $w->level }}
                                                </span>
                                            </td>
                                            <td class="py-3 px-4 space-y-1 max-w-[280px]">
                                                @if($w->example_fr)
                                                    <div class="italic text-slate-700 dark:text-slate-300 font-serif-fr text-[11px]">« {{ $w->example_fr }} »</div>
                                                @endif
                                                @if($w->use_case)
                                                    <div class="text-[10px] text-emerald-700 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-950/30 p-1.5 rounded">
                                                        💡 <strong>Use Case:</strong> {{ $w->use_case }}
                                                    </div>
                                                @endif
                                            </td>
                                            <td class="py-3 px-4 text-slate-500 font-mono text-[10px]">
                                                {{ $w->source ?? 'Hermes AI' }}
                                            </td>
                                            <td class="py-3 px-4 text-right space-x-1.5">
                                                <button onclick="openEditWordModal({{ json_encode($w) }})" 
                                                        class="text-indigo-600 hover:text-indigo-800 dark:text-indigo-400 text-xs font-semibold">
                                                    Edit
                                                </button>
                                                <form action="{{ route('admin.words.delete', $w->id) }}" method="POST" class="inline" onsubmit="return confirm('Delete this term?');">
                                                    @csrf
                                                    <button type="submit" class="text-red-600 hover:text-red-800 dark:text-red-400 text-xs font-semibold">
                                                        Delete
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="py-8 text-center text-slate-400">No words found in database.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div>
        </div>

    </main>

    <!-- MODAL: ADD SCHOLAR -->
    <div id="modal-add-scholar" class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center hidden p-4">
        <div class="bg-white dark:bg-darkcard border border-slate-200 dark:border-darkborder rounded-2xl max-w-md w-full p-6 shadow-2xl">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-base font-bold text-slate-900 dark:text-white">Add New Scholar</h3>
                <button onclick="closeModal('modal-add-scholar')" class="text-slate-400 hover:text-slate-600 dark:hover:text-white text-lg">&times;</button>
            </div>
            
            <form action="{{ route('admin.users.store') }}" method="POST" class="space-y-3.5">
                @csrf
                <div>
                    <label class="block text-[11px] font-bold text-slate-700 dark:text-slate-300 mb-1">Full Name *</label>
                    <input type="text" name="name" required placeholder="e.g. Marcel Proust" 
                           class="w-full px-3 py-2 rounded-xl text-xs bg-slate-50 dark:bg-darkinput border border-slate-200 dark:border-darkborder text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-red-500">
                </div>

                <div>
                    <label class="block text-[11px] font-bold text-slate-700 dark:text-slate-300 mb-1">Email Address *</label>
                    <input type="email" name="email" required placeholder="proust@academie.fr" 
                           class="w-full px-3 py-2 rounded-xl text-xs bg-slate-50 dark:bg-darkinput border border-slate-200 dark:border-darkborder text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-red-500">
                </div>

                <div>
                    <label class="block text-[11px] font-bold text-slate-700 dark:text-slate-300 mb-1">Password *</label>
                    <input type="password" name="password" required placeholder="Minimum 6 characters" 
                           class="w-full px-3 py-2 rounded-xl text-xs bg-slate-50 dark:bg-darkinput border border-slate-200 dark:border-darkborder text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-red-500">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-[11px] font-bold text-slate-700 dark:text-slate-300 mb-1">CEFR Level</label>
                        <select name="level" class="w-full px-3 py-2 rounded-xl text-xs bg-slate-50 dark:bg-darkinput border border-slate-200 dark:border-darkborder text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-red-500">
                            <option value="A1" selected>A1 · Beginner</option>
                            <option value="A2">A2 · Elementary</option>
                            <option value="B1">B1 · Intermediate</option>
                            <option value="B2">B2 · Upper-Int</option>
                            <option value="C1">C1 · Advanced</option>
                            <option value="C2">C2 · Mastery</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-[11px] font-bold text-slate-700 dark:text-slate-300 mb-1">Role</label>
                        <select name="role" class="w-full px-3 py-2 rounded-xl text-xs bg-slate-50 dark:bg-darkinput border border-slate-200 dark:border-darkborder text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-red-500">
                            <option value="student" selected>Student</option>
                            <option value="instructor">Instructor</option>
                            <option value="admin">Administrator</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-[11px] font-bold text-slate-700 dark:text-slate-300 mb-1">Country</label>
                        <input type="text" name="country" value="Canada" 
                               class="w-full px-3 py-2 rounded-xl text-xs bg-slate-50 dark:bg-darkinput border border-slate-200 dark:border-darkborder text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-red-500">
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-slate-700 dark:text-slate-300 mb-1">Initial Streak</label>
                        <input type="number" name="current_streak" value="0" min="0" 
                               class="w-full px-3 py-2 rounded-xl text-xs bg-slate-50 dark:bg-darkinput border border-slate-200 dark:border-darkborder text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-red-500">
                    </div>
                </div>

                <div class="flex items-center justify-end space-x-2 pt-3">
                    <button type="button" onclick="closeModal('modal-add-scholar')" class="px-3.5 py-2 rounded-xl text-xs font-semibold text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800">
                        Cancel
                    </button>
                    <button type="submit" class="px-4 py-2 rounded-xl text-xs font-bold bg-red-600 text-white hover:bg-red-700 transition">
                        Create Scholar
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL: EDIT SCHOLAR -->
    <div id="modal-edit-user" class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center hidden p-4">
        <div class="bg-white dark:bg-darkcard border border-slate-200 dark:border-darkborder rounded-2xl max-w-md w-full p-6 shadow-2xl">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-base font-bold text-slate-900 dark:text-white">Edit Scholar Details</h3>
                <button onclick="closeModal('modal-edit-user')" class="text-slate-400 hover:text-slate-600 dark:hover:text-white text-lg">&times;</button>
            </div>
            
            <form id="edit-user-form" method="POST" class="space-y-3.5">
                @csrf
                <div>
                    <label class="block text-[11px] font-bold text-slate-700 dark:text-slate-300 mb-1">Full Name *</label>
                    <input type="text" id="edit-user-name" name="name" required 
                           class="w-full px-3 py-2 rounded-xl text-xs bg-slate-50 dark:bg-darkinput border border-slate-200 dark:border-darkborder text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-red-500">
                </div>

                <div>
                    <label class="block text-[11px] font-bold text-slate-700 dark:text-slate-300 mb-1">Email Address *</label>
                    <input type="email" id="edit-user-email" name="email" required 
                           class="w-full px-3 py-2 rounded-xl text-xs bg-slate-50 dark:bg-darkinput border border-slate-200 dark:border-darkborder text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-red-500">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-[11px] font-bold text-slate-700 dark:text-slate-300 mb-1">CEFR Level</label>
                        <select id="edit-user-level" name="level" class="w-full px-3 py-2 rounded-xl text-xs bg-slate-50 dark:bg-darkinput border border-slate-200 dark:border-darkborder text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-red-500">
                            <option value="A1">A1 · Beginner</option>
                            <option value="A2">A2 · Elementary</option>
                            <option value="B1">B1 · Intermediate</option>
                            <option value="B2">B2 · Upper-Int</option>
                            <option value="C1">C1 · Advanced</option>
                            <option value="C2">C2 · Mastery</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-[11px] font-bold text-slate-700 dark:text-slate-300 mb-1">Role</label>
                        <select id="edit-user-role" name="role" class="w-full px-3 py-2 rounded-xl text-xs bg-slate-50 dark:bg-darkinput border border-slate-200 dark:border-darkborder text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-red-500">
                            <option value="student">Student</option>
                            <option value="instructor">Instructor</option>
                            <option value="admin">Administrator</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-[11px] font-bold text-slate-700 dark:text-slate-300 mb-1">Daily Streak (Days)</label>
                        <input type="number" id="edit-user-streak" name="current_streak" min="0" required
                               class="w-full px-3 py-2 rounded-xl text-xs bg-slate-50 dark:bg-darkinput border border-slate-200 dark:border-darkborder text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-red-500">
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-slate-700 dark:text-slate-300 mb-1">Country</label>
                        <input type="text" id="edit-user-country" name="country" 
                               class="w-full px-3 py-2 rounded-xl text-xs bg-slate-50 dark:bg-darkinput border border-slate-200 dark:border-darkborder text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-red-500">
                    </div>
                </div>

                <div>
                    <label class="block text-[11px] font-bold text-slate-700 dark:text-slate-300 mb-1">New Password (leave empty to keep current)</label>
                    <input type="password" name="password" placeholder="••••••••" 
                           class="w-full px-3 py-2 rounded-xl text-xs bg-slate-50 dark:bg-darkinput border border-slate-200 dark:border-darkborder text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-red-500">
                </div>

                <div class="flex items-center justify-end space-x-2 pt-3">
                    <button type="button" onclick="closeModal('modal-edit-user')" class="px-3.5 py-2 rounded-xl text-xs font-semibold text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800">
                        Cancel
                    </button>
                    <button type="submit" class="px-4 py-2 rounded-xl text-xs font-bold bg-red-600 text-white hover:bg-red-700 transition">
                        Update Scholar
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL: ADD COURSE -->
    <div id="modal-add-course" class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center hidden p-4">
        <div class="bg-white dark:bg-darkcard border border-slate-200 dark:border-darkborder rounded-2xl max-w-lg w-full p-6 shadow-2xl">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-base font-bold text-slate-900 dark:text-white">Create Academic Course</h3>
                <button onclick="closeModal('modal-add-course')" class="text-slate-400 hover:text-slate-600 dark:hover:text-white text-lg">&times;</button>
            </div>
            
            <form action="{{ route('admin.courses.store') }}" method="POST" class="space-y-3.5">
                @csrf
                <div>
                    <label class="block text-[11px] font-bold text-slate-700 dark:text-slate-300 mb-1">Course Title *</label>
                    <input type="text" name="title" required placeholder="e.g. French 102 · Everyday Conversations" 
                           class="w-full px-3 py-2 rounded-xl text-xs bg-slate-50 dark:bg-darkinput border border-slate-200 dark:border-darkborder text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-red-500">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-[11px] font-bold text-slate-700 dark:text-slate-300 mb-1">CEFR Level</label>
                        <select name="level" class="w-full px-3 py-2 rounded-xl text-xs bg-slate-50 dark:bg-darkinput border border-slate-200 dark:border-darkborder text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-red-500">
                            <option value="A1">A1</option>
                            <option value="A2" selected>A2</option>
                            <option value="B1">B1</option>
                            <option value="B2">B2</option>
                            <option value="C1">C1</option>
                            <option value="C2">C2</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-slate-700 dark:text-slate-300 mb-1">Category</label>
                        <input type="text" name="category" required value="Conversational" 
                               class="w-full px-3 py-2 rounded-xl text-xs bg-slate-50 dark:bg-darkinput border border-slate-200 dark:border-darkborder text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-red-500">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-[11px] font-bold text-slate-700 dark:text-slate-300 mb-1">Instructor Name *</label>
                        <input type="text" name="instructor_name" required value="Prof. Émile Laurent" 
                               class="w-full px-3 py-2 rounded-xl text-xs bg-slate-50 dark:bg-darkinput border border-slate-200 dark:border-darkborder text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-red-500">
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-slate-700 dark:text-slate-300 mb-1">Duration (Weeks)</label>
                        <input type="number" name="duration_weeks" value="6" min="1" 
                               class="w-full px-3 py-2 rounded-xl text-xs bg-slate-50 dark:bg-darkinput border border-slate-200 dark:border-darkborder text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-red-500">
                    </div>
                </div>

                <div>
                    <label class="block text-[11px] font-bold text-slate-700 dark:text-slate-300 mb-1">Short Description *</label>
                    <input type="text" name="short_desc" required placeholder="Brief one-line summary" 
                           class="w-full px-3 py-2 rounded-xl text-xs bg-slate-50 dark:bg-darkinput border border-slate-200 dark:border-darkborder text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-red-500">
                </div>

                <div>
                    <label class="block text-[11px] font-bold text-slate-700 dark:text-slate-300 mb-1">Full Syllabus Description *</label>
                    <textarea name="description" rows="3" required placeholder="Comprehensive curriculum description..."
                              class="w-full px-3 py-2 rounded-xl text-xs bg-slate-50 dark:bg-darkinput border border-slate-200 dark:border-darkborder text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-red-500"></textarea>
                </div>

                <div class="flex items-center justify-end space-x-2 pt-3">
                    <button type="button" onclick="closeModal('modal-add-course')" class="px-3.5 py-2 rounded-xl text-xs font-semibold text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800">
                        Cancel
                    </button>
                    <button type="submit" class="px-4 py-2 rounded-xl text-xs font-bold bg-red-600 text-white hover:bg-red-700 transition">
                        Create Course
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL: ADD LESSON -->
    <div id="modal-add-lesson" class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center hidden p-4 overflow-y-auto">
        <div class="bg-white dark:bg-darkcard border border-slate-200 dark:border-darkborder rounded-2xl max-w-xl w-full p-6 shadow-2xl my-8">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-base font-bold text-slate-900 dark:text-white">Add Curriculum Lesson & Interactive Quiz</h3>
                <button onclick="closeModal('modal-add-lesson')" class="text-slate-400 hover:text-slate-600 dark:hover:text-white text-lg">&times;</button>
            </div>
            
            <form action="{{ route('admin.lessons.store') }}" method="POST" class="space-y-3.5">
                @csrf
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-[11px] font-bold text-slate-700 dark:text-slate-300 mb-1">Course *</label>
                        <select name="course_id" required class="w-full px-3 py-2 rounded-xl text-xs bg-slate-50 dark:bg-darkinput border border-slate-200 dark:border-darkborder text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-red-500">
                            @foreach($courses as $c)
                                <option value="{{ $c->id }}">{{ $c->title }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-slate-700 dark:text-slate-300 mb-1">Lesson Order #</label>
                        <input type="number" name="order" value="1" min="1" required
                               class="w-full px-3 py-2 rounded-xl text-xs bg-slate-50 dark:bg-darkinput border border-slate-200 dark:border-darkborder text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-red-500">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-[11px] font-bold text-slate-700 dark:text-slate-300 mb-1">Lesson Title *</label>
                        <input type="text" name="title" required placeholder="e.g. Master the French R & Nasal Vowels" 
                               class="w-full px-3 py-2 rounded-xl text-xs bg-slate-50 dark:bg-darkinput border border-slate-200 dark:border-darkborder text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-red-500">
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-slate-700 dark:text-slate-300 mb-1">Section Title *</label>
                        <input type="text" name="section_title" required value="Phonetics & Pronunciation" 
                               class="w-full px-3 py-2 rounded-xl text-xs bg-slate-50 dark:bg-darkinput border border-slate-200 dark:border-darkborder text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-red-500">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-[11px] font-bold text-slate-700 dark:text-slate-300 mb-1">Duration (Minutes)</label>
                        <input type="number" name="duration_mins" value="15" min="1" required
                               class="w-full px-3 py-2 rounded-xl text-xs bg-slate-50 dark:bg-darkinput border border-slate-200 dark:border-darkborder text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-red-500">
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-slate-700 dark:text-slate-300 mb-1">Audio Phrase (for TTS)</label>
                        <input type="text" name="audio_phrase" placeholder="Bonjour, enchanté de faire votre connaissance." 
                               class="w-full px-3 py-2 rounded-xl text-xs bg-slate-50 dark:bg-darkinput border border-slate-200 dark:border-darkborder text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-red-500">
                    </div>
                </div>

                <div>
                    <label class="block text-[11px] font-bold text-slate-700 dark:text-slate-300 mb-1">French Passage / Core Teaching *</label>
                    <textarea name="french_text" rows="3" required placeholder="Original French reading text..."
                              class="w-full px-3 py-2 rounded-xl text-xs bg-slate-50 dark:bg-darkinput border border-slate-200 dark:border-darkborder text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-red-500 font-serif-fr"></textarea>
                </div>

                <div>
                    <label class="block text-[11px] font-bold text-slate-700 dark:text-slate-300 mb-1">English Translation *</label>
                    <textarea name="english_translation" rows="3" required placeholder="Academic English line-by-line translation..."
                              class="w-full px-3 py-2 rounded-xl text-xs bg-slate-50 dark:bg-darkinput border border-slate-200 dark:border-darkborder text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-red-500"></textarea>
                </div>

                <!-- Quiz Builder -->
                <div class="p-3.5 rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-darkborder space-y-2.5">
                    <div class="text-xs font-bold text-slate-900 dark:text-white flex items-center justify-between">
                        <span>Interactive Quiz Question</span>
                        <span class="text-[10px] text-red-600 font-mono">Streak task trigger</span>
                    </div>

                    <input type="text" name="quiz_question" required placeholder="e.g. Which nasal vowel appears in the word 'pain'?" 
                           class="w-full px-3 py-2 rounded-xl text-xs bg-white dark:bg-darkinput border border-slate-200 dark:border-darkborder text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-red-500">

                    <div class="grid grid-cols-2 gap-2 text-xs">
                        <input type="text" name="quiz_option_0" required placeholder="Option 1 (e.g. [ɛ̃])" 
                               class="px-2.5 py-1.5 rounded-lg bg-white dark:bg-darkinput border border-slate-200 dark:border-darkborder">
                        <input type="text" name="quiz_option_1" required placeholder="Option 2 (e.g. [ɑ̃])" 
                               class="px-2.5 py-1.5 rounded-lg bg-white dark:bg-darkinput border border-slate-200 dark:border-darkborder">
                        <input type="text" name="quiz_option_2" required placeholder="Option 3 (e.g. [ɔ̃])" 
                               class="px-2.5 py-1.5 rounded-lg bg-white dark:bg-darkinput border border-slate-200 dark:border-darkborder">
                        <input type="text" name="quiz_option_3" required placeholder="Option 4 (e.g. [œ̃])" 
                               class="px-2.5 py-1.5 rounded-lg bg-white dark:bg-darkinput border border-slate-200 dark:border-darkborder">
                    </div>

                    <div class="flex items-center justify-between pt-1">
                        <span class="text-[11px] font-semibold text-slate-600 dark:text-slate-400">Correct Answer Index:</span>
                        <select name="quiz_correct_index" class="px-2.5 py-1 rounded-lg text-xs bg-white dark:bg-darkinput border border-slate-200 dark:border-darkborder text-slate-900 dark:text-white">
                            <option value="0">Option 1</option>
                            <option value="1">Option 2</option>
                            <option value="2">Option 3</option>
                            <option value="3">Option 4</option>
                        </select>
                    </div>
                </div>

                <div class="flex items-center justify-end space-x-2 pt-3">
                    <button type="button" onclick="closeModal('modal-add-lesson')" class="px-3.5 py-2 rounded-xl text-xs font-semibold text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800">
                        Cancel
                    </button>
                    <button type="submit" class="px-4 py-2 rounded-xl text-xs font-bold bg-red-600 text-white hover:bg-red-700 transition">
                        Save Lesson & Quiz
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL: EDIT WORD -->
    <div id="modal-edit-word" class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center hidden p-4 overflow-y-auto">
        <div class="bg-white dark:bg-darkcard border border-slate-200 dark:border-darkborder rounded-2xl max-w-lg w-full p-6 shadow-2xl my-8">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-base font-bold text-slate-900 dark:text-white">Edit Vocabulary Term</h3>
                <button onclick="closeModal('modal-edit-word')" class="text-slate-400 hover:text-slate-600 dark:hover:text-white text-lg">&times;</button>
            </div>
            
            <form id="edit-word-form" method="POST" class="space-y-3.5">
                @csrf
                <div>
                    <label class="block text-[11px] font-bold text-slate-700 dark:text-slate-300 mb-1">French Term *</label>
                    <input type="text" id="edit-word-term" name="word" required 
                           class="w-full px-3 py-2 rounded-xl text-xs bg-slate-50 dark:bg-darkinput border border-slate-200 dark:border-darkborder text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-red-500">
                </div>

                <div>
                    <label class="block text-[11px] font-bold text-slate-700 dark:text-slate-300 mb-1">IPA Phonetics</label>
                    <input type="text" id="edit-word-ipa" name="ipa" 
                           class="w-full px-3 py-2 rounded-xl text-xs bg-slate-50 dark:bg-darkinput border border-slate-200 dark:border-darkborder text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-red-500">
                </div>

                <div>
                    <label class="block text-[11px] font-bold text-slate-700 dark:text-slate-300 mb-1">English Meaning *</label>
                    <input type="text" id="edit-word-meaning" name="meaning" required 
                           class="w-full px-3 py-2 rounded-xl text-xs bg-slate-50 dark:bg-darkinput border border-slate-200 dark:border-darkborder text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-red-500">
                </div>

                <div>
                    <label class="block text-[11px] font-bold text-slate-700 dark:text-slate-300 mb-1">Hindi Meaning (हिंदी अर्थ)</label>
                    <input type="text" id="edit-word-meaning-hi" name="meaning_hi" 
                           class="w-full px-3 py-2 rounded-xl text-xs bg-slate-50 dark:bg-darkinput border border-slate-200 dark:border-darkborder text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-red-500">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-[11px] font-bold text-slate-700 dark:text-slate-300 mb-1">CEFR Level</label>
                        <select id="edit-word-level" name="level" class="w-full px-3 py-2 rounded-xl text-xs bg-slate-50 dark:bg-darkinput border border-slate-200 dark:border-darkborder text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-red-500">
                            <option value="A1">A1</option>
                            <option value="A2">A2</option>
                            <option value="B1">B1</option>
                            <option value="B2">B2</option>
                            <option value="C1">C1</option>
                            <option value="C2">C2</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-slate-700 dark:text-slate-300 mb-1">Category</label>
                        <input type="text" id="edit-word-category" name="category" 
                               class="w-full px-3 py-2 rounded-xl text-xs bg-slate-50 dark:bg-darkinput border border-slate-200 dark:border-darkborder text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-red-500">
                    </div>
                </div>

                <div>
                    <label class="block text-[11px] font-bold text-slate-700 dark:text-slate-300 mb-1">French Example Sentence</label>
                    <textarea id="edit-word-example-fr" name="example_fr" rows="2" 
                              class="w-full px-3 py-2 rounded-xl text-xs bg-slate-50 dark:bg-darkinput border border-slate-200 dark:border-darkborder text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-red-500 font-serif-fr"></textarea>
                </div>

                <div>
                    <label class="block text-[11px] font-bold text-slate-700 dark:text-slate-300 mb-1">English Example Translation</label>
                    <input type="text" id="edit-word-example-en" name="example_en" 
                           class="w-full px-3 py-2 rounded-xl text-xs bg-slate-50 dark:bg-darkinput border border-slate-200 dark:border-darkborder text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-red-500">
                </div>

                <div>
                    <label class="block text-[11px] font-bold text-slate-700 dark:text-slate-300 mb-1">Hindi Example Translation (हिंदी अनुवाद)</label>
                    <input type="text" id="edit-word-example-hi" name="example_hi" 
                           class="w-full px-3 py-2 rounded-xl text-xs bg-slate-50 dark:bg-darkinput border border-slate-200 dark:border-darkborder text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-red-500">
                </div>

                <div>
                    <label class="block text-[11px] font-bold text-slate-700 dark:text-slate-300 mb-1">Practical Use Case (कहाँ और कैसे इस्तेमाल होगा?)</label>
                    <textarea id="edit-word-use-case" name="use_case" rows="2" 
                              class="w-full px-3 py-2 rounded-xl text-xs bg-slate-50 dark:bg-darkinput border border-slate-200 dark:border-darkborder text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-red-500"></textarea>
                </div>

                <div class="flex items-center justify-end space-x-2 pt-3">
                    <button type="button" onclick="closeModal('modal-edit-word')" class="px-3.5 py-2 rounded-xl text-xs font-semibold text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800">
                        Cancel
                    </button>
                    <button type="submit" class="px-4 py-2 rounded-xl text-xs font-bold bg-red-600 text-white hover:bg-red-700 transition">
                        Update Word
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function switchTab(tabId) {
            document.querySelectorAll('.tab-content').forEach(el => el.classList.add('hidden'));
            document.querySelectorAll('.tab-btn').forEach(btn => btn.classList.remove('active'));

            const targetTab = document.getElementById(tabId);
            if (targetTab) {
                targetTab.classList.remove('hidden');
            }

            const activeBtn = document.getElementById('btn-' + tabId);
            if (activeBtn) {
                activeBtn.classList.add('active');
            }
        }

        function closeModal(modalId) {
            document.getElementById(modalId).classList.add('hidden');
        }

        function openAddScholarModal() {
            document.getElementById('modal-add-scholar').classList.remove('hidden');
        }

        function openAddCourseModal() {
            document.getElementById('modal-add-course').classList.remove('hidden');
        }

        function openAddLessonModal() {
            document.getElementById('modal-add-lesson').classList.remove('hidden');
        }

        function openEditUserModal(user) {
            document.getElementById('edit-user-form').action = `/admin/users/${user.id}/update`;
            document.getElementById('edit-user-name').value = user.name;
            document.getElementById('edit-user-email').value = user.email;
            document.getElementById('edit-user-level').value = user.level;
            document.getElementById('edit-user-role').value = user.role;
            document.getElementById('edit-user-streak').value = user.current_streak;
            document.getElementById('edit-user-country').value = user.country || 'Canada';

            document.getElementById('modal-edit-user').classList.remove('hidden');
        }

        function openEditWordModal(word) {
            document.getElementById('edit-word-form').action = `/admin/words/${word.id}/update`;
            document.getElementById('edit-word-term').value = word.word;
            document.getElementById('edit-word-ipa').value = word.ipa || '';
            document.getElementById('edit-word-meaning').value = word.meaning || '';
            document.getElementById('edit-word-meaning-hi').value = word.meaning_hi || '';
            document.getElementById('edit-word-level').value = word.level || 'B1';
            document.getElementById('edit-word-category').value = word.category || 'Classical';
            document.getElementById('edit-word-example-fr').value = word.example_fr || '';
            document.getElementById('edit-word-example-en').value = word.example_en || '';
            document.getElementById('edit-word-example-hi').value = word.example_hi || '';
            document.getElementById('edit-word-use-case').value = word.use_case || '';

            document.getElementById('modal-edit-word').classList.remove('hidden');
        }

        function toggleDarkMode() {
            const isDark = document.documentElement.classList.toggle('dark');
            localStorage.setItem('theme', isDark ? 'dark' : 'light');
        }
    </script>
</body>
</html>

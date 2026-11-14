<!DOCTYPE html>
<html lang="en" class="h-full bg-slate-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign In · French Academic LMS (Filament Edition)</title>
    <script src="https://www.gstatic.com/antigravity/web/dev/tailwindcss.min.js"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@600;700&family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Playfair+Display:ital,wght@0,600;0,700;1,400&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', system-ui, sans-serif; }
        .font-royal { font-family: 'Cinzel', serif; }
        .font-serif { font-family: 'Playfair Display', Georgia, serif; }
        .primary-glow { box-shadow: 0 10px 25px -5px rgba(220, 38, 38, 0.28); }
    </style>
</head>
<body class="h-full text-slate-900 antialiased selection:bg-amber-200 selection:text-amber-900">

    <div class="min-h-full flex flex-col justify-center py-10 sm:px-6 lg:px-8 relative overflow-hidden bg-gradient-to-b from-amber-50/40 via-slate-50 to-slate-100">
        
        <!-- Ambient Filament decorative meshes -->
        <div class="absolute -top-24 -left-24 w-96 h-96 bg-amber-200/40 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute -bottom-24 -right-24 w-96 h-96 bg-amber-300/30 rounded-full blur-3xl pointer-events-none"></div>

        <div class="sm:mx-auto sm:w-full sm:max-w-2xl relative z-10 text-center">
            <!-- Official Real Flag of Canada National Emblem -->
            <div class="inline-flex items-center justify-center p-1.5 rounded-2xl bg-white shadow-xl shadow-red-500/10 border border-slate-200/90 mb-3 hover:scale-105 transition" title="National Flag of Canada">
                <div class="w-20 h-10 rounded-xl overflow-hidden shadow-sm border border-slate-200/80">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 9600 4800" class="w-full h-full block">
                        <path fill="#d52b1e" d="m0 0h2400l99 99h4602l99-99h2400v4800h-2400l-99-99h-4602l-99 99H0z"/>
                        <path fill="#fff" d="m2400 0h4800v4800h-4800zm2490 4430-45-863a95 95 0 0 1 111-98l859 151-116-320a65 65 0 0 1 20-73l941-762-212-99a65 65 0 0 1-34-79l186-572-542 115a65 65 0 0 1-73-38l-105-247-423 454a65 65 0 0 1-111-57l204-1052-327 189a65 65 0 0 1-91-27l-332-652-332 652a65 65 0 0 1-91 27l-327-189 204 1052a65 65 0 0 1-111 57l-423-454-105 247a65 65 0 0 1-73 38l-542-115 186 572a65 65 0 0 1-34 79l-212 99 941 762a65 65 0 0 1 20 73l-116 320 859-151a95 95 0 0 1 111 98l-45 863z"/>
                    </svg>
                </div>
            </div>
            <h2 class="text-2xl font-bold tracking-tight text-slate-900 font-royal">
                French Academic Academy LMS
            </h2>
            <div class="flex items-center justify-center gap-2 mt-1">
                <span class="inline-flex items-center rounded-md bg-amber-100 px-2 py-0.5 text-xs font-semibold text-amber-800 border border-amber-300/80">
                    Filament v3 Theme UI
                </span>
                <span class="inline-flex items-center rounded-md bg-indigo-50 px-2 py-0.5 text-xs font-semibold text-indigo-700 border border-indigo-200">
                    Multi-User Database Architecture
                </span>
                <span class="inline-flex items-center rounded-md bg-red-50 px-2 py-0.5 text-xs font-semibold text-red-700 border border-red-200">
                    Canada Bilingual Heritage
                </span>
            </div>
            <p class="mt-2 text-xs text-slate-500 font-serif italic max-w-md mx-auto">
                Bilingual French Literature & Classical Learning Management System · Multi-Scholar Access
            </p>
        </div>

        <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-2xl relative z-10 px-4 sm:px-0">
            <div class="bg-white py-8 px-6 shadow-xl shadow-slate-200/50 rounded-2xl sm:px-10 border border-slate-200/80">
                
                <!-- Multi-User Pre-Seeded Profiles Picker -->
                <div class="mb-6 p-4 rounded-xl bg-gradient-to-br from-amber-50/90 to-amber-100/40 border border-amber-200 text-xs">
                    <div class="flex items-center justify-between mb-2">
                        <span class="font-bold text-amber-950 flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-emerald-500 animate-ping"></span>
                            Pre-Seeded Multi-Scholar Database (Click Any Profile To Auto-Fill)
                        </span>
                        <span class="text-[11px] font-mono text-amber-800 bg-amber-200/70 px-2 py-0.5 rounded font-semibold">5 Scholars</span>
                    </div>
                    <p class="text-[11px] text-slate-600 leading-relaxed mb-3">
                        Choose any scholar below to instantly test the multi-user database with their custom CEFR syllabus, active streak, and profile:
                    </p>

                    <!-- Scholar Cards Grid -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-2.5" id="scholar-selector-grid">
                        
                        <!-- User 1: Alexandre Dumas -->
                        <div onclick="selectScholar('scholar@academie.fr', 'french123', 'Alexandre Dumas', 'B1', 'scholar-card-1')" 
                             id="scholar-card-1"
                             class="scholar-chip p-2.5 rounded-xl bg-white border-2 border-amber-500 shadow-xs cursor-pointer hover:border-amber-600 transition flex flex-col justify-between">
                            <div class="flex items-center justify-between">
                                <span class="font-bold text-slate-900 text-xs truncate">Alexandre Dumas</span>
                                <span class="text-[10px] font-mono px-1.5 py-0.2 rounded bg-amber-100 text-amber-900 font-bold border border-amber-300">B1</span>
                            </div>
                            <div class="text-[10px] text-slate-500 mt-1">🇨🇦 Canada · Scholar</div>
                            <div class="flex items-center justify-between mt-2 pt-1.5 border-t border-slate-100 text-[10px] font-mono text-amber-800 font-semibold">
                                <span>Active Scholar</span>
                                <span>🔥 14 Days</span>
                            </div>
                        </div>

                        <!-- User 2: Éléonore de Valois -->
                        <div onclick="selectScholar('eleonore@sorbonne.edu', 'french123', 'Éléonore de Valois', 'C1', 'scholar-card-2')" 
                             id="scholar-card-2"
                             class="scholar-chip p-2.5 rounded-xl bg-white border border-slate-200 shadow-xs cursor-pointer hover:border-indigo-400 transition flex flex-col justify-between">
                            <div class="flex items-center justify-between">
                                <span class="font-bold text-slate-900 text-xs truncate">Éléonore de Valois</span>
                                <span class="text-[10px] font-mono px-1.5 py-0.2 rounded bg-indigo-100 text-indigo-900 font-bold border border-indigo-200">C1</span>
                            </div>
                            <div class="text-[10px] text-slate-500 mt-1">🇫🇷 France · Fellow</div>
                            <div class="flex items-center justify-between mt-2 pt-1.5 border-t border-slate-100 text-[10px] font-mono text-indigo-800 font-semibold">
                                <span>Fellow Member</span>
                                <span>🔥 42 Days</span>
                            </div>
                        </div>

                        <!-- User 3: Liam Tremblay -->
                        <div onclick="selectScholar('liam@montreal.ca', 'french123', 'Liam Tremblay', 'A2', 'scholar-card-3')" 
                             id="scholar-card-3"
                             class="scholar-chip p-2.5 rounded-xl bg-white border border-slate-200 shadow-xs cursor-pointer hover:border-emerald-400 transition flex flex-col justify-between">
                            <div class="flex items-center justify-between">
                                <span class="font-bold text-slate-900 text-xs truncate">Liam Tremblay</span>
                                <span class="text-[10px] font-mono px-1.5 py-0.2 rounded bg-emerald-100 text-emerald-900 font-bold border border-emerald-200">A2</span>
                            </div>
                            <div class="text-[10px] text-slate-500 mt-1">🇨🇦 Canada · Student</div>
                            <div class="flex items-center justify-between mt-2 pt-1.5 border-t border-slate-100 text-[10px] font-mono text-emerald-800 font-semibold">
                                <span>Active Student</span>
                                <span>🔥 7 Days</span>
                            </div>
                        </div>

                        <!-- User 4: Sophie Martin -->
                        <div onclick="selectScholar('sophie@geneva.ch', 'french123', 'Sophie Martin', 'A1', 'scholar-card-4')" 
                             id="scholar-card-4"
                             class="scholar-chip p-2.5 rounded-xl bg-white border border-slate-200 shadow-xs cursor-pointer hover:border-rose-400 transition flex flex-col justify-between">
                            <div class="flex items-center justify-between">
                                <span class="font-bold text-slate-900 text-xs truncate">Sophie Martin</span>
                                <span class="text-[10px] font-mono px-1.5 py-0.2 rounded bg-rose-100 text-rose-900 font-bold border border-rose-200">A1</span>
                            </div>
                            <div class="text-[10px] text-slate-500 mt-1">🇨🇭 Switzerland · Novice</div>
                            <div class="flex items-center justify-between mt-2 pt-1.5 border-t border-slate-100 text-[10px] font-mono text-rose-800 font-semibold">
                                <span>A1 Novice</span>
                                <span>🔥 3 Days</span>
                            </div>
                        </div>

                        <!-- User 5: Prof. Émile Laurent -->
                        <div onclick="selectScholar('instructor@academie.fr', 'french123', 'Prof. Émile Laurent', 'C2', 'scholar-card-5')" 
                             id="scholar-card-5"
                             class="scholar-chip p-2.5 rounded-xl bg-white border border-slate-200 shadow-xs cursor-pointer hover:border-slate-500 transition flex flex-col justify-between sm:col-span-2">
                            <div class="flex items-center justify-between">
                                <span class="font-bold text-slate-900 text-xs truncate">Prof. Émile Laurent (Faculty Director)</span>
                                <span class="text-[10px] font-mono px-1.5 py-0.2 rounded bg-purple-100 text-purple-900 font-bold border border-purple-200">C2 Faculty</span>
                            </div>
                            <div class="text-[10px] text-slate-500 mt-1">🇨🇦 Canada · Academy Director & Romance Philologist</div>
                            <div class="flex items-center justify-between mt-2 pt-1.5 border-t border-slate-100 text-[10px] font-mono text-purple-800 font-semibold">
                                <span>Faculty Director</span>
                                <span>🔥 120 Days</span>
                            </div>
                        </div>

                    </div>
                </div>

                @if ($errors->any())
                    <div class="mb-5 rounded-xl bg-rose-50 border border-rose-200 p-3.5 text-xs text-rose-700">
                        <ul class="list-disc pl-4 space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form class="space-y-4" action="{{ route('login') }}" method="POST">
                    @csrf
                    <div>
                        <label for="email" class="block text-xs font-semibold text-slate-700">Academic Scholar Email</label>
                        <div class="mt-1">
                            <input id="email" name="email" type="email" autocomplete="email" required value="{{ old('email', 'scholar@academie.fr') }}"
                                   class="block w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-slate-900 placeholder-slate-400 focus:border-amber-500 focus:outline-none focus:ring-2 focus:ring-amber-500/20 text-sm transition">
                        </div>
                    </div>

                    <div>
                        <div class="flex items-center justify-between">
                            <label for="password" class="block text-xs font-semibold text-slate-700">Password</label>
                            <span class="text-[11px] text-slate-400 font-mono">default: french123</span>
                        </div>
                        <div class="mt-1">
                            <input id="password" name="password" type="password" autocomplete="current-password" required value="french123"
                                   class="block w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-slate-900 placeholder-slate-400 focus:border-amber-500 focus:outline-none focus:ring-2 focus:ring-amber-500/20 text-sm transition">
                        </div>
                    </div>

                    <div class="flex items-center justify-between text-xs pt-1">
                        <div class="flex items-center">
                            <input id="remember" name="remember" type="checkbox" checked
                                   class="h-4 w-4 rounded border-slate-300 text-amber-600 focus:ring-amber-500">
                            <label for="remember" class="ml-2 block text-slate-600">Remember session</label>
                        </div>
                        <span class="text-amber-700 hover:text-amber-800 font-medium">Bilingual Security Protocol</span>
                    </div>

                    <div class="pt-2">
                        <button type="submit"
                                class="flex w-full justify-center items-center gap-2 rounded-xl bg-gradient-to-r from-amber-600 to-amber-500 py-3 px-4 text-sm font-bold text-white shadow-md hover:from-amber-700 hover:to-amber-600 focus:outline-none focus:ring-2 focus:ring-amber-500 focus:ring-offset-2 transition active:scale-[0.99]">
                            <span>Sign In to Academic LMS Dashboard</span>
                            <span>→</span>
                        </button>
                    </div>
                </form>

                <div class="mt-6 pt-6 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500">
                    <div>
                        New scholar to the Academy?
                        <a href="{{ route('register') }}" class="font-bold text-amber-700 hover:text-amber-800 underline ml-1">
                            Register Account
                        </a>
                    </div>
                    <span class="font-mono text-[11px] text-slate-400">Laravel 13 · SQLite 3</span>
                </div>

            </div>

            <!-- Footer Badge -->
            <div class="mt-6 text-center text-xs text-slate-400 flex items-center justify-center gap-2">
                <span>Filament Themes</span>
                <span>•</span>
                <span>Multi-User Architecture</span>
                <span>•</span>
                <span>Proper English UI</span>
                <span>•</span>
                <span>🇨🇦 Canadian Bilingual Heritage</span>
            </div>
        </div>
    </div>

    <script>
        function selectScholar(email, password, name, level, cardId) {
            document.getElementById('email').value = email;
            document.getElementById('password').value = password;

            document.querySelectorAll('.scholar-chip').forEach(c => {
                c.classList.remove('border-2', 'border-amber-500', 'border-indigo-500', 'border-emerald-500', 'border-rose-500', 'border-purple-500');
                c.classList.add('border', 'border-slate-200');
            });

            const activeCard = document.getElementById(cardId);
            if (activeCard) {
                activeCard.classList.remove('border', 'border-slate-200');
                activeCard.classList.add('border-2', 'border-amber-500');
            }
        }
    </script>
</body>
</html>
<!DOCTYPE html>
<html lang="en" class="h-full bg-slate-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Scholar Registration · French Academic LMS</title>
    <script src="https://www.gstatic.com/antigravity/web/dev/tailwindcss.min.js"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Playfair+Display:ital,wght@0,600;0,700;1,400&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', system-ui, sans-serif; }
        .font-serif { font-family: 'Playfair Display', Georgia, serif; }
        .primary-glow { box-shadow: 0 10px 25px -5px rgba(220, 38, 38, 0.28); }
    </style>
</head>
<body class="h-full text-slate-900 antialiased selection:bg-amber-200 selection:text-amber-900">

    <div class="min-h-full flex flex-col justify-center py-12 sm:px-6 lg:px-8 relative overflow-hidden bg-gradient-to-b from-amber-50/40 via-slate-50 to-slate-100">
        
        <div class="absolute -top-24 -left-24 w-96 h-96 bg-amber-200/40 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute -bottom-24 -right-24 w-96 h-96 bg-amber-300/30 rounded-full blur-3xl pointer-events-none"></div>

        <div class="sm:mx-auto sm:w-full sm:max-w-md relative z-10 text-center">
            <!-- Official Real Flag of Canada National Emblem -->
            <div class="inline-flex items-center justify-center p-1.5 rounded-2xl bg-white shadow-xl shadow-red-500/10 border border-slate-200/90 mb-3 hover:scale-105 transition" title="National Flag of Canada">
                <div class="w-20 h-10 rounded-xl overflow-hidden shadow-sm border border-slate-200/80">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 9600 4800" class="w-full h-full block">
                        <path fill="#d52b1e" d="m0 0h2400l99 99h4602l99-99h2400v4800h-2400l-99-99h-4602l-99 99H0z"/>
                        <path fill="#fff" d="m2400 0h4800v4800h-4800zm2490 4430-45-863a95 95 0 0 1 111-98l859 151-116-320a65 65 0 0 1 20-73l941-762-212-99a65 65 0 0 1-34-79l186-572-542 115a65 65 0 0 1-73-38l-105-247-423 454a65 65 0 0 1-111-57l204-1052-327 189a65 65 0 0 1-91-27l-332-652-332 652a65 65 0 0 1-91 27l-327-189 204 1052a65 65 0 0 1-111 57l-423-454-105 247a65 65 0 0 1-73 38l-542-115 186 572a65 65 0 0 1-34 79l-212 99 941 762a65 65 0 0 1 20 73l-116 320 859-151a95 95 0 0 1 111 98l-45 863z"/>
                    </svg>
                </div>
            </div>
            <h2 class="text-2xl font-bold tracking-tight text-slate-900">
                Enroll at the Academy
            </h2>
            <p class="mt-1 text-xs text-slate-500 font-serif italic">
                Create your student record and begin your French literary journey
            </p>
        </div>

        <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md relative z-10 px-4 sm:px-0">
            <div class="bg-white py-8 px-6 shadow-xl shadow-slate-200/50 rounded-2xl sm:px-10 border border-slate-200/80">
                
                @if ($errors->any())
                    <div class="mb-5 rounded-xl bg-rose-50 border border-rose-200 p-3.5 text-xs text-rose-700">
                        <ul class="list-disc pl-4 space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form class="space-y-4" action="{{ route('register') }}" method="POST">
                    @csrf
                    <div>
                        <label for="name" class="block text-xs font-semibold text-slate-700">Full Name</label>
                        <div class="mt-1">
                            <input id="name" name="name" type="text" required value="{{ old('name') }}" placeholder="e.g. Jean Valjean"
                                   class="block w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-slate-900 placeholder-slate-400 focus:border-amber-500 focus:outline-none focus:ring-2 focus:ring-amber-500/20 text-sm transition">
                        </div>
                    </div>

                    <div>
                        <label for="email" class="block text-xs font-semibold text-slate-700">Email Address</label>
                        <div class="mt-1">
                            <input id="email" name="email" type="email" required value="{{ old('email') }}" placeholder="scholar@academie.fr"
                                   class="block w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-slate-900 placeholder-slate-400 focus:border-amber-500 focus:outline-none focus:ring-2 focus:ring-amber-500/20 text-sm transition">
                        </div>
                    </div>

                    <div>
                        <label for="level_display" class="block text-xs font-semibold text-slate-700">Assigned CEFR Proficiency Level</label>
                        <div class="mt-1">
                            <input id="level_display" type="text" readonly value="A1 · Complete Beginner (Foundations & Phonetics)"
                                   class="block w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2.5 text-slate-700 font-semibold text-xs cursor-not-allowed">
                            <input type="hidden" name="level" value="A1">
                        </div>
                        <p class="text-[11px] text-slate-400 mt-1">All newly enrolled scholars begin in the mandatory A1 Foundations track.</p>
                    </div>

                    <div>
                        <label for="password" class="block text-xs font-semibold text-slate-700">Password</label>
                        <div class="mt-1">
                            <input id="password" name="password" type="password" required minlength="6"
                                   class="block w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-slate-900 placeholder-slate-400 focus:border-amber-500 focus:outline-none focus:ring-2 focus:ring-amber-500/20 text-sm transition">
                        </div>
                    </div>

                    <div>
                        <label for="password_confirmation" class="block text-xs font-semibold text-slate-700">Confirm Password</label>
                        <div class="mt-1">
                            <input id="password_confirmation" name="password_confirmation" type="password" required minlength="6"
                                   class="block w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-slate-900 placeholder-slate-400 focus:border-amber-500 focus:outline-none focus:ring-2 focus:ring-amber-500/20 text-sm transition">
                        </div>
                    </div>

                    <div class="pt-2">
                        <button type="submit"
                                class="flex w-full justify-center rounded-xl bg-gradient-to-r from-amber-600 to-amber-500 py-2.5 px-4 text-sm font-bold text-white shadow-md hover:from-amber-700 hover:to-amber-600 focus:outline-none focus:ring-2 focus:ring-amber-500 focus:ring-offset-2 transition active:scale-[0.99]">
                            Create Account & Start Learning →
                        </button>
                    </div>
                </form>

                <div class="mt-6 pt-6 border-t border-slate-100 text-center text-xs text-slate-500">
                    Already registered?
                    <a href="{{ route('login') }}" class="font-bold text-amber-700 hover:text-amber-800 underline ml-1">
                        Sign in to account
                    </a>
                </div>

            </div>
        </div>
    </div>
</body>
</html>

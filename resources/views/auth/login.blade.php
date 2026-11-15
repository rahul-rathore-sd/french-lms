<!DOCTYPE html>
<html lang="en" class="h-full bg-slate-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Sign In · Académie de Français</title>
    <script src="https://www.gstatic.com/antigravity/web/dev/tailwindcss.min.js"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Merriweather:ital,wght@0,300;0,400;0,700;1,300&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', system-ui, sans-serif; }
        .font-serif-fr { font-family: 'Merriweather', serif; }
        .tab-btn.active {
            border-bottom: 2px solid #D52B1E;
            color: #D52B1E;
            font-weight: 700;
        }
    </style>
    @if (!empty($clerkPublishableKey))
        <!-- Clerk JavaScript SDK for Instant Email Authentication & OTP -->
        <script async crossorigin="anonymous" 
                data-clerk-publishable-key="{{ $clerkPublishableKey }}" 
                src="https://cdn.jsdelivr.net/npm/@clerk/clerk-js@latest/dist/clerk.browser.js" 
                type="text/javascript"></script>
    @endif
</head>
<body class="h-full text-slate-900 antialiased selection:bg-red-100 selection:text-red-900">

    <div class="min-h-full flex flex-col justify-center py-12 sm:px-6 lg:px-8 relative overflow-hidden bg-gradient-to-b from-slate-100 via-slate-50 to-slate-100">
        
        <!-- Ambient decorative blurs -->
        <div class="absolute -top-24 -left-24 w-96 h-96 bg-red-100/50 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute -bottom-24 -right-24 w-96 h-96 bg-amber-100/40 rounded-full blur-3xl pointer-events-none"></div>

        <div class="sm:mx-auto sm:w-full sm:max-w-md relative z-10 text-center px-4">
            <!-- Official Real Flag of Canada National Emblem -->
            <div class="inline-flex items-center justify-center p-1.5 rounded-2xl bg-white shadow-lg shadow-red-500/10 border border-slate-200/90 mb-4 hover:scale-105 transition" title="Académie de Français · Canadian Bilingual Heritage">
                <div class="w-16 h-8 rounded-lg overflow-hidden shadow-xs border border-slate-200/80">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 9600 4800" class="w-full h-full block">
                        <path fill="#d52b1e" d="m0 0h2400l99 99h4602l99-99h2400v4800h-2400l-99-99h-4602l-99 99H0z"/>
                        <path fill="#fff" d="m2400 0h4800v4800h-4800zm2490 4430-45-863a95 95 0 0 1 111-98l859 151-116-320a65 65 0 0 1 20-73l941-762-212-99a65 65 0 0 1-34-79l186-572-542 115a65 65 0 0 1-73-38l-105-247-423 454a65 65 0 0 1-111-57l204-1052-327 189a65 65 0 0 1-91-27l-332-652-332 652a65 65 0 0 1-91 27l-327-189 204 1052a65 65 0 0 1-111 57l-423-454-105 247a65 65 0 0 1-73 38l-542-115 186 572a65 65 0 0 1-34 79l-212 99 941 762a65 65 0 0 1 20 73l-116 320 859-151a95 95 0 0 1 111 98l-45 863z"/>
                    </svg>
                </div>
            </div>

            <h2 class="text-2xl font-extrabold tracking-tight text-slate-900 font-serif-fr">
                Académie de Français
            </h2>
            <p class="mt-1.5 text-xs text-slate-500 font-serif-fr italic">
                Classical French Immersion, CEFR Syllabi & Multilingual Lexicon Sanctuary
            </p>
        </div>

        <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md relative z-10 px-4">
            <div class="bg-white py-8 px-6 shadow-xl shadow-slate-200/60 rounded-2xl sm:px-8 border border-slate-200">
                
                @if (session('status'))
                    <div class="mb-4 rounded-xl bg-emerald-50 border border-emerald-200 p-3 text-xs text-emerald-800 font-medium">
                        {{ session('status') }}
                    </div>
                @endif

                @if ($errors->any())
                    <div class="mb-5 rounded-xl bg-rose-50 border border-rose-200 p-3.5 text-xs text-rose-700">
                        <ul class="list-disc pl-4 space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <!-- Auth Mode Tabs (Clerk Email Authentication vs Password Login) -->
                @if (!empty($clerkPublishableKey))
                    <div class="flex border-b border-slate-200 mb-6">
                        <button type="button" onclick="switchAuthTab('clerk')" id="tab-btn-clerk" 
                                class="tab-btn active pb-3 px-3 text-xs font-semibold transition flex items-center gap-1.5 flex-1 justify-center">
                            <span>⚡</span>
                            <span>Clerk Email Authentication</span>
                        </button>
                        <button type="button" onclick="switchAuthTab('password')" id="tab-btn-password" 
                                class="tab-btn pb-3 px-3 text-xs font-semibold text-slate-500 hover:text-slate-700 transition flex items-center gap-1.5 flex-1 justify-center">
                            <span>🔑</span>
                            <span>Password Sign In</span>
                        </button>
                    </div>

                    <!-- TAB 1: CLERK EMAIL AUTHENTICATION -->
                    <div id="auth-tab-clerk" class="space-y-4">
                        <div class="p-3 rounded-xl bg-slate-50 border border-slate-200 text-xs text-slate-600 flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                            <span>Secured by <strong>Clerk.com</strong> · Instant Email Verification & OTP</span>
                        </div>
                        <div id="clerk-sign-in" class="flex justify-center min-h-[320px]">
                            <div class="py-12 text-center text-xs text-slate-400 animate-pulse">
                                Initializing secure Clerk email authentication portal...
                            </div>
                        </div>
                    </div>
                @endif

                <!-- TAB 2: STANDARD / FALLBACK ACADEMIC PASSWORD LOGIN -->
                <div id="auth-tab-password" class="{{ !empty($clerkPublishableKey) ? 'hidden' : '' }}">

                    @if (empty($clerkPublishableKey))
                        <!-- Helpful Clerk Activation Pill -->
                        <div class="mb-5 p-3 rounded-xl bg-gradient-to-r from-red-50 to-amber-50 border border-red-200 text-xs text-slate-700">
                            <div class="flex items-center justify-between font-bold text-slate-900 mb-1">
                                <span class="flex items-center gap-1.5">
                                    <span class="text-sm">⚡</span>
                                    <span>Clerk.com Email Authentication Ready</span>
                                </span>
                                <span class="text-[10px] font-mono bg-red-100 text-red-800 px-1.5 py-0.5 rounded font-bold">Free Tier</span>
                            </div>
                            <p class="text-[11px] text-slate-600 leading-relaxed">
                                Add <code class="bg-white px-1 py-0.5 rounded border border-slate-200 text-red-700 font-mono">CLERK_PUBLISHABLE_KEY</code> in your environment to activate passwordless Email OTP verification.
                            </p>
                        </div>
                    @endif

                    <form class="space-y-4" action="{{ route('login') }}" method="POST">
                        @csrf
                        <div>
                            <label for="email" class="block text-xs font-semibold text-slate-700">Scholar / Admin Email</label>
                            <div class="mt-1">
                                <input id="email" name="email" type="email" autocomplete="email" required 
                                       value="{{ old('email') }}"
                                       placeholder="scholar@academie.fr or rrathore959910@gmail.com"
                                       class="block w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-slate-900 placeholder-slate-400 focus:border-red-500 focus:outline-none focus:ring-2 focus:ring-red-500/20 text-sm transition">
                            </div>
                        </div>

                        <div>
                            <div class="flex items-center justify-between">
                                <label for="password" class="block text-xs font-semibold text-slate-700">Password</label>
                            </div>
                            <div class="mt-1">
                                <input id="password" name="password" type="password" autocomplete="current-password" required 
                                       placeholder="••••••••"
                                       class="block w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-slate-900 placeholder-slate-400 focus:border-red-500 focus:outline-none focus:ring-2 focus:ring-red-500/20 text-sm transition">
                            </div>
                        </div>

                        <div class="flex items-center justify-between text-xs pt-1">
                            <div class="flex items-center">
                                <input id="remember" name="remember" type="checkbox" checked
                                       class="h-4 w-4 rounded border-slate-300 text-red-600 focus:ring-red-500">
                                <label for="remember" class="ml-2 block text-slate-600">Remember session</label>
                            </div>
                            <span class="text-red-700 font-medium">Bilingual Security</span>
                        </div>

                        <div class="pt-2">
                            <button type="submit"
                                    class="flex w-full justify-center items-center gap-2 rounded-xl bg-gradient-to-r from-red-600 to-red-700 py-3 px-4 text-sm font-bold text-white shadow-md hover:from-red-700 hover:to-red-800 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition active:scale-[0.99]">
                                <span>Sign In to Académie</span>
                                <span>→</span>
                            </button>
                        </div>
                    </form>
                </div>

                <div class="mt-6 pt-6 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500">
                    <div>
                        New to Académie?
                        <a href="{{ route('register') }}" class="font-bold text-red-700 hover:text-red-800 underline ml-1">
                            Register Account
                        </a>
                    </div>
                    <span class="font-mono text-[11px] text-slate-400">Laravel 12 · Secure</span>
                </div>

            </div>

            <!-- Footer Badge -->
            <div class="mt-6 text-center text-xs text-slate-400 flex items-center justify-center gap-2">
                <span>Multi-Scholar Architecture</span>
                <span>•</span>
                <span>A1–C2 CEFR Syllabus</span>
                <span>•</span>
                <span>🇨🇦 Canadian Heritage</span>
            </div>
        </div>
    </div>

    <!-- Clerk Sync & Auth Handling Script -->
    <script>
        function switchAuthTab(tab) {
            const clerkTab = document.getElementById('auth-tab-clerk');
            const pwdTab = document.getElementById('auth-tab-password');
            const btnClerk = document.getElementById('tab-btn-clerk');
            const btnPwd = document.getElementById('tab-btn-password');

            if (tab === 'clerk') {
                if (clerkTab) clerkTab.classList.remove('hidden');
                if (pwdTab) pwdTab.classList.add('hidden');
                if (btnClerk) { btnClerk.classList.add('active'); btnClerk.classList.remove('text-slate-500'); }
                if (btnPwd) { btnPwd.classList.remove('active'); btnPwd.classList.add('text-slate-500'); }
            } else {
                if (clerkTab) clerkTab.classList.add('hidden');
                if (pwdTab) pwdTab.classList.remove('hidden');
                if (btnPwd) { btnPwd.classList.add('active'); btnPwd.classList.remove('text-slate-500'); }
                if (btnClerk) { btnClerk.classList.remove('active'); btnClerk.classList.add('text-slate-500'); }
            }
        }

        @if (!empty($clerkPublishableKey))
        window.addEventListener('load', async function () {
            if (window.Clerk) {
                try {
                    await window.Clerk.load();

                    // If user is already authenticated with Clerk, sync session
                    if (window.Clerk.user) {
                        await syncClerkUserWithLaravel(window.Clerk.user);
                    } else {
                        // Mount Clerk Sign In component
                        const mountTarget = document.getElementById('clerk-sign-in');
                        if (mountTarget) {
                            mountTarget.innerHTML = '';
                            window.Clerk.mountSignIn(mountTarget, {
                                appearance: {
                                    variables: {
                                        colorPrimary: '#D52B1E',
                                        colorText: '#0f172a',
                                        borderRadius: '0.75rem',
                                    }
                                }
                            });

                            // Listen for successful authentication
                            window.Clerk.addListener(({ user }) => {
                                if (user) {
                                    syncClerkUserWithLaravel(user);
                                }
                            });
                        }
                    }
                } catch (err) {
                    console.error('Clerk initialization error:', err);
                }
            }
        });

        async function syncClerkUserWithLaravel(clerkUser) {
            try {
                const primaryEmail = clerkUser.primaryEmailAddress ? clerkUser.primaryEmailAddress.emailAddress : null;
                if (!primaryEmail) return;

                const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

                const response = await fetch("{{ route('auth.clerk-sync') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        email: primaryEmail,
                        name: clerkUser.fullName || clerkUser.firstName || primaryEmail.split('@')[0],
                        clerk_id: clerkUser.id
                    })
                });

                const data = await response.json();
                if (data.status === 'success' && data.redirect_url) {
                    window.location.href = data.redirect_url;
                }
            } catch (error) {
                console.error('Error synchronizing Clerk user with Laravel:', error);
            }
        }
        @endif
    </script>
</body>
</html>

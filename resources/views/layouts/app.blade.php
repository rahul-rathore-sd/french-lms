<!doctype html>
<html lang="en" data-theme="dark">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
<title>@yield('title', 'TCF Canada Prep')</title>
<meta name="description" content="Personal TCF Canada preparation, target NCLC 7: daily plan, grammar, listening, reading, writing, speaking and flashcards.">
<meta name="theme-color" content="#111219">
<meta name="color-scheme" content="dark light">
<meta name="robots" content="noindex, nofollow, noarchive">
<link rel="icon" href="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'%3E%3Crect width='100' height='100' rx='22' fill='%238ba1f2'/%3E%3Ctext x='50' y='72' font-size='62' text-anchor='middle' fill='%23111219' font-family='serif'%3E%C3%89%3C/text%3E%3C/svg%3E">
{{-- Dark by default on every device, whatever the system setting is; only an explicit "light" choice switches it off. --}}
<script>(function(){var t='dark';try{if(localStorage.getItem('tcf.theme')==='light')t='light'}catch(e){}document.documentElement.dataset.theme=t;if(t==='light'){var m=document.querySelector('meta[name=theme-color]');if(m)m.content='#f8f6f1'}})();</script>
@php($me = $learner ?? null)
@if ($me)
    @php($stateKeys = $needKeys ?? ['progress'])
    @php($state = $me->states()->whereIn('key', $stateKeys)->get()->mapWithKeys(fn ($st) => [$st->key => json_decode($st->value)])->all())
    <script type="application/json" id="me">{!! json_encode(['id' => $me->id, 'name' => $me->name, 'keys' => $stateKeys, 'state' => (object) $state], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG) !!}</script>
@endif
@if ($me)
    <script type="application/json" id="tts-cfg">{!! json_encode(['voice' => config('tts.enabled') ? $me->voice : 'browser'], JSON_UNESCAPED_UNICODE | JSON_HEX_TAG) !!}</script>
@endif
@vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
<header class="glass-header sticky top-0 z-40">
    <div class="mx-auto flex h-16 max-w-5xl items-center justify-between gap-3 px-4">
        <a href="/" class="flex items-center gap-2.5" aria-label="Home">
            <span class="font-display grid h-9 w-9 place-items-center rounded-xl bg-accent text-lg font-semibold text-accent-fg">É</span>
            <span class="font-display hidden text-lg font-semibold sm:block">TCF Canada Prep</span>
        </a>
        @if ($me)<nav class="hidden items-center gap-1 md:flex" aria-label="Main">
            @foreach ([['/', 'Today'], ['/learn', 'Learn'], ['/practice', 'Cards'], ['/tcf', 'TCF Canada'], ['/words', 'Words'], ['/level-check', 'Level check']] as [$href, $label])
                <a href="{{ $href }}" class="rounded-full px-3.5 py-2 text-sm transition {{ request()->is(ltrim($href, '/') ?: '/') ? 'bg-raised text-fg' : 'text-muted hover:text-fg' }}">{{ $label }}</a>
            @endforeach
        </nav>@endif
        <div class="flex items-center gap-2">
            @if ($me)
                <span class="chip" title="Day streak">🔥 <b data-streak>0</b></span>
                <a href="/me" class="chip hidden max-w-[9rem] truncate sm:inline-flex" title="Your profile">👤 {{ $me->name }}</a>
            @endif
            <div class="theme-switch" role="group" aria-label="Colour theme">
                <span class="thumb" aria-hidden="true"></span>
                <button type="button" data-theme-set="dark" aria-label="Dark mode" title="Dark mode (easy on the eyes)">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3a6 6 0 0 0 9 9 9 9 0 1 1-9-9Z"/></svg>
                </button>
                <button type="button" data-theme-set="light" aria-label="Light mode" title="Light mode">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="4"/><path d="M12 2v2M12 20v2M4.9 4.9l1.4 1.4M17.7 17.7l1.4 1.4M2 12h2M20 12h2M4.9 19.1l1.4-1.4M17.7 6.3l1.4-1.4"/></svg>
                </button>
            </div>
        </div>
    </div>
    {{-- mobile nav: scrollable pill row --}}
    @if ($me)<nav class="no-scrollbar flex gap-1 overflow-x-auto px-4 pb-2 md:hidden" aria-label="Main (mobile)">
        @foreach ([['/', 'Today'], ['/learn', 'Learn'], ['/practice', 'Cards'], ['/tcf', 'TCF'], ['/words', 'Words'], ['/level-check', 'Level']] as [$href, $label])
            <a href="{{ $href }}" class="shrink-0 rounded-full px-3.5 py-1.5 text-sm {{ request()->is(ltrim($href, '/') ?: '/') ? 'bg-raised text-fg' : 'text-muted' }}">{{ $label }}</a>
        @endforeach
        <a href="/me" class="shrink-0 rounded-full px-3.5 py-1.5 text-sm {{ request()->is('me') ? 'bg-raised text-fg' : 'text-muted' }}">Profile</a>
    </nav>@endif
</header>

<main class="mx-auto max-w-5xl px-4 py-8 sm:py-10">
    @yield('content')
</main>

<footer class="mx-auto max-w-5xl px-4 pb-10 pt-4 text-sm text-muted">
    <p>Personal study site for TCF Canada (target NCLC 7). Score bands come from published sources; always confirm them on the official IRCC / France Éducation international pages before booking.</p>
</footer>
</body>
</html>

@extends('layouts.app')

@section('title', 'Learn · TCF Canada Prep')

@section('content')
<h1 class="font-display text-4xl font-medium sm:text-5xl">The <em>library</em></h1>
<p class="mt-2 text-muted">Everything for TCF Canada in one place. Your current level is <b class="text-fg">{{ $currentLevel }}</b>.</p>

<nav class="mt-6 flex flex-wrap gap-2" aria-label="Filter by skill">
    <a href="/learn{{ $level ? '?level='.$level : '' }}" class="chip {{ ! $kind ? 'chip-accent' : '' }}">All</a>
    @foreach ($kinds as $k => $label)
        <a href="/learn?kind={{ $k }}{{ $level ? '&level='.$level : '' }}" class="chip {{ $kind === $k ? 'chip-accent' : '' }}">{{ $label }}</a>
    @endforeach
</nav>
<nav class="mt-2 flex flex-wrap gap-2" aria-label="Filter by level">
    <a href="/learn{{ $kind ? '?kind='.$kind : '' }}" class="chip {{ ! $level ? 'chip-accent' : '' }}">All levels</a>
    @foreach ($levels as $l)
        <a href="/learn?level={{ $l }}{{ $kind ? '&kind='.$kind : '' }}" class="chip {{ $level === $l ? 'chip-accent' : '' }}">{{ $l }}{{ $l === $currentLevel ? ' ★' : '' }}</a>
    @endforeach
</nav>

@forelse ($items as $lvl => $group)
    <section class="mt-8" aria-labelledby="lvl-{{ $lvl }}">
        <h2 id="lvl-{{ $lvl }}" class="font-display flex items-center gap-2 text-2xl font-semibold">{{ $lvl }} <span class="text-sm font-normal text-muted">{{ $group->count() }} items</span></h2>
        <ul class="mt-3 grid gap-2 sm:grid-cols-2">
            @foreach ($group as $m)
                <li>
                    <a href="/r/{{ $m->slug }}" class="panel flex h-full items-start gap-3 p-4 transition hover:border-accent/50">
                        <div class="min-w-0 flex-1">
                            <div class="flex flex-wrap items-center gap-1.5">
                                <span class="chip">{{ $kinds[$m->kind] ?? $m->kind }}</span>
                                @if ($m->source === 'daily')<span class="chip chip-gold">Daily</span>@endif
                                @if ($m->nclc)<span class="chip">{{ $m->nclc }}</span>@endif
                                <span class="ml-auto text-xs text-muted">{{ $m->minutes }} min</span>
                            </div>
                            <p class="mt-1.5 font-medium leading-snug">{{ $m->title }}</p>
                            @if ($m->summary)<p class="text-sm text-muted">{{ \Illuminate\Support\Str::limit($m->summary, 110) }}</p>@endif
                        </div>
                    </a>
                </li>
            @endforeach
        </ul>
    </section>
@empty
    <p class="mt-8 text-muted">Nothing here yet for this filter.</p>
@endforelse
@endsection

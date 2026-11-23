@extends('layouts.app')

@section('title', $m->title.' · TCF Canada Prep')

@section('content')
<p><a href="/learn?kind={{ $m->kind }}&level={{ $m->level }}" class="text-sm text-muted hover:text-fg">← {{ $kinds[$m->kind] ?? 'Library' }} · {{ $m->level }}</a></p>

<div class="mt-3 flex flex-wrap items-center gap-2">
    <span class="chip chip-accent">{{ $m->level }}</span>
    <span class="chip">{{ $kinds[$m->kind] ?? $m->kind }}</span>
    @if ($m->nclc)<span class="chip">{{ $m->nclc }}</span>@endif
    @if ($m->source === 'daily')<span class="chip chip-gold">Daily · {{ $m->publish_on?->format('j M') }}</span>@endif
    <span class="chip">{{ $m->minutes }} min</span>
</div>
<h1 class="font-display mt-3 text-3xl font-medium leading-tight sm:text-4xl">{{ $m->title }}</h1>
@if ($m->title_hi)<p class="mt-1 text-lg text-muted">{{ $m->title_hi }}</p>@endif
@if ($m->summary)<p class="mt-2 text-muted">{{ $m->summary }}</p>@endif

<div class="mt-6">
    @include('learn.kinds.'.(in_array($m->kind, ['reading', 'listening'], true) ? 'passage' : $m->kind))
</div>

<div class="mt-10 flex flex-wrap items-center gap-3 border-t border-line pt-6">
    <button type="button" class="btn btn-primary" data-done="{{ $m->slug }}">Mark as done</button>
    @if ($next)<a href="/r/{{ $next->slug }}" class="btn">Next: {{ \Illuminate\Support\Str::limit($next->title, 40) }} →</a>@endif
    <a href="/learn" class="btn">Library</a>
</div>
@endsection

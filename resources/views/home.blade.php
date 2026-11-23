@extends('layouts.app')

@section('title', 'Today · TCF Canada Prep')

@section('content')
@php
    $st = $plan['stage'];
    $cp = $plan['wish_checkpoint'];
    $kindLabel = ['grammar' => 'Grammar', 'listening' => 'Listening', 'reading' => 'Reading', 'writing' => 'Writing', 'speaking' => 'Speaking'];
@endphp

<p class="text-sm text-muted">Day {{ $plan['day_number'] }} · Week {{ $plan['week_number'] }} · {{ $plan['daily_minutes'] / 60 }} h a day</p>
<h1 class="font-display mt-1 text-4xl font-medium leading-tight sm:text-5xl">Bonjour ! <em>Today's 2 hours.</em></h1>

<div class="mt-6 grid gap-4 md:grid-cols-[1.4fr_1fr]">
    {{-- plan --}}
    <section class="panel p-5 sm:p-6" aria-labelledby="plan-h">
        <div class="flex flex-wrap items-center gap-2">
            <h2 id="plan-h" class="font-display text-xl font-semibold">Road to {{ $plan['target'] }}</h2>
            <span class="chip chip-accent">{{ $st['level'] }} · {{ $st['nclc'] }}</span>
        </div>
        <div class="bar mt-4" role="progressbar" aria-valuenow="{{ $plan['progress_percent'] }}" aria-valuemin="0" aria-valuemax="100"><i style="width: {{ max(2, $plan['progress_percent']) }}%"></i></div>
        <p class="mt-2 text-sm text-muted">{{ $plan['hours_planned_done'] }} of {{ $plan['hours_total_to_target'] }} hours (A1 to NCLC 7) · {{ $st['hours_left_in_stage'] }} h left in {{ $st['level'] }}</p>
        <dl class="mt-4 grid grid-cols-2 gap-3 text-sm">
            <div><dt class="text-muted">Ready around</dt><dd class="font-medium">{{ \Carbon\Carbon::parse($plan['target_ready_date'])->format('j M Y') }}</dd><dd class="text-xs text-muted">if you study every day</dd></div>
            <div><dt class="text-muted">{{ $cp['months'] }}-month checkpoint</dt><dd class="font-medium">{{ \Carbon\Carbon::parse($cp['date'])->format('j M Y') }}</dd><dd class="text-xs text-muted">about {{ $cp['level_then'] }} ({{ $cp['hours_then'] }} h)</dd></div>
        </dl>
        <p class="mt-4 rounded-xl border border-line bg-raised/50 p-3 text-sm text-muted">Honest note: NCLC 7 (about B2) needs roughly {{ $plan['hours_total_to_target'] }} study hours from zero. {{ $cp['months'] }} months at {{ $plan['daily_minutes'] / 60 }} h a day is about {{ $cp['hours_then'] }} h, so the realistic {{ $cp['months'] }}-month goal is <b class="text-fg">solid A2 and the start of B1</b>. <a class="text-accent underline" href="/tcf">See the full timeline</a>.</p>
    </section>

    {{-- stats --}}
    <section class="panel p-5 sm:p-6" aria-label="Your progress">
        <h2 class="font-display text-xl font-semibold">Your streak</h2>
        <p class="mt-2 text-5xl font-semibold">🔥 <span data-streak>0</span><span class="ml-2 text-base font-normal text-muted">days</span></p>
        <p class="mt-3 text-sm text-muted">Done items: <b class="text-fg" data-done-count>0</b> · Checklist today: <b class="text-fg" data-check-count>0 / 4</b></p>
        <div class="bar mt-2"><i data-check-bar style="width:0%"></i></div>
        <div class="mt-5 flex flex-wrap gap-2">
            <a href="/practice" class="btn btn-primary btn-sm">Start flashcards</a>
            <a href="/level-check" class="btn btn-sm">Level check</a>
        </div>
    </section>
</div>

{{-- today's checklist --}}
<section class="mt-8" aria-labelledby="today-h">
    <h2 id="today-h" class="font-display text-2xl font-semibold">Today's checklist</h2>
    <ol class="mt-4 grid gap-3">
        @foreach ($blocks as $i => $b)
            <li class="panel flex gap-4 p-4 sm:p-5">
                <label class="mt-1 flex shrink-0 cursor-pointer items-start">
                    <input type="checkbox" data-check="{{ $b['key'] }}" class="h-5 w-5 accent-[rgb(var(--accent))]" aria-label="Done: {{ $b['title'] }}">
                </label>
                <div class="min-w-0 flex-1">
                    <div class="flex flex-wrap items-center gap-2">
                        <h3 class="text-lg font-semibold">{{ $i + 1 }}. {{ $b['title'] }}</h3>
                        <span class="chip">{{ $b['minutes'] }} min</span>
                    </div>
                    <p class="text-sm text-muted">{{ $b['desc'] }}</p>
                    <div class="mt-3 flex flex-wrap gap-2">
                        @if (! empty($b['url']))
                            <a href="{{ $b['url'] }}" class="btn btn-sm btn-primary">Open flashcards</a>
                        @endif
                        @forelse ($b['items'] as $it)
                            <a href="/r/{{ $it->slug }}" class="btn btn-sm">
                                <span class="chip {{ $it->source === 'daily' ? 'chip-gold' : '' }}">{{ $it->level }}</span>
                                {{ $it->title }}
                            </a>
                        @empty
                            @if (empty($b['url']))
                                <span class="text-sm text-muted">Nothing new yet today. The daily automation adds it in the morning; meanwhile <a class="text-accent underline" href="/learn">browse the library</a>.</span>
                            @endif
                        @endforelse
                    </div>
                </div>
            </li>
        @endforeach
    </ol>
</section>

{{-- latest --}}
@if ($latest->isNotEmpty())
    <section class="mt-10" aria-labelledby="new-h">
        <div class="flex items-end justify-between"><h2 id="new-h" class="font-display text-2xl font-semibold">New this week</h2><a href="/learn" class="text-sm text-accent underline">All materials</a></div>
        <ul class="mt-4 grid gap-3 sm:grid-cols-2">
            @foreach ($latest as $m)
                <li><a href="/r/{{ $m->slug }}" class="panel block p-4 transition hover:border-accent/50">
                    <div class="flex items-center gap-2"><span class="chip chip-gold">{{ $m->level }}</span><span class="chip">{{ $kindLabel[$m->kind] ?? $m->kind }}</span><span class="ml-auto text-xs text-muted">{{ $m->publish_on?->format('j M') }}</span></div>
                    <p class="mt-2 font-medium">{{ $m->title }}</p>
                    @if ($m->summary)<p class="text-sm text-muted">{{ $m->summary }}</p>@endif
                </a></li>
            @endforeach
        </ul>
    </section>
@endif
@endsection

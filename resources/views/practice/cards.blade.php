@extends('layouts.app')

@section('title', 'Flashcards · TCF Canada Prep')

@section('content')
<h1 class="font-display text-4xl font-medium sm:text-5xl">Flash<em>cards</em></h1>
<p class="mt-2 text-muted">Spaced repetition: a word comes back right before you would forget it. Up to <b class="text-fg">{{ $newPerDay }}</b> new words a day, levels {{ implode(', ', $levels) }}.</p>

<div class="mt-4 flex flex-wrap gap-2 text-sm">
    <span class="chip">Due: <b id="stat-due">0</b></span>
    <span class="chip">New today: <b id="stat-new">0</b></span>
    <span class="chip">Learned: <b id="stat-learned">0</b></span>
    <span class="chip" id="left">0 left</span>
</div>

<div id="deck" data-levels="{{ implode(',', $levels) }}" data-new="{{ $newPerDay }}" class="mt-6">
    <div id="card-stage" class="panel p-6 text-center sm:p-10">
        <div id="card-front">
            <span class="chip chip-accent" id="c-lvl"></span>
            <p class="fr mt-4 text-5xl font-semibold sm:text-6xl" id="c-word"></p>
            <p class="ipa mt-2"><span id="c-ipa"></span> <span id="c-pos"></span></p>
            <button type="button" id="c-listen" class="btn btn-sm mt-4" data-speak="" aria-label="Listen">🔊 Listen</button>
        </div>
        <div id="card-back" class="mx-auto mt-6 max-w-xl border-t border-line pt-6 text-left" hidden>
            <p class="text-2xl font-medium" id="c-en"></p>
            <p class="mt-1 text-lg text-muted" id="c-hi"></p>
            <p class="fr mt-4 text-xl" id="c-ex"></p>
            <p class="text-sm text-muted" id="c-exen"></p>
            <button type="button" id="c-listen-ex" class="btn btn-sm mt-3" data-speak="" aria-label="Listen to the sentence">🔊 Sentence</button>
        </div>
        <div class="mt-8">
            <button type="button" id="btn-show" class="btn btn-primary">Show answer <span class="text-xs opacity-70">(Space)</span></button>
            <div id="grades" class="grid grid-cols-2 gap-2 sm:grid-cols-4" hidden>
                <button type="button" class="btn" data-grade="1">Again <span class="text-xs text-muted">1</span></button>
                <button type="button" class="btn" data-grade="2">Hard <span class="text-xs text-muted">2</span></button>
                <button type="button" class="btn btn-primary" data-grade="3">Good <span class="text-xs opacity-70">3</span></button>
                <button type="button" class="btn" data-grade="4">Easy <span class="text-xs text-muted">4</span></button>
            </div>
        </div>
    </div>

    <div id="deck-done" class="panel p-8 text-center" hidden>
        <p class="font-display text-3xl font-semibold">🎉 Bravo !</p>
        <p class="mt-2 text-muted" id="done-text"></p>
        <a href="/" class="btn btn-primary mt-5">Back to today</a>
    </div>
</div>

<script type="application/json" id="deck-data">@json($deck, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG)</script>
<p class="mt-6 text-sm text-muted">Shortcuts: Space shows the answer, 1-4 grade it, P plays the word. Your schedule is saved in this browser.</p>
@endsection

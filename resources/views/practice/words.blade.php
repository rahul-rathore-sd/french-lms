@extends('layouts.app')

@section('title', 'Words · TCF Canada Prep')

@section('content')
<h1 class="font-display text-4xl font-medium sm:text-5xl">The <em>lexicon</em></h1>
<p class="mt-2 text-muted">Search French, English or Hindi. Tap 🔊 to hear the word.</p>

<div id="words" class="mt-6">
    <input id="word-q" type="search" placeholder="Search a word…" class="w-full rounded-xl border border-line bg-raised/50 px-4 py-3" aria-label="Search words" autocomplete="off">
    <div class="mt-3 flex flex-wrap items-center gap-2">
        @foreach (['all' => 'All', 'A1' => 'A1', 'A2' => 'A2', 'B1' => 'B1', 'B2' => 'B2', 'C1' => 'C1'] as $v => $label)
            <button type="button" data-lvl="{{ $v }}" class="chip {{ $v === 'all' ? 'chip-accent' : '' }}">{{ $label }}</button>
        @endforeach
        <span id="word-count" class="ml-auto text-sm text-muted"></span>
    </div>
    <div id="word-list" class="mt-5 grid gap-3"></div>
</div>

<script type="application/json" id="deck-data">@json($deck, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG)</script>
@endsection

@extends('layouts.app')

@section('title', 'Level check · TCF Canada Prep')

@section('content')
<h1 class="font-display text-4xl font-medium sm:text-5xl">Level <em>check</em></h1>
<p class="mt-2 max-w-2xl text-muted">Not "do I know a few sentences", but "can I use French to do things". Tick what you can really do without help. Be strict with yourself.</p>

<div id="level-check" class="mt-6 grid gap-4 md:grid-cols-2">
    <section class="panel p-5">
        <h2 class="font-display text-xl font-semibold">A1 · can-do <span class="chip chip-accent">NCLC 1-3</span></h2>
        <ul class="mt-3 grid gap-2 text-sm">
            @foreach ($a1 as $t)
                <li><label class="flex cursor-pointer items-start gap-2"><input type="checkbox" data-lc="a1" class="mt-1 accent-[rgb(var(--accent))]"> <span>{{ $t }}</span></label></li>
            @endforeach
        </ul>
    </section>
    <section class="panel p-5">
        <h2 class="font-display text-xl font-semibold">A2 · can-do <span class="chip chip-accent">NCLC 4</span></h2>
        <ul class="mt-3 grid gap-2 text-sm">
            @foreach ($a2 as $t)
                <li><label class="flex cursor-pointer items-start gap-2"><input type="checkbox" data-lc="a2" class="mt-1 accent-[rgb(var(--accent))]"> <span>{{ $t }}</span></label></li>
            @endforeach
        </ul>
    </section>
    <p id="lc-result" class="panel p-4 text-sm md:col-span-2" aria-live="polite"></p>
</div>

@include('learn.kinds.quiz', ['questions' => $quiz, 'quizTitle' => 'Mini quiz (mixed A1 to B1)'])
<p id="quiz-result" class="mt-3 text-sm text-muted" aria-live="polite"></p>

<div class="panel mt-8 p-5 text-sm">
    <h2 class="font-display text-xl font-semibold">What the levels mean</h2>
    <div class="prose-box mt-3 overflow-x-auto">
        <table>
            <thead><tr><th>Level</th><th>NCLC</th><th>TCF score (listening / reading)</th><th>In plain words</th></tr></thead>
            <tbody>
                <tr><td><b>A1</b></td><td>1-3</td><td>100-199</td><td>Basic phrases, introduce yourself, simple questions if spoken slowly</td></tr>
                <tr><td><b>A2</b></td><td>4</td><td>200-299</td><td>Everyday topics in short simple sentences, shopping, routine, the past</td></tr>
                <tr><td><b>B1</b></td><td>5-6</td><td>300-399</td><td>Handle most situations, give opinions and plans</td></tr>
                <tr><td><b>B2</b></td><td>7-8</td><td>400-499</td><td>Your target. Follow news and interviews, write formal letters, argue a point</td></tr>
            </tbody>
        </table>
    </div>
    <p class="mt-3 text-muted">Free official-style tests (TV5Monde, Alliance Française, France Éducation international) can confirm your level. Learned phrases alone are not A1: you must be able to build new sentences.</p>
</div>
@endsection

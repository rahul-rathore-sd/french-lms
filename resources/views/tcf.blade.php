@extends('layouts.app')

@section('title', 'TCF Canada · TCF Canada Prep')

@section('content')
@php
    $row7 = collect($nclc)->firstWhere('nclc', 7);
    $skillName = ['CO' => 'Listening (CO)', 'CE' => 'Reading (CE)', 'EE' => 'Writing (EE)', 'EO' => 'Speaking (EO)'];
@endphp

<h1 class="font-display text-4xl font-medium sm:text-5xl">TCF Canada <em>→ NCLC 7</em></h1>
<p class="mt-2 max-w-2xl text-muted">{{ $tcf['tagline'] }} Four separate tests. Each one is converted to its own NCLC level, and the <b class="text-fg">lowest</b> of the four is the weakest link, so do not neglect any skill.</p>

{{-- target numbers --}}
<section class="mt-8" aria-labelledby="t-h">
    <h2 id="t-h" class="font-display text-2xl font-semibold">Your target numbers (NCLC 7)</h2>
    <div class="mt-4 grid grid-cols-2 gap-3 lg:grid-cols-4">
        @foreach (['CO' => $row7['tcfCO'], 'CE' => $row7['tcfCE'], 'EE' => $row7['tcfEE'], 'EO' => $row7['tcfEO']] as $sk => $val)
            <div class="panel p-4">
                <p class="text-sm text-muted">{{ $skillName[$sk] }}</p>
                <p class="font-display mt-1 text-3xl font-semibold">{{ $val }}</p>
                <p class="text-xs text-muted">{{ in_array($sk, ['CO', 'CE']) ? 'points out of 699' : 'points out of 20' }}</p>
            </div>
        @endforeach
    </div>
    <p class="mt-3 text-sm text-muted">{{ $row7['note'] }} NCLC 7 is about CEFR {{ $row7['cefr'] }}.</p>
</section>

{{-- format --}}
<section class="mt-10" aria-labelledby="f-h">
    <h2 id="f-h" class="font-display text-2xl font-semibold">The four tests</h2>
    <div class="mt-4 grid gap-3 sm:grid-cols-2">
        @foreach ($tcf['parts'] as $p)
            <div class="panel p-4">
                <div class="flex items-center gap-2"><span class="chip chip-accent">{{ $p['skill'] }}</span><b>{{ $skillName[$p['skill']] }}</b><span class="chip ml-auto">{{ $p['duration'] }}</span></div>
                <p class="mt-2 text-sm">{{ $p['detail'] }}</p>
                @if (! empty($p['detail_hi']))<p class="text-sm text-muted">{{ $p['detail_hi'] }}</p>@endif
            </div>
        @endforeach
    </div>
    <p class="mt-3 text-sm text-muted">Scoring: {{ $tcf['scoring'] }}</p>
</section>

{{-- NCLC table --}}
<section class="mt-10" aria-labelledby="n-h">
    <h2 id="n-h" class="font-display text-2xl font-semibold">TCF Canada → NCLC</h2>
    <div class="panel prose-box mt-4 overflow-x-auto p-2">
        <table>
            <thead><tr><th>NCLC</th><th>CEFR</th><th>Listening</th><th>Reading</th><th>Writing</th><th>Speaking</th></tr></thead>
            <tbody>
                @foreach ($nclc as $r)
                    <tr class="{{ $r['nclc'] == 7 ? 'bg-accent/10' : '' }}">
                        <td><b>{{ $r['nclc'] }}</b>{!! $r['nclc'] == 7 ? ' <span class="chip chip-accent">target</span>' : '' !!}</td>
                        <td>{{ $r['cefr'] }}</td><td>{{ $r['tcfCO'] }}</td><td>{{ $r['tcfCE'] }}</td><td>{{ $r['tcfEE'] }}</td><td>{{ $r['tcfEO'] }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <p class="mt-2 text-sm text-muted">Bands as published by IRCC / France Éducation international. Check them again on the official pages before you book: formats and conversion tables can change.</p>
</section>

{{-- roadmap + timeline --}}
<section class="mt-10" aria-labelledby="r-h">
    <h2 id="r-h" class="font-display text-2xl font-semibold">Realistic timeline</h2>
    <p class="mt-2 max-w-2xl text-muted">From zero to NCLC 7 needs about <b class="text-fg">{{ $total }} study hours</b> (guided learning plus practice). Speed depends only on how many hours you really study.</p>

    <div class="panel prose-box mt-4 overflow-x-auto p-2">
        <table>
            <thead><tr><th>Study per day</th><th>Days</th><th>About</th><th>Ready date (if you start {{ \Carbon\Carbon::parse($plan['date'])->format('j M Y') }})</th></tr></thead>
            <tbody>
                @foreach ($scenarios as $s)
                    <tr class="{{ $s['hours'] * 60 == $plan['daily_minutes'] ? 'bg-accent/10' : '' }}">
                        <td><b>{{ $s['hours'] }} h</b>{!! $s['hours'] * 60 == $plan['daily_minutes'] ? ' <span class="chip chip-accent">your plan</span>' : '' !!}</td>
                        <td>{{ $s['days'] }}</td><td>{{ $s['months'] }} months</td>
                        <td>{{ \Carbon\Carbon::parse($plan['date'])->addDays($s['days'] - 1)->format('j M Y') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <p class="mt-3 rounded-xl border border-line bg-raised/50 p-3 text-sm text-muted">Your wish was {{ $plan['wish_checkpoint']['months'] }} months. At {{ $plan['daily_minutes'] / 60 }} h a day that is about {{ $plan['wish_checkpoint']['hours_then'] }} hours: enough to finish A2 and begin B1, <b class="text-fg">not yet NCLC 7</b>. Keep {{ $plan['wish_checkpoint']['months'] }} months as your first checkpoint, then continue to B2. Real life varies: if you already know more, the plan moves faster.</p>

    <ol class="mt-6 grid gap-3">
        @foreach ($roadmap as $s)
            @if ($s['stage'] <= 4)
                <li class="panel p-4 sm:p-5">
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="chip chip-accent">{{ $s['level'] }}</span><span class="chip">{{ $s['nclc'] }}</span><span class="chip chip-gold">{{ $s['hours'] }}</span>
                        <b>{{ $s['title'] }}</b>
                    </div>
                    <ul class="mt-2 list-disc space-y-0.5 pl-5 text-sm">@foreach ($s['goals'] as $g)<li>{{ $g }}</li>@endforeach</ul>
                    <p class="mt-2 text-sm text-muted">Do now: {{ implode(' · ', $s['doNow']) }}</p>
                </li>
            @endif
        @endforeach
    </ol>
</section>

{{-- tips --}}
<section class="mt-10" aria-labelledby="tip-h">
    <h2 id="tip-h" class="font-display text-2xl font-semibold">Exam tips</h2>
    <ul class="panel mt-4 list-disc space-y-1.5 p-5 pl-9 text-sm">
        @foreach ($tcf['tips'] as $i => $tip)
            <li>{{ $tip }}@if (! empty($tcf['tips_hi'][$i])) <span class="text-muted"> · {{ $tcf['tips_hi'][$i] }}</span>@endif</li>
        @endforeach
    </ul>
</section>
@endsection

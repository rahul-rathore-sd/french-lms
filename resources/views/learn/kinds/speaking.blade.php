@php($b = $m->body)
<div class="panel p-5 sm:p-6">
    @if (! empty($b['exam']))<span class="chip">{{ $b['exam'] }}</span>@endif
    <p class="fr mt-2 text-xl leading-relaxed">{{ $b['prompt'] }}</p>
    @if (! empty($b['prompt_hi']))<p class="mt-1 text-sm text-muted">{{ $b['prompt_hi'] }}</p>@endif

    <div class="mt-5 flex flex-wrap items-center gap-4 rounded-xl border border-line bg-raised/40 p-4" data-timer="{{ ((int) ($b['speak_minutes'] ?? 2)) * 60 }}">
        <span class="font-mono text-3xl font-semibold" data-time>02:00</span>
        <button type="button" class="btn btn-primary btn-sm">Start</button>
        <p class="text-sm text-muted">Speak out loud without stopping. Record yourself on your phone and listen back.</p>
    </div>

    @if (! empty($b['phrases']))
        <div class="mt-5">
            <h3 class="font-semibold">Phrases you can use</h3>
            <ul class="mt-2 grid gap-2">
                @foreach ($b['phrases'] as $p)
                    <li class="flex items-start gap-3 rounded-xl border border-line bg-raised/40 p-3">
                        <button type="button" class="btn btn-sm shrink-0" data-speak="{{ $p['fr'] }}" aria-label="Listen">🔊</button>
                        <div><p class="fr text-lg font-semibold">{{ $p['fr'] }}</p>@if (! empty($p['en']))<p class="text-sm">{{ $p['en'] }}</p>@endif @if (! empty($p['hi']))<p class="text-sm text-muted">{{ $p['hi'] }}</p>@endif</div>
                    </li>
                @endforeach
            </ul>
        </div>
    @endif

    @if (! empty($b['model_fr']))
        <button type="button" class="btn mt-5" data-reveal="#model" data-show-text="Show model answer" data-hide-text="Hide model answer">Show model answer</button>
        <div id="model" class="mt-3 rounded-xl border border-line bg-raised/40 p-4" hidden>
            <p class="fr whitespace-pre-line text-lg leading-relaxed">{{ $b['model_fr'] }}</p>
            <button type="button" class="btn btn-sm mt-3" data-speak-from="#model p" data-stop="1">🔊 Listen</button>
        </div>
    @endif
</div>

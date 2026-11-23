@php($b = $m->body)
<div class="panel p-5 sm:p-6">
    @if (! empty($b['exam']))<span class="chip">{{ $b['exam'] }}</span>@endif
    <p class="fr mt-2 text-xl leading-relaxed">{{ $b['prompt'] }}</p>
    @if (! empty($b['prompt_hi']))<p class="mt-1 text-sm text-muted">{{ $b['prompt_hi'] }}</p>@endif

    <label for="answer" class="mt-5 block text-sm font-medium">Your answer</label>
    <textarea id="answer" rows="10" class="mt-1 w-full rounded-xl border border-line bg-raised/50 p-3 text-base leading-relaxed" data-wordcount="#wc" data-min="{{ $b['min_words'] ?? 0 }}" placeholder="Écrivez ici…"></textarea>
    <p id="wc" class="mt-1 text-sm text-muted"></p>

    @if (! empty($b['checklist']))
        <fieldset class="mt-5">
            <legend class="font-semibold">Check your text</legend>
            <ul class="mt-2 grid gap-1.5 text-sm">
                @foreach ($b['checklist'] as $c)
                    <li><label class="flex cursor-pointer items-start gap-2"><input type="checkbox" class="mt-1 accent-[rgb(var(--accent))]"> <span>{{ $c }}</span></label></li>
                @endforeach
            </ul>
        </fieldset>
    @endif

    @if (! empty($b['useful_phrases']))
        <div class="mt-5">
            <h3 class="font-semibold">Useful phrases</h3>
            <ul class="mt-2 grid gap-1 text-sm sm:grid-cols-2">
                @foreach ($b['useful_phrases'] as $p)
                    <li class="fr">{{ is_array($p) ? ($p['fr'] ?? '') : $p }}@if (is_array($p) && ! empty($p['en'])) <span class="font-sans text-muted">· {{ $p['en'] }}</span>@endif</li>
                @endforeach
            </ul>
        </div>
    @endif

    <button type="button" class="btn mt-5" data-reveal="#model" data-show-text="Show model answer" data-hide-text="Hide model answer">Show model answer</button>
    <div id="model" class="mt-3 rounded-xl border border-line bg-raised/40 p-4" hidden>
        <p class="fr whitespace-pre-line text-lg leading-relaxed">{{ $b['model_fr'] }}</p>
        @if (! empty($b['model_en']))<p class="mt-3 text-sm text-muted">{{ $b['model_en'] }}</p>@endif
        <button type="button" class="btn btn-sm mt-3" data-speak-from="#model p" data-stop="1">🔊 Listen</button>
    </div>
</div>

@php($b = $m->body)
@php($listening = $m->kind === 'listening')
<div class="panel p-5 sm:p-6">
    @if ($listening)
        <p class="text-sm text-muted">In the real TCF the audio plays <b class="text-fg">once</b>. First listen with the text hidden, answer the questions, then check the transcript.</p>
        <div class="mt-4 flex flex-wrap items-center gap-2">
            <button type="button" class="btn btn-primary" data-speak-from="#script" data-stop="1">▶ Play / stop</button>
            <button type="button" class="btn" data-speak-from="#script" data-rate="0.8">🐢 Slow</button>
        </div>
        <details class="mt-4">
            <summary class="cursor-pointer text-sm text-accent">Show transcript (after you answered)</summary>
            <p id="script" class="fr mt-3 whitespace-pre-line text-lg leading-relaxed">{{ $b['fr'] }}</p>
        </details>
    @else
        <p id="script" class="fr whitespace-pre-line text-lg leading-relaxed sm:text-xl">{{ $b['fr'] }}</p>
        <button type="button" class="btn btn-sm mt-3" data-speak-from="#script" data-stop="1">🔊 Read aloud</button>
    @endif

    <details class="mt-4">
        <summary class="cursor-pointer text-sm text-accent">Translation (English / हिन्दी)</summary>
        <p class="mt-2">{{ $b['en'] }}</p>
        @if (! empty($b['hi']))<p class="mt-2 text-muted">{{ $b['hi'] }}</p>@endif
    </details>

    @if (! empty($b['glossary']))
        <details class="mt-3">
            <summary class="cursor-pointer text-sm text-accent">Useful words</summary>
            <ul class="mt-2 grid gap-1 text-sm sm:grid-cols-2">
                @foreach ($b['glossary'] as $g)
                    <li><span class="fr font-semibold">{{ $g['fr'] }}</span> · {{ $g['en'] }}@if (! empty($g['hi'])) <span class="text-muted">· {{ $g['hi'] }}</span>@endif</li>
                @endforeach
            </ul>
        </details>
    @endif
</div>

@include('learn.kinds.quiz', ['questions' => $b['questions'] ?? [], 'quizTitle' => 'Questions'])

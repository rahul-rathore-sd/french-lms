{{-- expects $questions (array of {q, options[], correct, explain}) and optional $quizTitle --}}
@if (! empty($questions))
    <section class="panel mt-6 p-5 sm:p-6" data-quiz aria-label="Quiz">
        <div class="flex items-center justify-between gap-3">
            <h2 class="font-display text-xl font-semibold">{{ $quizTitle ?? 'Check yourself' }}</h2>
            <span class="chip" data-score>0 / {{ count($questions) }}</span>
        </div>
        <div class="mt-4 grid gap-6">
            @foreach ($questions as $qi => $qu)
                <div class="qz" data-correct="{{ $qu['correct'] }}">
                    <p class="fr text-lg font-medium">{{ $qi + 1 }}. {{ $qu['q'] }}</p>
                    <div class="mt-2 grid gap-2">
                        @foreach ($qu['options'] as $oi => $opt)
                            <button type="button" class="opt" data-i="{{ $oi }}">{{ $opt }}</button>
                        @endforeach
                    </div>
                    <p class="qz-explain mt-2 rounded-lg bg-raised/60 p-3 text-sm text-muted" hidden>💡 {{ $qu['explain'] ?? '' }}</p>
                </div>
            @endforeach
        </div>
    </section>
@endif

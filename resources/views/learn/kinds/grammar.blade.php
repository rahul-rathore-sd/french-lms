@php($b = $m->body)
<div class="panel p-5 sm:p-6">
    @if (! empty($b['summary_hi']))<p class="text-muted">{{ $b['summary_hi'] }}</p>@endif

    <div class="mt-2 grid gap-6">
        @foreach ($b['rules'] ?? [] as $rule)
            <div>
                <h2 class="text-lg font-semibold leading-snug">{{ $rule['rule'] }}</h2>
                @if (! empty($rule['rule_hi']))<p class="text-sm text-muted">{{ $rule['rule_hi'] }}</p>@endif
                <ul class="mt-3 grid gap-2">
                    @foreach ($rule['examples'] ?? [] as $ex)
                        <li class="flex items-start gap-3 rounded-xl border border-line bg-raised/40 p-3">
                            <button type="button" class="btn btn-sm shrink-0" data-speak="{{ $ex['fr'] }}" aria-label="Listen">🔊</button>
                            <div class="min-w-0">
                                <p class="fr text-lg font-semibold">{{ $ex['fr'] }}</p>
                                <p class="text-sm">{{ $ex['en'] }}</p>
                                @if (! empty($ex['hi']))<p class="text-sm text-muted">{{ $ex['hi'] }}</p>@endif
                            </div>
                        </li>
                    @endforeach
                </ul>
            </div>
        @endforeach
    </div>

    @foreach ($b['tables'] ?? [] as $t)
        <div class="prose-box mt-6 overflow-x-auto">
            <p class="font-medium">{{ $t['caption'] ?? '' }}</p>
            <table>
                <thead><tr>@foreach ($t['headers'] ?? [] as $h)<th>{{ $h }}</th>@endforeach</tr></thead>
                <tbody>@foreach ($t['rows'] ?? [] as $row)<tr>@foreach ($row as $cell)<td class="fr">{{ $cell }}</td>@endforeach</tr>@endforeach</tbody>
            </table>
        </div>
    @endforeach

    @if (! empty($b['tips']))
        <div class="mt-6 rounded-xl border border-line bg-raised/40 p-4">
            <h3 class="font-semibold">Tips</h3>
            <ul class="mt-2 list-disc space-y-1 pl-5 text-sm">
                @foreach ($b['tips'] as $i => $tip)
                    <li>{{ $tip }}@if (! empty($b['tips_hi'][$i])) <span class="text-muted"> · {{ $b['tips_hi'][$i] }}</span>@endif</li>
                @endforeach
            </ul>
        </div>
    @endif
</div>

@include('learn.kinds.quiz', ['questions' => $b['quiz'] ?? [], 'quizTitle' => 'Quick quiz'])

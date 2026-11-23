<?php

namespace App\Http\Controllers;

use App\Models\Material;
use App\Support\Plan;
use Illuminate\Contracts\View\View;

/** "Today": where the learner is in the plan and the 2-hour study checklist for the day. */
class HomeController extends Controller
{
    public function __invoke(): View
    {
        $plan = Plan::summary();
        $level = $plan['stage']['level'];
        $day = $plan['day_number'];

        $daily = Material::where('source', 'daily')->whereDate('publish_on', $plan['date'])->get()->groupBy('kind');

        // Today's item: the one the automation added today, else a starter item at (or just above) the current level.
        $pick = function (string $kind) use ($daily, $level, $day): ?Material {
            if ($daily->has($kind)) {
                return $daily[$kind]->first();
            }
            $seed = Material::where('source', 'seed')->where('kind', $kind)->where('level', '>=', $level)
                ->orderBy('level')->orderBy('id')->get();
            if ($seed->isEmpty()) {
                return null;
            }
            $first = $seed->first()->level;
            $same = $seed->where('level', $first)->values();

            return $same[($day - 1) % $same->count()];
        };

        $produceKind = $plan['writing_or_speaking'];
        $blocks = [
            ['key' => 'vocab', 'minutes' => 30, 'title' => 'Vocabulary and flashcards', 'desc' => $plan['quota']['vocab'].' new words and your reviews', 'items' => [], 'url' => '/practice'],
            ['key' => 'grammar', 'minutes' => 30, 'title' => 'Grammar focus', 'desc' => 'One rule, examples and a short quiz', 'items' => array_filter([$pick('grammar')])],
            ['key' => 'listenread', 'minutes' => 30, 'title' => 'Listening and reading', 'desc' => 'Audio exercise, then a reading text', 'items' => array_filter([$pick('listening'), $pick('reading')])],
            ['key' => 'produce', 'minutes' => 30, 'title' => $produceKind === 'writing' ? 'Writing task' : 'Speaking task', 'desc' => $produceKind === 'writing' ? 'Write it, then compare with the model' : 'Say it aloud with the timer, then compare', 'items' => array_filter([$pick($produceKind)])],
        ];

        return view('home', [
            'plan' => $plan,
            'blocks' => $blocks,
            'latest' => Material::where('source', 'daily')->orderByDesc('publish_on')->orderByDesc('id')->limit(6)->get(),
            'counts' => Material::selectRaw('kind, COUNT(*) as n')->groupBy('kind')->pluck('n', 'kind'),
        ]);
    }
}

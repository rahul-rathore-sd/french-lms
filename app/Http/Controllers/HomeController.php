<?php

namespace App\Http\Controllers;

use App\Models\Material;
use App\Support\Plan;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

/** "Today": where this learner is in their plan and the 2-hour study checklist for the day. */
class HomeController extends Controller
{
    public function __invoke(Request $request): View
    {
        $plan = Plan::for($request->attributes->get('learner'))->summary();
        $level = $plan['stage']['level'];
        $day = $plan['day_number'];

        // Today's item at the learner's level: the one the automation added today, else the newest earlier one,
        // else a starter item at (or just above) the level.
        $pick = function (string $kind) use ($plan, $level, $day): ?Material {
            $daily = Material::available()->where('source', 'daily')->where('kind', $kind)->where('level', $level);
            $today = (clone $daily)->whereDate('publish_on', $plan['date'])->orderByDesc('id')->first()
                ?? (clone $daily)->orderByDesc('publish_on')->orderByDesc('id')->first();
            if ($today) {
                return $today;
            }
            $seed = Material::where('source', 'seed')->where('kind', $kind)->where('level', '>=', $level)
                ->orderBy('level')->orderBy('id')->get();
            if ($seed->isEmpty()) {
                return null;
            }
            $same = $seed->where('level', $seed->first()->level)->values();

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
            'latest' => Material::available()->where('source', 'daily')->where('level', $level)->orderByDesc('publish_on')->orderByDesc('id')->limit(6)->get(),
        ]);
    }
}

<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Learner;
use App\Models\Material;
use App\Models\Vocabulary;
use App\Support\Api;
use App\Support\Plan;
use Carbon\CarbonImmutable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * GET /api/v1/plan/today[?date=YYYY-MM-DD]
 * What the daily automation needs: for every level that at least one learner is currently on, the day
 * numbers, the quota and what was already added today; plus what exists so far (no repeated topic or word).
 * Learner names and PINs are never exposed here.
 */
class PlanController extends Controller
{
    public function today(Request $request): JsonResponse
    {
        Api::requireActor($request);
        $date = Plan::today();
        if ($d = $request->query('date')) {
            $date = preg_match('/^\d{4}-\d{2}-\d{2}$/', $d) ? CarbonImmutable::parse($d) : Api::fail('date must be YYYY-MM-DD.', 422);
        }
        $day = $date->toDateString();

        // one entry per level that somebody is studying on that date (before anyone registers: the default plan)
        $plans = Learner::all()->map(fn ($l) => Plan::for($l)->summary($date));
        if ($plans->isEmpty()) {
            $plans = collect([Plan::default()->summary($date)]);
        }
        $levels = $plans->groupBy(fn ($s) => $s['stage']['level'])->map(function ($group, $level) use ($day) {
            $added = Material::where('source', 'daily')->whereDate('publish_on', $day)->where('level', $level)->get()->groupBy('kind')->map->count();
            $vocab = Vocabulary::where('data', 'like', '%"source":"Daily"%')->where('data', 'like', '%"level":"'.$level.'"%')
                ->where('created_at', 'like', $day.'%')->count();

            return [
                'level' => $level,
                'nclc' => $group->first()['stage']['nclc'],
                'learners' => $group->count(),
                'day_numbers' => $group->pluck('day_number')->values()->all(),
                'already_added_today' => $added->all() + ['vocab_words' => $vocab],
            ];
        })->values();

        $first = $plans->first();

        return Api::json(['success' => true, 'data' => [
            'date' => $day,
            'writing_or_speaking' => $first['writing_or_speaking'],
            'quota' => $first['quota'],
            'levels_needed' => $levels->pluck('level')->all(),
            'levels' => $levels,
            'counts' => Material::selectRaw('kind, level, COUNT(*) as n')->groupBy('kind', 'level')->get()->groupBy('kind')->map(fn ($g) => $g->pluck('n', 'level')),
            'recent_titles' => Material::where('source', 'daily')->orderByDesc('id')->limit(80)->get(['kind', 'level', 'title', 'slug'])
                ->map(fn ($m) => "{$m->kind}/{$m->level}: {$m->title}")->all(),
            // every word already in the deck (core list + earlier daily sets), so new sets never repeat one
            'existing_words' => Vocabulary::orderBy('id')->limit(5000)->pluck('french_word')->all(),
            'seed_grammar_slugs' => Material::where('kind', 'grammar')->where('source', 'seed')->pluck('slug')->all(),
        ]]);
    }
}

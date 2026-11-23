<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Material;
use App\Models\Vocabulary;
use App\Support\Api;
use App\Support\Plan;
use Carbon\CarbonImmutable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * GET /api/v1/plan/today[?date=YYYY-MM-DD]
 * What the daily automation needs: the plan for that day (stage, level, quotas) plus what already
 * exists, so it never repeats a topic and can skip work that was already done today.
 */
class PlanController extends Controller
{
    public function today(Request $request): JsonResponse
    {
        Api::requireActor($request);
        $date = null;
        if ($d = $request->query('date')) {
            $date = preg_match('/^\d{4}-\d{2}-\d{2}$/', $d) ? CarbonImmutable::parse($d) : Api::fail('date must be YYYY-MM-DD.', 422);
        }
        $plan = Plan::summary($date);

        $byKind = Material::selectRaw('kind, level, COUNT(*) as n')->groupBy('kind', 'level')->get()
            ->groupBy('kind')->map(fn ($g) => $g->pluck('n', 'level'));
        $addedToday = Material::where('source', 'daily')->whereDate('publish_on', $plan['date'])->get(['kind', 'slug'])
            ->groupBy('kind')->map->count();
        $vocabToday = Vocabulary::where('data', 'like', '%"source":"Daily"%')->where('created_at', 'like', $plan['date'].'%')->count();

        return Api::json(['success' => true, 'data' => $plan + [
            'already_added_today' => $addedToday->all() + ['vocab_words' => $vocabToday],
            'counts' => $byKind,
            'recent_titles' => Material::where('source', 'daily')->orderByDesc('id')->limit(80)->get(['kind', 'level', 'title', 'slug'])
                ->map(fn ($m) => "{$m->kind}/{$m->level}: {$m->title}")->all(),
            'recent_words' => Vocabulary::orderByDesc('id')->limit(150)->pluck('french_word')->all(),
            'seed_grammar_slugs' => Material::where('kind', 'grammar')->where('source', 'seed')->pluck('slug')->all(),
        ]]);
    }
}

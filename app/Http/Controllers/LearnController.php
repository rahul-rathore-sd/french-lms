<?php

namespace App\Http\Controllers;

use App\Models\Material;
use App\Support\Plan;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class LearnController extends Controller
{
    public const KINDS = ['grammar' => 'Grammar', 'listening' => 'Listening', 'reading' => 'Reading', 'writing' => 'Writing', 'speaking' => 'Speaking'];
    public const LEVELS = ['A1', 'A2', 'B1', 'B2', 'C1'];

    public function index(Request $request): View
    {
        $kind = array_key_exists($request->query('kind'), self::KINDS) ? $request->query('kind') : null;
        $level = in_array($request->query('level'), self::LEVELS, true) ? $request->query('level') : null;

        $items = Material::available()
            ->when($kind, fn ($q) => $q->where('kind', $kind))
            ->when($level, fn ($q) => $q->where('level', $level))
            ->orderBy('level')->orderBy('kind')->orderBy('id')
            ->get(['id', 'slug', 'kind', 'skill', 'level', 'nclc', 'title', 'title_hi', 'summary', 'minutes', 'source', 'publish_on']);

        return view('learn.index', [
            'items' => $items->groupBy('level'),
            'kind' => $kind, 'level' => $level,
            'kinds' => self::KINDS, 'levels' => self::LEVELS,
            'currentLevel' => Plan::for($request->attributes->get('learner'))->summary()['stage']['level'],
        ]);
    }

    public function show(string $slug): View
    {
        $m = Material::available()->where('slug', $slug)->first();
        abort_unless($m, 404);

        $next = Material::available()->where('kind', $m->kind)->where('level', $m->level)->where('id', '>', $m->id)->orderBy('id')->first();

        return view('learn.show', ['m' => $m, 'b' => $m->body, 'next' => $next, 'kinds' => self::KINDS]);
    }
}

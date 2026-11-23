<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Material;
use App\Models\Vocabulary;
use App\Support\Api;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

/**
 * Study materials API (used by the daily automation, authorised with the Hermes bearer key).
 *
 *   POST /api/v1/materials   upsert one item by slug. kind = grammar | reading | listening | writing | speaking | vocab
 *   GET  /api/v1/materials   list (filters: kind, level, source, limit)
 *   DELETE /api/v1/materials/{slug}
 */
class MaterialController extends Controller
{
    private const SKILL = ['grammar' => 'GR', 'reading' => 'CE', 'listening' => 'CO', 'writing' => 'EE', 'speaking' => 'EO'];

    public function index(Request $request): JsonResponse
    {
        Api::requireActor($request);
        $q = Material::query();
        foreach (['kind', 'level', 'source'] as $f) {
            if ($v = $request->query($f)) {
                $q->where($f, $v);
            }
        }
        $limit = max(1, min(500, (int) $request->query('limit', 100)));
        $rows = $q->orderByDesc('id')->limit($limit)->get(['id', 'slug', 'kind', 'skill', 'level', 'title', 'summary', 'source', 'publish_on', 'minutes']);

        return Api::json(['success' => true, 'total' => $rows->count(), 'data' => $rows]);
    }

    public function store(Request $request): JsonResponse
    {
        $who = Api::requireActor($request);
        $b = Api::body($request);
        $kind = $b['kind'] ?? '';
        if (! in_array($kind, [...array_keys(self::SKILL), 'vocab'], true)) {
            Api::fail('kind must be one of: grammar, reading, listening, writing, speaking, vocab.', 422);
        }

        $v = Validator::make($b, $this->rules($kind));
        $v->after(function ($v) use ($b, $kind) {
            foreach (['body.questions', 'body.quiz'] as $path) {
                foreach (data_get($b, $path, []) as $i => $qu) {
                    $opts = $qu['options'] ?? [];
                    if (! is_array($opts) || ! is_int($qu['correct'] ?? null) || $qu['correct'] < 0 || $qu['correct'] >= count($opts)) {
                        $v->errors()->add("$path.$i.correct", "$path.$i: \"correct\" must be the index of one of the options.");
                    } elseif (count(array_unique($opts)) !== count($opts)) {
                        $v->errors()->add("$path.$i.options", "$path.$i: options must be different from each other.");
                    }
                }
            }
        });
        if ($v->fails()) {
            return Api::json(['success' => false, 'error' => 'Validation failed: '.implode(' | ', array_slice($v->errors()->all(), 0, 6)), 'errors' => $v->errors()], 422);
        }

        if ($kind === 'vocab') {
            return $this->storeVocab($b, $who);
        }

        $slug = Api::s($b['slug'] ?? '', 120) ?: Str::slug(($b['level'] ?? '').' '.$kind.' '.$b['title']);
        $existing = Material::where('slug', $slug)->first();
        $material = Material::updateOrCreate(['slug' => $slug], [
            'kind' => $kind, 'skill' => self::SKILL[$kind], 'level' => $b['level'], 'nclc' => Api::s($b['nclc'] ?? '', 20) ?: null,
            'title' => Api::s($b['title'], 200), 'title_hi' => Api::s($b['title_hi'] ?? '', 200) ?: null,
            'summary' => Api::s($b['summary'] ?? '', 500), 'minutes' => (int) ($b['minutes'] ?? 10),
            'source' => 'daily', 'publish_on' => $b['publish_on'] ?? now()->toDateString(), 'body' => $b['body'],
        ]);
        Api::log($who, $existing ? 'update' : 'create', 'material', $material->id, "$kind $slug");

        return Api::json(['success' => true, 'data' => ['id' => $material->id, 'slug' => $slug, 'kind' => $kind, 'created' => ! $existing, 'url' => Api::siteUrl().'/r/'.$slug]], $existing ? 200 : 201);
    }

    public function destroy(Request $request, string $slug): JsonResponse
    {
        $who = Api::requireActor($request);
        $m = Material::where('slug', $slug)->first() ?? Api::fail('Not found.', 404);
        $m->delete();
        Api::log($who, 'delete', 'material', $m->id, $slug);

        return Api::json(['success' => true]);
    }

    private function storeVocab(array $b, string $who): JsonResponse
    {
        $added = $updated = 0;
        $skipped = [];
        foreach ($b['body']['words'] as $w) {
            $fr = Api::s($w['fr'], 120);
            $entry = [
                'word' => $fr, 'french_word' => $fr, 'ipa' => Api::s($w['ipa'] ?? '', 120) ?: null, 'ipa_phonetic' => Api::s($w['ipa'] ?? '', 120) ?: null,
                'part_of_speech' => Api::s($w['pos'] ?? '', 40) ?: null, 'meaning' => Api::s($w['en'], 300), 'meaning_en' => Api::s($w['en'], 300),
                'meaning_hi' => Api::s($w['hi'] ?? '', 300) ?: null, 'level' => $b['level'], 'cefr_level' => $b['level'],
                'category' => Api::s($w['category'] ?? ($b['title'] ?? 'Daily'), 80), 'example_fr' => Api::s($w['example_fr'], 300),
                'example_sentence' => Api::s($w['example_fr'], 300), 'example_en' => Api::s($w['example_en'], 300),
                'example_hi' => Api::s($w['example_hi'] ?? '', 300) ?: null, 'use_case' => 'TCF Canada daily vocabulary',
                'source' => 'Daily', 'created_at' => Api::nowIso(),
            ];
            $row = Vocabulary::whereRaw('LOWER(french_word) = ?', [mb_strtolower($fr)])->first();
            if ($row) {
                // never overwrite the core curriculum: only words added by this automation can be updated
                if ((json_decode($row->data, true)['source'] ?? '') !== 'Daily') {
                    $skipped[] = $fr;

                    continue;
                }
                $row->update(['data' => json_encode($entry, JSON_UNESCAPED_UNICODE)]);
                $updated++;
            } else {
                Vocabulary::create(['french_word' => $fr, 'data' => json_encode($entry, JSON_UNESCAPED_UNICODE), 'created_at' => Api::nowIso()]);
                $added++;
            }
        }
        Api::log($who, 'create', 'vocabulary', null, "daily set: +$added ~$updated skipped ".count($skipped));

        return Api::json(['success' => true, 'data' => ['kind' => 'vocab', 'added' => $added, 'updated' => $updated, 'skipped' => $skipped,
            'note' => $skipped ? 'These words already exist in the core list and were left unchanged. Post replacement words to reach 10.' : null]], 201);
    }

    private function rules(string $kind): array
    {
        $common = [
            'level' => ['required', Rule::in(['A1', 'A2', 'B1', 'B2', 'C1'])],
            'title' => ['required', 'string', 'max:200'],
            'title_hi' => ['nullable', 'string', 'max:200'],
            'summary' => ['nullable', 'string', 'max:500'],
            'nclc' => ['nullable', 'string', 'max:20'],
            'minutes' => ['nullable', 'integer', 'between:1,120'],
            'slug' => ['nullable', 'regex:/^[a-z0-9][a-z0-9-]{2,118}$/'],
            'publish_on' => ['nullable', 'date_format:Y-m-d'],
            'body' => ['required', 'array'],
        ];
        $question = [
            '*.q' => ['required', 'string', 'max:400'], '*.options' => ['required', 'array', 'min:2', 'max:5'], '*.options.*' => ['required', 'string', 'max:300'],
            '*.correct' => ['required', 'integer', 'min:0'], '*.explain' => ['required', 'string', 'max:600'],
        ];
        $q = fn (string $prefix, int $min) => ["$prefix" => ['required', 'array', "min:$min", 'max:12']]
            + collect($question)->mapWithKeys(fn ($r, $k) => ["$prefix.$k" => $r])->all();

        return $common + match ($kind) {
            'reading', 'listening' => [
                'body.fr' => ['required', 'string', 'min:40', 'max:6000'], 'body.en' => ['required', 'string', 'min:20', 'max:6000'], 'body.hi' => ['nullable', 'string', 'max:6000'],
                'body.glossary' => ['nullable', 'array', 'max:30'], 'body.glossary.*.fr' => ['required', 'string'], 'body.glossary.*.en' => ['required', 'string'],
            ] + $q('body.questions', 3),
            'grammar' => [
                'body.summary_hi' => ['nullable', 'string', 'max:800'], 'body.rules' => ['required', 'array', 'min:1', 'max:12'],
                'body.rules.*.rule' => ['required', 'string', 'max:800'], 'body.rules.*.examples' => ['required', 'array', 'min:2', 'max:12'],
                'body.rules.*.examples.*.fr' => ['required', 'string', 'max:300'], 'body.rules.*.examples.*.en' => ['required', 'string', 'max:300'],
                'body.tables' => ['nullable', 'array', 'max:6'], 'body.tips' => ['nullable', 'array', 'max:10'], 'body.tips.*' => ['string', 'max:400'],
            ] + $q('body.quiz', 3),
            'writing' => [
                'body.prompt' => ['required', 'string', 'max:1500'], 'body.min_words' => ['required', 'integer', 'between:20,400'],
                'body.model_fr' => ['required', 'string', 'min:40', 'max:4000'], 'body.model_en' => ['nullable', 'string', 'max:4000'],
                'body.checklist' => ['required', 'array', 'min:3', 'max:12'], 'body.checklist.*' => ['string', 'max:300'],
                'body.useful_phrases' => ['nullable', 'array', 'max:20'],
            ],
            'speaking' => [
                'body.prompt' => ['required', 'string', 'max:1500'], 'body.speak_minutes' => ['required', 'integer', 'between:1,10'],
                'body.phrases' => ['required', 'array', 'min:3', 'max:20'], 'body.phrases.*.fr' => ['required', 'string', 'max:300'],
                'body.model_fr' => ['nullable', 'string', 'max:3000'],
            ],
            'vocab' => [
                'body.words' => ['required', 'array', 'min:5', 'max:30'], 'body.words.*.fr' => ['required', 'string', 'max:120'],
                'body.words.*.en' => ['required', 'string', 'max:300'], 'body.words.*.example_fr' => ['required', 'string', 'max:300'],
                'body.words.*.example_en' => ['required', 'string', 'max:300'],
            ],
        };
    }
}

<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Vocabulary;
use App\Support\Api;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class VocabularyController extends Controller
{
    /** GET /vocabulary: public lexicon extras ingested by Hermes. */
    public function index(): JsonResponse
    {
        $words = [];
        foreach (Vocabulary::orderByDesc('id')->limit(2000)->get(['id', 'data']) as $r) {
            $w = json_decode($r->data, true);
            if (is_array($w)) {
                $w['id'] = 10000 + (int) $r->id;
                $words[] = $w;
            }
        }

        return Api::json(['success' => true, 'count' => count($words), 'data' => $words]);
    }

    /** POST /vocabulary: Hermes (or admin) adds/updates a word. */
    public function store(Request $request): JsonResponse
    {
        $who = Api::requireActor($request);
        $b = Api::body($request);
        $word = Api::s($b['french_word'] ?? '', 120);
        $en = Api::s($b['meaning_en'] ?? '', 500);
        $hi = Api::s($b['meaning_hi'] ?? '', 500);
        $use = Api::s($b['use_case'] ?? '', 800);
        if ($word === '' || $en === '' || $hi === '' || $use === '') {
            Api::fail('french_word, meaning_en, meaning_hi and use_case are required.', 422);
        }
        $level = strtoupper(Api::s($b['cefr_level'] ?? '', 2));
        $level = in_array($level, Api::CEFR, true) ? $level : 'B2';
        $example = Api::s($b['example_sentence'] ?? '', 500) ?: $word;
        $ipa = Api::s($b['ipa_phonetic'] ?? '', 120) ?: '/.../';
        $entry = [
            'word' => $word, 'french_word' => $word, 'ipa' => $ipa, 'ipa_phonetic' => $ipa,
            'part_of_speech' => Api::s($b['part_of_speech'] ?? '', 60) ?: 'nom',
            'meaning' => $en, 'meaning_en' => $en, 'meaning_hi' => $hi,
            'level' => $level, 'cefr_level' => $level, 'category' => Api::s($b['category'] ?? '', 80) ?: 'General',
            'example_fr' => $example, 'example_sentence' => $example, 'use_case' => $use,
            'notes' => Api::s($b['notes'] ?? '', 500) ?: 'Ingested via Hermes agent', 'source' => 'Hermes',
            'created_at' => Api::nowIso(),
        ];
        $json = json_encode($entry, JSON_UNESCAPED_UNICODE);

        $existing = Vocabulary::where('french_word', $word)->first();
        if ($existing) {
            $existing->update(['data' => $json]);
            $wid = $existing->id;
        } else {
            $wid = Vocabulary::create(['french_word' => $word, 'data' => $json, 'created_at' => Api::nowIso()])->id;
        }
        Api::log($who, $existing ? 'update' : 'create', 'vocabulary', $wid, $word);

        return Api::json(['success' => true, 'message' => $existing ? 'Word updated.' : 'New word added to the lexicon.',
            'data' => $entry + ['id' => 10000 + $wid]]);
    }
}

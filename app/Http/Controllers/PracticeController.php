<?php

namespace App\Http\Controllers;

use App\Models\Vocabulary;
use App\Support\Plan;
use Illuminate\Contracts\View\View;

/** Flashcards (spaced repetition, schedule kept in the browser) and the searchable word list. */
class PracticeController extends Controller
{
    private const ORDER = ['A1' => 1, 'A2' => 2, 'B1' => 3, 'B2' => 4, 'C1' => 5, 'C2' => 6];

    public function cards(): View
    {
        $plan = Plan::summary();
        $upTo = self::ORDER[$plan['stage']['level']] ?? 1;

        return view('practice.cards', [
            'deck' => $this->deck(),
            'levels' => array_keys(array_filter(self::ORDER, fn ($n) => $n <= $upTo)),
            'newPerDay' => (int) $plan['quota']['vocab'],
            'plan' => $plan,
        ]);
    }

    public function words(): View
    {
        return view('practice.words', ['deck' => $this->deck()]);
    }

    /** Compact word list: A1 first, then by id. */
    private function deck(): array
    {
        return Vocabulary::orderBy('id')->get(['id', 'french_word', 'data'])->map(function ($v) {
            $d = json_decode($v->data, true) ?: [];

            return [
                'w' => $v->french_word, 'ipa' => $d['ipa'] ?? $d['ipa_phonetic'] ?? '', 'pos' => $d['part_of_speech'] ?? '',
                'en' => $d['meaning_en'] ?? $d['meaning'] ?? '', 'hi' => $d['meaning_hi'] ?? '',
                'ex' => $d['example_fr'] ?? $d['example_sentence'] ?? '', 'exen' => $d['example_en'] ?? '',
                'lvl' => $d['level'] ?? $d['cefr_level'] ?? 'B1', 'cat' => $d['category'] ?? '',
            ];
        })->sortBy(fn ($w) => self::ORDER[$w['lvl']] ?? 9)->values()->all();
    }
}

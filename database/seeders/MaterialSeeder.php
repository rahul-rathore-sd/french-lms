<?php

namespace Database\Seeders;

use App\Models\Material;
use App\Models\Vocabulary;
use App\Support\Api;
use Illuminate\Database\Seeder;

/**
 * Loads the starter content (exported from the original app into database/seeders/data/*.json):
 * 50 grammar topics, TCF-style reading/listening exercises, writing and speaking tasks, and the
 * core vocabulary. Safe to run repeatedly (updates by slug / word).
 */
class MaterialSeeder extends Seeder
{
    public function run(): void
    {
        foreach ($this->json('grammar') as $g) {
            $this->put('g-'.$g['slug'], 'grammar', 'GR', $g['level'], [
                'title' => $g['title'], 'title_hi' => $g['title_hi'] ?? null, 'summary' => $g['summary'] ?? '', 'minutes' => 15,
            ], [
                'summary_hi' => $g['summary_hi'] ?? null, 'rules' => $g['rules'] ?? [], 'tables' => $g['tables'] ?? [],
                'tips' => $g['tips'] ?? [], 'tips_hi' => $g['tips_hi'] ?? [], 'quiz' => $g['quiz'] ?? [],
            ]);
        }

        foreach ($this->json('practice') as $p) {
            $reading = $p['skill'] === 'CE';
            $this->put($p['id'], $reading ? 'reading' : 'listening', $p['skill'], $p['level'], [
                'title' => $p['title'], 'summary' => $p['title_en'] ?? '', 'nclc' => $p['nclc'] ?? null, 'minutes' => 10,
            ], ['fr' => $p['fr'], 'en' => $p['en'], 'hi' => $p['hi'] ?? null, 'questions' => $p['questions'], 'glossary' => []]);
        }

        foreach ($this->json('writing-tasks') as $w) {
            $this->put($w['id'], 'writing', 'EE', $w['level'], [
                'title' => $w['title'], 'nclc' => $w['nclc'] ?? null, 'minutes' => 20,
                'summary' => $w['exam'].' · '.$w['minWords'].'+ words',
            ], [
                'exam' => $w['exam'], 'prompt' => $w['prompt'], 'prompt_hi' => $w['prompt_hi'] ?? null, 'min_words' => $w['minWords'],
                'model_fr' => $w['model'], 'model_en' => $w['model_en'] ?? null, 'checklist' => $w['checklist'] ?? [], 'useful_phrases' => [],
            ]);
        }

        foreach ($this->json('speaking-tasks') as $s) {
            $this->put($s['id'], 'speaking', 'EO', $s['level'], [
                'title' => $s['title'], 'nclc' => $s['nclc'] ?? null, 'minutes' => (int) ($s['minutes'] ?? 5) + 5,
                'summary' => $s['exam'].' · speak '.($s['minutes'] ?? 2).' min',
            ], [
                'exam' => $s['exam'], 'prompt' => $s['prompt'], 'prompt_hi' => $s['prompt_hi'] ?? null,
                'speak_minutes' => (int) ($s['minutes'] ?? 2), 'phrases' => $s['phrases'] ?? [], 'model_fr' => null, 'checklist' => [],
            ]);
        }

        // vocabulary (flashcards + lexicon)
        $now = Api::nowIso();
        foreach ($this->json('vocabulary') as $w) {
            $word = $w['french_word'] ?? $w['word'] ?? null;
            if (! $word) {
                continue;
            }
            unset($w['id']);
            Vocabulary::updateOrCreate(['french_word' => $word], ['data' => json_encode($w, JSON_UNESCAPED_UNICODE), 'created_at' => $now]);
        }
    }

    private function put(string $slug, string $kind, string $skill, string $level, array $meta, array $body): void
    {
        Material::updateOrCreate(['slug' => $slug], $meta + ['kind' => $kind, 'skill' => $skill, 'level' => $level, 'source' => 'seed', 'body' => $body]);
    }

    private function json(string $name): array
    {
        return json_decode(file_get_contents(__DIR__.'/data/'.$name.'.json'), true, 512, JSON_THROW_ON_ERROR);
    }
}

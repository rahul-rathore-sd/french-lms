<?php

namespace App\Console\Commands;

use App\Models\Learner;
use App\Models\Material;
use App\Models\Vocabulary;
use App\Support\Tts;
use Illuminate\Console\Command;

/**
 * Pre-generates audio for new material so the first play is instant.
 *   php artisan tts:warm --slug=a1-d002-listening-le-marche    listening script / grammar examples of one item
 *   php artisan tts:warm --recent=24                           words (and example sentences) of the newest vocabulary
 * Voices: the ones learners have chosen (override with --voice=fr-CA-SylvieNeural).
 */
class TtsWarm extends Command
{
    protected $signature = 'tts:warm {--slug=} {--recent=0} {--voice=*}';

    protected $description = 'Pre-generate the audio of new material';

    public function handle(Tts $tts): int
    {
        if (! config('tts.enabled')) {
            return self::SUCCESS;
        }
        $voices = $this->option('voice') ?: Learner::pluck('voice')->unique()->all();
        $voices = array_values(array_filter($voices ?: [config('tts.default_voice')], fn ($v) => Tts::isChoice($v) && $v !== 'browser'));
        $jobs = [];   // [text, rate]

        if ($slug = $this->option('slug')) {
            $m = Material::where('slug', $slug)->first();
            if (! $m) {
                $this->error("No item with slug $slug");

                return self::FAILURE;
            }
            $b = $m->body;
            if ($m->kind === 'listening') {
                $jobs[] = [$b['fr'] ?? '', '+0%'];
                $jobs[] = [$b['fr'] ?? '', '-25%'];
            }
            if ($m->kind === 'reading') {
                $jobs[] = [$b['fr'] ?? '', '+0%'];
            }
            if ($m->kind === 'grammar') {
                foreach ($b['rules'] ?? [] as $rule) {
                    foreach ($rule['examples'] ?? [] as $ex) {
                        $jobs[] = [$ex['fr'] ?? '', '+0%'];
                    }
                }
            }
            if ($m->kind === 'speaking') {
                foreach ($b['phrases'] ?? [] as $p) {
                    $jobs[] = [$p['fr'] ?? '', '+0%'];
                }
            }
        }

        if ($n = (int) $this->option('recent')) {
            foreach (Vocabulary::orderByDesc('id')->limit($n)->get() as $v) {
                $d = json_decode($v->data, true) ?: [];
                $jobs[] = [$v->french_word, '+0%'];
                if (! empty($d['example_fr'])) {
                    $jobs[] = [$d['example_fr'], '+0%'];
                }
            }
        }

        $made = 0;
        foreach ($voices as $voice) {
            foreach ($jobs as [$text, $rate]) {
                $text = Tts::normalize((string) $text);
                if ($text === '' || mb_strlen($text) > config('tts.max_chars')) {
                    continue;
                }
                $tts->parts($text, $voice, $rate, $new);
                $made += (int) $new;
            }
        }
        $this->info("tts:warm: $made new audio files for ".count($voices).' voice(s).');

        return self::SUCCESS;
    }
}

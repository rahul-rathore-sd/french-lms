<?php

namespace Tests\Feature;

use App\Models\Learner;
use App\Support\LearnerAuth;
use App\Support\Tts;
use App\Support\TtsEngine;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Support\FakeTtsEngine;
use Tests\TestCase;

class TtsTest extends TestCase
{
    use RefreshDatabase;

    private const FETCH = ['X-Requested-With' => 'fetch'];

    private FakeTtsEngine $engine;
    private string $dir;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
        $this->dir = sys_get_temp_dir().'/tts-test-'.uniqid();
        config(['tts.cache_path' => $this->dir]);
        $this->engine = new FakeTtsEngine;
        $this->app->instance(TtsEngine::class, $this->engine);
    }

    protected function tearDown(): void
    {
        foreach (glob($this->dir.'/*/*') ?: [] as $f) {
            @unlink($f);
        }
        parent::tearDown();
    }

    private function login(string $name = 'Anna'): array
    {
        $r = $this->postJson('/register', ['name' => $name, 'pin' => '123456', 'level' => 'A1'], self::FETCH)->assertOk();

        return [LearnerAuth::COOKIE => collect($r->headers->getCookies())->first(fn ($c) => $c->getName() === LearnerAuth::COOKIE)->getValue()];
    }

    private function say(array $cookies, array $body): \Illuminate\Testing\TestResponse
    {
        return $this->withUnencryptedCookies($cookies)->withCredentials()->postJson('/tts', $body, self::FETCH);
    }

    /* ------------------------------------------------------- splitting --- */

    public function test_plain_text_is_one_piece_and_long_text_is_cut_at_sentence_ends(): void
    {
        $this->assertSame([['text' => 'Bonjour tout le monde.', 'voice' => 'fr-CA-SylvieNeural']], Tts::split('Bonjour   tout le monde.', 'fr-CA-SylvieNeural'));

        $long = implode(' ', array_fill(0, 40, 'Ceci est une phrase assez longue pour le test.'));
        $parts = Tts::split($long, 'fr-FR-DeniseNeural');
        $this->assertGreaterThan(3, count($parts));
        foreach ($parts as $p) {
            $this->assertLessThanOrEqual(350, mb_strlen($p['text']));
        }
    }

    public function test_dialogues_use_two_voices_and_do_not_read_the_speaker_names(): void
    {
        $dialogue = "Madame Lefèvre : Bonjour monsieur.\nKarim : Bonjour madame.\nMadame Lefèvre : Vous habitez où ?\nKarim : J’habite à Québec.";
        $parts = Tts::split($dialogue, 'fr-CA-SylvieNeural');

        $this->assertCount(4, $parts);
        $this->assertSame('Bonjour monsieur.', $parts[0]['text']);
        $this->assertSame(['fr-CA-SylvieNeural', 'fr-CA-ThierryNeural', 'fr-CA-SylvieNeural', 'fr-CA-ThierryNeural'], array_column($parts, 'voice'));
        $this->assertStringNotContainsString('Karim', implode(' ', array_column($parts, 'text')));
    }

    public function test_a_single_labelled_line_or_a_normal_sentence_with_a_colon_is_not_a_dialogue(): void
    {
        $this->assertCount(1, Tts::split('Attention : le train part à midi.', 'fr-CA-SylvieNeural'));
        $this->assertSame('Attention : le train part à midi.', Tts::split('Attention : le train part à midi.', 'fr-CA-SylvieNeural')[0]['text']);
    }

    public function test_mixed_accents_pick_the_same_voice_for_the_same_text(): void
    {
        $a = Tts::resolveVoice('mix', 'Bonjour');
        $this->assertSame($a, Tts::resolveVoice('mix', 'Bonjour'));
        $this->assertContains($a, config('tts.mix'));
        $voices = collect(range(1, 60))->map(fn ($i) => Tts::resolveVoice('mix', "texte numéro $i"))->unique();
        $this->assertGreaterThan(3, $voices->count());           // really mixes several voices
    }

    /* -------------------------------------------------------- endpoint --- */

    public function test_the_audio_endpoint_needs_a_login_and_valid_input(): void
    {
        $this->postJson('/tts', ['text' => 'Bonjour'], self::FETCH)->assertStatus(401);

        $c = $this->login();
        $this->say($c, ['text' => ''])->assertStatus(422);
        $this->say($c, ['text' => '1234 ...'])->assertStatus(422);                                    // no letters
        $this->say($c, ['text' => str_repeat('a', 1000)])->assertStatus(422);                         // too long
        $this->say($c, ['text' => 'Bonjour', 'voice' => 'en-US-JennyNeural'])->assertStatus(422);     // not whitelisted
        $this->say($c, ['text' => 'Bonjour', 'voice' => 'browser'])->assertStatus(422);               // browser voice is client-side
        $this->say($c, ['text' => 'Bonjour', 'rate' => '+300%'])->assertStatus(422);
        $this->assertCount(0, $this->engine->rendered);
    }

    public function test_audio_is_generated_once_then_served_from_the_cache(): void
    {
        $c = $this->login();
        $first = $this->say($c, ['text' => 'Bonjour, je m’appelle Karim.', 'voice' => 'fr-CA-SylvieNeural'])->assertOk()->json('parts');
        $this->assertCount(1, $first);
        $this->assertMatchesRegularExpression('#^/audio/[0-9a-f]{2}/[0-9a-f]{40}\.mp3$#', $first[0]);
        $this->assertFileExists($this->dir.substr($first[0], strlen('/audio')));
        $this->assertCount(1, $this->engine->rendered);

        $second = $this->say($c, ['text' => 'Bonjour,   je m’appelle Karim.', 'voice' => 'fr-CA-SylvieNeural'])->assertOk()->json('parts');
        $this->assertSame($first, $second);
        $this->assertCount(1, $this->engine->rendered);           // not generated again

        // another speed or another voice is a different file
        $this->say($c, ['text' => 'Bonjour, je m’appelle Karim.', 'voice' => 'fr-CA-SylvieNeural', 'rate' => '-25%'])->assertOk();
        $this->say($c, ['text' => 'Bonjour, je m’appelle Karim.', 'voice' => 'fr-FR-DeniseNeural'])->assertOk();
        $this->assertCount(3, $this->engine->rendered);
    }

    public function test_a_dialogue_returns_one_file_per_line_with_two_voices(): void
    {
        $c = $this->login();
        $parts = $this->say($c, ['text' => "Anna : Salut !\nKarim : Salut Anna, ça va ?\nAnna : Très bien, merci.", 'voice' => 'fr-FR-DeniseNeural'])->assertOk()->json('parts');
        $this->assertCount(3, $parts);
        $voices = array_unique(array_column($this->engine->rendered, 'voice'));
        sort($voices);
        $this->assertSame(['fr-FR-DeniseNeural', 'fr-FR-HenriNeural'], $voices);
    }

    public function test_it_uses_the_learners_own_voice_by_default_and_reports_engine_failures(): void
    {
        $c = $this->login();
        Learner::first()->update(['voice' => 'fr-BE-CharlineNeural']);
        $this->say($c, ['text' => 'Bonne journée.'])->assertOk();
        $this->assertSame('fr-BE-CharlineNeural', $this->engine->rendered[0]['voice']);

        $this->engine->fail = true;
        $this->say($c, ['text' => 'Un texte jamais généré.'])->assertStatus(502)->assertJsonPath('ok', false);
    }

    public function test_server_voices_can_be_turned_off(): void
    {
        $c = $this->login();
        config(['tts.enabled' => false]);
        $this->say($c, ['text' => 'Bonjour'])->assertStatus(503);
        $this->withUnencryptedCookies($c)->get('/')->assertSee('"voice":"browser"', false);
    }

    /* --------------------------------------------------------- profile --- */

    public function test_the_learner_can_choose_a_voice_and_the_page_uses_it(): void
    {
        $c = $this->login();
        $this->withUnencryptedCookies($c)->get('/')->assertSee('"voice":"fr-CA-SylvieNeural"', false);        // default: Canadian

        $this->withUnencryptedCookies($c)->withCredentials()->postJson('/me/settings', ['voice' => 'fr-CH-FabriceNeural'], self::FETCH)->assertOk();
        $this->assertSame('fr-CH-FabriceNeural', Learner::first()->voice);
        $this->withUnencryptedCookies($c)->get('/')->assertSee('"voice":"fr-CH-FabriceNeural"', false);

        foreach (['mix', 'browser'] as $special) {
            $this->withUnencryptedCookies($c)->withCredentials()->postJson('/me/settings', ['voice' => $special], self::FETCH)->assertOk();
            $this->assertSame($special, Learner::first()->voice);
        }

        // an unknown voice is ignored, the old choice stays
        $this->withUnencryptedCookies($c)->withCredentials()->postJson('/me/settings', ['voice' => 'xx-XX-FakeNeural'], self::FETCH)->assertOk();
        $this->assertSame('browser', Learner::first()->voice);

        $this->withUnencryptedCookies($c)->get('/me')->assertOk()->assertSee('Thierry')->assertSee('Mixed accents')->assertSee('Listen to a sample');
    }

    public function test_two_learners_can_have_different_voices(): void
    {
        $a = $this->login('Anna');
        $k = $this->login('Karim');
        $this->withUnencryptedCookies($k)->withCredentials()->postJson('/me/settings', ['voice' => 'fr-FR-HenriNeural'], self::FETCH)->assertOk();

        $this->withUnencryptedCookies($a)->get('/')->assertSee('"voice":"fr-CA-SylvieNeural"', false);
        $this->withUnencryptedCookies($k)->get('/')->assertSee('"voice":"fr-FR-HenriNeural"', false);
    }
}

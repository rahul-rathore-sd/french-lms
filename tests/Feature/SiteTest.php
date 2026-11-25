<?php

namespace Tests\Feature;

use App\Models\Learner;
use App\Models\Material;
use App\Support\LearnerAuth;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SiteTest extends TestCase
{
    use RefreshDatabase;

    private const KEY = 'test-hermes-key-0123456789abcdef';
    private const FETCH = ['X-Requested-With' => 'fetch'];

    protected function setUp(): void
    {
        parent::setUp();
        config(['french.hermes_api_key' => self::KEY, 'tcf.start_date' => now()->toDateString()]);
        $this->seed(DatabaseSeeder::class);
    }

    /** Registers a learner and returns the login cookie. */
    private function register(string $name = 'Anna', string $level = 'A1', string $pin = '123456'): array
    {
        $r = $this->postJson('/register', ['name' => $name, 'pin' => $pin, 'level' => $level], self::FETCH)->assertOk();
        $cookie = collect($r->headers->getCookies())->first(fn ($c) => $c->getName() === LearnerAuth::COOKIE);

        return [LearnerAuth::COOKIE => $cookie->getValue()];
    }

    private function as(array $cookies): static
    {
        // withCredentials(): the test client only sends cookies on JSON requests when asked to
        return $this->withUnencryptedCookies($cookies)->withCredentials();
    }

    private function key(): array
    {
        return ['Authorization' => 'Bearer '.self::KEY];
    }

    private function reading(array $over = []): array
    {
        return array_replace_recursive([
            'kind' => 'reading', 'level' => 'A1', 'title' => 'Je me présente', 'minutes' => 10,
            'body' => [
                'fr' => 'Bonjour, je m\'appelle Rahul. J\'habite à Delhi. J\'apprends le français.',
                'en' => 'Hello, my name is Rahul. I live in Delhi. I am learning French.',
                'questions' => [
                    ['q' => 'Comment s\'appelle-t-il ?', 'options' => ['Rahul', 'Marc'], 'correct' => 0, 'explain' => 'Je m\'appelle Rahul.'],
                    ['q' => 'Où habite-t-il ?', 'options' => ['À Paris', 'À Delhi'], 'correct' => 1, 'explain' => 'J\'habite à Delhi.'],
                    ['q' => 'Que fait-il ?', 'options' => ['Il apprend le français', 'Il dort'], 'correct' => 0, 'explain' => 'J\'apprends le français.'],
                ],
            ],
        ], $over);
    }

    /* ----------------------------------------------------------- login --- */

    public function test_guests_are_sent_to_login_and_pages_need_a_learner(): void
    {
        $this->get('/')->assertRedirect('/login');
        $this->get('/learn')->assertRedirect('/login?next=/learn');
        $this->get('/login')->assertOk()->assertSee('Log in');
        $this->get('/register')->assertOk()->assertSee('Create profile');
    }

    public function test_registration_is_validated(): void
    {
        $this->postJson('/register', ['name' => 'Anna', 'pin' => '123456'])->assertStatus(403); // no fetch header
        $this->postJson('/register', ['name' => 'A', 'pin' => '123456'], self::FETCH)->assertStatus(422);
        $this->postJson('/register', ['name' => 'Anna', 'pin' => 'abcd'], self::FETCH)->assertStatus(422);
        $this->postJson('/register', ['name' => 'Anna', 'pin' => '12'], self::FETCH)->assertStatus(422);
        $this->postJson('/register', ['name' => 'Anna', 'pin' => '1234', 'level' => 'Z9'], self::FETCH)->assertStatus(422);

        $this->register('Anna');
        $this->postJson('/register', ['name' => 'anna', 'pin' => '654321'], self::FETCH)->assertStatus(409); // case-insensitive
        $this->assertNotSame('123456', Learner::first()->pin_hash);                                      // hashed
    }

    public function test_new_profiles_need_a_6_to_8_digit_pin(): void
    {
        foreach (['1234', '12345', '123456789', 'abcdef'] as $bad) {
            $this->postJson('/register', ['name' => 'Anna', 'pin' => $bad], self::FETCH)->assertStatus(422);
        }
        foreach (['123456', '12345678'] as $i => $good) {
            $this->postJson('/register', ['name' => 'User'.$i, 'pin' => $good], self::FETCH)->assertOk();
        }

        $c = $this->register('Karim');
        $this->as($c)->postJson('/me/pin', ['current' => '123456', 'new' => '1234'], self::FETCH)->assertStatus(422);
        $this->as($c)->postJson('/me/pin', ['current' => '123456', 'new' => '654321'], self::FETCH)->assertOk();
    }

    public function test_registration_needs_the_invite_code_when_one_is_set(): void
    {
        config(['tcf.invite_code' => 'SPIRE7']);
        $this->get('/register')->assertOk()->assertSee('Invite code');

        $this->postJson('/register', ['name' => 'Anna', 'pin' => '123456'], self::FETCH)->assertStatus(403);
        $this->postJson('/register', ['name' => 'Anna', 'pin' => '123456', 'invite' => 'wrong'], self::FETCH)->assertStatus(403);
        $this->assertSame(0, Learner::count());

        $this->postJson('/register', ['name' => 'Anna', 'pin' => '123456', 'invite' => ' SPIRE7 '], self::FETCH)->assertOk();
        $this->assertSame(1, Learner::count());

        // logging in never needs the code, and without a code configured the field disappears
        $this->postJson('/login', ['name' => 'Anna', 'pin' => '123456'], self::FETCH)->assertOk();
        config(['tcf.invite_code' => '']);
        $this->get('/register')->assertDontSee('Invite code');
    }

    public function test_login_works_from_any_address_and_survives_a_network_change(): void
    {
        // the phone moves from Wi-Fi to mobile data: another IP, same cookie. The app must not care.
        $c = $this->register('Anna');
        $this->withServerVariables(['REMOTE_ADDR' => '157.49.7.139'])->as($c)->get('/')->assertOk()->assertSee('Anna');
        $this->withServerVariables(['REMOTE_ADDR' => '223.191.80.29'])->as($c)
            ->putJson('/me/state/progress', ['value' => ['done' => ['x' => '2026-11-25'], 'days' => [], 'checks' => []]], self::FETCH)->assertOk();
    }

    public function test_login_page_has_no_signup_link_and_footer_has_credit(): void
    {
        $this->get('/login')->assertOk()
            ->assertDontSee('Create your profile')
            ->assertDontSee('href="/register"', false)
            ->assertSee('Rahul Rathore');
        $this->get('/register')->assertOk();
    }

    public function test_site_can_be_full(): void
    {
        config(['tcf.max_learners' => 1]);
        $this->register('Anna');
        $this->postJson('/register', ['name' => 'Karim', 'pin' => '123456'], self::FETCH)->assertStatus(403);
    }

    public function test_login_logout_and_throttle(): void
    {
        $this->register('Anna');
        $this->postJson('/login', ['name' => 'Anna', 'pin' => '000000'], self::FETCH)->assertStatus(401);
        $ok = $this->postJson('/login', ['name' => 'anna', 'pin' => '123456'], self::FETCH)->assertOk()->assertJsonPath('redirect', '/');
        $cookie = collect($ok->headers->getCookies())->first(fn ($c) => $c->getName() === LearnerAuth::COOKIE);
        $this->assertTrue($cookie->isHttpOnly());

        $cookies = [LearnerAuth::COOKIE => $cookie->getValue()];
        $this->as($cookies)->get('/')->assertOk()->assertSee('Anna');
        $this->as($cookies)->postJson('/logout', [], self::FETCH)->assertOk();
        $this->as($cookies)->get('/')->assertRedirect('/login'); // the token is gone server-side

        for ($i = 0; $i < 5; $i++) {
            $this->postJson('/login', ['name' => 'Anna', 'pin' => '999999'], self::FETCH);
        }
        $this->postJson('/login', ['name' => 'Anna', 'pin' => '123456'], self::FETCH)->assertStatus(429); // locked even with the right PIN
    }

    public function test_next_redirect_cannot_leave_the_site(): void
    {
        $this->register('Anna');
        $this->postJson('/login', ['name' => 'Anna', 'pin' => '123456', 'next' => '//evil.example'], self::FETCH)->assertJsonPath('redirect', '/');
        $this->postJson('/login', ['name' => 'Anna', 'pin' => '123456', 'next' => '/learn'], self::FETCH)->assertJsonPath('redirect', '/learn');
    }

    public function test_state_changing_calls_need_the_fetch_header(): void
    {
        $c = $this->register('Anna');
        $this->as($c)->postJson('/me/settings', ['minutes' => 60])->assertStatus(403);
        $this->as($c)->postJson('/me/settings', ['minutes' => 60], self::FETCH)->assertOk();
        $this->assertSame(60, Learner::first()->daily_minutes);
    }

    /* ----------------------------------------- two people, separate data --- */

    public function test_each_learner_has_their_own_progress(): void
    {
        $anna = $this->register('Anna');
        $karim = $this->register('Karim');

        $this->as($anna)->putJson('/me/state/progress', ['value' => ['done' => ['zz-marker-anna-only' => '2026-11-23'], 'days' => [], 'checks' => []]], self::FETCH)->assertOk();

        $this->as($anna)->get('/')->assertSee('zz-marker-anna-only', false);
        $this->as($karim)->get('/')->assertDontSee('zz-marker-anna-only', false);

        $this->as($anna)->putJson('/me/state/other', ['value' => []], self::FETCH)->assertStatus(404);
        $this->as($anna)->putJson('/me/state/progress', ['value' => 'not-an-object'], self::FETCH)->assertStatus(422);
    }

    public function test_card_schedule_is_sent_only_on_the_cards_page(): void
    {
        $c = $this->register('Anna');
        $srs = ['Bonjour' => ['ease' => 2.5, 'interval' => 1, 'reps' => 1, 'lapses' => 0, 'due' => '2099-01-01']];
        $this->as($c)->putJson('/me/state/srs', ['value' => $srs], self::FETCH)->assertOk();

        $this->as($c)->get('/practice')->assertSee('2099-01-01', false);
        $this->as($c)->get('/')->assertDontSee('2099-01-01', false);
    }

    public function test_each_learner_has_their_own_plan_and_level(): void
    {
        $a1 = $this->register('Anna', 'A1');
        $b1 = $this->register('Karim', 'B1');

        $this->as($a1)->get('/')->assertSee('A1 · NCLC 1-3');
        $this->as($b1)->get('/')->assertSee('B1 · NCLC 5-6');

        // restart at another level changes only that learner
        $this->as($a1)->postJson('/me/restart', ['level' => 'A2'], self::FETCH)->assertOk();
        $this->assertSame(70, (int) Learner::where('name', 'Anna')->first()->credit_hours);
        $this->assertSame(160, (int) Learner::where('name', 'Karim')->first()->credit_hours);
        $this->as($a1)->postJson('/me/restart', ['level' => 'Z9'], self::FETCH)->assertStatus(422);
    }

    public function test_pin_change_logs_out_other_devices(): void
    {
        $phone = $this->register('Anna');
        $laptop = collect($this->postJson('/login', ['name' => 'Anna', 'pin' => '123456'], self::FETCH)->headers->getCookies())
            ->first(fn ($c) => $c->getName() === LearnerAuth::COOKIE)->getValue();

        $this->as($phone)->postJson('/me/pin', ['current' => 'wrong', 'new' => '654321'], self::FETCH)->assertStatus(422);
        $this->as($phone)->postJson('/me/pin', ['current' => '123456', 'new' => '654321'], self::FETCH)->assertOk();

        $this->as($phone)->get('/')->assertOk();
        $this->as([LearnerAuth::COOKIE => $laptop])->get('/')->assertRedirect('/login');
        $this->postJson('/login', ['name' => 'Anna', 'pin' => '654321'], self::FETCH)->assertOk();
    }

    /* ------------------------------------------------------------ pages --- */

    public function test_pages_render_and_are_noindex(): void
    {
        $c = $this->register();
        foreach (['/', '/learn', '/practice', '/words', '/tcf', '/level-check', '/me', '/login'] as $url) {
            $this->as($c)->get($url)->assertHeader('X-Robots-Tag', 'noindex, nofollow, noarchive, nosnippet');
        }
        $this->as($c)->get('/')->assertOk()->assertSee('data-theme="dark"', false)->assertSee('Today', false);
        $this->as($c)->get('/does-not-exist')->assertNotFound();
        $this->assertStringContainsString('Disallow: /', file_get_contents(public_path('robots.txt')));
    }

    public function test_tcf_page_shows_the_nclc7_targets(): void
    {
        $this->as($this->register())->get('/tcf')->assertOk()->assertSee('458-502')->assertSee('453-498')->assertSee('10-11');
    }

    public function test_seeded_content_is_browsable(): void
    {
        $c = $this->register();
        $this->assertGreaterThan(40, Material::where('kind', 'grammar')->count());
        $g = Material::where('kind', 'grammar')->where('level', 'A1')->first();
        $this->as($c)->get('/r/'.$g->slug)->assertOk()->assertSee($g->title);
        $this->as($c)->get('/learn?kind=grammar&level=A1')->assertOk()->assertSee($g->title);
        $this->as($c)->get('/practice')->assertOk()->assertSee('deck-data', false);
    }

    public function test_todays_daily_item_is_shown_at_the_learners_level_only(): void
    {
        $a1 = $this->register('Anna', 'A1');
        $b1 = $this->register('Karim', 'B1');
        $this->postJson('/api/v1/materials', $this->reading(['title' => 'Texte pour A1']), $this->key())->assertCreated();
        $this->postJson('/api/v1/materials', $this->reading(['level' => 'B1', 'title' => 'Texte pour B1']), $this->key())->assertCreated();

        $this->as($a1)->get('/')->assertSee('Texte pour A1')->assertDontSee('Texte pour B1');
        $this->as($b1)->get('/')->assertSee('Texte pour B1')->assertDontSee('Texte pour A1');
    }

    /* -------------------------------------------------------------- API --- */

    public function test_api_needs_the_key(): void
    {
        $this->getJson('/api/v1/status')->assertOk()->assertJsonMissingPath('statistics');
        $this->getJson('/api/v1/status', $this->key())->assertOk()->assertJsonPath('statistics.materials', Material::count());
        $this->postJson('/api/v1/materials', $this->reading())->assertStatus(401);
        $this->getJson('/api/v1/plan/today')->assertStatus(401);
        $this->postJson('/api/v1/materials', $this->reading(), ['Authorization' => 'Bearer wrong'])->assertStatus(401);
    }

    public function test_daily_material_is_validated_stored_and_shown(): void
    {
        $c = $this->register();
        $this->postJson('/api/v1/materials', $this->reading(), $this->key())->assertCreated()->assertJsonPath('data.created', true);
        $m = Material::where('title', 'Je me présente')->firstOrFail();
        $this->assertSame('daily', $m->source);
        $this->as($c)->get('/r/'.$m->slug)->assertOk()->assertSee('J&#039;habite à Delhi', false);

        // same slug again updates instead of duplicating
        $this->postJson('/api/v1/materials', $this->reading(['slug' => $m->slug]), $this->key())->assertOk()->assertJsonPath('data.created', false);
        $this->assertSame(1, Material::where('title', 'Je me présente')->count());
    }

    public function test_bad_material_is_rejected(): void
    {
        $bad = $this->reading(['body' => ['questions' => [['q' => 'x', 'options' => ['a', 'b'], 'correct' => 5, 'explain' => 'e']]]]);
        $this->postJson('/api/v1/materials', $bad, $this->key())->assertStatus(422);
        $this->postJson('/api/v1/materials', ['kind' => 'nonsense', 'title' => 't'], $this->key())->assertStatus(422);
        $this->postJson('/api/v1/materials', $this->reading(['level' => 'Z9']), $this->key())->assertStatus(422);
    }

    public function test_vocab_set_feeds_the_flashcards(): void
    {
        $c = $this->register();
        $words = collect(range(1, 6))->map(fn ($i) => ['fr' => "motnouveau$i", 'en' => "new word $i", 'hi' => 'शब्द', 'example_fr' => "Voici le mot $i.", 'example_en' => "Here is word $i."])->all();
        $this->postJson('/api/v1/materials', ['kind' => 'vocab', 'level' => 'A1', 'title' => 'Day 1 words', 'body' => ['words' => $words]], $this->key())
            ->assertCreated()->assertJsonPath('data.added', 6);
        $this->getJson('/api/v1/vocabulary')->assertOk()->assertSee('motnouveau3');
        $this->as($c)->get('/words')->assertOk()->assertSee('motnouveau1');

        // a word from the core list (any letter case) is skipped and left unchanged
        $set = array_merge($words, [['fr' => 'bonjour', 'en' => 'CHANGED', 'example_fr' => 'Bonjour !', 'example_en' => 'Hello!']]);
        $this->postJson('/api/v1/materials', ['kind' => 'vocab', 'level' => 'A1', 'title' => 'Day 2', 'body' => ['words' => $set]], $this->key())
            ->assertCreated()->assertJsonPath('data.skipped.0', 'bonjour')->assertJsonPath('data.updated', 6);
        $this->getJson('/api/v1/vocabulary')->assertDontSee('CHANGED');
    }

    public function test_plan_endpoint_lists_the_levels_people_are_on_without_exposing_them(): void
    {
        $r = $this->getJson('/api/v1/plan/today', $this->key())->assertOk()->json('data');
        $this->assertSame(['A1'], $r['levels_needed']);          // nobody registered yet: the default plan
        $this->assertSame(10, $r['quota']['vocab']);
        $this->assertNotEmpty($r['seed_grammar_slugs']);

        $this->register('Anna', 'A1');
        $this->register('Karim', 'A1');
        $this->register('Sofia', 'B1');
        $r = $this->getJson('/api/v1/plan/today', $this->key())->assertOk()->json('data');
        $this->assertEqualsCanonicalizing(['A1', 'B1'], $r['levels_needed']);
        $a1 = collect($r['levels'])->firstWhere('level', 'A1');
        $this->assertSame(2, $a1['learners']);

        $this->assertStringNotContainsString('Anna', json_encode($r));
        $this->assertStringNotContainsString('pin', strtolower(json_encode($r)));

        // adding an A1 item moves it into "already added today" for A1 only
        $this->postJson('/api/v1/materials', $this->reading(), $this->key())->assertCreated();
        $r = $this->getJson('/api/v1/plan/today', $this->key())->json('data');
        $this->assertSame(1, collect($r['levels'])->firstWhere('level', 'A1')['already_added_today']['reading']);
        $this->assertArrayNotHasKey('reading', collect($r['levels'])->firstWhere('level', 'B1')['already_added_today']);
    }
}

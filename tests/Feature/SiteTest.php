<?php

namespace Tests\Feature;

use App\Models\Material;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SiteTest extends TestCase
{
    use RefreshDatabase;

    private const KEY = 'test-hermes-key-0123456789abcdef';

    protected function setUp(): void
    {
        parent::setUp();
        config(['french.hermes_api_key' => self::KEY, 'tcf.start_date' => now()->toDateString()]);
        $this->seed(DatabaseSeeder::class);
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

    public function test_pages_render_and_are_noindex(): void
    {
        foreach (['/', '/learn', '/practice', '/words', '/tcf', '/level-check'] as $url) {
            $this->get($url)->assertOk()->assertHeader('X-Robots-Tag', 'noindex, nofollow, noarchive, nosnippet');
        }
        $this->get('/')->assertSee('data-theme="dark"', false)->assertSee('Today', false);
        $this->get('/does-not-exist')->assertNotFound();
        $this->assertStringContainsString('Disallow: /', file_get_contents(public_path('robots.txt')));
    }

    public function test_tcf_page_shows_the_nclc7_targets(): void
    {
        $this->get('/tcf')->assertOk()->assertSee('458-502')->assertSee('453-498')->assertSee('10-11');
    }

    public function test_seeded_content_is_browsable(): void
    {
        $this->assertGreaterThan(40, Material::where('kind', 'grammar')->count());
        $g = Material::where('kind', 'grammar')->where('level', 'A1')->first();
        $this->get('/r/'.$g->slug)->assertOk()->assertSee($g->title);
        $this->get('/learn?kind=grammar&level=A1')->assertOk()->assertSee($g->title);
        $this->get('/practice')->assertOk()->assertSee('deck-data', false);
    }

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
        $this->postJson('/api/v1/materials', $this->reading(), $this->key())->assertCreated()->assertJsonPath('data.created', true);
        $m = Material::where('title', 'Je me présente')->firstOrFail();
        $this->assertSame('daily', $m->source);
        $this->get('/r/'.$m->slug)->assertOk()->assertSee('Je me présente')->assertSee('J&#039;habite à Delhi', false);
        $this->get('/')->assertOk()->assertSee('Je me présente');

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
        $words = collect(range(1, 6))->map(fn ($i) => ['fr' => "motnouveau$i", 'en' => "new word $i", 'hi' => 'शब्द', 'example_fr' => "Voici le mot $i.", 'example_en' => "Here is word $i."])->all();
        $this->postJson('/api/v1/materials', ['kind' => 'vocab', 'level' => 'A1', 'title' => 'Day 1 words', 'body' => ['words' => $words]], $this->key())
            ->assertCreated()->assertJsonPath('data.added', 6);
        $this->getJson('/api/v1/vocabulary')->assertOk()->assertSee('motnouveau3');
        $this->get('/words')->assertOk()->assertSee('motnouveau1');
    }

    public function test_plan_endpoint_reports_the_stage_and_what_exists(): void
    {
        $r = $this->getJson('/api/v1/plan/today', $this->key())->assertOk()->json('data');
        $this->assertSame(1, $r['day_number']);
        $this->assertSame('A1', $r['stage']['level']);
        $this->assertSame(10, $r['quota']['vocab']);
        $this->assertArrayHasKey('recent_titles', $r);
        $this->assertNotEmpty($r['seed_grammar_slugs']);
    }
}

<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ApiTest extends TestCase
{
    use RefreshDatabase;

    private const KEY = 'test-hermes-key-0123456789abcdef';

    protected function setUp(): void
    {
        parent::setUp();
        config([
            'french.hermes_api_key' => self::KEY,
            'french.admin_password_hash' => Hash::make('secret-pass'),
        ]);
    }

    private function hermes(): array
    {
        return ['Authorization' => 'Bearer '.self::KEY];
    }

    public function test_status_is_public(): void
    {
        $this->getJson('/api/v1/status')->assertOk()->assertJsonPath('status', 'operational')->assertJsonMissingPath('statistics');
    }

    public function test_lead_form_validates_and_merges_repeat_enquiries(): void
    {
        $this->postJson('/api/v1/leads', ['full_name' => '', 'email' => 'bad'])->assertStatus(422);

        $lead = ['full_name' => 'Asha', 'email' => 'asha@example.com', 'message' => 'Hi'];
        $this->postJson('/api/v1/leads', $lead)->assertOk()->assertJsonPath('success', true);
        $this->postJson('/api/v1/leads', $lead + ['message' => 'Again'])->assertOk();

        $this->assertDatabaseCount('leads', 1);
        $this->getJson('/api/v1/leads')->assertStatus(401);
        $this->getJson('/api/v1/leads', $this->hermes())->assertOk()->assertJsonPath('total', 1);
    }

    public function test_honeypot_creates_no_lead(): void
    {
        $this->postJson('/api/v1/leads', ['website' => 'spam', 'full_name' => 'Bot', 'email' => 'b@example.com', 'message' => 'x'])->assertOk();
        $this->assertDatabaseCount('leads', 0);
    }

    public function test_admin_login_requires_fetch_header_and_correct_password(): void
    {
        $this->postJson('/api/v1/admin/login', ['password' => 'secret-pass'])->assertStatus(403);
        $this->postJson('/api/v1/admin/login', ['password' => 'wrong'], ['X-Requested-With' => 'fetch'])->assertStatus(401);
        $this->postJson('/api/v1/admin/login', ['password' => 'secret-pass'], ['X-Requested-With' => 'fetch'])
            ->assertOk()->assertJsonPath('success', true);
    }

    public function test_hermes_cannot_approve_outbound_email(): void
    {
        $id = $this->postJson('/api/v1/emails', ['to_addr' => 'a@example.com', 'subject' => 'S', 'body' => 'B', 'status' => 'approved'], $this->hermes())
            ->assertCreated()->assertJsonPath('data.status', 'draft')->json('data.id');

        $this->patchJson("/api/v1/emails/$id", ['status' => 'approved'], $this->hermes())->assertStatus(403);
    }

    public function test_site_is_noindex_by_default_and_hides_the_sitemap(): void
    {
        $this->assertTrue(config('french.noindex'));
        $this->get('/')->assertOk()->assertHeader('X-Robots-Tag', 'noindex, nofollow, noarchive, nosnippet');
        $this->getJson('/api/v1/status')->assertHeader('X-Robots-Tag', 'noindex, nofollow, noarchive, nosnippet');
        $this->get('/sitemap.xml')->assertNotFound();
        $this->assertStringContainsString('Disallow: /', file_get_contents(public_path('robots.txt')));

        config(['french.noindex' => false]);
        $this->get('/')->assertHeaderMissing('X-Robots-Tag');
    }

    public function test_blog_drafts_stay_private_and_published_posts_are_public(): void
    {
        config(['french.noindex' => false]); // sitemap is only served when the site is public
        $this->postJson('/api/v1/blog', ['title' => 'Draft one', 'content_md' => 'secret'], $this->hermes())->assertCreated();
        $this->postJson('/api/v1/blog', ['title' => 'Public one', 'content_md' => "# Hi\n\n<script>alert(1)</script>", 'status' => 'published'], $this->hermes())->assertCreated();

        $this->getJson('/api/v1/blog')->assertOk()->assertJsonPath('total', 1)->assertJsonPath('data.0.slug', 'public-one');
        $this->get('/blog/draft-one/')->assertNotFound();
        $this->get('/blog/public-one/')->assertOk()->assertDontSee('<script>alert(1)</script>', false);
        $this->get('/sitemap.xml')->assertOk()->assertSee('/blog/public-one/');
    }

    public function test_vocabulary_ingest_needs_the_hermes_key(): void
    {
        $word = ['french_word' => 'bonjour', 'meaning_en' => 'hello', 'meaning_hi' => 'नमस्ते', 'use_case' => 'greeting'];
        $this->postJson('/api/v1/vocabulary', $word)->assertStatus(401);
        $this->postJson('/api/v1/vocabulary', $word, $this->hermes())->assertOk();
        $this->getJson('/api/v1/vocabulary')->assertOk()->assertJsonPath('count', 1);
    }

    public function test_static_pages_and_404(): void
    {
        $this->get('/')->assertOk();
        $this->get('/apply/')->assertOk();
        $this->get('/definitely-missing/')->assertNotFound();
        $this->get('/sign-in')->assertRedirect('/apply/');
    }
}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Same tables and column names as the original PHP/MySQL API, so the existing frontend and
 * the Hermes agent keep working unchanged. Timestamps are ISO-8601 UTC strings (created_at etc.).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('leads', function (Blueprint $t) {
            $t->id();
            $t->string('full_name', 160);
            $t->string('email', 190)->unique();
            $t->string('phone', 40)->default('');
            $t->string('target_cefr', 4)->default('B1');
            $t->string('background', 300)->default('');
            $t->mediumText('message');
            $t->string('source', 120)->default('website');
            $t->string('status', 20)->default('new');
            $t->mediumText('notes')->nullable();
            $t->mediumText('hermes_reply')->nullable();
            $t->string('created_at', 25);
            $t->string('updated_at', 25);
        });

        Schema::create('clients', function (Blueprint $t) {
            $t->id();
            $t->unsignedBigInteger('lead_id')->nullable();
            $t->string('full_name', 160);
            $t->string('email', 190)->unique();
            $t->string('phone', 40)->default('');
            $t->string('level', 4)->default('A1');
            $t->string('plan', 80)->default('');
            $t->string('status', 20)->default('active');
            $t->mediumText('notes')->nullable();
            $t->string('started_at', 25);
            $t->string('created_at', 25);
            $t->string('updated_at', 25);
        });

        Schema::create('blog_posts', function (Blueprint $t) {
            $t->id();
            $t->string('slug', 190)->unique();
            $t->string('title', 250);
            $t->string('excerpt', 500)->default('');
            $t->mediumText('content_md');
            $t->string('tags', 300)->default('');
            $t->string('cover_image', 500)->default('');
            $t->string('status', 20)->default('draft');
            $t->string('publish_at', 25)->nullable();
            $t->string('author', 80)->default('Académie de Français');
            $t->string('meta_description', 300)->default('');
            $t->string('created_by', 20)->default('admin');
            $t->string('created_at', 25);
            $t->string('updated_at', 25);
        });

        Schema::create('social_posts', function (Blueprint $t) {
            $t->id();
            $t->string('platform', 20);
            $t->mediumText('content');
            $t->string('link', 500)->default('');
            $t->string('media_url', 500)->default('');
            $t->string('status', 20)->default('draft');
            $t->string('scheduled_at', 25)->nullable();
            $t->string('posted_at', 25)->nullable();
            $t->string('post_url', 500)->default('');
            $t->unsignedBigInteger('blog_id')->nullable();
            $t->string('created_by', 20)->default('admin');
            $t->string('created_at', 25);
            $t->string('updated_at', 25);
        });

        Schema::create('emails', function (Blueprint $t) {
            $t->id();
            $t->string('direction', 3);
            $t->string('message_id', 190)->nullable()->unique();
            $t->string('in_reply_to', 190)->default('');
            $t->string('from_addr', 190)->default('');
            $t->string('to_addr', 190)->default('');
            $t->string('subject', 300)->default('');
            $t->mediumText('body');
            $t->string('status', 20);
            $t->mediumText('hermes_draft')->nullable();
            $t->unsignedBigInteger('lead_id')->nullable();
            $t->unsignedBigInteger('reply_to_id')->nullable();
            $t->string('received_at', 25)->nullable();
            $t->string('sent_at', 25)->nullable();
            $t->string('created_by', 20)->default('admin');
            $t->string('created_at', 25);
            $t->string('updated_at', 25);
        });

        Schema::create('vocabulary', function (Blueprint $t) {
            $t->id();
            $t->string('french_word', 190)->unique();
            $t->mediumText('data');
            $t->string('created_at', 25);
        });

        Schema::create('activity', function (Blueprint $t) {
            $t->id();
            $t->string('actor', 20);
            $t->string('action', 40);
            $t->string('entity', 30);
            $t->unsignedBigInteger('entity_id')->nullable();
            $t->string('detail', 500)->default('');
            $t->string('created_at', 25);
        });

        Schema::create('books', function (Blueprint $t) {
            $t->id();
            $t->string('slug', 190)->unique();
            $t->string('title', 250);
            $t->string('author', 160)->default('');
            $t->string('level', 40)->default('All');
            $t->unsignedInteger('pages')->default(0);
            $t->string('cover', 500)->default('');
            $t->string('file_url', 500)->default('');
            $t->string('file_name', 250)->default('');
            $t->string('category', 80)->default('Grammar');
            $t->mediumText('blurb');
            $t->mediumText('blurb_hi')->nullable();
            $t->string('created_at', 25);
            $t->string('updated_at', 25);
        });
    }

    public function down(): void
    {
        foreach (['books', 'activity', 'vocabulary', 'emails', 'social_posts', 'blog_posts', 'clients', 'leads'] as $table) {
            Schema::dropIfExists($table);
        }
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // One row per person who studies on the site (the owner, a friend, ...).
        Schema::create('learners', function (Blueprint $t) {
            $t->id();
            $t->string('name', 40)->unique();            // login name (case-insensitive unique)
            $t->string('pin_hash');
            $t->date('start_date');                       // day 1 of this learner's plan
            $t->unsignedSmallInteger('daily_minutes')->default(120);
            $t->unsignedTinyInteger('wish_months')->default(3);
            $t->unsignedSmallInteger('credit_hours')->default(0); // hours already mastered at the start (A2 = 70, B1 = 160, B2 = 295)
            $t->timestamp('last_seen_at')->nullable();
            $t->timestamps();
        });

        // Login cookies (only a hash of the random token is stored).
        Schema::create('learner_tokens', function (Blueprint $t) {
            $t->id();
            $t->foreignId('learner_id')->constrained()->cascadeOnDelete();
            $t->string('token_hash', 64)->unique();
            $t->string('device', 120)->default('');
            $t->timestamp('last_used_at')->nullable();
            $t->timestamps();
        });

        // Progress that follows the learner across devices: progress, srs (card schedule), srs.new.
        Schema::create('learner_states', function (Blueprint $t) {
            $t->id();
            $t->foreignId('learner_id')->constrained()->cascadeOnDelete();
            $t->string('key', 30);
            $t->longText('value');                        // JSON
            $t->timestamp('updated_at')->nullable();
            $t->unique(['learner_id', 'key']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('learner_states');
        Schema::dropIfExists('learner_tokens');
        Schema::dropIfExists('learners');
    }
};

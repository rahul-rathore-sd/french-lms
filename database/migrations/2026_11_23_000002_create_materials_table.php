<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // One row per study item: grammar lesson, reading text, listening script, writing/speaking task.
        Schema::create('materials', function (Blueprint $t) {
            $t->id();
            $t->string('slug', 120)->unique();
            $t->string('kind', 20);            // grammar | reading | listening | writing | speaking
            $t->string('skill', 2);            // GR | CE | CO | EE | EO
            $t->string('level', 2);            // A1 | A2 | B1 | B2 | C1
            $t->string('nclc', 20)->nullable();
            $t->string('title', 200);
            $t->string('title_hi', 200)->nullable();
            $t->string('summary', 500)->default('');
            $t->unsignedSmallInteger('minutes')->default(10);
            $t->string('source', 12)->default('seed');   // seed | daily
            $t->date('publish_on')->nullable();          // null = always available
            $t->json('body');
            $t->timestamps();
            $t->index(['kind', 'level']);
            $t->index('publish_on');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('materials');
    }
};

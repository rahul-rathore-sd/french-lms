<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // The learner's chosen voice: an edge-tts voice id, "mix" (mixed accents) or "browser" (voices built into the browser).
        Schema::table('learners', function (Blueprint $t) {
            $t->string('voice', 60)->default('fr-CA-SylvieNeural')->after('wish_months');
        });
    }

    public function down(): void
    {
        Schema::table('learners', function (Blueprint $t) {
            $t->dropColumn('voice');
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('avatar_url')->nullable()->after('name');
            $table->string('role')->default('student')->after('role_title'); // student, instructor, admin
            $table->string('country')->default('Canada')->after('role');
            $table->text('bio')->nullable()->after('country');
            $table->string('target_goal')->nullable()->after('bio');
            $table->timestamp('last_active_at')->nullable()->after('target_goal');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['avatar_url', 'role', 'country', 'bio', 'target_goal', 'last_active_at']);
        });
    }
};

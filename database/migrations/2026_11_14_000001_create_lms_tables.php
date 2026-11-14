<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add profile and LMS fields to users
        Schema::table('users', function (Blueprint $table) {
            $table->string('level')->default('B1');
            $table->integer('xp_points')->default(1250);
            $table->integer('current_streak')->default(14);
            $table->string('theme_preference')->default('amber'); // amber, emerald, indigo, slate
            $table->string('role_title')->default('Académie Scholar');
        });

        // Courses Table
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('level'); // A1, A2, B1, B2, C1
            $table->string('category');
            $table->text('short_desc');
            $table->text('description');
            $table->string('instructor_name');
            $table->string('instructor_role');
            $table->integer('duration_weeks')->default(6);
            $table->integer('lessons_count')->default(12);
            $table->string('badge_color')->default('amber');
            $table->string('icon')->default('book-open');
            $table->boolean('is_featured')->default(false);
            $table->timestamps();
        });

        // Lessons Table
        Schema::create('lessons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained()->cascadeOnDelete();
            $table->integer('order')->default(1);
            $table->string('title');
            $table->string('section_title');
            $table->integer('duration_mins')->default(15);
            $table->longText('french_text');
            $table->longText('english_translation');
            $table->text('audio_phrase')->nullable();
            $table->json('vocabulary_json')->nullable();
            $table->text('quiz_question')->nullable();
            $table->json('quiz_options_json')->nullable();
            $table->integer('quiz_correct_index')->default(0);
            $table->timestamps();
        });

        // Enrollments Table
        Schema::create('enrollments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('course_id')->constrained()->cascadeOnDelete();
            $table->integer('progress_percent')->default(0);
            $table->integer('completed_lessons_count')->default(0);
            $table->string('status')->default('in_progress'); // enrolled, in_progress, completed
            $table->timestamp('last_accessed_at')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'course_id']);
        });

        // Lesson Completions Table
        Schema::create('lesson_completions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('lesson_id')->constrained()->cascadeOnDelete();
            $table->timestamp('completed_at')->useCurrent();
            $table->timestamps();

            $table->unique(['user_id', 'lesson_id']);
        });

        // Custom Dictionary Words Table
        Schema::create('custom_words', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('word');
            $table->string('ipa')->nullable();
            $table->string('meaning');
            $table->string('level')->default('B1');
            $table->string('category')->default('General');
            $table->text('example_fr')->nullable();
            $table->text('example_en')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('custom_words');
        Schema::dropIfExists('lesson_completions');
        Schema::dropIfExists('enrollments');
        Schema::dropIfExists('lessons');
        Schema::dropIfExists('courses');

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['level', 'xp_points', 'current_streak', 'theme_preference', 'role_title']);
        });
    }
};

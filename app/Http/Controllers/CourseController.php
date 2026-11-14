<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Lesson;
use App\Models\LessonCompletion;
use App\Services\StreakService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CourseController extends Controller
{
    public function enroll(Request $request, $slug)
    {
        $user = Auth::user();
        $course = Course::where('slug', $slug)->firstOrFail();

        $enrollment = Enrollment::firstOrCreate(
            ['user_id' => $user->id, 'course_id' => $course->id],
            [
                'progress_percent' => 0,
                'completed_lessons_count' => 0,
                'status' => 'in_progress',
                'last_accessed_at' => now(),
            ]
        );

        $firstLesson = $course->lessons()->orderBy('order')->first();

        return response()->json([
            'status' => 'ok',
            'message' => "Enrolled in {$course->title} successfully!",
            'course' => $course,
            'firstLessonId' => $firstLesson ? $firstLesson->id : null,
        ]);
    }

    public function completeLesson(Request $request, $id)
    {
        $user = Auth::user();
        $lesson = Lesson::with('course.lessons')->findOrFail($id);
        $course = $lesson->course;

        // Record completion
        LessonCompletion::firstOrCreate([
            'user_id' => $user->id,
            'lesson_id' => $lesson->id,
        ]);

        // Record Task Activity & Update Daily Streak
        $streakData = StreakService::recordTaskCompletion($user);

        // Recalculate course progress
        $totalLessons = $course->lessons->count();
        $completedLessonsCount = LessonCompletion::where('user_id', $user->id)
            ->whereIn('lesson_id', $course->lessons->pluck('id'))
            ->count();

        $progressPercent = $totalLessons > 0 ? round(($completedLessonsCount / $totalLessons) * 100) : 0;
        $status = $progressPercent >= 100 ? 'completed' : 'in_progress';

        $enrollment = Enrollment::updateOrCreate(
            ['user_id' => $user->id, 'course_id' => $course->id],
            [
                'completed_lessons_count' => $completedLessonsCount,
                'progress_percent' => $progressPercent,
                'status' => $status,
                'last_accessed_at' => now(),
            ]
        );

        return response()->json([
            'status' => 'ok',
            'message' => 'Lesson marked as complete! Academic streak updated.',
            'progressPercent' => $progressPercent,
            'completedCount' => $completedLessonsCount,
            'streak' => $streakData['current_streak'],
            'activityHistory' => $streakData['activity_history'],
        ]);
    }

    public function submitQuiz(Request $request, $id)
    {
        $lesson = Lesson::findOrFail($id);
        $selectedIdx = (int) $request->input('selected_index');
        $user = Auth::user();

        $isCorrect = ($selectedIdx === (int) $lesson->quiz_correct_index);

        if ($isCorrect) {
            $streakData = StreakService::recordTaskCompletion($user);
            return response()->json([
                'status' => 'ok',
                'correct' => true,
                'message' => 'Correct! Outstanding work. Academic streak updated.',
                'streak' => $streakData['current_streak'],
                'activityHistory' => $streakData['activity_history'],
            ]);
        } else {
            return response()->json([
                'status' => 'ok',
                'correct' => false,
                'message' => 'Incorrect. Please review the lesson text and try again!',
                'correctIndex' => $lesson->quiz_correct_index,
            ]);
        }
    }

    public function addWord(Request $request)
    {
        $validated = $request->validate([
            'word' => 'required|string|max:100',
            'meaning' => 'required|string|max:255',
            'ipa' => 'nullable|string|max:100',
        ]);

        $user = Auth::user();
        $word = $user->customWords()->create([
            'word' => $validated['word'],
            'ipa' => $validated['ipa'] ?: '/' . strtolower($validated['word']) . '/',
            'meaning' => $validated['meaning'],
            'level' => 'B1',
            'category' => 'Custom Study',
            'example_fr' => "{$validated['word']} est une expression élégante.",
            'example_en' => "{$validated['word']} is an elegant expression.",
        ]);

        // Record Task Activity & Update Daily Streak
        $streakData = StreakService::recordTaskCompletion($user);

        return response()->json([
            'status' => 'ok',
            'message' => 'Word added to lexicon! Academic streak updated.',
            'streak' => $streakData['current_streak'],
            'activityHistory' => $streakData['activity_history'],
            'word' => [
                'fr' => $word->word,
                'ipa' => $word->ipa,
                'typeLabel' => 'custom word',
                'level' => $word->level,
                'en' => $word->meaning,
                'exFr' => $word->example_fr,
                'exEn' => $word->example_en,
            ],
        ]);
    }
}

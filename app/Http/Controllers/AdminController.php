<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\CustomWord;
use App\Models\Enrollment;
use App\Models\Lesson;
use App\Models\LessonCompletion;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AdminController extends Controller
{
    public function index(Request $request)
    {
        $currentUser = Auth::user();

        // Platform Metrics
        $totalUsers = User::count();
        $activeStreaksCount = User::where('current_streak', '>', 0)->count();
        $totalCourses = Course::count();
        $totalLessons = Lesson::count();
        $totalCompletions = LessonCompletion::count();
        $totalWords = CustomWord::count();

        // Data collections
        $users = User::withCount(['enrollments', 'completions', 'customWords'])
            ->orderBy('id', 'asc')
            ->get();

        $courses = Course::withCount('lessons')->orderBy('id', 'asc')->get();
        $lessons = Lesson::with('course')->orderBy('course_id')->orderBy('order')->get();
        $words = CustomWord::with('user')->latest()->get();

        return view('admin.dashboard', compact(
            'currentUser',
            'totalUsers',
            'activeStreaksCount',
            'totalCourses',
            'totalLessons',
            'totalCompletions',
            'totalWords',
            'users',
            'courses',
            'lessons',
            'words'
        ));
    }

    // --- USER MANAGEMENT ---
    public function storeUser(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'level' => 'required|in:A1,A2,B1,B2,C1,C2',
            'role' => 'required|in:student,instructor,admin',
            'country' => 'nullable|string|max:100',
            'current_streak' => 'nullable|integer|min:0',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'level' => $validated['level'],
            'role' => $validated['role'],
            'role_title' => $validated['role'] === 'admin' ? 'Academy Administrator' : ($validated['role'] === 'instructor' ? 'Faculty Professor' : "{$validated['level']} Scholar"),
            'country' => $validated['country'] ?? 'Canada',
            'current_streak' => $validated['current_streak'] ?? 0,
            'last_active_at' => now(),
        ]);

        // Auto enroll into Course 1
        $firstCourse = Course::first();
        if ($firstCourse) {
            Enrollment::create([
                'user_id' => $user->id,
                'course_id' => $firstCourse->id,
                'progress_percent' => 0,
                'status' => 'in_progress',
                'last_accessed_at' => now(),
            ]);
        }

        return redirect()->route('admin.index')->with('success', "Scholar {$user->name} created successfully!");
    }

    public function updateUser(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'level' => 'required|in:A1,A2,B1,B2,C1,C2',
            'role' => 'required|in:student,instructor,admin',
            'current_streak' => 'required|integer|min:0',
            'country' => 'nullable|string|max:100',
            'bio' => 'nullable|string|max:500',
        ]);

        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->level = $validated['level'];
        $user->role = $validated['role'];
        $user->current_streak = $validated['current_streak'];
        $user->country = $validated['country'] ?? $user->country;
        $user->bio = $validated['bio'] ?? $user->bio;

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        return redirect()->route('admin.index')->with('success', "Scholar {$user->name} updated successfully!");
    }

    public function deleteUser($id)
    {
        $user = User::findOrFail($id);
        if ($user->id === Auth::id()) {
            return redirect()->route('admin.index')->with('error', 'Cannot delete your own logged-in account.');
        }

        $name = $user->name;
        $user->delete();

        return redirect()->route('admin.index')->with('success', "Scholar {$name} has been removed.");
    }

    // --- COURSE MANAGEMENT ---
    public function storeCourse(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'level' => 'required|in:A1,A2,B1,B2,C1,C2',
            'category' => 'required|string|max:100',
            'short_desc' => 'required|string|max:300',
            'description' => 'required|string',
            'instructor_name' => 'required|string|max:255',
            'duration_weeks' => 'required|integer|min:1',
        ]);

        $slug = Str::slug($validated['title']);
        $originalSlug = $slug;
        $count = 1;
        while (Course::where('slug', $slug)->exists()) {
            $slug = "{$originalSlug}-" . (++$count);
        }

        Course::create([
            'title' => $validated['title'],
            'slug' => $slug,
            'level' => $validated['level'],
            'category' => $validated['category'],
            'short_desc' => $validated['short_desc'],
            'description' => $validated['description'],
            'instructor_name' => $validated['instructor_name'],
            'instructor_role' => 'Académie Faculty',
            'duration_weeks' => $validated['duration_weeks'],
            'lessons_count' => 0,
            'badge_color' => 'amber',
            'icon' => 'book-open',
        ]);

        return redirect()->route('admin.index')->with('success', "Course '{$validated['title']}' created successfully!");
    }

    public function updateCourse(Request $request, $id)
    {
        $course = Course::findOrFail($id);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'level' => 'required|in:A1,A2,B1,B2,C1,C2',
            'category' => 'required|string|max:100',
            'short_desc' => 'required|string|max:300',
            'description' => 'required|string',
            'instructor_name' => 'required|string|max:255',
            'duration_weeks' => 'required|integer|min:1',
        ]);

        $course->update($validated);

        return redirect()->route('admin.index')->with('success', "Course '{$course->title}' updated successfully!");
    }

    public function deleteCourse($id)
    {
        $course = Course::findOrFail($id);
        $title = $course->title;
        $course->delete();

        return redirect()->route('admin.index')->with('success', "Course '{$title}' deleted.");
    }

    // --- LESSON MANAGEMENT ---
    public function storeLesson(Request $request)
    {
        $validated = $request->validate([
            'course_id' => 'required|exists:courses,id',
            'title' => 'required|string|max:255',
            'section_title' => 'required|string|max:255',
            'order' => 'required|integer|min:1',
            'duration_mins' => 'required|integer|min:1',
            'french_text' => 'required|string',
            'english_translation' => 'required|string',
            'audio_phrase' => 'nullable|string',
            'quiz_question' => 'required|string',
            'quiz_option_0' => 'required|string',
            'quiz_option_1' => 'required|string',
            'quiz_option_2' => 'required|string',
            'quiz_option_3' => 'required|string',
            'quiz_correct_index' => 'required|integer|min:0|max:3',
        ]);

        $options = [
            $validated['quiz_option_0'],
            $validated['quiz_option_1'],
            $validated['quiz_option_2'],
            $validated['quiz_option_3'],
        ];

        Lesson::create([
            'course_id' => $validated['course_id'],
            'title' => $validated['title'],
            'section_title' => $validated['section_title'],
            'order' => $validated['order'],
            'duration_mins' => $validated['duration_mins'],
            'french_text' => $validated['french_text'],
            'english_translation' => $validated['english_translation'],
            'audio_phrase' => $validated['audio_phrase'] ?? $validated['title'],
            'quiz_question' => $validated['quiz_question'],
            'quiz_options_json' => json_encode($options),
            'quiz_correct_index' => $validated['quiz_correct_index'],
        ]);

        // Update course lessons count
        $course = Course::find($validated['course_id']);
        $course->lessons_count = $course->lessons()->count();
        $course->save();

        return redirect()->route('admin.index')->with('success', "Lesson '{$validated['title']}' created successfully!");
    }

    public function updateLesson(Request $request, $id)
    {
        $lesson = Lesson::findOrFail($id);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'section_title' => 'required|string|max:255',
            'order' => 'required|integer|min:1',
            'duration_mins' => 'required|integer|min:1',
            'french_text' => 'required|string',
            'english_translation' => 'required|string',
            'audio_phrase' => 'nullable|string',
            'quiz_question' => 'required|string',
            'quiz_option_0' => 'required|string',
            'quiz_option_1' => 'required|string',
            'quiz_option_2' => 'required|string',
            'quiz_option_3' => 'required|string',
            'quiz_correct_index' => 'required|integer|min:0|max:3',
        ]);

        $options = [
            $validated['quiz_option_0'],
            $validated['quiz_option_1'],
            $validated['quiz_option_2'],
            $validated['quiz_option_3'],
        ];

        $lesson->update([
            'title' => $validated['title'],
            'section_title' => $validated['section_title'],
            'order' => $validated['order'],
            'duration_mins' => $validated['duration_mins'],
            'french_text' => $validated['french_text'],
            'english_translation' => $validated['english_translation'],
            'audio_phrase' => $validated['audio_phrase'] ?? $validated['title'],
            'quiz_question' => $validated['quiz_question'],
            'quiz_options_json' => json_encode($options),
            'quiz_correct_index' => $validated['quiz_correct_index'],
        ]);

        return redirect()->route('admin.index')->with('success', "Lesson '{$lesson->title}' updated successfully!");
    }

    public function deleteLesson($id)
    {
        $lesson = Lesson::findOrFail($id);
        $courseId = $lesson->course_id;
        $title = $lesson->title;
        $lesson->delete();

        // Update count
        $course = Course::find($courseId);
        if ($course) {
            $course->lessons_count = $course->lessons()->count();
            $course->save();
        }

        return redirect()->route('admin.index')->with('success', "Lesson '{$title}' removed.");
    }

    // --- VOCABULARY MANAGEMENT ---
    public function storeWord(Request $request)
    {
        $validated = $request->validate([
            'word' => 'required|string|max:100',
            'meaning' => 'required|string|max:255',
            'meaning_hi' => 'nullable|string|max:255',
            'ipa' => 'nullable|string|max:100',
            'level' => 'required|in:A1,A2,B1,B2,C1,C2',
            'category' => 'nullable|string|max:100',
            'example_fr' => 'nullable|string',
            'example_en' => 'nullable|string',
            'example_hi' => 'nullable|string',
            'use_case' => 'nullable|string',
        ]);

        CustomWord::create([
            'user_id' => Auth::id(),
            'word' => $validated['word'],
            'ipa' => $validated['ipa'] ?: '/' . strtolower($validated['word']) . '/',
            'meaning' => $validated['meaning'],
            'meaning_hi' => $validated['meaning_hi'] ?? null,
            'level' => $validated['level'],
            'category' => $validated['category'] ?? 'Academic Core',
            'example_fr' => $validated['example_fr'] ?? "{$validated['word']} est un terme classique.",
            'example_en' => $validated['example_en'] ?? "{$validated['word']} is a classical term.",
            'example_hi' => $validated['example_hi'] ?? null,
            'use_case' => $validated['use_case'] ?? null,
            'source' => 'Admin Console',
        ]);

        return redirect()->route('admin.index')->with('success', "Vocabulary word '{$validated['word']}' added with Hindi definitions and practical use case.");
    }

    public function updateWord(Request $request, $id)
    {
        $word = CustomWord::findOrFail($id);

        $validated = $request->validate([
            'word' => 'required|string|max:100',
            'meaning' => 'required|string|max:255',
            'meaning_hi' => 'nullable|string|max:255',
            'ipa' => 'nullable|string|max:100',
            'level' => 'required|in:A1,A2,B1,B2,C1,C2',
            'category' => 'nullable|string|max:100',
            'example_fr' => 'nullable|string',
            'example_en' => 'nullable|string',
            'example_hi' => 'nullable|string',
            'use_case' => 'nullable|string',
        ]);

        $word->update([
            'word' => $validated['word'],
            'ipa' => $validated['ipa'] ?: '/' . strtolower($validated['word']) . '/',
            'meaning' => $validated['meaning'],
            'meaning_hi' => $validated['meaning_hi'] ?? null,
            'level' => $validated['level'],
            'category' => $validated['category'] ?? 'Academic Core',
            'example_fr' => $validated['example_fr'] ?? $word->example_fr,
            'example_en' => $validated['example_en'] ?? $word->example_en,
            'example_hi' => $validated['example_hi'] ?? $word->example_hi,
            'use_case' => $validated['use_case'] ?? $word->use_case,
        ]);

        return redirect()->route('admin.index')->with('success', "Vocabulary word '{$word->word}' updated successfully.");
    }

    public function deleteWord($id)
    {
        $word = CustomWord::findOrFail($id);
        $name = $word->word;
        $word->delete();

        return redirect()->route('admin.index')->with('success', "Word '{$name}' deleted from lexicon.");
    }
}

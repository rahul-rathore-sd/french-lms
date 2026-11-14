<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Enrollment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            $user = Auth::user();

            if ($user->role !== 'admin') {
                // Detect 1st login for scholars (last_active_at is null or 0 enrollments)
                $isFirstLogin = is_null($user->last_active_at) || $user->enrollments()->count() === 0;

                if ($isFirstLogin) {
                    // After 1st login, strictly assign scholar to A1 level
                    $user->level = 'A1';
                    $user->role_title = 'A1 Novice Scholar';
                    if (empty($user->target_goal)) {
                        $user->target_goal = 'Build acoustic foundations, perfect French nasal vowels and greetings';
                    }
                    $user->last_active_at = now();
                    $user->save();

                    // Auto-enroll scholar in the A1 foundations course
                    $a1Course = Course::where('level', 'A1')->first();
                    if ($a1Course) {
                        Enrollment::firstOrCreate([
                            'user_id' => $user->id,
                            'course_id' => $a1Course->id,
                        ], [
                            'progress_percent' => 0,
                            'completed_lessons_count' => 0,
                            'status' => 'in_progress',
                            'last_accessed_at' => now(),
                        ]);
                    }
                } else {
                    $user->last_active_at = now();
                    $user->save();
                }
            } else {
                $user->last_active_at = now();
                $user->save();
            }

            if ($user->role === 'admin') {
                return redirect()->intended(route('admin.index'));
            }

            return redirect()->intended(route('dashboard'));
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our academic records.',
        ])->onlyInput('email');
    }

    public function showRegister()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
        ]);

        // Create new user strictly at A1 Level
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'level' => 'A1', // Strictly A1 level for new users
            'role' => 'student',
            'role_title' => 'A1 Novice Scholar',
            'country' => 'Canada',
            'target_goal' => 'Build acoustic foundations, perfect French nasal vowels and greetings',
            'bio' => 'Newly enrolled bilingual scholar starting in the French A1 Foundations track.',
            'xp_points' => 300,
            'current_streak' => 1,
            'theme_preference' => 'amber',
            'last_active_at' => now(),
        ]);

        // Auto-enroll user into A1 course immediately
        $a1Course = Course::where('level', 'A1')->first();
        if ($a1Course) {
            Enrollment::firstOrCreate([
                'user_id' => $user->id,
                'course_id' => $a1Course->id,
            ], [
                'progress_percent' => 0,
                'status' => 'in_progress',
                'enrolled_at' => now(),
            ]);
        }

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('dashboard');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    public function switchUser(Request $request, $id)
    {
        $targetUser = User::findOrFail($id);
        Auth::login($targetUser);
        $request->session()->regenerate();
        session(['theme_preference' => $targetUser->theme_preference ?? 'amber']);

        return redirect()->route('dashboard')->with('status', 'Switched session to scholar ' . $targetUser->name);
    }
}

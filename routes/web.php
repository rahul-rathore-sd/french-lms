<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

// Public Authentication Routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/auth/clerk-sync', [AuthController::class, 'clerkSync'])->name('auth.clerk-sync');
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Root redirect
Route::get('/', function () {
    return redirect()->route('dashboard');
});

// Protected LMS Routes
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/api/theme', [DashboardController::class, 'updateTheme'])->name('theme.update');

    // LMS Actions
    Route::post('/courses/{slug}/enroll', [CourseController::class, 'enroll'])->name('courses.enroll');
    Route::post('/lessons/{id}/complete', [CourseController::class, 'completeLesson'])->name('lessons.complete');
    Route::post('/lessons/{id}/quiz', [CourseController::class, 'submitQuiz'])->name('lessons.quiz');

    // Custom Vocabulary
    Route::post('/api/add-word', [CourseController::class, 'addWord'])->name('dictionary.add');

    // Admin Management Routes
    Route::get('/admin', [AdminController::class, 'index'])->name('admin.index');
    Route::post('/admin/users', [AdminController::class, 'storeUser'])->name('admin.users.store');
    Route::post('/admin/users/{id}/update', [AdminController::class, 'updateUser'])->name('admin.users.update');
    Route::post('/admin/users/{id}/delete', [AdminController::class, 'deleteUser'])->name('admin.users.delete');

    Route::post('/admin/courses', [AdminController::class, 'storeCourse'])->name('admin.courses.store');
    Route::post('/admin/courses/{id}/update', [AdminController::class, 'updateCourse'])->name('admin.courses.update');
    Route::post('/admin/courses/{id}/delete', [AdminController::class, 'deleteCourse'])->name('admin.courses.delete');

    Route::post('/admin/lessons', [AdminController::class, 'storeLesson'])->name('admin.lessons.store');
    Route::post('/admin/lessons/{id}/update', [AdminController::class, 'updateLesson'])->name('admin.lessons.update');
    Route::post('/admin/lessons/{id}/delete', [AdminController::class, 'deleteLesson'])->name('admin.lessons.delete');

    Route::post('/admin/words', [AdminController::class, 'storeWord'])->name('admin.words.store');
    Route::post('/admin/words/{id}/update', [AdminController::class, 'updateWord'])->name('admin.words.update');
    Route::post('/admin/words/{id}/delete', [AdminController::class, 'deleteWord'])->name('admin.words.delete');

    // Multi-User Switcher
    Route::post('/switch-user/{id}', [AuthController::class, 'switchUser'])->name('user.switch');
});

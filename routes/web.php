<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LearnController;
use App\Http\Controllers\LevelCheckController;
use App\Http\Controllers\PracticeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\StateController;
use App\Http\Controllers\TcfController;
use Illuminate\Support\Facades\Route;

// public: sign in / create a profile (the web server's password already keeps strangers out)
Route::get('login', [AuthController::class, 'showLogin']);
Route::post('login', [AuthController::class, 'login']);
Route::get('register', [AuthController::class, 'showRegister']);
Route::post('register', [AuthController::class, 'register']);

// everything else needs a logged-in learner
Route::middleware('learner')->group(function () {
    Route::get('/', HomeController::class);
    Route::get('learn', [LearnController::class, 'index']);
    Route::get('r/{slug}', [LearnController::class, 'show'])->where('slug', '[a-z0-9-]+');
    Route::get('practice', [PracticeController::class, 'cards']);
    Route::get('words', [PracticeController::class, 'words']);
    Route::get('tcf', TcfController::class);
    Route::get('level-check', LevelCheckController::class);

    Route::post('logout', [AuthController::class, 'logout']);
    Route::get('me', [ProfileController::class, 'show']);
    Route::post('me/settings', [ProfileController::class, 'settings']);
    Route::post('me/restart', [ProfileController::class, 'restart']);
    Route::post('me/pin', [ProfileController::class, 'pin']);
    Route::put('me/state/{key}', [StateController::class, 'put'])->where('key', '[a-z.]+');
});

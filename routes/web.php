<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\LearnController;
use App\Http\Controllers\LevelCheckController;
use App\Http\Controllers\PracticeController;
use App\Http\Controllers\TcfController;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class);
Route::get('learn', [LearnController::class, 'index']);
Route::get('r/{slug}', [LearnController::class, 'show'])->where('slug', '[a-z0-9-]+');
Route::get('practice', [PracticeController::class, 'cards']);
Route::get('words', [PracticeController::class, 'words']);
Route::get('tcf', TcfController::class);
Route::get('level-check', LevelCheckController::class);

<?php

use App\Http\Controllers\Api\MaterialController;
use App\Http\Controllers\Api\PlanController;
use App\Http\Controllers\Api\StatusController;
use App\Http\Controllers\Api\VocabularyController;
use Illuminate\Support\Facades\Route;

/*
 | JSON API at /api/v1/*
 |
 | Public (the whole site is private anyway):  GET status · GET vocabulary
 | Hermes / automation only ("Authorization: Bearer <HERMES_API_KEY>"):
 |   POST vocabulary · GET|POST materials · DELETE materials/{slug} · GET plan/today
 */
Route::prefix('v1')->group(function () {
    Route::get('status', [StatusController::class, 'status']);

    Route::get('vocabulary', [VocabularyController::class, 'index']);
    Route::post('vocabulary', [VocabularyController::class, 'store']);

    Route::get('materials', [MaterialController::class, 'index']);
    Route::post('materials', [MaterialController::class, 'store']);
    Route::delete('materials/{slug}', [MaterialController::class, 'destroy']);

    Route::get('plan/today', [PlanController::class, 'today']);
});

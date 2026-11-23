<?php

use App\Http\Controllers\Api\AdminController;
use App\Http\Controllers\Api\BlogController;
use App\Http\Controllers\Api\BookController;
use App\Http\Controllers\Api\ClientController;
use App\Http\Controllers\Api\EmailController;
use App\Http\Controllers\Api\LeadController;
use App\Http\Controllers\Api\SocialController;
use App\Http\Controllers\Api\StatusController;
use App\Http\Controllers\Api\VocabularyController;
use Illuminate\Support\Facades\Route;

/*
 | JSON API at /api/v1/*  (same contract as the original PHP API, so the frontend and the Hermes agent are unchanged)
 |
 | Public:        POST leads · GET blog · GET blog/{slug} · GET vocabulary · GET books · GET status
 | Admin/Hermes:  everything else (admin session cookie, or "Authorization: Bearer <HERMES_API_KEY>")
 */
Route::middleware('apiadmin')->prefix('v1')->group(function () {
    Route::get('status', [StatusController::class, 'status']);
    Route::get('stats', [StatusController::class, 'stats']);
    Route::get('activity', [StatusController::class, 'activity']);

    Route::get('admin/me', [AdminController::class, 'me']);
    Route::post('admin/login', [AdminController::class, 'login']);
    Route::post('admin/logout', [AdminController::class, 'logout']);

    // "admissions" is the legacy alias of "leads"
    foreach (['leads', 'admissions'] as $name) {
        Route::get($name, [LeadController::class, 'index']);
        Route::post($name, [LeadController::class, 'store']);
        Route::get("$name/{id}", [LeadController::class, 'show']);
        Route::patch("$name/{id}", [LeadController::class, 'update']);
        Route::delete("$name/{id}", [LeadController::class, 'destroy']);
        Route::post("$name/{id}/convert", [LeadController::class, 'convert']);
    }

    Route::apiResource('clients', ClientController::class)->parameters(["clients" => "id"]);
    Route::apiResource('blog', BlogController::class)->parameters(['blog' => 'id']);
    Route::apiResource('social', SocialController::class)->parameters(['social' => 'id']);
    Route::apiResource('emails', EmailController::class)->parameters(['emails' => 'id']);
    Route::apiResource('books', BookController::class)->parameters(['books' => 'id']);

    Route::get('vocabulary', [VocabularyController::class, 'index']);
    Route::post('vocabulary', [VocabularyController::class, 'store']);
});

<?php

use App\Http\Controllers\Api\HermesApiController;
use Illuminate\Support\Facades\Route;

// Hermes Automated Daily Vocabulary & Curriculum Ingestion API
Route::prefix('v1')->group(function () {
    Route::get('/status', [HermesApiController::class, 'status'])->name('api.hermes.status');
    Route::get('/vocabulary', [HermesApiController::class, 'index'])->name('api.hermes.vocab.index');
    Route::post('/vocabulary', [HermesApiController::class, 'ingest'])->name('api.hermes.vocab.ingest');
});

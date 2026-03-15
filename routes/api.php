<?php

use App\Http\Controllers\Api\AuthController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\PmbIntegrationController;

// PMB Integration Routes
Route::middleware('pmb.auth')->group(function () {
    Route::post('/v1/pmb/receive-camaba', [PmbIntegrationController::class, 'receiveCamaba']);
});


// Endpoint Publik
Route::prefix('v1')->group(function () {
    // Public/API Key Protected
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/semesters', [AuthController::class, 'getSemesters']);
    Route::get('/prodi', [AuthController::class, 'getProdi']);
    Route::middleware('auth:sanctum')->group(function () {
        // Protected by User Token
        Route::get('/user/me', [AuthController::class, 'me']);
        Route::post('/user/logout', [AuthController::class, 'logout']);
        Route::get('/user/krs', [AuthController::class, 'getUserKrs']);
    });
});

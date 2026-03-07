<?php

use App\Http\Controllers\Api\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\PmbIntegrationController;

// PMB Integration Routes
Route::middleware('pmb.auth')->group(function () {
    Route::post('/v1/pmb/receive-camaba', [PmbIntegrationController::class, 'receiveCamaba']);
});


// Endpoint Publik
Route::post('/v1/login', [AuthController::class, 'login']);

// Endpoint Terproteksi Token (Wajib kirim Bearer Token)
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/v1/user/me', [AuthController::class, 'me']);
    Route::post('/v1/user/logout', [AuthController::class, 'logout']);
});

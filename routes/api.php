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
Route::post('/login', [AuthController::class, 'login']);

// Endpoint Terproteksi Token (Wajib kirim Bearer Token)
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user/me', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);
});

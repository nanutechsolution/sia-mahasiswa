<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\PmbIntegrationController;

// PMB Integration Routes
Route::middleware('pmb.auth')->group(function () {
    Route::post('/v1/pmb/receive-camaba', [PmbIntegrationController::class, 'receiveCamaba']);
});

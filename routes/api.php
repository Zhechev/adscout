<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PlayerController;
use App\Http\Controllers\TeamController;
use App\Http\Controllers\AuthController;

// Grouping routes that require user authentication.
Route::middleware('auth:sanctum')->group(function () {
    // Standard RESTful routes for players and teams management
    Route::apiResource('players', PlayerController::class);
    Route::apiResource('teams', TeamController::class);

    // Route to get the authenticated user's information
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    // Logout route for authenticated users
    Route::post('/logout', [AuthController::class, 'logout']);
});

// Registration and login routes for users
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

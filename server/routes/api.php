<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Lang\LanguageController;
use Helper\Response\Response;
use Translation\Message;

Route::get('/test', fn () => Response::_401(['message' => Message::get('invalid_email')]));
Route::get('/front-language', [LanguageController::class, 'getTranslations']);

// Auth
Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login',    [AuthController::class, 'login']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/me',      [AuthController::class, 'me']);
    });
});

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    // Membership Types
    Route::apiResource('membership-types', \App\Http\Controllers\MembershipTypeController::class);
    
    // Members
    Route::get('members/expiring', [\App\Http\Controllers\MemberController::class, 'expiring']);
    Route::get('members/stats', [\App\Http\Controllers\MemberController::class, 'stats']);
    Route::apiResource('members', \App\Http\Controllers\MemberController::class);

    // Check-in
    Route::post('gyms/{gym}/check-in', function (\App\Http\Requests\CheckInRequest $request, \App\Services\CheckInService $service) {
        return response()->json($service->handle($request->identifier, $request->method, $request->gym->id));
    })->middleware(\App\Http\Middleware\GymScope::class);
});

<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AnalyticsController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\CheckInController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Lang\LanguageController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\MembershipTypeController;
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
    Route::apiResource('membership-types', MembershipTypeController::class);
    
    // Members
    Route::get('members/expiring', [MemberController::class, 'expiring']);
    Route::get('members/stats', [MemberController::class, 'stats']);
    Route::apiResource('members', MemberController::class);

    // Check-in
    Route::post('gyms/{gym}/check-in', [CheckInController::class, 'store']);

    // Member card
    Route::get('gyms/{gym}/members/{member}/card-data', [MemberController::class, 'cardData']);

    // Dashboard & Analytics
    Route::get('gyms/{gym}/dashboard', [DashboardController::class, 'index']);
    Route::get('gyms/{gym}/analytics', [AnalyticsController::class, 'index']);

    // Member card
    Route::get('gyms/{gym}/members/{member}/card-data', [MemberController::class, 'cardData']);
    Route::get('gyms/{gym}/members/{member}/card-pdf', [MemberController::class, 'cardPdf']);
    Route::post('gyms/{gym}/members/cards-pdf', [MemberController::class, 'batchCardsPdf']);
});

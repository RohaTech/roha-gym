<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AnalyticsController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\GymController as AdminGymController;
use App\Http\Controllers\Admin\SubscriptionController as AdminSubscriptionController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\CheckInController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\GymProfileController;
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
    // Gym Profile
    Route::get('gym/profile', [GymProfileController::class, 'show']);
    Route::post('gym/profile', [GymProfileController::class, 'update']);

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

// Admin (platform super-admin) routes
Route::middleware(['auth:sanctum', 'admin'])->prefix('admin')->group(function () {
    // Platform dashboard
    Route::get('dashboard', [AdminDashboardController::class, 'index']);

    // Gym (tenant) management
    Route::get('gyms', [AdminGymController::class, 'index']);
    Route::post('gyms', [AdminGymController::class, 'store']);
    Route::get('gyms/{gym}', [AdminGymController::class, 'show']);
    Route::post('gyms/{gym}', [AdminGymController::class, 'update']); // POST + _method for multipart
    Route::patch('gyms/{gym}/status', [AdminGymController::class, 'updateStatus']);
    Route::delete('gyms/{gym}', [AdminGymController::class, 'destroy']);

    // Subscription / payment tracking
    Route::get('subscriptions', [AdminSubscriptionController::class, 'index']);
    Route::get('subscriptions/{gym}', [AdminSubscriptionController::class, 'show']);
    Route::post('subscriptions/{gym}/payments', [AdminSubscriptionController::class, 'store']);
    Route::delete('subscriptions/payments/{payment}', [AdminSubscriptionController::class, 'destroy']);
});

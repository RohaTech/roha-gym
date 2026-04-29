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

<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ServiceJobController;
use App\Http\Controllers\ApplicationController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ArtisanController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\UserProfileController;

Route::post('/auth/register/client', [AuthController::class, 'registerClient']);
Route::post('/auth/register/artisan', [AuthController::class, 'registerArtisan']);
Route::post('/auth/login', [AuthController::class, 'login']);
Route::post('/auth/firebase-login', [AuthController::class, 'firebaseAuth']);

Route::get('/categories', [CategoryController::class, 'index']);

Route::get('/jobs', [ServiceJobController::class, 'index']); // Public/Artisan view
Route::get('/reviews/artisan/{artisanId}', [ReviewController::class, 'artisanReviews']);
Route::get('/users/{user}', [UserProfileController::class, 'show']);

Route::get('/storage/{path}', function ($path) {
    $fullPath = storage_path('app/public/' . $path);
    if (!file_exists($fullPath)) {
        abort(404);
    }
    return response()->file($fullPath);
})->where('path', '.*');

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    Route::patch('/user', [UserProfileController::class, 'update']);

    // Jobs
    Route::get('/my-jobs', [ServiceJobController::class, 'clientJobs']);
    Route::post('/jobs', [ServiceJobController::class, 'store']);
    Route::get('/jobs/{serviceJob}', [ServiceJobController::class, 'show']);
    Route::post('/jobs/{serviceJob}', [ServiceJobController::class, 'update']);
    Route::delete('/jobs/{serviceJob}', [ServiceJobController::class, 'destroy']);
    Route::patch('/jobs/{serviceJob}/status', [ServiceJobController::class, 'updateStatus']);

    // Applications
    Route::post('/jobs/{serviceJob}/apply', [ApplicationController::class, 'store']);
    Route::patch('/applications/{application}/status', [ApplicationController::class, 'updateStatus']);

    // Reviews
    Route::post('/jobs/{serviceJob}/reviews', [ReviewController::class, 'store']);

    // Chat
    Route::get('/messages/conversations', [ChatController::class, 'conversations']);
    Route::get('/messages/{user}', [ChatController::class, 'history']);
    Route::post('/messages/{user}', [ChatController::class, 'store']);

    // Admin API Routes
    Route::middleware('admin')->prefix('admin')->group(function () {
        Route::get('/dashboard', [\App\Http\Controllers\Admin\AdminDashboardController::class, 'metricsJson']);
        Route::get('/users', [\App\Http\Controllers\Admin\AdminUserController::class, 'index']);
        Route::post('/users/{user}/verify', [\App\Http\Controllers\Admin\AdminUserController::class, 'updateVerification']);
        Route::post('/users/{user}/toggle-active', [\App\Http\Controllers\Admin\AdminUserController::class, 'toggleActive']);
        Route::get('/jobs', [\App\Http\Controllers\Admin\AdminJobController::class, 'index']);
        Route::patch('/jobs/{serviceJob}/status', [\App\Http\Controllers\Admin\AdminJobController::class, 'updateStatus']);
    });
});

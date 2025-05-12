<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BoardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\WorkspaceController;
use App\Http\Controllers\GoogleAuthController;
use App\Http\Controllers\Auth\VerificationController;
use App\Http\Controllers\Auth\PasswordResetController;
use App\Http\Controllers\Auth\ForgotPasswordController;

Route::prefix('auth')->group(function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('logout', [AuthController::class, 'logout']);
        Route::get('profile', [ProfileController::class, 'getProfile']);
        Route::put('profile', [ProfileController::class, 'updateProfile']);
        Route::put('profile/password', [ProfileController::class, 'updatePassword']);
    });
});

// Password Reset Routes
Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLinkEmail'])
    ->name('password.email');

Route::post('/reset-password', [PasswordResetController::class, 'resetPassword'])
    ->name('password.update');

// Email Verification Routes
Route::middleware('auth:sanctum')->group(function () {
    // Send verification email
    Route::post('/email/verification-notification', [VerificationController::class, 'sendVerificationEmail'])
        ->name('verification.send');
    
    // Resend verification email
    Route::post('/email/resend', [VerificationController::class, 'resend'])
        ->name('verification.resend');
    
    // Verify email
    Route::get('/email/verify/{id}/{hash}', [VerificationController::class, 'verify'])
        ->middleware(['signed'])
        ->name('verification.verify');
});

Route::prefix('auth/google')->group(function () {
    Route::get('/', [GoogleAuthController::class, 'redirectToGoogle']);
    Route::get('/callback', [GoogleAuthController::class, 'handleGoogleCallback']);
});

// Protected Routes
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', [GoogleAuthController::class, 'profile']);
    Route::post('/logout', [GoogleAuthController::class, 'logout']);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/workspaces', [WorkspaceController::class, 'index']);
    Route::post('/workspaces', [WorkspaceController::class, 'store']);
    Route::get('/workspaces/{slug}', [WorkspaceController::class, 'show']);
    Route::put('/workspaces/{id}', [WorkspaceController::class, 'update']);
    Route::delete('/workspaces/{id}', [WorkspaceController::class, 'destroy']);
    Route::post('workspaces/{id}/invite', [WorkspaceController::class, 'inviteUser']);
    Route::patch('workspaces/{id}/accept-invitation', [WorkspaceController::class, 'acceptInvitation']);
    Route::delete('workspaces/{id}/users', [WorkspaceController::class, 'removeUser']);
}); 

Route::apiResource('boards', BoardController::class)->middleware('auth:sanctum');


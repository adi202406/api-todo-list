<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CardController;
use App\Http\Controllers\BoardController;
use App\Http\Controllers\LabelController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CardLabelController;
use App\Http\Controllers\ChecklistController;
use App\Http\Controllers\WorkspaceController;
use App\Http\Controllers\GoogleAuthController;
use App\Http\Controllers\BoardActionController;
use App\Http\Controllers\FcmTokenController;
use App\Http\Controllers\ChecklistItemController;
use App\Http\Controllers\CardAssignmentController;
use App\Http\Controllers\Auth\VerificationController;
use App\Http\Controllers\Auth\PasswordResetController;
use App\Http\Controllers\Auth\ForgotPasswordController;

// ================== AUTH ROUTES ==================
Route::prefix('auth')->group(function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('logout', [AuthController::class, 'logout']);
        Route::get('profile', [ProfileController::class, 'getProfile']);
        Route::put('profile', [ProfileController::class, 'updateProfile']);
        Route::put('profile/password', [ProfileController::class, 'updatePassword']);
    });

    // Google OAuth
    Route::prefix('google')->group(function () {
        Route::get('/', [GoogleAuthController::class, 'redirectToGoogle']);
        Route::get('/callback', [GoogleAuthController::class, 'handleGoogleCallback']);
    });
});

// ================== PASSWORD RESET ==================
Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
Route::post('/reset-password', [PasswordResetController::class, 'resetPassword'])->name('password.update');

// ================== EMAIL VERIFICATION ==================
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/email/verification-notification', [VerificationController::class, 'sendVerificationEmail'])->name('verification.send');
    Route::post('/email/resend', [VerificationController::class, 'resend'])->name('verification.resend');
    Route::get('/email/verify/{id}/{hash}', [VerificationController::class, 'verify'])->middleware(['signed'])->name('verification.verify');
});

// ================== GOOGLE PROFILE & LOGOUT ==================
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', [GoogleAuthController::class, 'profile']);
    Route::post('/logout', [GoogleAuthController::class, 'logout']);
});

// ================== WORKSPACE ==================
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

// ================== BOARDS ==================
Route::middleware('auth:sanctum')->group(function () {
    Route::prefix('workspaces/{workspace}')->scopeBindings()->group(function () {
        Route::apiResource('boards', BoardController::class);
        Route::patch('boards/{board}/reorder', [BoardActionController::class, 'reorder']);
        Route::patch('boards/{board}/toggle-favorite', [BoardActionController::class, 'toggleFavorite']);
    });
});

// ================== CARDS ==================
Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('cards', CardController::class);
    Route::get('boards/{board}/cards', [CardController::class, 'getByBoard']);
});

// ================== LABELS ==================
Route::apiResource('labels', LabelController::class)->middleware('auth:sanctum');

// ================== CARD-LABEL ==================
Route::prefix('cards')->middleware('auth:sanctum')->group(function () {
    Route::post('/attach-label', [CardLabelController::class, 'attach']);
    Route::post('/detach-label', [CardLabelController::class, 'detach']);
    Route::get('/{cardId}/labels', [CardLabelController::class, 'getCardLabels']);
});

// ================== CARD ASSIGNMENTS ==================
Route::middleware('auth:sanctum')->group(function () {
    Route::get('cards/{card}/assignees', [CardAssignmentController::class, 'index']);
    Route::post('cards/{card}/assignees', [CardAssignmentController::class, 'store']);
    Route::delete('cards/{card}/assignees/{user}', [CardAssignmentController::class, 'destroy']);
});

// ================== CHECKLISTS ==================
Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('checklists', ChecklistController::class);
    Route::put('checklists/{checklist}/position/{position}', [ChecklistController::class, 'updatePosition']);
});

// ================== CHECKLIST ITEMS ==================
Route::middleware('auth:sanctum')->group(function () {
    Route::get('checklists/{checklist}/items', [ChecklistItemController::class, 'index']);
    Route::post('checklists/{checklist}/items', [ChecklistItemController::class, 'store']);
    Route::get('checklists/{checklist}/items/{item}', [ChecklistItemController::class, 'show']);
    Route::put('checklists/{checklist}/items/{item}', [ChecklistItemController::class, 'update']);
    Route::delete('checklists/{checklist}/items/{item}', [ChecklistItemController::class, 'destroy']);
    Route::put('checklists/{checklist}/items', [ChecklistItemController::class, 'bulkUpdate']);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/fcm-token', [FcmTokenController::class, 'store']);
    Route::post('/send-test-notification', [FcmTokenController::class, 'sendTestNotification']);
    Route::delete('/fcm-token/{deviceToken}', [FcmTokenController::class, 'destroy']);
});
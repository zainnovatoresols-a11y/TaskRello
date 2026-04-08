<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\ConfirmablePasswordController;
use App\Http\Controllers\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Auth\EmailVerificationPromptController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\VerifyEmailController;
use Illuminate\Support\Facades\Route;


use App\Http\Controllers\BoardController;
use App\Http\Controllers\ListController;
use App\Http\Controllers\CardController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\AttachmentController;
use App\Http\Controllers\LabelController;

Route::middleware('guest')->group(function () {
    Route::get('register', [RegisteredUserController::class, 'create'])
        ->name('register');

    Route::post('register', [RegisteredUserController::class, 'store']);

    Route::get('login', [AuthenticatedSessionController::class, 'create'])
        ->name('login');

    Route::post('login', [AuthenticatedSessionController::class, 'store']);

    Route::get('forgot-password', [PasswordResetLinkController::class, 'create'])
        ->name('password.request');

    Route::post('forgot-password', [PasswordResetLinkController::class, 'store'])
        ->name('password.email');

    Route::get('reset-password/{token}', [NewPasswordController::class, 'create'])
        ->name('password.reset');

    Route::post('reset-password', [NewPasswordController::class, 'store'])
        ->name('password.store');
});

Route::middleware('auth')->group(function () {
    Route::get('verify-email', EmailVerificationPromptController::class)
        ->name('verification.notice');

    Route::get('verify-email/{id}/{hash}', VerifyEmailController::class)
        ->middleware(['signed', 'throttle:6,1'])
        ->name('verification.verify');

    Route::post('email/verification-notification', [EmailVerificationNotificationController::class, 'store'])
        ->middleware('throttle:6,1')
        ->name('verification.send');

    Route::get('confirm-password', [ConfirmablePasswordController::class, 'show'])
        ->name('password.confirm');

    Route::post('confirm-password', [ConfirmablePasswordController::class, 'store']);

    Route::put('password', [PasswordController::class, 'update'])->name('password.update');

    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])
        ->name('logout');
});


// Public landing page
Route::get('/', fn() => redirect()->route('boards.index'));

// ── All routes require authentication ──────────────────
Route::middleware(['auth', 'verified'])->group(function () {

    // ── Board Routes ────────────────────────────────────
    Route::resource('boards', BoardController::class);

    // Extra board-level actions
    Route::post('/boards/{board}/members', [BoardController::class, 'addMember'])
        ->name('boards.members.add');

    // ── List Routes (nested under board) ────────────────
    Route::post('/boards/{board}/lists', [ListController::class, 'store'])
        ->name('lists.store');

    Route::put('/boards/{board}/lists/{list}', [ListController::class, 'update'])
        ->name('lists.update');

    Route::delete('/boards/{board}/lists/{list}', [ListController::class, 'destroy'])
        ->name('lists.destroy');

    Route::post('/boards/{board}/lists/reorder', [ListController::class, 'reorder'])
        ->name('lists.reorder');

    // ── Card Routes ─────────────────────────────────────
    Route::post('/lists/{list}/cards', [CardController::class, 'store'])
        ->name('cards.store');

    Route::get('/cards/{card}', [CardController::class, 'show'])
        ->name('cards.show');

    Route::put('/cards/{card}', [CardController::class, 'update'])
        ->name('cards.update');

    Route::delete('/cards/{card}', [CardController::class, 'destroy'])
        ->name('cards.destroy');

    Route::post('/cards/{card}/move', [CardController::class, 'move'])
        ->name('cards.move');

    Route::post('/cards/{card}/assign', [CardController::class, 'assign'])
        ->name('cards.assign');

    // ── Comment Routes ──────────────────────────────────
    Route::post('/cards/{card}/comments', [CommentController::class, 'store'])
        ->name('comments.store');

    Route::put('/comments/{comment}', [CommentController::class, 'update'])
        ->name('comments.update');

    Route::delete('/comments/{comment}', [CommentController::class, 'destroy'])
        ->name('comments.destroy');

    // ── Attachment Routes ───────────────────────────────
    Route::post('/cards/{card}/attachments', [AttachmentController::class, 'store'])
        ->name('attachments.store');

    Route::delete('/attachments/{attachment}', [AttachmentController::class, 'destroy'])
        ->name('attachments.destroy');

    // ── Label Routes ────────────────────────────────────
    Route::post('/boards/{board}/labels', [LabelController::class, 'store'])
        ->name('labels.store');

    Route::post('/cards/{card}/labels/{label}', [LabelController::class, 'attach'])
        ->name('labels.attach');

    Route::delete('/cards/{card}/labels/{label}', [LabelController::class, 'detach'])
        ->name('labels.detach');
});



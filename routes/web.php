<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\BoardController;
use App\Http\Controllers\ListController;
use App\Http\Controllers\CardController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\AttachmentController;
use App\Http\Controllers\LabelController;

// Public landing page
Route::get('/', fn() => redirect()->route('boards.index'));

// ── Profile Routes ──────────────────────────────────────
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// ── All routes require authentication ──────────────────
Route::middleware(['auth', 'verified'])->group(function () {

    // ── Board index and create — no membership needed ───
    Route::get('/boards', [BoardController::class, 'index'])->name('boards.index');
    Route::get('/boards/create', [BoardController::class, 'create'])->name('boards.create');
    Route::post('/boards', [BoardController::class, 'store'])->name('boards.store');

    // ── Board routes that require membership ────────────
    Route::middleware('board.member')->group(function () {
        Route::get('/boards/{board}', [BoardController::class, 'show'])->name('boards.show');
        Route::get('/boards/{board}/edit', [BoardController::class, 'edit'])->name('boards.edit');
        Route::put('/boards/{board}', [BoardController::class, 'update'])->name('boards.update');
        Route::delete('/boards/{board}', [BoardController::class, 'destroy'])->name('boards.destroy');

        Route::post('/boards/{board}/members', [BoardController::class, 'addMember'])->name('boards.members.add');
        Route::delete('/boards/{board}/members/{user}', [BoardController::class, 'removeMember'])->name('boards.members.remove');

        Route::post('/boards/{board}/lists', [ListController::class, 'store'])->name('lists.store');
        Route::put('/boards/{board}/lists/{list}', [ListController::class, 'update'])->name('lists.update');
        Route::delete('/boards/{board}/lists/{list}', [ListController::class, 'destroy'])->name('lists.destroy');
        Route::post('/boards/{board}/lists/reorder', [ListController::class, 'reorder'])->name('lists.reorder');

        Route::post('/boards/{board}/labels', [LabelController::class, 'store'])->name('labels.store');
    });

    // ── Card Routes — membership checked in controller ──
    Route::post('/lists/{list}/cards', [CardController::class, 'store'])->name('cards.store');
    Route::get('/cards/{card}', [CardController::class, 'show'])->name('cards.show');
    Route::put('/cards/{card}', [CardController::class, 'update'])->name('cards.update');
    Route::delete('/cards/{card}', [CardController::class, 'destroy'])->name('cards.destroy');
    Route::post('/cards/{card}/move', [CardController::class, 'move'])->name('cards.move');
    Route::post('/cards/{card}/assign', [CardController::class, 'assign'])->name('cards.assign');

    // ── Comment Routes ──────────────────────────────────
    Route::post('/cards/{card}/comments', [CommentController::class, 'store'])->name('comments.store');
    Route::put('/comments/{comment}', [CommentController::class, 'update'])->name('comments.update');
    Route::delete('/comments/{comment}', [CommentController::class, 'destroy'])->name('comments.destroy');

    // ── Attachment Routes ───────────────────────────────
    Route::post('/cards/{card}/attachments', [AttachmentController::class, 'store'])->name('attachments.store');
    Route::delete('/attachments/{attachment}', [AttachmentController::class, 'destroy'])->name('attachments.destroy');

    // ── Label Routes ────────────────────────────────────
    Route::post('/cards/{card}/labels/{label}', [LabelController::class, 'attach'])->name('labels.attach');
    Route::delete('/cards/{card}/labels/{label}', [LabelController::class, 'detach'])->name('labels.detach');
});

require __DIR__ . '/auth.php';

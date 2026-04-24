<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\BoardController;
use App\Http\Controllers\ListController;
use App\Http\Controllers\CardController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\AttachmentController;
use App\Http\Controllers\LabelController;
use App\Http\Controllers\NotificationController;


Route::get('/', fn() => redirect()->route('boards.index'));

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});


Route::middleware(['auth', 'verified'])->group(function () {

    Route::get('/boards', [BoardController::class, 'index'])->name('boards.index');
    Route::get('/boards/create', [BoardController::class, 'create'])->name('boards.create');
    Route::post('/boards', [BoardController::class, 'store'])->name('boards.store');

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
    });

    Route::post('/lists/{list}/cards', [CardController::class, 'store'])->name('cards.store');
    Route::get('/cards/{card}', [CardController::class, 'show'])->name('cards.show');
    Route::put('/cards/{card}', [CardController::class, 'update'])->name('cards.update');
    Route::delete('/cards/{card}', [CardController::class, 'destroy'])->name('cards.destroy');
    Route::post('/cards/{card}/move', [CardController::class, 'move'])->name('cards.move');
    Route::post('/cards/{card}/assign', [CardController::class, 'assign'])->name('cards.assign');
    Route::post('/cards/{card}/complete', [CardController::class, 'toggleComplete'])->name('cards.complete');

    Route::post('/cards/{card}/comments', [CommentController::class, 'store'])->name('comments.store');
    Route::put('/comments/{comment}', [CommentController::class, 'update'])->name('comments.update');
    Route::delete('/comments/{comment}', [CommentController::class, 'destroy'])->name('comments.destroy');


    Route::post('/cards/{card}/attachments', [AttachmentController::class, 'store'])->name('attachments.store');
    Route::delete('/attachments/{attachment}', [AttachmentController::class, 'destroy'])->name('attachments.destroy');

    Route::post('/boards/{board}/labels', [LabelController::class, 'store'])->name('labels.store');
    Route::put('/boards/{board}/labels/{label}', [LabelController::class, 'update'])->name('labels.update');
    Route::delete('/boards/{board}/labels/{label}', [LabelController::class, 'destroy'])->name('labels.destroy');
    Route::post('/cards/{card}/labels/{label}', [LabelController::class, 'attach'])->name('labels.attach');
    Route::delete('/cards/{card}/labels/{label}', [LabelController::class, 'detach'])->name('labels.detach');

    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/{notification}/read', [NotificationController::class, 'markRead'])->name('notifications.read');
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllRead'])->name('notifications.read-all');

    Route::post('/cards/{card}/cover-image',[CardController::class, 'uploadCoverImage'])->name('cards.cover-image.upload');
    Route::delete('/cards/{card}/cover-image',[CardController::class, 'removeCoverImage'])->name('cards.cover-image.remove');

    Route::post('/cards/{card}/description-images',[CardController::class, 'uploadDescriptionImage'])->name('cards.description-images.upload');
    Route::delete('/cards/{card}/description-images/{image}',[CardController::class, 'removeDescriptionImage'])->name('cards.description-images.remove');
});

require __DIR__ . '/auth.php';

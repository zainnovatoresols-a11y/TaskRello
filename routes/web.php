<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\BoardController;
use App\Http\Controllers\ListController;
use App\Http\Controllers\CardController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\AttachmentController;
use App\Http\Controllers\LabelController;

// Route::get('/', function () {
//     return view('welcome');
// });

// Route::get('/dashboard', function () {
//     return view('dashboard');
// })->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
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

    Route::delete('/boards/{board}/members/{user}', [BoardController::class, 'removeMember'])
        ->name('boards.members.remove');
});



require __DIR__.'/auth.php';

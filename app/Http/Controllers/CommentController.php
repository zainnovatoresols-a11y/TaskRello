<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCommentRequest;
use App\Models\Card;
use App\Models\Comment;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    // POST /cards/{card}/comments
    public function store(StoreCommentRequest $request, Card $card)
    {
        $board = $card->list->board;

        if (!$board->isMember($request->user())) {
            abort(403, 'You must be a board member to comment.');
        }

        $comment = $card->comments()->create([
            'user_id' => $request->user()->id,
            'body'    => $request->validated()['body'],
        ]);

        ActivityLog::log(
            $request->user(),
            'added_comment',
            "{$request->user()->name} commented on '{$card->title}'",
            $board->id,
            $card->id
        );

        // Return the new comment with author so JS renders it
        return response()->json([
            'success' => true,
            'comment' => $comment->load('author'),
        ], 201);
    }

    // PUT /comments/{comment}
    public function update(Request $request, Comment $comment)
    {
        $this->authorize('update', $comment);

        $request->validate([
            'body' => 'required|string|min:1|max:5000',
        ]);

        $comment->update(['body' => trim($request->body)]);

        return response()->json([
            'success' => true,
            'comment' => $comment->fresh()->load('author'),
        ]);
    }

    // DELETE /comments/{comment}
    public function destroy(Request $request, Comment $comment)
    {
        $this->authorize('delete', $comment);

        $comment->delete();

        return response()->json(['success' => true]);
    }
}

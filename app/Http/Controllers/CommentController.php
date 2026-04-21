<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCommentRequest;
use App\Models\Card;
use App\Models\Comment;
use App\Services\CommentService;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class CommentController extends Controller
{
    use AuthorizesRequests;

    public function __construct(
        private CommentService $commentService
    ) {}

    public function index(Request $request, Card $card)
    {
        $board = $card->list->board;

        if (!$board->isMember($request->user())) {
            abort(403, 'You must be a board member to view comments.');
        }

        $comments = $card->comments()
            ->with('author')
            ->orderBy('created_at')
            ->get();

        return response()->json([
            'success' => true,
            'data'    => $comments->map(fn($c) => [
                'id'         => $c->id,
                'body'       => $c->body,
                'created_at' => $c->created_at->toDateTimeString(),
                'time_ago'   => $c->created_at->diffForHumans(),
                'author'     => [
                    'id'   => $c->author->id,
                    'name' => $c->author->name,
                ],
            ]),
        ]);
    }

    public function store(StoreCommentRequest $request, Card $card)
    {
        $board = $card->list->board;

        if (!$board->isMember($request->user())) {
            abort(403, 'You must be a board member to comment.');
        }

        $comment = $this->commentService->create($card, $request->validated()['body'], $request->user());

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'comment' => $comment], 201);
        }

        return redirect()->route('boards.show', $board)->with('success', 'Comment added.');
    }

    public function destroy(Request $request, Comment $comment)
    {
        $this->authorize('delete', $comment);

        $board = $comment->card->list->board;

        $this->commentService->delete($comment);

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Comment deleted successfully']);
        }

        return redirect()->route('boards.show', $board)->with('success', 'Comment deleted.');
    }

}

<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBoardRequest;
use App\Http\Requests\UpdateBoardRequest;
use App\Models\Board;
use App\Models\User;
use App\Services\BoardService;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class BoardController extends Controller
{
    use AuthorizesRequests;

    public function __construct(
        private BoardService $boardService
    ) {}

    public function index(Request $request)
    {
        $boards = $this->boardService->getUserBoards($request->user());

        if ($request->wantsJson()) {
            return response()->json([
                'status'=> 'success',
                'data'=> $boards,
            ], 200);
        }

        return view('boards.index', compact('boards'));
    }

    public function create()
    {
        return view('boards.create');
    }

    public function store(StoreBoardRequest $request)
    {
        $board = $this->boardService->create($request->user(), $request->validated());

        if ($request->wantsJson()) {
            return response()->json([
                'status'=> 'created',
                'message'=> 'Board created successfully!',
                'data'=> $board,
            ], 201);
        }

        return redirect()
            ->route('boards.show', $board)
            ->with('success', 'Board created successfully!');
    }

    public function show(Request $request, Board $board)
    {
        $this->authorize('view', $board);
        $board = $this->boardService->loadBoardWithRelations($board);

        if ($request->wantsJson()) {
            return response()->json([
                'success'=> true,
                'data'=> [
                    'id'=> $board->id,
                    'name'=> $board->name,
                    'description'=> $board->description,
                    'background_color'=> $board->background_color,
                    'is_archived'=> $board->is_archived,
                    'created_at'=> $board->created_at->toDateTimeString(),
                    'owner'=> [
                        'id'=> $board->owner->id,
                        'name'=> $board->owner->name,
                    ],
                    'members'=> $board->members->map(fn($m) => [
                        'id'=> $m->id,
                        'name'=> $m->name,
                        'email'=> $m->email,
                        'role'=> $m->pivot->role,
                    ]),
                    'labels'=> $board->labels->map(fn($l) => [
                        'id'=> $l->id,
                        'name'=> $l->name,
                        'color'=> $l->color,
                    ]),
                    'lists'=> $board->lists->map(fn($list) => [
                        'id'=> $list->id,
                        'name'=> $list->name,
                        'position'=> $list->position,
                        'cards'=> $list->cards->map(fn($card) => [
                            'id'=> $card->id,
                            'title'=> $card->title,
                            'description'=> $card->description,
                            'position'=> $card->position,
                            'due_date'=> $card->due_date?->toDateString(),
                            'cover_color'=> $card->cover_color,
                            'is_completed'=> $card->is_completed,
                            'is_overdue'=> $card->isOverdue(),
                            'is_due_soon'=> $card->isDueSoon(),
                            'assignees'=> $card->assignees->map(fn($u) => [
                                'id'=> $u->id,
                                'name'=> $u->name,
                            ]),
                            'labels'=> $card->labels->map(fn($l) => [
                                'id'=> $l->id,
                                'name'=> $l->name,
                                'color'=> $l->color,
                            ]),
                        ]),
                    ]),
                ],
            ]);
        }

        return view('boards.show', compact('board'));
    }

    public function edit(Board $board)
    {
        $this->authorize('update', $board);
        return view('boards.edit', compact('board'));
    }

    public function update(UpdateBoardRequest $request, Board $board)
    {
        $this->boardService->update($board, $request->validated());

        if ($request->wantsJson()) {
            return response()->json([
                'status'=> 'success',
                'message'=> 'Board updated.',
                'data'=> $board->fresh(),
            ], 200);
        }

        return redirect()
            ->route('boards.show', $board)
            ->with('success', 'Board updated.');
    }

    public function destroy(Request $request, Board $board)
    {
        $this->authorize('delete', $board);
        $this->boardService->delete($board);

        if ($request->wantsJson()) {
            return response()->json([
                'status'=> 'success',
                'message'=> 'Board deleted.',
            ], 200);
        }

        return redirect()
            ->route('boards.index')
            ->with('success', 'Board deleted.');
    }

    public function addMember(Request $request, Board $board)
    {
        $this->authorize('manageMember', $board);

        $request->validate([
            'email' => 'required|email|exists:users,email',
        ], [
            'email.exists' => 'No user found with that email address.',
        ]);

        $user = User::where('email', $request->email)->first();

        $response = $this->boardService->addMember($board, $request->user(), $user, $request);

        if ($response) {
            return $response;
        }

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "{$user->name} has been added to the board.",
                'data' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => 'member',
                ],
            ], 201);
        }

        return redirect()
            ->route('boards.edit', $board)
            ->with('success', "{$user->name} has been added to the board.");
    }

    public function removeMember(Request $request, Board $board, User $user)
    {
        $this->authorize('manageMember', $board);

        $response = $this->boardService->removeMember($board, $request->user(), $user, $request);

        if ($response) {
            return $response;
        }

        if ($request->wantsJson()) {
            return response()->json([
                'status' => 'success',
                'message' => "{$user->name} has been removed from the board.",
            ], 200);
        }

        return redirect()
            ->route('boards.edit', $board)
            ->with('success', "{$user->name} has been removed from the board.");
    }
}

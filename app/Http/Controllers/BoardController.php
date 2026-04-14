<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBoardRequest;
use App\Http\Requests\UpdateBoardRequest;
use App\Models\ActivityLog;
use App\Models\Board;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class BoardController extends Controller
{
    use AuthorizesRequests;
    public function index(Request $request)
    {
        $boards = $request->user()
            ->boards()
            ->with('owner')
            ->withCount('members')
            ->latest()
            ->get();

        return view('boards.index', compact('boards'));
    }

    public function create()
    {
        return view('boards.create');
    }

    public function store(StoreBoardRequest $request)
    {
        $board = Board::create([
            'user_id'          => $request->user()->id,
            'name'             => $request->validated()['name'],
            'description'      => $request->validated()['description'] ?? null,
            'background_color' => $request->validated()['background_color'] ?? '#0052CC',
        ]);

        $board->members()->attach($request->user()->id, ['role' => 'owner']);

        ActivityLog::log(
            $request->user(),
            'created_board',
            "{$request->user()->name} created board '{$board->name}'",
            $board->id
        );

        return redirect()
            ->route('boards.show', $board)
            ->with('success', 'Board created successfully!');
    }

    public function show(Board $board)
    {
        $this->authorize('view', $board);

        $board->load([
            'lists.cards.assignees',
            'lists.cards.labels',
            'members',
            'labels',
        ]);

        return view('boards.show', compact('board'));
    }

    public function edit(Board $board)
    {
        $this->authorize('update', $board);

        return view('boards.edit', compact('board'));
    }

    public function update(UpdateBoardRequest $request, Board $board)
    {
        $board->update($request->validated());

        return redirect()
            ->route('boards.show', $board)
            ->with('success', 'Board updated.');
    }

    public function destroy(Request $request, Board $board)
    {
        $this->authorize('delete', $board);

        $board->delete();

        return redirect()
            ->route('boards.index')
            ->with('success', 'Board deleted.');
    }

    public function addMember(Request $request, Board $board)
    {
        $this->authorize('addMember', $board);

        $request->validate([
            'email' => 'required|email|exists:users,email',
        ], [
            'email.exists' => 'No user found with that email address.',
        ]);

        $user = User::where('email', $request->email)->first();

        if ($board->isMember($user)) {
            return redirect()
                ->route('boards.edit', $board)
                ->with('error', $user->name . ' is already a member of this board.');
        }

        $board->members()->attach($user->id, ['role' => 'member']);

        ActivityLog::log(
            $request->user(),
            'added_member',
            $request->user()->name . ' added ' . $user->name . ' to the board',
            $board->id
        );

        return redirect()
            ->route('boards.edit', $board)
            ->with('success', $user->name . ' has been added to the board.');
    }

    public function removeMember(Request $request, Board $board, User $user)
    {
        $this->authorize('addMember', $board);

        if ($board->user_id === $user->id) {
            return redirect()
                ->route('boards.edit', $board)
                ->with('error', 'You cannot remove the board owner.');
        }

        if ($request->user()->id === $user->id) {
            return redirect()
                ->route('boards.edit', $board)
                ->with('error', 'You cannot remove yourself from the board.');
        }

        $board->members()->detach($user->id);

        ActivityLog::log(
            $request->user(),
            'removed_member',
            $request->user()->name . ' removed ' . $user->name . ' from the board',
            $board->id
        );

        return redirect()
            ->route('boards.edit', $board)
            ->with('success', $user->name . ' has been removed from the board.');
    }
}

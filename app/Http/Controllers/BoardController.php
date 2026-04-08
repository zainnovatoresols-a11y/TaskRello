<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBoardRequest;
use App\Http\Requests\UpdateBoardRequest;
use App\Models\Board;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class BoardController extends Controller
{
    // GET /boards — dashboard listing
    public function index(Request $request)
    {
        // Get all boards the user is a member of
        $boards = $request->user()
            ->boards()
            ->with('owner')
            ->withCount('members')
            ->latest()
            ->get();

        return view('boards.index', compact('boards'));
    }

    // GET /boards/create
    public function create()
    {
        return view('boards.create');
    }

    // POST /boards
    public function store(StoreBoardRequest $request)
    {
        $board = Board::create([
            'user_id'          => $request->user()->id,
            'name'             => $request->validated()['name'],
            'description'      => $request->validated()['description'] ?? null,
            'background_color' => $request->validated()['background_color'] ?? '#0052CC',
        ]);

        // Add creator as owner in pivot table
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

    // GET /boards/{board} — the Kanban view
    public function show(Board $board)
    {
        $this->authorize('view', $board);

        // Eager-load everything the board view needs
        $board->load([
            'lists.cards.assignees',
            'lists.cards.labels',
            'members',
            'labels',
        ]);

        return view('boards.show', compact('board'));
    }

    // GET /boards/{board}/edit
    public function edit(Board $board)
    {
        $this->authorize('update', $board);

        return view('boards.edit', compact('board'));
    }

    // PUT /boards/{board}
    public function update(UpdateBoardRequest $request, Board $board)
    {
        $board->update($request->validated());

        return redirect()
            ->route('boards.show', $board)
            ->with('success', 'Board updated.');
    }

    // DELETE /boards/{board}
    public function destroy(Request $request, Board $board)
    {
        $this->authorize('delete', $board);

        $board->delete();

        return redirect()
            ->route('boards.index')
            ->with('success', 'Board deleted.');
    }
}

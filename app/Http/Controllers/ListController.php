<?php

namespace App\Http\Controllers;

use App\Models\Board;
use App\Models\BoardList;
use App\Services\ListService;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class ListController extends Controller
{

    use AuthorizesRequests;

    public function __construct(
        private ListService $listService
    ) {}

    public function index(Board $board)
    {
        $this->authorize('view', $board);

        $lists = $board->lists()
            ->where('is_archived', false)
            ->orderBy('position')
            ->withCount('cards')
            ->get();

        return response()->json([
            'success'=> true,
            'data'=> $lists->map(fn($list) => $this->formatList($list)),
        ]);
    }

    private function formatList(BoardList $list): array
    {
        return [
            'id'=> $list->id,
            'board_id'=> $list->board_id,
            'name'=> $list->name,
            'position'=> $list->position,
            'is_archived'=> $list->is_archived,
            'cards_count'=> $list->cards_count ?? $list->cards()->count(),
            'created_at'=> $list->created_at->toDateTimeString(),
        ];
    }

    public function store(Request $request, Board $board)
    {
        $this->authorize('update', $board);

        $request->validate([
            'name'=> 'required|string|max:255',
        ]);

        $list = $this->listService->create($board, $request->name, $request->user());

        if ($request->wantsJson()) {
            return response()->json([
                'success'=> true,
                'message'=> 'List created successfully',
                'list'=> $list,
            ]);
        }

        return redirect()
            ->route('boards.show', $board)
            ->with('success', "List '{$list->name}' created.");
    }

    public function update(Request $request, Board $board, BoardList $list)
    {
        $this->authorize('update', $board);

        $request->validate([
            'name'=> 'sometimes|required|string|max:255',
            'is_archived'=> 'sometimes|boolean',
        ]);

        $list = $this->listService->update($list, $request->only(['name', 'is_archived']));

        if ($request->wantsJson()) {
            return response()->json([
                'success'=> true,
                'list'=> $list,
            ]);
        }

        return redirect()
            ->route('boards.show', $board)
            ->with('success', "List updated.");
    }

    public function destroy(Request $request, Board $board, BoardList $list)
    {
        $this->authorize('update', $board);

        $this->listService->delete($list);

        if ($request->wantsJson()) {
            return response()->json([
                'success'=> true,
                'message'=> 'List deleted.',
            ]);
        }

        return redirect()
            ->route('boards.show', $board)
            ->with('success', 'List deleted.');
    }

    public function reorder(Request $request, Board $board)
    {
        $this->authorize('update', $board);

        $request->validate([
            'lists'=> 'required|array',
            'lists.*.id'=> 'required|integer|exists:lists,id',
            'lists.*.position'=> 'required|integer|min:0',
        ]);

        $this->listService->reorder($board, $request->lists);

        if ($request->wantsJson()) {
            return response()->json([
                'success'=> true,
                'message'=> 'Lists reordered.',
            ]);
        }

        return redirect()
            ->route('boards.show', $board)
            ->with('success', 'Lists reordered.');
    }

}
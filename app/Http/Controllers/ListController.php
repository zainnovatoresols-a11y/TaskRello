<?php

namespace App\Http\Controllers;

use App\Models\Board;
use App\Models\BoardList;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class ListController extends Controller
{
    use AuthorizesRequests;
    // POST /boards/{board}/lists
    public function store(Request $request, Board $board)
    {
        $this->authorize('update', $board);

        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        // Position = count of existing lists (append at end)
        $position = $board->lists()->count();

        $list = $board->lists()->create([
            'name'     => $request->name,
            'position' => $position,
        ]);

        ActivityLog::log(
            $request->user(),
            'created_list',
            "{$request->user()->name} added list '{$list->name}'",
            $board->id
        );

        // Return JSON — JS will inject the new list column into DOM
        return response()->json([
            'success' => true,
            'list'    => $list,
        ]);
    }

    // PUT /boards/{board}/lists/{list}
    public function update(Request $request, Board $board, BoardList $list)
    {
        $this->authorize('update', $board);

        $request->validate([
            'name'        => 'sometimes|required|string|max:255',
            'is_archived' => 'sometimes|boolean',
        ]);

        $list->update($request->only(['name', 'is_archived']));

        return response()->json(['success' => true, 'list' => $list]);
    }

    // DELETE /boards/{board}/lists/{list}
    public function destroy(Request $request, Board $board, BoardList $list)
    {
        $this->authorize('update', $board);

        $list->delete(); // cascade deletes cards too

        return response()->json(['success' => true]);
    }

    // POST /boards/{board}/lists/reorder
    // Body: { "lists": [{"id":3,"position":0},{"id":1,"position":1}] }
    public function reorder(Request $request, Board $board)
    {
        $this->authorize('update', $board);

        $request->validate([
            'lists'          => 'required|array',
            'lists.*.id'     => 'required|integer|exists:lists,id',
            'lists.*.position' => 'required|integer|min:0',
        ]);

        foreach ($request->lists as $item) {
            BoardList::where('id', $item['id'])
                ->where('board_id', $board->id)
                ->update(['position' => $item['position']]);
        }

        return response()->json(['success' => true]);
    }
}

<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCardRequest;
use App\Http\Requests\UpdateCardRequest;
use App\Models\BoardList;
use App\Models\Card;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class CardController extends Controller
{
    // POST /lists/{list}/cards
    public function store(StoreCardRequest $request, BoardList $list)
    {
        $board = $list->board;
        $this->authorize('create', [Card::class, $board]);

        $position = $list->cards()->count();

        $card = $list->cards()->create([
            'user_id'  => $request->user()->id,
            'title'    => $request->validated()['title'],
            'position' => $position,
        ]);

        ActivityLog::log(
            $request->user(),
            'created_card',
            "{$request->user()->name} created card '{$card->title}'",
            $board->id,
            $card->id
        );

        return response()->json([
            'success' => true,
            'card'    => $card->load('assignees', 'labels'),
        ]);
    }

    // GET /cards/{card} — returns HTML partial for the modal
    public function show(Card $card)
    {
        $this->authorize('view', $card);

        $card->load([
            'creator',
            'assignees',
            'labels',
            'comments.author',
            'attachments.uploader',
            'activityLogs.user',
            'list.board.members',
        ]);

        // Returns a Blade partial that JS loads into the modal
        return view('cards.show', compact('card'));
    }

    // PUT /cards/{card}
    public function update(UpdateCardRequest $request, Card $card)
    {
        $card->update($request->validated());

        ActivityLog::log(
            $request->user(),
            'updated_card',
            "{$request->user()->name} updated card '{$card->title}'",
            $card->list->board_id,
            $card->id
        );

        return response()->json([
            'success' => true,
            'card'    => $card->fresh()->load('assignees', 'labels'),
        ]);
    }

    // DELETE /cards/{card}
    public function destroy(Request $request, Card $card)
    {
        $this->authorize('delete', $card);

        $boardId = $card->list->board_id;
        $title   = $card->title;

        $card->delete();

        ActivityLog::log(
            $request->user(),
            'deleted_card',
            "{$request->user()->name} deleted card '{$title}'",
            $boardId
        );

        return response()->json(['success' => true]);
    }

    // POST /cards/{card}/move — drag and drop handler
    // Body: { "list_id": 5, "position": 2 }
    public function move(Request $request, Card $card)
    {
        $this->authorize('move', $card);

        $request->validate([
            'list_id'  => 'required|exists:lists,id',
            'position' => 'required|integer|min:0',
        ]);

        $oldList   = $card->list;
        $newListId = $request->list_id;
        $newPos    = $request->position;

        // Update the card itself
        $card->update([
            'list_id'  => $newListId,
            'position' => $newPos,
        ]);

        // Reorder siblings in the new list (push others down)
        Card::where('list_id', $newListId)
            ->where('id', '!=', $card->id)
            ->where('position', '>=', $newPos)
            ->increment('position');

        $newList = BoardList::find($newListId);

        ActivityLog::log(
            $request->user(),
            'moved_card',
            "{$request->user()->name} moved '{$card->title}' to '{$newList->name}'",
            $card->list->board_id,
            $card->id
        );

        return response()->json(['success' => true]);
    }

    // POST /cards/{card}/assign
    // Body: { "user_id": 3 } — toggles assignment on/off
    public function assign(Request $request, Card $card)
    {
        $this->authorize('assign', $card);

        $request->validate(['user_id' => 'required|exists:users,id']);

        // Toggle: if already assigned, remove; otherwise add
        $card->assignees()->toggle($request->user_id);

        return response()->json([
            'success'   => true,
            'assignees' => $card->fresh()->assignees,
        ]);
    }
}

<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCardRequest;
use App\Http\Requests\UpdateCardRequest;
use App\Models\BoardList;
use App\Models\Card;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class CardController extends Controller
{
    use AuthorizesRequests;
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

       
        return view('cards.show', compact('card'));
    }

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

   public function move(Request $request, Card $card)
{
    $this->authorize('move', $card);

    $request->validate([
        'list_id'  => 'required|exists:lists,id',
        'position' => 'required|integer|min:0',
    ]);

    $oldListId = $card->list_id;
    $oldPos    = $card->position;
    $newListId = (int) $request->list_id;
    $newPos    = (int) $request->position;

    if ($oldListId === $newListId) {
       
        if ($oldPos === $newPos) {
            return response()->json(['success' => true]);
        }

        if ($newPos > $oldPos) {
          
            Card::where('list_id', $newListId)
                ->where('id', '!=', $card->id)
                ->whereBetween('position', [$oldPos + 1, $newPos])
                ->decrement('position');
        } else {
            Card::where('list_id', $newListId)
                ->where('id', '!=', $card->id)
                ->whereBetween('position', [$newPos, $oldPos - 1])
                ->increment('position');
        }

        $card->update(['position' => $newPos]);

    } else {
    
        Card::where('list_id', $oldListId)
            ->where('position', '>', $oldPos)
            ->decrement('position');

        // Make space in the new list
        Card::where('list_id', $newListId)
            ->where('position', '>=', $newPos)
            ->increment('position');

        $card->update([
            'list_id'  => $newListId,
            'position' => $newPos,
        ]);
    }

    $newList = BoardList::find($newListId);

    ActivityLog::log(
        $request->user(),
        'moved_card',
        "{$request->user()->name} moved '{$card->title}' to '{$newList->name}'",
        $newList->board_id,
        $card->id
    );

    return response()->json(['success' => true]);
}

    public function assign(Request $request, Card $card)
    {
        $this->authorize('assign', $card);

        $request->validate(['user_id' => 'required|exists:users,id']);

        $card->assignees()->toggle($request->user_id);

        return response()->json([
            'success'   => true,
            'assignees' => $card->fresh()->assignees,
        ]);
    }

    public function toggleComplete(Request $request, Card $card)
    {
        $this->authorize('update', $card);

        $card->update([
            'is_completed' => ! $card->is_completed,
        ]);

        ActivityLog::log(
            $request->user(),
            $card->is_completed ? 'completed_card' : 'reopened_card',
            $request->user()->name
                . ($card->is_completed ? ' completed ' : ' reopened ')
                . "'{$card->title}'",
            $card->list->board_id,
            $card->id
        );

        return response()->json([
            'success'      => true,
            'is_completed' => $card->is_completed,
        ]);
    }
}

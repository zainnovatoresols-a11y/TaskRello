<?php

namespace App\Http\Controllers;

use App\Models\Board;
use App\Models\Card;
use App\Models\Label;
use Illuminate\Http\Request;

class LabelController extends Controller
{
    // POST /boards/{board}/labels
    public function store(Request $request, Board $board)
    {
        $this->authorize('update', $board);

        $request->validate([
            'name'  => 'required|string|max:100',
            'color' => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',
        ]);

        $label = $board->labels()->create([
            'name'  => $request->name,
            'color' => $request->color,
        ]);

        return response()->json([
            'success' => true,
            'label'   => $label,
        ], 201);
    }

    // POST /cards/{card}/labels/{label}
    public function attach(Request $request, Card $card, Label $label)
    {
        $board = $card->list->board;
        $this->authorize('update', $card);

        // Ensure label belongs to the same board
        if ($label->board_id !== $board->id) {
            return response()->json(['error' => 'Label does not belong to this board.'], 422);
        }

        // syncWithoutDetaching = add if not already attached
        $card->labels()->syncWithoutDetaching([$label->id]);

        return response()->json([
            'success' => true,
            'labels'  => $card->fresh()->labels,
        ]);
    }

    // DELETE /cards/{card}/labels/{label}
    public function detach(Request $request, Card $card, Label $label)
    {
        $this->authorize('update', $card);

        $card->labels()->detach($label->id);

        return response()->json([
            'success' => true,
            'labels'  => $card->fresh()->labels,
        ]);
    }
}

<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Board;
use App\Models\Card;
use App\Models\Label;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class LabelController extends Controller
{
    use AuthorizesRequests;
    public function store(Request $request, Board $board)
    {
        $this->authorize('update', $board);

        $request->validate([
            'name'  => 'required|string|max:100',
            'color' => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/|',
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

    public function attach(Request $request, Card $card, Label $label)
    {
        $board = $card->list->board;
        $this->authorize('update', $card);

        if ($label->board_id !== $board->id) {
            return response()->json(['error' => 'Label does not belong to this board.'], 422);
        }

        $card->labels()->syncWithoutDetaching([$label->id]);

        return response()->json([
            'success' => true,
            'labels'  => $card->fresh()->labels,
        ]);
    }

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

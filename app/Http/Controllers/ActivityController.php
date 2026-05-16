<?php

namespace App\Http\Controllers;

use App\Models\Board;
use App\Models\Card;
use App\Services\ActivityService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;

class ActivityController extends Controller
{
    use AuthorizesRequests;

    public function __construct(private readonly ActivityService $activityService) {}

    public function card(Request $request, Card $card)
    {
        $this->authorize('view', $card);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'data'    => $this->activityService->getCardLogs($card),
            ]);
        }

        return redirect()->route('boards.show', $card->list->board);
    }

    public function board(Request $request, Board $board)
    {
        $this->authorize('view', $board);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'data'    => $this->activityService->getBoardLogs($board),
            ]);
        }

        return redirect()->route('boards.show', $board);
    }
}
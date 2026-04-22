<?php

namespace App\Http\Controllers;

use App\Models\Board;
use App\Models\Card;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;

class ActivityController extends Controller
{
    use AuthorizesRequests;

    public function card(Request $request, Card $card)
    {
        $this->authorize('view', $card);

        $logs = $card->activityLogs()
            ->with('user')
            ->latest('created_at')
            ->take(50)
            ->get();

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'data'    => $logs->map(fn($log) => $this->formatLog($log)),
            ]);
        }

        return redirect()->route('boards.show', $card->list->board);
    }

    public function board(Request $request, Board $board)
    {
        $this->authorize('view', $board);

        $logs = $board->activityLogs()
            ->with('user', 'card')
            ->latest('created_at')
            ->take(100)
            ->get();

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'data'    => $logs->map(fn($log) => $this->formatLog($log, true)),
            ]);
        }

        return redirect()->route('boards.show', $board);
    }

    private function formatLog($log, bool $includeCard = false): array
    {
        $data = [
            'id'          => $log->id,
            'action'      => $log->action,
            'description' => $log->description,
            'created_at'  => $log->created_at->toDateTimeString(),
            'time_ago'    => $log->created_at->diffForHumans(),
            'user'        => [
                'id'   => $log->user->id,
                'name' => $log->user->name,
            ],
        ];

        if ($includeCard && $log->card) {
            $data['card'] = [
                'id'    => $log->card->id,
                'title' => $log->card->title,
            ];
        }

        return $data;
    }
}

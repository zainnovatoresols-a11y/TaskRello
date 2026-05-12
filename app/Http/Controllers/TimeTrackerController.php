<?php

namespace App\Http\Controllers;

use App\Models\Card;
use App\Models\CardTimeLog;
use App\Services\TimeTrackerService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;

class TimeTrackerController extends Controller
{
    use AuthorizesRequests;

    public function __construct(
        private TimeTrackerService $timeTrackerService
    ) {}

    public function start(Request $request, Card $card)
    {
        $this->authorize('update', $card);

        $result = $this->timeTrackerService->startSession(
            $card,
            $request->user()
        );

        $statusCode = $result['success'] ? 201 : 422;

        return response()->json($result, $statusCode);
    }

    public function stop(Request $request, Card $card)
    {
        $this->authorize('update', $card);

        $result = $this->timeTrackerService->stopSession(
            $card,
            $request->user()
        );

        $statusCode = $result['success'] ? 200 : 422;

        return response()->json($result, $statusCode);
    }

    public function status(Request $request, Card $card)
    {
        $this->authorize('view', $card);

        $result = $this->timeTrackerService->getStatus(
            $card,
            $request->user()
        );

        return response()->json($result);
    }

    public function logs(Request $request, Card $card)
    {
        $this->authorize('view', $card);

        $result = $this->timeTrackerService->getLogs($card);

        return response()->json($result);
    }

    public function boardActiveSessions(Request $request, \App\Models\Board $board)
    {
        if (!$board->isMember($request->user())) {
            return response()->json([
                'success' => false,
                'message' => 'You are not a member of this board.',
            ], 403);
        }

        $cardIds = $board->lists()
                         ->with('cards:id,list_id')
                         ->get()
                         ->pluck('cards')
                         ->flatten()
                         ->pluck('id')
                         ->toArray();

        if (empty($cardIds)) {
            return response()->json([
                'success'  => true,
                'sessions' => [],
            ]);
        }

        $sessions = $this->timeTrackerService->getActiveSessionsForBoard(
            $cardIds,
            $request->user()
        );

        return response()->json([
            'success'  => true,
            'sessions' => $sessions,
        ]);
    }

    public function updateNotes(Request $request, CardTimeLog $log)
    {
        $request->validate([
            'notes' => ['required', 'string', 'max:1000'],
        ]);

        $result = $this->timeTrackerService->updateNotes(
            $log,
            $request->user(),
            $request->notes
        );

        $statusCode = $result['success'] ? 200 : 403;

        return response()->json($result, $statusCode);
    }
}
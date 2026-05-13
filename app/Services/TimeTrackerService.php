<?php

namespace App\Services;

use App\Models\ActivityLog;
use App\Models\Card;
use App\Models\CardTimeLog;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class TimeTrackerService
{
    public function startSession(Card $card, User $user): array
    {
        $existingOnCard = CardTimeLog::getActiveSessionForCard($user, $card);

        if ($existingOnCard) {
            return [
                'success'  => false,
                'message'  => 'You already have an active session on this card.',
                'log'      => $this->formatLog($existingOnCard),
            ];
        }

        $activeElsewhere = CardTimeLog::getActiveSession($user);

        if ($activeElsewhere) {
            return [
                'success'           => false,
                'message'           => 'Please stop or complete your current running task before starting a new one.',
                'active_card_id'    => $activeElsewhere->card_id,
                'active_card_title' => $activeElsewhere->card?->title,
            ];
        }

        if ($card->is_completed) {
            return [
                'success' => false,
                'message' => 'Cannot track time on a completed card.',
            ];
        }

        $log = CardTimeLog::create([
            'card_id'    => $card->id,
            'user_id'    => $user->id,
            'started_at' => now(),
            'ended_at'   => null,
            'duration'   => null,
        ]);

        ActivityLog::log(
            $user,
            'started_task',
            "{$user->name} started working on '{$card->title}'",
            $card->list->board_id,
            $card->id
        );

        return [
            'success'         => true,
            'message'         => 'Timer started.',
            'log'             => $this->formatLog($log),
            'elapsed_seconds' => 0,
        ];
    }

    public function stopSession(Card $card, User $user): array
    {
        $log = CardTimeLog::getActiveSessionForCard($user, $card);

        if (!$log) {
            return [
                'success' => false,
                'message' => 'No active session found for this card.',
            ];
        }

        $stoppedLog = $this->stopSessionById($log);

        ActivityLog::log(
            $user,
            'stopped_task',
            "{$user->name} logged {$stoppedLog->duration_formatted} "
            . "on '{$card->title}'",
            $card->list->board_id,
            $card->id
        );

        return [
            'success'            => true,
            'message'            => 'Timer stopped. Time logged successfully.',
            'log'                => $this->formatLog($stoppedLog),
            'duration_seconds'   => $stoppedLog->duration,
            'duration_formatted' => $stoppedLog->duration_formatted,
            'total_seconds'      => $card->fresh()->total_time_seconds,
            'total_formatted'    => $card->fresh()->total_time_formatted,
            'is_completed'       => $card->is_completed,
        ];
    }

    public function getStatus(Card $card, User $user): array
    {
        $activeLog = CardTimeLog::getActiveSessionForCard($user, $card);

        return [
            'success'         => true,
            'is_running'      => (bool) $activeLog,
            'elapsed_seconds' => $activeLog
                ? $activeLog->elapsed_seconds
                : 0,
            'started_at'      => $activeLog
                ? $activeLog->started_at->toDateTimeString()
                : null,
            'total_seconds'   => $card->total_time_seconds,
            'total_formatted' => $card->total_time_formatted,
            'is_completed'    => $card->is_completed,
            'log_id'          => $activeLog?->id,
        ];
    }

    public function getLogs(Card $card): array
    {
        $logs = $card->timeLogs()
                     ->with('user')
                     ->get();

        return [
            'success'         => true,
            'logs'            => $logs->map(fn($log) => $this->formatLog($log)),
            'total_seconds'   => $card->total_time_seconds,
            'total_formatted' => $card->total_time_formatted,
        ];
    }

    public function getActiveSessionsForBoard(
        array $cardIds,
        User  $user
    ): array {
        $activeLogs = CardTimeLog::whereIn('card_id', $cardIds)
                                 ->whereNull('ended_at')
                                 ->get()
                                 ->keyBy('card_id');

        $result = [];

        foreach ($cardIds as $cardId) {
            $log = $activeLogs->get($cardId);

            $result[$cardId] = [
                'is_running'      => (bool) $log,
                'is_my_session'   => $log && $log->user_id === $user->id,
                'elapsed_seconds' => $log && $log->user_id === $user->id
                    ? $log->elapsed_seconds
                    : 0,
                'started_by'      => $log?->user?->name,
                'log_id'          => $log?->id,
            ];
        }

        return $result;
    }

    public function updateNotes(
        CardTimeLog $log,
        User        $user,
        string      $notes
    ): array {
        if ($log->user_id !== $user->id) {
            return [
                'success' => false,
                'message' => 'You can only update your own time logs.',
            ];
        }

        $log->update(['notes' => $notes]);

        return [
            'success' => true,
            'message' => 'Notes updated.',
            'log'     => $this->formatLog($log->fresh()),
        ];
    }

    private function stopSessionById(CardTimeLog $log): CardTimeLog
    {
        $endedAt = now();
        $duration = 0;

        if ($log->started_at) {
            $duration = $endedAt->getTimestamp() - $log->started_at->getTimestamp();
            $duration = max(0, $duration);
        }

        $log->update([
            'ended_at' => $endedAt,
            'duration' => $duration,
        ]);

        return $log->fresh();
    }

    private function formatLog(CardTimeLog $log): array
    {
        return [
            'id'                 => $log->id,
            'card_id'            => $log->card_id,
            'user_id'            => $log->user_id,
            'started_at'         => $log->started_at->toDateTimeString(),
            'started_at_human'   => $log->started_at->format('M d, g:i A'),
            'ended_at'           => $log->ended_at?->toDateTimeString(),
            'ended_at_human'     => $log->ended_at?->format('M d, g:i A'),
            'duration'           => $log->duration,
            'duration_formatted' => $log->duration_formatted,
            'elapsed_seconds'    => $log->elapsed_seconds,
            'is_running'         => $log->is_running,
            'notes'              => $log->notes,
            'user'               => $log->relationLoaded('user') ? [
                'id'   => $log->user->id,
                'name' => $log->user->name,
            ] : null,
        ];
    }
}
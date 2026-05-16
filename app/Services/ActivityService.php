<?php

namespace App\Services;

use App\Models\Board;
use App\Models\Card;
use App\Repositories\Contracts\ActivityRepositoryInterface;

class ActivityService
{
    public function __construct(
        private readonly ActivityRepositoryInterface $activityRepository
    ) {}

    public function getCardLogs(Card $card)
    {
        $logs = $this->activityRepository->getCardLogs($card);

        return $logs->map(fn($log) => $this->formatLog($log));
    }

    public function getBoardLogs(Board $board)
    {
        $logs = $this->activityRepository->getBoardLogs($board);

        return $logs->map(fn($log) => $this->formatLog($log, true));
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
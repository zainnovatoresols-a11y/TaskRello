<?php

namespace App\Repositories;

use App\Models\Board;
use App\Models\Card;
use App\Repositories\Contracts\ActivityRepositoryInterface;
use Illuminate\Support\Collection;

class ActivityRepository implements ActivityRepositoryInterface
{
    public function getCardLogs(Card $card): Collection
    {
        return $card->activityLogs()
            ->with('user')
            ->latest('created_at')
            ->take(50)
            ->get();
    }

    public function getBoardLogs(Board $board): Collection
    {
        return $board->activityLogs()
            ->with('user', 'card')
            ->latest('created_at')
            ->take(100)
            ->get();
    }
}
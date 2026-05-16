<?php

namespace App\Repositories\Contracts;

use App\Models\Board;
use App\Models\Card;
use Illuminate\Support\Collection;

interface ActivityRepositoryInterface
{
    public function getCardLogs(Card $card): Collection;
    public function getBoardLogs(Board $board): Collection;
}
<?php

namespace App\Repositories\Contracts;

use App\Models\BoardList;
use App\Models\Card;

interface CardRepositoryInterface
{
    public function create(BoardList $list, array $data): Card;
    public function update(Card $card, array $data): Card;
    public function delete(Card $card): void;
    public function getPosition(BoardList $list): int;
    public function loadWithRelations(Card $card): Card;
    public function shiftPositionsDown(int $listId, int $from, int $to, int $excludeId): void;
    public function shiftPositionsUp(int $listId, int $from, int $to, int $excludeId): void;
    public function closeGap(int $listId, int $fromPosition): void;
    public function makeSpace(int $listId, int $atPosition): void;
    public function toggleAssignee(Card $card, int $userId): bool;
    public function toggleComplete(Card $card): Card;
    public function getByList(BoardList $list);
}

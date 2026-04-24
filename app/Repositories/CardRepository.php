<?php

namespace App\Repositories;

use App\Models\BoardList;
use App\Models\Card;
use App\Repositories\Contracts\CardRepositoryInterface;

class CardRepository implements CardRepositoryInterface
{
    public function create(BoardList $list, array $data): Card
    {
        return $list->cards()->create($data);
    }

    public function update(Card $card, array $data): Card
    {
        $card->update($data);
        return $card;
    }

    public function delete(Card $card): void
    {
        $card->delete();
    }

    public function getPosition(BoardList $list): int
    {
        return $list->cards()->count();
    }

    public function loadWithRelations(Card $card): Card
    {
        return $card->load([
            'creator',
            'assignees',
            'labels',
            'comments.author',
            'attachments.uploader',
            'activityLogs.user',
            'list.board.members',
            'descriptionImages',
        ]);
    }

    public function shiftPositionsDown(int $listId, int $from, int $to, int $excludeId): void
    {
        Card::where('list_id', $listId)
            ->where('id', '!=', $excludeId)
            ->whereBetween('position', [$from, $to])
            ->decrement('position');
    }

    public function shiftPositionsUp(int $listId, int $from, int $to, int $excludeId): void
    {
        Card::where('list_id', $listId)
            ->where('id', '!=', $excludeId)
            ->whereBetween('position', [$from, $to])
            ->increment('position');
    }

    public function closeGap(int $listId, int $fromPosition): void
    {
        Card::where('list_id', $listId)
            ->where('position', '>', $fromPosition)
            ->decrement('position');
    }

    public function makeSpace(int $listId, int $atPosition): void
    {
        Card::where('list_id', $listId)
            ->where('position', '>=', $atPosition)
            ->increment('position');
    }

    public function toggleAssignee(Card $card, int $userId): bool
    {
        $card->assignees()->toggle($userId);
        return $card->fresh()->assignees->contains($userId);
    }

    public function toggleComplete(Card $card): Card
    {
        $card->update(['is_completed' => !$card->is_completed]);
        return $card->fresh();
    }

    public function getByList(BoardList $list)
    {
        return $list->cards()
            ->with(['assignees', 'labels'])
            ->where('is_archived', false)
            ->orderBy('position')
            ->get();
    }

}

<?php

namespace App\Services;

use App\Models\BoardList;
use App\Models\Card;
use App\Models\User;
use App\Models\ActivityLog;
use App\Models\Notification;
use App\Repositories\Contracts\CardRepositoryInterface;

class CardService
{
    public function __construct(
        private CardRepositoryInterface $cardRepository
    ) {}

    public function create(BoardList $list, array $data, User $user): Card
    {
        $board    = $list->board;
        $position = $this->cardRepository->getPosition($list);

        $card = $this->cardRepository->create($list, [
            'user_id'  => $user->id,
            'title'    => $data['title'],
            'position' => $position,
        ]);

        ActivityLog::log(
            $user,
            'created_card',
            "{$user->name} created card '{$card->title}'",
            $board->id,
            $card->id
        );

        return $card->load('assignees', 'labels');
    }

    public function show(Card $card): Card
    {
        return $this->cardRepository->loadWithRelations($card);
    }

    public function update(Card $card, array $data, User $user): Card
    {
        $card = $this->cardRepository->update($card, $data);

        ActivityLog::log(
            $user,
            'updated_card',
            "{$user->name} updated card '{$card->title}'",
            $card->list->board_id,
            $card->id
        );

        return $card->fresh()->load('assignees', 'labels');
    }

    public function delete(Card $card, User $user): void
    {
        $boardId = $card->list->board_id;
        $title   = $card->title;

        $this->cardRepository->delete($card);

        ActivityLog::log(
            $user,
            'deleted_card',
            "{$user->name} deleted card '{$title}'",
            $boardId
        );
    }

    public function move(Card $card, int $newListId, int $newPos, User $user): void
    {
        $oldListId = $card->list_id;
        $oldPos    = $card->position;

        if ($oldListId === $newListId) {
            if ($oldPos === $newPos) return;

            if ($newPos > $oldPos) {
                $this->cardRepository->shiftPositionsDown(
                    $newListId,
                    $oldPos + 1,
                    $newPos,
                    $card->id
                );
            } else {
                $this->cardRepository->shiftPositionsUp(
                    $newListId,
                    $newPos,
                    $oldPos - 1,
                    $card->id
                );
            }

            $this->cardRepository->update($card, ['position' => $newPos]);
        } else {
            $this->cardRepository->closeGap($oldListId, $oldPos);
            $this->cardRepository->makeSpace($newListId, $newPos);
            $this->cardRepository->update($card, [
                'list_id'  => $newListId,
                'position' => $newPos,
            ]);
        }

        $newList = BoardList::find($newListId);
        $board   = $newList->board;

        ActivityLog::log(
            $user,
            'moved_card',
            "{$user->name} moved '{$card->title}' to '{$newList->name}'",
            $board->id,
            $card->id
        );

        $this->notifyMoveCard($card, $newList, $board, $user);
    }

    public function assign(Card $card, int $userId, User $actor): \Illuminate\Database\Eloquent\Collection
    {
        $isNowAssigned = $this->cardRepository->toggleAssignee($card, $userId);

        if ($isNowAssigned) {
            $this->notifyAssignment($card, $userId, $actor);
        }

        return $card->fresh()->assignees;
    }

    public function toggleComplete(Card $card, User $user): Card
    {
        $card = $this->cardRepository->toggleComplete($card);

        ActivityLog::log(
            $user,
            $card->is_completed ? 'completed_card' : 'reopened_card',
            $user->name . ($card->is_completed ? ' completed ' : ' reopened ') . "'{$card->title}'",
            $card->list->board_id,
            $card->id
        );

        $board = $card->list->board;

        if ($board->user_id !== $user->id) {
            Notification::notify(
                userId: $board->user_id,
                actor: $user,
                type: $card->is_completed ? 'completed_card' : 'reopened_card',
                message: $user->name . ($card->is_completed ? ' completed ' : ' reopened ') . '"' . $card->title . '"',
                boardId: $board->id,
                cardId: $card->id,
                url: route('boards.show', $board->id)
            );
        }

        return $card;
    }

    private function notifyMoveCard(Card $card, $newList, $board, User $actor): void
    {
        $notified = [$actor->id];

        foreach ($card->assignees as $assignee) {
            if (!in_array($assignee->id, $notified)) {
                Notification::notify(
                    userId: $assignee->id,
                    actor: $actor,
                    type: 'moved_card',
                    message: "{$actor->name} moved \"{$card->title}\" to \"{$newList->name}\"",
                    boardId: $board->id,
                    cardId: $card->id,
                    url: route('boards.show', $board->id)
                );
                $notified[] = $assignee->id;
            }
        }

        if (!in_array($board->user_id, $notified)) {
            Notification::notify(
                userId: $board->user_id,
                actor: $actor,
                type: 'moved_card',
                message: "{$actor->name} moved \"{$card->title}\" to \"{$newList->name}\"",
                boardId: $board->id,
                cardId: $card->id,
                url: route('boards.show', $board->id)
            );
        }
    }

    private function notifyAssignment(Card $card, int $userId, User $actor): void
    {
        $board = $card->list->board;

        Notification::notify(
            userId: $userId,
            actor: $actor,
            type: 'assigned_card',
            message: "{$actor->name} assigned you to \"{$card->title}\"",
            boardId: $board->id,
            cardId: $card->id,
            url: route('boards.show', $board->id)
        );

        if ($board->user_id !== $userId) {
            Notification::notify(
                userId: $board->user_id,
                actor: $actor,
                type: 'assigned_card',
                message: "{$actor->name} assigned " . User::find($userId)?->name . " to \"{$card->title}\"",
                boardId: $board->id,
                cardId: $card->id,
                url: route('boards.show', $board->id)
            );
        }
    }
    
    public function getCardsByList(BoardList $list): \Illuminate\Support\Collection
    {
        $cards = $this->cardRepository->getByList($list);

        return $cards->map(fn($card) => $this->formatCard($card));
    }
    private function formatCard(Card $card): array
    {
        return [
            'id'           => $card->id,
            'title'        => $card->title,
            'description'  => $card->description,
            'position'     => $card->position,
            'due_date'     => $card->due_date?->toDateString(),
            'cover_color'  => $card->cover_color,
            'is_completed' => $card->is_completed,
            'is_archived'  => $card->is_archived,
            'is_overdue'   => $card->isOverdue(),
            'is_due_soon'  => $card->isDueSoon(),
            'list_id'      => $card->list_id,
            'created_at'   => $card->created_at->toDateTimeString(),
            'assignees'    => $card->assignees->map(fn($u) => [
                'id' => $u->id,
                'name' => $u->name
            ])->toArray(),
            'labels'       => $card->labels->map(fn($l) => [
                'id' => $l->id,
                'name' => $l->name,
                'color' => $l->color
            ])->toArray(),
        ];
    }
}

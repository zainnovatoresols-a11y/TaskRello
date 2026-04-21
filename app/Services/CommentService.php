<?php

namespace App\Services;

use App\Models\Card;
use App\Models\Comment;
use App\Models\User;
use App\Models\ActivityLog;
use App\Models\Notification;
use App\Repositories\Contracts\CommentRepositoryInterface;

class CommentService
{
    public function __construct(
        private CommentRepositoryInterface $commentRepository
    ) {}

    public function create(Card $card, string $body, User $user): Comment
    {
        $board = $card->list->board;

        $comment = $this->commentRepository->create($card, [
            'user_id' => $user->id,
            'body'    => $body,
        ]);

        ActivityLog::log(
            $user,
            'added_comment',
            "{$user->name} commented on '{$card->title}'",
            $board->id,
            $card->id
        );

        $this->notifyComment($card, $user);

        return $comment->load('author');
    }

    public function delete(Comment $comment): void
    {
        $this->commentRepository->delete($comment);
    }

    private function notifyComment(Card $card, User $actor): void
    {
        $board    = $card->list->board;
        $notified = [$actor->id];

        foreach ($card->assignees as $assignee) {
            if (!in_array($assignee->id, $notified)) {
                Notification::notify(
                    userId: $assignee->id,
                    actor: $actor,
                    type: 'new_comment',
                    message: "{$actor->name} commented on \"{$card->title}\"",
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
                type: 'new_comment',
                message: "{$actor->name} commented on \"{$card->title}\"",
                boardId: $board->id,
                cardId: $card->id,
                url: route('boards.show', $board->id)
            );
        }
    }
}

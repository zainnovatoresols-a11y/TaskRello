<?php

namespace App\Policies;

use App\Models\Comment;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class CommentPolicy
{
    // Any board member can post a comment
    // (we verify board membership through card → list → board)
    public function create(User $user, Comment $comment): bool
    {
        $board = $comment->card->list->board;

        return $board->isMember($user);
    }

    // Only the comment author can EDIT their comment
    public function update(User $user, Comment $comment): bool
    {
        return $comment->isAuthor($user);
    }

    // Author OR board owner can DELETE a comment
    public function delete(User $user, Comment $comment): bool
    {
        $board = $comment->card->list->board;

        return $comment->isAuthor($user)
            || $board->isOwner($user);
    }
}

<?php

namespace App\Repositories;

use App\Models\Card;
use App\Models\Comment;
use App\Repositories\Contracts\CommentRepositoryInterface;

class CommentRepository implements CommentRepositoryInterface
{
    public function create(Card $card, array $data): Comment
    {
        return $card->comments()->create($data);
    }

    public function delete(Comment $comment): void
    {
        $comment->delete();
    }
}

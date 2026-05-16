<?php

namespace App\Repositories;

use App\Models\Card;
use App\Models\Comment;
use App\Repositories\Contracts\CommentRepositoryInterface;
use Illuminate\Support\Collection;

class CommentRepository implements CommentRepositoryInterface
{
    public function getByCard(Card $card): Collection
    {
        return $card->comments()
            ->with('author')
            ->orderBy('created_at')
            ->get();
    }

    public function create(Card $card, array $data): Comment
    {
        return $card->comments()->create($data);
    }

    public function delete(Comment $comment): void
    {
        $comment->delete();
    }
}
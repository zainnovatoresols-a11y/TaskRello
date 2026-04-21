<?php

namespace App\Repositories\Contracts;

use App\Models\Card;
use App\Models\Comment;

interface CommentRepositoryInterface
{
    public function create(Card $card, array $data): Comment;
    public function delete(Comment $comment): void;
}

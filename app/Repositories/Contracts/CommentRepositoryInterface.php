<?php

namespace App\Repositories\Contracts;

use App\Models\Card;
use App\Models\Comment;
use Illuminate\Support\Collection;

interface CommentRepositoryInterface
{
    public function getByCard(Card $card): Collection;
    public function create(Card $card, array $data): Comment;
    public function delete(Comment $comment): void;
}
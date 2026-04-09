<?php

namespace App\Policies;

use App\Models\Board;
use App\Models\Card;
use App\Models\User;

class CardPolicy
{
    private function getBoard(Card $card): Board
    {
        return $card->list->board;
    }

    public function view(User $user, Card $card): bool
    {
        return $this->getBoard($card)->isMember($user);
    }

    public function create(User $user, Board $board): bool
    {
        return $board->isMember($user);
    }

    public function update(User $user, Card $card): bool
    {
        return $this->getBoard($card)->isMember($user);
    }

    public function delete(User $user, Card $card): bool
    {
        $board = $this->getBoard($card);

        return $card->user_id === $user->id
            || $board->isOwner($user);
    }

    public function move(User $user, Card $card): bool
    {
        return $this->getBoard($card)->isMember($user);
    }

    public function assign(User $user, Card $card): bool
    {
        return $this->getBoard($card)->isMember($user);
    }

}

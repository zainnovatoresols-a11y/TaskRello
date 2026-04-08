<?php

namespace App\Policies;

use App\Models\Card;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class CardPolicy
{
    // Helper: get the board from the card (through its list)
    private function getBoard(Card $card)
    {
        return $card->list->board;
    }

    public function view(User $user, Card $card): bool
    {
        return $this->getBoard($card)->isMember($user);
    }

    public function create(User $user, Card $card): bool
    {
        return $this->getBoard($card)->isMember($user);
    }

    public function update(User $user, Card $card): bool
    {
        return $this->getBoard($card)->isMember($user);
    }

    // Card creator OR board owner can delete
    public function delete(User $user, Card $card): bool
    {
        $board = $this->getBoard($card);

        return $card->user_id === $user->id
            || $board->isOwner($user);
    }

    // Any board member can move (drag-drop) a card
    public function move(User $user, Card $card): bool
    {
        return $this->getBoard($card)->isMember($user);
    }

    // Any board member can assign users to a card
    public function assign(User $user, Card $card): bool
    {
        return $this->getBoard($card)->isMember($user);
    }
}

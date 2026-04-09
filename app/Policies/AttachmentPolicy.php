<?php

namespace App\Policies;

use App\Models\Card;

use App\Models\Attachment;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class AttachmentPolicy
{
    // Any board member can upload an attachment to a card
    public function create(User $user, Card $card): bool
    {
        return $card->list->board->isMember($user);
    }

    // Only the uploader OR board owner can delete an attachment
    public function delete(User $user, Attachment $attachment): bool
    {
        $board = $attachment->card->list->board;

        return $attachment->user_id === $user->id
            || $board->isOwner($user);
    }
}

<?php

namespace App\Policies;

use App\Models\Board;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class BoardPolicy
{
    // Any logged-in user can list boards (they only see THEIR boards
    // in the controller query — this just gates the route)
    public function viewAny(User $user): bool
    {
        return true;
    }

    // User must be a member of the board to view it
    public function view(User $user, Board $board): bool
    {
        return $board->isMember($user);
    }

    // Any authenticated user can create a board
    public function create(User $user): bool
    {
        return true;
    }

    // Any board member can update board name/description/color
    public function update(User $user, Board $board): bool
    {
        return $board->isMember($user);
    }

    // ONLY the board owner can delete it
    public function delete(User $user, Board $board): bool
    {
        return $board->isOwner($user);
    }

    // ONLY the board owner can invite/remove members
    public function addMember(User $user, Board $board): bool
    {
        return $board->isOwner($user);
    }
}

<?php

namespace App\Repositories;

use App\Models\Conversation;
use App\Models\User;
use App\Repositories\Contracts\ConversationRepositoryInterface;
use Illuminate\Support\Collection;

class ConversationRepository implements ConversationRepositoryInterface
{
    public function loadWithRelations(Conversation $conversation): Conversation
    {
        return $conversation->load(['users', 'board']);
    }

    public function searchBoardMembers(User $user, string $searchTerm): Collection
    {
        return User::distinct()
            ->join('board_user', 'users.id', '=', 'board_user.user_id')
            ->join('boards', 'board_user.board_id', '=', 'boards.id')
            ->where('boards.user_id', $user->id)
            ->where('users.id', '!=', $user->id)
            ->where('board_user.status', 'accepted')
            ->where(function ($q) use ($searchTerm) {
                $q->where('users.name', 'like', '%' . $searchTerm . '%')
                    ->orWhere('users.email', 'like', '%' . $searchTerm . '%');
            })
            ->select('users.id', 'users.name', 'users.email', 'users.avatar')
            ->limit(10)
            ->get();
    }

    public function getBoardMembers(User $user): Collection
    {
        return User::distinct()
            ->join('board_user', 'users.id', '=', 'board_user.user_id')
            ->join('boards', 'board_user.board_id', '=', 'boards.id')
            ->where('boards.user_id', $user->id)
            ->where('users.id', '!=', $user->id)
            ->where('board_user.status', 'accepted')
            ->select('users.id', 'users.name', 'users.email', 'users.avatar')
            ->get();
    }
}
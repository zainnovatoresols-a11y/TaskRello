<?php

namespace App\Repositories\Contracts;

use App\Models\Conversation;
use App\Models\User;
use Illuminate\Support\Collection;

interface ConversationRepositoryInterface
{
    public function loadWithRelations(Conversation $conversation): Conversation;
    public function searchBoardMembers(User $user, string $searchTerm): Collection;
    public function getBoardMembers(User $user): Collection;
}
<?php

namespace App\Repositories\Contracts;

use App\Models\Board;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

interface BoardRepositoryInterface
{
    public function getUserBoards(User $user): Collection;
    public function create(array $data): Board;
    public function update(Board $board, array $data): Board;
    public function delete(Board $board): void;
    public function attachMember(Board $board, int $userId, string $role): void;
    public function detachMember(Board $board, int $userId): void;
    public function loadBoardWithRelations(Board $board): Board;
    public function unassignFromAllCards(Board $board, int $userId): void;
}

<?php

namespace App\Repositories;

use App\Models\Board;
use App\Models\User;
use App\Repositories\Contracts\BoardRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class BoardRepository implements BoardRepositoryInterface
{
    public function getUserBoards(User $user): Collection
    {
        return $user->boards()
            ->with('owner')
            ->withCount('members')
            ->latest()
            ->get();
    }

    public function create(array $data): Board
    {
        return Board::create($data);
    }

    public function update(Board $board, array $data): Board
    {
        $board->update($data);
        return $board;
    }

    public function delete(Board $board): void
    {
        $board->delete();
    }

    public function attachMember(Board $board, int $userId, string $role): void
    {
        $board->members()->attach($userId, ['role' => $role]);
    }

    public function detachMember(Board $board, int $userId): void
    {
        $board->members()->detach($userId);
    }

    public function loadBoardWithRelations(Board $board): Board
    {
        return $board->load([
            'lists.cards.assignees',
            'lists.cards.labels',
            'members',
            'labels',
        ]);
    }

    public function unassignFromAllCards(Board $board, int $userId): void
    {
        $board->lists->each(function ($list) use ($userId) {
            $list->cards->each(function ($card) use ($userId) {
                $card->assignees()->detach($userId);
            });
        });
    }
}

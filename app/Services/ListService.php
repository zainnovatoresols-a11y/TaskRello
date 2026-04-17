<?php
// app/Services/ListService.php

namespace App\Services;

use App\Models\Board;
use App\Models\BoardList;
use App\Models\ActivityLog;
use App\Repositories\Contracts\ListRepositoryInterface;

class ListService
{
    public function __construct(
        private ListRepositoryInterface $listRepository
    ) {}

    public function create(Board $board, string $name, $user): BoardList
    {
        $position = $this->listRepository->getPosition($board);

        $list = $this->listRepository->create($board, [
            'name'     => $name,
            'position' => $position,
        ]);

        ActivityLog::log(
            $user,
            'created_list',
            "{$user->name} added list '{$list->name}'",
            $board->id
        );

        return $list;
    }

    public function update(BoardList $list, array $data): BoardList
    {
        return $this->listRepository->update($list, $data);
    }

    public function delete(BoardList $list): void
    {
        $this->listRepository->delete($list);
    }

    public function reorder(Board $board, array $lists): void
    {
        $this->listRepository->reorder($board, $lists);
    }
}

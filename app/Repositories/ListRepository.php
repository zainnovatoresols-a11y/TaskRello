<?php

namespace App\Repositories;

use App\Models\Board;
use App\Models\BoardList;
use App\Repositories\Contracts\ListRepositoryInterface;

class ListRepository implements ListRepositoryInterface
{
    public function create(Board $board, array $data): BoardList
    {
        return $board->lists()->create($data);
    }

    public function update(BoardList $list, array $data): BoardList
    {
        $list->update($data);
        return $list;
    }

    public function delete(BoardList $list): void
    {
        $list->delete();
    }

    public function reorder(Board $board, array $lists): void
    {
        foreach ($lists as $item) {
            BoardList::where('id', $item['id'])
                ->where('board_id', $board->id)
                ->update(['position' => $item['position']]);
        }
    }

    public function getPosition(Board $board): int
    {
        return $board->lists()->count();
    }
}

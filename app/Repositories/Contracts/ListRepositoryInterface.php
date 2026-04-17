<?php

namespace App\Repositories\Contracts;

use App\Models\Board;
use App\Models\BoardList;

interface ListRepositoryInterface
{
    public function create(Board $board, array $data): BoardList;
    public function update(BoardList $list, array $data): BoardList;
    public function delete(BoardList $list): void;
    public function reorder(Board $board, array $lists): void;
    public function getPosition(Board $board): int;
}

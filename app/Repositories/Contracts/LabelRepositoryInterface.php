<?php

namespace App\Repositories\Contracts;

use App\Models\Board;
use App\Models\Card;
use App\Models\Label;
use Illuminate\Database\Eloquent\Collection;

interface LabelRepositoryInterface
{
    public function create(Board $board, array $data): Label;
    public function update(Label $label, array $data): Label;
    public function delete(Label $label): void;
    public function attach(Card $card, int $labelId): void;
    public function detach(Card $card, int $labelId): void;
    public function getCardLabels(Card $card): Collection;
    public function belongsToBoard(Label $label, Board $board): bool;
}

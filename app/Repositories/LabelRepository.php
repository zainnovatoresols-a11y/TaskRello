<?php

namespace App\Repositories;

use App\Models\Board;
use App\Models\Card;
use App\Models\Label;
use App\Repositories\Contracts\LabelRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class LabelRepository implements LabelRepositoryInterface
{
    public function create(Board $board, array $data): Label
    {
        return $board->labels()->create($data);
    }

    public function update(Label $label, array $data): Label
    {
        $label->update($data);
        return $label;
    }

    public function delete(Label $label): void
    {
        $label->delete();
    }

    public function attach(Card $card, int $labelId): void
    {
        $card->labels()->syncWithoutDetaching([$labelId]);
    }

    public function detach(Card $card, int $labelId): void
    {
        $card->labels()->detach($labelId);
    }

    public function getCardLabels(Card $card): Collection
    {
        return $card->fresh()->labels;
    }

    public function belongsToBoard(Label $label, Board $board): bool
    {
        return $label->board_id === $board->id;
    }
}

<?php

namespace App\Services;

use App\Models\Board;
use App\Models\Card;
use App\Models\Label;
use App\Repositories\Contracts\LabelRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class LabelService
{
    public function __construct(
        private LabelRepositoryInterface $labelRepository
    ) {}

    public function create(Board $board, array $data): Label
    {
        return $this->labelRepository->create($board, [
            'name'  => $data['name'],
            'color' => $data['color'],
        ]);
    }

    public function update(Board $board, Label $label, array $data): Label
    {
        if (!$this->labelRepository->belongsToBoard($label, $board)) {
            throw new \InvalidArgumentException('This label does not belong to this board.');
        }

        return $this->labelRepository->update($label, $data);
    }

    public function attach(Card $card, Label $label): Collection
    {
        $board = $card->list->board;

        if (!$this->labelRepository->belongsToBoard($label, $board)) {
            throw new \InvalidArgumentException('Label does not belong to this board.');
        }

        $this->labelRepository->attach($card, $label->id);

        return $this->labelRepository->getCardLabels($card);
    }

    public function detach(Card $card, Label $label): Collection
    {
        $this->labelRepository->detach($card, $label->id);

        return $this->labelRepository->getCardLabels($card);
    }

    public function delete(Board $board, Label $label): void
    {
        if (!$this->labelRepository->belongsToBoard($label, $board)) {
            throw new \InvalidArgumentException('This label does not belong to this board.');
        }

        $this->labelRepository->delete($label);
    }
}

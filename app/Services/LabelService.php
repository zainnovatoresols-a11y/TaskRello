<?php

namespace App\Services;

use App\Models\Board;
use App\Models\Card;
use App\Models\Label;
use App\Models\User;
use App\Repositories\Contracts\LabelRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use App\Models\ActivityLog;


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

    public function attach(Card $card, Label $label, User $user): Collection
    {
        $board = $card->list->board;

        if (!$this->labelRepository->belongsToBoard($label, $board)) {
            throw new \InvalidArgumentException('Label does not belong to this board.');
        }

        $this->labelRepository->attach($card, $label->id);

        ActivityLog::log(
            $user,
            'attached_label',
            "{$user->name} attached label '{$label->name}' to '{$card->title}'",
            $board->id,
            $card->id
        );

        return $this->labelRepository->getCardLabels($card);
    }

    public function detach(Card $card, Label $label, User $user): Collection
    {
        $board = $card->list->board;

        $this->labelRepository->detach($card, $label->id);

        ActivityLog::log(
            $user,
            'detached_label',
            "{$user->name} removed label '{$label->name}' from '{$card->title}'",
            $board->id,
            $card->id
        );

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

<?php
namespace App\Repositories\Contracts;

use App\Models\Card;
use App\Models\Attachment;
use Illuminate\Support\Collection;

interface AttachmentRepositoryInterface
{
    public function getByCard(Card $card): Collection;
    public function create(Card $card, array $data): Attachment;
    public function delete(Attachment $attachment): void;
}
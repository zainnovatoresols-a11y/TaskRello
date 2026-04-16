<?php
namespace App\Repositories\Contracts;

use App\Models\Card;
use App\Models\Attachment;

interface AttachmentRepositoryInterface
{
    public function create(Card $card, array $data): Attachment;
    public function delete(Attachment $attachment): void;
}

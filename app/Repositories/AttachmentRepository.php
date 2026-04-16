<?php
// app/Repositories/AttachmentRepository.php

namespace App\Repositories;

use App\Models\Card;
use App\Models\Attachment;
use App\Repositories\Contracts\AttachmentRepositoryInterface;

class AttachmentRepository implements AttachmentRepositoryInterface
{
    public function create(Card $card, array $data): Attachment
    {
        return $card->attachments()->create($data);
    }

    public function delete(Attachment $attachment): void
    {
        $attachment->delete();
    }
}

<?php
namespace App\Repositories;

use App\Models\Card;
use App\Models\Attachment;
use App\Repositories\Contracts\AttachmentRepositoryInterface;
use Illuminate\Support\Collection;

class AttachmentRepository implements AttachmentRepositoryInterface
{
    public function getByCard(Card $card): Collection
    {
        return $card->attachments()
            ->with('uploader')
            ->latest()
            ->get();
    }

    public function create(Card $card, array $data): Attachment
    {
        return $card->attachments()->create($data);
    }

    public function delete(Attachment $attachment): void
    {
        $attachment->delete();
    }
}
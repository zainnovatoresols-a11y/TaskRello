<?php

namespace App\Services;

use App\Models\Card;
use App\Models\Attachment;
use App\Models\User;
use App\Models\ActivityLog;
use App\Repositories\Contracts\AttachmentRepositoryInterface;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;


class AttachmentService
{
    public function __construct(
        private AttachmentRepositoryInterface $attachmentRepository
    ) {}

    public function getByCard(Card $card): Collection
    {
        return $this->attachmentRepository->getByCard($card);
    }

    public function store(Card $card, UploadedFile $file, int $userId): Attachment
    {
        $board = $card->list->board;
        $path  = $file->store('attachments/card-' . $card->id, 'public');

        try {
            return DB::transaction(function () use ($card, $file, $path, $userId, $board) {

                $attachment = $this->attachmentRepository->create($card, [
                    'user_id'   => $userId,
                    'filename'  => pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)
                        . '.' . $file->getClientOriginalExtension(),
                    'file_path' => $path,
                    'file_size' => $file->getSize(),
                    'mime_type' => $file->getMimeType(),
                ]);

                ActivityLog::log(
                    auth()->user(),
                    'added_attachment',
                    auth()->user()->name . " attached '{$file->getClientOriginalName()}'",
                    $board->id,
                    $card->id
                );

                return $attachment;
            });
        } catch (\Exception $e) {
            Storage::disk('public')->delete($path);
            throw $e;
        }
    }

    public function delete(Attachment $attachment, User $user): void
    {
        $card = $attachment->card;
        $board = $card->list->board;

        Storage::disk('public')->delete($attachment->file_path);
        $this->attachmentRepository->delete($attachment);

        ActivityLog::log(
            $user,
            'removed_attachment',
            "{$user->name} removed attachment '{$attachment->filename}'",
            $board->id,
            $card->id
        );
    }
}
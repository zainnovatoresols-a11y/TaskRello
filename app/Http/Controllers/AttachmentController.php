<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Card;
use App\Models\Attachment;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class AttachmentController extends Controller
{
    use AuthorizesRequests;
    // POST /cards/{card}/attachments
    public function store(Request $request, Card $card)
    {
        $board = $card->list->board;
        $this->authorize('create', [Attachment::class, $card]);

        $request->validate([
            'file' => [
                'required',
                'file',
                'max:10240', // 10 MB max
                'mimes:jpg,jpeg,png,gif,pdf,doc,docx,xls,xlsx,txt,zip',
            ],
        ]);

        $file     = $request->file('file');
        $path     = $file->store('attachments/card-' . $card->id, 'public');

        $attachment = $card->attachments()->create([
            'user_id'   => $request->user()->id,
            'filename'  => $file->getClientOriginalName(),
            'file_path' => $path,
            'file_size' => $file->getSize(),
            'mime_type' => $file->getMimeType(),
        ]);

        ActivityLog::log(
            $request->user(),
            'added_attachment',
            "{$request->user()->name} attached '{$file->getClientOriginalName()}'",
            $board->id,
            $card->id
        );

        return response()->json([
            'success'    => true,
            'attachment' => $attachment, // includes url and is_image via $appends
        ], 201);
    }

    // DELETE /attachments/{attachment}
    public function destroy(Request $request, Attachment $attachment)
    {
        $this->authorize('delete', $attachment);

        // Delete physical file from disk
        Storage::disk('public')->delete($attachment->file_path);

        // Delete DB record
        $attachment->delete();

        return response()->json(['success' => true]);
    }
}

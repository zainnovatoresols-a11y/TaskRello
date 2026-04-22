<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Card;
use App\Models\Attachment;
use App\Services\AttachmentService;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Http\Requests\StoreAttachmentRequest;

class AttachmentController extends Controller
{
    use AuthorizesRequests;

    public function __construct(private AttachmentService $attachmentService)
    {
        
    }

    public function index(Request $request, Card $card)
    {
        $board = $card->list->board;

        if (!$board->isMember($request->user())) {
            abort(403, 'You must be a board member to view attachments.');
        }

        $attachments = $card->attachments()
            ->with('uploader')
            ->latest()
            ->get();

        return response()->json([
            'success' => true,
            'data'    => $attachments->map(fn($a) => [
                'id'         => $a->id,
                'filename'   => $a->filename,
                'url'        => $a->url,
                'is_image'   => $a->is_image,
                'file_size'  => $a->file_size,
                'mime_type'  => $a->mime_type,
                'created_at' => $a->created_at->toDateTimeString(),
                'uploader'   => [
                    'id'   => $a->uploader->id,
                    'name' => $a->uploader->name,
                ],
            ]),
        ]);
    }

    public function store(StoreAttachmentRequest $request, Card $card)
    {
        $this->authorize('create', [Attachment::class, $card]);

        $file = $request->file('file');
        $user_id = $request->user()->id;
        $attachment = $this->attachmentService->store($card, $file , $user_id);

        return response()->json([
            'success'    => true,
            'attachment' => $attachment,
        ], 201);

    }

    public function destroy(Request $request, Attachment $attachment)
    {
        $this->authorize('delete', $attachment);

        $this->attachmentService->delete($attachment);
        return response()->json([
            'success' => true,
            'message' => 'Attachment deleted successfully'
        ]);
    }
}

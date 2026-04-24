<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCardRequest;
use App\Http\Requests\UpdateCardRequest;
use App\Models\BoardList;
use App\Models\Card;
use App\Services\CardService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use App\Models\ActivityLog;
use App\Models\CardDescriptionImage;
use Illuminate\Container\Attributes\Storage;

class CardController extends Controller
{
    use AuthorizesRequests;

    public function __construct(private CardService $cardService) {}

    public function index(Request $request, BoardList $list)
    {
        $board = $list->board;
        $this->authorize('view', $board);
        $cards = $this->cardService->getCardsByList($list);
        
        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'data'    => $cards,
            ]);
        }

        return redirect()->route('boards.show', $board);
    }

    public function store(StoreCardRequest $request, BoardList $list)
    {
        $board = $list->board;
        $this->authorize('create', [Card::class, $board]);

        $card = $this->cardService->create($list, $request->validated(), $request->user());

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Card created successfully.',
                'card'    => $this->formatCard($card), 
            ], 201);
        }

        return redirect()->route('boards.show', $board)->with('success', "Card '{$card->title}' created.");
    }

    public function show(Request $request, Card $card)
    {
        $this->authorize('view', $card);

        $card = $this->cardService->show($card);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'data'    => [
                    'id'            => $card->id,
                    'title'         => $card->title,
                    'description'   => $card->description,
                    'position'      => $card->position,
                    'due_date'      => $card->due_date?->toDateString(),
                    'cover_color'   => $card->cover_color,
                    'is_completed'  => $card->is_completed,
                    'is_archived'   => $card->is_archived,
                    'is_overdue'    => $card->isOverdue(),
                    'is_due_soon'   => $card->isDueSoon(),
                    'created_at'    => $card->created_at->toDateTimeString(),
                    'list'          => ['id' => $card->list->id, 'name' => $card->list->name],
                    'creator'       => ['id' => $card->creator->id, 'name' => $card->creator->name],
                    'assignees'     => $card->assignees->map(fn($u) => ['id' => $u->id, 'name' => $u->name, 'email' => $u->email]),
                    'labels'        => $card->labels->map(fn($l) => ['id' => $l->id, 'name' => $l->name, 'color' => $l->color]),
                    'comments'      => $card->comments->map(fn($c) => [
                        'id'         => $c->id,
                        'body'       => $c->body,
                        'created_at' => $c->created_at->diffForHumans(),
                        'author'     => ['id' => $c->author->id, 'name' => $c->author->name],
                    ]),
                    'attachments'   => $card->attachments->map(fn($a) => [
                        'id'        => $a->id,
                        'filename'  => $a->filename,
                        'url'       => $a->url,
                        'is_image'  => $a->is_image,
                        'file_size' => $a->file_size,
                        'mime_type' => $a->mime_type,
                    ]),
                    'activity_logs' => $card->activityLogs->map(fn($log) => [
                        'id'          => $log->id,
                        'action'      => $log->action,
                        'description' => $log->description,
                        'created_at'  => $log->created_at->diffForHumans(),
                        'user'        => ['id' => $log->user->id, 'name' => $log->user->name],
                    ]),
                    'board_members' => $card->list->board->members->map(fn($m) => [
                        'id'   => $m->id,
                        'name' => $m->name,
                        'role' => $m->pivot->role,
                    ]),
                ],
            ]);
        }

        return view('cards.show', compact('card'));
    }

    public function update(UpdateCardRequest $request, Card $card)
    {
        $board       = $card->list->board;
        $updatedCard = $this->cardService->update($card, $request->validated(), $request->user());

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Card updated successfully.', 'data' => $this->formatCard($updatedCard)]);
        }

        return redirect()->route('boards.show', $board)->with('success', 'Card updated.');
    }

    public function destroy(Request $request, Card $card)
    {
        $this->authorize('delete', $card);

        $board = $card->list->board;
        $this->cardService->delete($card, $request->user());

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Card deleted successfully.']);
        }

        return redirect()->route('boards.show', $board)->with('success', 'Card deleted.');
    }

    public function move(Request $request, Card $card)
    {
        $this->authorize('move', $card);

        $request->validate([
            'list_id'  => 'required|exists:lists,id',
            'position' => 'required|integer|min:0',
        ]);

        $board = $card->list->board;
        $this->cardService->move($card, (int) $request->list_id, (int) $request->position, $request->user());

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Card moved successfully.',
                'data'    => $this->formatCard($card->fresh()->load('assignees', 'labels')),
            ]);
        }

        return redirect()->route('boards.show', $board)->with('success', 'Card moved.');
    }

    public function assign(Request $request, Card $card)
    {
        $this->authorize('assign', $card);

        $request->validate(['user_id' => 'required|exists:users,id']);

        $assignees = $this->cardService->assign($card, (int) $request->user_id, $request->user());

        return response()->json([
            'success'   => true,
            'assignees' => $assignees,
        ]);
    }

    public function toggleComplete(Request $request, Card $card)
    {
        $this->authorize('update', $card);

        $board       = $card->list->board;
        $updatedCard = $this->cardService->toggleComplete($card, $request->user());
        $message     = $updatedCard->is_completed ? 'Card marked as completed.' : 'Card marked as incomplete.';

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'message' => $message, 'data' => ['id' => $updatedCard->id, 'is_completed' => $updatedCard->is_completed]]);
        }

        return redirect()->route('boards.show', $board)->with('success', $message);
    }

    private function formatCard(Card $card): array
    {
        return [
            'id'           => $card->id,
            'title'        => $card->title,
            'description'  => $card->description,
            'position'     => $card->position,
            'due_date'     => $card->due_date?->toDateString(),
            'cover_color'  => $card->cover_color,
            'is_completed' => $card->is_completed,
            'is_archived'  => $card->is_archived,
            'is_overdue'   => $card->isOverdue(),
            'is_due_soon'  => $card->isDueSoon(),
            'list_id'      => $card->list_id,
            'created_at'   => $card->created_at->toDateTimeString(),
            'assignees'    => $card->assignees->map(fn($u) => [
                'id' => $u->id,
                'name' => $u->name
            ])->toArray(),
            'labels'       => $card->labels->map(fn($l) => [
                'id' => $l->id,
                'name' => $l->name,
                'color' => $l->color
            ])->toArray(),
        ];
    }

    public function uploadCoverImage(Request $request, Card $card)
    {
        $this->authorize('update', $card);

        $request->validate([
            'image' => [
                'required',
                'image',
                'mimes:jpg,jpeg,png,gif,webp',
                'max:5120',
            ],
        ]);

        if ($card->cover_image) {
           Storage::disk('public')
                ->delete($card->cover_image);
        }

        $path = $request->file('image')->store(
            'cover-images/card-' . $card->id,
            'public'
        );

        $card->update(['cover_image' => $path]);

        ActivityLog::log(
            $request->user(),
            'updated_card',
            "{$request->user()->name} added a cover image to '{$card->title}'",
            $card->list->board_id,
            $card->id
        );

        return response()->json([
            'success'         => true,
            'message'         => 'Cover image uploaded.',
            'cover_image_url' => $card->fresh()->cover_image_url,
        ]);
    }

    public function removeCoverImage(Request $request, Card $card)
    {
        $this->authorize('update', $card);

        if ($card->cover_image) {
            Storage::disk('public')
                ->delete($card->cover_image);
        }

        $card->update(['cover_image' => null]);

        return response()->json([
            'success' => true,
            'message' => 'Cover image removed.',
        ]);
    }

    public function uploadDescriptionImage(Request $request, Card $card)
    {
        $this->authorize('update', $card);

        $request->validate([
            'image' => [
                'required',
                'image',
                'mimes:jpg,jpeg,png,gif,webp',
                'max:5120',
            ],
        ]);

        $path = $request->file('image')->store(
            'description-images/card-' . $card->id,
            'public'
        );

        $image = CardDescriptionImage::create([
            'card_id'    => $card->id,
            'user_id'    => $request->user()->id,
            'image_path' => $path,
            'created_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'image'   => [
                'id'  => $image->id,
                'url' => $image->url,
            ],
        ]);
    }

    public function removeDescriptionImage(Request $request, Card $card, CardDescriptionImage $image)
    {
        $this->authorize('update', $card);

        Storage::disk('public')
            ->delete($image->image_path);

        $image->delete();

        return response()->json([
            'success' => true,
            'message' => 'Image removed.',
        ]);
    }
}

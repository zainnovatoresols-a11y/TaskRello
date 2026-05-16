<?php

namespace App\Services;

use App\Models\Board;
use App\Models\User;
use App\Models\ActivityLog;
use App\Models\Notification;
use App\Repositories\Contracts\BoardRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;

class BoardService
{
    public function __construct(
        private BoardRepositoryInterface $boardRepository
    ) {}

    public function getUserBoards(User $user): Collection
    {
        return $this->boardRepository->getUserBoards($user);
    }

    public function create(User $user, array $data): Board
    {
        $board = $this->boardRepository->create([
            'user_id'          => $user->id,
            'name'             => $data['name'],
            'description'      => $data['description'] ?? null,
            'background_color' => $data['background_color'] ?? '#0052CC',
        ]);

        $this->boardRepository->attachMember($board, $user->id, 'owner');

        // Auto-create board conversation
        app(\App\Services\ConversationService::class)
            ->findOrCreateBoardConversation($board);

        ActivityLog::log(
            $user,
            'created_board',
            "{$user->name} created board '{$board->name}'",
            $board->id
        );

        return $board;
    }

    public function update(Board $board, array $data): Board
    {
        return $this->boardRepository->update($board, $data);
    }

    public function delete(Board $board): void
    {
        $this->boardRepository->delete($board);
    }

    public function loadBoardWithRelations(Board $board): Board
    {
        return $this->boardRepository->loadBoardWithRelations($board);
    }

    public function loadStateRelations(Board $board): Board
    {
        return $board->load(['members', 'invitedMembers']);
    }

    public function acceptInvitation(Board $board, User $user, ?string $notificationId): void
    {
        DB::transaction(function () use ($board, $user, $notificationId) {
            $this->boardRepository->updateMemberPivot($board, $user->id, ['status' => 'accepted']);

            $notificationQuery = Notification::where('user_id', $user->id)
                ->where('type', 'board_invite')
                ->where('board_id', $board->id);

            if ($notificationId) {
                $notificationQuery->where('id', $notificationId);
            }

            $notificationQuery->delete();

            Notification::notify(
                userId: $board->user_id,
                actor: $user,
                type: 'accepted_invitation',
                message: "{$user->name} accepted the invitation to join board \"{$board->name}\"",
                boardId: $board->id,
                cardId: null,
                url: route('boards.edit', $board->id)
            );

            ActivityLog::log(
                $user,
                'accepted_invitation',
                "{$user->name} accepted the invitation to join the board",
                $board->id
            );
        });
    }

    public function declineInvitation(Board $board, User $user, ?string $notificationId): void
    {
        DB::transaction(function () use ($board, $user, $notificationId) {
            $this->boardRepository->detachMember($board, $user->id);

            // Remove from board conversation
            app(\App\Services\ConversationService::class)
                ->syncBoardParticipant($board, $user, 'remove');

            $notificationQuery = Notification::where('user_id', $user->id)
                ->where('type', 'board_invite')
                ->where('board_id', $board->id);

            if ($notificationId) {
                $notificationQuery->where('id', $notificationId);
            }

            $notificationQuery->delete();

            ActivityLog::log(
                $user,
                'declined_invitation',
                "{$user->name} declined the invitation to join the board",
                $board->id
            );
        });
    }

    public function uploadBackgroundImage(Board $board, UploadedFile $image, User $user): Board
    {
        if ($board->background_image) {
            Storage::disk('public')->delete($board->background_image);
        }

        $path = $image->store('board-backgrounds/board-' . $board->id, 'public');

        $this->boardRepository->uploadBackgroundImage($board, $path);

        ActivityLog::log(
            $user,
            'updated_board',
            "{$user->name} updated the background image of '{$board->name}'",
            $board->id
        );

        return $board->fresh();
    }

    public function removeBackgroundImage(Board $board): void
    {
        if ($board->background_image) {
            Storage::disk('public')->delete($board->background_image);
        }

        $this->boardRepository->removeBackgroundImage($board);
    }

    public function addMember(Board $board, User $actor, User $user, Request $request): mixed
    {
        if ($board->isMember($user)) {
            if ($request->wantsJson()) {
                return response()->json([
                    'status'  => 'error',
                    'message' => "{$user->name} is already a member of this board.",
                ], 409);
            }

            return redirect()
                ->route('boards.edit', $board)
                ->with('error', "{$user->name} is already a member of this board.");
        }

        if ($board->hasPendingInvite($user)) {
            if ($request->wantsJson()) {
                return response()->json([
                    'status'  => 'error',
                    'message' => "An invitation has already been sent to {$user->name}.",
                ], 409);
            }

            return redirect()
                ->route('boards.edit', $board)
                ->with('error', "An invitation has already been sent to {$user->name}.");
        }

        // Action Logic
        DB::transaction(function () use ($board, $actor, $user) {
            $this->boardRepository->attachMember($board, $user->id, 'member', 'pending');

            // Sync new member to board conversation
            app(\App\Services\ConversationService::class)
                ->syncBoardParticipant($board, $user, 'add');

            Notification::notify(
                userId: $user->id,
                actor: $actor,
                type: 'board_invite',
                message: "{$actor->name} invited you to join board \"{$board->name}\"",
                boardId: $board->id,
                cardId: null,
                url: route('boards.invitations.accept', $board->id)
            );

            ActivityLog::log(
                $actor,
                'invited_member',
                "{$actor->name} invited {$user->name} to the board",
                $board->id
            );
        });

        return null;
    }

    public function removeMember(Board $board, User $actor, User $user, Request $request): mixed
    {
        $wasPendingInvite = $board->hasPendingInvite($user);

        if ($board->user_id === $user->id) {
            if ($request->wantsJson()) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'You cannot remove the board owner.',
                ], 403);
            }

            return redirect()
                ->route('boards.edit', $board)
                ->with('error', 'You cannot remove the board owner.');
        }

        if ($request->user()->id === $user->id) {
            if ($request->wantsJson()) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'You cannot remove yourself from the board.',
                ], 403);
            }

            return redirect()
                ->route('boards.edit', $board)
                ->with('error', 'You cannot remove yourself from the board.');
        }

        DB::transaction(function () use ($board, $actor, $user, $wasPendingInvite) {

            $this->boardRepository->unassignFromAllCards($board, $user->id);
            $this->boardRepository->detachMember($board, $user->id);

            // Remove from board conversation
            app(\App\Services\ConversationService::class)
                ->syncBoardParticipant($board, $user, 'remove');

            if ($wasPendingInvite) {
                Notification::where('user_id', $user->id)
                    ->where('type', 'board_invite')
                    ->where('board_id', $board->id)
                    ->delete();
            } else {
                Notification::notify(
                    userId: $user->id,
                    actor: $actor,
                    type: 'removed_from_board',
                    message: "{$actor->name} removed you from the board \"{$board->name}\"",
                    boardId: $board->id,
                    cardId: null,
                    url: route('boards.index')
                );

                ActivityLog::log(
                    $actor,
                    'removed_member',
                    "{$actor->name} removed {$user->name} from the board",
                    $board->id
                );
            }
        });

        return null;
    }
}
<?php

namespace App\Services;

use App\Models\Board;
use App\Models\User;
use App\Models\ActivityLog;
use App\Models\Notification;
use App\Repositories\Contracts\BoardRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
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
            'user_id'=> $user->id,
            'name'=> $data['name'],
            'description'=> $data['description'] ?? null,
            'background_color'=> $data['background_color'] ?? '#0052CC',
        ]);

        $this->boardRepository->attachMember($board, $user->id, 'owner');

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

    public function addMember(Board $board, User $actor, User $user, Request $request): mixed
    {
        if ($board->isMember($user)) {
            if ($request->wantsJson()) {
                return response()->json([
                    'status' => 'error',
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
                    'status' => 'error',
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

        if ($board->user_id === $user->id) {
            if ($request->wantsJson()) {
                return response()->json([
                    'status' => 'error',
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
                    'status' => 'error',
                    'message' => 'You cannot remove yourself from the board.',
                ], 403);
            }

            return redirect()
                ->route('boards.edit', $board)
                ->with('error', 'You cannot remove yourself from the board.');
        }

        DB::transaction(function () use ($board, $actor, $user) {

            $this->boardRepository->unassignFromAllCards($board, $user->id);
            $this->boardRepository->detachMember($board, $user->id);

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
        });

        return null;
    }
}


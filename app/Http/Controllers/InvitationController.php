<?php

namespace App\Http\Controllers;

use App\Models\BoardInvitation;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\Request;

class InvitationController extends Controller
{
    public function accept(Request $request, string $token)
    {
        $invitation = BoardInvitation::findValid($token);

        if (!$invitation) {
            return redirect()
                ->route('boards.index')
                ->with('error', 'This invitation is invalid or has expired.');
        }

        $user  = $request->user();
        $board = $invitation->board;

        if ($user->email !== $invitation->email) {
            return redirect()
                ->route('boards.index')
                ->with('error', 'This invitation was sent to a different email address.');
        }

        if ($board->isMember($user)) {
            $invitation->update(['status' => 'accepted']);
            return redirect()
                ->route('boards.show', $board)
                ->with('success', "You are already a member of \"{$board->name}\".");
        }

        $board->members()->attach($user->id, ['role' => 'member']);
        $invitation->update(['status' => 'accepted']);

        return redirect()
            ->route('boards.show', $board)
            ->with('success', "Welcome to \"{$board->name}\"! You have joined successfully.");
    }

    public static function acceptPendingForUser(User $user): void
    {
        $pending = BoardInvitation::getPendingForEmail($user->email);

        foreach ($pending as $invitation) {
            $board = $invitation->board;
            if (!$board) continue;

            if (!$board->isMember($user)) {
                $board->members()->attach($user->id, ['role' => 'member']);
            }

            $invitation->update(['status' => 'accepted']);
        }
    }
}

<?php

use App\Models\Conversation;
use App\Models\ConversationParticipant;
use App\Models\User;
use Illuminate\Support\Facades\Broadcast;

// ── Private conversation channel ─────────────────────────────
// Used for ALL conversation types: direct, group, board
// Authorization: user must be a participant
Broadcast::channel('conversation.{conversationId}', function (User $user, int $conversationId) {
    return ConversationParticipant::where('conversation_id', $conversationId)
                                  ->where('user_id', $user->id)
                                  ->exists();
});

// ── Private personal channel ──────────────────────────────────
// Used for: new conversation notifications, unread count updates
// Authorization: only the user themselves
Broadcast::channel('user.{userId}', function (User $user, int $userId) {
    return $user->id === $userId;
});
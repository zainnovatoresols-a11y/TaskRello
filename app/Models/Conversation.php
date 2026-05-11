<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Conversation extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'name',
        'board_id',
        'created_by',
        'last_message_at',
    ];

    protected $casts = [
        'last_message_at' => 'datetime',
    ];

    // ── Relationships ─────────────────────────────────────────

    public function participants()
    {
        return $this->hasMany(ConversationParticipant::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'conversation_participants')
                    ->withPivot(['role', 'last_read_at', 'joined_at', 'is_muted'])
                    ->withTimestamps();
    }

    public function messages()
    {
        return $this->hasMany(Message::class)
                    ->orderBy('created_at');
    }

    public function lastMessage()
    {
        return $this->hasOne(Message::class)
                    ->latestOfMany();
    }

    public function board()
    {
        return $this->belongsTo(Board::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // ── Helper Methods ────────────────────────────────────────

    // Check if a user is a participant
    public function hasParticipant(User $user): bool
    {
        return $this->participants()
                    ->where('user_id', $user->id)
                    ->exists();
    }

    // Get unread count for a specific user
    public function unreadCountFor(User $user): int
    {
        $participant = $this->participants()
                            ->where('user_id', $user->id)
                            ->first();

        if (!$participant || !$participant->last_read_at) {
            return $this->messages()->count();
        }

        return $this->messages()
                    ->where('created_at', '>', $participant->last_read_at)
                    ->count();
    }

    // Get display name based on type
    public function getDisplayNameFor(User $user): string
    {
        if ($this->type === 'direct') {
            // For direct chats show the OTHER person's name
            $other = $this->users()
                          ->where('users.id', '!=', $user->id)
                          ->first();
            return $other?->name ?? 'Unknown';
        }

        return $this->name ?? 'Unnamed conversation';
    }

    // Static — find existing direct conversation between two users
    public static function findDirect(int $userAId, int $userBId): ?self
    {
        return self::where('type', 'direct')
                   ->whereHas('participants', function ($q) use ($userAId) {
                       $q->where('user_id', $userAId);
                   })
                   ->whereHas('participants', function ($q) use ($userBId) {
                       $q->where('user_id', $userBId);
                   })
                   ->first();
    }
}
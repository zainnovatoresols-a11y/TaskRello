<?php

namespace App\Models;

use App\Models\User;
use App\Models\BoardList;
use App\Models\Comment;
use App\Models\Attachment;
use App\Models\Label;
use App\Models\ActivityLog;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use CarbonCarbon;

class Card extends Model
{
    use HasFactory;

    protected $fillable = [
        'list_id',
        'user_id',
        'title',
        'description',
        'position',
        'due_date',
        'cover_color',
        'is_archived',
    ];

    protected $casts = [
        'is_archived' => 'boolean',
        'due_date'    => 'date',
    ];

    // ─── Relationships ───────────────────────────────

    // The list (column) this card belongs to
    public function list()
    {
        return $this->belongsTo(BoardList::class, 'list_id');
    }

    // The user who created this card
    public function creator()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Users assigned to this card
    public function assignees()
    {
        return $this->belongsToMany(User::class, 'card_user')
            ->withTimestamps();
    }

    // Labels attached to this card
    public function labels()
    {
        return $this->belongsToMany(Label::class, 'card_label');
    }

    // Comments on this card, newest last
    public function comments()
    {
        return $this->hasMany(Comment::class)
            ->orderBy('created_at');
    }

    public function attachments()
    {
        return $this->hasMany(Attachment::class);
    }

    public function activityLogs()
    {
        return $this->hasMany(ActivityLog::class);
    }

    // ─── Helper Methods ──────────────────────────────

    // Is this card past its due date?
    public function isOverdue(): bool
    {
        return $this->due_date
            && $this->due_date->isPast()
            && !$this->is_archived;
    }

    // Is the due date within the next 24 hours?
    public function isDueSoon(): bool
    {
        return $this->due_date
            && $this->due_date->isToday()
            && !$this->isOverdue();
    }

    // Is a specific user assigned to this card?
    public function isAssignedTo(User $user): bool
    {
        return $this->assignees()
            ->where('user_id', $user->id)
            ->exists();
    }
}

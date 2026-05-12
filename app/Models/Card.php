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
        'cover_image',
        'is_archived',
        'is_completed',
    ];

    protected $casts = [
        'is_archived'  => 'boolean',
        'is_completed' => 'boolean',
        'due_date'     => 'date',
    ];

    protected $appends = ['cover_image_url', 'total_time_seconds', 'total_time_formatted',];

    public function getCoverImageUrlAttribute(): ?string
    {
        return $this->cover_image
            ? asset('storage/' . $this->cover_image)
            : null;
    }

    public function list()
    {
        return $this->belongsTo(BoardList::class, 'list_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function assignees()
    {
        return $this->belongsToMany(User::class, 'card_user')->withTimestamps();
    }

    public function labels()
    {
        return $this->belongsToMany(Label::class, 'card_label');
    }

    public function comments()
    {
        return $this->hasMany(Comment::class)->orderBy('created_at');
    }

    public function attachments()
    {
        return $this->hasMany(Attachment::class);
    }

    public function activityLogs()
    {
        return $this->hasMany(ActivityLog::class);
    }

    public function isOverdue(): bool
    {
        return $this->due_date
            && $this->due_date->isPast()
            && !$this->is_archived;
    }

    public function isDueSoon(): bool
    {
        return $this->due_date
            && $this->due_date->isToday()
            && !$this->isOverdue();
    }

    public function isAssignedTo(User $user): bool
    {
        return $this->assignees()->where('user_id', $user->id)->exists();
    }

    public function descriptionImages()
    {
        return $this->hasMany(CardDescriptionImage::class)
            ->latest('created_at');
    }

    public function timeLogs()
    {
        return $this->hasMany(CardTimeLog::class)
            ->orderByDesc('started_at');
    }

    public function completedTimeLogs()
    {
        return $this->hasMany(CardTimeLog::class)
            ->whereNotNull('ended_at')
            ->orderByDesc('started_at');
    }

    public function activeTimeLogs()
    {
        return $this->hasMany(CardTimeLog::class)
            ->whereNull('ended_at');
    }

    public function getTotalTimeSecondsAttribute(): int
    {
        return (int) $this->completedTimeLogs()->sum('duration');
    }

    public function getTotalTimeFormattedAttribute(): string
    {
        $seconds = $this->total_time_seconds;

        if ($seconds <= 0) {
            return '0m';
        }

        return CardTimeLog::formatSeconds($seconds);
    }

    public function hasActiveSessionFor(User $user): bool
    {
        return $this->activeTimeLogs()
            ->where('user_id', $user->id)
            ->exists();
    }

    public function getActiveSessionFor(User $user): ?CardTimeLog
    {
        return $this->activeTimeLogs()
            ->where('user_id', $user->id)
            ->first();
    }
}

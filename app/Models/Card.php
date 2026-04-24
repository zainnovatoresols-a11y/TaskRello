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

    protected $appends = ['cover_image_url']; // ← removed 'url' and 'is_image'

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
}

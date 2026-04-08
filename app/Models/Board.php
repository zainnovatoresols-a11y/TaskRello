<?php

namespace App\Models;

use App\Models\User;
use App\Models\BoardList;
use App\Models\Label;
use App\Models\ActivityLog;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Board extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'description',
        'background_color',
        'is_archived',
    ];

    protected $casts = [
        'is_archived' => 'boolean',
    ];

    // ─── Relationships ───────────────────────────────

    // The user who owns/created this board
    public function owner()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // All members of this board (many-to-many)
    public function members()
    {
        return $this->belongsToMany(User::class)
            ->withPivot('role')
            ->withTimestamps();
    }

    // All lists (columns) on this board, ordered by position
    public function lists()
    {
        return $this->hasMany(BoardList::class)
            ->orderBy('position');
    }

    // Labels that belong to this board
    public function labels()
    {
        return $this->hasMany(Label::class);
    }

    // All activity logs for this board
    public function activityLogs()
    {
        return $this->hasMany(ActivityLog::class);
    }

    // ─── Helper Methods ──────────────────────────────

    // Check if a given user is a member of this board
    public function isMember(User $user): bool
    {
        return $this->members()
            ->where('user_id', $user->id)
            ->exists();
    }

    // Check if a given user is the owner
    public function isOwner(User $user): bool
    {
        return $this->user_id === $user->id;
    }
}

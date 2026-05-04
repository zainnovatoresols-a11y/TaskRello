<?php

namespace App\Models;

use App\Models\User;
use App\Models\BoardList;
use App\Models\Label;
use App\Models\ActivityLog;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Storage;

class Board extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'description',
        'background_color',
         'background_image',
        'is_archived',
    ];

    protected $casts = [
        'is_archived' => 'boolean',
    ];

    protected $appends = ['background_image_url'];

    public function getBackgroundImageUrlAttribute(): ?string
    {
        return $this->background_image
            ? Storage::url($this->background_image)
            : null;
    }

    public function owner()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function members()
    {
        return $this->belongsToMany(User::class)
            ->withPivot('role')
            ->withTimestamps();
    }

    public function lists()
    {
        return $this->hasMany(BoardList::class)
            ->orderBy('position');
    }

    public function labels()
    {
        return $this->hasMany(Label::class);
    }

    public function activityLogs()
    {
        return $this->hasMany(ActivityLog::class);
    }

    public function isMember(User $user): bool
    {
        return $this->members()
            ->where('user_id', $user->id)
            ->exists();
    }

    public function isOwner(User $user): bool
    {
        return $this->user_id === $user->id;
    }
}

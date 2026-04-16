<?php

namespace App\Models;

use App\Models\Board;
use App\Models\Card;
use App\Models\Comment;
use App\Models\Attachment;
use App\Models\ActivityLog;
use Laravel\Sanctum\HasApiTokens;


use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function ownedBoards()
    {
        return $this->hasMany(Board::class);
    }

    public function boards()
    {
        return $this->belongsToMany(Board::class)
            ->withPivot('role')
            ->withTimestamps();
    }

    public function assignedCards()
    {
        return $this->belongsToMany(Card::class, 'card_user')
            ->withTimestamps();
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function attachments()
    {
        return $this->hasMany(Attachment::class);
    }

    public function activityLogs()
    {
        return $this->hasMany(ActivityLog::class);
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class)
            ->orderByDesc('created_at');
    }

    public function unreadNotificationsCount(): int
    {
        return $this->notifications()
            ->where('is_read', false)
            ->count();
    }
}

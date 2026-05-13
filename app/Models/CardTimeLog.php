<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CardTimeLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'card_id',
        'user_id',
        'started_at',
        'ended_at',
        'duration',
        'notes',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'ended_at'   => 'datetime',
        'duration'   => 'integer',
    ];

    protected $appends = [
        'duration_formatted',
        'is_running',
    ];


    public function card()
    {
        return $this->belongsTo(Card::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getIsRunningAttribute(): bool
    {
        return is_null($this->ended_at);
    }

    public function getDurationFormattedAttribute(): string
    {
        $seconds = $this->duration;

        if ($this->is_running) {
            $seconds = now()->diffInSeconds($this->started_at);
        }

        if (!$seconds || $seconds <= 0) {
            return '0m';
        }

        return self::formatSeconds($seconds);
    }

    public function getElapsedSecondsAttribute(): int
    {
        if ($this->is_running) {
            return (int) now()->diffInSeconds($this->started_at);
        }

        return $this->duration ?? 0;
    }

    public static function formatSeconds(int $seconds): string
    {
        if ($seconds < 60) {
            return $seconds . 's';
        }

        $hours   = intdiv($seconds, 3600);
        $minutes = intdiv($seconds % 3600, 60);
        $seconds = $seconds % 60;

        $parts = [];

        if ($hours > 0) {
            $parts[] = $hours . 'h';
        }

        if ($minutes > 0) {
            $parts[] = $minutes . 'm';
        }

        if ($seconds > 0 || empty($parts)) {
            $parts[] = $seconds . 's';
        }

        return implode(' ', $parts);
    }

    public static function getActiveSession(User $user): ?self
    {
        return static::where('user_id', $user->id)
                     ->whereNull('ended_at')
                     ->latest('started_at')
                     ->first();
    }

    public static function getActiveSessionForCard(
        User $user,
        Card $card
    ): ?self {
        return static::where('user_id', $user->id)
                     ->where('card_id', $card->id)
                     ->whereNull('ended_at')
                     ->latest('started_at')
                     ->first();
    }
}
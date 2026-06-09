<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class BoardInvitation extends Model
{
    protected $fillable = [
        'board_id',
        'invited_by',
        'email',
        'token',
        'status',
        'expires_at',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
    ];

    // ── Relationships ─────────────────────────────────────────

    public function board()
    {
        return $this->belongsTo(Board::class);
    }

    public function inviter()
    {
        return $this->belongsTo(User::class, 'invited_by');
    }

    // ── Helpers ───────────────────────────────────────────────

    public function isPending(): bool
    {
        return $this->status === 'pending'
            && $this->expires_at->isFuture();
    }

    public static function generateToken(): string
    {
        return Str::random(40) . time();
    }

    public static function findValid(string $token): ?self
    {
        return static::where('token', $token)
                     ->where('status', 'pending')
                     ->where('expires_at', '>', now())
                     ->with(['board', 'inviter'])
                     ->first();
    }

    public static function getPendingForEmail(string $email)
    {
        return static::where('email', $email)
                     ->where('status', 'pending')
                     ->where('expires_at', '>', now())
                     ->with(['board', 'inviter'])
                     ->get();
    }
}
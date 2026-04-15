<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    public $timestamps    = false;
    const CREATED_AT      = 'created_at';

    protected $fillable = [
        'user_id',
        'actor_id',
        'board_id',
        'card_id',
        'type',
        'message',
        'url',
        'is_read',
    ];

    protected $casts = [
        'is_read'    => 'boolean',
        'created_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function actor()
    {
        return $this->belongsTo(User::class, 'actor_id');
    }

    public function board()
    {
        return $this->belongsTo(Board::class);
    }

    public function card()
    {
        return $this->belongsTo(Card::class);
    }

    public static function notify(
        int     $userId,
        User    $actor,
        string  $type,
        string  $message,
        ?int    $boardId = null,
        ?int    $cardId  = null,
        ?string $url     = null
    ): self {
        if ($userId === $actor->id) {
            return new self();
        }

        return static::create([
            'user_id'    => $userId,
            'actor_id'   => $actor->id,
            'board_id'   => $boardId,
            'card_id'    => $cardId,
            'type'       => $type,
            'message'    => $message,
            'url'        => $url,
            'is_read'    => false,
            'created_at' => now(),
        ]);
    }
}

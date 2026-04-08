<?php

namespace App\Models;

use App\Models\Board;
use App\Models\Card;
use App\Models\User;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ActivityLog extends Model
{
    use HasFactory;

    // Logs are immutable — only created_at, never updated_at
    public $timestamps    = false;
    const CREATED_AT      = 'created_at';

    protected $fillable = [
        'user_id',
        'board_id',
        'card_id',
        'action',
        'description',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    // ─── Relationships ───────────────────────────────

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Nullable — some logs are board-level only
    public function board()
    {
        return $this->belongsTo(Board::class);
    }

    // Nullable — some logs are board-level only
    public function card()
    {
        return $this->belongsTo(Card::class);
    }

    // ─── Static Helper ───────────────────────────────

    // Call from any controller: ActivityLog::log(...)
    // Example:
    //   ActivityLog::log($user, 'moved_card',
    //       "{$user->name} moved '{$card->title}' to '{$list->name}'",
    //       $board->id, $card->id);
    public static function log(
        User   $user,
        string $action,
        string $description,
        ?int   $boardId = null,
        ?int   $cardId  = null
    ): self {
        return static::create([
            'user_id'     => $user->id,
            'board_id'    => $boardId,
            'card_id'     => $cardId,
            'action'      => $action,
            'description' => $description,
            'created_at'  => now(),
        ]);
    }
}

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


    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function board()
    {
        return $this->belongsTo(Board::class);
    }

    public function card()
    {
        return $this->belongsTo(Card::class);
    }

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

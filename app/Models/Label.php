<?php

namespace App\Models;

use App\Models\Board;
use App\Models\Card;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Label extends Model
{
    use HasFactory;

    protected $fillable = [
        'board_id',
        'name',
        'color',
    ];

    // ─── Relationships ───────────────────────────────

    public function board()
    {
        return $this->belongsTo(Board::class);
    }

    // Cards that have this label applied
    public function cards()
    {
        return $this->belongsToMany(Card::class, 'card_label');
    }
}

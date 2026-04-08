<?php

namespace App\Models;

use App\Models\Board;
use App\Models\Card;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BoardList extends Model
{
    use HasFactory;

    // CRITICAL: override the table name because the class
    // cannot be named 'List' (PHP reserved keyword)
    protected $table = 'lists';

    protected $fillable = [
        'board_id',
        'name',
        'position',
        'is_archived',
    ];

    protected $casts = [
        'is_archived' => 'boolean',
    ];

    // ─── Relationships ───────────────────────────────

    public function board()
    {
        return $this->belongsTo(Board::class);
    }

    // Cards inside this list, always ordered by position
    public function cards()
    {
        return $this->hasMany(Card::class, 'list_id')
            ->orderBy('position');
    }

    // ─── Scopes ──────────────────────────────────────

    // Usage: BoardList::active()->get()
    public function scopeActive($query)
    {
        return $query->where('is_archived', false);
    }
}

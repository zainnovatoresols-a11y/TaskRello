<?php

namespace App\Models;

use App\Models\Board;
use App\Models\Card;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BoardList extends Model
{
    use HasFactory;

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


    public function board()
    {
        return $this->belongsTo(Board::class);
    }

    public function cards()
    {
        return $this->hasMany(Card::class, 'list_id')
            ->orderBy('position');
    }

    public function scopeActive($query)
    {
        return $query->where('is_archived', false);
    }
}

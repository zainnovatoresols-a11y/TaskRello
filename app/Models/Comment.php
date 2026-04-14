<?php

namespace App\Models;

use App\Models\Card;
use App\Models\User;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Comment extends Model
{
    use HasFactory;

    protected $fillable = [
        'card_id',
        'user_id',
        'body',
    ];

    public function card()
    {
        return $this->belongsTo(Card::class);
    }

    public function author()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function isAuthor(User $user): bool
    {
        return $this->user_id === $user->id;
    }
}

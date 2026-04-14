<?php

namespace App\Models;

use App\Models\Card;
use App\Models\User;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Storage;

class Attachment extends Model
{
    use HasFactory;

    protected $fillable = [
        'card_id',
        'user_id',
        'filename',
        'file_path',
        'file_size',
        'mime_type',
    ];

    protected $appends = ['url', 'is_image'];

    public function card()
    {
        return $this->belongsTo(Card::class);
    }

    public function uploader()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function getUrlAttribute(): string
    {
        return Storage::url($this->file_path);
    }

    public function getIsImageAttribute(): bool
    {
        return str_starts_with($this->mime_type, 'image/');
    }
}

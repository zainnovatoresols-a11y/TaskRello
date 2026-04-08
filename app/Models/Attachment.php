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

    // Auto-append these computed properties to JSON output
    protected $appends = ['url', 'is_image'];

    // ─── Relationships ───────────────────────────────

    public function card()
    {
        return $this->belongsTo(Card::class);
    }

    // Use 'uploader' for clarity — who uploaded this file
    public function uploader()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // ─── Accessors ───────────────────────────────────

    // Returns the full public URL to the file
    // Usage in Vue: attachment.url
    public function getUrlAttribute(): string
    {
        return Storage::url($this->file_path);
    }

    // Returns true if this attachment is an image
    // Usage in Vue: attachment.is_image (shows preview)
    public function getIsImageAttribute(): bool
    {
        return str_starts_with($this->mime_type, 'image/');
    }
}

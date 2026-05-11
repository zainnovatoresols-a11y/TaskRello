<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class Message extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'conversation_id',
        'user_id',
        'body',
        'type',
        'attachment_path',
        'attachment_name',
        'attachment_size',
        'reply_to_id',
        'is_edited',
    ];

    protected $casts = [
        'is_edited'       => 'boolean',
        'attachment_size' => 'integer',
    ];

    protected $appends = ['attachment_url', 'is_deleted'];

    // ── Relationships ─────────────────────────────────────────

    public function conversation()
    {
        return $this->belongsTo(Conversation::class);
    }

    public function sender()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function replyTo()
    {
        return $this->belongsTo(Message::class, 'reply_to_id')
                    ->withTrashed();
        // withTrashed so reply context still shows
        // even if original message was deleted
    }

    public function reads()
    {
        return $this->hasMany(MessageRead::class);
    }

    // ── Accessors ─────────────────────────────────────────────

    public function getAttachmentUrlAttribute(): ?string
    {
        return $this->attachment_path
            ? Storage::url($this->attachment_path)
            : null;
    }

    public function getIsDeletedAttribute(): bool
    {
        return $this->trashed();
    }

    // ── Helper Methods ────────────────────────────────────────

    public function isReadBy(User $user): bool
    {
        return $this->reads()
                    ->where('user_id', $user->id)
                    ->exists();
    }

    // Format file size for display (e.g. "2.4 MB")
    public function getFormattedSizeAttribute(): ?string
    {
        if (!$this->attachment_size) return null;

        $bytes = $this->attachment_size;

        if ($bytes < 1024)        return $bytes . ' B';
        if ($bytes < 1048576)     return round($bytes / 1024, 1) . ' KB';
        return round($bytes / 1048576, 1) . ' MB';
    }
}
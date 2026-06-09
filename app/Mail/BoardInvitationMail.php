<?php

namespace App\Mail;

use App\Models\Board;
use App\Models\BoardInvitation;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class BoardInvitationMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public BoardInvitation $invitation,
        public Board           $board,
        public User            $inviter,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "{$this->inviter->name} invited you to \"{$this->board->name}\" on " . config('app.name'),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.board-invitation',
        );
    }
}
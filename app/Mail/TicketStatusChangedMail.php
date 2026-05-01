<?php

namespace App\Mail;

use App\Models\Ticket;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TicketStatusChangedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Ticket $ticket,
        public string $oldStatus,
        public string $newStatus
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "[MISE À JOUR] {$this->ticket->ticket_number} - Statut : {$this->ticket->status_label}"
        );
    }

    public function content(): Content
    {
        return new Content(view: 'emails.ticket-status-changed');
    }

    public function attachments(): array { return []; }
}

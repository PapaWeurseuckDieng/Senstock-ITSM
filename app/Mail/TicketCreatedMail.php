<?php

namespace App\Mail;

use App\Models\Ticket;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TicketCreatedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Ticket $ticket) {}

    public function envelope(): Envelope
    {
        $subject = match($this->ticket->type) {
            'panne'    => "[PANNE] {$this->ticket->ticket_number} - {$this->ticket->title}",
            'incident' => "[INCIDENT] {$this->ticket->ticket_number} - {$this->ticket->title}",
            default    => "[NOUVEAU] {$this->ticket->ticket_number} - {$this->ticket->title}",
        };

        return new Envelope(subject: $subject);
    }

    public function content(): Content
    {
        return new Content(view: 'emails.ticket-created');
    }

    public function attachments(): array
    {
        return [];
    }
}

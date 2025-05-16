<?php

namespace App\Mail;

use App\Models\Reminder;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ReminderEmail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public User $user,
        public Reminder $reminder
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Reminder: ' . $this->reminder->card->title,
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.reminders.notification',
            with: [
                'title' => $this->reminder->card->title,
                'description' => $this->reminder->card->description ?? 'You have a reminder!',
                'url' => config('app.frontend_url') . '/cards/' . $this->reminder->card_id,
                'user' => $this->user,
            ]
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
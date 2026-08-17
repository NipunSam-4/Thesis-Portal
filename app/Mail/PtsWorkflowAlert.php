<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PtsWorkflowAlert extends Mailable
{
    use Queueable, SerializesModels;

    public string $emailSubject;
    public string $title;
    public string $messageContent;
    public string $recipientName;
    public string $senderDeskName;
    public array $details;
    public ?string $actionUrl;
    public ?string $actionText;
    public string $mailView;
    public array $viewData;

    /**
     * Create a new message instance.
     */
    public function __construct(
        string $subject,
        string $title = '',
        string $messageContent = '',
        string $recipientName = 'User',
        string $senderDeskName = 'PhD Thesis Portal System',
        array $details = [],
        ?string $actionUrl = null,
        ?string $actionText = null,
        string $view = 'emails.pts_alert',
        array $viewData = []
    ) {
        $this->emailSubject = $subject;
        $this->title = $title;
        $this->messageContent = $messageContent;
        $this->recipientName = $recipientName;
        $this->senderDeskName = $senderDeskName;
        $this->details = $details;
        $this->actionUrl = $actionUrl;
        $this->actionText = $actionText;
        $this->mailView = $view;
        $this->viewData = $viewData;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->emailSubject,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        $defaultWith = [
            'subject' => $this->emailSubject,
            'title' => $this->title,
            'messageContent' => $this->messageContent,
            'recipientName' => $this->recipientName,
            'senderDeskName' => $this->senderDeskName,
            'details' => $this->details,
            'actionUrl' => $this->actionUrl,
            'actionText' => $this->actionText,
        ];

        return new Content(
            view: $this->mailView,
            with: array_merge($defaultWith, $this->viewData),
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}

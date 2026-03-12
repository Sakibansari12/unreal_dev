<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Attachment;

class SendAgreementToPm extends Mailable
{
    use Queueable, SerializesModels;

    public $mailData;

    public function __construct($data)
    {
        $this->mailData = $data['mailData'];
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Agreement || Unreal Estate',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'pms.emails.agreement-pm',
            with: [
                'data' => $this->mailData,
            ],
        );
    }

    public function attachments(): array
    {
        if (!empty($this->mailData->file_pdf)) {
            $path = public_path('storage/' . $this->mailData->file_pdf);
            if (file_exists($path)) {
                return [
                    Attachment::fromPath($path)
                        ->as('Agreement.pdf')
                        ->withMime('application/pdf'),
                ];
            }
        }
        return [];
    }
}

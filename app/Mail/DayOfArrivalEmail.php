<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\TblHomeImageVideo;

class DayOfArrivalEmail extends Mailable{
    use Queueable, SerializesModels;

    public $mailData;
    public $type;

    /**
     * Create a new message instance.
     */
    public function __construct($data){
        $this->mailData = $data['mailData'];
        $this->type = $data['type'];
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope{
        return new Envelope(
            subject: 'Unreal Estate – See you soon at your vacation home',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content{
        return new Content(
            view: 'pms.emails.day-of-arrival',
            with: [
                'data' => $this->mailData,
                'type' =>$this->type
            ],
        );
        die();
    }

    /**
     * Get the attachments for the message.
     *
     * @return array

     */
    public function attachments(): array{
        return [];
    }
}

<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PropertylistEmail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    protected $data;
    public $locationTypes;
    public $propertyTypes;
    public function __construct($data, $locationTypes, $propertyTypes)
    {
        $this->data = $data;
        $this->locationTypes = $locationTypes;
        $this->propertyTypes = $propertyTypes;
    }
    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Property List Request',
        );
    }

    /**
     * Get the message content definition.
     */
    public function build()
    {
        return $this->view('website.emails.list-your-property')
            ->with([
                'data' => $this->data,
                'locationTypes' => $this->locationTypes,
                'propertyTypes' => $this->propertyTypes,
            ]);
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

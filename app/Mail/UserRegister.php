<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class UserRegister extends Mailable
{
    use Queueable, SerializesModels;

    public $email;
    public $password;
    public $name;

    public function __construct($password, $email, $name)
    {
        $this->password = $password;
        $this->email = $email;
        $this->name = $name;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Unreal Estate Registration',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'website.mail.userregister',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}

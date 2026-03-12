<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ForgotPassword extends Mailable
{
    use Queueable, SerializesModels;

    public $email;
    public $password;
    public $name;

    /**
     * Create a new message instance.
     */
    public function __construct($password,$email,$name)
    {
        $this->password = $password;
        $this->email = $email;
        $this->name = $name;
    }

    // public function build()
    // {
    //     return $this->view('mail.forgotpassword')
    //             ->with([
    //                 'email' => $this->email,
    //                 'name' => $this->name,
    //                 'password' => $this->password,
    //             ]);
    // }
    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Unreal Estate New Login Password',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'website.mail.forgotpassword',
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

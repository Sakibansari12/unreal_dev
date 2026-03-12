<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;


class ForgotPasswordEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $token;
    public $email;

    public function __construct($token, $email)
    {
        $this->token = $token;
        $this->email = $email;
    }

    public function build()
    {
        $resetUrl = url('/pms/password/reset/' . $this->token . '?email=' . urlencode($this->email));

        return $this->subject('Reset Your Password')
            ->view('pms.emails.forgot-password')
            ->with([
                'resetUrl' => $resetUrl,
            ]);
    }
}
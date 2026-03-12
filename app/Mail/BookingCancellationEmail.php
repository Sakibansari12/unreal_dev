<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\TblHomeMultiUnit;
use App\Models\TblHomeUnit;
use App\Models\TblHomeImageVideo;

class BookingCancellationEmail extends Mailable{
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
            subject: 'Booking Cancellation From Unreal Estate [Your Booking Refrence Number Is- '.$this->mailData->booking_id.']',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content{
        
        $property = ($this->mailData->pType === 'unit')
        ? TblHomeUnit::with(['images', 'locationData'])->where('id', $this->mailData->property_id)->first()
        : TblHomeMultiUnit::with(['images', 'locationData'])->where('id', $this->mailData->property_id)->first();
       
      $customerDetail = is_array($this->mailData->customer_detail) 
      ? $this->mailData->customer_detail 
      : json_decode($this->mailData->customer_detail, true);


        return new Content(
            view: 'pms.emails.booking-cancellation',
            with: [
                'data' => $this->mailData,
                'customerDetail' => $customerDetail,
                'prorperty'=>$property
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

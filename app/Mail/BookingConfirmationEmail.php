<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\TblHomeImageVideo;
use App\Models\TblHomeMultiUnit;
use App\Models\TblHomeUnit;
use App\Models\TblHome;

class BookingConfirmationEmail extends Mailable{
    use Queueable, SerializesModels;

    public $mailData;

    /**
     * Create a new message instance.
     */
    public function __construct($mailData){
        $this->mailData = $mailData['bookingDetail'];
        $this->id = $mailData['id'];
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope{
        if($this->id ==''){
            return new Envelope(
                subject: 'Booking Confirmation From Unreal Estate [Your Booking Refrence Number Is- '.$this->mailData->booking_id.']',
            );
        }
        else{
            return new Envelope(
                subject: 'Booking Confirmation From Unreal Estate [Your Booking Refrence Number Is- '.$this->mailData->booking_id.']',
            );
        }
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content{
        $property = ($this->mailData->pType === 'unit')
        ? TblHomeUnit::with(['imagesWebsite', 'locationData'])->where('id', $this->mailData->property_id)->first()
        : TblHomeMultiUnit::with(['imagesWebsite', 'locationData'])->where('id', $this->mailData->property_id)->first();
       
      $customerDetail = is_array($this->mailData->customer_detail) 
      ? $this->mailData->customer_detail 
      : json_decode($this->mailData->customer_detail, true);
     // dd($property->image_full_path);
        return new Content(
            view: 'pms.emails.booking-confirmation',
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

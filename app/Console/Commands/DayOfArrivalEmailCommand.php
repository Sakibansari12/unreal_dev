<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\PropertyBooking;
use App\Models\TblHomeUnit;
use App\Models\TblHomeMultiUnit;
use App\Mail\DayOfArrivalEmail;
use Carbon\Carbon;
use Mail;

class DayOfArrivalEmailCommand extends Command{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'DayOfArrivalEmailCommandJob';
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send Emails To User Day Of Arrival Email';
    /**
     * Execute the console command.
     */
    public function handle(){
        set_time_limit(0);
        $currentDate = date('Y-m-d');
        try {
            $bookingList = PropertyBooking::/* with('property')-> */where('checkin_date', $currentDate)->where('property_booking_status', 'Confirmed')->get();
            
            foreach($bookingList as $booking){
               
                $guestDetail = json_decode($booking->customer_detail);

            $property = ($booking->pType == 'unit')
                ? TblHomeUnit::with(['imagesWebsite', 'locationData'])->where('id', $booking->property_id)->first()
                : TblHomeMultiUnit::with(['imagesWebsite', 'locationData'])->where('id', $booking->property_id)->first();
             $booking->property = $property;
             // dd($booking);


                Mail::to($guestDetail->email)->send(new DayOfArrivalEmail(array('mailData'=>$booking, 'type'=>'customer')));
                if(setting()->booking_manager_email){
                    Mail::to(setting()->booking_manager_email)->send(new DayOfArrivalEmail(array('mailData'=>$booking, 'type'=>'booking_manager')));
                }
            }
        }
        catch (\Exception $e) {
            dd($e->getMessage());
        }
    }
}

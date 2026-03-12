<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\PropertyBooking;
use App\Models\TblHomeUnit;
use App\Models\TblHomeMultiUnit;
use App\Mail\DayOfDepartureEmail;
use Carbon\Carbon;
use Mail;

class DayOfDepartureEmailCommand extends Command{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'DayOfDepartureEmailCommandJob';
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
           // $bookingList = PropertyBooking::with('property')->where('payment_status', 'Paid')->where('checkout_date', $currentDate)->whereIn('channel', ['Offline', 'website'])->whereNull('invoice_no')->get();
           $bookingList = PropertyBooking::/* with('property')-> */where('checkout_date', $currentDate)->where('property_booking_status', 'Confirmed')->get();
           
          // dd($bookingList);
           
            foreach($bookingList as $booking){
                $guestDetail = json_decode($booking->customer_detail);

            $property = ($booking->pType == 'unit')
                ? TblHomeUnit::with(['imagesWebsite', 'locationData'])->where('id', $booking->property_id)->first()
                : TblHomeMultiUnit::with(['imagesWebsite', 'locationData'])->where('id', $booking->property_id)->first();
             $booking->property = $property;


                $parameters = array(
                    array('name'=>'name','value'=>$guestDetail->first_name.' '.$guestDetail->last_name),
                    array('name'=>'home', 'value'=>$booking->property->home_name),
                    array('name'=>'location', 'value'=>$booking->property->location),
                    array('name'=>'arrival', 'value'=>date('j F', strtotime($booking->checkin_date))),
                    array('name'=>'departure', 'value'=>date('j F', strtotime($booking->checkout_date))),
                    array('name'=>'no_of_guests', 'value'=>$booking->no_of_adult),
                    array('name'=>'no_of_infants', 'value'=>$booking->no_of_children?$mailData->booking->no_of_children:0),
                    array('name'=>'no_of_nights', 'value'=>$booking->no_of_nights),
                    array('name'=>'price', 'value'=>round($booking->payable_amount)),
                    array('name'=>'guest_mobile_no', 'value'=>round($guestDetail->mobile_number)),
                    array('name'=>'link', 'value'=>'https://Unreal Estate.in/'),
                );

                // $whatsAppReqParameters = array(
                //     'template_name'=>'day_of_departure',
                //     'broadcast_name'=>'day_of_departure_090920241643',
                //     'parameters'=>$parameters
                // );
                // $whatsAppReqParametersJson = json_encode($whatsAppReqParameters);
                // $whatsAppRespons = sendWhatsAppMessage($whatsAppReqParametersJson, $guestDetail->mobile_number);

                // if($booking->property->admin_emails){
                //     if(isset($booking->property->admin_emails->vacation_manager_mobile_no) && $booking->property->admin_emails->vacation_manager_mobile_no){
                //         $whatsAppReqParameters = array(
                //             'template_name'=>'day_of_departure_vacation_manager',
                //             'broadcast_name'=>'day_of_departure_vacation_manager_090920241644',
                //             'parameters'=>$parameters
                //         );
                //         $whatsAppReqParametersJson = json_encode($whatsAppReqParameters);
                //         $whatsAppRespons = sendWhatsAppMessage($whatsAppReqParametersJson, $booking->property->admin_emails->vacation_manager_mobile_no);
                //     }
                // }
               
                Mail::to($guestDetail->email)->send(new DayOfDepartureEmail(array('mailData'=>$booking, 'type'=>'customer')));
                
                // if($booking->property->admin_emails){
                //     if(isset($booking->property->admin_emails->vacation_manager_email) && $booking->property->admin_emails->vacation_manager_email){
                //         Mail::to($booking->property->admin_emails->vacation_manager_email)->send(new DayOfDepartureEmail(array('mailData'=>$booking, 'type'=>'booking_manager')));
                //     }
                // }

                // if($booking->property->admin_emails){
                //     if(isset($booking->property->admin_emails->site_team_manager_email) && $booking->property->admin_emails->site_team_manager_email){
                //         Mail::to($booking->property->admin_emails->site_team_manager_email)->send(new DayOfDepartureEmail(array('mailData'=>$booking, 'type'=>'site_team_manager')));
                //     }
                // }

                // if($booking->property->admin_emails){
                //     if(isset($booking->property->admin_emails->email) && $booking->property->admin_emails->email){
                //         $mail = Mail::to($booking->property->admin_emails->email);
                //         if(isset($booking->property->admin_emails->addMultiItem)){
                //             foreach($booking->property->admin_emails->addMultiItem as $cc_mail){
                //                 $mail->cc($cc_mail->cc_email);
                //             }
                //         }
                //         $mail->send(new DayOfDepartureEmail(array('mailData'=>$data, 'type'=>'booking_manager')));
                //     }
                // }
            }
        }
        catch (\Exception $e) {
            dd($e->getMessage());
        }
    }
}
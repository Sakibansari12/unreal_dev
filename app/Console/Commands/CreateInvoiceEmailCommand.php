<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\PropertyBooking;
use App\Models\TblHomeUnit;
use App\Models\TblHomeMultiUnit;
use App\Models\Admin;
use App\Mail\BookingInvoiceEmail;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Mail;

class CreateInvoiceEmailCommand extends Command{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'CreateInvoiceEmailCommandJob';
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create invoice of booking order';
    /**
     * Execute the console command.
     */
    public function handle(){
        set_time_limit(0);

        try {
            $currentDate = date('Y-m-d');

            $bookingList = PropertyBooking::with('property', 'property.stateDetail.companyInfo', 'property')
                ->where('payment_status', 'Paid')
                ->where('checkout_date', $currentDate)
                ->whereIn('channel', ['Offline', 'Website'])
                ->whereNull('invoice_no')
                ->get();
//dd($bookingList);
          //  $bookingList = PropertyBooking::where('payment_status', 'Paid')->whereIn('channel', ['Offline', 'Website'])->get();
            $bookingList->chunk(2)->each(function ($chunkedBookings) {
                foreach ($chunkedBookings as $detail) {
                    if($detail->pType == 'multiunit') {
                       // $detail->property = TblHomeMultiUnit::with('stateDetail.companyInfo')->find($detail->property_id);
                       $detail->setRelation('property', TblHomeMultiUnit::with('stateDetail.companyInfo')->find($detail->property_id));
                    }
                    else {
                       // $detail->property = TblHomeUnit::with('stateDetail.companyInfo')->find($detail->property_id);
                       $detail->setRelation('property', TblHomeUnit::with('stateDetail.companyInfo')->find($detail->property_id));
                    }
                    if (!$detail->property) {
                        continue; // Skip if property not found
                    }
                   // dd($detail);
                    if ($detail->property->stateDetail->companyInfo) {

                        $totalPriceInWords = convertNumberToIndianCurrencyWords($detail->payable_amount);
                        $invoice_no = 'SC/' . date("Y") . '/001';
                        
                        // ✅ Decode JSON if string
                        if (is_string($detail->additional_charges)) {
                            $detail->additional_charges = json_decode($detail->additional_charges, true);
                        }
            
                        // ✅ Ensure it's an array (avoid null error)
                        if (!is_array($detail->additional_charges)) {
                            $detail->additional_charges = [];
                        }
                      //  dd($detail->property->user_id);
                        
                        $pdf = Pdf::loadView('pms.booking.invoice', compact('detail', 'totalPriceInWords', 'invoice_no'));

                        $detail->invoice_serial_no = getInvoiceSerialNo();
                        $detail->invoice_no = $invoice_no;
                        $detail->invoice = $invoice_no;

                        $fileName = 'invoice' . $detail->booking_id . '.pdf';
                        $detail->invoice_file = $fileName;
                        $detail->save();

                        $pdf->save(public_path('invoice/' . $fileName))->stream($fileName);

                        $data = array(
                            'mailData' => $detail,
                            'type' => 'customer',
                            'attachment' => public_path('invoice/' . $fileName),
                            'attachment_name' => $fileName,
                            'attachment_mime' => 'application/pdf'
                        );

                        $guestDetail = json_decode($detail->customer_detail);
                        Mail::to($guestDetail->email)->send(new BookingInvoiceEmail(['mailData' => $data, 'type' => 'customer']));
                        
                        
                        // 🔹 Send to property owner (Admin user)
                            if (isset($detail->property->user_id)) {
                                $userData = Admin::where('id', $detail->property->user_id)->first();
                                if ($userData && !empty($userData->email)) {
                                    Mail::to($userData->email)->send(new BookingInvoiceEmail(['mailData' => $data, 'type' => 'customer']));
                                }
                            }

                    }

                   
                }
            });
        }
        catch (\Exception $e) {
            dd($e->getMessage());
        }
    }
}
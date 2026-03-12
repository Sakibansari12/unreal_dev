<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\PropertyBooking;
use App\Models\TblHomeUnit;
use App\Models\TblHomeMultiUnit;
use App\helper\MasterHelper;
use App\Mail\BookingInvoiceEmail;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Mail;
use DB;

class ChangeRuCurrencyCommand extends Command{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ChangeRuCurrencyCommand';
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'ChangeRuCurrencyCommand';
    /**
     * Execute the console command.
     */
    public function handle(){
        set_time_limit(0);

        try {
            $locationList  = DB::table('tbl_ru_location')->where('status', 1)->get();
            foreach($locationList as $val){
                $xml = "<Push_ChangeCurrency_RQ>
                    <Authentication>
                        <UserName>".config('ru.RU_USER_NAME')."</UserName>
                        <Password>".config('ru.RU_PASSWORD')."</Password>
                    </Authentication>
                    <Location>".$val->ru_location_id."</Location>
                    <Currency>INR</Currency>
                </Push_ChangeCurrency_RQ>";
                $xmlResponse = MasterHelper::makeXmlRequest($xml);
               
            }
        }
        catch (\Exception $e) {
            dd($e->getMessage());
        }
    }
}
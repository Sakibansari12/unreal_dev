<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;
 use GuzzleHttp\Client;
class TestingController extends Controller
{
   

public function fetchRuPropertyTypes()
{
    $client = new \GuzzleHttp\Client();

    $xml = '<?xml version="1.0" encoding="UTF-8"?>
<GetPropertyTypes_RQ>
    <Authentication>
        <UserName>' . config("ru.RU_USER_NAME") . '</UserName>
        <Password>' . config("ru.RU_PASSWORD") . '</Password>
    </Authentication>
    <OwnerID>' . config("ru.RU_OWNER_ID") . '</OwnerID>
</GetPropertyTypes_RQ>';

    try {
        $response = $client->post(
            config('ru.RU_URL'),
            [
                'query' => [
                    'action' => 'GetPropertyTypes'
                ],
                'headers' => [
                    'Content-Type' => 'application/xml; charset=UTF-8',
                ],
                'body' => $xml,
            ]
        );

        $body = $response->getBody()->getContents();

        // 🔍 DEBUG (very important)
        logger('RU RESPONSE RAW', [$body]);

        $result = simplexml_load_string($body, "SimpleXMLElement", LIBXML_NOCDATA);
        $json   = json_encode($result);
        $array  = json_decode($json, true);

        return response()->json($array);

    } catch (\Exception $e) {
        return response()->json([
            'error' => true,
            'message' => $e->getMessage(),
        ]);
    }
}

  public function test()
    {

       // air bnb se booking banane ka tarika 
        $data ='<LNM_PutConfirmedReservation_RQ><Authentication><UserName>gagan@tisyastays.com</UserName><Password>05F2F2906216054CA864DD0EA7F330765EF80F8B</Password></Authentication><Reservation><ReservationID>145290001</ReservationID><LastMod>2026-01-22 05:32:27</LastMod><StayInfos><StayInfo><PropertyID>3906154</PropertyID><DateFrom>2026-02-23</DateFrom><DateTo>2026-02-25</DateTo><NumberOfGuests>2</NumberOfGuests><Costs><RUPrice>11814.00</RUPrice><ClientPrice>13860.00</ClientPrice><AlreadyPaid>13860.00</AlreadyPaid><PriceScale>0%</PriceScale></Costs><ResapaID>171412856</ResapaID><Comments>Rate name: Standard&#xD;
Room remarks: 11154.00INR&#xD;
New comments: 11814.00INR&#xD;
Paid 13860.00INR</Comments><Mapping><ReservationID>HMD5T2ZN8F</ReservationID><StayID>1298240438742238388</StayID></Mapping><Units>1</Units><ReservationBreakdown><RUBreakdown><DayPrices Date="2026-01-24"><Rent>3938.0</Rent><Price>3938.0</Price></DayPrices><DayPrices Date="2026-01-25"><Rent>3938.0</Rent><Price>3938.0</Price></DayPrices><DayPrices Date="2026-01-26"><Rent>3938.0</Rent><Price>3938.0</Price></DayPrices><Total>11814.00</Total><Rent>11814.0</Rent></RUBreakdown><ChannelBreakdown><ChannelTotalFeesTaxes><ChannelTotalFeeTax IncludedInChannelTotal="true" Amount="330.00" Currency="INR" Name="SGST (In - Goa (in-ga))" ItemType="Tax" /><ChannelTotalFeeTax IncludedInChannelTotal="true" Amount="330.00" Currency="INR" Name="CGST (In - Goa (in-ga))" ItemType="Tax" /></ChannelTotalFeesTaxes><ChannelTotal>13860.00</ChannelTotal><ChannelRent>13200.00</ChannelRent></ChannelBreakdown><ChannelCommission>2046.00</ChannelCommission></ReservationBreakdown></StayInfo></StayInfos><CancellationPolicyInfo><PolicyText>strict_14_with_grace_period</PolicyText></CancellationPolicyInfo><CustomerInfo><Name>Lohith</Name><SurName>Sivakumar</SurName><Email>stay+kf1ca8x7yxbc5guy3fixix9hpa@guests.quickconnect.rentals</Email><Phone>918894020248</Phone><SkypeID /><Address /><ZipCode /><LanguageID>1</LanguageID></CustomerInfo><GuestDetailsInfo><NumberOfAdults>2</NumberOfAdults><NumberOfChildren>0</NumberOfChildren><NumberOfInfants>0</NumberOfInfants><NumberOfPets>0</NumberOfPets></GuestDetailsInfo><Creator>airbnb@rentalsunited.com</Creator><Comments>The original price is 11154.00 &#xD;
There is no credit card info.&#xD;
The original price is 11814.00 INR&#xD;
Total Price: 11814.00 INR&#xD;
&#xD;
Check-in 15:00, check-out till 11:00 (Asia/Calcutta time)&#xD;
Price breakdown: 13200.00INR stay + 0INR fees - 2046.00INR Airbnb fee&#xD;
1 adult, 0 children, 0 infants, 0 pets&#xD;
Taxes remitted by host: SGST (In - Goa (in-ga)) 330.00INR, CGST (In - Goa (in-ga)) 330.00INR. &#xD;
Commission: 2046.00INR&#xD;
Cancellation Policy: strict_14_with_grace_period&#xD;
Check-in 15:00, check-out till 11:00 (Asia/Calcutta time)&#xD;
Price breakdown: 13200.00INR stay + 0INR fees - 2046.00INR Airbnb fee&#xD;
2 adults, 0 children, 0 infants, 0 pets&#xD;
Taxes remitted by host: SGST (In - Goa (in-ga)) 330.00INR, CGST (In - Goa (in-ga)) 330.00INR. </Comments><ReservationStatusID>3</ReservationStatusID><ReferenceID>HMD5T2ZN8F</ReferenceID><IsArchived>false</IsArchived><CreatedDate>2026-01-22 05:19:00</CreatedDate></Reservation></LNM_PutConfirmedReservation_RQ>';
        










        return response()->json([
            'message' => 'Testing endpoint is working!',
        ]);
    }


}

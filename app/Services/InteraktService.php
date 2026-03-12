<?php
namespace App\Services;

use Illuminate\Support\Facades\Http;

class InteraktService
{
     /*  public function sendWhatsappConfirmation($userName, $bookingId, $mobile)
    {
        
        $response = Http::withHeaders([
            'Authorization' =>  'Basic ' . env('INTERAKT_SECRET'),
            'Content-Type' => 'application/json'
        ])->post('https://api.interakt.ai/v1/public/message/', [
            "countryCode" => "+91",
            "phoneNumber" => $mobile,
            "callbackData" => "booking_confirmation",
            "type" => "Template",
            "template" => [
                "name" => "booking_confirmation_template",
                "languageCode" => "en",
                "bodyValues" => [
                    $userName,
                    $bookingId
                ]
            ]
        ]);
        return $response->json();
    } */

        public function sendWhatsappConfirmation($userName, $bookingId, $mobile)
    {
        $response = Http::withHeaders([
            'Authorization' => 'Basic ' . env('INTERAKT_SECRET'),
            'Content-Type' => 'application/json'
        ])->post('https://api.interakt.ai/v1/public/message/', [
            "countryCode" => "+91",
            "phoneNumber" => $mobile,
            "callbackData" => "booking_confirmation",
            "type" => "Template",
            "template" => [
                "name" => "booking_confirmation_template",
                "languageCode" => "en",

                // 👇 ADD THIS
                "headerValues" => [
                    "https://unreal.tempsite.in/assets/website/images/unreal-logo.png"
                ],

                "bodyValues" => [
                    $userName
                    
                ]
            ]
        ]);

        return $response->json();
    }
}
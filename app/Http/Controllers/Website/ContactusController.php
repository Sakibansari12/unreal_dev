<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\ContactUs;
use Illuminate\Support\Facades\Mail;
use App\Mail\ContactusEmail;
use App\Mail\ContactEmail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;



class ContactusController extends Controller
{
    public function contactus(){
             $captcha = generateCaptcha();
        session(['captcha_code' => $captcha]);
         $countryCode = DB::table('countries')->get();
        return view('website.contactus',compact('countryCode','captcha'));
    }

public function contactussubmit(Request $request)
{
    $rules = [
        'first_name' => 'required|string|max:255',
        'last_name' => 'required|string|max:255',
        'email' => 'required|email|max:255',
        'country_code' => 'nullable|string|max:10',
        'phone_number' => 'required|string|max:20',
        'message' => 'required|string',
        'captcha' => 'required',
    ];

    $userCaptcha = $request->input('captcha');
    $sessionCaptcha = session('captcha_code');

   

    $validated = $request->validate($rules);
   if ($userCaptcha != $sessionCaptcha) {
        return response()->json([
            'errors' => ['captcha' => ['Invalid captcha! Try again.']]
        ], 422);
    }
    $contact = new ContactUs();
    $contact->first_name = $validated['first_name'];
    $contact->last_name = $validated['last_name'];
    $contact->email = $validated['email'];
    $contact->country_code = $validated['country_code'] ?? null;
    $contact->phone_number = $validated['phone_number'];
    $contact->message = $validated['message'] ?? null;
    $contact->save();
   
    $admin="support@unrealestate.in";
    Mail::to($admin)->send(new ContactusEmail($validated));
    return response()->json(['success' => true]);
}
    
    public function contactSubmit(Request $request)
{
    $rules = [
        'first_name_1' => 'required|string|max:255',
        'last_name_1' => 'required|string|max:255',
        'email_1' => 'required|email|max:255',
        'country_code_1' => 'nullable|string|max:10',
        'phone_number_1' => 'required|string|max:20',
        'message_1' => 'required|string',
        'captcha_1' => 'required',
    ];

    $userCaptcha = $request->input('captcha_1');
    $sessionCaptcha = session('captcha_new');

   

    $validated = $request->validate($rules);
   if ($userCaptcha != $sessionCaptcha) {
        return response()->json([
            'errors' => ['captcha' => ['Invalid captcha! Try again.']]
        ], 422);
    }
    $contact = new ContactUs();
    $contact->first_name = $validated['first_name_1'];
    $contact->last_name = $validated['last_name_1'];
    $contact->email = $validated['email_1'];
    $contact->country_code = $validated['country_code_1'] ?? null;
    $contact->phone_number = $validated['phone_number_1'];
    $contact->message = $validated['message_1'] ?? null;
    $contact->save();
   
    $admin="support@unrealestate.in";
    Mail::to($admin)->send(new ContactEmail($validated));
    return response()->json(['success' => true]);
}
    
    
    
    
    
    public function refreshCaptcha()
    {
        $captcha = Str::random(6);
        session(['captcha_code' => $captcha]);
        return response()->json(['captcha' => $captcha]);
    }
 
}
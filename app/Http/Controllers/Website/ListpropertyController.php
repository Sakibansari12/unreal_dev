<?php

namespace App\Http\Controllers\Website;

use Illuminate\Http\Request;
use App\Models\TblFaqCategory;
use App\Models\TblFaq;
use App\Models\TblHomeType;
use App\Models\TblLocation;
use App\Models\Property;
use App\Models\Service;
use Session;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Str;
use App\Mail\PropertylistEmail;
use Illuminate\Support\Facades\Mail;

class ListpropertyController extends Controller
{
 public function listProperty(){
   $categories = TblFaqCategory::where('id', '=', 1)
   ->with(['faqs' => function ($query) {
      $query->where('status', 1); // Only active FAQs
  }])->get();

 
  $propertyTypes = TblHomeType::whereNull('deleted_at')
  ->where('status', 1)
  ->get();
  
  $locationTypes = TblLocation::where('status', 1)
  ->get();
          $captcha = generateCaptcha();
        session(['captcha_code' => $captcha]);
         $countryCode = DB::table('countries')->get();
         $services=Service::all();
    return view('website.list.list-property',compact('categories','services','propertyTypes','locationTypes','captcha','countryCode'));
 }

 public function store(Request $request)
{
    $rules = [
        'first_name' => 'required|string|max:255',
        'last_name' => 'required|string|max:255',
        'email' => 'required|email|max:255',
        'country_code' => 'nullable|string|max:10',
        'phone_number' => 'required|string|max:20',
        'property_type_id' => 'required',
        'location_type_id' => 'required',
        'description' => 'nullable|string',
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
    $property = new Property();
    $property->first_name = $validated['first_name'];
    $property->last_name = $validated['last_name'];
    $property->email = $validated['email'];
    $property->country_code = $validated['country_code'] ?? null;
    $property->phone_number = $validated['phone_number'];
    $property->property_type_id = $validated['property_type_id'];
    $property->location_type_id = $validated['location_type_id'];
    $property->description = $validated['description'] ?? null;
    $property->save();
   
    $admin="karrar@iws.in";
     $locationTypes = TblLocation::where('status', 1)->get();
     $propertyTypes = TblHomeType::where('status', 1)->get();
    Mail::to($admin)->send(new PropertylistEmail($validated,$locationTypes, $propertyTypes));
    return response()->json(['success' => true]);
}
public function refreshCaptcha()
    {
        $captcha = Str::random(6);
        session(['captcha_code' => $captcha]);
        return response()->json(['captcha' => $captcha]);
    }
 
}
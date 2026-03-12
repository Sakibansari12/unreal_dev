<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AboutUs;
use App\Models\TblAboutUs;
use App\Models\Team;
use App\Models\TblTermsandCondition;
use Illuminate\Support\Facades\Validator;
use App\Models\SubscribeWebsite;

class AboutusController extends Controller
{
    public function aboutUs()
    {
        $about_us = TblAboutUs::first(); // or ->latest()->first()

        $teams = Team::where('status', 1)
            ->whereNull('deleted_at')
            ->orderBy('position', 'asc')
            ->orderBy('id', 'desc')
            ->get();

        return view('website.about.about-us', compact('about_us', 'teams'));
    }

    public function cookiepolicy()
    {
        return view('website.policy.cookiepolicy');
    }

    public function termsCondition()
    {
        $terms = TblTermsandCondition::first();
        return view('website.terms-and-condition', compact('terms'));
    }

    public function privacyPolicy()
    {
        return view('website.privacy-policy');
    }

    public function careers()
    {
        return view('website.careers');
    }

    public function corporateBookings()
    {
        return view('website.corporate-bookings');
    }
    public function cancellation_refund()
    {
        return view('website.cancellation_and_refund');
    }
    public function customerSupport()
    {
        return view('website.customersupport');
    }

    public function hostwithnook()
    {
        return view('website.hostwithnook');
    }

    public function SubscribeStore(Request $request)
    {
        if (SubscribeWebsite::where('email', $request->email)->exists()) {
            return response()->json([
                'status' => false,
                'errors' => [
                    'email' => ['This user is already subscribed.']
                ],
            ]);
        }

        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors(),
            ]);
        }

        SubscribeWebsite::create([
            'email' => $request->email,
            'status' => 1,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Subscribe created successfully',
        ], 201);
    }
}

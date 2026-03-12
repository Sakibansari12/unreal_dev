<?php

namespace App\Http\Controllers\PMS;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PropertyBooking;
use App\Models\TblHome;
use App\Models\PropertyBookingPaymentRequest;
use App\Models\BookingGuestId;
use App\Models\BookingEnquiry;
use App\Models\RuPropertyPrice;
use App\Models\TblHomeUnit;
use App\Models\TblHomeMultiUnit;
use App\Models\RuPropertyMinstay;
use App\Models\RuPropertyAvailability;
use App\Models\TblHomeImageVideo;
use App\Models\CancellationSlab;
use App\Models\TblRuAmenityMapping;
use App\Models\HomeAdditionalCharge;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class DashboardController extends Controller{
    
    
    public function dashboard(Request $request){
        $booking_icon_detail = [];
        $searchDateFrom = date('Y-m-d');
        $type = $request->type;
         $user = Auth::guard('admin')->user();
        if (!$request->has('type')) {
            $request->merge(['type' => 'today']);
        }
    
        if ($request->has('type') && $request->type === 'tomorrow') {
            $searchDateFrom = date('Y-m-d', strtotime('+1 day'));
        }
        $searchDateTo = date('Y-m-d', strtotime($searchDateFrom . ' +7 days'));
        
        if($request->has('type') && $request->type != 'next_7_days'){
        
            $query = PropertyBooking::query()
            
            ->when($user->role_id != 1, function ($query) use ($user) {
                if (in_array($user->role_id, [3, 4, 5, 2])) {
                    return $query->where('property_bookings.parent_user_id', $user->parent_user_id);
                }
                elseif ($user->role_id == 6) {
                    return $query->where('property_bookings.owner_id', $user->id);
                } 
                elseif ($user->role_id == 7) {
                    return $query->where('property_bookings.parent_user_id', $user->id);
                }
                elseif ($user->role_id == 8) {
                    return $query->where('property_bookings.travelagent_id', $user->id);
                }
            })

             
            
            
                ->when($request->has('type') && in_array($request->type, ['today', 'tomorrow']), function ($query) use ($searchDateFrom) {
                    return $query->where(function ($q) use ($searchDateFrom) {
                        $q->where('checkin_date', $searchDateFrom)->orWhere('checkout_date', $searchDateFrom);
                    });
                })
                ->when($request->has('created_by') && $request->created_by !== '', function ($query) use ($request) {
                    return $query->where('created_by', $request->created_by);
                });
            
            $nextArrivalDepartureList = $query->where('property_booking_status', 'Confirmed')->orderBy('id', 'desc')->get()->take(100)->map(function ($booking) {
                if ($booking->pType === 'unit') {
                    $booking->home = TblHomeUnit::where('id', $booking->property_id)->first();
                }
                elseif ($booking->pType === 'multiunit') {
                    $booking->home = TblHomeMultiUnit::where('id', $booking->property_id)->first();
                }
                else {
                    $booking->home = null;
                }
                return $booking;
            });
            $nextArrivalDepartureList->map(function ($booking) use ($request, $searchDateFrom, $searchDateTo, $type) {
                $booking->booking_icon_detail =  $this->getDasboardBookingParameters($booking, $searchDateFrom, $searchDateTo, $type);
                return $booking;
            });
        }
        else {
            $nextArrivalDepartureList = collect();
            $currentDate = Carbon::parse($searchDateFrom);
            $endDate = Carbon::parse($searchDateTo);
        
            while ($currentDate->lte($endDate)) {
                $dateStr = $currentDate->format('Y-m-d');
                $checkinQuery = PropertyBooking::query()
                
                ->when($user->role_id != 1, function ($query) use ($user) {
                if (in_array($user->role_id, [3, 4, 5, 2])) {
                    return $query->where('property_bookings.parent_user_id', $user->parent_user_id);
                }
                elseif ($user->role_id == 6) {
                    return $query->where('property_bookings.owner_id', $user->id);
                } 
                elseif ($user->role_id == 7) {
                    return $query->where('property_bookings.parent_user_id', $user->id);
                }
                elseif ($user->role_id == 8) {
                    return $query->where('property_bookings.travelagent_id', $user->id);
                }
            })
                
                
                    ->when($request->has('created_by') && $request->created_by !== '', function ($query) use ($request) {
                        return $query->where('created_by', $request->created_by);
                    })->where('property_booking_status', 'Confirmed')->whereDate('checkin_date', $dateStr)->orderBy('id', 'desc')->take(100)->get()->map(function ($booking) {
                    if ($booking->pType === 'unit') {
                        $booking->home = TblHomeUnit::where('id', $booking->property_id)->first();
                    }
                    elseif ($booking->pType === 'multiunit') {
                        $booking->home = TblHomeMultiUnit::where('id', $booking->property_id)->first();
                    }
                    else {
                        $booking->home = null;
                    }
                    return $booking;
                })->map(function ($booking) use ($request, $dateStr, $type) {
                    $booking->booking_icon_detail = $this->getDasboardBookingParameters($booking, $dateStr, $dateStr, $type);
                    return $booking;
                });
        
                $checkoutQuery = PropertyBooking::query()
                
                ->when($user->role_id != 1, function ($query) use ($user) {
                if (in_array($user->role_id, [3, 4, 5, 2])) {
                    return $query->where('property_bookings.parent_user_id', $user->parent_user_id);
                }
                elseif ($user->role_id == 6) {
                    return $query->where('property_bookings.owner_id', $user->id);
                } 
                elseif ($user->role_id == 7) {
                    return $query->where('property_bookings.parent_user_id', $user->id);
                }
                elseif ($user->role_id == 8) {
                    return $query->where('property_bookings.travelagent_id', $user->id);
                }
            })
                
                
                
                ->when($request->has('created_by') && $request->created_by !== '', function ($query) use ($request) {
                    return $query->where('created_by', $request->created_by);
                })->where('property_booking_status', 'Confirmed')->whereDate('checkout_date', $dateStr)->orderBy('id', 'desc')->take(100)->get()->map(function ($booking) use ($request, $dateStr, $type) {
                    $booking->booking_icon_detail = $this->getDasboardBookingParameters($booking, $dateStr, $dateStr, $type);
                    return $booking;
                })->map(function ($booking) {
                    if ($booking->pType === 'unit') {
                        $booking->home = TblHomeUnit::where('id', $booking->property_id)->first();
                    }
                    elseif ($booking->pType === 'multiunit') {
                        $booking->home = TblHomeMultiUnit::where('id', $booking->property_id)->first();
                    }
                    else {
                        $booking->home = null;
                    }
                    return $booking;
                });
                $nextArrivalDepartureList = $nextArrivalDepartureList->merge($checkinQuery)->merge($checkoutQuery);
                $currentDate->addDay();
            }
        }
        
     
        $checkoutOperator = (date('H:i') < '12:00') ? '>=' : '>';
        
        $queryCurrentOccupancy = PropertyBooking::query()
        
        ->when($user->role_id != 1, function ($query) use ($user) {
                if (in_array($user->role_id, [3, 4, 5, 2])) {
                    return $query->where('property_bookings.parent_user_id', $user->parent_user_id);
                }
                elseif ($user->role_id == 6) {
                    return $query->where('property_bookings.owner_id', $user->id);
                } 
                elseif ($user->role_id == 7) {
                    return $query->where('property_bookings.parent_user_id', $user->id);
                }
                elseif ($user->role_id == 8) {
                    return $query->where('property_bookings.travelagent_id', $user->id);
                }
            })
        
        
        
        ->when($request->has('type') && $request->type !='', function ($query) use ($searchDateFrom, $checkoutOperator) {
            return $query->where('checkin_date', '<=', date('Y-m-d'))->where('checkout_date', $checkoutOperator, date('Y-m-d'));
        })
        ->when($request->has('created_by') && $request->created_by !== '', function ($query) use ($request) {
            return $query->where('created_by', $request->created_by);
        });
        
        $currentOccupancyList = $queryCurrentOccupancy->where('property_booking_status', 'Confirmed')->orderBy('id', 'desc')->get()->take(100)->map(function ($booking) {
            if ($booking->pType === 'unit') {
                $booking->home = TblHomeUnit::where('id', $booking->property_id)->first();
            }
            elseif ($booking->pType === 'multiunit') {
                $booking->home = TblHomeMultiUnit::where('id', $booking->property_id)->first();
            }
            else {
                $booking->home = null;
            }
            return $booking;
        });
            
        $currentOccupancyList->map(function ($booking) use ($request, $searchDateFrom, $searchDateTo, $type) {
            $booking->booking_icon_detail =  $this->getDasboardBookingParametersCurrentOccupancy($booking, $searchDateFrom, $searchDateTo, $type);
            return $booking;
        });
        
        $currentOccpancyList = $currentOccupancyList;
        $nextArrivalDepartureList = $nextArrivalDepartureList;
        
      
       
        return view('pms.dashboard.index', compact('nextArrivalDepartureList', 'currentOccpancyList'));
    }
    
    
    public function getDasboardBookingParametersCurrentOccupancy($booking, $searchDateFrom, $searchDateTo, $type){
        $icon = '';
            $text = '';
            $date = '';
            $type = 'today';
            if(date('Y-m-d', strtotime($booking->checkin_date)) == $searchDateFrom){
                $icon =  URL('/').'/assets/pms/images/arrival.svg';
                $text = 'Check-in';
                $date = $booking->checkin_date;
                $type = 'checkin';
            }
            else if(date('Y-m-d', strtotime($booking->checkout_date)) == $searchDateFrom){
                $icon = URL('/').'/assets/pms/images/departure.svg';
                $text = 'Check-out';
                $date = $booking->checkout_date;
                $type = 'checkout';
            }
            $color = 'bg-info';
            if($booking->channel=='Airbnb'){
                $color ='alert-danger';
            }
            
            else if($booking->channel=='Booking.com'){
                $color ='alert-warning';
            }
            else if($booking->channel=='MakeMyTrip'){
                $color ='alert-dark';
            }
            else if($booking->channel=='Agoda'){
                $color ='alert-success';
            }
            else{
                $color ='alert-info';
            }
            return ['booking_icon'=>$icon, 'text'=>$text, 'date'=>$date, 'type'=>$type, 'alert_class'=>$color];
    }
    
    
    public function getDasboardBookingParameters($booking, $searchDateFrom, $searchDateTo, $type){
        $icon = '';
            $text = '';
            $date = '';
            $type = '';
            if(date('Y-m-d', strtotime($booking->checkin_date)) == $searchDateFrom){
                $icon =  URL('/').'/assets/pms/images/arrival.svg';
                $text = 'Check-in';
                $date = $booking->checkin_date;
                $type = 'checkin';
            }
            else if(date('Y-m-d', strtotime($booking->checkout_date)) == $searchDateFrom){
                $icon = URL('/').'/assets/pms/images/departure.svg';
                $text = 'Check-out';
                $date = $booking->checkout_date;
                $type = 'checkout';
            }
            $color = 'bg-info';
            if($booking->channel=='Airbnb'){
                $color ='alert-danger';
            }
            
            else if($booking->channel=='Booking.com'){
                $color ='alert-warning';
            }
            else if($booking->channel=='MakeMyTrip'){
                $color ='alert-dark';
            }
            else if($booking->channel=='Agoda'){
                $color ='alert-success';
            }
            else{
                $color ='alert-info';
            }
            
            return ['booking_icon'=>$icon, 'text'=>$text, 'date'=>$date, 'type'=>$type, 'alert_class'=>$color];
    }
    
    public function truncateTable(){
       
    }
}
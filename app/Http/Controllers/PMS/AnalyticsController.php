<?php

namespace App\Http\Controllers\PMS;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\TblHome;
use App\Models\TblLocation;
use App\Services\PropertyService;
use App\Models\TblHomeUnit;
use App\Models\TblHomeMultiUnit;
use App\Models\PropertyBooking;
use App\Models\RuPropertyPrice;
use Carbon\Carbon;
use Illuminate\Pagination\LengthAwarePaginator;


class AnalyticsController extends Controller{
    

    protected $propertyService;

    public function __construct(){
        $this->propertyService = new PropertyService();
    }


    public function analytics(Request $request){

       return view('pms.analytics.analytics');

    }


    public function index(Request $request){
        $booking_icon_detail = [];
        $searchDateFrom = date('Y-m-d');
        $type = $request->type;
        if ($request->has('type') && $request->type === 'tomorrow') {
            $searchDateFrom = date('Y-m-d', strtotime('+1 day'));
        }
        $searchDateTo = date('Y-m-d', strtotime($searchDateFrom . ' +7 days'));
        
        if($request->has('type') && $request->type != 'next_7_days'){
        
            $query = PropertyBooking::query()
                ->when($request->has('type') && in_array($request->type, ['today', 'tomorrow']), function ($query) use ($searchDateFrom) {
                    return $query->where(function ($q) use ($searchDateFrom) {
                        $q->where('checkin_date', $searchDateFrom)->orWhere('checkout_date', $searchDateFrom);
                    });
                })
                
                ->when($request->has('created_by') && $request->created_by !== '', function ($query) use ($request) {
                    return $query->where('created_by', $request->created_by);
                });
            
            $nextArrivalDepartureList = $query
                ->with('home')
                ->where('property_booking_status', 'Confirmed')
                ->orderBy('id', 'desc')
                ->get()->take(100);
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
                    ->when($request->has('created_by') && $request->created_by !== '', function ($query) use ($request) {
                        return $query->where('created_by', $request->created_by);
                    })
                    ->with('home')
                    ->where('property_booking_status', 'Confirmed')
                    ->whereDate('checkin_date', $dateStr)
                    ->orderBy('id', 'desc')
                    ->take(100)
                    ->get()
                    ->map(function ($booking) use ($request, $dateStr, $type) {
                        $booking->booking_icon_detail = $this->getDasboardBookingParameters($booking, $dateStr, $dateStr, $type);
                        return $booking;
                    });
        
                $checkoutQuery = PropertyBooking::query()
                    ->when($request->has('created_by') && $request->created_by !== '', function ($query) use ($request) {
                        return $query->where('created_by', $request->created_by);
                    })
                    ->with('home')
                    ->where('property_booking_status', 'Confirmed')
                    ->whereDate('checkout_date', $dateStr)
                    ->orderBy('id', 'desc')
                    ->take(100)
                    ->get()
                    ->map(function ($booking) use ($request, $dateStr, $type) {
                        $booking->booking_icon_detail = $this->getDasboardBookingParameters($booking, $dateStr, $dateStr, $type);
                        return $booking;
                    });
                $nextArrivalDepartureList = $nextArrivalDepartureList->merge($checkinQuery)->merge($checkoutQuery);
        
                $currentDate->addDay();
            }
        }
        
     
        $checkoutOperator = (date('H:i') < '12:00') ? '>=' : '>';
        
        $queryCurrentOccupancy = PropertyBooking::query()
        ->when($request->has('type') && $request->type !='', function ($query) use ($searchDateFrom, $checkoutOperator) {
            return $query->where('checkin_date', '<=', date('Y-m-d'))
                     ->where('checkout_date', $checkoutOperator, date('Y-m-d'));
        })
        ->when($request->has('created_by') && $request->created_by !== '', function ($query) use ($request) {
            return $query->where('created_by', $request->created_by);
        });
        
        $currentOccupancyList = $queryCurrentOccupancy->with('home')->where('property_booking_status', 'Confirmed')->orderBy('id', 'desc')->get()->take(100);
            
        $currentOccupancyList->map(function ($booking) use ($request, $searchDateFrom, $searchDateTo, $type) {
            $booking->booking_icon_detail =  $this->getDasboardBookingParametersCurrentOccupancy($booking, $searchDateFrom, $searchDateTo, $type);
            return $booking;
        });    
        
        return response()->json([
            'status' => true,
            'nextArrivalDepartureList' => $nextArrivalDepartureList,
            'currentOccpancyList' =>$currentOccupancyList,
            'message' => 'Data fetched successfully.'
        ], 200);    
    }
    
    
    public function getDasboardBookingParametersCurrentOccupancy($booking, $searchDateFrom, $searchDateTo, $type){
        $icon = '';
            $text = '';
            $date = '';
            $type = 'today';
            if(date('Y-m-d', strtotime($booking->checkin_date)) == $searchDateFrom){
                $icon =  URL('/').'/assets/images/arrival.svg';
                $text = 'Check-in';
                $date = $booking->checkin_date;
                $type = 'checkin';
            }
            else if(date('Y-m-d', strtotime($booking->checkout_date)) == $searchDateFrom){
                $icon = URL('/').'/assets/images/departure.svg';
                $text = 'Check-out';
                $date = $booking->checkout_date;
                $type = 'checkout';
            }
            $color = 'bg-info';
            if($booking->channel=='Airbnb'){
                $color ='alert-danger';
            }
            else if($booking->channel=='Pms' || $booking->channel=='Website'){
                $color ='alert-info';
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
            
            return ['booking_icon'=>$icon, 'text'=>$text, 'date'=>$date, 'type'=>$type, 'alert_class'=>$color];
    }
    
    
    public function getDasboardBookingParameters($booking, $searchDateFrom, $searchDateTo, $type){
        $icon = '';
            $text = '';
            $date = '';
            $type = '';
            if(date('Y-m-d', strtotime($booking->checkin_date)) == $searchDateFrom){
                $icon =  URL('/').'/assets/images/arrival.svg';
                $text = 'Check-in';
                $date = $booking->checkin_date;
                $type = 'checkin';
            }
            else if(date('Y-m-d', strtotime($booking->checkout_date)) == $searchDateFrom){
                $icon = URL('/').'/assets/images/departure.svg';
                $text = 'Check-out';
                $date = $booking->checkout_date;
                $type = 'checkout';
            }
            $color = 'bg-info';
            if($booking->channel=='Airbnb'){
                $color ='alert-danger';
            }
            else if($booking->channel=='Pms' || $booking->channel=='Website'){
                $color ='alert-info';
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
            
            return ['booking_icon'=>$icon, 'text'=>$text, 'date'=>$date, 'type'=>$type, 'alert_class'=>$color];
    }
    
    public function property(){
        try{
            //  $units = TblHomeUnit::select('id','ru_property_id', 'unit_name as property_name')->whereNotNull('ru_property_id')->get()->map(function ($unit) {
            //     $unit->pType = 'unit';
            //     return $unit;
            // });
            // $multiUnits = TblHomeMultiUnit::select('id','ru_property_id', 'unit_name as property_name')->whereNotNull('ru_property_id')->get()->map(function ($multiUnit) {
            //     $multiUnit->pType = 'multiunit';
            //     return $multiUnit;
            // });
            // $property =  $units->concat($multiUnits);


            // First, build the query and apply the role filter on the query builder
            $unitsQuery = TblHomeUnit::select('id', 'ru_property_id', 'unit_name as property_name')
                ->whereNotNull('ru_property_id');
            $units = $this->propertyService->applyUserRoleFilter($unitsQuery)
                ->get() 
                ->map(function ($unit) {
                    $unit->pType = 'unit';  
                    return $unit;
                });

            $multiUnitsQuery = TblHomeMultiUnit::select('id', 'ru_property_id', 'unit_name as property_name')
                ->whereNotNull('ru_property_id');

            $multiUnits = $this->propertyService->applyUserRoleFilter($multiUnitsQuery)
                ->get()  
                ->map(function ($multiUnit) {
                    $multiUnit->pType = 'multiunit';  
                    return $multiUnit;
                });

            $property = $units->concat($multiUnits);

            return response()->json([
                'status' => true,
                'property' => $property,
                'message' => 'Successfully Retrive'
            ], 200);
        }catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => "Internal Error",
                'error' => $e->getMessage(),
            ], 500);
        }
        
    }
    public function location(){
        try{
            $location = TblLocation::select('id as location_id', 'location_name')->where('status',1)->orderby('location_name','asc')->get();
             //dd($location);
            return response()->json([
                'status' => true,
                'location' => $location,
                'message' => 'Successfully Retrive'
            ], 200);
        }catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => "Internal Error",
                'error' => $e->getMessage(),
            ], 500);
        }
        
    } 
    public function channel(){
        try{
            $channel = ['Airbnb','Booking.com','MakeMyTrip','Offline','RU','Website'];
            return response()->json([
                'status' => true,
                'channel' => $channel,
                'message' => 'Successfully Retrive'
            ], 200);
        }catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => "Internal Error",
                'error' => $e->getMessage(),
            ], 500);
        }
        
    } 
    

public function dashboardLineChart(Request $request)
{
    try {
        $user = Auth::guard('admin')->user();
        $location_id = (int) $request->location_id;
        $ru_property_id = (int) $request->ru_property_id;
        $type = $request->type;
        $from_date = $request->from_date;
        $to_date = $request->to_date;
        $pType = $request->pType;

        if ($ru_property_id) {
            $tblHome = ($request->pType == 'unit')
                ? TblHomeUnit::select('id as property_id')->where('ru_property_id', $ru_property_id)->first()
                : TblHomeMultiUnit::select('id as property_id')->where('ru_property_id', $ru_property_id)->first();
            $property_id = (int) ($tblHome->property_id ?? 0);
        } else {
            $property_id = 0;
        }

        $days = $request->days;

        if ($days) {
            if ($days == '7_next') {
                $number = preg_replace('/_next$/', '', $days);
                $next_7days = $number;

                $from_date = date('Y-m-d');
                $to_date = date('Y-m-d', strtotime("+{$next_7days} days"));

                $percentage_from_date = date('Y-m-d', strtotime("-7 days"));
                $percentage_to_date = date('Y-m-d');
            } else {
                $from_date = date('Y-m-d', strtotime("-{$days} days"));
                $to_date = date('Y-m-d', strtotime('-1 day'));
                if ($days == 60) {
                    $percentage_from_date = date('Y-m-d', strtotime("-120 days"));
                    $percentage_to_date = date('Y-m-d', strtotime("-60 days"));
                } elseif ($days == 90) {
                    $percentage_from_date = date('Y-m-d', strtotime("-180 days"));
                    $percentage_to_date = date('Y-m-d', strtotime("-90 days"));
                } elseif ($days == 30) {
                    $percentage_from_date = date('Y-m-d', strtotime("-60 days"));
                    $percentage_to_date = date('Y-m-d', strtotime("-30 days"));
                } elseif ($days == 7) {
                    $percentage_from_date = date('Y-m-d', strtotime("-14 days"));
                    $percentage_to_date = date('Y-m-d', strtotime("-7 days"));
                }
            }
        } elseif ($from_date && $to_date) {
            $from_date = date('Y-m-d', strtotime($from_date));
            $to_date = date('Y-m-d', strtotime($to_date));
            $percentage_from_date = null;
            $percentage_to_date = null;
        } else {
            $from_date = null;
            $to_date = null;
            $percentage_from_date = null;
            $percentage_to_date = null;
        }

        // Helper function to calculate percentage change
        $calculatePercentageChange = function ($current, $previous, $maxPercentage = 100) {
            if ($previous == 0) {
                return $current > 0 ? $maxPercentage : 0;
            }
            $change = (($current - $previous) / $previous) * 100;
            // Cap the percentage change
            if (abs($change) > $maxPercentage) {
                $change = $change > 0 ? $maxPercentage : -$maxPercentage;
            }
            return round($change, 2);
        };

        /* Net Revenue */
        $netRevenueValue = PropertyBooking::select(DB::raw('checkin_date as label, ROUND(SUM(total_amount), 0) as price'))
            ->whereNull('deleted_at')
           // ->where('payment_status', 'Paid')
            ->where('property_booking_status', 'Confirmed')
            ->when($location_id, function ($query, $location_id) {
                return $query->where('location_id', $location_id);
            })
            /* ->when($property_id, function ($query, $property_id) {
                return $query->where('property_id', $property_id);
            }) */

            ->when($property_id && $pType, function ($query) use ($property_id, $pType) {
                        return $query->where('property_id', $property_id)
                                    ->where('pType', $pType);
            })

             // Role-based filters for different users
            ->when($user->role_id != 1, function ($query) use ($user) {
                if (in_array($user->role_id, [3, 4, 5, 2])) {
                    return $query->where('property_bookings.parent_user_id', $user->parent_user_id);
                } elseif ($user->role_id == 6) {
                    return $query->where('property_bookings.owner_id', $user->id);
                } elseif ($user->role_id == 7) {
                    return $query->where('property_bookings.parent_user_id', $user->id);
                } elseif ($user->role_id == 8) {
                    return $query->where('property_bookings.travelagent_id', $user->id);
                }
            })



            
            ->whereBetween('checkin_date', [$from_date, $to_date])
            ->groupBy('checkin_date')
            ->orderBy('checkin_date', 'desc')
            ->get()
            ->toArray();

        $netRevenue = PropertyBooking::
            //where('payment_status', 'Paid')
            whereNull('deleted_at')
            ->where('property_booking_status', 'Confirmed')
            ->when($location_id, function ($query, $location_id) {
                return $query->where('location_id', $location_id);
            })
            /* ->when($property_id, function ($query, $property_id) {
                return $query->where('property_id', $property_id);
            }) */

            ->when($property_id && $pType, function ($query) use ($property_id, $pType) {
                return $query->where('property_id', $property_id)
                                    ->where('pType', $pType);
            })

            ->when($user->role_id != 1, function ($query) use ($user) {
                if (in_array($user->role_id, [3, 4, 5, 2])) {
                    return $query->where('property_bookings.parent_user_id', $user->parent_user_id);
                } elseif ($user->role_id == 6) {
                    return $query->where('property_bookings.owner_id', $user->id);
                } elseif ($user->role_id == 7) {
                    return $query->where('property_bookings.parent_user_id', $user->id);
                } elseif ($user->role_id == 8) {
                    return $query->where('property_bookings.travelagent_id', $user->id);
                }
            })

            ->whereBetween('checkin_date', [$from_date, $to_date])
            ->sum('total_amount');
        $netRevenue = (int) round($netRevenue);

        $perNetRevenue = PropertyBooking::
        //where('payment_status', 'Paid')
            whereNull('deleted_at')
            ->where('property_booking_status', 'Confirmed')
            ->when($location_id, function ($query, $location_id) {
                return $query->where('location_id', $location_id);
            })
            /* ->when($property_id, function ($query, $property_id) {
                return $query->where('property_id', $property_id);
            }) */

            ->when($property_id && $pType, function ($query) use ($property_id, $pType) {
                return $query->where('property_id', $property_id)
                                    ->where('pType', $pType);
            })

            ->when($user->role_id != 1, function ($query) use ($user) {
                if (in_array($user->role_id, [3, 4, 5, 2])) {
                    return $query->where('property_bookings.parent_user_id', $user->parent_user_id);
                } elseif ($user->role_id == 6) {
                    return $query->where('property_bookings.owner_id', $user->id);
                } elseif ($user->role_id == 7) {
                    return $query->where('property_bookings.parent_user_id', $user->id);
                } elseif ($user->role_id == 8) {
                    return $query->where('property_bookings.travelagent_id', $user->id);
                }
            })

            ->whereBetween('checkin_date', [$percentage_from_date, $percentage_to_date])
            ->sum('total_amount');
        $perNetRevenue = (int) round($perNetRevenue);

        $netRevenuePercentageChange = $calculatePercentageChange($netRevenue, $perNetRevenue);

        /* Average Price Per Night for Current Period */
        $averagePricesValue = PropertyBooking::selectRaw("checkin_date as label, AVG(CAST(per_night_price AS DECIMAL(10, 2))) AS price")
           ->whereNull('deleted_at')
        //->where('payment_status', 'Paid')
            ->where('property_booking_status', 'Confirmed')
            ->when($location_id, function ($query, $location_id) {
                return $query->where('location_id', $location_id);
            })
            /* ->when($property_id, function ($query, $property_id) {
                return $query->where('property_id', $property_id);
            }) */
            ->when($property_id && $pType, function ($query) use ($property_id, $pType) {
                return $query->where('property_id', $property_id)
                                    ->where('pType', $pType);
            })

            ->when($user->role_id != 1, function ($query) use ($user) {
                if (in_array($user->role_id, [3, 4, 5, 2])) {
                    return $query->where('property_bookings.parent_user_id', $user->parent_user_id);
                } elseif ($user->role_id == 6) {
                    return $query->where('property_bookings.owner_id', $user->id);
                } elseif ($user->role_id == 7) {
                    return $query->where('property_bookings.parent_user_id', $user->id);
                } elseif ($user->role_id == 8) {
                    return $query->where('property_bookings.travelagent_id', $user->id);
                }
            })


            ->whereBetween('checkin_date', [$from_date, $to_date])
            ->groupBy('checkin_date')
            ->orderBy('checkin_date', 'asc')
            ->get()
            ->toArray();

        $averagePrices = PropertyBooking::selectRaw("checkin_date as label, AVG(CAST(per_night_price AS DECIMAL(10, 2))) AS price")
            ->whereNull('deleted_at')
           //->where('payment_status', 'Paid')
            ->where('property_booking_status', 'Confirmed')
            ->when($location_id, function ($query, $location_id) {
                return $query->where('location_id', $location_id);
            })
            /* ->when($property_id, function ($query, $property_id) {
                return $query->where('property_id', $property_id);
            }) */

            ->when($property_id && $pType, function ($query) use ($property_id, $pType) {
                return $query->where('property_id', $property_id)
                                    ->where('pType', $pType);
            })

        ->when($user->role_id != 1, function ($query) use ($user) {
                if (in_array($user->role_id, [3, 4, 5, 2])) {
                    return $query->where('property_bookings.parent_user_id', $user->parent_user_id);
                } elseif ($user->role_id == 6) {
                    return $query->where('property_bookings.owner_id', $user->id);
                } elseif ($user->role_id == 7) {
                    return $query->where('property_bookings.parent_user_id', $user->id);
                } elseif ($user->role_id == 8) {
                    return $query->where('property_bookings.travelagent_id', $user->id);
                }
            })


            ->whereBetween('checkin_date', [$from_date, $to_date])
            ->groupBy('checkin_date')
            ->orderBy('checkin_date', 'asc')
            ->get();

        $sumAveragePrice = $averagePrices->sum('price');
        $totalCount = $averagePrices->count();
        $averagePricePerNight = $totalCount > 0 ? $sumAveragePrice / $totalCount : 0;
        $averagePricePerNight = (int) round($averagePricePerNight);

        /* Average Price Per Night for Previous Period */
        $perAveragePrices = PropertyBooking::selectRaw("checkin_date as label, AVG(CAST(per_night_price AS DECIMAL(10, 2))) AS price")
            ->whereNull('deleted_at')
        //->where('payment_status', 'Paid')
            ->where('property_booking_status', 'Confirmed')
            ->when($location_id, function ($query, $location_id) {
                return $query->where('location_id', $location_id);
            })
            /* ->when($property_id, function ($query, $property_id) {
                return $query->where('property_id', $property_id);
            }) */
            ->when($property_id && $pType, function ($query) use ($property_id, $pType) {
                return $query->where('property_id', $property_id)
                                    ->where('pType', $pType);
            })

            ->when($user->role_id != 1, function ($query) use ($user) {
                if (in_array($user->role_id, [3, 4, 5, 2])) {
                    return $query->where('property_bookings.parent_user_id', $user->parent_user_id);
                } elseif ($user->role_id == 6) {
                    return $query->where('property_bookings.owner_id', $user->id);
                } elseif ($user->role_id == 7) {
                    return $query->where('property_bookings.parent_user_id', $user->id);
                } elseif ($user->role_id == 8) {
                    return $query->where('property_bookings.travelagent_id', $user->id);
                }
            })

            ->whereBetween('checkin_date', [$percentage_from_date, $percentage_to_date])
            ->groupBy('checkin_date')
            ->orderBy('checkin_date', 'asc')
            ->get();

        $sumPerAveragePrice = $perAveragePrices->sum('price');
        $totalPerCount = $perAveragePrices->count();
        $perAveragePricePerNight = $totalPerCount > 0 ? $sumPerAveragePrice / $totalPerCount : 0;
        $perAveragePricePerNight = (int) round($perAveragePricePerNight);

        $averagePercentagePerNightChange = $calculatePercentageChange($averagePricePerNight, $perAveragePricePerNight);

        /* Created Bookings for Current Period */
        $createdBookingsValue = DB::table('property_bookings')
            ->select(
                DB::raw('DATE_FORMAT(checkin_date, "%Y-%m-%d") AS label'),
                DB::raw('ROUND(SUM(payable_amount), 0) AS price')
            )
            ->whereNull('deleted_at')
           // ->where('payment_status', 'Paid')
            ->where('property_booking_status', 'Confirmed')
            /* ->when($property_id, function ($query, $property_id) {
                return $query->where('property_id', $property_id);
            }) */
            ->when($property_id && $pType, function ($query) use ($property_id, $pType) {
                return $query->where('property_id', $property_id)
                                    ->where('pType', $pType);
            })

        ->when($user->role_id != 1, function ($query) use ($user) {
                if (in_array($user->role_id, [3, 4, 5, 2])) {
                    return $query->where('property_bookings.parent_user_id', $user->parent_user_id);
                } elseif ($user->role_id == 6) {
                    return $query->where('property_bookings.owner_id', $user->id);
                } elseif ($user->role_id == 7) {
                    return $query->where('property_bookings.parent_user_id', $user->id);
                } elseif ($user->role_id == 8) {
                    return $query->where('property_bookings.travelagent_id', $user->id);
                }
            })

            ->when($location_id, function ($query, $location_id) {
                return $query->where('location_id', $location_id);
            })
            ->whereBetween('checkin_date', [$from_date, $to_date])
            ->groupBy(DB::raw('DATE_FORMAT(checkin_date, "%Y-%m-%d")'))
            ->orderBy(DB::raw('DATE_FORMAT(checkin_date, "%Y-%m-%d")'), 'desc')
            ->get();

        $createdBookings = DB::table('property_bookings')
        ->whereNull('deleted_at')
            //->where('payment_status', 'Paid')
            ->where('property_booking_status', 'Confirmed')
            /* ->when($property_id, function ($query, $property_id) {
                return $query->where('property_id', $property_id);
            }) */

            ->when($property_id && $pType, function ($query) use ($property_id, $pType) {
                return $query->where('property_id', $property_id)
                                    ->where('pType', $pType);
            })

        ->when($user->role_id != 1, function ($query) use ($user) {
                if (in_array($user->role_id, [3, 4, 5, 2])) {
                    return $query->where('property_bookings.parent_user_id', $user->parent_user_id);
                } elseif ($user->role_id == 6) {
                    return $query->where('property_bookings.owner_id', $user->id);
                } elseif ($user->role_id == 7) {
                    return $query->where('property_bookings.parent_user_id', $user->id);
                } elseif ($user->role_id == 8) {
                    return $query->where('property_bookings.travelagent_id', $user->id);
                }
            })

            ->when($location_id, function ($query, $location_id) {
                return $query->where('location_id', $location_id);
            })
            ->whereBetween('checkin_date', [$from_date, $to_date])
            ->count();

        /* Created Bookings for Previous Period */
        $perCreatedBookings = DB::table('property_bookings')
        ->whereNull('deleted_at')
            //->where('payment_status', 'Paid')
            ->where('property_booking_status', 'Confirmed')
            /* ->when($property_id, function ($query, $property_id) {
                return $query->where('property_id', $property_id);
            }) */
            ->when($property_id && $pType, function ($query) use ($property_id, $pType) {
                return $query->where('property_id', $property_id)
                                    ->where('pType', $pType);
            })

            ->when($user->role_id != 1, function ($query) use ($user) {
                if (in_array($user->role_id, [3, 4, 5, 2])) {
                    return $query->where('property_bookings.parent_user_id', $user->parent_user_id);
                } elseif ($user->role_id == 6) {
                    return $query->where('property_bookings.owner_id', $user->id);
                } elseif ($user->role_id == 7) {
                    return $query->where('property_bookings.parent_user_id', $user->id);
                } elseif ($user->role_id == 8) {
                    return $query->where('property_bookings.travelagent_id', $user->id);
                }
            })

            ->when($location_id, function ($query, $location_id) {
                return $query->where('location_id', $location_id);
            })
            ->whereBetween('checkin_date', [$percentage_from_date, $percentage_to_date])
            ->count();

        $createdBookingsPercentageChange = $calculatePercentageChange($createdBookings, $perCreatedBookings);

        /* Nights Filled for Current Period */
        $nightFilledValue = DB::table('property_bookings')
            ->selectRaw('DATE(checkin_date) AS label, COALESCE(SUM(no_of_nights), 0) AS price')
            ->whereNull('deleted_at')
            //->where('payment_status', 'Paid')
            ->where('property_booking_status', 'Confirmed')
            ->when($location_id, function ($query, $location_id) {
                return $query->where('location_id', $location_id);
            })
           /*  ->when($property_id, function ($query, $property_id) {
                return $query->where('property_id', $property_id);
            }) */

            ->when($property_id && $pType, function ($query) use ($property_id, $pType) {
                return $query->where('property_id', $property_id)
                                    ->where('pType', $pType);
            })

            ->when($user->role_id != 1, function ($query) use ($user) {
                if (in_array($user->role_id, [3, 4, 5, 2])) {
                    return $query->where('property_bookings.parent_user_id', $user->parent_user_id);
                } elseif ($user->role_id == 6) {
                    return $query->where('property_bookings.owner_id', $user->id);
                } elseif ($user->role_id == 7) {
                    return $query->where('property_bookings.parent_user_id', $user->id);
                } elseif ($user->role_id == 8) {
                    return $query->where('property_bookings.travelagent_id', $user->id);
                }
            })

            ->whereBetween('checkin_date', [$from_date, $to_date])
            ->groupBy(DB::raw('DATE(checkin_date)'))
            ->orderBy(DB::raw('DATE(checkin_date)'), 'DESC')
            ->get()
            ->toArray();

        $nightFilled = DB::table('property_bookings')
            ->when($location_id, function ($query, $location_id) {
                return $query->where('location_id', $location_id);
            })
            /* ->when($property_id, function ($query, $property_id) {
                return $query->where('property_id', $property_id);
            }) */

            ->when($property_id && $pType, function ($query) use ($property_id, $pType) {
                return $query->where('property_id', $property_id)
                                    ->where('pType', $pType);
            })

            ->when($user->role_id != 1, function ($query) use ($user) {
                if (in_array($user->role_id, [3, 4, 5, 2])) {
                    return $query->where('property_bookings.parent_user_id', $user->parent_user_id);
                } elseif ($user->role_id == 6) {
                    return $query->where('property_bookings.owner_id', $user->id);
                } elseif ($user->role_id == 7) {
                    return $query->where('property_bookings.parent_user_id', $user->id);
                } elseif ($user->role_id == 8) {
                    return $query->where('property_bookings.travelagent_id', $user->id);
                }
            })


            ->whereBetween('checkin_date', [$from_date, $to_date])
            ->whereNull('deleted_at')
            //->where('payment_status', 'Paid')
            ->where('property_booking_status', 'Confirmed')
            ->sum('no_of_nights');
        $nightFilled = (int) $nightFilled;

        /* Nights Filled for Previous Period */
        $perNightFilled = DB::table('property_bookings')
            ->when($location_id, function ($query, $location_id) {
                return $query->where('location_id', $location_id);
            })
            /* ->when($property_id, function ($query, $property_id) {
                return $query->where('property_id', $property_id);
            }) */

            ->when($property_id && $pType, function ($query) use ($property_id, $pType) {
                return $query->where('property_id', $property_id)
                                    ->where('pType', $pType);
            })

            ->when($user->role_id != 1, function ($query) use ($user) {
                if (in_array($user->role_id, [3, 4, 5, 2])) {
                    return $query->where('property_bookings.parent_user_id', $user->parent_user_id);
                } elseif ($user->role_id == 6) {
                    return $query->where('property_bookings.owner_id', $user->id);
                } elseif ($user->role_id == 7) {
                    return $query->where('property_bookings.parent_user_id', $user->id);
                } elseif ($user->role_id == 8) {
                    return $query->where('property_bookings.travelagent_id', $user->id);
                }
            })

            ->whereBetween('checkin_date', [$percentage_from_date, $percentage_to_date])
            //->where('payment_status', 'Paid')
            ->whereNull('deleted_at')
            ->where('property_booking_status', 'Confirmed')
            ->sum('no_of_nights');
        $perNightFilled = (int) $perNightFilled;

        $nightFilledPercentageChange = $calculatePercentageChange($nightFilled, $perNightFilled);

        /* Occupancy Rates */
        $filters = [
           // ['property_bookings.payment_status', '=', 'Paid'],
            ['property_bookings.property_booking_status', '=', 'Confirmed'],
            ['property_bookings.checkin_date', '>=', $from_date],
            ['property_bookings.checkin_date', '<=', $to_date],
        ];

        $unitResults = DB::table('tbl_home_units')
            ->join('property_bookings', 'tbl_home_units.id', '=', 'property_bookings.property_id')
            ->whereNull('property_bookings.deleted_at')
            ->selectRaw('DATE(property_bookings.checkin_date) AS label, COUNT(property_bookings.id) AS price')
            ->where($filters)
            ->when($location_id, function ($query, $location_id) {
                return $query->where('property_bookings.location_id', $location_id);
            })
            /* ->when($property_id, function ($query, $property_id) {
                return $query->where('property_bookings.property_id', $property_id);
            }) */

            ->when($property_id && $pType, function ($query) use ($property_id, $pType) {
                return $query->where('property_id', $property_id)
                                    ->where('pType', $pType);
            })
            ->when($user->role_id != 1, function ($query) use ($user) {
                if (in_array($user->role_id, [3, 4, 5, 2])) {
                    return $query->where('property_bookings.parent_user_id', $user->parent_user_id);
                } elseif ($user->role_id == 6) {
                    return $query->where('property_bookings.owner_id', $user->id);
                } elseif ($user->role_id == 7) {
                    return $query->where('property_bookings.parent_user_id', $user->id);
                } elseif ($user->role_id == 8) {
                    return $query->where('property_bookings.travelagent_id', $user->id);
                }
            })

            ->groupBy(DB::raw('DATE(property_bookings.checkin_date)'))
            ->orderBy(DB::raw('DATE(property_bookings.checkin_date)'), 'DESC')
            ->get();

        $multiUnitResults = DB::table('tbl_home_multi_units')
            ->join('property_bookings', 'tbl_home_multi_units.id', '=', 'property_bookings.property_id')
             ->whereNull('property_bookings.deleted_at')
            ->selectRaw('DATE(property_bookings.checkin_date) AS label, COUNT(property_bookings.id) AS price')
            ->where($filters)
            ->when($location_id, function ($query, $location_id) {
                return $query->where('property_bookings.location_id', $location_id);
            })
            /* ->when($property_id, function ($query, $property_id) {
                return $query->where('property_id', $property_id);
            }) */

            ->when($property_id && $pType, function ($query) use ($property_id, $pType) {
                return $query->where('property_id', $property_id)
                                    ->where('pType', $pType);
            })

            ->when($user->role_id != 1, function ($query) use ($user) {
                if (in_array($user->role_id, [3, 4, 5, 2])) {
                    return $query->where('property_bookings.parent_user_id', $user->parent_user_id);
                } elseif ($user->role_id == 6) {
                    return $query->where('property_bookings.owner_id', $user->id);
                } elseif ($user->role_id == 7) {
                    return $query->where('property_bookings.parent_user_id', $user->id);
                } elseif ($user->role_id == 8) {
                    return $query->where('property_bookings.travelagent_id', $user->id);
                }
            })

            ->groupBy(DB::raw('DATE(property_bookings.checkin_date)'))
            ->orderBy(DB::raw('DATE(property_bookings.checkin_date)'), 'DESC')
            ->get();

        $results = $unitResults->merge($multiUnitResults);

        // Total units and multi-units
        // $unitIds = TblHomeUnit::whereNotNull('ru_property_id')->pluck('id')->toArray();
        // $multiUnitIds = TblHomeMultiUnit::whereNotNull('ru_property_id')->pluck('id')->toArray();
        // $totalProperties = count($unitIds) + count($multiUnitIds);

            $unitIds = $this->propertyService->applyUserRoleFilter(
                TblHomeUnit::whereNotNull('ru_property_id')
            )->pluck('id')->toArray();
            $multiUnitIds = $this->propertyService->applyUserRoleFilter(
                TblHomeMultiUnit::whereNotNull('ru_property_id')
            )->pluck('id')->toArray();
            $allIds = array_merge($unitIds, $multiUnitIds);
            $totalProperties = count($allIds);



        // Current Period Booked Property IDs
        $bookedIds = DB::table('property_bookings')
        ->whereNull('deleted_at')
           // ->where('payment_status', 'Paid')
            ->where('property_booking_status', 'Confirmed')
            ->when($location_id, fn($q) => $q->where('location_id', $location_id))
           // ->when($property_id, fn($q) => $q->where('property_id', $property_id))
           ->when($property_id && $pType, fn($q) => $q->where('property_id', $property_id)->where('pType', $pType))

                ->when($user->role_id != 1, function ($query) use ($user) {
                    if (in_array($user->role_id, [3, 4, 5, 2])) {
                        return $query->where('property_bookings.parent_user_id', $user->parent_user_id);
                    } elseif ($user->role_id == 6) {
                        return $query->where('property_bookings.owner_id', $user->id);
                    } elseif ($user->role_id == 7) {
                        return $query->where('property_bookings.parent_user_id', $user->id);
                    } elseif ($user->role_id == 8) {
                        return $query->where('property_bookings.travelagent_id', $user->id);
                    }
                })

            ->whereBetween('checkin_date', [$from_date, $to_date])
            ->pluck('property_id')
            ->unique()
            ->toArray();

        $bookedUnitCount = count(array_intersect($bookedIds, $unitIds));
        $bookedMultiUnitCount = count(array_intersect($bookedIds, $multiUnitIds));
        $bookedTotal = $bookedUnitCount + $bookedMultiUnitCount;

        $occupancyPercentage = ($totalProperties > 0)
            ? round(($bookedTotal * 100) / $totalProperties, 1)
            : 0;

        // Previous Period Booked Property IDs
        $prevBookedIds = DB::table('property_bookings')
        ->whereNull('deleted_at')
            //->where('payment_status', 'Paid')
            ->where('property_booking_status', 'Confirmed')
            ->when($location_id, fn($q) => $q->where('location_id', $location_id))
           // ->when($property_id, fn($q) => $q->where('property_id', $property_id))
           ->when($property_id && $pType, fn($q) => $q->where('property_id', $property_id)->where('pType', $pType))

            ->when($user->role_id != 1, function ($query) use ($user) {
                    if (in_array($user->role_id, [3, 4, 5, 2])) {
                        return $query->where('property_bookings.parent_user_id', $user->parent_user_id);
                    } elseif ($user->role_id == 6) {
                        return $query->where('property_bookings.owner_id', $user->id);
                    } elseif ($user->role_id == 7) {
                        return $query->where('property_bookings.parent_user_id', $user->id);
                    } elseif ($user->role_id == 8) {
                        return $query->where('property_bookings.travelagent_id', $user->id);
                    }
                })


            ->whereBetween('checkin_date', [$percentage_from_date, $percentage_to_date])
            ->pluck('property_id')
            ->unique()
            ->toArray();

        $prevBookedUnitCount = count(array_intersect($prevBookedIds, $unitIds));
        $prevBookedMultiUnitCount = count(array_intersect($prevBookedIds, $multiUnitIds));
        $prevBookedTotal = $prevBookedUnitCount + $prevBookedMultiUnitCount;

        $occupancyPreviousPercentage = ($totalProperties > 0)
            ? round(($prevBookedTotal * 100) / $totalProperties, 1)
            : 0;

        $percentageChange = round($occupancyPercentage - $occupancyPreviousPercentage, 1);

        return response()->json([
            'status' => true,
            'data' => [
                0 => [
                    'data' => $netRevenueValue,
                    'value' => $netRevenue,
                    'type' => 'price',
                    'percentage' => $netRevenuePercentageChange,
                    'title' => 'Net Revenue',
                    'url' => 'net-revenue'
                ],
                1 => [
                    'data' => $averagePricesValue,
                    'value' => $averagePricePerNight,
                    'type' => 'price',
                    'percentage' => $averagePercentagePerNightChange,
                    'title' => 'Average Price Per Night',
                    'url' => 'average-price-per-night'
                ],
                2 => [
                    'data' => $createdBookingsValue,
                    'value' => $createdBookings,
                    'type' => 'number',
                    'percentage' => $createdBookingsPercentageChange,
                    'title' => 'Bookings Created',
                    'url' => 'bookings-created'
                ],
                3 => [
                    'data' => $nightFilledValue,
                    'value' => $nightFilled,
                    'type' => 'number',
                    'percentage' => $nightFilledPercentageChange,
                    'title' => 'Nights Filled',
                    'url' => ''
                ],
                4 => [
                    'data' => $results,
                    'value' => $occupancyPercentage,
                    'type' => 'percentage',
                    'percentage' => $percentageChange,
                    'title' => 'Occupancy Rates',
                    'url' => ''
                ]
            ],
            'message' => 'Successfully Retrieved'
        ], 200);
    } catch (\Exception $e) {
        return response()->json([
            'status' => false,
            'message' => "Internal Error",
            'error' => $e->getMessage(),
        ], 500);
    }
}
    
    
    public function dashboardWeeklyReport(Request $request){
        DB::statement("SET SESSION sql_mode=(SELECT REPLACE(@@sql_mode, 'ONLY_FULL_GROUP_BY', ''))");
        try{
            $user = Auth::guard('admin')->user();
            $location_id = (int) $request->location_id;
            $ru_property_id = (int) $request->ru_property_id;
            $type = $request->type;
            $from_date = $request->from_date;
            $to_date = $request->to_date;
            $pType = $request->pType;
            if($ru_property_id){
               // $tblHome = TblHome::select('id as property_id')->where('ru_property_id',$ru_property_id)->where('status',1)->first();
               // $property_id = (int) $tblHome->property_id ?? 0;
            $tblHome = ($request->pType == 'unit')
                ? TblHomeUnit::select('id as property_id')->where('ru_property_id', $ru_property_id)->first()
                : TblHomeMultiUnit::select('id as property_id')->where('ru_property_id', $ru_property_id)->first();
            $property_id = (int) $tblHome->property_id ?? 0;



            }else{
                $property_id = 0;
            }
            
            $days = $request->days;
            
            if($days){
                if($days == '7_next'){
                    $number = preg_replace('/_next$/', '', $days);
                    $next_7days = $number;
                    
                    $from_date = date('Y-m-d');
                    
                    $to_date = date('Y-m-d', strtotime("+{$next_7days} days"));
                    
                } else {
                    $from_date = date('Y-m-d', strtotime("-{$days} days"));
                    $to_date = date('Y-m-d', strtotime('-1 day'));
                }
            } elseif($from_date && $to_date) {
                $from_date = date('Y-m-d', strtotime($from_date));
                $to_date = date('Y-m-d', strtotime($to_date));
            } else {
                $from_date = null;
                $to_date = null;
            }
            
            /* Weekly Revenue Analysis*/
            if($type == 'gross'){
                $weeklyRevenue = PropertyBooking::
                //where('payment_status', 'Paid')
                whereNull('deleted_at')
                    ->where('property_booking_status', 'Confirmed')
                    ->when($location_id, function ($query, $location_id) {
                        return $query->where('location_id', $location_id);
                    })
                    /* ->when($property_id, function ($query, $property_id) {
                        return $query->where('property_id', $property_id);
                    }) */

                    ->when($property_id && $pType, function ($query) use ($property_id, $pType) {
                        return $query->where('property_id', $property_id)
                                    ->where('pType', $pType);
                    })

                ->when($user->role_id != 1, function ($query) use ($user) {
                    if (in_array($user->role_id, [3, 4, 5, 2])) {
                        return $query->where('property_bookings.parent_user_id', $user->parent_user_id);
                    } elseif ($user->role_id == 6) {
                        return $query->where('property_bookings.owner_id', $user->id);
                    } elseif ($user->role_id == 7) {
                        return $query->where('property_bookings.parent_user_id', $user->id);
                    } elseif ($user->role_id == 8) {
                        return $query->where('property_bookings.travelagent_id', $user->id);
                    }
                })


                    ->whereBetween('checkin_date', [$from_date, $to_date])
                    ->select(
                        DB::raw("WEEK(checkin_date) as week_number"),
                        DB::raw("YEAR(checkin_date) as year"),
                        DB::raw("SUM(payable_amount) as total_revenue"),
                        DB::raw("DATE_FORMAT(checkin_date, '%Y-%m-%d') as price_date")
                    )
                    ->groupBy('year', 'week_number')
                    ->orderBy('year', 'asc')
                    ->orderBy('week_number', 'asc')
                    ->get()
                    ->toArray();
                
                // dd($weeklyRevenue);
                    
            }else{
                $weeklyRevenue = PropertyBooking::
                //where('payment_status', 'Paid')
                whereNull('deleted_at')
                    ->where('property_booking_status', 'Confirmed')
                    ->when($location_id, function ($query, $location_id) {
                        return $query->where('location_id', $location_id);
                    })
                    /* ->when($property_id, function ($query, $property_id) {
                        return $query->where('property_id', $property_id);
                    }) */

                    ->when($property_id && $pType, function ($query) use ($property_id, $pType) {
                        return $query->where('property_id', $property_id)
                                    ->where('pType', $pType);
                    })

                    ->when($user->role_id != 1, function ($query) use ($user) {
                    if (in_array($user->role_id, [3, 4, 5, 2])) {
                        return $query->where('property_bookings.parent_user_id', $user->parent_user_id);
                    } elseif ($user->role_id == 6) {
                        return $query->where('property_bookings.owner_id', $user->id);
                    } elseif ($user->role_id == 7) {
                        return $query->where('property_bookings.parent_user_id', $user->id);
                    } elseif ($user->role_id == 8) {
                        return $query->where('property_bookings.travelagent_id', $user->id);
                    }
                })

                    ->whereBetween('checkin_date', [$from_date, $to_date])
                    ->select(
                        DB::raw("WEEK(checkin_date) as week_number"),
                        DB::raw("YEAR(checkin_date) as year"),
                        DB::raw("SUM(total_amount) as total_revenue"),
                        DB::raw("DATE_FORMAT(checkin_date, '%Y-%m-%d') as price_date")
                    )
                    ->groupBy('year', 'week_number')
                    ->orderBy('year', 'asc')
                    ->orderBy('week_number', 'asc')
                    ->get()
                    ->toArray();
            }
            
          
            
            return response()->json([
                'status' => true,
                'data' => $weeklyRevenue,
                'message' => 'Successfully Retrive'
            ], 200);
            
        }catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => "Internal Error",
                'error' => $e->getMessage(),
            ], 500);
        }
    }    
    
    public function dashboardChannelRevenue(Request $request){
        try{
            $user = Auth::guard('admin')->user();
            $location_id = (int) $request->location_id;
            $ru_property_id = (int) $request->ru_property_id;
            $type = $request->type;
            
            $from_date = $request->from_date;
            $to_date = $request->to_date;
            $pType = $request->pType;
            if($ru_property_id){
               // $tblHome = TblHome::select('id as property_id')->where('ru_property_id',$ru_property_id)->where('status',1)->first();
               // $property_id = (int) $tblHome->property_id ?? 0;

            $tblHome = ($request->pType == 'unit')
                ? TblHomeUnit::select('id as property_id')->where('ru_property_id', $ru_property_id)->first()
                : TblHomeMultiUnit::select('id as property_id')->where('ru_property_id', $ru_property_id)->first();
            $property_id = (int) $tblHome->property_id ?? 0;

            }else{
                $property_id = 0;
            }
            
            $days = $request->days;
            
           if($days){
                if($days == '7_next'){
                    $number = preg_replace('/_next$/', '', $days);
                    $next_7days = $number;
                    
                    $from_date = date('Y-m-d');
                    
                    $to_date = date('Y-m-d', strtotime("+{$next_7days} days"));
                    
                } else {
                    $from_date = date('Y-m-d', strtotime("-{$days} days"));
                    $to_date = date('Y-m-d', strtotime('-1 day'));
                }
            } elseif($from_date && $to_date) {
                $from_date = date('Y-m-d', strtotime($from_date));
                $to_date = date('Y-m-d', strtotime($to_date));
            } else {
                $from_date = null;
                $to_date = null;
            }
            
            /* Channel Distribution / Revenue*/
            if($type == 'gross'){
                 $channelDistributionRevenue = DB::table('property_bookings')
                 ->whereNull('deleted_at')
                    ->where('property_booking_status', 'Confirmed')
                    ->select('channel', DB::raw('COUNT(*) as total_count'), DB::raw('ROUND(SUM(payable_amount), 0) as total_amount'))
                    ->when($location_id, function ($query, $location_id) {
                        return $query->where('location_id', $location_id);
                    })
                    /* ->when($property_id, function ($query, $property_id) {
                        return $query->where('property_id', $property_id);
                    }) */

                    ->when($property_id && $pType, function ($query) use ($property_id, $pType) {
                        return $query->where('property_id', $property_id)
                                    ->where('pType', $pType);
                    })

                    ->when($user->role_id != 1, function ($query) use ($user) {
                    if (in_array($user->role_id, [3, 4, 5, 2])) {
                        return $query->where('property_bookings.parent_user_id', $user->parent_user_id);
                    } elseif ($user->role_id == 6) {
                        return $query->where('property_bookings.owner_id', $user->id);
                    } elseif ($user->role_id == 7) {
                        return $query->where('property_bookings.parent_user_id', $user->id);
                    } elseif ($user->role_id == 8) {
                        return $query->where('property_bookings.travelagent_id', $user->id);
                    }
                })
                    
                    ->whereBetween('checkin_date', [$from_date, $to_date])
                    //->where('payment_status', 'Paid')
                    ->groupBy('channel')
                    ->get();
                    
                    
                $totalChannelRevenue = DB::table('property_bookings')
                    ->when($location_id, function ($query, $location_id) {
                        return $query->where('location_id', $location_id);
                    })
                    /* ->when($property_id, function ($query, $property_id) {
                        return $query->where('property_id', $property_id);
                    }) */

                    ->when($property_id && $pType, function ($query) use ($property_id, $pType) {
                        return $query->where('property_id', $property_id)
                                    ->where('pType', $pType);
                    })


                    ->when($user->role_id != 1, function ($query) use ($user) {
                    if (in_array($user->role_id, [3, 4, 5, 2])) {
                        return $query->where('property_bookings.parent_user_id', $user->parent_user_id);
                    } elseif ($user->role_id == 6) {
                        return $query->where('property_bookings.owner_id', $user->id);
                    } elseif ($user->role_id == 7) {
                        return $query->where('property_bookings.parent_user_id', $user->id);
                    } elseif ($user->role_id == 8) {
                        return $query->where('property_bookings.travelagent_id', $user->id);
                    }
                })

                    ->whereBetween('checkin_date', [$from_date, $to_date])
                    //->where('payment_status', 'Paid')
                    ->whereNull('deleted_at')
                    ->where('property_booking_status', 'Confirmed')
                    ->groupBy('channel')
                    ->select(DB::raw('SUM(payable_amount) as total_revenue'))
                    ->get();
                
                $totalChannelRevenueSum = (int) round($totalChannelRevenue->sum('total_revenue'));
                
            }else{
                
                $channelDistributionRevenue = DB::table('property_bookings')
                    ->select('channel', DB::raw('COUNT(*) as total_count'), DB::raw('ROUND(SUM(total_amount), 0) as total_amount'))
                    ->when($location_id, function ($query, $location_id) {
                        return $query->where('location_id', $location_id);
                    })
                    /* ->when($property_id, function ($query, $property_id) {
                        return $query->where('property_id', $property_id);
                    }) */

                    ->when($property_id && $pType, function ($query) use ($property_id, $pType) {
                        return $query->where('property_id', $property_id)
                                    ->where('pType', $pType);
                    })

                    ->when($user->role_id != 1, function ($query) use ($user) {
                    if (in_array($user->role_id, [3, 4, 5, 2])) {
                        return $query->where('property_bookings.parent_user_id', $user->parent_user_id);
                    } elseif ($user->role_id == 6) {
                        return $query->where('property_bookings.owner_id', $user->id);
                    } elseif ($user->role_id == 7) {
                        return $query->where('property_bookings.parent_user_id', $user->id);
                    } elseif ($user->role_id == 8) {
                        return $query->where('property_bookings.travelagent_id', $user->id);
                    }
                })

                    ->whereBetween('checkin_date', [$from_date, $to_date])
                    //->where('payment_status', 'Paid')
                    ->whereNull('deleted_at')
                    ->where('property_booking_status', 'Confirmed')
                    ->groupBy('channel')
                    ->get();
                    
                $totalChannelRevenue = DB::table('property_bookings')
                    ->when($location_id, function ($query, $location_id) {
                        return $query->where('location_id', $location_id);
                    })
                    /* ->when($property_id, function ($query, $property_id) {
                        return $query->where('property_id', $property_id);
                    }) */

                    ->when($property_id && $pType, function ($query) use ($property_id, $pType) {
                        return $query->where('property_id', $property_id)
                                    ->where('pType', $pType);
                    })

                    ->when($user->role_id != 1, function ($query) use ($user) {
                        if (in_array($user->role_id, [3, 4, 5, 2])) {
                            return $query->where('property_bookings.parent_user_id', $user->parent_user_id);
                        } elseif ($user->role_id == 6) {
                            return $query->where('property_bookings.owner_id', $user->id);
                        } elseif ($user->role_id == 7) {
                            return $query->where('property_bookings.parent_user_id', $user->id);
                        } elseif ($user->role_id == 8) {
                            return $query->where('property_bookings.travelagent_id', $user->id);
                        }
                    })

                    ->whereBetween('checkin_date', [$from_date, $to_date])
                    //->where('payment_status', 'Paid')
                    ->whereNull('deleted_at')
                    ->where('property_booking_status', 'Confirmed')
                    ->groupBy('channel')
                    ->select(DB::raw('SUM(total_amount) as total_revenue'))
                    ->get();
                
                $totalChannelRevenueSum = (int) round($totalChannelRevenue->sum('total_revenue'));
            }
            
            return response()->json([
                'status' => true,
                'data' => [
                    'channel_distribution_revenue' => $channelDistributionRevenue,
                    'total_channel_revenue' => $totalChannelRevenueSum,
                ],
                'message' => 'Successfully Retrive'
            ], 200);
            
        }catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => "Internal Error",
                'error' => $e->getMessage(),
            ], 500);
        }
    } 
    
    public function dashboardAnalytics(Request $request){
        try{
            $user = Auth::guard('admin')->user();
            $location_id = (int) $request->location_id;
            $ru_property_id = (int) $request->ru_property_id;
            $type = $request->type;
            $from_date = $request->from_date;
            $to_date = $request->to_date;
            $pType = $request->pType;
            if($ru_property_id){
               // $tblHome = TblHome::select('id as property_id')->where('ru_property_id',$ru_property_id)->where('status',1)->first();
              //  $property_id = (int) $tblHome->property_id ?? 0;
            $tblHome = ($request->pType == 'unit')
                ? TblHomeUnit::select('id as property_id')->where('ru_property_id', $ru_property_id)->first()
                : TblHomeMultiUnit::select('id as property_id')->where('ru_property_id', $ru_property_id)->first();
            $property_id = (int) $tblHome->property_id ?? 0;

              
            }else{
                $property_id = 0;
            }
            
            $days = $request->days;
            
            if($days){
                if($days == '7_next'){
                    $number = preg_replace('/_next$/', '', $days);
                    $next_7days = $number;
                    
                    $from_date = date('Y-m-d');
                    
                    $to_date = date('Y-m-d', strtotime("+{$next_7days} days"));
                    
                } else {
                    $from_date = date('Y-m-d', strtotime("-{$days} days"));
                    $to_date = date('Y-m-d', strtotime('-1 day'));
                }
            } elseif($from_date && $to_date) {
                $from_date = date('Y-m-d', strtotime($from_date));
                $to_date = date('Y-m-d', strtotime($to_date));
            } else {
                $from_date = null;
                $to_date = null;
            }

       
            /*Average Length of Stay*/
            $averageLengthOfStay = PropertyBooking::
            //where('payment_status', 'Paid')
            whereNull('deleted_at')
            ->where('property_booking_status', 'Confirmed')
                ->when($location_id, function ($query, $location_id) {
                    return $query->where('location_id', $location_id);
                })
                /* ->when($property_id, function ($query, $property_id) {
                    return $query->where('property_id', $property_id);
                }) */

                ->when($property_id && $pType, function ($query) use ($property_id, $pType) {
                        return $query->where('property_id', $property_id)
                                    ->where('pType', $pType);
                })
                ->when($user->role_id != 1, function ($query) use ($user) {
                        if (in_array($user->role_id, [3, 4, 5, 2])) {
                            return $query->where('property_bookings.parent_user_id', $user->parent_user_id);
                        } elseif ($user->role_id == 6) {
                            return $query->where('property_bookings.owner_id', $user->id);
                        } elseif ($user->role_id == 7) {
                            return $query->where('property_bookings.parent_user_id', $user->id);
                        } elseif ($user->role_id == 8) {
                            return $query->where('property_bookings.travelagent_id', $user->id);
                        }
                    })

                ->whereBetween('checkin_date', [$from_date, $to_date])
                ->avg('no_of_nights');
                //   dd($averageLengthOfStay);
            $averageLengthOfStay = (int) round($averageLengthOfStay) ?? 0; //in Nights
            $nightName = ($averageLengthOfStay == 1) ? 'Night' : 'Nights';
           
            /*Average Lead Time*/
            $averageLeadTime = DB::table('property_bookings')
            ->whereNull('deleted_at')
               // ->where('payment_status', 'Paid')
                ->where('property_booking_status', 'Confirmed')
                ->when($location_id, function ($query, $location_id) {
                    return $query->where('location_id', $location_id);
                })
                /* ->when($property_id, function ($query, $property_id) {
                    return $query->where('property_id', $property_id);
                }) */

                ->when($property_id && $pType, function ($query) use ($property_id, $pType) {
                        return $query->where('property_id', $property_id)
                                    ->where('pType', $pType);
                })


                ->when($user->role_id != 1, function ($query) use ($user) {
                        if (in_array($user->role_id, [3, 4, 5, 2])) {
                            return $query->where('property_bookings.parent_user_id', $user->parent_user_id);
                        } elseif ($user->role_id == 6) {
                            return $query->where('property_bookings.owner_id', $user->id);
                        } elseif ($user->role_id == 7) {
                            return $query->where('property_bookings.parent_user_id', $user->id);
                        } elseif ($user->role_id == 8) {
                            return $query->where('property_bookings.travelagent_id', $user->id);
                        }
                    })

                ->whereBetween('checkin_date', [$from_date, $to_date])
                ->select(DB::raw('AVG(DATEDIFF(checkin_date, created_at)) AS average_days_difference'))
                ->value('average_days_difference');
                
            $averageLeadTime = (int) round($averageLeadTime) ?? 0;   // In days
            $daystName = ($averageLeadTime == 1) ? 'Day' : 'Days';
            
            return response()->json([
                'status' => true,
                'data' =>[
                     0 => [
                        'title'=> 'Average Length of Stay',
                        'name'=> $nightName,
                        'value' => $averageLengthOfStay,
                    ],
                    1 => [
                        'title'=> 'Average Lead Time',
                        'name'=> $daystName,
                        'value' => $averageLeadTime 
                    ]    
                ],
                'message' => 'Successfully Retrive'
            ], 200);
            
            
        }catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => "Internal Error",
                'error' => $e->getMessage(),
            ], 500);
        }
    }
    
    

public function netRevenueDetails(Request $request)
{
    try {
        $user = Auth::guard('admin')->user();
        $unitsQuery = TblHomeUnit::select('id', 'ru_property_id', 'unit_name as property_name')
                ->whereNotNull('ru_property_id');
            $units = $this->propertyService->applyUserRoleFilter($unitsQuery)
                ->get() 
                ->map(function ($unit) {
                    $unit->pType = 'unit';  
                    return $unit;
                });

            $multiUnitsQuery = TblHomeMultiUnit::select('id', 'ru_property_id', 'unit_name as property_name')
                ->whereNotNull('ru_property_id');

            $multiUnits = $this->propertyService->applyUserRoleFilter($multiUnitsQuery)
                ->get()  
                ->map(function ($multiUnit) {
                    $multiUnit->pType = 'multiunit';  
                    return $multiUnit;
                });

            $properties = $units->concat($multiUnits);

        // 2. Locations
        $locations = TblLocation::select('id as location_id', 'location_name')
            ->where('status', 1)
            ->orderBy('location_name', 'asc')
            ->get();

        $channels = ['Airbnb', 'Booking.com', 'MakeMyTrip', 'Offline', 'RU', 'Website'];

        // 3. Filters
        $location_id = (int) $request->location_id;
        $ru_property_id = (int) $request->property_id;
        $channel = $request->channel;
        $days = $request->days;
        $pType = $request->pType;
        $date_range = $request->date_range;

        if ($date_range && str_contains($date_range, ' to ')) {
            [$from, $to] = explode(' to ', $date_range);

            try {
                $from_date = \Carbon\Carbon::createFromFormat('d/m/Y', trim($from))->format('Y-m-d');
                $to_date = \Carbon\Carbon::createFromFormat('d/m/Y', trim($to))->format('Y-m-d');
                $date_range = $from_date . ' to ' . $to_date;
            } catch (\Exception $e) {
                $date_range = null;
            }
        }




        
//dd($date_range);
        // 4. Date filters
        if ($days) {
            if ($days == '7_next') {
                $number = preg_replace('/_next$/', '', $days);
                $from_date = date('Y-m-d');
                $to_date = date('Y-m-d', strtotime("+{$number} days"));
            } else {
                $from_date = date('Y-m-d', strtotime("-{$days} days"));
                $to_date = date('Y-m-d', strtotime('-1 day'));
            }
        } elseif ($date_range) {
            [$from_date, $to_date] = explode(' to ', $date_range);
            $from_date = date('Y-m-d', strtotime($from_date));
            $to_date = date('Y-m-d', strtotime($to_date));
        } else {
            $from_date = null;
            $to_date = null;
        }

        // 5. Map ru_property_id to property_id
        if ($ru_property_id) {
            $tblHome = ($request->pType == 'unit')
                ? TblHomeUnit::select('id as property_id')->where('ru_property_id', $ru_property_id)->first()
                : TblHomeMultiUnit::select('id as property_id')->where('ru_property_id', $ru_property_id)->first();
            $property_id = (int) ($tblHome->property_id ?? 0);
        } else {
            $property_id = 0;
        }

        // 6. Bookings query
        $bookingsQuery = PropertyBooking::select(
        'property_id',
        'location_id',
        'channel',
        'checkin_date',
        DB::raw('ROUND(total_amount, 2) as revenue')
    )
    ->when($location_id, fn($q) => $q->where('location_id', $location_id))
    ->when($property_id && $pType, function ($q) use ($property_id, $pType) {
        return $q->where('property_id', $property_id)->where('pType', $pType);
    })

    ->when($user->role_id != 1, function ($q) use ($user) {
            if (in_array($user->role_id, [3, 4, 5, 2])) {
                return $q->where('property_bookings.parent_user_id', $user->parent_user_id);
            } elseif ($user->role_id == 6) {
                return $q->where('property_bookings.owner_id', $user->id);
            } elseif ($user->role_id == 7) {
                return $q->where('property_bookings.parent_user_id', $user->id);
            } elseif ($user->role_id == 8) {
                return $q->where('property_bookings.travelagent_id', $user->id);
            }
        })


    ->when($channel, fn($q) => $q->where('channel', $channel))
    ->when($from_date && $to_date, fn($q) => $q->whereBetween('checkin_date', [$from_date, $to_date]))
    //->where('payment_status', 'Paid')
    ->where('property_booking_status', 'Confirmed')
    ->orderBy('checkin_date', 'asc');


        // 7. Paginate for UI list
        $netRevenueDetails = $bookingsQuery->paginate(50);
//dd($netRevenueDetails);
        // 8. Clone for total calculation
        $bookingsForTotal = (clone $bookingsQuery)->get();
//dd($bookingsForTotal);
        // 9. Load name maps
        $unitNames = TblHomeUnit::pluck('unit_name', 'id')->toArray();
        $multiUnitNames = TblHomeMultiUnit::pluck('unit_name', 'id')->toArray();
        $locationNames = TblLocation::pluck('location_name', 'id')->toArray();

        // 10. Add property_name & location to each item in pagination
        $netRevenueDetails->getCollection()->transform(function ($item) use ($unitNames, $multiUnitNames, $locationNames) {
            $item->property_name = $unitNames[$item->property_id] ?? ($multiUnitNames[$item->property_id] ?? 'N/A');
            $item->location = $locationNames[$item->location_id] ?? 'N/A';
            return $item;
        });

        // 11. Total revenue grouping
        $groupedDataTotal = [];
        $grandTotalRevenue = 0;

        foreach ($bookingsForTotal as $item) {
            $propertyName = $unitNames[$item->property_id] ?? ($multiUnitNames[$item->property_id] ?? 'N/A');
            $locationName = $locationNames[$item->location_id] ?? 'N/A';

            $key = $propertyName . '|' . $locationName . '|' . $item->channel . '|' . $item->checkin_date;

            if (!isset($groupedDataTotal[$key])) {
                $groupedDataTotal[$key] = [
                    'property_name' => $propertyName,
                    'location' => $locationName,
                    'channel' => $item->channel,
                    'checkin_date' => $item->checkin_date,
                    'revenue' => $item->revenue,
                ];
            } else {
                $groupedDataTotal[$key]['revenue'] += $item->revenue;
            }
        }

        foreach ($groupedDataTotal as $data) {
            $grandTotalRevenue += $data['revenue'];
        }

        return view('pms.analytics.net_revenue', [
            'properties' => $properties,
            'locations' => $locations,
            'channels' => $channels,
            'dataList' => $netRevenueDetails,
            'netRevenue' => round($grandTotalRevenue, 2),
            'isListLoading' => false,
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'status' => false,
            'message' => 'Internal Error',
            'error' => $e->getMessage(),
        ], 500);
    }
}


 
public function averagePricePerNightView(Request $request)
{
    try {
        
        $user = Auth::guard('admin')->user();
        $property_id = $request->property_id;
        $pType = $request->pType;
        $days = $request->days;

        $unitsQuery = TblHomeUnit::select('id', 'ru_property_id', 'unit_name as property_name')
                ->whereNotNull('ru_property_id');
            $units = $this->propertyService->applyUserRoleFilter($unitsQuery)
                ->get() 
                ->map(function ($unit) {
                    $unit->pType = 'unit';  
                    return $unit;
                });

            $multiUnitsQuery = TblHomeMultiUnit::select('id', 'ru_property_id', 'unit_name as property_name')
                ->whereNotNull('ru_property_id');

            $multiUnits = $this->propertyService->applyUserRoleFilter($multiUnitsQuery)
                ->get()  
                ->map(function ($multiUnit) {
                    $multiUnit->pType = 'multiunit';  
                    return $multiUnit;
                });

            $properties = $units->concat($multiUnits);

        

        // Handle date range
        if ($days) {
            if ($days == '7_next') {
                $number = preg_replace('/_next$/', '', $days);
                $from_date = date('Y-m-d');
                $to_date = date('Y-m-d', strtotime("+{$number} days"));
            } else {
                $from_date = date('Y-m-d', strtotime("-{$days} days"));
                $to_date = date('Y-m-d', strtotime('-1 day'));
            }
        } else {
            $from_date = null;
            $to_date = null;
        }

        // Load property names
        $unitNames = TblHomeUnit::pluck('unit_name', 'id')->toArray();
        $multiUnitNames = TblHomeMultiUnit::pluck('unit_name', 'id')->toArray();

        // Base query
        $baseQuery = PropertyBooking::query()
        ->whereNull('deleted_at')
            //->where('payment_status', 'Paid')
            ->where('property_booking_status', 'Confirmed')
            ->when($property_id && $pType, function ($query) use ($property_id, $pType) {
                return $query->where('property_id', $property_id)
                             ->where('pType', $pType);
            })

            ->when($user->role_id != 1, function ($query) use ($user) {
                if (in_array($user->role_id, [3, 4, 5, 2])) {
                    return $query->where('property_bookings.parent_user_id', $user->parent_user_id);
                } elseif ($user->role_id == 6) {
                    return $query->where('property_bookings.owner_id', $user->id);
                } elseif ($user->role_id == 7) {
                    return $query->where('property_bookings.parent_user_id', $user->id);
                } elseif ($user->role_id == 8) {
                    return $query->where('property_bookings.travelagent_id', $user->id);
                }
            })

            ->when($from_date && $to_date, function ($query) use ($from_date, $to_date) {
                return $query->whereBetween('checkin_date', [$from_date, $to_date]);
            });

            

        // Clone for totals
        $totalQuery = clone $baseQuery;

        // === Paginated List (Group by property) ===
        $averagePricePerNightDetails = $baseQuery
            ->select(
                'property_id',
                'pType',
                DB::raw('COUNT(*) as count'),
                DB::raw('SUM(per_night_price) as total_price'),
                DB::raw('ROUND(AVG(per_night_price), 2) as average_price_per_night')
            )
            ->groupBy('property_id', 'pType')
            ->paginate(50);

        // === Total List (non-paginated, grouped) ===
        $averagePricePerNightTotalDetails = $totalQuery
            ->select(
                'property_id',
                'pType',
                DB::raw('COUNT(*) as count'),
                DB::raw('SUM(per_night_price) as total_price'),
                DB::raw('ROUND(AVG(per_night_price), 2) as average_price_per_night')
            )
            ->groupBy('property_id', 'pType')
            ->get();

        // === Total Average Calculation ===
        $totalAveragePrice = 0;
        $totalCountAveragePrice = 0;

        foreach ($averagePricePerNightTotalDetails as $item) {
            $totalAveragePrice += $item->average_price_per_night;
            $totalCountAveragePrice += $item->count;
        }

        $totalCountAveragePrice = round($totalCountAveragePrice, 2);
        $totalAveragePrice = round($totalAveragePrice, 2);

        // === Pagination Meta ===
        $pagination = [
            'total' => $averagePricePerNightDetails->total(),
            'per_page' => $averagePricePerNightDetails->perPage(),
            'current_page' => $averagePricePerNightDetails->currentPage(),
            'last_page' => $averagePricePerNightDetails->lastPage(),
        ];

        // === Attach Property Name ===
        $dataWithNames = $averagePricePerNightDetails->map(function ($item) use ($unitNames, $multiUnitNames) {
            $item->property_name = $item->pType === 'unit'
                ? ($unitNames[$item->property_id] ?? '')
                : ($multiUnitNames[$item->property_id] ?? '');
            return $item;
        });
//dd($totalAveragePrice);
        return view('pms.analytics.average_price_per_night', [
            'data' => $dataWithNames,
            'properties' => $properties,
            'pagination' => $averagePricePerNightDetails,
            'total_average_price' => $totalAveragePrice,
            'isListLoading' => false,
            'message' => 'Successfully Retrieved'
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'status' => false,
            'message' => "Internal Error",
            'error' => $e->getMessage(),
        ], 500);
    }
}

public function bookingsCreatedView(Request $request)
{
    try {
        $user = Auth::guard('admin')->user();
        $location_id = $request->location_id;
        $property_id = $request->property_id;
        $pType = $request->pType;
        $channel = $request->channel;
        $days = $request->days;

        
        $unitsQuery = TblHomeUnit::select('id', 'ru_property_id', 'unit_name as property_name')
                ->whereNotNull('ru_property_id');
            $units = $this->propertyService->applyUserRoleFilter($unitsQuery)
                ->get() 
                ->map(function ($unit) {
                    $unit->pType = 'unit';  
                    return $unit;
                });

            $multiUnitsQuery = TblHomeMultiUnit::select('id', 'ru_property_id', 'unit_name as property_name')
                ->whereNotNull('ru_property_id');

            $multiUnits = $this->propertyService->applyUserRoleFilter($multiUnitsQuery)
                ->get()  
                ->map(function ($multiUnit) {
                    $multiUnit->pType = 'multiunit';  
                    return $multiUnit;
                });

            $properties = $units->concat($multiUnits);

        $locations = TblLocation::select('id as location_id', 'location_name')
            ->where('status', 1)
            ->orderBy('location_name', 'asc')
            ->get();

        if ($days) {
            if ($days == '7_next') {
                $number = preg_replace('/_next$/', '', $days);
                $from_date = date('Y-m-d');
                $to_date = date('Y-m-d', strtotime("+{$number} days"));
            } else {
                $from_date = date('Y-m-d', strtotime("-{$days} days"));
                $to_date = date('Y-m-d', strtotime('-1 day'));
            }
        } else {
            $from_date = null;
            $to_date = null;
        }

        $unitNames = TblHomeUnit::pluck('unit_name', 'id')->toArray();
        $multiUnitNames = TblHomeMultiUnit::pluck('unit_name', 'id')->toArray();
        $locationNames = TblLocation::pluck('location_name', 'id')->toArray();

        $baseQuery = PropertyBooking::query()
            //->where('payment_status', 'Paid')
            ->where('property_booking_status', 'Confirmed')
            ->when($location_id, fn($q) => $q->where('location_id', $location_id))
            ->when($property_id && $pType, fn($q) => $q->where('property_id', $property_id)->where('pType', $pType))
            ->when($user->role_id != 1, function ($query) use ($user) {
                    if (in_array($user->role_id, [3, 4, 5, 2])) {
                        return $query->where('property_bookings.parent_user_id', $user->parent_user_id);
                    } elseif ($user->role_id == 6) {
                        return $query->where('property_bookings.owner_id', $user->id);
                    } elseif ($user->role_id == 7) {
                        return $query->where('property_bookings.parent_user_id', $user->id);
                    } elseif ($user->role_id == 8) {
                        return $query->where('property_bookings.travelagent_id', $user->id);
                    }
                })
            ->when($channel, fn($q) => $q->where('channel', $channel))
            ->when($from_date && $to_date, fn($q) => $q->whereBetween('checkin_date', [$from_date, $to_date]));

        $createdBookingsdetails = (clone $baseQuery)
            ->select(
                'property_id',
                'pType',
                'channel',
                'location_id',
                DB::raw('COUNT(*) as count'),
                DB::raw('ROUND(SUM(payable_amount), 2) as total_amount')
            )
            ->groupBy('property_id', 'pType', 'channel', 'location_id')
            ->paginate(50);

        $createdBookingsTotaldetails = (clone $baseQuery)
            ->select(
                'property_id',
                'pType',
                'channel',
                'location_id',
                DB::raw('COUNT(*) as count'),
                DB::raw('ROUND(SUM(payable_amount), 2) as total_amount')
            )
            ->groupBy('property_id', 'pType', 'channel', 'location_id')
            ->get();

        $grandTotal = $createdBookingsTotaldetails->sum('total_amount');

        $mappedData = $createdBookingsdetails->getCollection()->map(function ($item) use ($unitNames, $multiUnitNames, $locationNames) {
            $item->property_name = $item->pType === 'unit'
                ? ($unitNames[$item->property_id] ?? '')
                : ($multiUnitNames[$item->property_id] ?? '');

            $item->location_name = $locationNames[$item->location_id] ?? '';
            return $item;
        });

        $dataList = new LengthAwarePaginator(
            $mappedData,
            $createdBookingsdetails->total(),
            $createdBookingsdetails->perPage(),
            $createdBookingsdetails->currentPage(),
            ['path' => request()->url(), 'query' => request()->query()]
        );

        $pagination = [
            'total' => $createdBookingsdetails->total(),
            'per_page' => $createdBookingsdetails->perPage(),
            'current_page' => $createdBookingsdetails->currentPage(),
            'last_page' => $createdBookingsdetails->lastPage(),
        ];

        $channels = ['Airbnb', 'Booking.com', 'MakeMyTrip', 'Offline', 'RU', 'Website'];

        return view('pms.analytics.bookings_created', [
            'properties' => $properties,
            'locations' => $locations,
            'channels' => $channels,
            'dataList' => $dataList,
            'pagination' => $pagination,
            'grand_total' => round($grandTotal, 2),
            'isListLoading' => false,
            'message' => 'Successfully Retrieved'
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'status' => false,
            'message' => "Internal Error",
            'error' => $e->getMessage(),
        ], 500);
    }
}




  

// public function averagePricePerNightView(Request $request)
// {
//     return view('pms.analytics.average_price_per_night', $request->all());
// }

// public function bookingsCreatedView(Request $request)
// {
//     return view('pms.analytics.bookings_created', $request->all());
// }
}
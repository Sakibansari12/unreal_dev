<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;
use App\Models\PropertyBooking;
use App\Models\PropertyBookingPaymentRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Builder;
use App\Models\TblHomeUnit;
use App\Models\TblHomeMultiUnit;
use App\Models\RuPropertyPrice;
use App\Models\RuPropertyAvailability;
use App\Http\Controllers\MinStayController;
use App\Models\RuMinStay;
use App\helper\MasterHelper;
use App\helper\TblLocation;
use Razorpay\Api\Api;
use Config;
use Mail;
use DB;
use URL;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use App\Services\PriceLabsPayloadService;
use App\Services\PriceLabService;



class PropertyService
{
    
    protected $priceLabsPayloadService;
    protected $priceLabService;


    public function __construct() {
       
        $this->priceLabsPayloadService = new PriceLabsPayloadService();
        $this->priceLabService = new PriceLabService();
    }


    public function filterPropertyListByDates($requestParams = [])
    {
        $requestParams = (object)$requestParams;
        try {
            //----------------merge properties----------------//
            // $units = TblHomeUnit::whereNotNull('ru_property_id')->get();
            // $multiUnits = TblHomeMultiUnit::whereNotNull('ru_property_id')->get();
            // $properties =  $units->merge($multiUnits);


            $units = $this->applyUserRoleFilter(
                TblHomeUnit::whereNotNull('ru_property_id')->where('status', 1)->where('only_for_enquiry', 0)
            )->get();

            $multiUnits = $this->applyUserRoleFilter(
                TblHomeMultiUnit::whereNotNull('ru_property_id')->where('status', 1)->where('only_for_enquiry', 0)
            )->get();

            $properties = $units->merge($multiUnits);


            // $user = Auth::guard('admin')->user();

            // $properties = $properties->filter(function ($property) use ($user) {
            //     if (in_array($user->role_id, [3, 4, 5])) {
            //         return $property->parent_user_id == $user->parent_user_id;
            //     } elseif ($user->role_id == 6) {
            //         return $property->user_id == $user->id;
            //     } elseif ($user->role_id == 7) {
            //         return $property->parent_user_id == $user->id;
            //     }
            //     return true;
            // })->values(); 



            //--------------Website markup------------//

            $markup = setting()->website_markup ?? 0;
            //-------------------filter by checkin and checkout date-------------//
            if (!empty($requestParams->checkin_date) && !empty($requestParams->checkout_date)) {
                $checkInDate = date('Y-m-d', strtotime($requestParams->checkin_date));
                $checkOutDate = date('Y-m-d', strtotime($requestParams->checkout_date));

                // $checkInDate = $requestParams->checkin_date ? Carbon::createFromFormat('d/m/Y', $requestParams->checkin_date)->format('Y-m-d') : null;
                // $checkOutDate = $requestParams->checkout_date ? Carbon::createFromFormat('d/m/Y', $requestParams->checkout_date)->format('Y-m-d') : null;


                $last_date =  date('Y-m-d', strtotime($checkOutDate . '-1 days'));
                if ($last_date == $checkInDate) {
                    $date_difference_count = 1;
                } else {
                    $date_difference_count = MasterHelper::getDateDifference($checkInDate, $checkOutDate);
                }
                $date_difference_count = (int)$date_difference_count;

                //--------------filter property by minstay--------------------//
                // $filteredProperties = $properties->filter(function ($property) use ($checkInDate, $checkOutDate, $date_difference_count) {
                //     $minStayController = new MinStayController();
                //     $minStays = $minStayController->syncMinStayByRu(date('Y-m-d', strtotime($checkInDate)), $property->ru_property_id);
                //     // Reject property if its minStay is greater than date_difference_count
                //     return (int)$date_difference_count >= (int)$minStays;
                // });


                // $properties = $filteredProperties->values();

                //--------------filter property by availability--------------------//
                $filteredProperties = $properties->reject(function ($property) use ($checkInDate, $checkOutDate) {
                    return RuPropertyAvailability::where('ru_property_id', $property->ru_property_id)
                        ->whereBetween('availability_date', [$checkInDate, $checkOutDate])
                        ->where('is_available', 'no')
                        ->exists();
                });
                $properties = $filteredProperties->values();

                //--------------map price--------------------//
                $properties = $properties->map(function ($property) use ($checkInDate, $checkOutDate, $markup, $last_date, $date_difference_count) {
                    $price = RuPropertyPrice::where('ru_property_id', $property->ru_property_id)->whereBetween('price_date', [$checkInDate, $last_date])->sum('price');
                    $per_night_price = round($price / $date_difference_count);
                    $property->website_markup_price  = ($per_night_price * $markup) / 100;
                    $per_night_price = $per_night_price +  ($per_night_price * $markup) / 100;
                    $property->per_night_price = $per_night_price;
                    $property->no_of_nights = $date_difference_count;
                    $property->price = $per_night_price * $date_difference_count;
                    $property->date_from = $checkInDate;
                    $property->date_to = $checkOutDate;
                    return $property;
                })->values();
            }
            return $properties;
        } catch (Exception $e) {
            return $e;
        }
    }

    public static function applyUserRoleFilter(Builder $query): Builder
    {
        // 1 super admin, 2 admin, 3 finance, 4 front office, 
        //5 reservation, 6 owners, 7 property manager

        $user = Auth::guard('admin')->user();
        if ($user->role_id != 1) {
            if (in_array($user->role_id, [3, 4, 5, 2])) {
                $query->where('parent_user_id', $user->parent_user_id);
            } elseif ($user->role_id == 6) {
                $query->where('owner_id', $user->id);
            } elseif ($user->role_id == 7) {
                $query->where('parent_user_id', $user->id);
            }
        }

        return $query;



        // return $query;
    }

    public static function applyUserOwenerExRoleFilter(Builder $query): Builder
    {
        $user = Auth::guard('admin')->user();
        if ($user->role_id != 1) {
            if (in_array($user->role_id, [3, 4, 5, 2])) {
                $query->where('parent_user_id', $user->parent_user_id);
            } elseif ($user->role_id == 6) {
                $query->where('parent_user_id', $user->id);
            } elseif ($user->role_id == 7) {
                $query->where('parent_user_id', $user->id);
            }
        }
        return $query;
    }

    public function websitePropertyListByDates($requestParams = [])
    {
        $requestParams = (object)$requestParams;
        try {
            //----------------merge properties----------------//
            $units = TblHomeUnit::with(['imagesWebsite', 'tags', 'websiteAmenities', 'homeReviews', 'locationData'])->where('status', 1)->whereNotNull('ru_property_id')->get()->map(function ($unit) {
                $unit->pType = 'unit';
                return $unit;
            });
            $multiUnits = TblHomeMultiUnit::with(['imagesWebsite', 'tags', 'websiteAmenities', 'homeReviews', 'locationData'])->where('status', 1)->whereNotNull('ru_property_id')->get()->map(function ($multiUnit) {
                $multiUnit->pType = 'multiunit';
                return $multiUnit;
            });
            $properties =  $units->concat($multiUnits);
            //--------------Website markup------------//
            $markup = setting()->website_markup ?? 0;
            
            //-------------------filter by checkin and checkout date-------------//
            if (!empty($requestParams->checkin_date) && !empty($requestParams->checkout_date)) {
                $checkInDate = date('Y-m-d', strtotime($requestParams->checkin_date));
                $checkOutDate = date('Y-m-d', strtotime($requestParams->checkout_date));
                $last_date =  date('Y-m-d', strtotime($checkOutDate . '-1 days'));
                if ($last_date == $checkInDate) {
                    $date_difference_count = 1;
                } else {
                    $date_difference_count = MasterHelper::getDateDifference($checkInDate, $checkOutDate);
                }
                $date_difference_count = (int)$date_difference_count;

                //--------------filter property by minstay--------------------//
                $filteredProperties = $properties->filter(function ($property) use ($checkInDate, $checkOutDate, $date_difference_count) {
                    $minStayController = new MinStayController();
                    $minStays = $minStayController->syncMinStayByRu(date('Y-m-d', strtotime($checkInDate)), $property->ru_property_id);
                    // Reject property if its minStay is greater than date_difference_count
                    return (int)$date_difference_count >= (int)$minStays;
                });
                $properties = $filteredProperties->values();

                //--------------filter property by availability--------------------//
                 /* $filteredProperties = $properties->reject(function ($property) use ($checkInDate, $checkOutDate) {
                    return RuPropertyAvailability::where('ru_property_id', $property->ru_property_id)
                        ->whereBetween('availability_date', [$checkInDate, $checkOutDate])
                        ->where('is_available', 'no')
                        ->exists();
                });  */

                $filteredProperties = $properties->reject(function ($property) use ($checkInDate, $checkOutDate) {
                     $checkCheckout =  DB::table('ru_property_blocked')->where('ru_property_id', $property->ru_property_id)->where('date_from', $checkOutDate)->first();
                     if($checkCheckout){
                         $checkOutDate = date('Y-m-d', strtotime($checkOutDate.'-1 day'));
                     }
                     $checkCheckin =  DB::table('ru_property_blocked')->where('ru_property_id', $property->ru_property_id)->where('date_to', $checkInDate)->first();
                     if($checkCheckin){
                         $checkInDate = date('Y-m-d', strtotime($checkInDate.'+1 day'));
                     }
                    return RuPropertyAvailability::where('ru_property_id', $property->ru_property_id)
                        ->where("availability_date", ">=", $checkInDate)
                        ->where("availability_date", "<=", $checkOutDate)
                        ->where('is_available', 'no')
                        ->exists();
                }); 
               // $properties = $filteredProperties->values();

                $properties = $filteredProperties->values();

                //--------------map price--------------------//
                $properties = $properties->map(function ($property) use ($checkInDate, $checkOutDate, $markup, $last_date, $date_difference_count) {
                    $price = RuPropertyPrice::where('ru_property_id', $property->ru_property_id)->whereBetween('price_date', [$checkInDate, $last_date])->sum('price');
                    $per_night_price = round($price / $date_difference_count);
                    $per_night_price = $per_night_price +  ($per_night_price * $markup) / 100;
                    $property->per_night_price = $per_night_price;
                    $property->no_of_nights = $date_difference_count;
                    $property->price = $per_night_price * $date_difference_count;
                    $property->date_from = $checkInDate;
                    $property->date_to = $checkOutDate;

                    $reviews = $property->homeReviews;
                    $rating = 0;
                    if ($reviews && $reviews->count() > 0) {
                        $rating = round($reviews->avg('rating'), 1);
                    }
                    $property->rating = $rating;

                    return $property;
                })->values();
                
            }
            return $properties;
        } catch (Exception $e) {
            return $e;
        }
    }

    public function list($requestParams = [])
    {
        try {
            $units = TblHomeUnit::with(['imagesWebsite', 'tags', 'websiteAmenities', 'homeReviews', 'locationData'])->where('status', 1)->whereNotNull('ru_property_id')->get()->map(function ($unit) {
                $unit->pType = 'unit';
                return $unit;
            });
            $multiUnits = TblHomeMultiUnit::with(['imagesWebsite', 'tags', 'websiteAmenities', 'homeReviews', 'locationData'])->where('status', 1)->whereNotNull('ru_property_id')->get()->map(function ($multiUnit) {
                $multiUnit->pType = 'multiunit';
                return $multiUnit;
            });
            $properties =  $units->concat($multiUnits);

            $properties = $properties->shuffle();

            //dd($properties);
            $markup = setting()->website_markup ?? 0;
            $properties = $properties->map(function ($property) use ($markup) {
                $availabilityYesDates = RuPropertyAvailability::where('ru_property_id', $property->ru_property_id)->where('availability_date', '>=', date('Y-m-d'))->where('is_available', 'yes')->orderBy('availability_date')
                    ->pluck('availability_date')->toArray();

                $previouslyBookedCheckoutDates = PropertyBooking::where('property_id', $property->id)->where('property_booking_status', '!=', 'Canceled')->where('checkin_date', '>=', date('Y-m-d'))
                    ->where(function ($query) {
                        $query->where('channel', '!=', 'Website')
                            ->orWhere(function ($q) {
                                $q->where('channel', 'Website')
                                    ->where('property_booking_status', 'Confirmed');
                            });
                    })
                    ->pluck('checkout_date')->map(function ($date) {
                        return date('Y-m-d', strtotime($date));
                    })->toArray();
                $availabilities = array_values(
                    array_unique(
                        array_merge($availabilityYesDates, $previouslyBookedCheckoutDates)
                    )
                );
                sort($availabilities);
                if (!empty($availabilities)) {
                    $checkInDate = null;
                    $minStay = 2;
                    foreach ($availabilities as $availableDate) {
                        $minStayController = new MinStayController();
                        $minStayController->syncMinStay(date('Y-m-d', strtotime($availableDate)), $property->id, $property->pType);
                        $minStayDetail = RuMinStay::where([
                            'ru_property_id' => $property->ru_property_id,
                            'minstay_date'   => date('Y-m-d', strtotime($availableDate)),
                            'type' => $property->pType
                        ])->orderBy('id', 'desc')->first();

                        if ($minStayDetail) {
                            $minStay = (int) $minStayDetail->is_minstay_count;
                        }
                        $isValid = true;
                        for ($i = 0; $i < $minStay; $i++) {
                            $checkDate = date('Y-m-d', strtotime($availableDate . " +$i days"));
                            if (!in_array($checkDate, $availabilities)) {
                                $isValid = false;
                                break;
                            }
                        }
                        if ($isValid) {
                            $checkInDate = $availableDate;
                            break;
                        }
                    }
                    if ($checkInDate) {
                        $checkOutDate = date('Y-m-d', strtotime($checkInDate . " +$minStay days"));
                        $date_difference_count = $minStay;
                        $price = RuPropertyPrice::where('ru_property_id', $property->ru_property_id)
                            ->whereBetween('price_date', [
                                $checkInDate,
                                date('Y-m-d', strtotime($checkOutDate . '-1 days'))
                            ])->get()->unique('price_date')->sum('price');

                        $per_night_price = round($price / $date_difference_count);
                        $per_night_price = $per_night_price +  ($per_night_price * $markup) / 100;
                        $property->per_night_price = $per_night_price;
                        $property->no_of_nights = $date_difference_count;
                        $property->price = $per_night_price * $date_difference_count;
                        $property->date_from = $checkInDate;
                        $property->date_to = $checkOutDate;
                        $reviews = $property->homeReviews;
                        $rating = 0;
                        if ($reviews && $reviews->count() > 0) {
                            $rating = round($reviews->avg('rating'), 1);
                        }
                        $property->rating = $rating;
                        return $property;
                    }
                }

                return $property;
            })->values();
            return $properties;
        } catch (Exception $e) {
            return $e;
        }
    }

    public function getPropertyDetailBySlug(array $requestParams = [])
    {

        $requestParams = (object) $requestParams;

        try {
            // Merge properties
            $property = ($requestParams->pType === 'unit')
                ? TblHomeUnit::with(['images', 'locationData', 'homeReviews', 'ImportantInformation', 'home', 'websiteamenities'])->where('url_key', $requestParams->slug)->first()
                : TblHomeMultiUnit::with(['images', 'locationData', 'homeReviews', 'ImportantInformation', 'home', 'websiteamenities'])->where('url_key', $requestParams->slug)->first();

            // Website markup
            $markup = setting()->website_markup ?? 0;

            if (!$property) {
                return null; // Return null if property not found
            }

            // Filter by check-in and check-out date
            if (!empty($requestParams->checkin_date) && !empty($requestParams->checkout_date)) {
                $checkInDate = date('Y-m-d', strtotime($requestParams->checkin_date));
                $checkOutDate = date('Y-m-d', strtotime($requestParams->checkout_date));
                $lastDate = date('Y-m-d', strtotime($checkOutDate . ' -1 days'));

                $dateDifferenceCount = ($lastDate === $checkInDate)
                    ? 1
                    : MasterHelper::getDateDifference($checkInDate, $checkOutDate);

                $dateDifferenceCount = (int) $dateDifferenceCount;

                $property->per_night_price = round(
                    RuPropertyPrice::where('ru_property_id', $property->ru_property_id)
                        ->whereBetween('price_date', [$checkInDate, $lastDate])
                        ->get()->unique('price_date')
                        ->sum('price') / $dateDifferenceCount
                );

                $property->per_night_price += ($property->per_night_price * $markup) / 100;
                $property->no_of_nights = $dateDifferenceCount;
                $property->price = $property->per_night_price * $dateDifferenceCount;
                $property->date_from = $checkInDate;
                $property->date_to = $checkOutDate;
            } else {
                $availabilityYesDates = RuPropertyAvailability::where('ru_property_id', $property->ru_property_id)->where('availability_date', '>=', date('Y-m-d'))->where('is_available', 'yes')->orderBy('availability_date')
                    ->pluck('availability_date')->toArray();

                $previouslyBookedCheckoutDates = PropertyBooking::where('property_id', $property->id)->where('property_booking_status', '!=', 'Canceled')->where('checkin_date', '>=', date('Y-m-d'))
                    ->where(function ($query) {
                        $query->where('channel', '!=', 'Website')
                            ->orWhere(function ($q) {
                                $q->where('channel', 'Website')
                                    ->where('property_booking_status', 'Confirmed');
                            });
                    })
                    ->pluck('checkout_date')->map(function ($date) {
                        return date('Y-m-d', strtotime($date));
                    })->toArray();
                $availabilities = array_values(
                    array_unique(
                        array_merge($availabilityYesDates, $previouslyBookedCheckoutDates)
                    )
                );
                sort($availabilities);
                if (!empty($availabilities)) {
                    $checkInDate = null;
                    $minStay = 1;
                    foreach ($availabilities as $availableDate) {
                        // Sync minstay for this date
                        $minStayController = new MinStayController();
                        $minStayController->syncMinStay(
                            date('Y-m-d', strtotime($availableDate)),
                            $property->id,
                            $property->pType
                        );
                        $minStayDetail = RuMinStay::where([
                            'ru_property_id' => $property->ru_property_id,
                            'minstay_date'   => date('Y-m-d', strtotime($availableDate)),
                            'type'           => $property->pType
                        ])->orderBy('id', 'desc')->first();


                        if ($minStayDetail) {
                            $minStay = (int) $minStayDetail->is_minstay_count;
                        }

                        // 🔥 Check continuous availability for minstay


                        // $isValid = true;
                        // for ($i = 0; $i < $minStay; $i++) {
                        //     $checkDate = date('Y-m-d', strtotime($availableDate . " +$i days"));
                        //     if (!in_array($checkDate, $availabilities)) {
                        //         $isValid = false;
                        //         break;
                        //     }
                        // }

                        // 🔥 Check continuous availability for minstay (BLOCK LOGIC)
                            $isValid = true;
                            for ($i = 0; $i < $minStay; $i++) {
                                $checkDate = date('Y-m-d', strtotime($availableDate . " +$i days"));
                                $blocked = DB::table('ru_property_blocked')
                                    ->where('ru_property_id', $property->ru_property_id)
                                    ->where('date_from', '<=', $checkDate)
                                    ->where('date_to', '>', $checkDate) // ✅ checkout excluded
                                    ->exists();

                                if ($blocked) {
                                    $isValid = false;
                                    break;
                                }
                            }





                        // ✅ Valid check-in date found
                        if ($isValid) {
                            $checkInDate = $availableDate;
                            break;
                        }
                    }

                    // Agar valid check-in mila
                    if ($checkInDate) {

                        $checkOutDate = date('Y-m-d', strtotime($checkInDate . " +$minStay days"));
                        $date_difference_count = $minStay;

                        $price = RuPropertyPrice::where('ru_property_id', $property->ru_property_id)
                            ->whereBetween('price_date', [
                                $checkInDate,
                                date('Y-m-d', strtotime($checkOutDate . '-1 days'))
                            ])
                            ->get()
                            ->unique('price_date')
                            ->sum('price');

                        $property->per_night_price = round($price / $date_difference_count);
                        $property->per_night_price += ($property->per_night_price * $markup) / 100;

                        $property->no_of_nights = $date_difference_count;
                        $property->price       = $property->per_night_price * $date_difference_count;
                        $property->date_from   = $checkInDate;
                        $property->date_to     = $checkOutDate;
                    }
                }
            }
            return $property;
        } catch (Exception $e) {
            Log::error('Error fetching property details: ' . $e->getMessage());
            return response()->json(['error' => 'Something went wrong'], 500);
        }
    }

    public function updateAvaliabilityWithReservationHistorical($id, $date_from, $date_to, $close, $pType, $reason = null){
        try {
            // Get the property
            $property = ($pType == 'unit') ? TblHomeUnit::where('id', $id)->first() : TblHomeUnit::where('id', $id)->first();
            if (!$property) {
                return ['status' => false, 'message' => 'Property not found'];
            }
            
          
            // Update RuPropertyAvailability table
            RuPropertyAvailability::where('ru_property_id', $property->ru_property_id)->whereBetween('availability_date', [date('Y-m-d', strtotime($date_from)), date('Y-m-d', strtotime($date_to))])->where('type', $pType)->update(['is_available' => $close==0 ? 'no' : 'yes', 'reason' => $reason]);
            // Insert into ru_property_blocked table
            if($close == 0  ){
                DB::table('ru_property_blocked')->insert(['ru_property_id' => $property->ru_property_id, 'property_id' => $property->id, 'date_from' => date('Y-m-d', strtotime($date_from)), 'date_to' => date('Y-m-d', strtotime($date_to)), 'is_available' => $close==0 ? 'no' : 'yes', 'reason' => $reason, 'type' => $pType]);
            }
            else {
                DB::table('ru_property_blocked')->where('property_id', $property->id)->where('type', $pType)->whereBetween('date_from', [$date_from, $date_to])->delete();
            }
            
           
           

            //------push staah date block--//
            // $payload = $this->payloadService->blockDatesPayload($id, $pType, $date_from, $date_to, $close);
            // $response = $this->staahService->updateRatesAndAvailability($payload);
            //------push price lab date block--//
            $blocked_units = $close == 0? 1 : 0;
            $ru_property_id = $property->ru_property_id;
            //---------------------price lab-------------------//
            $payload = $this->priceLabsPayloadService->updateAvaliabilityWithReservation($ru_property_id, $date_from, $date_to);
            $response = $this->priceLabService->syncCalendars($payload);
            return ['status' => true,  'staahResponse' => $response ?? null];
        }
        catch (\Exception $e) {
            Log::error('Error in updateAvaliability: ' . $e->getMessage());
            return ['status' => false, 'message' => 'Error: ' . $e->getMessage()];
        }
    }

    public function updateAvaliabilityWithReservation($id, $date_from, $date_to, $close, $pType, $reason = null){
        try {
            // Get the property
            $property = ($pType == 'unit') ? TblHomeUnit::where('id', $id)->first() : TblHomeUnit::where('id', $id)->first();
            if (!$property) {
                return ['status' => false, 'message' => 'Property not found'];
            }
            
          
            // Update RuPropertyAvailability table
           // RuPropertyAvailability::where('ru_property_id', $property->ru_property_id)->whereBetween('availability_date', [date('Y-m-d', strtotime($date_from)), date('Y-m-d', strtotime($date_to))])->where('type', $pType)->update(['is_available' => $close==0 ? 'no' : 'yes', 'reason' => $reason]);
            // Insert into ru_property_blocked table
            
            
            /* if($close == 0  ){
                DB::table('ru_property_blocked')->insert(['ru_property_id' => $property->ru_property_id, 'property_id' => $property->id, 'date_from' => date('Y-m-d', strtotime($date_from)), 'date_to' => date('Y-m-d', strtotime($date_to)), 'is_available' => $close==0 ? 'no' : 'yes', 'reason' => $reason, 'type' => $pType]);
            }
            else {
                DB::table('ru_property_blocked')->where('property_id', $property->id)->where('type', $pType)->whereBetween('date_from', [$date_from, $date_to])->delete();
            } */
            
           
           

            //------push staah date block--//
            //$payload = $this->payloadService->blockDatesPayload($id, $pType, $date_from, $date_to, $close);
            //$response = $this->staahService->updateRatesAndAvailability($payload);
            //------push price lab date block--//
            $blocked_units = $close == 0? 1 : 0;
            $ru_property_id = $property->ru_property_id;
            //---------------------price lab-------------------//
            $payload = $this->priceLabsPayloadService->updateAvaliabilityWithReservation($ru_property_id, $date_from, $date_to);
            $response = $this->priceLabService->syncCalendars($payload);
            return ['status' => true,  'staahResponse' => $response ?? null];
        }
        catch (\Exception $e) {
            Log::error('Error in updateAvaliability: ' . $e->getMessage());
            return ['status' => false, 'message' => 'Error: ' . $e->getMessage()];
        }
    }

    public function updateAvaliabilityOnBookingCancelOrDeleteFromCalendar($id, $date_from, $date_to){
        try {
            // Get the property
            $pType = 'unit';
            $close = 1;
            $reason = 'Booking Cancellation'; 
            $property = ($pType == 'unit') ? TblHomeUnit::where('id', $id)->first() : TblHomeMultiUnit::where('id', $id)->first();
            
            
            $ru_property_id = $property->ru_property_id;
            //---------------------price lab-------------------//
            $payload = $this->priceLabsPayloadService->preparePriceLabsBlockUnblockOnBookingCancelOrDelete($ru_property_id, $date_from, $date_to);
        
            $response = $this->priceLabService->syncCalendars($payload);
            return ['status' => true,  'staahResponse' => $response ?? null];
        }
        catch (\Exception $e) {
            Log::error('Error in updateAvaliability: ' . $e->getMessage());
            return ['status' => false, 'message' => 'Error: ' . $e->getMessage()];
        }
    }

    public function updateAvaliabilityOnBookingCancelOrDelete($id, $date_from, $date_to){
        try {
            // Get the property
            
            $pType = 'unit';
            $close = 1;
            $reason = 'Booking Cancellation'; 
            $property = ($pType == 'unit') ? TblHomeUnit::where('id', $id)->first() : TblHomeMultiUnit::where('id', $id)->first();
            RuPropertyAvailability::where('ru_property_id', $property->ru_property_id)->whereBetween('availability_date', [date('Y-m-d', strtotime($date_from)), date('Y-m-d', strtotime($date_to))])->where('type', $pType)->update(['is_available' => $close==0 ? 'no' : 'yes', 'reason' => $reason]);
            //------push staah date block--//
            
            
            // $payload = $this->payloadService->blockDatesPayload($id, $pType, $date_from, $date_to, $close);
            // $response = $this->staahService->updateRatesAndAvailability($payload);

            //------push price lab date block--//
            
            $ru_property_id = $property->ru_property_id;
            //---------------------price lab-------------------//
            $payload = $this->priceLabsPayloadService->preparePriceLabsBlockUnblockOnBookingCancelOrDelete($ru_property_id, $date_from, $date_to);
        
            $response = $this->priceLabService->syncCalendars($payload);
            return ['status' => true,  'staahResponse' => $response ?? null];
        }
        catch (\Exception $e) {
            Log::error('Error in updateAvaliability: ' . $e->getMessage());
            return ['status' => false, 'message' => 'Error: ' . $e->getMessage()];
        }
    }


    public function updateAvaliability($id, $date_from, $date_to, $close, $pType, $reason = null){
        try {
            // Get the property
            $property = ($pType == 'unit') ? TblHomeUnit::where('id', $id)->first() : TblHomeMultiUnit::where('id', $id)->first();
            if (!$property) {
                return ['status' => false, 'message' => 'Property not found'];
            }
           
            $ru_property_id = $property->ru_property_id;
            //---------------------price lab-------------------//
            if(isPriceLabEnable() && $property->price_lab_sync_date_time){
                $payload = $this->priceLabsPayloadService->preparePriceLabsBlockUnblockPayload($ru_property_id, $date_from, $date_to, $close);
                $response = $this->priceLabService->syncCalendars($payload);
                if($close == 0){
                    $payload = $this->priceLabsPayloadService->preparePriceLabsSyncReservationOnDateBlock($ru_property_id, $date_from, $date_to);
                    $response = $this->priceLabService->syncReservations($payload);
                }
                else{
                    $payload = $this->priceLabsPayloadService->preparePriceLabsCancelReservationOnDateBlock($ru_property_id, $date_from, $date_to);
                    $response = $this->priceLabService->syncReservations($payload);
                }
            }    
            return ['status' => true,  'staahResponse' => $response ?? null];
        }
        catch (\Exception $e) {
            Log::error('Error in updateAvaliability: ' . $e->getMessage());
            return ['status' => false, 'message' => 'Error: ' . $e->getMessage()];
        }
    }
}

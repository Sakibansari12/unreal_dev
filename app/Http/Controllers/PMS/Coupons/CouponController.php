<?php

namespace App\Http\Controllers\PMS\Coupons;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use App\Models\Role;
use App\Models\DiscountCoupon;
use App\Models\TblHomeUnit;
use App\Models\TblHomeMultiUnit;
use App\Models\TblHomeType;
use App\Models\TblHome;
use App\Exports\CouponExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\DiscountCouponCodeMapping;
use DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class CouponController extends Controller
{
    public function index(Request $request){
        $query = DiscountCoupon::query()
            ->when($request->title, function($q) use ($request) {
                return $q->where('title', 'like', '%' . $request->title . '%');
            })
            ->when($request->status, function($q) use ($request) {
                return $q->where('status', 'like', '%' . $request->status . '%');
            })
            ->orderBy('id', 'desc');
    
        $items = $query->paginate(20)->withQueryString();
    
        return view('pms.coupon.list', compact('items'));
            
    }

    
    


     public function form($id = null)
    {
        $detail = $id ? DiscountCoupon::find($id) : null;
        $homeTypeData = TblHomeType::get();

        $userMappedPropertyIds = $detail ? ($detail->property_id ?? []) : [];
        $userMappedLocationIds = $detail ? ($detail->property_type_id ?? []) : [];

        $units = TblHomeUnit::whereNotNull('ru_property_id')
            
            ->where('only_for_enquiry', 0)
            ->select('id', 'unit_name')->get();
        $multiUnits = TblHomeMultiUnit::whereNotNull('ru_property_id')
            
            ->where('only_for_enquiry', 0)
            ->select('id', 'unit_name')->get();
        $allProperties = $units->merge($multiUnits)->values();

        return view('pms.coupon.form', compact('homeTypeData', 'allProperties', 'detail', 'userMappedPropertyIds', 'userMappedLocationIds'));
    }
    // public function getPropertiesByHomeType(Request $request)
    // {
    //     $homeTypeIds = $request->input('home_type_ids', []);
    //     $unitQuery = TblHomeUnit::query()
    //         ->whereNotNull('ru_property_id')
    //         ->select('id', 'unit_name', 'home_type_id');
    //     $multiUnitQuery = TblHomeMultiUnit::query()
    //         ->whereNotNull('ru_property_id')
    //         ->select('id', 'unit_name', 'home_type_id'); 
    //     if (!empty($homeTypeIds)) {
    //         $unitQuery->whereIn('home_type_id', $homeTypeIds);
    //         $multiUnitQuery->whereIn('home_type_id', $homeTypeIds);
    //     }
    //     $units = $unitQuery->get();
    //     $multiUnits = $multiUnitQuery->get();
    //     $properties = $units->merge($multiUnits)->values();
    //     return response()->json([
    //         'properties' => $properties,
    //     ]);
    // }
    
    public function getPropertiesByHomeType(Request $request)
    {
        $homeTypeIds = $request->input('home_type_ids', []);
        // $unitQuery = TblHomeUnit::query()
        //     ->whereNotNull('ru_property_id')
        //     ->select('id', 'unit_name', 'home_type_id');
        // $multiUnitQuery = TblHomeMultiUnit::query()
        //     ->whereNotNull('ru_property_id')
        //     ->select('id', 'unit_name', 'home_type_id'); 
            
        $unitQuery = DB::table('tbl_home_units')
        ->whereNull('deleted_at')
        ->where('only_for_enquiry', 0)
    ->whereNotNull('ru_property_id')
    ->select('id', 'unit_name', 'home_type_id');

$multiUnitQuery = DB::table('tbl_home_multi_units')
->whereNull('deleted_at')
    ->where('only_for_enquiry', 0)
    ->whereNotNull('ru_property_id')
    ->select('id', 'unit_name', 'home_type_id');    
            
            
            
        if (!empty($homeTypeIds)) {
            $unitQuery->whereIn('home_type_id', $homeTypeIds);
            $multiUnitQuery->whereIn('home_type_id', $homeTypeIds);
        }
        $units = $unitQuery->get();
        $multiUnits = $multiUnitQuery->get();
        $properties = $units->merge($multiUnits)->values();
        return response()->json([
            'properties' => $properties,
        ]);
    }

//   public function save(Request $request)
//     {
//         $rules = [
//             'title' => 'required',
//             'start_date' => 'required|date',
//             'end_date' => 'required|date|after_or_equal:start_date',
//             'stay_date_from' => 'required',
//             'stay_date_to' => 'required|date|after_or_equal:stay_date_from',
//             'user_type' => 'required|in:single,multiple',
//             'discount_type' => 'required|in:percentage,flat',
//             'generated_coupon_code_by' => 'required|in:self,auto',
//         ];

//         if ($request->generated_coupon_code_by == 'self') {
//             $rules['self_code'] = 'required|string|max:100';
//         }

//         if ($request->generated_coupon_code_by == 'auto') {
//             $rules['prefix'] = 'required|string|max:100';
//             $rules['no_of_codes'] = 'required|integer|min:1';
//         }

//         if ($request->user_type == 'multiple') {
//             $rules['use_limit'] = 'required|integer|min:1';
//         }

//         if ($request->discount_type == 'percentage') {
//             $rules['discount_percentage'] = 'required|numeric|min:0|max:100';
//         }

//         if ($request->discount_type == 'flat') {
//             $rules['discount_amount'] = 'required|numeric|min:0';
//         }

//         $validated = $request->validate($rules);

//         $property_type_id = json_encode($request->mappedLocations ?? []);
//         $property_id = json_encode($request->mappedProperties ?? []);

//         $detail = DiscountCoupon::firstOrNew(['id' => $request->id]);
//         $detail->coupon_code = $request->generated_coupon_code_by == 'self' ? $request->self_code : 'ST00' . rand(10000, 99999);
//         $detail->title = $request->title;
//         $detail->start_date = $request->start_date;
//         $detail->end_date = $request->end_date;
//         $detail->user_type = $request->user_type;
//         $detail->use_limit = $request->user_type == 'multiple' ? $request->use_limit : null;
//         $detail->discount_type = $request->discount_type;
//         $detail->discount = $request->discount_type == 'flat' ? $request->discount_amount : $request->discount_percentage;
//         $detail->generated_coupon_code_by = $request->generated_coupon_code_by;
//         $detail->coupon_valid_on_min_no_of_nights = $request->coupon_valid_on_min_no_of_nights;
//         $detail->coupon_valid_on_min_total_booking_amount = $request->coupon_valid_on_min_total_booking_amount;
//         $detail->stay_date_from = $request->stay_date_from;
//         $detail->stay_date_to = $request->stay_date_to;
//         $detail->property_type_id = $property_type_id;
//         $detail->property_id = $property_id;
//         $detail->term_and_conditions = $request->term_and_conditions ?? '';
//         $detail->prefix = $request->prefix ?? '';
//         // $detail->no_of_codes = $request->no_of_codes ?? null;
//         $detail->code = $request->self_code ?? null;
//         $detail->save();

//         // Handle coupon code mappings
//         DiscountCouponCodeMapping::where('discount_coupon_id', $detail->id)->forceDelete();

//         if ($request->generated_coupon_code_by == 'auto') {
//             for ($i = 1; $i <= (int)$request->no_of_codes; $i++) {
//                 $code = $request->prefix . rand(10000, 99999);
//                 DiscountCouponCodeMapping::create([
//                     'code' => $code,
//                     'discount_coupon_id' => $detail->id,
//                 ]);
//             }
//         } else {
//             DiscountCouponCodeMapping::create([
//                 'code' => $request->self_code,
//                 'discount_coupon_id' => $detail->id,
//             ]);
//         }

//         return redirect()->route('pms.coupon.list')
//             ->with('success', $request->id ? 'Coupon updated successfully.' : 'Coupon saved successfully.');
//     }


    public function save(Request $request)
    {
        $rules = [
            'title' => 'required',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'stay_date_from' => 'required',
            'stay_date_to' => 'required|date|after_or_equal:stay_date_from',
            'user_type' => 'required|in:single,multiple',
            'discount_type' => 'required|in:percentage,flat',
            'generated_coupon_code_by' => 'required|in:self,auto',
            'mappedProperties' => 'required|array|min:1',
        ];

        if ($request->generated_coupon_code_by == 'self') {
            $rules['self_code'] = 'required|string|max:100';
        }

        if ($request->generated_coupon_code_by == 'auto') {
            $rules['prefix'] = 'required|string|max:100';
            $rules['no_of_codes'] = 'required|integer|min:1';
        }

        if ($request->user_type == 'multiple') {
            $rules['use_limit'] = 'required|integer|min:1';
        }

        if ($request->discount_type == 'percentage') {
            $rules['discount_percentage'] = 'required|numeric|min:0|max:100';
        }

        if ($request->discount_type == 'flat') {
            $rules['discount_amount'] = 'required|numeric|min:0';
        }

        $validated = $request->validate($rules);

        $property_type_id = json_encode($request->mappedLocations ?? []);
        $property_id = json_encode($request->mappedProperties ?? []);

        $detail = DiscountCoupon::firstOrNew(['id' => $request->id]);
        $detail->coupon_code = $request->generated_coupon_code_by == 'self' ? $request->self_code : 'KIWI00' . rand(10000, 99999);
        $detail->title = $request->title;
        $detail->start_date = $request->start_date;
        $detail->end_date = $request->end_date;
        $detail->user_type = $request->user_type;
        $detail->use_limit = $request->use_limit;
        //$detail->use_limit = $request->user_type == 'multiple' ? $request->use_limit : null;
        $detail->discount_type = $request->discount_type;
        $detail->discount = $request->discount_type == 'flat' ? $request->discount_amount : $request->discount_percentage;
        $detail->generated_coupon_code_by = $request->generated_coupon_code_by;
        $detail->coupon_valid_on_min_no_of_nights = $request->coupon_valid_on_min_no_of_nights;
        $detail->coupon_valid_on_min_total_booking_amount = $request->coupon_valid_on_min_total_booking_amount;
        $detail->stay_date_from = $request->stay_date_from;
        $detail->stay_date_to = $request->stay_date_to;
        $detail->property_type_id = $property_type_id;
        $detail->property_id = $property_id;
        $detail->term_and_conditions = $request->term_and_conditions ?? '';
        $detail->prefix = $request->prefix ?? '';
        // $detail->no_of_codes = $request->no_of_codes ?? null;
        //$detail->code = $request->self_code ?? null;
        
        if($request->generated_coupon_code_by == 'auto'){
            $detail->prefix = $request->prefix;
            $detail->code = $request->no_of_codes;
        }
        if($request->generated_coupon_code_by == 'self'){
            $detail->prefix = '';
            $detail->code = $request->self_code;
        }
        
        $detail->save();
        
        if ($request->generated_coupon_code_by =='auto') {
            $existingCodes = DiscountCouponCodeMapping::where('discount_coupon_id', $detail->id)
                                ->orderBy('id', 'asc')
                                ->get();
            $existingCount = $existingCodes->count();
            $newCount = (int)$request->no_of_codes;
        
            $prefixChanged = false;
            if ($existingCount > 0) {
                $firstCode = $existingCodes->first()->code;
                $oldPrefix = substr($firstCode, 0, strlen($request->prefix));
                if ($oldPrefix !== $request->prefix) {
                    $prefixChanged = true;
                }
            }
        
            // CASE 1: Prefix changed → delete all and regenerate
            if ($prefixChanged) {
                DiscountCouponCodeMapping::where('discount_coupon_id', $detail->id)->delete();
                for ($i = 1; $i <= $newCount; $i++) {
                    $code = $request->prefix . rand(10000, 99999);
                    DiscountCouponCodeMapping::create([
                        'code' => $code,
                        'is_birthday_coupon' => 1,
                        'discount_coupon_id' => $detail->id
                    ]);
                }
            } 
            // CASE 2: Prefix same & newCount > oldCount → add extra codes
            elseif ($newCount > $existingCount) {
                $extra = $newCount - $existingCount;
                for ($i = 1; $i <= $extra; $i++) {
                    $code = $request->prefix . rand(10000, 99999);
                    DiscountCouponCodeMapping::create([
                        'code' => $code,
                        'is_birthday_coupon' => 1,
                        'discount_coupon_id' => $detail->id
                    ]);
                }
            } 
            // CASE 3: Prefix same & newCount < oldCount → remove extra codes
            elseif ($newCount < $existingCount) {
                $deleteIds = $existingCodes->skip($newCount)->pluck('id');
                DiscountCouponCodeMapping::whereIn('id', $deleteIds)->delete();
            }
            // CASE same count & same prefix → kuch nahi karna
        }
        else {
            // manual coupon case
            DiscountCouponCodeMapping::where('discount_coupon_id', $detail->id)->delete();
            DiscountCouponCodeMapping::create([
                'code' => $request->self_code,
                'discount_coupon_id' => $detail->id
            ]);
        }

        return redirect()->route('pms.coupon.list')
            ->with('success', $request->id ? 'Coupon updated successfully.' : 'Coupon saved successfully.');
    }
    
    public function couponCodeExport(Request $request) {

        return Excel::download(new CouponExport($request->all()), 'coupon_code.xlsx');

    }


    public function toggleStatus($id)
    {
        try {
            $detail = DiscountCoupon::where(['id' => $id])->first();
            DiscountCoupon::where(['id' => $id])->update(['status' => !$detail->status]);
            return response()->json([
                'status' => true,
                'data' => '',
                'message' => 'Status updated successfully'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => "Internal Error",
                'error' => $e->getMessage(),
            ], 500);
        }
    }
    public function delete($id)
    {
        try {
            $user = DiscountCoupon::findOrFail($id);
            $user->delete();

            return response()->json([
                'status' => true,
                'data' => '',
                'message' => 'Coupon deleted successfully'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Internal Error',
                'error' => $e->getMessage(),
            ], 500);
        }
    }


    public function multiDelete(Request $request)
    {
        $ids = $request->input('ids');
        if (empty($ids) || !is_array($ids)) {
            return response()->json([
                'status' => false,
                'message' => 'No coupon selected for deletion.'
            ], 400);
        }
        try {

            DiscountCoupon::whereIn('id', $ids)->delete();
            return response()->json([
                'status' => true,
                'message' => 'Coupon(s) deleted successfully.'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Internal Error',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
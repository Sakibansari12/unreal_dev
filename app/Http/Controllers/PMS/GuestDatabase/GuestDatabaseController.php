<?php

namespace App\Http\Controllers\PMS\GuestDatabase;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\BookingGuestId;
use App\Exports\GuestDatabaseExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Auth;
use DB;

class GuestDatabaseController extends Controller{
    
    /* public function index(Request $request){
        try {
            DB::statement("SET SESSION sql_mode=(SELECT REPLACE(@@sql_mode, 'ONLY_FULL_GROUP_BY', ''))");
            $user = Auth::guard('admin')->user();
         
            $filteredSubQuery = BookingGuestId::query();
    
            $filteredSubQuery->when(!empty($request->search_name), function ($q) use ($request) {
                return $q->where('name', 'like', '%' . $request->search_name . '%');
            });
    
            $filteredSubQuery->when(!empty($request->search_email), function ($q) use ($request) {
                return $q->where('email', 'like', '%' . $request->search_email . '%');
            });
    
            $filteredSubQuery->when(!empty($request->search_mobile), function ($q) use ($request) {
                return $q->where('mobile_no', 'like', '%' . $request->search_mobile . '%');
            });
            
             // 🔹 Property Name Filter
            $filteredSubQuery->when(!empty($request->property_name), function ($q) use ($request) {
                $q->whereHas('propertyBooking.homeUnit', function ($q2) use ($request) {
                    $q2->where('unit_name', 'like', '%' . $request->property_name . '%');
                })
                ->orWhereHas('propertyBooking.homeMultiUnit', function ($q2) use ($request) {
                    $q2->where('unit_name', 'like', '%' . $request->property_name . '%');
                });
            });
            
            
            $filteredSubQuery->when($user->role_id != 1, function ($query) use ($user) {
                return $query->where('user_id', $user->id);
            });
            $ids = $filteredSubQuery->selectRaw('MIN(id) as id')->groupBy('property_booking_id');
           $items = BookingGuestId::with([
                        'propertyBooking' => function ($q) {
                            $q->whereNull('deleted_at');
                        },
                        'propertyBooking.homeUnit.locationData',   
                        'propertyBooking.homeMultiUnit.locationData'
                    ])
                    ->whereIn('id', $ids)
                    ->whereHas('propertyBooking', function ($q) {
                        $q->whereNull('deleted_at');
                    })
                    ->orderByDesc('id')->groupBy('mobile_no')->paginate(50)->withQueryString();
                    return view('pms.guest-database.list', compact('items'));
    
        }
        catch (\Exception $e) {
            return back()->withInput()->with('error', 'Something went wrong: ' . $e->getMessage());
        }
    } */

    public function index(Request $request)
{
    try {
        DB::statement("SET SESSION sql_mode=(SELECT REPLACE(@@sql_mode, 'ONLY_FULL_GROUP_BY', ''))");

        $user = Auth::guard('admin')->user();

        /*
        |--------------------------------------------------------------------------
        | Step 1: Decide ONE record per mobile_no (jo display hoga)
        |--------------------------------------------------------------------------
        | Yahan hum har mobile_no ka MIN(id) le rahe hain
        */
        $baseIds = BookingGuestId::selectRaw('MIN(id) as id')
            ->groupBy('mobile_no');

        /*
        |--------------------------------------------------------------------------
        | Step 2: Main Query – sirf wahi records jinke IDs upar decide hue
        |--------------------------------------------------------------------------
        */
        $items = BookingGuestId::with([
                'propertyBooking' => function ($q) {
                    $q->whereNull('deleted_at');
                },
                'propertyBooking.homeUnit.locationData',
                'propertyBooking.homeMultiUnit.locationData',
            ])
            ->whereIn('id', $baseIds)

            /*
            |--------------------------------------------------------------------------
            | Step 3: Filters – ab sirf DISPLAYED DATA pe lagenge
            |--------------------------------------------------------------------------
            */
            ->when(!empty($request->search_name), function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search_name . '%');
            })

            ->when(!empty($request->search_email), function ($q) use ($request) {
                $q->where('email', 'like', '%' . $request->search_email . '%');
            })

            ->when(!empty($request->search_mobile), function ($q) use ($request) {
                $q->where('mobile_no', 'like', '%' . $request->search_mobile . '%');
            })

            // 🔹 Property Name Filter
            ->when(!empty($request->property_name), function ($q) use ($request) {
                $q->where(function ($qq) use ($request) {
                    $qq->whereHas('propertyBooking.homeUnit', function ($q2) use ($request) {
                        $q2->where('unit_name', 'like', '%' . $request->property_name . '%');
                    })
                    ->orWhereHas('propertyBooking.homeMultiUnit', function ($q2) use ($request) {
                        $q2->where('unit_name', 'like', '%' . $request->property_name . '%');
                    });
                });
            })

            /*
            |--------------------------------------------------------------------------
            | Step 4: Role-based restriction
            |--------------------------------------------------------------------------
            */
            ->when($user->role_id != 1, function ($q) use ($user) {
                $q->where('user_id', $user->id);
            })

            ->orderByDesc('id')
            ->paginate(50)
            ->withQueryString();

        return view('pms.guest-database.list', compact('items'));

    } catch (\Exception $e) {
        return back()
            ->withInput()
            ->with('error', 'Something went wrong: ' . $e->getMessage());
    }
}



    public function exportToExcel(Request $request)
    {
        $data = [
            'search_name'          => $request->input('search_name'),
            'search_email'         => $request->input('search_email'),
            'search_property_name' => $request->input('property_name'),
            'search_mobile'        => $request->input('search_mobile'),
        ];

        return Excel::download(new GuestDatabaseExport($data), 'guest-database.xlsx');
    }

}
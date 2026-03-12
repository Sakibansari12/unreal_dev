<?php

namespace App\Http\Controllers\PMS\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TblCollection;
use App\Models\TblHomeUnit;
use App\Models\TblHomeMultiUnit;
use App\Models\PropertyBooking;
use App\Models\TblOwnerExpenses;
use App\Models\TblHome;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use App\Services\PropertyService;
use Illuminate\Pagination\LengthAwarePaginator;
use App\helper\MasterHelper;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\OwnerExpensesExport;
use App\Exports\OwnerRevenueReportExport;
use Carbon\Carbon;
class OwnerExpensesController extends Controller
{   

    protected $propertyService;

    public function __construct()
    {
        $this->propertyService = new PropertyService();
    }

    
 public function index(Request $request)
{



        $units = $this->propertyService->applyUserRoleFilter(
            TblHomeUnit::whereNotNull('ru_property_id')
        )->get();

        $multiUnits = $this->propertyService->applyUserRoleFilter(
            TblHomeMultiUnit::whereNotNull('ru_property_id')
        )->get();

        $properties = $units->merge($multiUnits);
        $start_date = null;
        $end_date = null;
        // Date Range Filter
        if ($request->filled('searchDateRange') && str_contains($request->searchDateRange, ' to ')) {
            [$from, $to] = explode(' to ', $request->searchDateRange);
            try {
                $start_date = \Carbon\Carbon::createFromFormat('d/m/Y', trim($from))->format('Y-m-d');
                $end_date = \Carbon\Carbon::createFromFormat('d/m/Y', trim($to))->format('Y-m-d');
            } catch (\Exception $e) {
                $start_date = null;
                $end_date = null;
            }
        }

    // Base Query
    //$query = TblOwnerExpenses::query();
     $query = $this->propertyService->applyUserRoleFilter(TblOwnerExpenses::query());
    // Expense Name Filter
    if ($request->filled('search')) {
        $query->where('expenses_name', 'like', '%' . $request->search . '%');
    }

    // Date Filter
    if ($start_date && $end_date) {
        $query->whereBetween('date', [$start_date, $end_date]);
    }

    // Unit Filter
    if ($request->filled('searchUnitId')) {
        $query->where('pType', $request->pType)->where('property_id', $request->searchUnitId);
    }


    // Fetch initial results
    $items = $query->orderBy('id', 'desc')->get();

    $items->transform(function ($item) {
        if ($item->pType === 'unit') {
            $item->unit_property = \App\Models\TblHomeUnit::with('ownerData')->find($item->property_id);
        } elseif ($item->pType === 'multiunit') {
            $item->unit_property = \App\Models\TblHomeMultiUnit::with('ownerData')->find($item->property_id);
        } else {
            $item->unit_property = null;
        }
        return $item;
    });

    if ($request->filled('owner_name')) {
        $items = $items->filter(function ($item) use ($request) {
            if (!$item->unit_property || !$item->unit_property->ownerData) {
                return false;
            }

            return stripos($item->unit_property->ownerData->name, $request->owner_name) !== false;
        })->values(); 
    }

    $perPage = 50;
    $currentPage = LengthAwarePaginator::resolveCurrentPage();
    $currentItems = $items->slice(($currentPage - 1) * $perPage, $perPage)->values();
    $paginatedItems = new \Illuminate\Pagination\LengthAwarePaginator(
        $currentItems,
        $items->count(),
        $perPage,
        $currentPage,
        ['path' => request()->url(), 'query' => request()->query()]
    );

    return view('pms.master.owner-expenses.list', [
        'items' => $paginatedItems,
        'properties'=> $properties
    ]);
}

public function ownerExpensesExport(Request $request) {
        return Excel::download(new OwnerExpensesExport($request->all()), 'owner-expenses.xlsx');
}



    public function form($id=null){

      $query = $this->propertyService->applyUserOwenerExRoleFilter(TblHome::query());   

      $propertyUnitList = $query->with(['user.parent'])->orderBy('id', 'desc')->get();
        $propertyUnitList->map(function ($home) {
            $user = $home->user;
            if ($user) {
                if ($user->role_id == 7) {
                    $home->property_manager_name = $user->name;
                } elseif ($user->parent && $user->parent->role_id == 7) {
                    $home->property_manager_name = $user->parent->role;
                } else {
                    $home->property_manager_name = '';
                }
            } else {
                $home->property_manager_name = '';
            }
            return $home;
        });
        $detail = TblOwnerExpenses::where('id',$id)->first();

       

   $propertyList = collect();
    if ($detail && $detail->home_id) {
        $query = $this->propertyService->applyUserRoleFilter(TblHome::query());
        $property = $query->where('id', $detail->home_id)
            ->with([
                'units:id,home_id,unit_name',
                'multiUnits:id,home_id,unit_name'
            ])
            ->first();

        if ($property) {
            if ($property->units) {
                foreach ($property->units as $unit) {
                    $propertyList->push([
                        'id'   => $unit->id,
                        'name' => $unit->unit_name,
                        'type' => $unit->pType,
                    ]);
                }
            }
            if ($property->multiUnits) {
                foreach ($property->multiUnits as $multi) {
                    $propertyList->push([
                        'id'   => $multi->id,
                        'name' => $multi->unit_name,
                        'type' => $multi->pType,
                    ]);
                }
            }
        }
    }
        $owner_expenses_name = TblOwnerExpenses::pluck('expenses_name')->unique()->values();

        return view('pms.master.owner-expenses.form',compact('detail', 'propertyList','owner_expenses_name','propertyUnitList'));
    }

    public function save(Request $request){
       // dd($request);
        $rules = [
            'expenses_name' => 'required',
            'unit' => 'required',
            'property' => 'required',
            'expenses_date' => 'required',
            'expenses_amount' => 'required|numeric',
        ];

        if ($request->has('id') && $request->id) {
            if ($request->hasFile('image') || $request->input('remove_image') == 1) {
                //$rules['image'] = 'required';
                $rules['image'] = 'nullable|mimes:jpg,jpeg,webp,svg,png,pdf|max:3072';
            }
        } else {
          //  $rules['image'] = 'required';
          $rules['image'] = 'nullable|mimes:jpg,jpeg,webp,svg,png,pdf|max:3072';
        }

        $request->validate($rules);
        $user = Auth::guard('admin')->user();
        $data = [
            'expenses_name' => $request->input('expenses_name'),
            'home_id' => $request->input('unit'),
            'property_id' => $request->input('property'),
            'amount' => $request->input('expenses_amount'),
            'date' => $request->input('expenses_date'),
            'pType' => $request->input('type'),
            'status' => 1,
            'user_id'=> $user->id,
            'parent_user_id'=> ($user->role_id == 7) ? $user->id : $user->parent_user_id,
        ];

        if ($request->hasFile('image')) {
            $filePath = $request->file('image')->store('expenses', 'public');
            $data['file'] = basename($filePath);
        }

        

        if ($request->has('id') && $request->id) {
            TblOwnerExpenses::where('id', $request->id)->update($data);
        } else {
            $maxId = TblOwnerExpenses::max('id');
            $data['id'] = $maxId ? $maxId + 1 : 1;
    
            DB::table('tbl_owner_expenses')->insert($data);
        }
        return redirect()->route('pms.owner-expenses.list')->with('success', $request->id ? 'Owner Expenses updated successfully!' : 'Collection added successfully!');
    }

    public function toggleStatus($id){   
        try {
            $detail = TblOwnerExpenses::where(['id'=>$id])->first();
            TblOwnerExpenses::where(['id'=>$id])->update(['status'=>!$detail->status]);
            return response()->json([
                'status' => true,
                'data' => '',
                'message' => 'Status updated successfully'
            ], 200);
        }
        catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => "Internal Error",
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function delete($id){   
        try {
            TblOwnerExpenses::where('id', $id)->delete();
            return response()->json([
                'status' => true,
                'data' => '',
                'message' => 'OwnerExpenses deleted successfully'
            ], 200);
        }
        catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => "Internal Error",
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
                'message' => 'No OwnerExpenses selected for deletion.'
            ], 400);
        }

        try {
            TblOwnerExpenses::whereIn('id', $ids)->delete();
            return response()->json([
                'status' => true,
                'message' => 'OwnerExpenses(s) deleted successfully.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Internal Error',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function savePosition(Request $request)
    {
        try {
            // Extract positions array from the request
            $positions = $request->position;

            // Check if $positions is an array
            if (!is_array($positions)) {
                throw new \Exception('$positions must be an array.');
            }

            // Loop through the positions array
            foreach ($positions as $index => $id) {
                // Update the position value for the record with the given id
                TblOwnerExpenses::where('id', $id)->update(['position' => $index]);
            }

            // Return success response
            return response()->json([
                'status' => true,
                'message' => 'Successfully Updated.'
            ], 200);

        } catch (\Exception $e) {
            // Return error response if an exception occurs
            return response()->json([
                'status' => false,
                'message' => 'Internal Error',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    public function ShowHomecollectionStatus($id){   
        try {
            $detail = TblOwnerExpenses::where(['id'=>$id])->first();
            TblOwnerExpenses::where(['id'=>$id])->update(['show_on_collection_page'=>!$detail->show_on_collection_page]);
            return response()->json([
                'status' => true,
                'data' => '',
                'message' => 'Show on home page updated successfully'
            ], 200);
        }
        catch (\Exception $e) {

            return response()->json([
                'status' => false,
                'message' => "Internal Error",
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ], 500);
        }
    }


   public function getPropertiesByUnit($unitId)
{
    $query = $this->propertyService->applyUserRoleFilter(TblHome::query());

    $property = $query->where('id', $unitId)
        ->with([
            'units:id,home_id,unit_name',
            'multiUnits:id,home_id,unit_name'
        ])
        ->first();

    if (!$property) {
        return response()->json([]);
    }

    // Merge units + multiUnits
    $merged = collect();

    if ($property->units) {
        foreach ($property->units as $unit) {
            $merged->push([
                'id'   => $unit->id,
                'name' => $unit->unit_name,
                'type' => 'unit'
            ]);
        }
    }

    if ($property->multiUnits) {
        foreach ($property->multiUnits as $multi) {
            $merged->push([
                'id'   => $multi->id,
                'name' => $multi->unit_name,
                'type' => 'multiunit'
            ]);
        }
    }

    return response()->json($merged);
}

    // public function ownerRevenueList(Request $request)
    //     {
            
    //     $filterPType = $request->pType;
    //     $filterPropertyId = $request->searchUnitId;

    //         $months = collect(range(0, 5))->map(function ($i) {
    //             return Carbon::now()->subMonths($i)->format('Y-m');
    //         })->reverse()->values();

    //         $groupedData = [];

    //         foreach ($months as $month) {
    //             $start = Carbon::createFromFormat('Y-m', $month)->startOfMonth();
    //             $end = Carbon::createFromFormat('Y-m', $month)->endOfMonth();
    //             $monthName = Carbon::createFromFormat('Y-m', $month)->format('F');

    //             // 🔹 Bookings
    //             $bookingQuery = PropertyBooking::whereBetween('checkin_date', [$start, $end]);
    //             if ($filterPType && $filterPropertyId) {
    //                 $bookingQuery->where('pType', $filterPType)->where('property_id', $filterPropertyId);
    //             }
    //             $bookings = $bookingQuery->get();

    //             $bookingSummary = $bookings->groupBy(fn($item) => $item->property_id . '||' . $item->pType)
    //                 ->map(function ($items, $key) {
    //                     [$propId, $pType] = explode('||', $key);
    //                     return [
    //                         'property_id' => $propId,
    //                         'pType' => $pType,
    //                         'bookings' => $items->count(),
    //                         'amount' => $items->sum('payable_amount'),
    //                     ];
    //                 });

    //             // 🔹 Expenses
    //             $expenseQuery = TblOwnerExpenses::whereBetween('date', [$start, $end]);
    //             if ($filterPType && $filterPropertyId) {
    //                 $expenseQuery->where('pType', $filterPType)->where('property_id', $filterPropertyId);
    //             }
    //             $expenses = $expenseQuery->get();

    //             $expenseSummary = $expenses->groupBy(fn($item) => $item->property_id . '||' . $item->pType)
    //                 ->map(function ($items, $key) {
    //                     [$propId, $pType] = explode('||', $key);
    //                     return [
    //                         'property_id' => $propId,
    //                         'pType' => $pType,
    //                         'expenses' => $items->sum('amount'),
    //                     ];
    //                 });

    //           // $keys = $bookingSummary->keys()->merge($expenseSummary->keys())->unique();
    //             $keys = $expenseSummary->keys();
    //             foreach ($keys as $key) {
    //                 [$unitId, $unitPType] = explode('||', $key);

    //                 $booking = $bookingSummary[$key] ?? ['bookings' => 0, 'amount' => 0];
    //                 $expense = $expenseSummary[$key] ?? ['expenses' => 0];

    //                 // 🔹 Unit Name
    //                 $unitName = '';
    //                 if ($unitPType == 'unit') {
    //                     $unit = TblHomeUnit::find($unitId);
    //                     $unitName = $unit?->unit_name ?? 'Unit ' . $unitId;
    //                 } elseif ($unitPType == 'multiunit') {
    //                     $unit = TblHomeMultiUnit::find($unitId);
    //                     $unitName = $unit?->unit_name ?? 'MultiUnit ' . $unitId;
    //                 }
    //                 $groupedData[] = [
    //                     'month' => $monthName,
    //                     'unit' => $unitId,
    //                     'pType' => $unitPType,
    //                     'unit_name' => $unitName,
    //                     'bookings' => $booking['bookings'],
    //                     'amount' => $booking['amount'],
    //                     'expenses' => $expense['expenses'],
    //                     'total' => $booking['amount'] - $expense['expenses'],
    //                 ];
    //             }
    //         }
    //         $items = collect($groupedData);
    //         $perPage = 50;
    //         $currentPage = LengthAwarePaginator::resolveCurrentPage();
    //         $currentItems = $items->slice(($currentPage - 1) * $perPage, $perPage)->values();
    //         $paginatedItems = new LengthAwarePaginator(
    //             $currentItems,
    //             $items->count(),
    //             $perPage,
    //             $currentPage,
    //             ['path' => request()->url(), 'query' => request()->query()]
    //         );
    //     $ownerExpensesList = TblOwnerExpenses::get(); 
    //     return view('pms.master.owner-expenses.owner-revenue-list', [
    //         'items' => $paginatedItems,
    //         'ownerExpensesList' => $ownerExpensesList,
    //     ]);
    // }
    
    public function ownerList(Request $request){
        
         $units = $this->propertyService->applyUserRoleFilter(
            TblHomeUnit::with('ownerData')->whereNotNull('ru_property_id')
        )->get();

        $multiUnits = $this->propertyService->applyUserRoleFilter(
            TblHomeMultiUnit::with('ownerData')->whereNotNull('ru_property_id')
        )->get();

        $items = $units->merge($multiUnits);


    $perPage = 50;
    $currentPage = LengthAwarePaginator::resolveCurrentPage();
    $currentItems = $items->slice(($currentPage - 1) * $perPage, $perPage)->values();
    $paginatedItems = new \Illuminate\Pagination\LengthAwarePaginator(
        $currentItems,
        $items->count(),
        $perPage,
        $currentPage,
        ['path' => request()->url(), 'query' => request()->query()]
    );


     //  dd($properties);
      return view('pms.master.owner-expenses.owner-list', [
        'items' => $paginatedItems,
    ]);
}

    
    
    public function ownerRevenueList(Request $request)
{
    $filterYear = $request->input('year', now()->year);
    $filterPType = $request->pType;
    $filterPropertyId = $request->property_id;
    $filterOwnerId = $request->owner_id;

            
    $months = collect();
    $groupedData = []; 
   
    if ($filterPType && $filterPropertyId) {
        $months = collect(range(1, 12))->map(fn ($i) => Carbon::createFromDate($filterYear, $i, 1));
        foreach ($months as $month) {
            $start = $month->copy()->startOfMonth();
            $end   = $month->copy()->endOfMonth();
            $monthKey = $month->format('F'); 

            // Bookings
            $bookingQuery = PropertyBooking::whereBetween('checkin_date', [$start, $end]);
            $bookingQuery->where('property_booking_status', 'Confirmed')->where('pType', $filterPType)->where('property_id', $filterPropertyId);
            $bookings = $bookingQuery->get();

            $bookingSummary = $bookings->groupBy(fn($item) => $item->property_id.'||'.$item->pType)
                ->map(fn($items) => [
                    'bookings' => $items->count(),
                    'amount'   => $items->sum('base_price'),
                ]);

            // Expenses
            $expenseQuery = TblOwnerExpenses::whereBetween('date', [$start, $end]);
            $expenseQuery->where('pType', $filterPType)->where('property_id', $filterPropertyId);
            $expenses = $expenseQuery->get();

            $expenseSummary = $expenses->groupBy(fn($item) => $item->property_id.'||'.$item->pType)
                ->map(fn($items) => [
                    'expenses' => $items->sum('amount'),
                ]);

            // Merge keys
            $keys = $bookingSummary->keys()->merge($expenseSummary->keys())->unique();

            foreach ($keys as $key) {
                [$unitId, $unitPType] = explode('||', $key);

                $booking = $bookingSummary[$key] ?? ['bookings' => 0, 'amount' => 0];
                $expense = $expenseSummary[$key] ?? ['expenses' => 0];

                $unitName = '';
                $ownerName = '';

                if ($unitPType === 'unit') {
                    $unit = TblHomeUnit::with('ownerData')->find($unitId);
                    $unitName = $unit->unit_name ?? 'Unit ' . $unitId;
                    $ownerName = optional($unit->ownerData)->name ?? '';
                } else {
                    $unit = TblHomeMultiUnit::with('ownerData')->find($unitId);
                    $unitName = $unit->unit_name ?? 'MultiUnit ' . $unitId;
                    $ownerName = optional($unit->ownerData)->name ?? '';
                }
                
                $uKey = $unitId.'||'.$unitPType;

                if (!isset($groupedData[$uKey])) {
                    $groupedData[$uKey] = [
                        'unit_id'   => $unitId,
                        'pType'     => $unitPType,
                        'unit_name' => $unitName,
                        'owner_name' => $ownerName,
                        'months'    => []
                    ];
                }
                $groupedData[$uKey]['months'][$monthKey] = [
                    'bookings' => $booking['bookings'],
                    'amount'   => $booking['amount'],
                    'expenses' => $expense['expenses'],
                    'total'    => $booking['amount'] - $expense['expenses'],
                ];
            }
        }
    }

    $items = collect(array_values($groupedData));
    $perPage = 10;
    $currentPage = LengthAwarePaginator::resolveCurrentPage();
    $currentItems = $items->slice(($currentPage - 1) * $perPage, $perPage)->values();

    $paginatedItems = new LengthAwarePaginator(
        $currentItems,
        $items->count(),
        $perPage,
        $currentPage,
        ['path' => request()->url(), 'query' => request()->query()]
    );
   
    $owner_new_name = null;
    $property_new_name = null;
    if ($filterPType === 'unit') {
        $unit = TblHomeUnit::with('ownerData')->find($filterPropertyId);
        $owner_new_name = $unit->ownerData->name ?? '';
        $property_new_name = $unit->unit_name ?? '';
    } else {
        $unit = TblHomeMultiUnit::with('ownerData')->find($filterPropertyId);
        $owner_new_name = $unit->ownerData->name ?? '';
        $property_new_name = $unit->unit_name ?? ' ';
    }
    return view('pms.master.owner-expenses.owner-revenue-list', [
        'months' => $months,          
        'items'  => $paginatedItems,   
        'year'   => $filterYear,
        'property_new_name'=> $property_new_name,
        'owner_new_name'=> $owner_new_name,
    ]);
}


    public function ownerRevenueExport(Request $request) {
        return Excel::download(new OwnerRevenueReportExport($request->all()), 'owner-revenue-report.xlsx');
    }
    
    public function getUnitsByOwnerExpense(Request $request)
    {
        $owner_expenses = TblOwnerExpenses::where('id', $request->expense_id)->first(); 

        $units = [];
 
        if ($request->pType == 'unit') {
            $units = TblHomeUnit::where('id', $owner_expenses->property_id)->get(['id','unit_name']);
        } elseif ($request->pType == 'multiunit') {
            $units = TblHomeMultiUnit::where('id', $request->property_id)->get(['id','unit_name']);
        }

        return response()->json($units);
    }



}
<?php

namespace App\Http\Controllers\PMS\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TblCollection;
use App\Models\TblLead;
use App\Models\Country;
use App\Models\State;
use App\Models\City;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use App\helper\MasterHelper;
use Maatwebsite\Excel\Facades\Excel;
use App\Services\PropertyService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use App\Exports\LeadListExport;
use Illuminate\Support\Facades\Validator;
class LeadController extends Controller
{   

    protected $propertyService;

    public function __construct()
    {
        $this->propertyService = new PropertyService();
    }



    public function index(Request $request){

        $start_date = null;
        $end_date = null;
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
        
        
        $start_date_in = null;
        $end_date_in = null;
        if ($request->filled('searchDateCheckIn') && str_contains($request->searchDateCheckIn, ' to ')) {
            [$from, $to] = explode(' to ', $request->searchDateCheckIn);
            try {
                $start_date_in = \Carbon\Carbon::createFromFormat('d/m/Y', trim($from))->format('Y-m-d');
                $end_date_in = \Carbon\Carbon::createFromFormat('d/m/Y', trim($to))->format('Y-m-d');
            } catch (\Exception $e) {
                $start_date_in = null;
                $end_date_in = null;
            }
        }


        //$query = TblLead::with(['user']);
        $query = $this->propertyService->applyUserRoleFilter(TblLead::with(['user']));
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }
        if ($request->filled('stage')) {
            $query->where('stage', $request->stage);
        }
        
        // if ($request->filled('checkInDate') && $request->filled('checkOutDate')) {
        //     $query->where('checkin_date', '>=', $request->checkInDate)
        //             ->where('checkout_date', '<=', $request->checkOutDate);
        // }
        

         if ($start_date_in && $end_date_in) {
            $query->whereBetween('checkin_date', [$start_date_in, $end_date_in]);
        }

        $items = $query->orderby('id','desc')->paginate(20)->withQueryString();
      //  dd($items);
        return view('pms.master.lead.list',compact('items'));
    } 

     /*  public function index(Request $request)
{
    $query = TblLead::with(['country', 'state', 'city']); 

    // search by name
    if ($request->filled('search')) {
        $query->where('name', 'like', '%' . $request->search . '%');
    }

    // filter by country
    if ($request->filled('country')) {
        $query->where('country_id', $request->country);
    }

    // filter by state
    if ($request->filled('state')) {
        $query->where('state_id', $request->state);
    }

    // filter by city
    if ($request->filled('city')) {
        $query->where('city_id', $request->city);
    }

    $items = $query->orderBy('id', 'desc')->paginate(20)->withQueryString();

    

if ($request->filled('state') && !$states->pluck('id')->contains($request->state)) {
    $selectedState = State::select('id','state_name')->find($request->state);
    if ($selectedState) {
        $states->push($selectedState);
    }
}

if ($request->filled('city') && !$cities->pluck('id')->contains($request->city)) {
    $selectedCity = City::select('id','city_name')->find($request->city);
    if ($selectedCity) {
        $cities->push($selectedCity);
    }
}


 
    return view('pms.master.lead.list', compact('items', 'countries','states','cities'));
} */


    public function leadExport(Request $request) {
        return Excel::download(new LeadListExport($request->all()), 'lead.xlsx');
    }


    public function form($id = null)
{
    $detail = TblLead::where('id', $id)->first();
    $countries = DB::table('countries')->whereNull('deleted_at')->get();

    $states = collect();
    $cities = collect();

    if ($detail && $detail->country_id) {
        $states = DB::table('states')
            ->where('country_id', $detail->country_id)
            ->whereNull('deleted_at')
            ->get();
    }

    if ($detail && $detail->state_id) {
        $cities = DB::table('cities')
            ->where('state_id', $detail->state_id)
            ->whereNull('deleted_at')
            ->get();
    }

    return view('pms.master.lead.form', compact('detail', 'countries', 'states', 'cities'));
}

public function getStates(Request $request)
{
    $states = DB::table('states')
        ->where('country_id', $request->country_id)
        ->get(['id', 'state_name']);
    return response()->json($states);
}

public function getCities(Request $request)
{
    $cities = DB::table('cities')
        ->where('state_id', $request->state_id)
        ->whereNull('deleted_at')
        ->get(['id', 'city_name']);
    return response()->json($cities);
}


    public function save(Request $request){
        
        $rules = [
            'name' => 'required',
            'email' => 'required|string|email|max:255',
            'mobile' => 'required|digits_between:6,12',
            'stage'  => 'required',
        ];
        if ($request->stage === 'Booked') {
            $rules['booking_id'] = 'required';
        }
        $request->validate($rules);
        $user = Auth::guard('admin')->user();
        $data = [
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'mobile' => $request->input('mobile'),
            'source' => $request->input('source'),
            'note' => $request->input('note'),
            'booking_id' => $request->input('booking_id'),
            'checkin_date' => $request->input('checkin_date'),
            'checkout_date' => $request->input('checkout_date'),
            'date' => date('Y-m-d'),
            'stage' => $request->input('stage'),
            'user_id'=> $user->id,
            'owner_id'=> ($user->role_id == 6) ? $user->id : 0,
            'parent_user_id'=> ($user->role_id == 7) ? $user->id : $user->parent_user_id,
        ];
        if ($request->has('id') && $request->id) {
            TblLead::where('id', $request->id)->update($data);
        } else {
            $maxId = TblLead::max('id');
            $data['id'] = $maxId ? $maxId + 1 : 1;
            DB::table('tbl_leads')->insert($data);
        }
        return redirect()->route('pms.lead.list')->with('success', $request->id ? 'Lead updated successfully!' : 'Lead added successfully!');
    }

    public function toggleStatus($id){   
        try {
            $detail = TblCollection::where(['id'=>$id])->first();
            TblCollection::where(['id'=>$id])->update(['status'=>!$detail->status]);
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
            TblLead::where('id', $id)->delete();
            return response()->json([
                'status' => true,
                'data' => '',
                'message' => 'Lead deleted successfully'
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
                'message' => 'No Lead selected for deletion.'
            ], 400);
        }

        try {
            TblLead::whereIn('id', $ids)->delete();
            return response()->json([
                'status' => true,
                'message' => 'Lead(s) deleted successfully.'
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
                throw new \Exception('positions must be an array.');
            }

            // Loop through the positions array
            foreach ($positions as $index => $id) {
                // Update the position value for the record with the given id
                TblCollection::where('id', $id)->update(['position' => $index]);
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
            $detail = TblCollection::where(['id'=>$id])->first();
            TblCollection::where(['id'=>$id])->update(['show_on_collection_page'=>!$detail->show_on_collection_page]);
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

    public function ajaxCountries(Request $request)
{
    $search = $request->get('q');
    $query = Country::select('id','name');
    if ($search) {
        $query->where('name','like',"%$search%");
    }
    $results = $query->orderBy('name')->limit(50)->get();
    return response()->json($results);
}

// For states
public function ajaxStates(Request $request)
{
    $query = State::query();

    if ($request->has('country_id')) {
        $query->where('country_id', $request->country_id);
    }

    if ($request->q) {
        $query->where('state_name', 'like', '%'.$request->q.'%');
    }

    return $query->limit(50)->get();
}

// For cities
public function ajaxCities(Request $request)
{
    $query = City::query();

    if ($request->has('state_id')) {
        $query->where('state_id', $request->state_id);
    }

    if ($request->q) {
        $query->where('city_name', 'like', '%'.$request->q.'%');
    }

    return $query->limit(50)->get();
}

public function leadImport(Request $request)
{
    
    $validator = Validator::make($request->all(), [
        //'file' => 'required|mimes:csv|max:2048'
        'file' => 'required|file|mimes:csv,txt|max:2048',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'status' => false,
            'errors' => $validator->errors()->all()
        ], 422);
    }

    
    $formattedRows = Excel::toArray([], $request->file('file'));

    if (empty($formattedRows) || empty($formattedRows[0])) {
        return response()->json([
            'status' => false,
            'errors' => ['File is empty or invalid']
        ], 422);
    }

   
    unset($formattedRows[0][0]);

    
    $output = [];
    foreach ($formattedRows[0] as $index => $row) {
        $output[$index]['lead'] = array_slice($row, 0, 9); 
    }

    $leadArr = isset($output) ? $output : [];

   
    if (empty($leadArr)) {
        return response()->json([
            'status' => false,
            'errors' => ['No leads to import. The file contains only headers or is empty.']
        ], 422);
    }

    // 6. Validation rules
    $validator = Validator::make(['leadArr' => $leadArr], [
        'leadArr.*.lead.0' => 'required',                     
        'leadArr.*.lead.1' => 'required|email',              
        'leadArr.*.lead.2' => 'required|digits_between:6,12', 
        
        'leadArr.*.lead.3' => ['nullable', 'regex:/^\d{2}-\d{2}-\d{4}$/'], 
        'leadArr.*.lead.4' => ['nullable', 'regex:/^\d{2}-\d{2}-\d{4}$/'], 
        
        
        
        'leadArr.*.lead.5' => 'required|in:Cold,Warm,Hot,Dropped,Booked', 
        'leadArr.*.lead.6' => 'required_if:leadArr.*.lead.3,Booked',
        'leadArr.*.lead.7' => 'nullable', 
        'leadArr.*.lead.8' => 'nullable', 
    ]);

    $customMsg = [];
    foreach ($leadArr as $rIndex => $row) {
        $customMsg['leadArr.' . $rIndex . '.lead.0'] = 'Row ' . ($rIndex + 1) . ': The name field is required.';
        $customMsg['leadArr.' . $rIndex . '.lead.1'] = 'Row ' . ($rIndex + 1) . ': The email must be a valid email address.';
        $customMsg['leadArr.' . $rIndex . '.lead.2'] = 'Row ' . ($rIndex + 1) . ': The mobile field must be between 6 and 12 digits.';
        
        $customMsg['leadArr.' . $rIndex . '.lead.3'] = 'Row ' . ($rIndex + 1) . ': Check-in date must be in DD-MM-YYYY format.';
        $customMsg['leadArr.' . $rIndex . '.lead.4'] = 'Row ' . ($rIndex + 1) . ': Check-out date must be in DD-MM-YYYY format.';

        
        $customMsg['leadArr.' . $rIndex . '.lead.5'] = 'Row ' . ($rIndex + 1) . ': The stage field must be one of Cold, Warm, Hot, Dropped, or Booked.';
        $customMsg['leadArr.' . $rIndex . '.lead.6'] = 'Row ' . ($rIndex + 1) . ': The booking ID is required when stage is Booked.';
    }

    $validator->setCustomMessages($customMsg);
    if ($validator->fails()) {
        return response()->json([
            'status' => false,
            'errors' => $validator->errors()->all() 
        ], 422);
    }
    $errors = [];
    foreach ($leadArr as $rIndex => $row) {
        $lead = $row['lead'];
        $email = $lead[1];
        $mobile = $lead[2];
    }

    // 10. If there are duplicate errors, return them
    if (!empty($errors)) {
        return response()->json([
            'status' => false,
            'errors' => $errors
        ], 422);
    }
//dd($leadArr);
    // 11. If all validations pass, insert leads
    try {
        $user = Auth::guard('admin')->user();
        foreach ($leadArr as $row) {
            $lead = $row['lead'];
//dd($lead[6]);
            TblLead::create([
                'date'           => now()->format('Y-m-d'),
                'user_id'        => $user->id,
                'parent_user_id' => ($user->role_id == 7) ? $user->id : $user->parent_user_id,
                'name'           => $lead[0],
                'email'          => $lead[1],
                'mobile'         => $lead[2],
                
                'checkin_date'   => $this->convertDate($lead[3]),
               'checkout_date'  => $this->convertDate($lead[4]),
                
                'stage'          => $lead[5],
                'booking_id'     => $lead[6] ?? null,
                'source'     => $lead[7] ?? null,
                'note'     => $lead[8] ?? null,
            ]);
        }

        return response()->json([
            'status' => true,
            'message' => 'Leads imported successfully!'
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'status' => false,
            'errors' => ['An error occurred while importing leads: ' . $e->getMessage()]
        ], 500);
    }
}


private function convertDate($date)
{
    if (empty($date)) return null;

    try {
        return \Carbon\Carbon::createFromFormat('d-m-Y', $date)->format('Y-m-d');
    } catch (\Exception $e) {
        return null;
    }
}


}
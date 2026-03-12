<?php

namespace App\Http\Controllers\PMS\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TblLanding;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Models\TblHomeUnit;
use App\Models\TblHomeMultiUnit;
use App\Models\TblHomeType;
use Illuminate\Validation\Rule;
use App\helper\MasterHelper;
use Illuminate\Support\Facades\DB;

class LandingController extends Controller
{   

    public function index(Request $request){
        $query = TblLanding::query();

        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }
        $items = $query->orderby('id','desc')->paginate(20)->withQueryString();
        return view('pms.master.landing.list',compact('items'));
    }

    public function form($id = null)
    {
        $detail = $id ? TblLanding::find($id) : null;
        $homeTypeData = TblHomeType::get();
        $userMappedPropertyIds = [];
        $userMappedLocationIds = [];
        if ($detail) {
            $userMappedPropertyIds = is_string($detail->property_id)
                ? json_decode($detail->property_id, true) ?? explode(',', $detail->property_id)
                : (array) $detail->property_id;

            $userMappedLocationIds = is_string($detail->property_type_id)
                ? json_decode($detail->property_type_id, true) ?? explode(',', $detail->property_type_id)
                : (array) $detail->property_type_id;
        }

        

        // $units = TblHomeUnit::whereNotNull('ru_property_id')
        //     ->select('id', 'unit_name')->get();

        // $multiUnits = TblHomeMultiUnit::whereNotNull('ru_property_id')
        //     ->select('id', 'unit_name')->get();

        // $allProperties = $units->merge($multiUnits)->values();
        
    $unitsQuery = TblHomeUnit::whereNotNull('ru_property_id')
    ->select('id', 'unit_name', 'home_type_id'); 
    $multiUnitsQuery = TblHomeMultiUnit::whereNotNull('ru_property_id')
        ->select('id', 'unit_name', 'home_type_id'); 
    if (!empty($userMappedLocationIds)) {
        $unitsQuery->whereIn('home_type_id', $userMappedLocationIds);
        $multiUnitsQuery->whereIn('home_type_id', $userMappedLocationIds);
    }
    $units = $unitsQuery->get();
    $multiUnits = $multiUnitsQuery->get();
    $allProperties = $units->merge($multiUnits)->values();
        

        return view('pms.master.landing.form', compact(
            'homeTypeData',
            'allProperties',
            'detail',
            'userMappedPropertyIds',
            'userMappedLocationIds'
        ));
    }

    public function save(Request $request){
        
        $rules = [
            'title' => 'required',
        ];

        if ($request->has('id') && $request->id) {
            if ($request->hasFile('image') || $request->input('remove_image') == 1) {
                $rules['image'] = 'required|image|max:3072';
            }
        } else {
            $rules['image'] = 'required|image|max:3072';
        }

        $request->validate($rules);


        $checkDuplicate = TblLanding::where('title', '=', $request->title);
        if ($request->id) {
            $checkDuplicate->where('id', '!=', $request->id);
        }
        $checkDuplicate = $checkDuplicate->first();
        
        if ($checkDuplicate) {
            return redirect()->back()
            ->withErrors(['title' => 'The title is already in use.'])
            ->withInput();
        }

        $property_type_id = json_encode($request->mappedLocations ?? []);
        $property_id = json_encode($request->mappedProperties ?? []);
        
        $data = [
            'title' => $request->input('title'),
            'keyword' => $request->input('keyword'),
            'description' => $request->input('description'),
            'property_type_id' => $property_type_id,
            'property_id' => $property_id,
            'slug' => Str::of($request->input('title'))->slug('-'),
            'status' => 1,
        ];

        if ($request->hasFile('image')) {
            $filePath = $request->file('image')->store('landing', 'public');
            $data['image'] = basename($filePath);
        }

         if ($request->has('id') && $request->id) {
            // Update Mode
            TblLanding::where('id', $request->id)->update($data);
        } else {
            // Generate custom ID manually (latest + 1)
            // $maxId = TblLanding::max('id');
            // $data['id'] = $maxId ? $maxId + 1 : 1;
    
            // DB::table('tbl_landing')->insert($data);
            TblLanding::create($data);
        }

        return redirect()->route('pms.landing.list')->with('success', $request->id ? 'Landing page updated successfully!' : 'Landing page added successfully!');
    }

    public function toggleStatus($id){   
        try {
            $detail = TblLanding::where(['id'=>$id])->first();
            TblLanding::where(['id'=>$id])->update(['status'=>!$detail->status]);
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
            TblLanding::where('id', $id)->delete();
            return response()->json([
                'status' => true,
                'data' => '',
                'message' => 'Landing Page deleted successfully'
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
                'message' => 'No Landing Page selected for deletion.'
            ], 400);
        }

        try {
            TblLanding::whereIn('id', $ids)->delete();
            return response()->json([
                'status' => true,
                'message' => 'Landing page deleted successfully.'
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
                TblLanding::where('id', $id)->update(['position' => $index]);
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
            $detail = TblLanding::where(['id'=>$id])->first();
            TblLanding::where(['id'=>$id])->update(['show_on_collection_page'=>!$detail->show_on_collection_page]);
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
}
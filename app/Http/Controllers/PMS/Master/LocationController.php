<?php

namespace App\Http\Controllers\PMS\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\TblLocation;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Pagination\Paginator;
use App\helper\MasterHelper;
use App\Models\RuLocation;
use App\Models\TblRuLocation;
use App\Models\TblState;


use DB;

class LocationController extends Controller
{
    public function index(Request $request)
    {
        try {
            $query = TblLocation::query();
            $query->select('tbl_location.*', 'tbl_states.name', 'tbl_states.id as state_id')->leftJoin('tbl_states', 'tbl_states.id', '=', 'tbl_location.state_id');
            if ($request->filled('search_location')) {
                $query->where('tbl_location.location_name', 'like', $request->get('search_location') . '%');
            }

            $items = $query->paginate(20)->withQueryString();
            
            return view('pms.master.location.list', compact('items'));
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => "Internal Error",
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function form($id = null)
    {

        $detail = TblLocation::select('tbl_location.*', 'tbl_states.name', 'tbl_states.id as state_id')->leftJoin('tbl_states', 'tbl_states.id', '=', 'tbl_location.state_id')
            ->where('tbl_location.id', $id)
            ->first();

        $states = TblState::where('status', 1)->get();
        $ruLocations = TblRuLocation::where('status', 1)->where('ru_location_type_id', 4)->orderBy('name')->get();
        return view('pms.master.location.form', compact('detail', 'states', 'ruLocations', 'ruLocations'));
    }

    // public function save(Request $request)
    // {
    //     $validator = Validator::make($request->all(), [
    //         'state_id' => 'required',

    //         'location' => [
    //             'required',
    //             Rule::unique('tbl_location', 'location_name')->ignore($request->id),
    //         ],

    //         'title' => 'required',
    //         'sub_title' => 'required',

    //         // Image validation (optional)
    //         'image' => [
    //             'nullable',
    //             'image',
    //             'max:3072', 
                
    //         ],
    //     ]);

    //     if ($validator->fails()) {
    //         return back()
    //             ->withErrors($validator)
    //             ->withInput()
    //             ->with('error', $validator->errors()->first());
    //     }

    //     try {
    //         $data = TblLocation::updateOrCreate(
    //             ['id' => $request->id],
    //             [
    //                 'state_id'         => $request->state_id,
    //                 'location_name'    => $request->location,
    //                 'title'            => $request->title,
    //                 'sub_title'        => $request->sub_title,
    //                 'slug_name'        => Str::slug($request->location),
    //                 'meta_title'       => $request->meta_title ?? $request->location,
    //                 'meta_description' => $request->meta_description ?? $request->location,
    //                 'meta_keyword'     => $request->meta_keywords ?? $request->location,
    //             ]
    //         );

    //         // Upload image
    //         if ($request->hasFile('image')) {
    //             $filePath = $request->file('image')->store('location', 'public');
    //             $data->image = basename($filePath);
    //             $data->save();
    //         }

    //         // Remove image
    //         if ($request->input('remove_image') == 1) {
    //             if ($data->image && file_exists(public_path('storage/location/' . $data->image))) {
    //                 unlink(public_path('storage/location/' . $data->image));
    //             }
    //             $data->image = null;
    //             $data->save();
    //         }

    //         return redirect()
    //             ->route('pms.location.list')
    //             ->with('success', $request->id
    //                 ? 'Location updated successfully.'
    //                 : 'Location saved successfully.');
    //     } catch (\Exception $e) {
    //         return back()
    //             ->withInput()
    //             ->with('error', 'Something went wrong: ' . $e->getMessage());
    //     }
    // }
    
    
    public function save(Request $request){

        $validator = Validator::make($request->all(), [
            'state_id' => 'required',
             'location' => 'required',

             'location_name' => [
                'required',
                Rule::unique('tbl_location', 'location_name')->ignore($request->id),
            ],


            'title' => 'required',
            'sub_title' => 'required',

            // Image validation (optional)
            'image' => [
                'nullable',
                'image',
                'max:3072',

            ],
        ]);

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput()
                ->with('error', $validator->errors()->first());
        }

        try {
            $data = TblLocation::updateOrCreate(
                ['id' => $request->id],
                [
                    'state_id'         => $request->state_id,
                    'ru_location_id'  => $request->location,
                    'location_name'    => $request->location_name ?? $request->location_name,
                    'title'            => $request->title,
                    'sub_title'        => $request->sub_title,
                    'slug_name'        => Str::slug($request->location_name),
                    'meta_title'       => $request->meta_title ?? $request->title,
                    'meta_description' => $request->meta_description ?? $request->title,
                    'meta_keyword'     => $request->meta_keywords ?? $request->title,
                ]
            );
            
            
            

            // Upload image
            if ($request->hasFile('image')) {
                $filePath = $request->file('image')->store('location', 'public');
                $data->image = basename($filePath);
                $data->save();
            }

            // Remove image
            if ($request->input('remove_image') == 1) {
                if ($data->image && file_exists(public_path('storage/location/' . $data->image))) {
                    unlink(public_path('storage/location/' . $data->image));
                }
                $data->image = null;
                $data->save();
            }
            
            
            $xml = "<Push_SetPropertiesStatus_RQ>
                          <Authentication>
                          <UserName>" . config('ru.RU_USER_NAME') . "</UserName>
                          <Password>" . config('ru.RU_PASSWORD') . "</Password>
                      </Authentication>
                      <IsActive>0</IsActive>
                      <IsArchived>1</IsArchived>
                      <PropertyIDs>
                          <PropertyID>" . $request->location . "</PropertyID>
                      </PropertyIDs>
             </Push_SetPropertiesStatus_RQ>";
            $xmlResponse = MasterHelper::makeXmlRequest($xml);

            return redirect()
                ->route('pms.location.list')
                ->with('success', $request->id
                    ? 'Location updated successfully.'
                    : 'Location saved successfully.');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Something went wrong: ' . $e->getMessage());
        }
    }



    public function toggleStatus($id)
    {
        try {
            $detail = TblLocation::where(['id' => $id])->first();
            TblLocation::where(['id' => $id])->update(['status' => !$detail->status]);
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
                'trace' => $e->getTraceAsString()
            ], 500);
        }
    }

    public function ShowHometoggleStatus($id)
    {
        try {
            $detail = TblLocation::where(['id' => $id])->first();
            TblLocation::where(['id' => $id])->update(['show_on_location_page' => !$detail->show_on_location_page]);
            return response()->json([
                'status' => true,
                'data' => '',
                'message' => 'Show home page updated successfully'
            ], 200);
        } catch (\Exception $e) {

            return response()->json([
                'status' => false,
                'message' => "Internal Error",
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ], 500);
        }
    }

    public function delete($id)
    {
        try {
            TblLocation::where('id', $id)->delete();
            return response()->json([
                'status' => true,
                'data' => '',
                'message' => 'Location deleted successfully'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => "Internal Error",
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ], 500);
        }
    }

    public function multiDelete(Request $request)
    {
        $ids = $request->input('ids');

        if (empty($ids) || !is_array($ids)) {
            return response()->json([
                'status' => false,
                'message' => 'No location selected for deletion.'
            ], 400);
        }

        try {
            TblLocation::whereIn('id', $ids)->delete();
            return response()->json([
                'status' => true,
                'message' => 'Location(s) deleted successfully.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Internal Error',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
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
                TblLocation::where('id', $id)->update(['position' => $index]);
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
}

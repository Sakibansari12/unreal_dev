<?php

namespace App\Http\Controllers\PMS\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\TblAmenities;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use App\helper\MasterHelper;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class AmenitiesController extends Controller
{
    public function index(Request $request){
        $query = TblAmenities::query();

        if ($request->filled('search_amenitie')) {
            $query->where('amenities_name', 'like', '%' . $request->search_amenitie . '%');
        }
        $items = $query->paginate(20)->withQueryString();
        return view('pms.master.amenities.list',compact('items'));
    }
    public function form($id=null){
        $detail = TblAmenities::where('id',$id)->first();
        return view('pms.master.amenities.form',compact('detail'));
    }

    public function save(Request $request){
        
        $rules = [
            'name' => 'required|string|max:255',
        ];

        // Check if it's an update
        if ($request->has('id') && $request->id) {
            // Update Mode
            if ($request->hasFile('icon') || $request->input('remove_icon') == 1) {
                $rules['icon'] = 'required|image|max:1024';
            }
        } else {
            // Add Mode
            $rules['icon'] = 'required|image|max:1024';
        }

        $request->validate($rules);

        $data = [
            'amenities_name' => $request->input('name'),
            'add_ip' => $request->ip(),
            'add_by' => Auth::guard('admin')->user()->name,
        ];

        if ($request->hasFile('icon')) {
            $filePath = $request->file('icon')->store('amenities', 'public');
            $data['amenities_image'] = basename($filePath);
        }

        $amenity = TblAmenities::updateOrCreate(
            ['id' => $request->input('id')], 
            $data 
        );

        return redirect()->route('pms.amenities.list')->with('success', $request->id ? 'Amenity updated successfully!' : 'Amenity added successfully!');
    }

    public function toggleStatus($id){   
        try {
            $detail = TblAmenities::where(['id'=>$id])->first();
            TblAmenities::where(['id'=>$id])->update(['status'=>!$detail->status]);
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
            TblAmenities::where('id', $id)->delete();
            return response()->json([
                'status' => true,
                'data' => '',
                'message' => 'Amenity deleted successfully'
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
                'message' => 'No amenities selected for deletion.'
            ], 400);
        }

        try {
            TblAmenities::whereIn('id', $ids)->delete();
            return response()->json([
                'status' => true,
                'message' => 'Amenitie(s) deleted successfully.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Internal Error',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}

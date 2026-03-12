<?php

namespace App\Http\Controllers\PMS\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TblArea;
use App\Models\TblState;
use App\Models\TblLocation;
use Illuminate\Support\Facades\DB;

class AreaController extends Controller
{
    public function index(Request $request)
    {
        try {
            $query = TblArea::query();

            $query->leftJoin('tbl_location', 'tbl_location.id', '=', 'tbl_area.location_id');
            $query->leftJoin('tbl_states', 'tbl_states.id', '=', 'tbl_location.state_id');
            $query->select('tbl_area.*', 'tbl_states.name as state_name', 'tbl_location.location_name');




            if ($request->filled('search_name')) {
                $query->where('area_name', 'like', '%' . $request->search_name . '%');
            }
            $query->orderBy('tbl_area.id', 'desc');
            $items = $query->paginate(20)->withQueryString();
            return view('pms.master.area.list', compact('items'));
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
        $detail = TblArea::where('id', $id)->first();
        $states = TblState::where('status', 1)->get();

        $locations = [];

        $stateId = old('state_id') ?? ($detail->state_id ?? null);

        if ($stateId) {
            $locations = TblLocation::where('state_id', $stateId)
                ->where('status', 1)
                ->get();
        }

        return view('pms.master.area.form', compact('detail', 'states', 'locations'));
    }

    public function getLocations($state_id)
    {
        $locations = TblLocation::where('state_id', $state_id)
            ->where('status', 1)
            ->get();

        return response()->json($locations);
    }




    public function save(Request $request)
    {
        // ✅ Validation
        $request->validate([
            'state_id'    => 'required|integer',
            'location_id' => 'required|integer',
            'area_name'   => 'required|string|max:255',
        ]);

        // ✅ Duplicate check (same state + same location + same area name)
        $duplicate = TblArea::where('state_id', $request->state_id)
            ->where('location_id', $request->location_id)
            ->where('area_name', $request->area_name);

        if ($request->filled('id')) {
            $duplicate->where('id', '!=', $request->id);
        }

        if ($duplicate->exists()) {
            return redirect()->back()
                ->withErrors(['area_name' => 'This area already exists for the selected state & location.'])
                ->withInput();
        }

        $icon = TblArea::find($request->id);
        $data = [
            'state_id'    => $request->state_id,
            'location_id' => $request->location_id,
            'area_name'   => $request->area_name,
            'status'      => $icon?->status ?? 1,
        ];

        // ✅ Update or Insert
        if ($request->filled('id')) {
            TblArea::where('id', $request->id)->update($data);
            $message = 'Area updated successfully!';
        } else {
            TblArea::create($data);
            $message = 'Area added successfully!';
        }

        return redirect()->route('pms.area.list')->with('success', $message);
    }





    public function toggleStatus($id)
    {
        try {
            $detail = TblArea::where(['id' => $id])->first();
            TblArea::where(['id' => $id])->update(['status' => !$detail->status]);
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


    public function delete($id)
    {
        try {
            TblArea::where('id', $id)->delete();
            return response()->json([
                'status' => true,
                'data' => '',
                'message' => 'Area deleted successfully'
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
                'message' => 'No tag selected for deletion.'
            ], 400);
        }

        try {
            TblArea::whereIn('id', $ids)->delete();
            return response()->json([
                'status' => true,
                'message' => 'Area(s) deleted successfully.'
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

    public function ShowHometagsStatus($id)
    {
        try {
            $detail = TblArea::where(['id' => $id])->first();
            TblArea::where(['id' => $id])->update(['area_show_on_page' => !$detail->area_show_on_page]);
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
}

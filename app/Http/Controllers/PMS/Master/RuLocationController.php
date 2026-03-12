<?php

namespace App\Http\Controllers\PMS\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TblLocation;
use App\helper\MasterHelper;
use App\Models\RuLocation;
use App\Models\TblRuLocation;

class RuLocationController extends Controller
{
   public function index(Request $request){
        $query = TblRuLocation::where('ru_location_type_id', 4)->orderBy('id', 'desc');

        if ($request->filled('search_location')) {
            $query->where('name', 'like', '%' . $request->search_location . '%');
        }

        $items = $query->paginate(50)->withQueryString();
   
        return view('pms.master.ru-location-list', compact('items'));
   }

   public function toggleStatus($id)
    {   
        try {
            $detail = TblRuLocation::where(['id'=>$id])->first();
            TblRuLocation::where(['id'=>$id])->update(['status'=>!$detail->status]);
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

    public function delete($id)
    {   
        try {
            TblRuLocation::where('id', $id)->delete();
            return response()->json([
                'status' => true,
                'data' => '',
                'message' => 'Location Deleted successfully'
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

    public function multiDelete(Request $request){
        $ids = $request->input('ids');
        try {
            TblRuLocation::whereIn('id', $ids)->delete();
            return response()->json([
                'status' => true,
                'data' => '',
                'message' => 'Location Deleted successfully'
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


}

<?php

namespace App\Http\Controllers\PMS\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TblRuAmenity;

class RuAmenitieController extends Controller
{
    public function index(Request $request)
    {
        $query = TblRuAmenity::orderBy('amenities_id', 'desc');

        if ($request->filled('search_amenitie')) {
            $query->where('amenities_name', 'like', '%' . $request->search_amenitie . '%');
        }

        $items = $query->paginate(50)->withQueryString();

        return view('pms.master.ru-amenities-list', compact('items'));
    }


    public function toggleStatus($id)
    {   
        try {
            $detail = TblRuAmenity::where(['amenities_id'=>$id])->first();
            TblRuAmenity::where(['amenities_id'=>$id])->update(['active'=>!$detail->active]);
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
}

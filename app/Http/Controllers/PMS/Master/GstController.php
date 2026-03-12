<?php

namespace App\Http\Controllers\PMS\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\TblGst;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Pagination\Paginator;
use App\helper\MasterHelper;
use DB;


class GstController extends Controller
{
    public function view()
    {
        return view('pms.master.gst.form');
    }


    public function form()
    {
        try {
            $query = TblGst::get();
            return response()->json([
                'status' => true,
                'data' => $query,               
                'message' => 'Successfully Retrive'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => "Internal Error",
                'error' => $e->getMessage(),
            ], 500);
        }

    }

    public function save(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'addMultiItem.*.slabs_start' => 'required|numeric|min:1',
                'addMultiItem.*.slabs_upto' => 'required|numeric|min:1|gt:addMultiItem.*.slabs_start',
                'addMultiItem.*.gst_percentage' => 'required|numeric|min:0|max:100',
            ]);

            // If slabs_upto is missing or null, set a default value before validating.
            foreach ($request->input('addMultiItem', []) as $index => $item) {
                if (empty($item['slabs_upto'])) {
                    // Using square brackets for the correct field name format
                    $request->merge(["addMultiItem[$index][slabs_upto]" => 0]);
                }
            }
            // dd($validator->errors());
            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            $addMultiItem = $request->addMultiItem;

            if (count($addMultiItem) > 0) {
                TblGst::truncate();

                foreach ($addMultiItem as $rs) {
                    $SQL = new TblGst();
                    $SQL->slabs_start = $rs['slabs_start'];
                    $SQL->slabs_upto = $rs['slabs_upto'];
                    $SQL->gst_percentage = $rs['gst_percentage'];
                    $SQL->add_ip = $request->ip();
                    $SQL->add_by = Auth::guard('admin')->user()->name;
                    $SQL->save();
                }

                return response([
                    'status' => true,
                    'message' => 'Successfully Submitted'
                ], 200);
            } else {
                return response([
                    'status' => false,
                    'message' => 'No data received to process.'
                ], 400);
            }

        } catch (\Exception $e) {
            return response([
                'status' => false,
                'message' => 'Error!, please try again later.',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ], 500);
        }
    }

}

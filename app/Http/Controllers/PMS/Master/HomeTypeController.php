<?php

namespace App\Http\Controllers\PMS\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\TblHomeType;
use Illuminate\Support\Str;
use App\helper\MasterHelper;
use DB;

class HomeTypeController extends Controller
{
    public function index(Request $request){
        try {
            $query = TblHomeType::query();
           
            if ($request->filled('search_name')) {
               $query->where('name', 'like', $request->get('search_name') . '%');
            }

            $items = $query->paginate(20)->withQueryString();

            return view('pms.master.hometype.list',compact('items'));

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => "Internal Error",
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function form($id=null){
        $detail = TblHomeType::where('id', $id)->first();
        return view('pms.master.hometype.form',compact('detail'));
    }

    public function save(Request $request)
    {
        $id = $request->id;

        $request->validate([
            'name' => 'required|string|max:255|unique:tbl_home_types,name' . ($id ? ',' . $id : ''),
        ]);

        try {
            $SQL = $id ? TblHomeType::findOrFail($id) : new TblHomeType();

            $SQL->name = $request->name;
            $SQL->url_key = Str::of($request->input('name'))->slug('-');
            $SQL->meta_title = $request->meta_title ?: $request->name;
            $SQL->meta_description = $request->meta_description ?: $request->name;
            $SQL->meta_keyword = $request->meta_keyword ?: $request->name;
            $SQL->add_ip = $request->ip();
            $SQL->add_by = Auth::guard('admin')->user()->name;

            $SQL->save();

            return redirect()->route('pms.hometype.list')
                ->with('success', $id ? 'Home type updated successfully.' : 'Home type saved successfully.');

        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Something went wrong: ' . $e->getMessage());
        }
    }


    public function toggleStatus($id){   
        try {
            $detail = TblHomeType::where(['id'=>$id])->first();
            TblHomeType::where(['id'=>$id])->update(['status'=>!$detail->status]);
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
                'trace' => $e->getTraceAsString()
            ], 500);
        }
    }

    public function delete($id){   
        try {
            TblHomeType::where('id', $id)->delete();
            return response()->json([
                'status' => true,
                'data' => '',
                'message' => 'Home type deleted successfully'
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

    public function multiDelete(Request $request)
    {
        $ids = $request->input('ids');

        if (empty($ids) || !is_array($ids)) {
            return response()->json([
                'status' => false,
                'message' => 'No home type selected for deletion.'
            ], 400);
        }

        try {
            TblHomeType::whereIn('id', $ids)->delete();
            return response()->json([
                'status' => true,
                'message' => 'home type(s) deleted successfully.'
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
}

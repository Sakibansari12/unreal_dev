<?php

namespace App\Http\Controllers\PMS\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Role;
use App\Models\TblHomeUnit;
use App\Models\TblHome;
use App\Models\Admin;
use DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = Admin::orderBy('id', 'desc');

        if ($request->filled('search_user')) {
            $query->where('name', 'like', '%' . $request->search_user . '%');
        }

        $query->where('parent_user_id', Auth::guard('admin')->user()->id);
   
        $items = $query->where('id','!=',1)->paginate(20)->withQueryString();

        return view('pms.user.list', compact('items'));
    }


    public function form($id = null)
    {
        // dump(Auth::guard('admin')->user()->id);
        $detail = $id ? Admin::find($id) : null;

        $query = Role::orderBy('id', 'asc')->where('status', 1);
        if(Auth::guard('admin')->user()->role_id != 1 ){
            $query->where('id','!=',Auth::guard('admin')->user()->role_id);
        }
        $query->where('id', '!=', 1);

        $roles= $query->get();
        $states = DB::table('states')->get();
        $cities = $detail ? DB::table('cities')->where('state_id', $detail->state_id)->get() : collect([]);
        
        $userMappedPropertyIds = [];
        $properties = [];

        if ($detail && $detail->role === 'Owners') {
            $userMappedPropertyIds = TblHomeUnit::where('owner_id', $detail->id)->pluck('id')->toArray();

        $properties = TblHomeUnit::where(function ($q) use ($userMappedPropertyIds) {
            $q->whereIn('id', $userMappedPropertyIds)
              ->orWhere(function($subQuery) {
                  $subQuery->whereNull('owner_id')
                           ->where('parent_user_id', Auth::guard('admin')->user()->id);
              });
        })->get();

        } else {
            $properties = TblHomeUnit::whereNull('owner_id')->where('parent_user_id',Auth::guard('admin')->user()->id)->get();
        }
       
        return view('pms.user.form', compact('detail', 'roles', 'states', 'cities','properties', 'userMappedPropertyIds'));
    }

    public function getCities($state_id)
    {
        $cities = DB::table('cities')->where('state_id', $state_id)->get(['id', 'city_name']);
        return response()->json($cities);
    }

    public function save(Request $request)
    {
        if($request->role_id == 7){
            $request->validate([
                "role_id" => "required",
                "name" => 'required',
                "contact_person" => 'required',
                "mobile" => 'required|string|max:13|regex:/^\+?\d+$/',
                "email" => 'required|email|max:255|unique:admins,email,' . ($request->id ?? 'NULL') . ',id',
                "password" => $request->id ? 'nullable|string|min:8' : 'required|string|min:8',
                "address" => 'required',
                "state_id" => 'required',
                "city_id" => 'required'
            ]);
        }elseif($request->role_id == 8){
            $request->validate([
                "role_id" => "required",
                "name" => 'required',
                "contact_person" => 'required',
                "mobile" => 'required|string|max:13|regex:/^\+?\d+$/',
                "email" => 'required|email|max:255|unique:admins,email,' . ($request->id ?? 'NULL') . ',id',
                "password" => $request->id ? 'nullable|string|min:8' : 'required|string|min:8',
                "address" => 'required',
                "state_id" => 'required',
                "city_id" => 'required'
            ]);
        }else{
            $request->validate([
                'role_id' => 'required',
                'name' => 'required|max:255',
                'email' => 'required|email|max:255|unique:admins,email,' . ($request->id ?? 'NULL') . ',id',
                'mobile' => 'required|string|max:13|regex:/^\+?\d+$/',
                'password' => $request->id ? 'nullable|string|min:8' : 'required|string|min:8'
            ]);
        }
        

        $role = Role::where('id', $request->role_id)->where('status', 1)->firstOrFail();
        // dd($role);
        $state = $request->state_id ? DB::table('states')->select('state_name')->where('id', $request->state_id)->first() : null;
        $city = $request->city_id ? DB::table('cities')->select('city_name')->where('id', $request->city_id)->first() : null;

        if($request->role_id == 7){
            $data = Admin::firstOrNew(['id' => $request->id]);
            $data->name = $request->name;
            $data->email = $request->email;
            $data->mobile_no = $request->mobile;
            $data->contact_person = $request->contact_person;
            $data->address = $request->address;
            $data->state_id = $request->state_id;
            $data->state_name = $state ? $state->state_name : null;
            $data->city_id = $request->city_id;
            $data->city_name = $city ? $city->city_name : null;
            $data->gst = $request->gst;
            $data->sale_plan = $request->sale_plan;
            $data->note = $request->note;
            if ($request->password) {
                $data->password = Hash::make($request->password);
            }
            $data->role = $role->role_name;
            $data->role_id = $request->role_id;
        }elseif($request->role_id == 8){
            $data = Admin::firstOrNew(['id' => $request->id]);
            $data->name = $request->name;
            $data->email = $request->email;
            $data->mobile_no = $request->mobile;
            $data->contact_person = $request->contact_person;
            $data->address = $request->address;
            $data->state_id = $request->state_id;
            $data->state_name = $state ? $state->state_name : null;
            $data->city_id = $request->city_id;
            $data->city_name = $city ? $city->city_name : null;
            $data->gst = $request->gst;
            $data->discount = $request->discount ?? null;
            if ($request->password) {
                $data->password = Hash::make($request->password);
            }
            $data->role = $role->role_name;
            $data->role_id = $request->role_id;
        }else{
            $data = Admin::firstOrNew(['id' => $request->id]);
            $data->name = $request->name;
            $data->email = $request->email;
            $data->mobile_no = $request->mobile;
            if ($request->password) {
                $data->password = Hash::make($request->password);
            }
            $data->role = $role->role_name;
            $data->role_id = $request->role_id;
        }
        $data->parent_user_id = Auth::guard('admin')->user()->id;
        $data->save();
        
        if ($role->role_name === 'Owners') {
            TblHomeUnit::where('owner_id', $data->id)->update(['owner_id' => null]);
            if ($request->has('mappedProperties') && is_array($request->mappedProperties)) {
                TblHomeUnit::whereIn('id', $request->mappedProperties)->update(['owner_id'=>$data->id]);
            }
        } else {
            TblHomeUnit::where('owner_id', $data->id)->update(['owner_id'=>null]);
        }

        return redirect()->route('pms.user.list')
            ->with('success', $request->id ? 'User updated successfully.' : 'User saved successfully.');
    }

    public function toggleStatus($id)
    {
        try {
            $detail = Admin::where(['id' => $id])->first();
            Admin::where(['id' => $id])->update(['status' => !$detail->status]);
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
            ], 500);
        }
    }
    public function delete($id)
    {
        try {
            $user = Admin::findOrFail($id);
            if ($user->role == 'Owners') {
                TblHomeUnit::where('owner_id', $user->id)->update(['owner_id' => null]);
            }
            $user->delete();

            return response()->json([
                'status' => true,
                'data' => '',
                'message' => 'User deleted successfully'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Internal Error',
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
                'message' => 'No users selected for deletion.'
            ], 400);
        }
        try {
            $owners = Admin::whereIn('id', $ids)->where('role', 'Owners')->pluck('id')->toArray();
            if (!empty($owners)) {
                TblHomeUnit::whereIn('owner_id', $owners)->update(['owner_id' => null]);
            }
            Admin::whereIn('id', $ids)->delete();
            return response()->json([
                'status' => true,
                'message' => 'User(s) deleted successfully.'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Internal Error',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}

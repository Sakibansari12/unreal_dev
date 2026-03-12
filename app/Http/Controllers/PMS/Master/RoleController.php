<?php

namespace App\Http\Controllers\PMS\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Role;
use Illuminate\Support\Str;

class RoleController extends Controller
{
    public function index(Request $request){
        $query = Role::orderBy('id', 'desc');

        if ($request->filled('search_role')) {
            $query->where('role_name', 'like', '%' . $request->search_role . '%');
        }

        $items = $query->paginate(50)->withQueryString();

        return view('pms.master.role.list',compact('items'));
    }

    public function form($id=null){
        $detail = Role::where('id', $id)->first();
        return view('pms.master.role.form',compact('detail'));
    }

    public function save(Request $request)
    {
        $request->validate([
            'role_name' => 'required|string|max:255',
        ]);

        try {
            $role = Role::updateOrCreate(
                ['id' => $request->id],
                [
                    'role_name' => $request->role_name,
                    'role_slug' => Str::slug($request->role_name)
                ]
            );

            return redirect()->route('pms.role.list')
                ->with('success', $request->id ? 'Role updated successfully.' : 'Role saved successfully.');

        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Something went wrong: ' . $e->getMessage());
        }
    }


    public function toggleStatus($id){   
        try {
            $detail = Role::where(['id'=>$id])->first();
            Role::where(['id'=>$id])->update(['status'=>!$detail->status]);
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
            Role::where('id', $id)->delete();
            return response()->json([
                'status' => true,
                'data' => '',
                'message' => 'Role deleted successfully'
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
                'message' => 'No roles selected for deletion.'
            ], 400);
        }

        try {
            Role::whereIn('id', $ids)->delete();
            return response()->json([
                'status' => true,
                'message' => 'Role(s) deleted successfully.'
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

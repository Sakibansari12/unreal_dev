<?php

namespace App\Http\Controllers\PMS\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Role;
use App\Models\Sidebar;
use App\Models\SubSidebar;
use App\Models\UserSidebarAccess;
use Illuminate\Support\Facades\DB;

class RolePermissonController extends Controller
{
    public function index(Request $request)
    {
        // Get search input if any
        $search = $request->get('search_role', '');

        // Query to fetch data
        $results = DB::table('user_sidebar_accesses')
            ->join('roles', 'user_sidebar_accesses.role_id', '=', 'roles.id')
            ->where('roles.role_name', '!=', 'Owner')
            ->when($search, function ($query) use ($search) {
                return $query->where('roles.role_name', 'like', '%' . $search . '%');
            })
            ->select(
                'user_sidebar_accesses.role_id',
                'roles.role_name as role_name',
                'roles.id as role_id',
                'roles.status as role_status',
                'user_sidebar_accesses.sidebar_id',
                'user_sidebar_accesses.sub_sidebar_id',
                'user_sidebar_accesses.status',
                'user_sidebar_accesses.is_checked',
                'user_sidebar_accesses.type',
            )
            ->orderBy('roles.id', 'desc')
            ->paginate(50)
            ->withPath(route('pms.rolepermission.list')); // Ensure you update this to your actual route

        // Group the data by role name
        $grouped = collect($results->items())->groupBy('role_name');
        
        // Format the data
        $formattedData = [];
        foreach ($grouped as $roleName => $accesses) {
            $subSidebarData = [];

            foreach ($accesses as $access) {
                $subSidebarData[] = [
                    'sidebar_id' => $access->sidebar_id,
                    'sub_sidebar_id' => $access->sub_sidebar_id,
                    'is_checked' => $access->is_checked,
                    'type' => $access->type,
                    'status' => $access->status,
                ];
            }

            $formattedData[] = [
                'role_id' => $accesses[0]->role_id,
                'role_name' => $roleName,
                'sidebar_id' => $accesses[0]->sidebar_id ?? null,
                'sub_sidebars' => $subSidebarData,
                'status' => $accesses[0]->status,
                'role_status' => $accesses[0]->role_status,
            ];
        }

        // Return the Blade view with data
        return view('pms.master.rolepermission.list',compact('formattedData','results','search'));
    }
    public function form($id=null){
        $roles =  Role::where('status',1)->get();
        $query = Sidebar::with('subSidebars'); 
        $items = $query->where('sidebars.status',1)->get()->toArray();


        $roles = Role::where('status', 1)
                ->where('role_name', '!=', 'Owner')
                ->get();

        $sidebarItems = Sidebar::with('subSidebars')
                    ->where('status', 1)
                    ->get()
                    ->toArray();

        $raw_access = DB::table('user_sidebar_accesses')
            ->join('roles', 'user_sidebar_accesses.role_id', '=', 'roles.id')
            ->where('roles.role_name', '!=', 'Owner')
            ->where('roles.id', $id)
            ->select(
                'user_sidebar_accesses.role_id',
                'roles.role_name as role_name',
                'roles.id as role_id',
                'roles.status as role_status',
                'user_sidebar_accesses.sidebar_id',
                'user_sidebar_accesses.sub_sidebar_id',
                'user_sidebar_accesses.status',
                'user_sidebar_accesses.is_checked',
                'user_sidebar_accesses.type',
            )
            ->get()
            ->groupBy('role_name');

        $formattedData = [];

        foreach ($raw_access as $roleName => $accesses) {
            $subSidebarData = [];

            foreach ($accesses as $access) {
                $subSidebarData[] = [
                    'sidebar_id'     => $access->sidebar_id,
                    'sub_sidebar_id' => $access->sub_sidebar_id,
                    'is_checked'     => $access->is_checked,
                    'type'           => $access->type,
                    'status'         => $access->status,
                ];
            }

            $formattedData[] = [
                'role_id'     => $accesses[0]->role_id,
                'role_name'   => $roleName,
                'sidebar_id'  => $accesses[0]->sidebar_id ?? null,
                'sub_sidebars' => $subSidebarData,
                'status'      => $accesses[0]->status,
                'role_status' => $accesses[0]->role_status,
            ];
        }

        $role_id = $formattedData[0]['role_id'] ?? '';


        return view('pms.master.rolepermission.form',compact('items','roles','sidebarItems','formattedData','role_id'));
    }

    public function save(Request $request)
    {
        $role_id = $request->role_id;

        UserSidebarAccess::where('role_id', $role_id)->delete();

        foreach ($request->all() as $key => $value) {
            if (str_starts_with($key, 'cbk')) {
             
                $sidebar_id = str_replace('cbk', '', $key);

                UserSidebarAccess::create([
                    'role_id'        => $role_id,
                    'sidebar_id'     => $sidebar_id,
                    'sub_sidebar_id' => null,
                    'is_checked'     => true,
                    'type'           => 'sidebar',
                ]);
            }

            if (str_starts_with($key, 'subcbk')) {
                $sub_sidebar_id = str_replace('subcbk', '', $key);
                $sidebar_id = $request->input("sub_parent_map.$sub_sidebar_id"); // Get mapped parent

                UserSidebarAccess::create([
                    'role_id'        => $role_id,
                    'sidebar_id'     => $sidebar_id,
                    'sub_sidebar_id' => $sub_sidebar_id,
                    'is_checked'     => true,
                    'type'           => 'subsidebar',
                ]);
            }
        }

        return redirect()->route('pms.rolepermission.list')->with('success', 'Permissions saved successfully.');
    }

    public function toggleStatus($id){   
        try {
            $role = Role::findOrFail($id);
            Role::where('id',$id)->update(['status'=>!$role->status]);
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
            UserSidebarAccess::where('role_id',$id)->delete();
            return response()->json([
                'status' => true,
                'data' => '',
                'message' => 'Permissions deleted successfully'
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

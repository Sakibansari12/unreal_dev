<?php

namespace App\Http\Controllers\PMS\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TblCollection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use App\helper\MasterHelper;
use Illuminate\Support\Facades\DB;

class CollectionController extends Controller
{   

    public function index(Request $request){
        $query = TblCollection::query();

        if ($request->filled('search')) {
            $query->where('collection_name', 'like', '%' . $request->search . '%');
        }
        $items = $query->orderby('id','desc')->paginate(20)->withQueryString();
        return view('pms.master.collection.list',compact('items'));
    }

    public function form($id=null){
        $detail = TblCollection::where('id',$id)->first();
        return view('pms.master.collection.form',compact('detail'));
    }

    public function save(Request $request){
        
        $rules = [
            'collection_name' => 'required',
           // 'collection_description' => 'required',
        ];

        if ($request->has('id') && $request->id) {
            // Update Mode
            if ($request->hasFile('image') || $request->input('remove_image') == 1) {
                $rules['image'] = 'required|image|max:3072';
            }
        } else {
            // Add Mode
            $rules['image'] = 'required|image|max:3072';
        }

        $request->validate($rules);


        $checkDuplicate = TblCollection::where('collection_name', '=', $request->collection_name);
        if ($request->id) {
            $checkDuplicate->where('id', '!=', $request->id);
        }
        $checkDuplicate = $checkDuplicate->first();
        
        if ($checkDuplicate) {
            return redirect()->back()
            ->withErrors(['collection_name' => 'The collection name is already in use.'])
            ->withInput();
        }

        $data = [
            'collection_name' => $request->input('collection_name'),
            'collection_description' => $request->input('collection_description'),
            'slug_name' => Str::of($request->input('collection_name'))->slug('-'),
            'status' => 1,
        ];

        if ($request->hasFile('image')) {
            $filePath = $request->file('image')->store('collection', 'public');
            $data['image'] = basename($filePath);
        }

        // $amenity = TblCollection::updateOrCreate(
        //     ['id' => $request->input('id')], 
        //     $data 
        // );

        if ($request->has('id') && $request->id) {
            // Update Mode
            TblCollection::where('id', $request->id)->update($data);
        } else {
            // Generate custom ID manually (latest + 1)
            $maxId = TblCollection::max('id');
            $data['id'] = $maxId ? $maxId + 1 : 1;
    
            DB::table('tbl_collection')->insert($data);
        }

        return redirect()->route('pms.collection.list')->with('success', $request->id ? 'Collection updated successfully!' : 'Collection added successfully!');
    }

    public function toggleStatus($id){   
        try {
            $detail = TblCollection::where(['id'=>$id])->first();
            TblCollection::where(['id'=>$id])->update(['status'=>!$detail->status]);
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
            TblCollection::where('id', $id)->delete();
            return response()->json([
                'status' => true,
                'data' => '',
                'message' => 'Collection deleted successfully'
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
                'message' => 'No collection selected for deletion.'
            ], 400);
        }

        try {
            TblCollection::whereIn('id', $ids)->delete();
            return response()->json([
                'status' => true,
                'message' => 'Collection(s) deleted successfully.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Internal Error',
                'error' => $e->getMessage()
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
                TblCollection::where('id', $id)->update(['position' => $index]);
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
    public function ShowHomecollectionStatus($id){   
        try {
            $detail = TblCollection::where(['id'=>$id])->first();
            TblCollection::where(['id'=>$id])->update(['show_on_collection_page'=>!$detail->show_on_collection_page]);
            return response()->json([
                'status' => true,
                'data' => '',
                'message' => 'Show on home page updated successfully'
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
}

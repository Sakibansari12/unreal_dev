<?php

namespace App\Http\Controllers\PMS\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TblBlog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use App\helper\MasterHelper;
use Illuminate\Support\Facades\DB;

class BlogController extends Controller
{   

    public function index(Request $request)
    {
        $query = TblBlog::query();

        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        $items = $query->orderBy('position', 'asc')->orderBy('id', 'desc')->paginate(20)->withQueryString();
        return view('pms.master.blog.list', compact('items'));
    }

    public function form($id = null)
    {
        $detail = $id ? TblBlog::where('id', $id)->first() : null;
        return view('pms.master.blog.form', compact('detail'));
    }

    public function save(Request $request)
    {
        $rules = [
            'title' => 'required',
            'blog_description' => 'required',
            'date' => 'required|date_format:d-m-Y',
            'facebook_url' => 'nullable|url',
            'linkedin_url' => 'nullable|url',
            'position' => 'nullable|integer|min:0',
            'status' => 'nullable|boolean',
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

        $data = [
            'title' => $request->input('title'),
             'slug' => Str::of($request->input('title'))->slug('-') . '-' . time(),
            'description' => $request->input('blog_description'),
            'date' => $request->input('date'),
            // 'facebook_url' => $request->input('facebook_url'),
            // 'linkedin_url' => $request->input('linkedin_url'),
            // 'position' => $request->input('position', 0),
            // 'status' => $request->input('status', 0),
        ];

        if ($request->hasFile('image')) {
            $filePath = $request->file('image')->store('blog', 'public');
            $data['image'] = basename($filePath);
        } elseif ($request->input('remove_image') == 1) {
            $data['image'] = null;
        }

        if ($request->has('id') && $request->id) {
            // Update Mode
            $data['update_ip'] = $request->ip();
            $data['update_by'] = Auth::guard('admin')->user()->name;
            TblBlog::where('id', $request->id)->update($data);
        } else {
            // Add Mode
            $data['add_ip'] = $request->ip();
            $data['add_by'] = Auth::guard('admin')->user()->name;
            $maxId = TblBlog::max('id');
            // $data['id'] = $maxId ? $maxId + 1 : 1;

            DB::table('tbl_blogs')->insert($data);
        }

        return redirect()->route('pms.blog.list')->with('success', $request->id ? 'Blog updated successfully!' : 'Blog added successfully!');
    }

    public function toggleStatus($id){   
        try {
            $detail = TblBlog::where(['id'=>$id])->first();
            TblBlog::where(['id'=>$id])->update(['status'=>!$detail->status]);
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
            TblBlog::where('id', $id)->delete();
            return response()->json([
                'status' => true,
                'data' => '',
                'message' => 'Blog deleted successfully'
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
                'message' => 'Blogs selected for deletion.'
            ], 400);
        }

        try {
            TblBlog::whereIn('id', $ids)->delete();
            return response()->json([
                'status' => true,
                'message' => 'Blog deleted successfully.'
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
                TblBlog::where('id', $id)->update(['position' => $index]);
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
            $detail = TblBlog::where(['id'=>$id])->first();
            TblBlog::where(['id'=>$id])->update(['show_on_collection_page'=>!$detail->show_on_collection_page]);
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

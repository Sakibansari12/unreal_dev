<?php

namespace App\Http\Controllers\PMS\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TblHomeBanner;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use App\helper\MasterHelper;
use Illuminate\Support\Facades\DB;

class BannerSlideController extends Controller
{   

    public function index(Request $request){
        $query = TblHomeBanner::query();

        if ($request->filled('search_heading')) {
            $query->where('heading', 'like', '%' . $request->search_heading . '%');
        }
        $items = $query->orderby('position','asc')->paginate(50)->withQueryString();
        return view('pms.master.bannerslide.list',compact('items'));
    }

    public function form($id=null){
        $detail = TblHomeBanner::where('id',$id)->first();
        return view('pms.master.bannerslide.form',compact('detail'));
    }

   public function save(Request $request){
        
        $rules = [
            
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

        if ($request->has('id') && $request->id) {
            // Update Mode
            if ($request->hasFile('mobile_image') || $request->input('mobile_remove_image') == 1) {
                $rules['mobile_image'] = 'required|image|max:3072';
            }
        } else {
            // Add Mode
            $rules['mobile_image'] = 'required|image|max:3072';
        }

        $request->validate($rules);


       
        $data = [
            'heading' => $request->input('banner_title'),
            'button_name' => $request->input('button_name'),
            'link' => $request->input('link'),
            'status' => 1,
        ];

        if ($request->hasFile('image')) {
            $filePath = $request->file('image')->store('home_banner', 'public');
            $data['image'] = basename($filePath);
        }
        if ($request->hasFile('mobile_image')) {
            $filePath = $request->file('mobile_image')->store('home_banner', 'public');
            $data['mobile_image'] = basename($filePath);
        }
        if ($request->has('id') && $request->id) {
            TblHomeBanner::where('id', $request->id)->update($data);
        } else {
            $maxId = TblHomeBanner::max('id');
            $data['id'] = $maxId ? $maxId + 1 : 1;
    
            DB::table('tbl_home_banners')->insert($data);
        }

        return redirect()->route('pms.bannerslide.list')->with('success', $request->id ? 'Hero Slide updated successfully!' : 'Hero Slide added successfully!');
    }
    
    /* public function form(Request $request)
    {
        $banner = TblHomeBanner::orderBy('position', 'asc')->get(); // Order by position
        $images = [];
        foreach ($banner as $item) {
            if ($item->image) {
                $images[] = [
                    'base64_image' => asset('storage/home_banner/' . $item->image),
                    'id' => $item->id,
                    'position' => $item->position
                ];
            }
        }

        return view('pms.master.bannerslide.form', compact('images', 'banner'));
    } */
    /* public function save(Request $request)
    {
        
        
        $deletedIds = $request->deleted_image_ids ?? [];
        $existingCount = TblHomeBanner::whereNotIn('id', $deletedIds)->count();
        $newImages = $request->file('images');
    
        if ($existingCount == 0 && empty($newImages)) {
            return back()->withErrors(['images' => 'At least one image is required.'])->withInput();
        }
        
        if ($request->has('deleted_image_ids')) {
            foreach ($request->deleted_image_ids as $id) {
                $banner = TblHomeBanner::find($id);
                if ($banner) {
                    $filePath = 'home_banner/' . $banner->image;
                    if (Storage::disk('public')->exists($filePath)) {
                        Storage::disk('public')->delete($filePath);
                    }
                    $banner->delete();
                }
            }
        }
        if ($request->hasFile('images')) {
            $maxPosition = TblHomeBanner::max('position') ?? 0;
            foreach ($request->file('images') as $index => $image) {
                if ($image->isValid()) {
                    $filename = uniqid() . '.' . $image->getClientOriginalExtension();
                    $image->storeAs('public/home_banner', $filename);

                    TblHomeBanner::create([
                        'image' => $filename,
                        'heading' => 'Default Heading',
                        'subtitle' => 'Default Subtitle',
                        'status' => 1,
                        'position' => $maxPosition + $index + 1,
                        'add_ip' => $request->ip(),
                        'add_by' => Auth::guard('admin')->user()->name ?? 'System',
                    ]);
                }
            }
        }
        if ($request->has('positions')) {
            foreach ($request->positions as $id => $position) {
                $banner = TblHomeBanner::find($id);
                if ($banner) {
                    $banner->update(['position' => $position]);
                }
            }
        }

        return back()->with('success', 'Images uploaded successfully!');
    } */

    public function toggleStatus($id){   
        try {
            $detail = TblHomeBanner::where(['id'=>$id])->first();
            TblHomeBanner::where(['id'=>$id])->update(['status'=>!$detail->status]);
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
            TblHomeBanner::where('id', $id)->delete();
            return response()->json([
                'status' => true,
                'data' => '',
                'message' => 'Banner slide deleted successfully'
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
                'message' => 'No banner slides selected for deletion.'
            ], 400);
        }

        try {
            TblHomeBanner::whereIn('id', $ids)->delete();
            return response()->json([
                'status' => true,
                'message' => 'Banner slide(s) deleted successfully.'
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
                TblHomeBanner::where('id', $id)->update(['position' => $index]);
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
}
<?php

namespace App\Http\Controllers\PMS\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Team;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;



class TeamConroller extends Controller
{
   
  

    public function index(Request $request)
    {

        $items = Team::query()
          ->orderBy('position', 'asc')->orderBy('id', 'desc')
            ->paginate(50);
        return view('pms.master.team.list', compact('items'));
    }
    


    public function form($id=null){
        $detail = Team::where('id',$id)->orderBy('id', 'desc')->first();
        return view('pms.master.team.form',compact('detail'));
    }

    public function save(Request $request)
{
    // dd($request->all());
    $request->validate([
        'name' => 'required|max:255',
        'designation' => 'required',
        'image' => $request->id ? 'nullable|image' : 'required|image',
    ]);
// dd($request->all());
    try {
        // Get existing item if updating
        $item = $request->id ? Team::find($request->id) : new Team();

        $item->name = $request->name;
        $item->designation = $request->designation;
        $item->description = $request->description;

        // Image handling
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = 'team_image_' . time() . '_' . Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) . '.' . $file->getClientOriginalExtension();

            // Delete old image if exists
            if (!empty($item->image) && Storage::disk('public')->exists($item->image)) {
                Storage::disk('public')->delete($item->image);
            }

            // Store new image
            $file->storeAs('team/images', $filename, 'public');
            $item->image = 'team/images/' . $filename;
        }

        $item->save();

        $message = $request->id ? 'Team updated successfully!' : 'Team added successfully!';
        return redirect()->route('pms.team.list')->with('success', $message);
    } catch (\Exception $e) {
        return back()->withInput()->with('error', 'Something went wrong: ' . $e->getMessage());
    }
}
    

    public function toggleStatus($id){   
        try {
            $detail = Team::where(['id'=>$id])->first();
            Team::where(['id'=>$id])->update(['status'=>!$detail->status]);
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
            Team::where('id', $id)->delete();
            return response()->json([
                'status' => true,
                'data' => '',
                'message' => 'Team member deleted successfully.'
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
                'message' => 'No Team member selected for deletion.'
            ], 400);
        }

        try {
            Team::whereIn('id', $ids)->delete();
            return response()->json([
                'status' => true,
                'message' => 'Team member deleted successfully.'
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
            $positions = $request->position;
            if (!is_array($positions)) {
                throw new \Exception('$positions must be an array.');
            }
            foreach ($positions as $index => $id) {
                Team::where('id', $id)->update(['position' => $index]);
            }
            return response()->json([
                'status' => true,
                'message' => 'Successfully Updated.'
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
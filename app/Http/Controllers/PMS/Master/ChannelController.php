<?php

namespace App\Http\Controllers\PMS\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TblSpecialInvitation;
use App\Models\TblReviewImages;
use App\Models\DiscountCoupon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class ChannelController extends Controller
{
    public function index(Request $request){
        $query = TblReviewImages::query();

        if ($request->filled('search')) {
            $query->where('review_name', 'like', '%' . $request->search . '%');
        }

        $specialInvitations = $query->orderBy('id', 'desc')->paginate(20)->withQueryString();
        //dd($specialInvitations);
        return view('pms.master.channel.list', compact('specialInvitations'));
    }

    public function form($id = null){
        $detail = $id ? TblReviewImages::find($id) : null;
        $homeTypeData = DiscountCoupon::where('status', 1)->get();

        return view('pms.master.channel.form', compact('detail', 'homeTypeData'));
    }

    public function save(Request $request){
        $rules = [
            'name' => 'required',
        ];

        // Image validation logic
        if ($request->has('id') && $request->id) {
            if ($request->hasFile('image') || $request->input('remove_image') == 1) {
                $rules['image'] = 'required|image|mimes:jpg,jpeg,webp,svg,png|max:3072';
            }
        } else {
            $rules['image'] = 'required|image|mimes:jpg,jpeg,webp,svg,png|max:3072';
        }

        $validated = $request->validate($rules);

        $imageName = null;

        // Handle image upload
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $image->storeAs('public/channel', $imageName);
        } elseif ($request->input('remove_image') == 1) {
            $imageName = null;
        }

        $data = [
            'review_name' => $request->input('name'),
        ];

        // Assign image if available
        if ($imageName !== null) {
            $data['review_image'] = $imageName;
        }

        if ($request->has('id') && $request->id) {
            // Update mode
            //$data['update_ip'] = $request->ip();
         //   $data['update_by'] = Auth::guard('admin')->user()->name;

            $existing = TblReviewImages::find($request->id);
            if ($existing) {
                // If image is to be removed
                if ($request->input('remove_image') == 1 && $existing->image) {
                    Storage::delete('public/channel/' . $existing->image);
                }
                // Keep previous image if no new image uploaded and not removed
                if (!$request->hasFile('image') && !$request->input('remove_image')) {
                    $data['review_image'] = $existing->image;
                }

                $existing->update($data);
            } else {
                return redirect()->back()->with('error', 'Offer not found.')->withInput();
            }
        } else {
            TblReviewImages::create($data);
        }

        return redirect()->route('pms.channel.list')->with('success', $request->id ? 'channel updated successfully.' : 'Special offer added successfully.');
    }

    public function toggleStatus($id){   
        try {
            $detail = TblReviewImages::where(['id'=>$id])->first();
            TblReviewImages::where(['id'=>$id])->update(['status'=>!$detail->status]);
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
            TblReviewImages::where('id', $id)->delete();
            return response()->json([
                'status' => true,
                'data' => '',
                'message' => 'Channel deleted successfully'
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

    public function multiDelete(Request $request){
        $ids = $request->get('ids');

        if (empty($ids) || !is_array($ids)) {
            return redirect()->route('pms.channel.list')->with('error', 'Invalid or empty IDs provided.');
        }

        $invitations = TblReviewImages::whereIn('id', $ids)->get();

        foreach ($invitations as $invitation) {
            if ($invitation->image) {
                Storage::delete('public/channel/' . $invitation->image);
            }
            $invitation->status = 0;
            $invitation->save();
            $invitation->delete();
        }

        return redirect()->route('pms.channel.list')->with('success', 'Selected channel deleted successfully.');
    }

    public function savePosition(Request $request){
        $positions = $request->position;

        if (!is_array($positions)) {
            return redirect()->route('pms.channel.list')->with('error', 'Invalid position data.');
        }

        foreach ($positions as $index => $id) {
            TblSpecialInvitation::where('id', $id)->update(['position' => $index]);
        }

        return redirect()->route('pms.channel.list')->with('success', 'Positions updated successfully.');
    }

    public function ShowHomeblogStatus(Request $request, $id){
        $invitation = TblSpecialInvitation::find($id);

        if (!$invitation) {
            return redirect()->route('pms.channel.list')->with('error', 'Invitation not found.');
        }

        $validator = Validator::make($request->all(), [
            'status' => 'required|boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->route('pms.channel.list')->with('error', 'Invalid status value.');
        }

        $invitation->status = $request->status;
        $invitation->save();

        return redirect()->route('pms.channel.list')->with('success', 'Home status updated successfully.');
    }
}
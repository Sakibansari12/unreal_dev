<?php

namespace App\Http\Controllers\PMS\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TblSpecialInvitation;
use App\Models\DiscountCoupon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class SpecialOfferController extends Controller
{
    public function index(Request $request){
        $query = TblSpecialInvitation::query();

        if ($request->filled('search')) {
            $query->where('offer_name', 'like', '%' . $request->search . '%');
        }

        $specialInvitations = $query->orderBy('position', 'asc')->orderBy('id', 'desc')->paginate(20)->withQueryString();
        return view('pms.master.specialOffer.list', compact('specialInvitations'));
    }

    public function form($id = null){
        $detail = $id ? TblSpecialInvitation::find($id) : null;
        $homeTypeData = DiscountCoupon::where('status', 1)->get();

        return view('pms.master.specialOffer.form', compact('detail', 'homeTypeData'));
    }

    public function save(Request $request){
        $rules = [
            'offer_name' => 'required',
            'couponcode_id' => 'required|exists:discount_coupons,id',
            'validity' => 'required|date_format:d/m/Y|after_or_equal:today',
            'headline' => 'required',
            'description' => 'required|max:255',
        ];

        // Image validation logic
        if ($request->has('id') && $request->id) {
            if ($request->hasFile('image') || $request->input('remove_image') == 1) {
                $rules['image'] = 'required|image|mimes:jpeg,png,jpg|max:3072';
            }
        } else {
            $rules['image'] = 'required|image|mimes:jpeg,png,jpg|max:3072';
        }

        $validated = $request->validate($rules);

        $imageName = null;

        // Handle image upload
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $image->storeAs('public/specialoffer', $imageName);
        } elseif ($request->input('remove_image') == 1) {
            $imageName = null;
        }

        $data = [
            'offer_name' => $request->input('offer_name'),
            'couponcode_id' => $request->input('couponcode_id'),
            'validity' => \Carbon\Carbon::createFromFormat('d/m/Y', $request->input('validity'))->format('Y-m-d'),
            'headline' => $request->input('headline'),
            'description' => $request->input('description'),
        ];

        // Assign image if available
        if ($imageName !== null) {
            $data['image'] = $imageName;
        }

        if ($request->has('id') && $request->id) {
            // Update mode
            $data['update_ip'] = $request->ip();
            $data['update_by'] = Auth::guard('admin')->user()->name;

            $existing = TblSpecialInvitation::find($request->id);
            if ($existing) {
                // If image is to be removed
                if ($request->input('remove_image') == 1 && $existing->image) {
                    Storage::delete('public/specialoffer/' . $existing->image);
                }
                // Keep previous image if no new image uploaded and not removed
                if (!$request->hasFile('image') && !$request->input('remove_image')) {
                    $data['image'] = $existing->image;
                }

                $existing->update($data);
            } else {
                return redirect()->back()->with('error', 'Offer not found.')->withInput();
            }
        } else {
            // Add mode
            $data['position'] = TblSpecialInvitation::max('position') !== null ? TblSpecialInvitation::max('position') + 1 : 0;
            $data['status'] = 1;
            $data['add_ip'] = $request->ip();
            $data['add_by'] = Auth::guard('admin')->user()->name;

            TblSpecialInvitation::create($data);
        }

        return redirect()->route('pms.specialoffer.list')->with('success', $request->id ? 'Special offer updated successfully.' : 'Special offer added successfully.');
    }

    public function toggleStatus($id){   
        try {
            $detail = TblSpecialInvitation::where(['id'=>$id])->first();
            TblSpecialInvitation::where(['id'=>$id])->update(['status'=>!$detail->status]);
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
            TblSpecialInvitation::where('id', $id)->delete();
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

    public function multiDelete(Request $request){
        $ids = $request->get('ids');

        if (empty($ids) || !is_array($ids)) {
            return redirect()->route('pms.specialoffer.list')->with('error', 'Invalid or empty IDs provided.');
        }

        $invitations = TblSpecialInvitation::whereIn('id', $ids)->get();

        foreach ($invitations as $invitation) {
            if ($invitation->image) {
                Storage::delete('public/specialoffer/' . $invitation->image);
            }
            $invitation->status = 0;
            $invitation->save();
            $invitation->delete();
        }

        return redirect()->route('pms.specialoffer.list')->with('success', 'Selected special offers deleted successfully.');
    }

    public function savePosition(Request $request){
        $positions = $request->position;

        if (!is_array($positions)) {
            return redirect()->route('pms.specialoffer.list')->with('error', 'Invalid position data.');
        }

        foreach ($positions as $index => $id) {
            TblSpecialInvitation::where('id', $id)->update(['position' => $index]);
        }

        return redirect()->route('pms.specialoffer.list')->with('success', 'Positions updated successfully.');
    }

    public function ShowHomeblogStatus(Request $request, $id){
        $invitation = TblSpecialInvitation::find($id);

        if (!$invitation) {
            return redirect()->route('pms.specialoffer.list')->with('error', 'Invitation not found.');
        }

        $validator = Validator::make($request->all(), [
            'status' => 'required|boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->route('pms.specialoffer.list')->with('error', 'Invalid status value.');
        }

        $invitation->status = $request->status;
        $invitation->save();

        return redirect()->route('pms.specialoffer.list')->with('success', 'Home status updated successfully.');
    }
}
<?php

namespace App\Http\Controllers\PMS\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TblPrivacyPolicy;

class PrivacyPolicyController extends Controller
{
    public function form($id=null){
        $detail = TblPrivacyPolicy::first();
        return view('pms.master.privacy-policy',compact('detail'));
    }

    public function save(Request $request){

        $request->validate([
            'title'=>'required',
            'cancellation_policy'=>'required'
        ]);

        $item = TblPrivacyPolicy::firstOrNew(['id' => $request->id]);

        $item->title = $request->title;
        $item->cancellation_policy = $request->cancellation_policy;
        $item->save();

        return redirect()->route('pms.privacy.policy.form')
                ->with('success', $request->id ? 'Privacy Policy updated successfully.' : 'Privacy policy saved successfully.');
    }
}

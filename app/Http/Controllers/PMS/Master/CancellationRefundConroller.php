<?php

namespace App\Http\Controllers\PMS\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CancellationPolicy;  

class CancellationRefundConroller extends Controller
{
    public function form($id=null)
{
    $detail = CancellationPolicy::first();
    return view('pms.master.cancellation_and_refund', compact('detail'));
}


    public function save(Request $request){

        $request->validate([
            'title'=>'required',
            'cancellation_policy'=>'required'
        ]);

        $item = CancellationPolicy::firstOrNew(['id' => $request->id]);

        $item->title = $request->title;
        $item->cancellation_policy = $request->cancellation_policy;
        $item->save();

        return redirect()->route('pms.refund.policy.form')
                ->with('success', $request->id ? 'Cancellation and Refund policy updated successfully.' : 'Cancellation and Refund policy saved successfully.');
    }
}

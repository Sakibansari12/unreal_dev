<?php

namespace App\Http\Controllers\PMS\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TblSitesetting;

class SettingController extends Controller
{
    public function setGST(){
        $items = TblSitesetting::first(); 
        return view('pms.master.gst-setting',compact('items'));
    }

    public function toggleStatus($id)
    {   
        try {
            $detail = TblSitesetting::where(['id'=>$id])->first();
            TblSitesetting::where(['id'=>$id])->update(['is_allow_gst'=>!$detail->is_allow_gst]);
            return response()->json([
                'status' => true,
                'data' => '',
                'message' => 'The GST setting has been changed'
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

    public function websiteMarkup(){
        $items = TblSitesetting::where('id',1)->first(); 
        return view('pms.master.website-markup',compact('items'));
    }

    public function WebsiteMarkupSave(Request $request)
    {   
        $request->validate([
            'website_markup' => 'required',
        ]);

        try {
            TblSitesetting::where('id',$request->id)->update([
                'website_markup' => $request->website_markup,
            ]);

            return redirect()->route('pms.website.markup.list')
                ->with('success', $request->id ? 'Website markup updated successfully.' : 'Website markup saved successfully.');

        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Something went wrong: ' . $e->getMessage());
        }
    }
}

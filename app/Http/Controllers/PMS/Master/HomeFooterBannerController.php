<?php

namespace App\Http\Controllers\PMS\Master;

use App\Http\Controllers\Controller;
use App\Models\HomeFooterBanner;
use Illuminate\Http\Request;

class HomeFooterBannerController extends Controller
{
    public function edit()
    {
        $banner = HomeFooterBanner::first(); 
        return view('pms.master.footer_banner.edit', compact('banner'));
    }

    public function save(Request $request)
    {
        $request->validate([
            'title' => 'required|string',
           // 'sub_title' => 'required|string',
           // 'list_content.*' => 'required|string'
        ]);

        $banner = HomeFooterBanner::firstOrNew(['id' => $request->id ?? null]);
        $banner->title = $request->title;
        ///$banner->sub_title = $request->sub_title;
      //  $banner->list_content = json_encode($request->list_content);
        $banner->save();

        return redirect()->back()->with('success', 'Footer content saved successfully!');
    }
}
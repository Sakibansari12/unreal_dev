<?php

namespace App\Http\Controllers\PMS\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request; 
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Models\Service; // Assuming you have a Service model for the service table
class ServiceController extends Controller
{
    public function form($id=null)
{
    $detail = Service::first();
    return view('pms.master.service.form', compact('detail'));
}


   public function save(Request $request)
{
    // dd($request->all());
    // Validate required fields
    $request->validate([
        'customer_service_title' => 'required|string|max:255',
        'customer_service_short_description' => 'required',
        'privacy_flexibility_title' => 'required|string|max:255',
        'privacy_flexibility_short_description' => 'required',
        'professionally_managed_title' => 'required',
        'professionally_managed_description' => 'required',
        'best_feature_title' => 'required',
        'best_feature_description' => 'required',
        'customer_service_icon' => $request->id ? 'nullable|image|mimes:jpg,jpeg,png,webp' : 'required|image|mimes:jpg,jpeg,png,webp',
        'privacy_flexibility_icon' => $request->id ? 'nullable|image|mimes:jpg,jpeg,png,webp' : 'required|image|mimes:jpg,jpeg,png,webp',
        'privacy_flexibility_icon' => $request->id ? 'nullable|image|mimes:jpg,jpeg,png,webp' : 'required|image|mimes:jpg,jpeg,png,webp',
        'professionally_managed_icon' => $request->id ? 'nullable|image|mimes:jpg,jpeg,png,webp' : 'required|image|mimes:jpg,jpeg,png,webp',
        'best_feature_icon' => $request->id ? 'nullable|image|mimes:jpg,jpeg,png,webp' : 'required|image|mimes:jpg,jpeg,png,webp',

    ]);

    // Get or create model
    // dd($request->all());
    $item = Service::firstOrNew(['id' => $request->id]);

    // Assign basic fields
    $item->customer_service_title = $request->customer_service_title;
    $item->customer_service_short_description = $request->customer_service_short_description;
    $item->privacy_flexibility_title = $request->privacy_flexibility_title;
    $item->privacy_flexibility_short_description = $request->privacy_flexibility_short_description;
    $item->professionally_managed_title = $request->professionally_managed_title;
    $item->professionally_managed_description = $request->professionally_managed_description;
    $item->best_feature_title = $request->best_feature_title;
    $item->best_feature_description = $request->best_feature_description;


     if ($request->hasFile('image')) {
        $file = $request->file('image');
        $filename = 'service_image_' . time() . '_' . Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) . '.' . $file->getClientOriginalExtension();

   
        if ($item->image && Storage::disk('public')->exists($item->image)) {
            Storage::disk('public')->delete($item->image);
        }

        $file->storeAs('service/images', $filename, 'public');
        $item->image = 'service/images/' . $filename;
    }

 
    if ($request->hasFile('customer_service_icon')) {
        $file = $request->file('customer_service_icon');
        $filename = 'customer_service_icon_' . time() . '_' . Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) . '.' . $file->getClientOriginalExtension();

   
        if ($item->customer_service_icon && Storage::disk('public')->exists($item->customer_service_icon)) {
            Storage::disk('public')->delete($item->customer_service_icon);
        }

        $file->storeAs('service/images', $filename, 'public');
        $item->customer_service_icon = 'service/images/' . $filename;
    }


    if ($request->hasFile('privacy_flexibility_icon')) {
        $file = $request->file('privacy_flexibility_icon');
        $filename = 'privacy_flexibility_icon_' . time() . '_' . Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) . '.' . $file->getClientOriginalExtension();

        if ($item->privacy_flexibility_icon && Storage::disk('public')->exists($item->privacy_flexibility_icon)) {
            Storage::disk('public')->delete($item->privacy_flexibility_icon);
        }

        $file->storeAs('service/icons', $filename, 'public');
        $item->privacy_flexibility_icon = 'service/icons/' . $filename;
    }

   
    if ($request->hasFile('professionally_managed_icon')) {
        $file = $request->file('professionally_managed_icon');
        $filename = 'professionally_managed_icon_' . time() . '_' . Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) . '.' . $file->getClientOriginalExtension();

        if ($item->professionally_managed_icon && Storage::disk('public')->exists($item->professionally_managed_icon)) {
            Storage::disk('public')->delete($item->professionally_managed_icon);
        }

        $file->storeAs('service/icons', $filename, 'public');
        $item->professionally_managed_icon = 'service/icons/' . $filename;
    }
  if ($request->hasFile('best_feature_icon')) {
        $file = $request->file('best_feature_icon');
        $filename = 'best_feature_icon_' . time() . '_' . Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) . '.' . $file->getClientOriginalExtension();

        if ($item->best_feature_icon && Storage::disk('public')->exists($item->best_feature_icon)) {
            Storage::disk('public')->delete($item->best_feature_icon);
        }

        $file->storeAs('service/icons', $filename, 'public');
        $item->best_feature_icon = 'service/icons/' . $filename;
    }
    
    $item->save();

    return redirect()->route('pms.service.form')
        ->with('success', $request->id ? 'Service updated successfully.' : 'Service saved successfully.');
}
}

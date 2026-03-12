<?php

namespace App\Http\Controllers\PMS\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TblAboutUs;
use App\Models\TblAboutInformation;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class AboutusConroller extends Controller
{
    /* =========================
       FORM VIEW
    ========================= */
    public function form()
    {
        $data = TblAboutUs::with('aboutInformation')->first();

        if (!$data) {
            $data = new TblAboutUs();
            $data->aboutInformation = collect();
        }

        $data->banner_path = $data->banner
            ? asset('storage/about_us/' . $data->banner)
            : '';

        foreach ($data->aboutInformation as $row) {
            $row->image_path = $row->image
                ? asset('storage/about_information/' . $row->image)
                : '';
        }

        return view('pms.master.aboutus.form', compact('data'));
    }

    /* =========================
       API VIEW
    ========================= */
    public function index()
    {
        $data = TblAboutUs::with('aboutInformation')->first();

        if (!$data) {
            return response()->json([
                'status' => false,
                'message' => 'No data found'
            ], 404);
        }

        $data->banner_path = $data->banner
            ? "/storage/about_us/" . $data->banner
            : '';

        foreach ($data->aboutInformation as $row) {
            $row->image_path = $row->image
                ? "/storage/about_information/" . $row->image
                : '';
        }

        return response()->json([
            'status' => true,
            'data' => $data
        ]);
    }

    /* =========================
       STORE / UPDATE
    ========================= */
    public function store(Request $request)
    {
        $aboutUs = TblAboutUs::first() ?? new TblAboutUs();

        $validator = Validator::make($request->all(), [
            'banner_image' => $aboutUs->exists
                ? 'nullable|image|mimes:jpg,jpeg,png,webp'
                : 'required|image|mimes:jpg,jpeg,png,webp',

            'banner_text' => 'nullable|string|max:255',
            'about_text' => 'required|string',
            // 'about_service_text' => 'required|string',

            // 'services' => 'required|array|min:1',
            // 'services.*.title' => 'required|string|max:255',
            // 'services.*.image' => 'nullable|image|mimes:jpg,jpeg,png,webp',
            // 'services.*.content' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }
        // dd($request->hasFile('banner_image'));
        /* ---------- Banner Image ---------- */
        if ($request->hasFile('banner_image')) {
            $file = $request->file('banner_image');
            $filename = 'banner_' . time() . '.' . $file->getClientOriginalExtension();
            $file->storeAs('about_us', $filename, 'public');
            $aboutUs->banner = $filename;
        }

        /* ---------- About Us ---------- */
        $aboutUs->banner_text = $request->banner_text;
        $aboutUs->about_content = $request->about_text;
        // $aboutUs->service_content = $request->about_service_text;
        $aboutUs->status = 1;
        $aboutUs->add_ip = $request->ip();
        $aboutUs->add_by = Auth::user()->name ?? 'system';
        $aboutUs->save();

        /* ---------- Remove Deleted Services ---------- */
        $incomingIds = collect($request->services)->pluck('id')->filter()->toArray();

        TblAboutInformation::where('about_id', $aboutUs->id)
            ->whereNotIn('id', $incomingIds)
            ->delete();

        // /* ---------- Save Services ---------- */
        // foreach ($request->services as $service) {

        //     $aboutInfo = isset($service['id'])
        //         ? TblAboutInformation::find($service['id'])
        //         : new TblAboutInformation();

        //     if (!empty($service['image'])) {
        //         $file = $service['image'];
        //         $imgName = 'service_' . time() . rand(100, 999) . '.' . $file->getClientOriginalExtension();
        //         $file->storeAs('about_information', $imgName, 'public');
        //         $aboutInfo->image = $imgName;
        //     }

        //     $aboutInfo->about_id = $aboutUs->id;
        //     $aboutInfo->title = $service['title'];
        //     $aboutInfo->text = $service['content'] ?? '';
        //     $aboutInfo->status = 1;
        //     $aboutInfo->add_ip = $request->ip();
        //     $aboutInfo->add_by = Auth::user()->name ?? 'system';
        //     $aboutInfo->save();
        // }

        return redirect()->back()->with('success', 'About Us saved successfully');
    }
}

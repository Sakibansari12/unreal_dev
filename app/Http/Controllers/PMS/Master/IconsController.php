<?php

namespace App\Http\Controllers\PMS\Master;

use App\Http\Controllers\Controller;
use App\Models\TblIcon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class IconsController extends Controller
{
    public function index(Request $request)
    {
        $query = TblIcon::query();

        if ($request->filled('search_name')) {
            $query->where('icons_name', 'like', '%' . $request->search_name . '%');
        }

        $items = $query->orderBy('id', 'desc')->paginate(20)->withQueryString();

        return view('pms.master.icons.list', compact('items'));
    }

    public function form($id = null)
    {
        $detail = TblIcon::find($id);
        return view('pms.master.icons.form', compact('detail'));
    }

    public function save(Request $request)
    {
        $rules = [
            'icons_name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('tbl_icons', 'icons_name')->ignore($request->id),
            ],
            'icons_image' => [
                $request->id ? 'nullable' : 'required',
                'file',
                'max:1024',
                'dimensions:width=100,height=100',
            ],
        ];

        if (!$request->id) {
            $rules['icons_image'] = [
                $request->id ? 'nullable' : 'required',
                'file',
                'max:1024',
                'dimensions:width=100,height=100',
            ];
        }

        try {
            $request->validate($rules);

            if (!$request->id && !$request->hasFile('icons_image')) {
                return back()->with('error', 'Icon image is required.')->withInput();
            }
            $icon = TblIcon::find($request->id);
            $data = [
                'icons_name' => $request->icons_name,
                'icon_slug'  => Str::slug($request->icons_name),
                'status'     => $icon?->status ?? 1,
            ];

            if ($request->hasFile('icons_image')) {

                $file = $request->file('icons_image');
                $ext  = strtolower($file->getClientOriginalExtension());

                $allowed = ['svg', 'png', 'jpg', 'jpeg', 'webp'];
                if (!in_array($ext, $allowed)) {
                    return back()
                        ->with('error', 'Invalid file type. Allowed: svg, png, jpg, jpeg, webp')
                        ->withInput();
                }

                // SVG safety check
                if ($ext === 'svg') {
                    $content = file_get_contents($file->getRealPath());
                    if (
                        stripos($content, '<svg') === false ||
                        stripos($content, '<script') !== false ||
                        stripos($content, '<?php') !== false
                    ) {
                        return back()
                            ->with('error', 'Invalid or unsafe SVG file')
                            ->withInput();
                    }
                }

                // ✅ SAVE FILE
                $filename = time() . '_' . uniqid() . '.' . $ext;
                $file->storeAs('icons', $filename, 'public');

                // ✅ SAVE TO DB
                $data['icons_image'] = $filename;
            }

            if ($request->remove_image == 1 && !$request->hasFile('icons_image')) {
                $data['icons_image'] = null;
            }

            TblIcon::updateOrCreate(['id' => $request->id], $data);

            return redirect()
                ->route('pms.icons.list')
                ->with('success', $request->id ? 'Icon updated successfully!' : 'Icon added successfully!');
        } catch (\Illuminate\Validation\ValidationException $e) {

            return back()
                ->with('error', $e->validator->errors()->first())
                ->withInput();
        } catch (\Exception $e) {

            return back()
                ->with('error', 'Something went wrong. Please try again.')
                ->withInput();
        }
    }

    public function toggleStatus($id)
    {
        $icon = TblIcon::findOrFail($id);
        $icon->update(['status' => !$icon->status]);

        return response()->json(['status' => true, 'message' => 'Status updated']);
    }

    public function delete($id)
    {
        TblIcon::where('id', $id)->delete();

        return response()->json(['status' => true, 'message' => 'Icon deleted']);
    }

    public function multiDelete(Request $request)
    {
        TblIcon::whereIn('id', $request->ids)->delete();

        return response()->json(['status' => true, 'message' => 'Icons deleted']);
    }
}

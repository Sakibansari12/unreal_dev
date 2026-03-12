<?php

namespace App\Http\Controllers\PMS\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TblTag;
use Illuminate\Support\Facades\DB;

class TagsController extends Controller
{
    public function index(Request $request)
    {
        try {
            $query = TblTag::query();
            if ($request->filled('search_name')) {
                $query->where('tags_name', 'like', '%' . $request->search_name . '%');
            }
            $query->orderBy('tbl_tags.id', 'desc');
            $items = $query->paginate(20)->withQueryString();
            return view('pms.master.tags.list', compact('items'));
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => "Internal Error",
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function form($id = null)
    {

        $detail = TblTag::where('id', $id)->first();
        return view('pms.master.tags.form', compact('detail'));
    }

    /*  public function save(Request $request)
    {
        $rules = [
            'name'      => 'required',
            'title'     => 'required',
            'sub_title' => 'required',
        ];

        if ($request->has('id') && $request->id) {
            if ($request->hasFile('image') || $request->input('remove_image') == 1) {
                $rules['image'] = 'required|image|max:3072';
            }
        } else {
            $rules['image'] = 'required|image|max:3072';
        }

        $request->validate($rules);




        $request->validate($rules);

        try {
            if ($request->id) {
                $data = TblTag::updateOrCreate(
                    ['id' => $request->id],
                    [
                        'tags_name'   => $request->name,
                        'tag_title'   => $request->title,
                        'tab_sub_title' => $request->sub_title,
                        //'update_ip'   => $request->ip() ?? '0.0.0.0',
                       // 'updated_by'  => auth()->user()->name,
                    ]
                );
            } else {
                $data = TblTag::updateOrCreate(
                    ['id' => $request->id],
                    [
                        'tags_name'   => $request->name,
                        'tag_title'   => $request->title,
                        'tab_sub_title' => $request->sub_title,
                       // 'add_ip'      => $request->ip() ?? '0.0.0.0',
                      //  'add_by'      => auth()->user()->name,
                    ]
                );
            }

            return redirect()->route('pms.tags.list')
                ->with('success', $request->id ? 'Tag updated successfully.' : 'Tag saved successfully.');
        } catch (\Exception $e) {
           // dd($e);
            return back()->withInput()->with('error', 'Something went wrong: ' . $e->getMessage());
        }
    }
 */

    public function save(Request $request)
    {
        $rules = [
            'name'      => 'required|string|max:255',
            'title'     => 'required|string|max:255',
            'sub_title' => 'required|string|max:255',
        ];

        if (!$request->id) {
            $rules['image'] = 'required|max:3072';
        }
        $existingTag = null;
        if ($request->id) {
            $existingTag = TblTag::find($request->id);
        }

        if (
            $request->id &&
            $request->remove_image == 1 &&
            !$request->hasFile('image')
        ) {
            return back()
                ->with('error', 'Image is required. Please upload a new image.')
                ->withInput();
        }
        try {
            $request->validate($rules);

            if (!$request->id && !$request->hasFile('image')) {
                return back()
                    ->with('error', 'Tag image is required.')
                    ->withInput();
            }

            $checkDuplicate = TblTag::where('tags_name', $request->name);
            if ($request->id) {
                $checkDuplicate->where('id', '!=', $request->id);
            }

            if ($checkDuplicate->exists()) {
                return back()
                    ->with('error', 'The tag name is already in use.')
                    ->withInput();
            }

            $data = [
                'tags_name'      => $request->name,
                'tag_title'      => $request->title,
                'tab_sub_title'  => $request->sub_title,
            ];

            if ($request->hasFile('image')) {

                $file = $request->file('image');
                $ext  = strtolower($file->getClientOriginalExtension());

                $allowed = ['svg', 'png', 'jpg', 'jpeg', 'webp'];
                if (!in_array($ext, $allowed)) {
                    return back()
                        ->with('error', 'Invalid image type. Allowed: svg, png, jpg, jpeg, webp')
                        ->withInput();
                }

                if ($ext === 'svg') {
                    $content = file_get_contents($file->getRealPath());

                    if (
                        stripos($content, '<svg') === false ||
                        stripos($content, '<script') !== false ||
                        stripos($content, '<?php') !== false
                    ) {
                        return back()
                            ->with('error', 'Invalid or unsafe SVG file.')
                            ->withInput();
                    }
                }

                $filename = time() . '_' . uniqid() . '.' . $ext;
                $file->storeAs('tag', $filename, 'public');

                $data['tags_image'] = $filename;
            }

            if ($request->remove_image == 1 && !$request->hasFile('image')) {
                $data['tags_image'] = null;
            }

            TblTag::updateOrCreate(
                ['id' => $request->id],
                $data
            );

            return redirect()
                ->route('pms.tags.list')
                ->with(
                    'success',
                    $request->id
                        ? 'Tag updated successfully!'
                        : 'Tag added successfully!'
                );
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
        try {
            $detail = TblTag::where(['id' => $id])->first();
            TblTag::where(['id' => $id])->update(['status' => !$detail->status]);
            return response()->json([
                'status' => true,
                'data' => '',
                'message' => 'Status updated successfully'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => "Internal Error",
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ], 500);
        }
    }


    public function delete($id)
    {
        try {
            TblTag::where('id', $id)->delete();
            return response()->json([
                'status' => true,
                'data' => '',
                'message' => 'Tag deleted successfully'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => "Internal Error",
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ], 500);
        }
    }

    public function multiDelete(Request $request)
    {
        $ids = $request->input('ids');

        if (empty($ids) || !is_array($ids)) {
            return response()->json([
                'status' => false,
                'message' => 'No tag selected for deletion.'
            ], 400);
        }

        try {
            TblTag::whereIn('id', $ids)->delete();
            return response()->json([
                'status' => true,
                'message' => 'Tag(s) deleted successfully.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Internal Error',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ], 500);
        }
    }

    public function ShowHometagsStatus($id)
    {
        try {
           // $detail = TblTag::where(['id' => $id])->first();
          //  TblTag::where(['id' => $id])->update(['tag_show_on_page' => !$detail->tag_show_on_page]);
            $tag = TblTag::findOrFail($id);
            if ($tag->tag_show_on_page == 1) {
                TblTag::query()->update(['tag_show_on_page' => 0]);
            } else {
                TblTag::query()->update(['tag_show_on_page' => 0]);
                $tag->update(['tag_show_on_page' => 1]);
            }
            return response()->json([
                'status' => true,
                'data' => '',
                'message' => 'Show home page updated successfully'
            ], 200);
        } catch (\Exception $e) {

            return response()->json([
                'status' => false,
                'message' => "Internal Error",
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ], 500);
        }
    }
}

<?php

namespace App\Http\Controllers\PMS\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
Use App\Models\TblFaqCategory;

class FaqCategoryController extends Controller
{
    public function index(Request $request){
        $query = TblFaqCategory::orderBy('id', 'desc');

        if ($request->filled('search_title')) {
            $query->where('title', 'like', '%' . $request->search_title . '%');
        }

        $items = $query->paginate(20)->withQueryString();

        return view('pms.master.faqcategory.list',compact('items'));
    }

    public function form($id=null){
        $detail = TblFaqCategory::where('id', $id)->first();
        return view('pms.master.faqcategory.form',compact('detail'));
    }

    public function save(Request $request)
    {

        $request->validate([
            'title' => 'required|string|max:255',
        ]);
        
        try {
            $role = TblFaqCategory::updateOrCreate(
                ['id' => $request->id],
                [
                    'title' => $request->title,
                ]
            );

            return redirect()->route('pms.faqcategory.list')
                ->with('success', $request->id ? 'Faq category updated successfully.' : 'Faq category saved successfully.');

        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Something went wrong: ' . $e->getMessage());
        }
    }


    public function toggleStatus($id){   
        try {
            $detail = TblFaqCategory::where(['id'=>$id])->first();
            TblFaqCategory::where(['id'=>$id])->update(['status'=>!$detail->status]);
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
            TblFaqCategory::where('id', $id)->delete();
            return response()->json([
                'status' => true,
                'data' => '',
                'message' => 'Faq category deleted successfully'
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

    public function multiDelete(Request $request)
    {
        $ids = $request->input('ids');

        if (empty($ids) || !is_array($ids)) {
            return response()->json([
                'status' => false,
                'message' => 'No faq category selected for deletion.'
            ], 400);
        }

        try {
            TblFaqCategory::whereIn('id', $ids)->delete();
            return response()->json([
                'status' => true,
                'message' => 'Faq categore(s) deleted successfully.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Internal Error',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}

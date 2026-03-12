<?php

namespace App\Http\Controllers\PMS\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
Use App\Models\TblFaqCategory;
Use App\Models\TblFaq;
use Illuminate\Support\Facades\Auth;

class FaqController extends Controller
{
    public function index(Request $request){
        $query = TblFaq::query();
        $query->select('tbl_faqs.*','tbl_faq_categories.title','tbl_faq_categories.id as faq_category_id')->leftJoin('tbl_faq_categories','tbl_faq_categories.id','=','tbl_faqs.faq_category_id');
        if ($request->filled('search_question')) {
            $query->where('question', 'like', '%' . $request->search_question . '%');
        }

        $items = $query->paginate(20)->withQueryString();

        return view('pms.master.faq.list',compact('items'));
    }

    public function form($id=null){
        $detail = TblFaq::select('tbl_faqs.*','tbl_faq_categories.title','tbl_faq_categories.id as faq_category_id')->leftJoin('tbl_faq_categories','tbl_faq_categories.id','=','tbl_faqs.faq_category_id')->where('tbl_faqs.id',$id)->first();

        $faqCategories = TblFaqCategory::where('status',1)->get();
        return view('pms.master.faq.form',compact('detail','faqCategories'));
    }

    public function save(Request $request)
    {

        $request->validate([
            'faq_category_id' => 'required',
            'question' => 'required',
            'answer' => 'required',
        ]);
        
        try {
            $role = TblFaq::updateOrCreate(
                ['id' => $request->id],
                [
                    'faq_category_id' => $request->faq_category_id,
                    'question' => $request->question,
                    'answer' => $request->answer,
                    'add_ip' => request()->ip(),
                    'add_by' => Auth::guard('admin')->user()->name,
                ]
            );

            return redirect()->route('pms.faq.list')
                ->with('success', $request->id ? 'Faq updated successfully.' : 'Faq saved successfully.');

        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Something went wrong: ' . $e->getMessage());
        }
    }


    public function toggleStatus($id){   
        try {
            $detail = TblFaq::where(['id'=>$id])->first();
            TblFaq::where(['id'=>$id])->update(['status'=>!$detail->status]);
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
            TblFaq::where('id', $id)->delete();
            return response()->json([
                'status' => true,
                'data' => '',
                'message' => 'Faq deleted successfully'
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
                'message' => 'No faq selected for deletion.'
            ], 400);
        }

        try {
            TblFaq::whereIn('id', $ids)->delete();
            return response()->json([
                'status' => true,
                'message' => 'Faq(s) deleted successfully.'
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

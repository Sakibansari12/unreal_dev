<?php

namespace App\Http\Controllers\PMS\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Agreement;
use Illuminate\Support\Facades\Auth;


class AgreementController extends Controller
{

    public function index(Request $request)
    {
        $items = Agreement::query()
            ->where('id', '!=', 1)
            ->orderBy('id', 'desc')
            ->paginate(50);
        return view('pms.master.agreement.list', compact('items'));
    }
    public function form($id = null)
    {
        $detail = Agreement::where('id', $id)->orderBy('id', 'desc')->first();
        return view('pms.master.agreement.form', compact('detail'));
    }
    public function save(Request $request)
    {
        $request->validate([
            'title' => 'required|max:255',
            'file_pdf' => $request->id ? 'nullable|file|mimes:pdf' : 'required|file|mimes:pdf',
        ]);
        try {
            $data = [
                'title' => $request->title,
                'add_ip' => $request->ip(),
                'add_by' => Auth::guard('admin')->user()->name,
            ];
            if ($request->hasFile('file_pdf')) {
                $timestamp = now()->format('Ymd_His');
                $file = $request->file('file_pdf');
                $originalName = $file->getClientOriginalName();
                $fileName = "{$timestamp}_{$originalName}";
                $filePath = $file->storeAs('agreements', $fileName, 'public');

                $data['file_pdf'] = $filePath;
            }
            $agreement = Agreement::updateOrCreate(
                ['id' => $request->id ?? null],
                $data
            );
            $message = $request->id ? 'Agreement updated successfully!' : 'Agreement added successfully!';
            return redirect()->route('pms.agreement.list')->with('success', $message);
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Something went wrong: ' . $e->getMessage());
        }
    }


    public function toggleStatus($id)
    {
        try {
            $detail = Agreement::where(['id' => $id])->first();
            Agreement::where(['id' => $id])->update(['status' => !$detail->status]);
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
            ], 500);
        }
    }

    public function delete($id)
    {
        try {
            Agreement::where('id', $id)->delete();
            return response()->json([
                'status' => true,
                'data' => '',
                'message' => 'Company deleted successfully'
            ], 200);
        } catch (\Exception $e) {
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
                'message' => 'No company selected for deletion.'
            ], 400);
        }

        try {
            Agreement::whereIn('id', $ids)->delete();
            return response()->json([
                'status' => true,
                'message' => 'Company(s) deleted successfully.'
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

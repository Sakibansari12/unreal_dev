<?php

namespace App\Http\Controllers\PMS\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TblCompany;
use Illuminate\Support\Str;
use App\Models\TblState;
use App\helper\MasterHelper;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;


class CompanyController extends Controller
{
    public function index(Request $request)
    {

        // Fetch company records excluding id=1
        $items = TblCompany::query()
            ->where('id', '!=', 1) // Exclude company with id 1

            // If search_company is provided, filter results by company_name
            ->when(!empty($request->search_company), function ($query) use ($request) {
                $query->where('company_name', 'like', '%' . $request->search_company . '%');
            })

            // Order results by latest ID
            ->orderBy('id', 'desc')

            // Paginate results with 20 per page and preserve query string in pagination links
            ->paginate(20)
            ->withQueryString();


        return view('pms.master.company.list', compact('items'));
    }

    public function form($id = null)
    {
        $detail = TblCompany::where('id', $id)->orderBy('id', 'desc')->first();
        $states = TblState::where('status', 1)->get();
        return view('pms.master.company.form', compact('states', 'detail'));
    }

    public function save(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'company_name'    => 'required|string|max:255',
            'company_address' => 'required',
            'state_id'        => 'required',

            'gst_no' => [
                'required',
                'regex:/^(?=.*[A-Za-z])(?=.*\d)[A-Za-z0-9]+$/',
                Rule::unique('tbl_companies', 'gst_no')->ignore($request->id),
            ],

            'cin_no' => [
                'required',
                'regex:/^(?=.*[A-Za-z])(?=.*\d)[A-Za-z0-9]+$/',
                Rule::unique('tbl_companies', 'cin_no')->ignore($request->id),
            ],

            'company_email' => [
                'required',
                'email',
                Rule::unique('tbl_companies', 'company_email')->ignore($request->id),
            ],

            // Website validation using preg_match
            'company_website' => [
                'nullable',
                function ($attribute, $value, $fail) {
                    // Add https:// if missing
                    if (!preg_match('~^https?://~i', $value)) {
                        $value = 'https://' . $value;
                    }

                    // Strict URL regex
                    if (!preg_match('~^https?:\/\/([\w\-]+\.)+[\w\-]{2,}(\/\S*)?$~i', $value)) {
                        $fail('Please enter a valid website URL.');
                    }
                },
            ],
        ]);
        $validator->setCustomMessages([
            'gst_no.regex' => 'GST No must be alphanumeric and contain both letters and numbers. Special characters are not allowed.',
            'cin_no.regex' => 'CIN No must be alphanumeric and contain both letters and numbers. Special characters are not allowed.',
        ]);
        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput()
                ->with('error', $validator->errors()->first());
        }

        // Normalize website
        $website = $request->company_website;
        if ($website && !preg_match('~^https?://~i', $website)) {
            $website = 'https://' . $website;
        }

        try {
            $state = TblState::findOrFail($request->state_id);

            $data = [
                'company_name'    => trim($request->company_name),
                'company_address' => trim($request->company_address),
                'state_id'        => $state->id,
                'state_name'      => $state->name,
                'state_code'      => $state->state_code,
                'gst_no'          => strtoupper(str_replace(' ', '', $request->gst_no)),
                'cin_no'          => strtoupper(str_replace(' ', '', $request->cin_no)),
                'company_phone'   => $request->company_phone,
                'company_email'   => $request->company_email,
                'company_website' => $website, // ✅ FIXED
                'add_ip'          => $request->ip(),
                'add_by'          => Auth::guard('admin')->user()->name,
            ];

            TblCompany::updateOrCreate(['id' => $request->id], $data);

            return redirect()
                ->route('pms.company.list')
                ->with('success', $request->id ? 'Company updated successfully!' : 'Company added successfully!');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Something went wrong: ' . $e->getMessage());
        }
    }

    public function toggleStatus($id)
    {
        try {
            $detail = TblCompany::where(['id' => $id])->first();
            TblCompany::where(['id' => $id])->update(['status' => !$detail->status]);
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
            TblCompany::where('id', $id)->delete();
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
            TblCompany::whereIn('id', $ids)->delete();
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

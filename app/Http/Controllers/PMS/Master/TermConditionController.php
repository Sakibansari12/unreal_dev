<?php

namespace App\Http\Controllers\PMS\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TblTermsandCondition;

class TermConditionController extends Controller
{
    public function form($id=null){
        $detail = TblTermsandCondition::first();
        return view('pms.master.term-condition', compact('detail'));
    }

    public function save(Request $request){

        $request->validate([
            'title_text'=>'required',
            'terms_and_condition'=>'required'
        ]);

        $item = TblTermsandCondition::firstOrNew(['id' => $request->id]);

        $item->title_text = $request->title_text;
        $item->terms_and_condition = $request->terms_and_condition;
        $item->save();

        return redirect()->route('pms.term.condition.form')
                ->with('success', $request->id ? 'Terms & Conditions updated successfully.' : 'Terms & Conditions saved successfully.');
    }
}

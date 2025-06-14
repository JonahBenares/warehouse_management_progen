<?php

namespace App\Http\Controllers;

use App\Models\Purpose;
use App\Exports\PurposeExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;

class PurposeController extends Controller
{
    public function get_all_purpose(Request $request){
        // $purpose = Purpose::orderBy('purpose_name','ASC')->paginate(10);
        // return response()->json($purpose);
        $filter=$request->get('filter');
        if($filter!=null){
            $purpose=Purpose::where('purpose_name','LIKE',"%$filter%")->orderBy('purpose_name','ASC')->paginate(10);
            return response()->json($purpose);
        }else{
            $purpose = Purpose::paginate(10);
            return response()->json($purpose);
        }
    }

    public function search_purpose(Request $request){
        $filter=$request->get('filter');
        if($filter!=null){
            $purposes=Purpose::where('purpose_name','LIKE',"%$filter%")->orderBy('purpose_name','ASC')->paginate(10);
            return response()->json($purposes);
        }else{
            $purposes = Purpose::paginate(10);
            return response()->json($purposes);
        }
    }

    public function create_purpose(Request $request){
        $formData=[
            'purpose_name'=>null,
        ];
        return response()->json($formData);
    }

    public function add_purpose(Request $request){
        $validated=$this->validate($request,[
            'purpose_name'=>['unique:purposes','required','string']
        ]);
        $purpose=Purpose::create($validated);
    }

    public function edit_purpose($id){
        $purposes = Purpose::find($id);
        return response()->json([
            'purposes'=>$purposes
        ],200);
    }

    public function update_purpose(Request $request, $id){
        $purposes=Purpose::where('id',$id)->first();
        $validated=$this->validate($request,[
            'purpose_name'=>'required|string|unique:purposes,purpose_name,'.$id
        ]);

        $purposes->update($validated);
    }

    public function export_purpose(){
        //Excel::store(new BeginningBalanceExport, 'begbal.xlsx');
        return Excel::download(new PurposeExport, 'purpose.xlsx');
    }
}
